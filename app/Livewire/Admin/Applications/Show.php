<?php

namespace App\Livewire\Admin\Applications;

use App\Enums\ApplicationStatus;
use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\Feedback;
use App\Models\Placement;
use App\Notifications\ApplicationStatusChanged;
use App\Notifications\FeedbackReceived;
use App\Notifications\InterviewScheduled;
use App\Notifications\OfferSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Show extends Component
{
    public Application $application;

    // Status management
    public $newStatus;
    public $statusNotes = '';
    public $showStatusModal = false;

    // Interview scheduling
    public $showInterviewModal = false;
    public $interviewDate;
    public $interviewTime;
    public $interviewType = 'online';
    public $interviewLocation = '';
    public $interviewNotes = '';

    // Offer management
    public $showOfferModal = false;
    public $offerStipend;
    public $offerStartDate;
    public $offerEndDate;
    public $offerNotes = '';
    public $offerTerms = '';

    // Placement creation
    public $showPlacementModal = false;
    public $placementSupervisorName;
    public $placementSupervisorContact;
    public $placementDepartment;
    public $placementStartDate;
    public $placementEndDate;
    public $placementNotes = '';

    // Feedback
    public $showFeedbackModal = false;
    public $feedbackMessage = '';
    public $feedbackType = 'general'; // general, interview, offer, rejection

    // Documents
    public $showDocumentModal = false;
    public $documentTitle;
    public $documentType;
    public $documentFile;

    // Activity tracking
    public $activityLogs;

    // Notes
    public $newNote = '';
    public $showNotes = false;

    // Similar applications
    public $similarApplications;

    // Validation rules
    public function getRules()
    {
        return [
            'newStatus' => ['required', Rule::in(ApplicationStatus::values())],
            'statusNotes' => 'nullable|string|max:500',

            'interviewDate' => 'required_if:interviewType,scheduled|date|after_or_equal:today',
            'interviewTime' => 'required_if:interviewType,scheduled',
            'interviewType' => 'required|in:online,phone,in_person',
            'interviewLocation' => 'required_if:interviewType,in_person|nullable|string|max:255',
            'interviewNotes' => 'nullable|string|max:1000',

            'offerStipend' => 'required_if:showOfferModal,true|numeric|min:0',
            'offerStartDate' => 'required_if:showOfferModal,true|date|after_or_equal:today',
            'offerEndDate' => 'required_if:showOfferModal,true|date|after:offerStartDate',
            'offerNotes' => 'nullable|string|max:1000',
            'offerTerms' => 'nullable|string|max:2000',

            'placementSupervisorName' => 'required_if:showPlacementModal,true|string|max:255',
            'placementSupervisorContact' => 'required_if:showPlacementModal,true|string|max:255',
            'placementDepartment' => 'required_if:showPlacementModal,true|string|max:255',
            'placementStartDate' => 'required_if:showPlacementModal,true|date',
            'placementEndDate' => 'required_if:showPlacementModal,true|date|after:placementStartDate',
            'placementNotes' => 'nullable|string|max:1000',

            'feedbackMessage' => 'required|string|max:2000',
            'newNote' => 'nullable|string|max:500',
        ];
    }

    public function mount($id)
    {
        $this->application = Application::with([
            'student' => function ($query) {
                $query->with(['studentProfile', 'mentorships', 'placements' => function ($q) {
                    $q->latest()->limit(5);
                }]);
            },
            'opportunity' => function ($query) {
                $query->with(['organization', 'applications' => function ($q) {
                    $q->with('student.studentProfile')
                        ->where('status', '!=', 'rejected')
                        ->latest()
                        ->limit(5);
                }]);
            },
            'placement'
        ])->findOrFail($id);

        $this->loadActivityLogs();
        $this->loadSimilarApplications();
    }

    protected function loadActivityLogs()
    {
        $this->activityLogs = ActivityLog::where('subject_id', $this->application->id)
            ->where('subject_type', Application::class)
            ->with('causer')
            ->latest()
            ->take(20)
            ->get();
    }

    protected function loadSimilarApplications()
    {
        // Find similar applications based on same opportunity or similar student profiles
        $this->similarApplications = Application::with(['student.studentProfile'])
            ->where('id', '!=', $this->application->id)
            ->where(function ($query) {
                $query->where('attachment_opportunity_id', $this->application->attachment_opportunity_id)
                    ->orWhereHas('student.studentProfile', function ($q) {
                        $q->where('course_name', $this->application->student->studentProfile?->course_name)
                            ->orWhere('institution_name', $this->application->student->studentProfile?->institution_name);
                    });
            })
            ->whereIn('status', ['submitted', 'under_review', 'shortlisted', 'interview_scheduled'])
            ->latest()
            ->take(5)
            ->get();
    }

    // Status Management
    public function openStatusModal($status)
    {
        // Validate that the requested status transition is allowed
        $targetStatus = ApplicationStatus::tryFrom($status);

        if (!$targetStatus) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Invalid status selected'
            ]);
            return;
        }

        if (!$this->application->canTransitionTo($targetStatus)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => "Cannot transition from {$this->application->status->label()} to {$targetStatus->label()}"
            ]);
            return;
        }

        $this->newStatus = $status;
        $this->statusNotes = '';
        $this->showStatusModal = true;
    }

    public function updateStatus()
    {
        $this->validate([
            'newStatus' => ['required', Rule::in(ApplicationStatus::values())],
            'statusNotes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () {
            $oldStatus = $this->application->status;
            $newStatusEnum = ApplicationStatus::from($this->newStatus);

            // Update application status using the enum
            $this->application->status = $newStatusEnum;

            // Set timestamps based on status using enum
            switch ($newStatusEnum) {
                case ApplicationStatus::UNDER_REVIEW:
                    $this->application->reviewed_at = now();
                    break;
                case ApplicationStatus::INTERVIEW_SCHEDULED:
                    $this->application->interview_scheduled_at = now();
                    break;
                case ApplicationStatus::OFFER_SENT:
                    $this->application->offer_sent_at = now();
                    break;
                case ApplicationStatus::OFFER_ACCEPTED:
                case ApplicationStatus::OFFER_REJECTED:
                    $this->application->offer_response_at = now();
                    break;
                case ApplicationStatus::HIRED:
                    $this->application->hired_at = now();
                    break;
            }

            $this->application->save();

            // Log the activity using enum labels
            activity_log(
                "Application status updated from {$oldStatus->label()} to {$newStatusEnum->label()}",
                'status_updated',
                [
                    'old_status' => $oldStatus->value,
                    'old_status_label' => $oldStatus->label(),
                    'new_status' => $newStatusEnum->value,
                    'new_status_label' => $newStatusEnum->label(),
                    'notes' => $this->statusNotes,
                    'application_id' => $this->application->id,
                    'opportunity_title' => $this->application->opportunity->title,
                    'subject_id' => $this->application->id,
                    'subject_type' => Application::class,
                ],
                'application'
            );

            // Send notification to student for important status changes
            $notifiableStatuses = [
                ApplicationStatus::SHORTLISTED,
                ApplicationStatus::INTERVIEW_SCHEDULED,
                ApplicationStatus::OFFER_SENT,
                ApplicationStatus::HIRED,
                ApplicationStatus::REJECTED,
            ];

            if (in_array($newStatusEnum, $notifiableStatuses)) {
                $this->application->student->notify(new ApplicationStatusChanged($this->application, $this->statusNotes));
            }
        });

        $this->showStatusModal = false;
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Application status updated successfully!'
        ]);

        $this->loadActivityLogs();
    }

    // Quick action methods for common status updates
    public function markAsUnderReview()
    {
        $this->openStatusModal(ApplicationStatus::UNDER_REVIEW->value);
    }

    public function markAsShortlisted()
    {
        $this->openStatusModal(ApplicationStatus::SHORTLISTED->value);
    }

    public function markAsRejected()
    {
        $this->openStatusModal(ApplicationStatus::REJECTED->value);
    }

    // Interview Management
    public function openInterviewModal()
    {
        // Check if we can transition to interview_scheduled
        if (!$this->application->canTransitionTo(ApplicationStatus::INTERVIEW_SCHEDULED)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot schedule interview from current status'
            ]);
            return;
        }

        $this->showInterviewModal = true;
        $this->interviewDate = now()->addDays(3)->format('Y-m-d');
        $this->interviewTime = '10:00';
        $this->interviewType = 'online';
        $this->interviewNotes = '';
    }

    public function scheduleInterview()
    {
        $this->validate([
            'interviewDate' => 'required|date|after_or_equal:today',
            'interviewTime' => 'required',
            'interviewType' => 'required|in:online,phone,in_person',
            'interviewLocation' => 'required_if:interviewType,in_person|nullable|string|max:255',
            'interviewNotes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () {
            // Update application status using enum
            $this->application->status = ApplicationStatus::INTERVIEW_SCHEDULED;
            $this->application->interview_scheduled_at = now();
            $this->application->interview_details = [
                'date' => $this->interviewDate,
                'time' => $this->interviewTime,
                'type' => $this->interviewType,
                'location' => $this->interviewLocation,
                'notes' => $this->interviewNotes,
                'scheduled_by' => auth()->id(),
                'scheduled_at' => now()->toDateTimeString(),
            ];
            $this->application->save();

            // Log activity
            $this->application->logModelActivity(
                'Interview scheduled for application',
                'interview_scheduled',
                [
                    'interview_date' => $this->interviewDate,
                    'interview_time' => $this->interviewTime,
                    'interview_type' => $this->interviewType,
                    'interview_location' => $this->interviewLocation,
                    'notes' => $this->interviewNotes,
                    'application_id' => $this->application->id,
                    'opportunity_title' => $this->application->opportunity->title
                ]
            );

            // Notify student
            $this->application->student->notify(new InterviewScheduled($this->application, [
                'date' => $this->interviewDate,
                'time' => $this->interviewTime,
                'type' => $this->interviewType,
                'location' => $this->interviewLocation,
                'notes' => $this->interviewNotes,
            ]));
        });

        $this->showInterviewModal = false;
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Interview scheduled successfully!'
        ]);

        $this->loadActivityLogs();
    }

    // Offer Management
    public function openOfferModal()
    {
        // Check if we can transition to offer_sent
        if (!$this->application->canTransitionTo(ApplicationStatus::OFFER_SENT)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot send offer from current status'
            ]);
            return;
        }

        $this->showOfferModal = true;
        $this->offerStipend = $this->application->opportunity->stipend ?? 0;
        $this->offerStartDate = now()->addWeeks(2)->format('Y-m-d');
        $this->offerEndDate = now()->addWeeks(2)->addMonths(3)->format('Y-m-d');
        $this->offerNotes = '';
        $this->offerTerms = $this->application->opportunity->default_offer_terms ?? '';
    }

    public function sendOffer()
    {
        $this->validate([
            'offerStipend' => 'required|numeric|min:0',
            'offerStartDate' => 'required|date|after_or_equal:today',
            'offerEndDate' => 'required|date|after:offerStartDate',
            'offerNotes' => 'nullable|string|max:1000',
            'offerTerms' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () {
            // Update application status using enum
            $this->application->status = ApplicationStatus::OFFER_SENT;
            $this->application->offer_sent_at = now();
            $this->application->offer_details = [
                'stipend' => $this->offerStipend,
                'start_date' => $this->offerStartDate,
                'end_date' => $this->offerEndDate,
                'notes' => $this->offerNotes,
                'terms' => $this->offerTerms,
                'sent_by' => auth()->id(),
                'sent_at' => now()->toDateTimeString(),
            ];
            $this->application->save();

            // Log activity
            $this->application->logModelActivity(
                'Offer sent to student',
                'offer_sent',
                [
                    'stipend' => $this->offerStipend,
                    'start_date' => $this->offerStartDate,
                    'end_date' => $this->offerEndDate,
                    'notes' => $this->offerNotes,
                    'terms' => $this->offerTerms,
                    'application_id' => $this->application->id,
                    'opportunity_title' => $this->application->opportunity->title,
                    'organization' => $this->application->opportunity->organization->name
                ]
            );

            // Notify student
            $this->application->student->notify(new OfferSent($this->application, [
                'stipend' => $this->offerStipend,
                'start_date' => $this->offerStartDate,
                'end_date' => $this->offerEndDate,
                'notes' => $this->offerNotes,
                'terms' => $this->offerTerms,
            ]));
        });

        $this->showOfferModal = false;
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Offer sent successfully!'
        ]);

        $this->loadActivityLogs();
    }

    public function createPlacement()
    {
        // Check if we can create placement (should be offer_accepted)
        if ($this->application->status !== ApplicationStatus::OFFER_ACCEPTED) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Placement can only be created after offer is accepted'
            ]);
            return;
        }

        $this->validate([
            'placementSupervisorName' => 'required|string|max:255',
            'placementSupervisorContact' => 'required|string|max:255',
            'placementDepartment' => 'required|string|max:255',
            'placementStartDate' => 'required|date',
            'placementEndDate' => 'required|date|after:placementStartDate',
            'placementNotes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () {
            // Create placement
            $placement = Placement::create([
                'student_id' => $this->application->user_id,
                'admin_id' => auth()->id(),
                'organization_id' => $this->application->opportunity->organization_id,
                'attachment_opportunity_id' => $this->application->attachment_opportunity_id,
                'status' => 'placed',
                'start_date' => $this->placementStartDate,
                'end_date' => $this->placementEndDate,
                'department' => $this->placementDepartment,
                'supervisor_name' => $this->placementSupervisorName,
                'supervisor_contact' => $this->placementSupervisorContact,
                'notes' => $this->placementNotes,
                'stipend' => $this->application->offer_details['stipend'] ?? null,
                'placement_confirmed_at' => now(),
            ]);

            // Update application status using enum
            $this->application->status = ApplicationStatus::HIRED;
            $this->application->hired_at = now();
            $this->application->placement_id = $placement->id;
            $this->application->save();

            // Update student profile
            if ($this->application->student->studentProfile) {
                $this->application->student->studentProfile->update([
                    'attachment_status' => 'placed',
                    'attachment_start_date' => $this->placementStartDate,
                    'attachment_end_date' => $this->placementEndDate,
                ]);
            }

            // Log activity for the placement
            $placement->logModelActivity(
                'Placement created for student',
                'placement_created',
                [
                    'placement_id' => $placement->id,
                    'student_name' => $this->application->student->full_name,
                    'student_id' => $this->application->student->id,
                    'organization' => $this->application->opportunity->organization->name,
                    'opportunity' => $this->application->opportunity->title,
                    'start_date' => $this->placementStartDate,
                    'end_date' => $this->placementEndDate,
                    'department' => $this->placementDepartment,
                    'supervisor' => $this->placementSupervisorName
                ]
            );

            // Also log on the application
            $this->application->logModelActivity(
                'Student hired and placement created',
                'hired',
                [
                    'placement_id' => $placement->id,
                    'start_date' => $this->placementStartDate,
                    'end_date' => $this->placementEndDate,
                    'department' => $this->placementDepartment
                ]
            );
        });

        $this->showPlacementModal = false;
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Placement created successfully!'
        ]);

        // Refresh application to show placement
        $this->application->refresh();
        $this->loadActivityLogs();
    }

    // Feedback Management
    public function openFeedbackModal($type = 'general')
    {
        $this->feedbackType = $type;
        $this->feedbackMessage = '';
        $this->showFeedbackModal = true;
    }

    public function sendFeedback()
    {
        $this->validate([
            'feedbackMessage' => 'required|string|max:2000',
        ]);

        // Save feedback
        $feedback = Feedback::create([
            'application_id' => $this->application->id,
            'user_id' => auth()->id(),
            'type' => $this->feedbackType,
            'message' => $this->feedbackMessage,
        ]);

        // Log activity
        $this->application->logModelActivity(
            "{$this->getFeedbackTypeLabel($this->feedbackType)} feedback sent to student",
            'feedback_sent',
            [
                'feedback_id' => $feedback->id,
                'feedback_type' => $this->feedbackType,
                'feedback_type_label' => $this->getFeedbackTypeLabel($this->feedbackType),
                'message_preview' => substr($this->feedbackMessage, 0, 100) . (strlen($this->feedbackMessage) > 100 ? '...' : ''),
                'application_id' => $this->application->id
            ]
        );

        // Notify student
        $this->application->student->notify(new FeedbackReceived($this->application, $feedback));

        $this->showFeedbackModal = false;
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Feedback sent successfully!'
        ]);

        $this->loadActivityLogs();
    }

    protected function getFeedbackTypeLabel($type)
    {
        return match ($type) {
            'general' => 'General',
            'interview' => 'Interview',
            'offer' => 'Offer',
            'rejection' => 'Rejection',
            default => ucfirst($type)
        };
    }

    // Notes Management
    public function addNote()
    {
        $this->validate([
            'newNote' => 'required|string|max:500',
        ]);

        // Log the note as activity
        $this->application->logModelActivity(
            'Note added to application',
            'note_added',
            [
                'note' => $this->newNote,
                'application_id' => $this->application->id,
                'opportunity_title' => $this->application->opportunity->title
            ]
        );

        $this->newNote = '';
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Note added successfully!'
        ]);

        $this->loadActivityLogs();
    }

    // Document Management
    public function downloadDocument($type)
    {
        $url = match ($type) {
            'cv' => $this->application->student->studentProfile?->cv_url,
            'transcript' => $this->application->student->studentProfile?->transcript_url,
            'school_letter' => $this->application->student->studentProfile?->school_letter_url,
            default => null,
        };

        if ($url && Storage::disk('public')->exists($url)) {
            return response()->download(Storage::disk('public')->path($url));
        }

        $this->dispatch('show-toast', [
            'type' => 'error',
            'message' => 'Document not found!'
        ]);
    }

    // Communication
    public function sendEmail()
    {
        // Log the email action
        $this->application->logModelActivity(
            "Email composition started for student",
            'email_compose',
            [
                'student_email' => $this->application->student->email,
                'student_name' => $this->application->student->full_name,
                'application_id' => $this->application->id
            ]
        );
        // Redirect to email composer with pre-filled details
        return redirect()->route('admin.communications.compose', [
            'recipient' => $this->application->student->email,
            'subject' => "Application #{$this->application->id} - {$this->application->opportunity->title}",
        ]);
    }

    // Navigation
    public function goToNextApplication()
    {
        $next = Application::where('id', '>', $this->application->id)
            ->orderBy('id')
            ->first();

        if ($next) {
            return redirect()->route('admin.applications.show', $next->id);
        }
    }

    public function goToPreviousApplication()
    {
        $prev = Application::where('id', '<', $this->application->id)
            ->orderBy('id', 'desc')
            ->first();

        if ($prev) {
            return redirect()->route('admin.applications.show', $prev->id);
        }
    }

    public function render()
    {
        return view('livewire.admin.applications.show', [
            'statusFlow' => $this->getStatusFlow(),
            'matchAnalysis' => $this->getMatchAnalysis(),
            'documentStatus' => $this->getDocumentStatus(),
        ]);
    }

    protected function getStatusFlow()
    {
        // You could also generate this dynamically from the enum
        // But keeping it as is for now since it's used in the blade
        return [
            'submitted' => ['label' => 'Submitted', 'icon' => 'fas fa-file-alt', 'color' => 'info'],
            'under_review' => ['label' => 'Under Review', 'icon' => 'fas fa-search', 'color' => 'primary'],
            'shortlisted' => ['label' => 'Shortlisted', 'icon' => 'fas fa-list-check', 'color' => 'success'],
            'interview_scheduled' => ['label' => 'Interview Scheduled', 'icon' => 'fas fa-calendar-check', 'color' => 'warning'],
            'interview_completed' => ['label' => 'Interview Completed', 'icon' => 'fas fa-check-circle', 'color' => 'success'],
            'offer_sent' => ['label' => 'Offer Sent', 'icon' => 'fas fa-handshake', 'color' => 'info'],
            'offer_accepted' => ['label' => 'Offer Accepted', 'icon' => 'fas fa-check-double', 'color' => 'success'],
            'offer_rejected' => ['label' => 'Offer Rejected', 'icon' => 'fas fa-times-circle', 'color' => 'danger'],
            'hired' => ['label' => 'Hired/Placed', 'icon' => 'fas fa-user-check', 'color' => 'success'],
            'rejected' => ['label' => 'Rejected', 'icon' => 'fas fa-ban', 'color' => 'danger'],
        ];
    }

    protected function getMatchAnalysis()
    {
        $student = $this->application->student;
        $opportunity = $this->application->opportunity;
        $profile = $student->studentProfile;

        if (!$profile) {
            return null;
        }

        $analysis = [
            'gpa' => [
                'score' => $profile->cgpa ?? 0,
                'required' => $opportunity->min_gpa ?? 0,
                'match' => ($profile->cgpa ?? 0) >= ($opportunity->min_gpa ?? 0),
            ],
            'skills' => [
                'student' => $profile->skills ?? [],
                'required' => $opportunity->skills_required ?? [],
                'matched' => array_intersect($profile->skills ?? [], $opportunity->skills_required ?? []),
                'missing' => array_diff($opportunity->skills_required ?? [], $profile->skills ?? []),
            ],
            'location' => [
                'student' => $profile->preferred_location ?? $student->county,
                'opportunity' => $opportunity->location,
                'match' => ($profile->preferred_location ?? $student->county) == $opportunity->location,
            ],
            'course' => [
                'student' => $profile->course_name,
                'required' => $opportunity->courses_required ?? [],
                'match' => in_array($profile->course_name, $opportunity->courses_required ?? []),
            ],
        ];

        // Calculate overall match percentage
        $totalWeight = 0;
        $achievedWeight = 0;

        // GPA (25%)
        if ($analysis['gpa']['required'] > 0) {
            $totalWeight += 25;
            if ($analysis['gpa']['match']) {
                $achievedWeight += 25;
            }
        }

        // Skills (40%)
        $totalWeight += 40;
        $skillMatchCount = count($analysis['skills']['matched']);
        $requiredSkillCount = count($analysis['skills']['required']);
        if ($requiredSkillCount > 0) {
            $skillPercentage = ($skillMatchCount / $requiredSkillCount) * 40;
            $achievedWeight += $skillPercentage;
        }

        // Location (15%)
        $totalWeight += 15;
        if ($analysis['location']['match']) {
            $achievedWeight += 15;
        }

        // Course (20%)
        $totalWeight += 20;
        if ($analysis['course']['match']) {
            $achievedWeight += 20;
        }

        $analysis['overall'] = $totalWeight > 0 ? round(($achievedWeight / $totalWeight) * 100) : 0;

        return $analysis;
    }

    protected function getDocumentStatus()
    {
        $profile = $this->application->student->studentProfile;

        return [
            'cv' => [
                'exists' => !empty($profile->cv_url),
                'url' => $profile->cv_url,
                'uploaded_at' => $profile->updated_at,
            ],
            'transcript' => [
                'exists' => !empty($profile->transcript_url),
                'url' => $profile->transcript_url,
                'uploaded_at' => $profile->updated_at,
            ],
            'school_letter' => [
                'exists' => !empty($profile->school_letter_url),
                'url' => $profile->school_letter_url,
                'uploaded_at' => $profile->updated_at,
            ],
        ];
    }
}
