<?php

namespace App\Livewire\Admin\Applications;

use App\Enums\ApplicationStatus;
use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\Interview;
use App\Models\InterviewOutcome;
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

    // Activity tracking
    public array $activityLogs = [];

    // Similar applications
    public $similarApplications;

    // Add these properties
    public $showInterviewModal = false;
    public $interviewDate;
    public $interviewTime;
    public $interviewType = 'online';
    public $interviewDuration = 60;
    public $interviewLocation;
    public $interviewMeetingLink;
    public $interviewPhoneNumber;
    public $interviewNotes;
    public $interviewerId;

    // Add these properties
    public $showCompleteInterviewModal = false;
    public $selectedInterviewId;
    public $interviewFeedback;
    public $interviewRating = 5;
    public $interviewOutcome = 'successful'; // successful, unsuccessful
    public $studentStrengths = [];
    public $studentWeaknesses = [];
    public $skillsAssessment = [];
    public $followUpRequired = false;
    public $followUpDate;
    public $nextSteps = '';

    // Offer modal properties
    public $showOfferModal = false;
    public $offerStipend;
    public $offerStartDate;
    public $offerEndDate;
    public $offerNotes;
    public $offerTerms;

    public $selectedApplicationId = null;

    // Common skills list for assessment
    public $commonSkills = [
        'Technical Knowledge',
        'Communication Skills',
        'Problem Solving',
        'Teamwork',
        'Leadership',
        'Time Management',
        'Adaptability',
        'Attention to Detail',
        'Creativity',
        'Work Ethic',
    ];

    // Add this method to open the modal
    public function openCompleteInterviewModal($interviewId)
    {
        $this->selectedInterviewId = $interviewId;
        $this->interviewFeedback = '';
        $this->interviewRating = 5;
        $this->interviewOutcome = 'successful';
        $this->interviewNotes = '';
        $this->studentStrengths = [];
        $this->studentWeaknesses = [];
        $this->skillsAssessment = [];
        $this->followUpRequired = false;
        $this->followUpDate = null;
        $this->nextSteps = '';
        $this->showCompleteInterviewModal = true;
    }

    // Update the completeInterview method
    public function completeInterview()
    {
        $this->validate([
            'interviewFeedback' => 'nullable|string|max:1000',
            'interviewRating' => 'required|integer|min:1|max:5',
            'interviewOutcome' => 'required|in:successful,unsuccessful',
            'interviewNotes' => 'nullable|string|max:500',
            'studentStrengths' => 'nullable|array',
            'studentWeaknesses' => 'nullable|array',
            'skillsAssessment' => 'nullable|array',
            'followUpRequired' => 'boolean',
            'followUpDate' => 'required_if:followUpRequired,true|nullable|date|after_or_equal:today',
            'nextSteps' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () {
            $interview = Interview::findOrFail($this->selectedInterviewId);

            // Update interview status
            $interview->update([
                'status' => 'completed',
                'completed_at' => now(),
                'feedback' => $this->interviewFeedback,
            ]);

            // Create interview outcome record
            $outcome = InterviewOutcome::create([
                'interview_id' => $interview->id,
                'application_id' => $this->application->id,
                'student_id' => $this->application->student_id,
                'organization_id' => $this->application->organization_id,
                'recorded_by' => auth()->id(),
                'outcome' => $this->interviewOutcome,
                'rating' => $this->interviewRating,
                'feedback' => $this->interviewFeedback,
                'notes' => $this->interviewNotes,
                'strengths' => $this->studentStrengths,
                'areas_for_improvement' => $this->studentWeaknesses,
                'skills_assessment' => $this->skillsAssessment,
                'decision_reason' => $this->interviewNotes,
                'decision_date' => now(),
                'next_steps' => $this->nextSteps,
                'follow_up_required' => $this->followUpRequired,
                'follow_up_date' => $this->followUpDate,
                'metadata' => [
                    'completed_by' => auth()->user()->full_name,
                    'completed_at' => now()->toDateTimeString(),
                    'rating_stars' => $this->interviewRating,
                ],
            ]);

            // Add interview history
            $interview->history()->create([
                'application_id' => $this->application->id,
                'user_id' => auth()->id(),
                'action' => 'completed',
                'notes' => $this->interviewNotes,
                'metadata' => [
                    'rating' => $this->interviewRating,
                    'outcome' => $this->interviewOutcome,
                    'feedback' => $this->interviewFeedback,
                    'outcome_id' => $outcome->id,
                    'completed_by' => auth()->user()->full_name,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Update application status based on outcome
            $oldStatus = $this->application->status;

            if ($this->interviewOutcome === 'successful') {
                // Move to offer stage
                $this->application->status = ApplicationStatus::INTERVIEW_COMPLETED;
                $this->application->interview_completed_at = now();
                $this->application->save();

                // Add application history
                $this->application->addHistory(
                    'interview_completed',
                    $this->application->student_id,
                    $this->application->organization_id,
                    $oldStatus->value,
                    ApplicationStatus::INTERVIEW_COMPLETED->value,
                    'Interview completed successfully',
                    [
                        'interview_id' => $interview->id,
                        'outcome_id' => $outcome->id,
                        'rating' => $this->interviewRating,
                        'outcome' => $this->interviewOutcome,
                    ]
                );
            } else {
                // Interview unsuccessful - move to rejected
                $this->application->status = ApplicationStatus::REJECTED;
                $this->application->declined_at = now();
                $this->application->decline_reason = 'Interview unsuccessful';
                $this->application->decline_feedback = $this->interviewFeedback;
                $this->application->save();

                // Update student profile status
                $studentProfile = $this->application->student->studentProfile;
                if ($studentProfile) {
                    $hasOtherActiveApps = Application::where('student_id', $this->application->student_id)
                        ->where('id', '!=', $this->application->id)
                        ->whereIn('status', [
                            ApplicationStatus::UNDER_REVIEW->value,
                            ApplicationStatus::SHORTLISTED->value,
                            ApplicationStatus::INTERVIEW_SCHEDULED->value,
                            ApplicationStatus::OFFER_SENT->value,
                        ])
                        ->exists();

                    if (!$hasOtherActiveApps) {
                        $studentProfile->update(['attachment_status' => 'seeking']);
                    }
                }

                // Add application history
                $this->application->addHistory(
                    'rejected',
                    $this->application->student_id,
                    $this->application->organization_id,
                    $oldStatus->value,
                    ApplicationStatus::REJECTED->value,
                    'Interview unsuccessful',
                    [
                        'interview_id' => $interview->id,
                        'outcome_id' => $outcome->id,
                        'rating' => $this->interviewRating,
                        'feedback' => $this->interviewFeedback,
                    ]
                );
            }

            // Log activity
            activity_log(
                "Interview #{$interview->id} completed - Outcome: {$this->interviewOutcome} (Rating: {$this->interviewRating}/5)",
                'interview_completed',
                [
                    'application_id' => $this->application->id,
                    'interview_id' => $interview->id,
                    'outcome_id' => $outcome->id,
                    'student_name' => $this->application->student->full_name,
                    'outcome' => $this->interviewOutcome,
                    'rating' => $this->interviewRating,
                    'follow_up_required' => $this->followUpRequired,
                ],
                'interview'
            );
        });

        $this->showCompleteInterviewModal = false;
        $this->selectedInterviewId = null;

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Interview completed and outcome recorded successfully!'
        ]);

        $this->loadActivityLogs();
    }

    public function mount(Application $application)
    {
        $this->application = $application->load([
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
            'placement',
            'history.user'
        ]);

        $this->loadActivityLogs();
        $this->loadSimilarApplications();
    }


    protected function loadActivityLogs()
    {
        // Load both activity logs and application history
        $activityLogs = ActivityLog::where('subject_id', $this->application->id)
            ->where('subject_type', Application::class)
            ->with('causer')
            ->get()
            ->map(function ($log) {
                $properties = $log->properties ?? [];
                return [
                    'id' => 'log_' . $log->id,
                    'type' => 'activity',
                    'causer' => $log->causer?->only(['id', 'full_name']),
                    'description' => $log->description,
                    'properties' => $log->properties,
                    'created_at' => $log->created_at,
                    'icon' => $properties['icon'] ?? 'fa-history',
                    'color' => $properties['color'] ?? 'secondary',
                ];
            });

        // Load application history
        $historyLogs = $this->application->history()
            ->with('user')
            ->get()
            ->map(function ($history) {
                return  [
                    'id' => 'history_' . $history->id,
                    'type' => 'history',
                    'causer' => $history->user?->name,
                    'description' => $this->formatHistoryDescription($history),
                    'properties' => [
                        'old_status' => $history->old_status,
                        'new_status' => $history->new_status,
                        'old_status_label' => $history->old_status_label,
                        'new_status_label' => $history->new_status_label,
                        'action' => $history->action,
                        'notes' => $history->notes,
                        'metadata' => $history->metadata,
                        'color' => $this->getHistoryColor($history->action),
                        'icon' => $history->action_icon,
                    ],
                    'created_at' => $history->created_at,
                    'icon' => $history->action_icon,
                    'color' => $this->getHistoryColor($history->action),
                ];
            });

        // Merge and sort by created_at
        $this->activityLogs = $activityLogs->concat($historyLogs)
            ->sortByDesc('created_at')
            ->take(20)
            ->values()
            ->toArray();
    }

    protected function formatHistoryDescription($history)
    {
        $userName = $history->user?->full_name ?? 'System';

        return match ($history->action) {
            'created' => "{$userName} submitted the application",
            'status_changed' => "{$userName} changed status from {$history->old_status_label} to {$history->new_status_label}",
            'interview_scheduled' => "{$userName} scheduled an interview",
            'interview_completed' => "{$userName} marked interview as completed",
            'interview_cancelled' => "{$userName} cancelled the interview",
            'offer_sent' => "{$userName} sent an offer",
            'offer_accepted' => "{$userName} accepted the offer",
            'offer_rejected' => "{$userName} rejected the offer",
            'hired' => "{$userName} hired the student",
            'rejected' => "{$userName} rejected the application",
            'cancelled' => "{$userName} cancelled the application",
            'note_added' => "{$userName} added a note",
            'document_uploaded' => "{$userName} uploaded a document",
            'email_sent' => "{$userName} sent an email",
            default => $history->notes ?? "{$userName} performed {$history->action}",
        };
    }


    protected function getHistoryColor($action)
    {
        return match ($action) {
            'created' => 'info',
            'status_changed' => 'primary',
            'interview_scheduled' => 'warning',
            'interview_completed' => 'success',
            'interview_cancelled' => 'danger',
            'offer_sent' => 'info',
            'offer_accepted' => 'success',
            'offer_rejected' => 'danger',
            'hired' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'danger',
            'note_added' => 'info',
            'document_uploaded' => 'primary',
            'email_sent' => 'primary',
            default => 'secondary'
        };
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

        // Special validation: When trying to move to OFFER_SENT, check payment
        if ($targetStatus === ApplicationStatus::OFFER_SENT) {
            if ($this->application->hasPaymentRequired()) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => 'Cannot move to Offer Sent: Payment is required and not yet completed. Payment status: ' .
                        ($this->application->paymentTransaction ? ucfirst($this->application->paymentTransaction->status) : 'Not initiated')
                ]);
                return;
            }

            if ($this->application->paymentTransaction && $this->application->paymentTransaction->status !== 'completed') {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => 'Cannot move to Offer Sent: Payment is not completed. Current status: ' . ucfirst($this->application->paymentTransaction->status)
                ]);
                return;
            }
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


    public function updateStatus()
    {
        $this->validate([
            'newStatus' => ['required', Rule::in(ApplicationStatus::values())],
            'statusNotes' => 'nullable|string|max:500',
        ]);

        $newStatusEnum = ApplicationStatus::from($this->newStatus);

        // Double-check payment requirement before updating to OFFER_SENT
        if ($newStatusEnum === ApplicationStatus::OFFER_SENT) {
            if ($this->application->hasPaymentRequired()) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => 'Cannot update to Offer Sent: Payment is required and not yet completed.'
                ]);
                $this->showStatusModal = false;
                return;
            }

            if ($this->application->paymentTransaction && $this->application->paymentTransaction->status !== 'completed') {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => 'Cannot update to Offer Sent: Payment is not completed.'
                ]);
                $this->showStatusModal = false;
                return;
            }
        }

        DB::transaction(function () use ($newStatusEnum) {
            $oldStatus = $this->application->status;

            // Update application status
            $this->application->status = $newStatusEnum;

            // Set timestamps based on status
            switch ($newStatusEnum) {
                case ApplicationStatus::UNDER_REVIEW:
                    $this->application->reviewed_at = now();
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

            // Add history record
            $this->application->addHistory(
                'status_changed',
                $this->application->student_id,
                $this->application->organization_id,
                $oldStatus->value,
                $newStatusEnum->value,
                $this->statusNotes,
                [
                    'old_status_label' => $oldStatus->label(),
                    'new_status_label' => $newStatusEnum->label(),
                    'opportunity_title' => $this->application->opportunity->title,
                    'payment_completed' => $this->application->payment_completed_at ? true : false,
                ]
            );

            // Log to activity log
            activity_log(
                "Application status updated from {$oldStatus->label()} to {$newStatusEnum->label()}",
                'status_updated',
                [
                    'old_status' => $oldStatus->value,
                    'new_status' => $newStatusEnum->value,
                    'notes' => $this->statusNotes,
                    'application_id' => $this->application->id,
                    'payment_verified' => $newStatusEnum === ApplicationStatus::OFFER_SENT,
                ],
                'application'
            );
        });

        $this->showStatusModal = false;

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Application status updated successfully!'
        ]);

        $this->loadActivityLogs();
    }

    /**
     * Check if offer can be sent (for UI button visibility)
     */
    public function canSendOffer()
    {
        // Check if application is in INTERVIEW_COMPLETED status
        if ($this->application->status !== ApplicationStatus::INTERVIEW_COMPLETED) {
            return false;
        }

        // Check if payment is completed
        if ($this->application->payment_completed_at) {
            return true;
        }

        // Check if payment transaction exists and is completed
        if ($this->application->paymentTransaction && $this->application->paymentTransaction->status === 'completed') {
            return true;
        }

        return false;
    }


    /**
     * Get payment status for display
     */
    public function getPaymentStatusAttribute()
    {
        if ($this->application->payment_completed_at) {
            return [
                'label' => 'Payment Completed',
                'color' => 'success',
                'icon' => 'fa-check-circle',
                'date' => $this->application->payment_completed_at,
            ];
        }

        if ($this->application->paymentTransaction) {
            $statusColors = [
                'pending' => 'warning',
                'processing' => 'info',
                'failed' => 'danger',
                'refunded' => 'danger',
            ];

            return [
                'label' => 'Payment ' . ucfirst($this->application->paymentTransaction->status),
                'color' => $statusColors[$this->application->paymentTransaction->status] ?? 'secondary',
                'icon' => $this->getPaymentIcon($this->application->paymentTransaction->status),
                'status' => $this->application->paymentTransaction->status,
            ];
        }

        return [
            'label' => 'Payment Required',
            'color' => 'warning',
            'icon' => 'fa-credit-card',
            'status' => 'required',
        ];
    }

    protected function getPaymentIcon($status)
    {
        return match ($status) {
            'pending' => 'fa-clock',
            'processing' => 'fa-spinner fa-pulse',
            'completed' => 'fa-check-circle',
            'failed' => 'fa-times-circle',
            'refunded' => 'fa-undo',
            default => 'fa-credit-card',
        };
    }

    // Add this method
    public function openInterviewModal()
    {
        $this->resetInterviewForm();
        $this->showInterviewModal = true;
    }

    protected function resetInterviewForm()
    {
        $this->interviewDate = null;
        $this->interviewTime = null;
        $this->interviewType = 'online';
        $this->interviewDuration = 60;
        $this->interviewLocation = null;
        $this->interviewMeetingLink = null;
        $this->interviewPhoneNumber = null;
        $this->interviewNotes = null;
        $this->interviewerId = null;
    }

    public function scheduleInterview()
    {
        $this->validate([
            'interviewDate' => 'required|date|after_or_equal:today',
            'interviewTime' => 'required',
            'interviewType' => 'required|in:online,phone,in_person',
            'interviewDuration' => 'required|integer|min:15|max:480',
            'interviewLocation' => 'required_if:interviewType,in_person|nullable|string|max:255',
            'interviewMeetingLink' => 'required_if:interviewType,online|nullable|url|max:255',
            'interviewPhoneNumber' => 'required_if:interviewType,phone|nullable|string|max:20',
            'interviewNotes' => 'nullable|string|max:1000',
            'interviewerId' => 'nullable|exists:users,id',
        ]);

        DB::transaction(function () {
            // Create datetime from date and time
            $scheduledAt = \Carbon\Carbon::parse($this->interviewDate . ' ' . $this->interviewTime);

            // Create interview record
            $interview = Interview::create([
                'application_id' => $this->application->id,
                'scheduled_by' => auth()->id(),
                'interviewer_id' => $this->interviewerId,
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $this->interviewDuration,
                'type' => $this->interviewType,
                'location' => $this->interviewLocation,
                'meeting_link' => $this->interviewMeetingLink,
                'phone_number' => $this->interviewPhoneNumber,
                'notes' => $this->interviewNotes,
                'status' => 'scheduled',
            ]);

            // Update application status
            $oldStatus = $this->application->status;
            $this->application->status = ApplicationStatus::INTERVIEW_SCHEDULED;
            $this->application->save();

            // Update student profile status if needed
            $studentProfile = $this->application->student->studentProfile;
            if ($studentProfile && $studentProfile->attachment_status === 'applied') {
                $studentProfile->update(['attachment_status' => 'interviewing']);
            }

            // Add application history
            $this->application->addHistory(
                'interview_scheduled',
                $this->application->student_id,
                $this->application->organization_id,
                $oldStatus->value,
                ApplicationStatus::INTERVIEW_SCHEDULED->value,
                'Interview scheduled',
                [
                    'interview_id' => $interview->id,
                    'scheduled_at' => $scheduledAt->toDateTimeString(),
                    'type' => $this->interviewType,
                    'duration' => $this->interviewDuration,
                    'notes' => $this->interviewNotes,
                ]
            );

            // Add interview history
            $interview->history()->create([
                'application_id' => $this->application->id,
                'user_id' => auth()->id(),
                'action' => 'scheduled',
                'notes' => 'Interview scheduled',
                'metadata' => [
                    'scheduled_at' => $scheduledAt->toDateTimeString(),
                    'type' => $this->interviewType,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Log activity
            activity_log(
                "Interview scheduled for application #{$this->application->id}",
                'interview_scheduled',
                [
                    'application_id' => $this->application->id,
                    'student_name' => $this->application->student->full_name,
                    'interview_id' => $interview->id,
                    'scheduled_at' => $scheduledAt->toDateTimeString(),
                ],
                'interview'
            );
        });

        $this->showInterviewModal = false;
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Interview scheduled successfully!'
        ]);

        $this->loadActivityLogs();
    }

    public function markInterviewCompleted($interviewId)
    {
        $interview = Interview::findOrFail($interviewId);

        DB::transaction(function () use ($interview) {
            $oldStatus = $this->application->status;

            // Mark interview as completed
            $interview->markAsCompleted();

            // Add application history
            $this->application->addHistory(
                'interview_completed',
                $this->application->student_id,
                $this->application->organization_id,
                $oldStatus->value,
                ApplicationStatus::INTERVIEW_COMPLETED->value,
                'Interview completed',
                ['interview_id' => $interview->id]
            );
        });

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Interview marked as completed!'
        ]);

        $this->loadActivityLogs();
    }


    /**
     * Open offer modal with payment validation
     */
    public function openOfferModal()
    {
        // Check if payment is required and completed
        if ($this->application->hasPaymentRequired()) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot send offer: Payment is required and not yet completed. Please wait for the student to complete payment or check payment status.'
            ]);
            return;
        }

        // Check if payment transaction exists and is completed
        if ($this->application->paymentTransaction && $this->application->paymentTransaction->status !== 'completed') {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot send offer: Payment is pending or failed. Payment status: ' . ucfirst($this->application->paymentTransaction->status)
            ]);
            return;
        }

        // Check if offer can be sent (status must be INTERVIEW_COMPLETED)
        if ($this->application->status !== ApplicationStatus::INTERVIEW_COMPLETED) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot send offer: Application must be in "Interview Completed" status. Current status: ' . $this->application->status->label()
            ]);
            return;
        }

        // Reset offer form fields
        $this->offerStipend = $this->application->opportunity->stipend ?? 0;
        $this->offerStartDate = $this->application->opportunity->start_date?->format('Y-m-d') ?? now()->addDays(7)->format('Y-m-d');
        $this->offerEndDate = $this->application->opportunity->end_date?->format('Y-m-d') ?? now()->addMonths(3)->format('Y-m-d');
        $this->offerNotes = '';
        $this->offerTerms = '';

        $this->showOfferModal = true;
    }

    /**
     * Send offer to student (with payment verification)
     */

    public function sendOffer()
    {
        $this->validate([
            'offerStipend' => 'required|numeric|min:0',
            'offerStartDate' => 'required|date|after_or_equal:today',
            'offerEndDate' => 'required|date|after:offerStartDate',
            'offerNotes' => 'nullable|string|max:1000',
            'offerTerms' => 'nullable|string|max:2000',
        ]);

        $application = Application::findOrFail($this->selectedApplicationId);

        // Double-check payment status
        if (!$application->payment_completed_at) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot send offer: Payment not completed.'
            ]);
            $this->showOfferModal = false;
            return;
        }

        DB::transaction(function () use ($application) {
            $oldStatus = $application->status;

            // Update application with offer details
            $application->status = ApplicationStatus::OFFER_SENT;
            $application->offer_details = [
                'stipend' => $this->offerStipend,
                'start_date' => $this->offerStartDate,
                'end_date' => $this->offerEndDate,
                'notes' => $this->offerNotes,
                'terms' => $this->offerTerms,
                'sent_by' => auth()->user()->full_name,
                'sent_at' => now()->toDateTimeString(),
            ];
            $application->save();

            // Add application history
            $application->addHistory(
                'offer_sent',
                $application->student_id,
                $application->organization_id,
                $oldStatus->value,
                ApplicationStatus::OFFER_SENT->value,
                $this->offerNotes ?: 'Offer sent to student',
                [
                    'stipend' => $this->offerStipend,
                    'start_date' => $this->offerStartDate,
                    'end_date' => $this->offerEndDate,
                    'payment_reference' => $application->payment_reference,
                ]
            );

            // Log activity
            activity_log(
                "Offer sent for application #{$application->id} - KSh {$this->offerStipend}",
                'offer_sent',
                [
                    'application_id' => $application->id,
                    'student_name' => $application->student->full_name,
                    'opportunity' => $application->opportunity->title,
                    'stipend' => $this->offerStipend,
                ],
                'application'
            );
        });

        $this->showOfferModal = false;
        $this->selectedApplicationId = null;

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Offer sent successfully!'
        ]);

        $this->dispatch('refreshComponent');
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


    /**
     * Get the next steps message for the application
     */
    public function getNextStepsMessage()
    {
        if ($this->application->status === ApplicationStatus::INTERVIEW_COMPLETED) {
            if (!$this->application->payment_completed_at) {
                return [
                    'message' => 'Waiting for student to complete payment before offer can be sent.',
                    'icon' => 'fa-credit-card',
                    'color' => 'warning'
                ];
            } else {
                return [
                    'message' => 'Payment completed. Ready to send offer.',
                    'icon' => 'fa-check-circle',
                    'color' => 'success'
                ];
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.admin.applications.show', [
            'statusFlow' => $this->getStatusFlow(),
            'matchAnalysis' => $this->getMatchAnalysis(),
            'documentStatus' => $this->getDocumentStatus(),
        ]);
    }
}
