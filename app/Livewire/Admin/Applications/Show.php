<?php

namespace App\Livewire\Admin\Applications;

use App\Enums\ApplicationStatus;
use App\Models\ActivityLog;
use App\Models\Application;
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

        DB::transaction(function () {
            $oldStatus = $this->application->status;
            $newStatusEnum = ApplicationStatus::from($this->newStatus);

            // Update application status
            $this->application->status = $newStatusEnum;

            // Set timestamps based on status
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

            // ===== UPDATE STUDENT PROFILE BASED ON STATUS =====
            $studentProfile = $this->application->student->studentProfile;

            if ($studentProfile) {

                $oldAttachmentStatus = $studentProfile->attachment_status;
                $newAttachmentStatus = null;
                $startDate = null;
                $endDate = null;

                // Determine new attachment status based on application status
                switch ($newStatusEnum) {
                    case ApplicationStatus::HIRED:
                        $newAttachmentStatus = 'placed';
                        // If we have placement dates, use them
                        if ($this->application->placement) {
                            $startDate = $this->application->placement->start_date;
                            $endDate = $this->application->placement->end_date;
                        } elseif ($this->application->offer_details) {
                            // Otherwise use offer dates
                            $startDate = $this->application->offer_details['start_date'] ?? null;
                            $endDate = $this->application->offer_details['end_date'] ?? null;
                        }
                        break;
                    case ApplicationStatus::REJECTED:
                        // If rejected and no other active applications, reset to seeking
                        $hasOtherActiveApps = Application::where('user_id', $this->application->user_id)
                            ->where('id', '!=', $this->application->id)
                            ->whereIn('status', [
                                ApplicationStatus::UNDER_REVIEW->value,
                                ApplicationStatus::SHORTLISTED->value,
                                ApplicationStatus::INTERVIEW_SCHEDULED->value,
                                ApplicationStatus::INTERVIEW_COMPLETED->value,
                                ApplicationStatus::OFFER_SENT->value,
                            ])
                            ->exists();

                        if (!$hasOtherActiveApps) {
                            $newAttachmentStatus = 'seeking';
                        }
                        break;
                    case ApplicationStatus::OFFER_ACCEPTED:
                        // Student has accepted offer but not yet placed
                        // Keep as 'interviewing' or 'applied' - no change
                        break;

                    case ApplicationStatus::INTERVIEW_SCHEDULED:
                    case ApplicationStatus::INTERVIEW_COMPLETED:
                        if ($oldAttachmentStatus === 'applied') {
                            $newAttachmentStatus = 'interviewing';
                        }
                        break;
                }

                // Update student profile if status changed
                if ($newAttachmentStatus && $newAttachmentStatus !== $oldAttachmentStatus) {
                    $updateData = ['attachment_status' => $newAttachmentStatus];

                    if ($startDate) {
                        $updateData['attachment_start_date'] = $startDate;
                    }
                    if ($endDate) {
                        $updateData['attachment_end_date'] = $endDate;
                    }

                    $studentProfile->update($updateData);

                    // Log the student profile status change
                    activity_log(
                        "Student attachment status updated from {$oldAttachmentStatus} to {$newAttachmentStatus}",
                        'student_status_changed',
                        [
                            'student_id' => $this->application->student->id,
                            'student_name' => $this->application->student->full_name,
                            'old_status' => $oldAttachmentStatus,
                            'new_status' => $newAttachmentStatus,
                            'application_id' => $this->application->id,
                            'application_status' => $newStatusEnum->value,
                        ],
                        'student_profile'
                    );
                }
            }

            // ===== END STUDENT PROFILE UPDATE =====
            
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
                ]
            );

            // Log to activity log as well (optional)
            activity_log(
                "Application status updated from {$oldStatus->label()} to {$newStatusEnum->label()}",
                'status_updated',
                [
                    'old_status' => $oldStatus->value,
                    'new_status' => $newStatusEnum->value,
                    'notes' => $this->statusNotes,
                    'application_id' => $this->application->id,
                ],
                'application'
            );

            // Send notification...
        });

        $this->showStatusModal = false;
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Application status updated successfully!'
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

    public function render()
    {
        return view('livewire.admin.applications.show', [
            'statusFlow' => $this->getStatusFlow(),
            'matchAnalysis' => $this->getMatchAnalysis(),
            'documentStatus' => $this->getDocumentStatus(),
        ]);
    }
}
