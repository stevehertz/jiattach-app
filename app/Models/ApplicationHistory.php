<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'user_id',
        'student_id',
        'organization_id',
        'old_status',
        'new_status',
        'action',
        'notes',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /*
     * Get the user who performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
     * Get the user who performed this action.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }


    /*
     * Get the related application.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    /*
        * Get the related organization.
        */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }


    /**
     * Get the old status enum instance.
     */
    public function getOldStatusEnumAttribute(): ?ApplicationStatus
    {
        return $this->old_status ? ApplicationStatus::tryFrom($this->old_status) : null;
    }

    /**
     * Get the new status enum instance.
     */
    public function getNewStatusEnumAttribute(): ?ApplicationStatus
    {
        return $this->new_status ? ApplicationStatus::tryFrom($this->new_status) : null;
    }

    /**
     * Get the old status label.
     */
    public function getOldStatusLabelAttribute(): ?string
    {
        return $this->old_status_enum?->label();
    }

    /**
     * Get the new status label.
     */
    public function getNewStatusLabelAttribute(): ?string
    {
        return $this->new_status_enum?->label();
    }

    /**
     * Get the action icon based on action type.
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'created' => 'fas fa-plus-circle text-success',
            'status_changed' => 'fas fa-sync-alt text-primary',
            'interview_scheduled' => 'fas fa-calendar-check text-warning',
            'interview_completed' => 'fas fa-check-circle text-success',
            'interview_cancelled' => 'fas fa-times-circle text-danger',
            'offer_sent' => 'fas fa-paper-plane text-info',
            'offer_accepted' => 'fas fa-handshake text-success',
            'offer_rejected' => 'fas fa-times-circle text-danger',
            'hired' => 'fas fa-briefcase text-success',
            'rejected' => 'fas fa-ban text-danger',
            'cancelled' => 'fas fa-times-circle text-danger',
            'note_added' => 'fas fa-sticky-note text-info',
            'document_uploaded' => 'fas fa-upload text-primary',
            'document_downloaded' => 'fas fa-download text-secondary',
            'email_sent' => 'fas fa-envelope text-primary',
            default => 'fas fa-history text-secondary'
        };
    }


    /**
     * Get formatted time ago.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Scope to get history for a specific application.
     */
    public function scopeForApplication($query, $applicationId)
    {
        return $query->where('application_id', $applicationId);
    }

    /**
     * Scope to get history for a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get history for a specific user.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }


    /**
     * Scope to get history for a specific action.
     */
    public function scopeWithAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get history for a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('new_status', $status);
    }

    /**
     * Scope to get today's history.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope to get history within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
