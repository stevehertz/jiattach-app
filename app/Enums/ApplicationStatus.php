<?php 

namespace App\Enums;

use App\Traits\EnumHelpers;

enum ApplicationStatus: string
{
    use EnumHelpers;

    case PENDING = 'Pending';
    case UNDER_REVIEW = 'under_review';
    case SHORTLISTED = 'shortlisted';
    case INTERVIEW_SCHEDULED = 'interview_scheduled';
    case INTERVIEW_COMPLETED = 'interview_completed';
    case OFFER_SENT = 'offer_sent';
    case OFFER_ACCEPTED = 'offer_accepted';
    case OFFER_REJECTED = 'offer_rejected';
    case HIRED = 'hired';
    case REJECTED = 'rejected';

    /**
     * Get the display label for the status
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::UNDER_REVIEW => 'Under Review',
            self::SHORTLISTED => 'Shortlisted',
            self::INTERVIEW_SCHEDULED => 'Interview Scheduled',
            self::INTERVIEW_COMPLETED => 'Interview Completed',
            self::OFFER_SENT => 'Offer Sent',
            self::OFFER_ACCEPTED => 'Offer Accepted',
            self::OFFER_REJECTED => 'Offer Rejected',
            self::HIRED => 'Hired',
            self::REJECTED => 'Rejected',
        };
    }

    /**
     * Get the badge color for the status
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'secondary',
            self::UNDER_REVIEW => 'info',
            self::SHORTLISTED => 'primary',
            self::INTERVIEW_SCHEDULED, self::INTERVIEW_COMPLETED => 'warning',
            self::OFFER_SENT, self::OFFER_ACCEPTED, self::HIRED => 'success',
            self::OFFER_REJECTED, self::REJECTED => 'danger',
        };
    }

    /**
     * Get the icon for the status
     */
    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'fa-clock',
            self::UNDER_REVIEW => 'fa-search',
            self::SHORTLISTED => 'fa-list-check',
            self::INTERVIEW_SCHEDULED, self::INTERVIEW_COMPLETED => 'fa-calendar-check',
            self::OFFER_SENT => 'fa-paper-plane',
            self::OFFER_ACCEPTED => 'fa-handshake',
            self::OFFER_REJECTED, self::REJECTED => 'fa-times-circle',
            self::HIRED => 'fa-briefcase',
        };
    }

    /**
     * Get the description for the status
     */
    public function description(): string
    {
        return match($this) {
            self::PENDING => 'Application has been submitted and is waiting for review',
            self::UNDER_REVIEW => 'Application is currently being reviewed by the employer',
            self::SHORTLISTED => 'Student has been shortlisted for consideration',
            self::INTERVIEW_SCHEDULED => 'Interview has been scheduled with the student',
            self::INTERVIEW_COMPLETED => 'Interview has been completed',
            self::OFFER_SENT => 'Offer has been sent to the student',
            self::OFFER_ACCEPTED => 'Student has accepted the offer',
            self::OFFER_REJECTED => 'Student has rejected the offer',
            self::HIRED => 'Student has been hired and placement is confirmed',
            self::REJECTED => 'Application has been rejected',
        };
    }

    /**
     * Get all possible next statuses for the current status
     */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::PENDING => [self::UNDER_REVIEW, self::REJECTED],
            self::UNDER_REVIEW => [self::SHORTLISTED, self::REJECTED],
            self::SHORTLISTED => [self::INTERVIEW_SCHEDULED, self::REJECTED],
            self::INTERVIEW_SCHEDULED => [self::INTERVIEW_COMPLETED, self::REJECTED],
            self::INTERVIEW_COMPLETED => [self::OFFER_SENT, self::REJECTED],
            self::OFFER_SENT => [self::OFFER_ACCEPTED, self::OFFER_REJECTED],
            self::OFFER_ACCEPTED => [self::HIRED],
            self::OFFER_REJECTED => [], // Terminal state
            self::HIRED => [], // Terminal state
            self::REJECTED => [], // Terminal state
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
            self::PENDING,
            self::UNDER_REVIEW,
            self::SHORTLISTED,
            self::INTERVIEW_SCHEDULED,
            self::INTERVIEW_COMPLETED,
            self::OFFER_SENT,
        ];
    }

    /**
     * Get all terminal statuses
     */
    public static function terminal(): array
    {
        return [
            self::OFFER_ACCEPTED,
            self::OFFER_REJECTED,
            self::HIRED,
            self::REJECTED,
        ];
    }

    /**
     * Get all positive outcome statuses
     */
    public static function positive(): array
    {
        return [
            self::OFFER_ACCEPTED,
            self::HIRED,
        ];
    }

    /**
     * Get all negative outcome statuses
     */
    public static function negative(): array
    {
        return [
            self::OFFER_REJECTED,
            self::REJECTED,
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

