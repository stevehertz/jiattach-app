<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_name',
        'description',
        'subject_id',
        'subject_type',
        'causer_id',
        'causer_type',
        'properties',
        'event',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

     protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that caused the activity.
     */
    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    /**
     * Scope a query to only include logs from today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope a query to only include logs for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('causer_id', $userId);
    }

    /**
     * Get activity time ago.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get activity icon based on event type.
     */
    public function getIconAttribute()
    {
        return match($this->event) {
            'created' => 'fa-plus-circle text-success',
            'updated' => 'fa-edit text-warning',
            'deleted' => 'fa-trash text-danger',
            'logged_in' => 'fa-sign-in-alt text-info',
            'logged_out' => 'fa-sign-out-alt text-info',
            default => 'fa-history text-secondary'
        };
    }
}
