<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes, LogsModelActivity;

    protected $fillable = [
        'user_id', // Logged in ID for admin tracking
        'student_id', // Student's User ID for relationship
        'attachment_opportunity_id',
        'organization_id',
        'payment_transaction_id',
        'match_score',
        'match_quality',
        'matched_criteria',
        'match_details',
        'cover_letter',
        'submitted_at',
        'status', // pending, reviewing, shortlisted, offered, accepted, rejected
        'employer_notes',
        'reviewed_at',
        'accepted_at',
        'declined_at',
        'decline_reason',
        'decline_feedback',
        'payment_completed_at',
        'offer_letter_generated_at',
        'offer_letter_url',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'match_score' => 'float',
        'match_quality' => 'string',
        'matched_criteria' => 'array',
        'match_details' => 'array',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
        'submitted_at' => 'datetime',
        'status' => ApplicationStatus::class,
        'payment_completed_at' => 'datetime',
        'offer_letter_generated_at' => 'datetime',
    ];

     /**
     * Get the payment transaction associated with this application.
     */
    public function paymentTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

    // Helper methods
    public function hasPaymentRequired()
    {
        // Check if payment is required for this application
        // Payment is required when interview is completed and payment not yet made
        return $this->status === ApplicationStatus::INTERVIEW_COMPLETED
            && !$this->payment_completed_at
            && (!$this->paymentTransaction || $this->paymentTransaction->status !== 'completed');
    }

    public function canGenerateOfferLetter()
    {
        return $this->payment_completed_at !== null
            && $this->status === ApplicationStatus::INTERVIEW_COMPLETED;
    }

    public function generateOfferLetter()
    {
        if (!$this->canGenerateOfferLetter()) {
            return false;
        }

        // Generate offer letter (you can implement PDF generation here)
        $this->status = ApplicationStatus::OFFER_SENT;
        $this->offer_sent_at = now();
        $this->offer_letter_generated_at = now();
        $this->save();

        // Log activity
        activity_log(
            "Offer letter generated for application #{$this->id}",
            'offer_letter_generated',
            [
                'application_id' => $this->id,
                'student_name' => $this->student->full_name,
                'opportunity' => $this->opportunity->title,
            ],
            'application'
        );

        return true;
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function opportunity()
    {
        return $this->belongsTo(AttachmentOpportunity::class, 'attachment_opportunity_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function placement()
    {
        return $this->hasOne(Placement::class);
    }

    // In your Application model, add this relationship:

    /**
     * Get the history records for this application.
     */
    public function history()
    {
        return $this->hasMany(ApplicationHistory::class)->latest();
    }

    /**
     * Get the interviews for this application.
     */
    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * Get the latest interview.
     */
    public function latestInterview()
    {
        return $this->hasOne(Interview::class)->latestOfMany();
    }

    /**
     * Get the upcoming interview.
     */
    public function upcomingInterview()
    {
        return $this->hasOne(Interview::class)
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>', now())
            ->latest('scheduled_at');
    }

    /**
     * Get the progress percentage for the application status.
     */
    public function getProgressPercentage(): ?int
    {
        $statuses = [
            'pending' => 10,
            'under_review' => 20,
            'shortlisted' => 40,
            'interview_scheduled' => 60,
            'interview_completed' => 80,
            'offer_sent' => 90,
            'offer_accepted' => 95,
            'hired' => 100,
            'rejected' => 100,
            'offer_rejected' => 100,
        ];

        return $statuses[$this->status->value] ?? 0;
    }



    /**
     * Get the timeline history grouped by date.
     */
    public function getTimelineHistoryAttribute()
    {
        return $this->history()
            ->with('user')
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m-d'));
    }

    /**
     * Add a history record for this application.
     */
    public function addHistory(
        string $action,
        $student_id = null,
        $organization_id = null,
        ?string $oldStatus = null,
        string $newStatus,
        ?string $notes = null,
        array $metadata = []
    ): ApplicationHistory {
        return $this->history()->create([
            'user_id' => auth()->id(),
            'student_id' => $student_id ?? $this->student_id,
            'organization_id' => $organization_id ?? $this->organization_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'action' => $action,
            'notes' => $notes,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }



    /**
     * Get the feedback for this application.
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    // Status Helper
    public function getStatusBadgeAttribute()
    {
        $color = $this->status->color();
        return "<span class='badge badge-{$color} p-2'>{$this->status->label()}</span>";
    }

    public function getStatusLabelAttribute()
    {
        return $this->status->label();
    }

    public function getStatusIconAttribute()
    {
        return $this->status->icon();
    }

    public function getStatusColorAttribute()
    {
        return $this->status->color();
    }


    /**
     * Check if status transition is valid using Enum
     */
    public function canTransitionTo(ApplicationStatus|string $newStatus): bool
    {
        if (is_string($newStatus)) {
            $newStatus = ApplicationStatus::tryFrom($newStatus);
            if (!$newStatus) {
                return false;
            }
        }

        return $this->status->canTransitionTo($newStatus);
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
