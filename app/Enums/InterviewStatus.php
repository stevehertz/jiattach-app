<?php

namespace App\Enums;

use App\Traits\EnumHelpers;


enum InterviewStatus: string
{
    use EnumHelpers;

    case SCHEDULED = 'scheduled';
    case RESCHEDULED = 'rescheduled';
    case CONFIRMED = 'confirmed';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';
    case RESCHEDULE_REQUESTED = 'reschedule_requested';

    /**
     * Get the display label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Scheduled',
            self::RESCHEDULED => 'Rescheduled',
            self::CONFIRMED => 'Confirmed',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::NO_SHOW => 'No Show',
            self::RESCHEDULE_REQUESTED => 'Reschedule Requested',
        };
    }

    /**
     * Get the badge color for the status
     */
    public function color(): string
    {
        return match ($this) {
            self::SCHEDULED => 'primary',
            self::RESCHEDULED => 'warning',
            self::CONFIRMED => 'info',
            self::IN_PROGRESS => 'secondary',
            self::COMPLETED => 'success',
            self::CANCELLED, self::NO_SHOW => 'danger',
            self::RESCHEDULE_REQUESTED => 'warning',
        };
    }

    /**
     * Get the icon for the status
     */
    public function icon(): string
    {
        return match ($this) {
            self::SCHEDULED => 'fa-calendar-plus',
            self::RESCHEDULED => 'fa-calendar-alt',
            self::CONFIRMED => 'fa-check-circle',
            self::IN_PROGRESS => 'fa-spinner',
            self::COMPLETED => 'fa-check-double',
            self::CANCELLED => 'fa-times-circle',
            self::NO_SHOW => 'fa-user-slash',
            self::RESCHEDULE_REQUESTED => 'fa-question-circle',
        };
    }

    /**
     * Get the description for the status
     */
    public function description(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Interview has been scheduled and is awaiting confirmation',
            self::RESCHEDULED => 'Interview has been rescheduled to a new date/time',
            self::CONFIRMED => 'Interview has been confirmed by all parties',
            self::IN_PROGRESS => 'Interview is currently in progress',
            self::COMPLETED => 'Interview has been successfully completed',
            self::CANCELLED => 'Interview has been cancelled',
            self::NO_SHOW => 'Candidate did not show up for the interview',
            self::RESCHEDULE_REQUESTED => 'Candidate or interviewer has requested to reschedule',
        };
    }

    /**
     * Get the next possible statuses based on current status
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::SCHEDULED => [
                self::CONFIRMED,
                self::RESCHEDULED,
                self::CANCELLED,
                self::RESCHEDULE_REQUESTED,
            ],
            self::RESCHEDULED => [
                self::SCHEDULED,
                self::CONFIRMED,
                self::CANCELLED,
            ],
            self::CONFIRMED => [
                self::IN_PROGRESS,
                self::RESCHEDULED,
                self::CANCELLED,
            ],
            self::IN_PROGRESS => [
                self::COMPLETED,
                self::CANCELLED,
            ],
            self::RESCHEDULE_REQUESTED => [
                self::SCHEDULED,
                self::RESCHEDULED,
                self::CANCELLED,
            ],
            self::COMPLETED => [], // Terminal state
            self::CANCELLED => [], // Terminal state
            self::NO_SHOW => [], // Terminal state
        };
    }

     /**
     * Check if transition to given status is allowed
     */
    public function canTransitionTo(self|string $status): bool
    {
        $targetStatus = $status instanceof self ? $status : self::from($status);
        return in_array($targetStatus, $this->allowedTransitions());
    }

    /**
     * Get all active statuses (non-terminal)
     */
    public static function active(): array
    {
        return [
            self::SCHEDULED,
            self::RESCHEDULED,
            self::CONFIRMED,
            self::IN_PROGRESS,
            self::RESCHEDULE_REQUESTED,
        ];
    }

    /**
     * Get all terminal statuses
     */
    public static function terminal(): array
    {
        return [
            self::COMPLETED,
            self::CANCELLED,
            self::NO_SHOW,
        ];
    }

    /**
     * Get all positive outcome statuses
     */
    public static function positive(): array
    {
        return [
            self::COMPLETED,
        ];
    }

    /**
     * Get all negative outcome statuses
     */
    public static function negative(): array
    {
        return [
            self::CANCELLED,
            self::NO_SHOW,
        ];
    }

    /**
     * Check if status is active (non-terminal)
     */
    public function isActive(): bool
    {
        return in_array($this, self::active());
    }

    /**
     * Check if status is terminal
     */
    public function isTerminal(): bool
    {
        return in_array($this, self::terminal());
    }

    /**
     * Check if status is positive outcome
     */
    public function isPositive(): bool
    {
        return in_array($this, self::positive());
    }

    /**
     * Check if status is negative outcome
     */
    public function isNegative(): bool
    {
        return in_array($this, self::negative());
    }

    /**
     * Check if interview can be rescheduled
     */
    public function canReschedule(): bool
    {
        return in_array($this, [
            self::SCHEDULED,
            self::CONFIRMED,
            self::RESCHEDULE_REQUESTED,
        ]);
    }

    /**
     * Check if interview can be cancelled
     */
    public function canCancel(): bool
    {
        return !in_array($this, [
            self::COMPLETED,
            self::CANCELLED,
            self::NO_SHOW,
        ]);
    }

    /**
     * Check if feedback can be added
     */
    public function canAddFeedback(): bool
    {
        return in_array($this, [
            self::COMPLETED,
            self::NO_SHOW,
        ]);
    }

    /**
     * Get status for select dropdown
     */
    public static function forSelect(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    /**
     * Get status with all metadata
     */
    public function metadata(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
            'color' => $this->color(),
            'icon' => $this->icon(),
            'description' => $this->description(),
            'is_active' => $this->isActive(),
            'is_terminal' => $this->isTerminal(),
            'is_positive' => $this->isPositive(),
            'is_negative' => $this->isNegative(),
            'can_reschedule' => $this->canReschedule(),
            'can_cancel' => $this->canCancel(),
            'can_add_feedback' => $this->canAddFeedback(),
            'allowed_transitions' => array_map(
                fn($status) => [
                    'value' => $status->value,
                    'label' => $status->label(),
                    'color' => $status->color(),
                    'icon' => $status->icon(),
                ],
                $this->allowedTransitions()
            ),
        ];
    }
}
