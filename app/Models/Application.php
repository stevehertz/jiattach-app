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
    ];

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
