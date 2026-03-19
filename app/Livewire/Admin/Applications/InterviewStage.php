<?php

namespace App\Livewire\Admin\Applications;

use App\Enums\ApplicationStatus;
use App\Models\Interview;
use Livewire\Component;
use Livewire\WithPagination;

class InterviewStage extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $interviewType = '';
    public $interviewStatus = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $organizationFilter = '';
    public $sortField = 'scheduled_at';
    public $sortDirection = 'asc';
    public $perPage = 20;

    // View type
    public $viewType = 'all'; // all, today, upcoming, completed, pending

    // Bulk actions
    public $selectedInterviews = [];
    public $selectAll = false;
    public $showBulkActions = false;
    public $bulkAction = '';
    public $showBulkActionModal = false;

    // Interview management
    public $showInterviewModal = false;
    public $editingInterview = null;
    public $interviewDate;
    public $interviewTime;
    public $interviewType_new;
    public $interviewDuration;
    public $interviewLocation;
    public $interviewMeetingLink;
    public $interviewPhoneNumber;
    public $interviewNotes;
    public $interviewerId;
    public $interviewStatus_new;
    public $interviewFeedback;

    protected $listeners = [
        'refreshInterviews' => '$refresh',
        'confirmReschedule',
        'confirmCancel',
        'confirmComplete'
    ];

    public function mount($viewType = 'all')
    {
        $this->viewType = $viewType;
    }

    public function getInterviewsQuery()
    {
        $query = Interview::with([
            'application.student.studentProfile',
            'application.opportunity.organization',
            'scheduledBy',
            'interviewer'
        ])
            ->whereHas('application', function ($q) {
                $q->whereIn('status', [
                    ApplicationStatus::INTERVIEW_SCHEDULED->value,
                    ApplicationStatus::INTERVIEW_COMPLETED->value
                ]);
            })
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('application.student', function ($q) use ($search) {
                        $q->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                        ->orWhereHas('application.opportunity', function ($q) use ($search) {
                            $q->where('title', 'like', '%' . $search . '%');
                        })
                        ->orWhere('location', 'like', '%' . $search . '%')
                        ->orWhere('notes', 'like', '%' . $search . '%');
                });
            })
            ->when($this->interviewType, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($this->interviewStatus, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($this->organizationFilter, function ($query, $orgId) {
                $query->whereHas('application.opportunity', function ($q) use ($orgId) {
                    $q->where('organization_id', $orgId);
                });
            })
            ->when($this->dateFrom, function ($query, $date) {
                $query->whereDate('scheduled_at', '>=', $date);
            })
            ->when($this->dateTo, function ($query, $date) {
                $query->whereDate('scheduled_at', '<=', $date);
            });

        // Apply view type filters
        switch ($this->viewType) {
            case 'today':
                $query->whereDate('scheduled_at', today());
                break;
            case 'upcoming':
                $query->where('scheduled_at', '>', now())
                    ->whereNotIn('status', ['completed', 'cancelled', 'no_show']);
                break;
            case 'completed':
                $query->whereIn('status', ['completed', 'no_show']);
                break;
            case 'pending':
                $query->where('status', 'scheduled')
                    ->where('scheduled_at', '<', now())
                    ->whereDoesntHave('history', function ($q) {
                        $q->where('action', 'reminder_sent');
                    });
                break;
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    public function getInterviewsProperty()
    {
        return $this->getInterviewsQuery()->paginate($this->perPage);
    }

    public function getStatsProperty()
    {
        $baseQuery = Interview::whereHas('application', function ($q) {
            $q->whereIn('status', [
                ApplicationStatus::INTERVIEW_SCHEDULED->value,
                ApplicationStatus::INTERVIEW_COMPLETED->value
            ]);
        });

        return [
            'total' => $baseQuery->count(),
            'today' => $baseQuery->whereDate('scheduled_at', today())->count(),
            'upcoming' => $baseQuery->where('scheduled_at', '>', now())
                ->whereNotIn('status', ['completed', 'cancelled', 'no_show'])
                ->count(),
            'completed' => $baseQuery->where('status', 'completed')->count(),
            'pending' => $baseQuery->where('status', 'scheduled')
                ->where('scheduled_at', '<', now())
                ->count(),
            'cancelled' => $baseQuery->where('status', 'cancelled')->count(),
            'no_show' => $baseQuery->where('status', 'no_show')->count(),
        ];
    }

    public function getOrganizationsProperty()
    {
        return \App\Models\Organization::whereHas('opportunities.applications', function ($q) {
            $q->whereIn('status', [
                ApplicationStatus::INTERVIEW_SCHEDULED->value,
                ApplicationStatus::INTERVIEW_COMPLETED->value
            ]);
        })->orderBy('name')->get(['id', 'name']);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'interviewType',
            'interviewStatus',
            'dateFrom',
            'dateTo',
            'organizationFilter',
            'sortField',
            'sortDirection'
        ]);
        $this->sortField = 'interview_scheduled_at';
        $this->sortDirection = 'asc';
    }

    // Interview Management Methods
    public function viewInterview($interviewId)
    {
        return redirect()->route('admin.interviews.show', $interviewId);
    }

    public function editInterview($interviewId)
    {
        $this->editingInterview = Interview::with('application')->findOrFail($interviewId);

        $this->interviewDate = $this->editingInterview->scheduled_at->format('Y-m-d');
        $this->interviewTime = $this->editingInterview->scheduled_at->format('H:i');
        $this->interviewType_new = $this->editingInterview->type;
        $this->interviewDuration = $this->editingInterview->duration_minutes;
        $this->interviewLocation = $this->editingInterview->location;
        $this->interviewMeetingLink = $this->editingInterview->meeting_link;
        $this->interviewPhoneNumber = $this->editingInterview->phone_number;
        $this->interviewNotes = $this->editingInterview->notes;
        $this->interviewerId = $this->editingInterview->interviewer_id;
        $this->interviewStatus_new = $this->editingInterview->status;

        $this->showInterviewModal = true;
    }

    public function updateInterview()
    {
        $this->validate([
            'interviewDate' => 'required|date',
            'interviewTime' => 'required',
            'interviewType_new' => 'required|in:online,phone,in_person',
            'interviewDuration' => 'required|integer|min:15|max:480',
            'interviewLocation' => 'required_if:interviewType_new,in_person|nullable|string|max:255',
            'interviewMeetingLink' => 'required_if:interviewType_new,online|nullable|url|max:255',
            'interviewPhoneNumber' => 'required_if:interviewType_new,phone|nullable|string|max:20',
            'interviewNotes' => 'nullable|string|max:1000',
            'interviewerId' => 'nullable|exists:users,id',
            'interviewStatus_new' => 'required|in:scheduled,rescheduled,completed,cancelled,no_show',
        ]);

        $scheduledAt = \Carbon\Carbon::parse($this->interviewDate . ' ' . $this->interviewTime);
        $oldData = [
            'scheduled_at' => $this->editingInterview->scheduled_at,
            'type' => $this->editingInterview->type,
            'status' => $this->editingInterview->status,
        ];

        $this->editingInterview->update([
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $this->interviewDuration,
            'type' => $this->interviewType_new,
            'location' => $this->interviewLocation,
            'meeting_link' => $this->interviewMeetingLink,
            'phone_number' => $this->interviewPhoneNumber,
            'notes' => $this->interviewNotes,
            'interviewer_id' => $this->interviewerId,
            'status' => $this->interviewStatus_new,
        ]);

        // Add history record
        $this->editingInterview->history()->create([
            'application_id' => $this->editingInterview->application_id,
            'user_id' => auth()->id(),
            'action' => 'updated',
            'old_values' => $oldData,
            'new_values' => [
                'scheduled_at' => $scheduledAt,
                'type' => $this->interviewType_new,
                'status' => $this->interviewStatus_new,
            ],
            'notes' => 'Interview details updated',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->showInterviewModal = false;
        $this->editingInterview = null;

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Interview updated successfully!'
        ]);
    }

    public function confirmReschedule($interviewId)
    {
        $this->dispatch('confirm-action', [
            'interviewId' => $interviewId,
            'action' => 'reschedule',
            'title' => 'Reschedule Interview?',
            'text' => 'This will mark the interview as rescheduled and you can set a new date/time.',
            'confirmButtonText' => 'Yes, Reschedule',
        ]);
    }

    public function rescheduleInterview($interviewId)
    {
        $interview = Interview::findOrFail($interviewId);

        // This would typically open a modal, but for now we'll redirect
        return redirect()->route('admin.interviews.reschedule', $interviewId);
    }

    public function confirmCancel($interviewId)
    {
        $this->dispatch('confirm-action', [
            'interviewId' => $interviewId,
            'action' => 'cancel',
            'title' => 'Cancel Interview?',
            'text' => 'Are you sure you want to cancel this interview? This action cannot be undone.',
            'confirmButtonText' => 'Yes, Cancel',
        ]);
    }

    public function cancelInterview($interviewId)
    {
        $interview = Interview::findOrFail($interviewId);
        $interview->cancel('Cancelled by admin');

        $this->dispatch('show-toast', [
            'type' => 'warning',
            'message' => 'Interview cancelled successfully!'
        ]);
    }

    public function confirmComplete($interviewId)
    {
        $this->dispatch('show-complete-modal', [
            'interviewId' => $interviewId
        ]);
    }

    public function completeInterview($interviewId, $feedback = null)
    {
        $interview = Interview::findOrFail($interviewId);
        $interview->markAsCompleted($feedback);

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Interview marked as completed!'
        ]);
    }

    public function markNoShow($interviewId)
    {
        $interview = Interview::findOrFail($interviewId);

        $interview->update([
            'status' => 'no_show',
            'completed_at' => now(),
        ]);

        // Add history
        $interview->history()->create([
            'application_id' => $interview->application_id,
            'user_id' => auth()->id(),
            'action' => 'no_show',
            'notes' => 'Student did not show up for interview',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->dispatch('show-toast', [
            'type' => 'warning',
            'message' => 'Marked as no-show'
        ]);
    }

    public function sendReminder($interviewId)
    {
        $interview = Interview::with('application.student')->findOrFail($interviewId);

        // Here you would implement your notification logic
        // For now, we'll just log it

        $interview->update(['reminder_sent_at' => now()]);

        $interview->history()->create([
            'application_id' => $interview->application_id,
            'user_id' => auth()->id(),
            'action' => 'reminder_sent',
            'notes' => 'Reminder sent to student',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Reminder sent successfully!'
        ]);
    }

    // Bulk Actions
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedInterviews = $this->getInterviewsQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedInterviews = [];
        }
    }

    public function updatedSelectedInterviews()
    {
        $this->selectAll = false;
        $this->showBulkActions = count($this->selectedInterviews) > 0;
    }

    public function applyBulkAction()
    {
        if (!$this->bulkAction || empty($this->selectedInterviews)) {
            return;
        }

        $interviews = Interview::whereIn('id', $this->selectedInterviews)->get();

        switch ($this->bulkAction) {
            case 'send_reminders':
                foreach ($interviews as $interview) {
                    $this->sendReminder($interview->id);
                }
                $message = 'Reminders sent to ' . count($interviews) . ' interviews';
                break;

            case 'mark_completed':
                foreach ($interviews as $interview) {
                    if ($interview->status === 'scheduled') {
                        $interview->markAsCompleted('Bulk completion');
                    }
                }
                $message = count($interviews) . ' interviews marked as completed';
                break;

            case 'export':
                return $this->exportInterviews();
                break;

            default:
                return;
        }

        $this->selectedInterviews = [];
        $this->showBulkActions = false;
        $this->bulkAction = '';

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    private function exportInterviews()
    {
        $interviews = Interview::whereIn('id', $this->selectedInterviews)
            ->with(['application.student', 'application.opportunity.organization'])
            ->get();

        $csvData = [];

        // Headers
        $csvData[] = [
            'Interview ID',
            'Student Name',
            'Student Email',
            'Opportunity',
            'Organization',
            'Interview Date',
            'Interview Time',
            'Type',
            'Status',
            'Duration',
            'Location/Link',
            'Interviewer',
            'Notes',
        ];

        // Data
        foreach ($interviews as $interview) {
            $csvData[] = [
                $interview->id,
                $interview->application->student->full_name,
                $interview->application->student->email,
                $interview->application->opportunity->title,
                $interview->application->opportunity->organization->name,
                $interview->scheduled_at->format('Y-m-d'),
                $interview->scheduled_at->format('H:i'),
                ucfirst($interview->type),
                ucfirst($interview->status),
                $interview->duration_minutes . ' mins',
                $interview->meeting_details ?? $interview->location ?? 'N/A',
                $interview->interviewer?->full_name ?? 'Not assigned',
                $interview->notes ?? '',
            ];
        }

        $filename = 'interviews_export_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.admin.applications.interview-stage', [
            'interviews' => $this->interviews,
            'stats' => $this->stats,
            'organizations' => $this->organizations,
        ]);
    }
}
