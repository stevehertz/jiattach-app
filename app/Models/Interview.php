<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\InterviewStatus;
use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use HasFactory, SoftDeletes, LogsModelActivity;

    protected $fillable = [
        'application_id',
        'scheduled_by',
        'interviewer_id',
        'scheduled_at',
        'duration_minutes',
        'type',
        'location',
        'meeting_link',
        'phone_number',
        'status',
        'notes',
        'feedback',
        'interviewers',
        'documents',
        'reminder_sent_at',
        'rescheduled_at',
        'cancelled_at',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'rescheduled_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'interviewers' => 'array',
        'documents' => 'array',
        'status' => InterviewStatus::class,
    ];

    /**
     * Get the application this interview belongs to.
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the user who scheduled this interview.
     */
    public function scheduledBy()
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }

    /**
     * Get the main interviewer.
     */
    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    /**
     * Get the history records for this interview.
     */
    public function history()
    {
        return $this->hasMany(InterviewHistory::class);
    }

    /**
     * Check if interview is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->status === 'scheduled' && $this->scheduled_at->isFuture();
    }

    /**
     * Check if interview is today.
     */
    public function isToday(): bool
    {
        return $this->scheduled_at->isToday();
    }

    /**
     * Check if interview is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if interview was cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get the interview type badge color.
     */
    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'online' => 'info',
            'phone' => 'warning',
            'in_person' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Get the status badge color.
     */
    public function getStatusBadgeAttribute(): string
    {
        return "<span class='badge badge-{$this->status->color()} p-2'>{$this->status->label()}</span>";
    }

    /**
     * Get the meeting type icon.
     */
    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'online' => 'fa-video',
            'phone' => 'fa-phone',
            'in_person' => 'fa-building',
            default => 'fa-calendar'
        };
    }

    /**
     * Get the duration in hours/minutes format.
     */
    public function getDurationFormattedAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Get the meeting details/instructions.
     */
    public function getMeetingDetailsAttribute(): ?string
    {
        return match ($this->type) {
            'online' => $this->meeting_link ?? 'Link to be provided',
            'phone' => $this->phone_number ?? 'Phone number to be provided',
            'in_person' => $this->location ?? 'Location to be provided',
            default => null
        };
    }

    /**
     * Mark interview as completed with feedback.
     */
    public function markAsCompleted(string $feedback = null): void
    {
        $this->update([
            'status' => 'completed',
            'feedback' => $feedback,
            'completed_at' => now(),
        ]);

        // Update application status to INTERVIEW_COMPLETED
        $this->application->update([
            'status' => ApplicationStatus::INTERVIEW_COMPLETED,
            'interview_completed_at' => now(),
        ]);

        // Add history
        $this->history()->create([
            'application_id' => $this->application_id,
            'user_id' => auth()->id(),
            'action' => 'interview_completed',
            'notes' => 'Interview marked as completed',
            'metadata' => ['feedback' => $feedback],
        ]);
    }

    /**
     * Cancel interview.
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'notes' => $reason,
        ]);

        // Add history
        $this->history()->create([
            'application_id' => $this->application_id,
            'user_id' => auth()->id(),
            'action' => 'interview_cancelled',
            'notes' => $reason ?? 'Interview cancelled',
        ]);
    }

    /**
     * Reschedule interview.
     */
    public function reschedule($newDateTime, string $reason = null): void
    {
        $oldDateTime = $this->scheduled_at;

        $this->update([
            'scheduled_at' => $newDateTime,
            'status' => 'rescheduled',
            'rescheduled_at' => now(),
            'notes' => $reason,
        ]);

        // Add history
        $this->history()->create([
            'application_id' => $this->application_id,
            'user_id' => auth()->id(),
            'action' => 'interview_rescheduled',
            'notes' => $reason ?? 'Interview rescheduled',
            'metadata' => [
                'old_date' => $oldDateTime,
                'new_date' => $newDateTime,
            ],
        ]);
    }

    /**
     * Check if transition to given status is allowed
     */
    public function canTransitionTo(InterviewStatus|string $status): bool
    {
        if (is_string($status)) {
            $status = InterviewStatus::tryFrom($status);
            if (!$status) {
                return false;
            }
        }

        return $this->status->canTransitionTo($status);
    }

    /**
     * Get available next statuses with metadata
     */
    public function getAvailableNextStatuses(): array
    {
        $nextStatuses = [];

        foreach ($this->status->allowedTransitions() as $status) {
            $nextStatuses[$status->value] = [
                'value' => $status->value,
                'label' => $status->label(),
                'color' => $status->color(),
                'icon' => $status->icon(),
                'description' => $status->description(),
            ];
        }

        return $nextStatuses;
    }
}
