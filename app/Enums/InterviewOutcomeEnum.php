<?php 

namespace App\Enums;

use App\Traits\EnumHelpers;

enum InterviewOutcomeEnum: string
{
    use EnumHelpers;

    case SUCCESSFUL = 'successful';
    case UNSUCCESSFUL = 'unsuccessful';
    case PENDING = 'pending';
    case RESCHEDULED = 'rescheduled';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';
    case OFFER_EXTENDED = 'offer_extended';
    case OFFER_ACCEPTED = 'offer_accepted';
    case OFFER_DECLINED = 'offer_declined';

     /**
     * Get the display label for the outcome
     */
    public function label(): string
    {
        return match($this) {
            self::SUCCESSFUL => 'Successful',
            self::UNSUCCESSFUL => 'Unsuccessful',
            self::PENDING => 'Pending',
            self::RESCHEDULED => 'Rescheduled',
            self::CANCELLED => 'Cancelled',
            self::NO_SHOW => 'No Show',
            self::OFFER_EXTENDED => 'Offer Extended',
            self::OFFER_ACCEPTED => 'Offer Accepted',
            self::OFFER_DECLINED => 'Offer Declined',
        };
    }

    /**
     * Get the badge color for the outcome
     */
    public function color(): string
    {
        return match($this) {
            self::SUCCESSFUL, self::OFFER_ACCEPTED => 'success',
            self::OFFER_EXTENDED => 'info',
            self::RESCHEDULED => 'warning',
            self::UNSUCCESSFUL, self::OFFER_DECLINED, self::CANCELLED => 'danger',
            self::PENDING => 'secondary',
            self::NO_SHOW => 'dark',
        };
    }

    /**
     * Get the icon for the outcome
     */
    public function icon(): string
    {
        return match($this) {
            self::SUCCESSFUL => 'fa-check-circle',
            self::UNSUCCESSFUL => 'fa-times-circle',
            self::PENDING => 'fa-clock',
            self::RESCHEDULED => 'fa-calendar-alt',
            self::CANCELLED => 'fa-ban',
            self::NO_SHOW => 'fa-user-slash',
            self::OFFER_EXTENDED => 'fa-handshake',
            self::OFFER_ACCEPTED => 'fa-check-double',
            self::OFFER_DECLINED => 'fa-thumbs-down',
        };
    }

    /**
     * Get the description for the outcome
     */
    public function description(): string
    {
        return match($this) {
            self::SUCCESSFUL => 'Interview was successful, candidate progressing to next stage',
            self::UNSUCCESSFUL => 'Interview was unsuccessful, candidate not progressing',
            self::PENDING => 'Awaiting outcome decision',
            self::RESCHEDULED => 'Interview has been rescheduled',
            self::CANCELLED => 'Interview has been cancelled',
            self::NO_SHOW => 'Candidate did not attend the interview',
            self::OFFER_EXTENDED => 'Job offer has been extended to candidate',
            self::OFFER_ACCEPTED => 'Candidate accepted the job offer',
            self::OFFER_DECLINED => 'Candidate declined the job offer',
        };
    }

    /**
     * Check if outcome is positive
     */
    public function isPositive(): bool
    {
        return in_array($this, [
            self::SUCCESSFUL,
            self::OFFER_EXTENDED,
            self::OFFER_ACCEPTED,
        ]);
    }

    /**
     * Check if outcome is negative
     */
    public function isNegative(): bool
    {
        return in_array($this, [
            self::UNSUCCESSFUL,
            self::OFFER_DECLINED,
            self::CANCELLED,
            self::NO_SHOW,
        ]);
    }

    /**
     * Check if outcome is terminal (no further action)
     */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::OFFER_ACCEPTED,
            self::OFFER_DECLINED,
            self::UNSUCCESSFUL,
            self::CANCELLED,
            self::NO_SHOW,
        ]);
    }
}