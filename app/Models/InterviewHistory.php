<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'interview_id',
        'application_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'notes',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
    ];

     /**
     * Get the interview this history belongs to.
     */
    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    /**
     * Get the application this history belongs to.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the user who performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the action icon.
     */
    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'scheduled' => 'fas fa-calendar-plus text-primary',
            'rescheduled' => 'fas fa-calendar-alt text-warning',
            'completed' => 'fas fa-check-circle text-success',
            'cancelled' => 'fas fa-times-circle text-danger',
            'no_show' => 'fas fa-user-slash text-danger',
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
}
