<?php

namespace App\Livewire\Admin\Interviews;

use App\Enums\ApplicationStatus;
use App\Models\ActivityLog;
use App\Models\Interview;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Show extends Component
{
    public Interview $interview;
    public $showRescheduleModal = false;
    public $showCancelModal = false;
    public $showCompleteModal = false;
    public $showFeedbackModal = false;

    // Reschedule form
    public $newDate;
    public $newTime;
    public $rescheduleReason;

    // Cancel form
    public $cancelReason;

    // Complete form
    public $interviewFeedback;
    public $interviewRating = 5;

    // Feedback form
    public $feedbackMessage;
    public $feedbackType = 'general'; // general, technical, communication, etc.

    // Notes
    public $newNote;

    // Activity logs
    public $activityLogs = [];
    public $timelineEvents = [];

    protected $listeners = [
        'refreshInterview' => '$refresh',
        'confirmReschedule',
        'confirmCancel',
        'confirmComplete',
        'sendReminder'
    ];

    public function mount(Interview $interview)
    {
        $this->interview = $interview->load([
            'application' => function ($query) {
                $query->with([
                    'student' => function ($q) {
                        $q->with(['studentProfile', 'mentorships', 'placements']);
                    },
                    'opportunity' => function ($q) {
                        $q->with(['organization', 'applications' => function ($app) {
                            $app->where('id', '!=', $this->interview->application_id)
                                ->with('student.studentProfile')
                                ->latest()
                                ->limit(5);
                        }]);
                    },
                    'placement'
                ]);
            },
            'scheduledBy',
            'interviewer',
            'history' => function ($query) {
                $query->with('user')->latest();
            }
        ]);

        $this->loadTimeline();
    }

    protected function loadTimeline()
    {
        // Load interview history
        $historyLogs = $this->interview->history()
            ->with('user')
            ->get()
            ->map(function ($history) {
                return [
                    'id' => 'history_' . $history->id,
                    'type' => 'history',
                    'causer' => $history->user?->full_name ?? 'System',
                    'description' => $this->formatHistoryDescription($history),
                    'properties' => [
                        'action' => $history->action,
                        'notes' => $history->notes,
                        'old_values' => $history->old_values,
                        'new_values' => $history->new_values,
                        'metadata' => $history->metadata,
                    ],
                    'created_at' => $history->created_at,
                    'icon' => $this->getHistoryIcon($history->action),
                    'color' => $this->getHistoryColor($history->action),
                ];
            });

        // Load activity logs
        $activityLogs = ActivityLog::where('subject_id', $this->interview->id)
            ->where('subject_type', Interview::class)
            ->with('causer')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => 'log_' . $log->id,
                    'type' => 'activity',
                    'causer' => $log->causer?->full_name ?? 'System',
                    'description' => $log->description,
                    'properties' => $log->properties,
                    'created_at' => $log->created_at,
                    'icon' => $log->properties['icon'] ?? 'fa-history',
                    'color' => $log->properties['color'] ?? 'secondary',
                ];
            });

        // Merge and sort
        $this->timelineEvents = $historyLogs->concat($activityLogs)
            ->sortByDesc('created_at')
            ->values()
            ->toArray();
    }

     protected function formatHistoryDescription($history)
    {
        return match ($history->action) {
            'scheduled' => "Interview scheduled for " . ($history->metadata['scheduled_at'] ?? ''),
            'rescheduled' => "Interview rescheduled from " . 
                (isset($history->old_values['scheduled_at']) ? 
                    \Carbon\Carbon::parse($history->old_values['scheduled_at'])->format('M d, Y h:i A') : '') .
                " to " . 
                (isset($history->new_values['scheduled_at']) ? 
                    \Carbon\Carbon::parse($history->new_values['scheduled_at'])->format('M d, Y h:i A') : ''),
            'confirmed' => "Interview confirmed",
            'completed' => "Interview completed" . ($history->notes ? ": {$history->notes}" : ''),
            'cancelled' => "Interview cancelled" . ($history->notes ? ": {$history->notes}" : ''),
            'no_show' => "Student did not show up for interview",
            'reminder_sent' => "Reminder sent to student",
            'updated' => "Interview details updated",
            default => ucfirst(str_replace('_', ' ', $history->action)),
        };
    }

    protected function getHistoryIcon($action)
    {
        return match ($action) {
            'scheduled' => 'fa-calendar-plus',
            'rescheduled' => 'fa-calendar-alt',
            'confirmed' => 'fa-check-circle',
            'completed' => 'fa-check-double',
            'cancelled' => 'fa-times-circle',
            'no_show' => 'fa-user-slash',
            'reminder_sent' => 'fa-bell',
            'updated' => 'fa-edit',
            default => 'fa-history',
        };
    }

    protected function getHistoryColor($action)
    {
        return match ($action) {
            'scheduled' => 'primary',
            'rescheduled' => 'warning',
            'confirmed' => 'info',
            'completed' => 'success',
            'cancelled', 'no_show' => 'danger',
            'reminder_sent' => 'secondary',
            'updated' => 'secondary',
            default => 'secondary',
        };
    }

    // Reschedule Interview
    public function openRescheduleModal()
    {
        $this->newDate = $this->interview->scheduled_at->format('Y-m-d');
        $this->newTime = $this->interview->scheduled_at->format('H:i');
        $this->rescheduleReason = '';
        $this->showRescheduleModal = true;
    }

    public function rescheduleInterview()
    {
        $this->validate([
            'newDate' => 'required|date|after_or_equal:today',
            'newTime' => 'required',
            'rescheduleReason' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () {
            $oldDateTime = $this->interview->scheduled_at;
            $newDateTime = \Carbon\Carbon::parse($this->newDate . ' ' . $this->newTime);

            // Update interview
            $this->interview->update([
                'scheduled_at' => $newDateTime,
                'status' => 'rescheduled',
                'rescheduled_at' => now(),
                'notes' => $this->rescheduleReason ?: $this->interview->notes,
            ]);

            // Add history
            $this->interview->history()->create([
                'application_id' => $this->interview->application_id,
                'user_id' => auth()->id(),
                'action' => 'rescheduled',
                'old_values' => ['scheduled_at' => $oldDateTime],
                'new_values' => ['scheduled_at' => $newDateTime],
                'notes' => $this->rescheduleReason,
                'metadata' => [
                    'reason' => $this->rescheduleReason,
                    'rescheduled_by' => auth()->user()->full_name,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Log activity
            activity_log(
                "Interview #{$this->interview->id} rescheduled",
                'interview_rescheduled',
                [
                    'interview_id' => $this->interview->id,
                    'application_id' => $this->interview->application_id,
                    'student_name' => $this->interview->application->student->full_name,
                    'old_date' => $oldDateTime->toDateTimeString(),
                    'new_date' => $newDateTime->toDateTimeString(),
                    'reason' => $this->rescheduleReason,
                ],
                'interview'
            );
        });

        $this->showRescheduleModal = false;
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Interview rescheduled successfully!'
        ]);

        $this->loadTimeline();
    }

     // Cancel Interview
    public function openCancelModal()
    {
        $this->cancelReason = '';
        $this->showCancelModal = true;
    }

    public function cancelInterview()
    {
        $this->validate([
            'cancelReason' => 'required|string|max:500',
        ]);

        DB::transaction(function () {
            // Update interview
            $this->interview->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'notes' => $this->cancelReason,
            ]);

            // Update application status? (optional - could go back to shortlisted)
            // $this->interview->application->update([
            //     'status' => ApplicationStatus::SHORTLISTED
            // ]);

            // Add history
            $this->interview->history()->create([
                'application_id' => $this->interview->application_id,
                'user_id' => auth()->id(),
                'action' => 'cancelled',
                'notes' => $this->cancelReason,
                'metadata' => [
                    'cancelled_by' => auth()->user()->full_name,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Log activity
            activity_log(
                "Interview #{$this->interview->id} cancelled",
                'interview_cancelled',
                [
                    'interview_id' => $this->interview->id,
                    'application_id' => $this->interview->application_id,
                    'student_name' => $this->interview->application->student->full_name,
                    'reason' => $this->cancelReason,
                ],
                'interview'
            );
        });

        $this->showCancelModal = false;
        $this->dispatch('show-toast', [
            'type' => 'warning',
            'message' => 'Interview cancelled successfully!'
        ]);

        $this->loadTimeline();
    }

    // Complete Interview
    public function openCompleteModal()
    {
        $this->interviewFeedback = '';
        $this->interviewRating = 5;
        $this->showCompleteModal = true;
    }

    public function completeInterview()
    {
        $this->validate([
            'interviewFeedback' => 'nullable|string|max:1000',
            'interviewRating' => 'required|integer|min:1|max:5',
        ]);

        DB::transaction(function () {
            // Update interview
            $this->interview->update([
                'status' => 'completed',
                'completed_at' => now(),
                'feedback' => $this->interviewFeedback,
            ]);

            // Update application status
            $this->interview->application->update([
                'status' => ApplicationStatus::INTERVIEW_COMPLETED,
                'interview_completed_at' => now(),
            ]);

            // Add history
            $this->interview->history()->create([
                'application_id' => $this->interview->application_id,
                'user_id' => auth()->id(),
                'action' => 'completed',
                'notes' => $this->interviewFeedback,
                'metadata' => [
                    'rating' => $this->interviewRating,
                    'completed_by' => auth()->user()->full_name,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Log activity
            activity_log(
                "Interview #{$this->interview->id} completed",
                'interview_completed',
                [
                    'interview_id' => $this->interview->id,
                    'application_id' => $this->interview->application_id,
                    'student_name' => $this->interview->application->student->full_name,
                    'rating' => $this->interviewRating,
                ],
                'interview'
            );
        });

        $this->showCompleteModal = false;
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Interview marked as completed!'
        ]);

        $this->loadTimeline();
    }

     // Mark as No Show
    public function markNoShow()
    {
        DB::transaction(function () {
            $this->interview->update([
                'status' => 'no_show',
                'completed_at' => now(),
            ]);

            // Add history
            $this->interview->history()->create([
                'application_id' => $this->interview->application_id,
                'user_id' => auth()->id(),
                'action' => 'no_show',
                'notes' => 'Student did not show up for interview',
                'metadata' => [
                    'marked_by' => auth()->user()->full_name,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            activity_log(
                "Interview #{$this->interview->id} marked as no-show",
                'interview_no_show',
                [
                    'interview_id' => $this->interview->id,
                    'application_id' => $this->interview->application_id,
                    'student_name' => $this->interview->application->student->full_name,
                ],
                'interview'
            );
        });

        $this->dispatch('show-toast', [
            'type' => 'warning',
            'message' => 'Interview marked as no-show'
        ]);

        $this->loadTimeline();
    }

    // Send Reminder
    public function sendReminder()
    {
        // Here you would implement email/SMS notification
        // For now, we'll just log it

        $this->interview->update([
            'reminder_sent_at' => now(),
        ]);

        $this->interview->history()->create([
            'application_id' => $this->interview->application_id,
            'user_id' => auth()->id(),
            'action' => 'reminder_sent',
            'notes' => 'Reminder sent to student',
            'metadata' => [
                'sent_by' => auth()->user()->full_name,
                'sent_at' => now()->toDateTimeString(),
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        activity_log(
            "Reminder sent for interview #{$this->interview->id}",
            'reminder_sent',
            [
                'interview_id' => $this->interview->id,
                'student_name' => $this->interview->application->student->full_name,
            ],
            'interview'
        );

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Reminder sent successfully!'
        ]);

        $this->loadTimeline();
    }

    // Add Note
    public function addNote()
    {
        $this->validate([
            'newNote' => 'required|string|max:500',
        ]);

        $this->interview->history()->create([
            'application_id' => $this->interview->application_id,
            'user_id' => auth()->id(),
            'action' => 'note_added',
            'notes' => $this->newNote,
            'metadata' => [
                'added_by' => auth()->user()->full_name,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->newNote = '';
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Note added successfully!'
        ]);

        $this->loadTimeline();
    }

    // Add Feedback
    public function openFeedbackModal()
    {
        $this->feedbackMessage = '';
        $this->feedbackType = 'general';
        $this->showFeedbackModal = true;
    }

    public function submitFeedback()
    {
        $this->validate([
            'feedbackMessage' => 'required|string|max:1000',
            'feedbackType' => 'required|in:general,technical,communication,preparation,other',
        ]);

        $this->interview->history()->create([
            'application_id' => $this->interview->application_id,
            'user_id' => auth()->id(),
            'action' => 'feedback_added',
            'notes' => $this->feedbackMessage,
            'metadata' => [
                'type' => $this->feedbackType,
                'added_by' => auth()->user()->full_name,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->showFeedbackModal = false;
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Feedback submitted successfully!'
        ]);

        $this->loadTimeline();
    }

    // Navigation
    public function goToPreviousInterview()
    {
        $previous = Interview::where('id', '<', $this->interview->id)
            ->whereHas('application', function ($q) {
                $q->whereIn('status', [
                    ApplicationStatus::INTERVIEW_SCHEDULED->value,
                    ApplicationStatus::INTERVIEW_COMPLETED->value
                ]);
            })
            ->latest('id')
            ->first();

        if ($previous) {
            return redirect()->route('admin.interviews.show', $previous->id);
        }

        $this->dispatch('show-toast', [
            'type' => 'info',
            'message' => 'No previous interview found'
        ]);
    }

    public function goToNextInterview()
    {
        $next = Interview::where('id', '>', $this->interview->id)
            ->whereHas('application', function ($q) {
                $q->whereIn('status', [
                    ApplicationStatus::INTERVIEW_SCHEDULED->value,
                    ApplicationStatus::INTERVIEW_COMPLETED->value
                ]);
            })
            ->oldest('id')
            ->first();

        if ($next) {
            return redirect()->route('admin.interviews.show', $next->id);
        }

        $this->dispatch('show-toast', [
            'type' => 'info',
            'message' => 'No next interview found'
        ]);
    }

    public function goToApplication()
    {
        return redirect()->route('admin.applications.show', $this->interview->application_id);
    }

    public function goToStudent()
    {
        return redirect()->route('admin.users.show', $this->interview->application->student_id);
    }


    public function render()
    {
        return view('livewire.admin.interviews.show', [
            'timelineEvents' => $this->timelineEvents,
            'similarApplications' => $this->interview->application->opportunity->applications
                ->where('id', '!=', $this->interview->application_id)
                ->take(5),
        ]);
    }
}
