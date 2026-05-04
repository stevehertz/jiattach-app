<?php

namespace App\Livewire\Employer\Applications;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Notification;
use App\Models\Placement;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Show extends Component
{
    public $application;
    public $applicationId;
    public $organization;

    // Tab management
    public $activeTab = 'details';

    // Action modals
    public $showStatusModal = false;
    public $showOfferModal = false;
    public $showRejectModal = false;
    public $showInterviewModal = false;
    public $showPlacementModal = false;

    // Form fields
    public $newStatus = '';
    public $statusNotes = '';
    public $offerMessage = '';
    public $offerStipend = '';
    public $offerStartDate = '';
    public $offerEndDate = '';
    public $offerTerms = '';
    public $rejectionReason = '';
    public $interviewDate = '';
    public $interviewType = 'in-person';
    public $interviewLocation = '';
    public $interviewNotes = '';

    // Placement fields
    public $placementStartDate = '';
    public $placementEndDate = '';
    public $placementPosition = '';
    public $placementSupervisor = '';
    public $placementDepartment = '';

    protected $listeners = [
        'refreshApplication' => '$refresh',
    ];

    public function mount($applicationId)
    {
        $user = User::findOrFail(auth()->user()->id);
        $this->organization = $user->primaryOrganization();
        $this->applicationId = $applicationId;

        $this->loadApplication();
    }

    /**
     * Load application with all relationships
     */
    public function loadApplication()
    {
        $this->application = Application::with([
            'student',
            'student.studentProfile',
            'opportunity',
            'opportunity.organization',
            'organization',
            'history.user',
            'interviews',
            'latestInterview',
            'upcomingInterview',
            'placement',
            'paymentTransaction',
            'feedbacks',
        ])->findOrFail($this->applicationId);

        // Verify this application belongs to the employer's organization
        if ($this->application->organization_id !== $this->organization->id) {
            abort(403, 'Unauthorized access to this application.');
        }
    }

    /**
     * Switch active tab
     */
    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * Show status update modal
     */
    public function showStatusUpdate($status = null)
    {
        $this->newStatus = $status ?? $this->application->status->value;
        $this->statusNotes = '';
        $this->showStatusModal = true;
    }

    /**
     * Update application status
     */
    public function updateStatus()
    {
        $this->validate([
            'newStatus' => 'required|string',
            'statusNotes' => 'nullable|string|max:1000',
        ]);

        try {
            $oldStatus = $this->application->status->value;
            $newStatusEnum = ApplicationStatus::from($this->newStatus);

            // Check if transition is valid
            if (!$this->application->canTransitionTo($newStatusEnum)) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Invalid status transition.',
                ]);
                return;
            }

            // Update application status
            $this->application->update([
                'status' => $newStatusEnum,
                'employer_notes' => $this->statusNotes ?: $this->application->employer_notes,
                'reviewed_at' => in_array($this->newStatus, ['under_review', 'reviewing']) ? now() : $this->application->reviewed_at,
            ]);

            // Add history record
            $this->application->addHistory(
                'status_changed',
                $this->application->student_id,
                $this->organization->id,
                $oldStatus,
                $this->newStatus,
                $this->statusNotes,
                ['changed_by' => auth()->user()->full_name]
            );

            // Create notification
            $this->createStatusNotification();

            // Log activity
            activity_log(
                "Application #{$this->application->id} status changed from '{$oldStatus}' to '{$this->newStatus}'",
                'status_updated',
                [
                    'application_id' => $this->application->id,
                    'old_status' => $oldStatus,
                    'new_status' => $this->newStatus,
                ],
                'application'
            );

            $this->showStatusModal = false;
            $this->reset(['newStatus', 'statusNotes']);
            $this->loadApplication();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Application status updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating application status:', [
                'error' => $e->getMessage(),
                'application_id' => $this->application->id,
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Show interview scheduling modal
     */
    public function showInterviewForm()
    {
        $this->interviewDate = now()->addDays(2)->format('Y-m-d\TH:i');
        $this->interviewType = 'in-person';
        $this->interviewLocation = $this->organization->address ?? '';
        $this->interviewNotes = '';
        $this->showInterviewModal = true;
    }

    /**
     * Schedule interview
     */
    public function scheduleInterview()
    {
        $this->validate([
            'interviewDate' => 'required|date|after:now',
            'interviewType' => 'required|in:in-person,virtual,phone',
            'interviewLocation' => 'required|string|max:255',
            'interviewNotes' => 'nullable|string|max:1000',
        ]);

        try {
            // Create interview record
            $this->application->interviews()->create([
                'scheduled_at' => $this->interviewDate,
                'type' => $this->interviewType,
                'location' => $this->interviewLocation,
                'notes' => $this->interviewNotes,
                'status' => 'scheduled',
                'scheduled_by' => auth()->id(),
            ]);

            // Update application status
            $oldStatus = $this->application->status->value;
            $this->application->update([
                'status' => ApplicationStatus::INTERVIEW_SCHEDULED,
            ]);

            // Add history
            $this->application->addHistory(
                'interview_scheduled',
                $this->application->student_id,
                $this->organization->id,
                $oldStatus,
                ApplicationStatus::INTERVIEW_SCHEDULED->value,
                "Interview scheduled for " . $this->interviewDate,
                [
                    'interview_date' => $this->interviewDate,
                    'interview_type' => $this->interviewType,
                    'scheduled_by' => auth()->user()->full_name,
                ]
            );

            // Create notification
            Notification::create([
                'user_id' => $this->application->student_id,
                'type' => 'interview_scheduled',
                'title' => 'Interview Scheduled',
                'message' => $this->organization->name . ' has scheduled an interview for you.',
                'icon' => 'fas fa-calendar-check',
                'data' => [
                    'application_id' => $this->application->id,
                    'organization_name' => $this->organization->name,
                    'interview_date' => $this->interviewDate,
                    'interview_type' => $this->interviewType,
                    'interview_location' => $this->interviewLocation,
                ],
            ]);

            $this->showInterviewModal = false;
            $this->reset(['interviewDate', 'interviewType', 'interviewLocation', 'interviewNotes']);
            $this->loadApplication();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Interview scheduled successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Error scheduling interview:', ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Show offer modal
     */
    public function showOfferForm()
    {
        $this->offerMessage = '';
        $this->offerStipend = '';
        $this->offerStartDate = now()->addDays(14)->format('Y-m-d');
        $this->offerEndDate = now()->addMonths(3)->format('Y-m-d');
        $this->offerTerms = '';
        $this->showOfferModal = true;
    }

    /**
     * Send offer
     */
    public function sendOffer()
    {
        $this->validate([
            'offerMessage' => 'required|string|max:2000',
            'offerStipend' => 'nullable|numeric|min:0',
            'offerStartDate' => 'required|date',
            'offerEndDate' => 'required|date|after:offerStartDate',
            'offerTerms' => 'nullable|string|max:2000',
        ]);

        try {
            $oldStatus = $this->application->status->value;

            $this->application->update([
                'status' => ApplicationStatus::OFFER_SENT,
                'offer_details' => [
                    'message' => $this->offerMessage,
                    'stipend' => $this->offerStipend,
                    'start_date' => $this->offerStartDate,
                    'end_date' => $this->offerEndDate,
                    'terms' => $this->offerTerms,
                    'sent_by' => auth()->user()->full_name,
                    'sent_at' => now()->toDateTimeString(),
                ],
            ]);

            // Add history
            $this->application->addHistory(
                'offer_sent',
                $this->application->student_id,
                $this->organization->id,
                $oldStatus,
                ApplicationStatus::OFFER_SENT->value,
                $this->offerMessage,
                [
                    'stipend' => $this->offerStipend,
                    'start_date' => $this->offerStartDate,
                    'end_date' => $this->offerEndDate,
                ]
            );

            // Create notification
            Notification::create([
                'user_id' => $this->application->student_id,
                'type' => 'placement_offer',
                'title' => 'Placement Offer Received!',
                'message' => $this->organization->name . ' has sent you a placement offer.',
                'icon' => 'fas fa-handshake',
                'data' => [
                    'application_id' => $this->application->id,
                    'organization_name' => $this->organization->name,
                    'offer_message' => $this->offerMessage,
                ],
            ]);

            $this->showOfferModal = false;
            $this->reset(['offerMessage', 'offerStipend', 'offerStartDate', 'offerEndDate', 'offerTerms']);
            $this->loadApplication();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Offer sent successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending offer:', ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Show reject modal
     */
    public function showRejectForm()
    {
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    /**
     * Reject application
     */
    public function rejectApplication()
    {
        $this->validate([
            'rejectionReason' => 'required|string|max:1000',
        ]);

        try {
            $oldStatus = $this->application->status->value;

            $this->application->update([
                'status' => ApplicationStatus::REJECTED,
                'decline_reason' => $this->rejectionReason,
                'declined_at' => now(),
            ]);

            // Add history
            $this->application->addHistory(
                'rejected',
                $this->application->student_id,
                $this->organization->id,
                $oldStatus,
                ApplicationStatus::REJECTED->value,
                $this->rejectionReason,
                ['rejected_by' => auth()->user()->full_name]
            );

            // Create notification
            Notification::create([
                'user_id' => $this->application->student_id,
                'type' => 'application_rejected',
                'title' => 'Application Update',
                'message' => 'Your application to ' . $this->organization->name . ' has been updated.',
                'icon' => 'fas fa-info-circle',
                'data' => [
                    'application_id' => $this->application->id,
                    'organization_name' => $this->organization->name,
                    'reason' => $this->rejectionReason,
                ],
            ]);

            $this->showRejectModal = false;
            $this->rejectionReason = '';
            $this->loadApplication();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Application rejected.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error rejecting application:', ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Show placement modal
     */
    public function showPlacementForm()
    {
        $this->placementStartDate = now()->addDays(7)->format('Y-m-d');
        $this->placementEndDate = now()->addMonths(3)->format('Y-m-d');
        $this->placementPosition = $this->application->opportunity->title ?? '';
        $this->placementSupervisor = '';
        $this->placementDepartment = '';
        $this->showPlacementModal = true;
    }

    /**
     * Create placement from application
     */
    public function createPlacement()
    {
        $this->validate([
            'placementStartDate' => 'required|date',
            'placementEndDate' => 'required|date|after:placementStartDate',
            'placementPosition' => 'required|string|max:255',
            'placementSupervisor' => 'nullable|string|max:255',
            'placementDepartment' => 'nullable|string|max:255',
        ]);

        try {
            // Check if placement already exists
            $existingPlacement = Placement::where('application_id', $this->application->id)->first();
            if ($existingPlacement) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => 'A placement already exists for this application.',
                ]);
                return;
            }

            // Create placement
            $placement = Placement::create([
                'student_id' => $this->application->student_id,
                'organization_id' => $this->organization->id,
                'application_id' => $this->application->id,
                'admin_id' => auth()->id(),
                'status' => 'pending',
                'start_date' => $this->placementStartDate,
                'end_date' => $this->placementEndDate,
                'position' => $this->placementPosition,
                'supervisor_name' => $this->placementSupervisor,
                'department' => $this->placementDepartment,
            ]);

            // Update application status
            $oldStatus = $this->application->status->value;
            $this->application->update([
                'status' => ApplicationStatus::HIRED,
            ]);

            // Add history
            $this->application->addHistory(
                'placement_created',
                $this->application->student_id,
                $this->organization->id,
                $oldStatus,
                ApplicationStatus::HIRED->value,
                'Placement created successfully',
                [
                    'placement_id' => $placement->id,
                    'start_date' => $this->placementStartDate,
                    'end_date' => $this->placementEndDate,
                ]
            );

            // Create notification
            Notification::create([
                'user_id' => $this->application->student_id,
                'type' => 'placement_created',
                'title' => 'Placement Created!',
                'message' => 'Your placement at ' . $this->organization->name . ' has been created.',
                'icon' => 'fas fa-briefcase',
                'data' => [
                    'application_id' => $this->application->id,
                    'placement_id' => $placement->id,
                    'organization_name' => $this->organization->name,
                    'start_date' => $this->placementStartDate,
                ],
            ]);

            $this->showPlacementModal = false;
            $this->reset(['placementStartDate', 'placementEndDate', 'placementPosition', 'placementSupervisor', 'placementDepartment']);
            $this->loadApplication();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Placement created successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating placement:', ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Create notification for status changes
     */
    protected function createStatusNotification()
    {
        $messages = [
            'under_review' => 'Your application is now under review by ' . $this->organization->name,
            'reviewing' => 'Your application is being reviewed by ' . $this->organization->name,
            'shortlisted' => 'You have been shortlisted by ' . $this->organization->name,
            'hired' => 'Congratulations! You have been hired by ' . $this->organization->name,
        ];

        if (isset($messages[$this->newStatus])) {
            Notification::create([
                'user_id' => $this->application->student_id,
                'type' => 'application_status',
                'title' => 'Application Status Updated',
                'message' => $messages[$this->newStatus],
                'icon' => 'fas fa-file-alt',
                'data' => [
                    'application_id' => $this->application->id,
                    'organization_name' => $this->organization->name,
                    'status' => $this->newStatus,
                ],
            ]);
        }
    }

    public function render()
    {
        return view('livewire.employer.applications.show', [
            'application' => $this->application,
            'availableStatuses' => $this->application->getAvailableNextStatuses(),
            'timelineHistory' => $this->application->timeline_history,
        ]);
    }
}
