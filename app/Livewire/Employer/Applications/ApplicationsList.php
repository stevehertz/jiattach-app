<?php

namespace App\Livewire\Employer\Applications;

use App\Models\Application;
use App\Models\Notification;
use App\Models\Placement;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ApplicationsList extends Component
{
    use WithPagination;

    public $organization;
    public $search = '';
    public $filterStatus = '';
    public $filterOpportunity = '';
    public $filterScore = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // View application details
    public $viewingApplication = null;
    public $showViewModal = false;

    // Action modals
    public $showOfferModal = false;
    public $showRejectModal = false;
    public $showInterviewModal = false;
    public $selectedApplication = null;
    
    // Form fields
    public $offerMessage = '';
    public $interviewDate = '';
    public $interviewType = 'in-person';
    public $interviewLocation = '';
    public $interviewNotes = '';
    public $rejectionReason = '';

    // Statistics
    public $totalApplications = 0;
    public $pendingCount = 0;
    public $reviewedCount = 0;
    public $offeredCount = 0;
    public $acceptedCount = 0;
    public $rejectedCount = 0;

    protected $paginationTheme = 'bootstrap';

     protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterOpportunity' => ['except' => ''],
        'filterScore' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $listeners = [
        'applicationUpdated' => '$refresh',
    ];

    public function mount()
    {
        $user =  User::findOrFail(auth()->user()->id);
        $this->organization = $user->primaryOrganization();

        if (!$this->organization) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'No organization found.');
        }

        $this->loadStats();
    }

     /**
     * Load application statistics
     */
    public function loadStats()
    {
        $query = Application::where('organization_id', $this->organization->id);

        $this->totalApplications = $query->count();
        $this->pendingCount = (clone $query)->where('status', 'pending')->count();
        $this->reviewedCount = (clone $query)->where('status', 'reviewed')->count();
        $this->offeredCount = (clone $query)->where('status', 'offered')->count();
        $this->acceptedCount = (clone $query)->where('status', 'accepted')->count();
        $this->rejectedCount = (clone $query)->where('status', 'rejected')->count();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterOpportunity()
    {
        $this->resetPage();
    }

    public function updatingFilterScore()
    {
        $this->resetPage();
    }

    /**
     * Sort by field
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * View application details
     */
    public function viewApplication($applicationId)
    {
        $this->viewingApplication = Application::with([
            'student',
            'student.studentProfile',
            'opportunity',
        ])->findOrFail($applicationId);

        $this->showViewModal = true;
    }

    /**
     * Close view modal
     */
    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingApplication = null;
    }

    /**
     * Update application status
     */
    public function updateStatus($applicationId, $status)
    {
        try {
            $application = Application::findOrFail($applicationId);
            
            // Verify this application belongs to the organization
            if ($application->organization_id !== $this->organization->id) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Unauthorized action.',
                ]);
                return;
            }

            $application->update(['status' => $status]);

            // Log activity
            activity_log(
                "Application #{$application->id} status changed to '{$status}'",
                'updated',
                [
                    'application_id' => $application->id,
                    'organization_id' => $this->organization->id,
                    'student_id' => $application->student_id,
                    'status' => $status,
                ],
                'application'
            );

            // Create notification for student
            $this->createStatusNotification($application, $status);

            $this->loadStats();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Application status updated successfully.',
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating application status:', [
                'error' => $e->getMessage(),
                'application_id' => $applicationId,
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

      /**
     * Show offer modal
     */
    public function showOfferForm($applicationId)
    {
        $this->selectedApplication = Application::findOrFail($applicationId);
        $this->offerMessage = '';
        $this->showOfferModal = true;
    }

    /**
     * Send offer
     */
    public function sendOffer()
    {
        $this->validate([
            'offerMessage' => 'required|string|max:1000',
        ]);

        try {
            $application = $this->selectedApplication;
            
            $application->update([
                'status' => 'offered',
                'offer_message' => $this->offerMessage,
                'offered_at' => now(),
            ]);

            // Create notification
            $this->createOfferNotification($application);

            // Log activity
            activity_log(
                "Offer sent for application #{$application->id}",
                'updated',
                [
                    'application_id' => $application->id,
                    'student_id' => $application->student_id,
                ],
                'application'
            );

            $this->showOfferModal = false;
            $this->selectedApplication = null;
            $this->offerMessage = '';
            $this->loadStats();

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
     * Show interview modal
     */
    public function showInterviewForm($applicationId)
    {
        $this->selectedApplication = Application::findOrFail($applicationId);
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
            $application = $this->selectedApplication;
            
            $application->update([
                'status' => 'interviewing',
                'interview_date' => $this->interviewDate,
                'interview_type' => $this->interviewType,
                'interview_location' => $this->interviewLocation,
                'interview_notes' => $this->interviewNotes,
            ]);

            // Create notification
            $this->createInterviewNotification($application);

            // Log activity
            activity_log(
                "Interview scheduled for application #{$application->id}",
                'updated',
                [
                    'application_id' => $application->id,
                    'interview_date' => $this->interviewDate,
                ],
                'application'
            );

            $this->showInterviewModal = false;
            $this->selectedApplication = null;
            $this->reset(['interviewDate', 'interviewType', 'interviewLocation', 'interviewNotes']);
            $this->loadStats();

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
     * Show reject modal
     */
    public function showRejectForm($applicationId)
    {
        $this->selectedApplication = Application::findOrFail($applicationId);
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
            $application = $this->selectedApplication;
            
            $application->update([
                'status' => 'rejected',
                'rejection_reason' => $this->rejectionReason,
                'rejected_at' => now(),
            ]);

            // Create notification
            $this->createRejectionNotification($application);

            // Log activity
            activity_log(
                "Application #{$application->id} rejected",
                'updated',
                [
                    'application_id' => $application->id,
                    'reason' => $this->rejectionReason,
                ],
                'application'
            );

            $this->showRejectModal = false;
            $this->selectedApplication = null;
            $this->rejectionReason = '';
            $this->loadStats();

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
     * Create placement from accepted application
     */
    public function createPlacement($applicationId)
    {
        try {
            $application = Application::with('opportunity')->findOrFail($applicationId);

            if ($application->status !== 'accepted') {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Only accepted applications can be converted to placements.',
                ]);
                return;
            }

            // Check if placement already exists
            $existingPlacement = Placement::where('application_id', $application->id)->first();
            if ($existingPlacement) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => 'A placement already exists for this application.',
                ]);
                return;
            }

            // Create placement
            $placement = Placement::create([
                'student_id' => $application->student_id,
                'organization_id' => $this->organization->id,
                'application_id' => $application->id,
                'status' => 'pending',
                'start_date' => now()->addDays(7),
                'end_date' => now()->addMonths(3),
                'position' => $application->opportunity->title ?? 'Intern',
            ]);

            // Update application status
            $application->update(['status' => 'placed']);

            // Create notification
            $this->createPlacementNotification($application, $placement);

            // Log activity
            activity_log(
                "Placement created for application #{$application->id}",
                'created',
                [
                    'application_id' => $application->id,
                    'placement_id' => $placement->id,
                ],
                'placement'
            );

            $this->loadStats();
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
     * Create notifications for status changes
     */
    protected function createStatusNotification($application, $status)
    {
        $messages = [
            'reviewed' => 'Your application has been reviewed by ' . $this->organization->name,
            'shortlisted' => 'You have been shortlisted by ' . $this->organization->name,
        ];

        if (isset($messages[$status])) {
            Notification::create([
                'user_id' => $application->student_id,
                'type' => 'application_status',
                'title' => 'Application Status Updated',
                'message' => $messages[$status],
                'icon' => 'fas fa-file-alt',
                'data' => [
                    'application_id' => $application->id,
                    'organization_name' => $this->organization->name,
                    'status' => $status,
                ],
            ]);
        }
    }

    /**
     * Create offer notification
     */
    protected function createOfferNotification($application)
    {
        Notification::create([
            'user_id' => $application->student_id,
            'type' => 'placement_offer',
            'title' => 'Placement Offer Received!',
            'message' => $this->organization->name . ' has sent you a placement offer.',
            'icon' => 'fas fa-handshake',
            'data' => [
                'application_id' => $application->id,
                'organization_name' => $this->organization->name,
                'offer_message' => $this->offerMessage,
            ],
        ]);
    }

    /**
     * Create interview notification
     */
    protected function createInterviewNotification($application)
    {
        Notification::create([
            'user_id' => $application->student_id,
            'type' => 'interview_scheduled',
            'title' => 'Interview Scheduled',
            'message' => $this->organization->name . ' has scheduled an interview for you.',
            'icon' => 'fas fa-calendar-check',
            'data' => [
                'application_id' => $application->id,
                'organization_name' => $this->organization->name,
                'interview_date' => $this->interviewDate,
                'interview_type' => $this->interviewType,
                'interview_location' => $this->interviewLocation,
            ],
        ]);
    }

    /**
     * Create rejection notification
     */
    protected function createRejectionNotification($application)
    {
        Notification::create([
            'user_id' => $application->student_id,
            'type' => 'application_rejected',
            'title' => 'Application Update',
            'message' => 'Your application to ' . $this->organization->name . ' has been updated.',
            'icon' => 'fas fa-info-circle',
            'data' => [
                'application_id' => $application->id,
                'organization_name' => $this->organization->name,
                'reason' => $this->rejectionReason,
            ],
        ]);
    }

    /**
     * Create placement notification
     */
    protected function createPlacementNotification($application, $placement)
    {
        Notification::create([
            'user_id' => $application->student_id,
            'type' => 'placement_created',
            'title' => 'Placement Created!',
            'message' => 'Your placement at ' . $this->organization->name . ' has been created.',
            'icon' => 'fas fa-briefcase',
            'data' => [
                'application_id' => $application->id,
                'placement_id' => $placement->id,
                'organization_name' => $this->organization->name,
            ],
        ]);
    }

     /**
     * Get opportunities for filter dropdown
     */
    public function getOpportunitiesProperty()
    {
        return $this->organization->opportunities()
            ->whereIn('status', ['active', 'closed'])
            ->orderBy('title')
            ->get();
    }

    /**
     * Get applications with filters
     */
    public function getApplicationsProperty()
    {
        $query = Application::where('organization_id', $this->organization->id)
            ->with([
                'student',
                'student.studentProfile',
                'opportunity',
            ]);

        // Apply search
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('student', function($studentQ) use ($searchTerm) {
                    $studentQ->where('first_name', 'like', $searchTerm)
                             ->orWhere('last_name', 'like', $searchTerm)
                             ->orWhere('email', 'like', $searchTerm);
                })
                ->orWhereHas('opportunity', function($oppQ) use ($searchTerm) {
                    $oppQ->where('title', 'like', $searchTerm);
                })
                ->orWhere('id', 'like', $searchTerm);
            });
        }

        // Filter by status
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Filter by opportunity
        if ($this->filterOpportunity) {
            $query->where('attachment_opportunity_id', $this->filterOpportunity);
        }

        // Filter by match score
        if ($this->filterScore !== '') {
            switch ($this->filterScore) {
                case 'high':
                    $query->where('match_score', '>=', 80);
                    break;
                case 'medium':
                    $query->whereBetween('match_score', [50, 79]);
                    break;
                case 'low':
                    $query->where('match_score', '<', 50);
                    break;
            }
        }

        return $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.employer.applications.applications-list', [
            'applications' => $this->applications,
            'opportunities' => $this->opportunities,
        ]);
    }
}
