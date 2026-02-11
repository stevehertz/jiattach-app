<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the notifiable entity that the notification belongs to.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread(): void
    {
        if (!is_null($this->read_at)) {
            $this->update(['read_at' => null]);
        }
    }

    /**
     * Determine if a notification has been read.
     */
    public function read(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Determine if a notification has not been read.
     */
    public function unread(): bool
    {
        return $this->read_at === null;
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include notifications of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include notifications for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('notifiable_type', 'App\Models\User')
                    ->where('notifiable_id', $userId);
    }

    /**
     * Get the notification title from data.
     */
    public function getTitleAttribute(): string
    {
        return $this->data['title'] ?? 'Notification';
    }

    /**
     * Get the notification message from data.
     */
    public function getMessageAttribute(): string
    {
        return $this->data['message'] ?? '';
    }

    /**
     * Get the notification icon from data.
     */
    public function getIconAttribute(): string
    {
        return $this->data['icon'] ?? 'fas fa-bell';
    }

    /**
     * Get the notification URL from data.
     */
    public function getUrlAttribute(): ?string
    {
        return $this->data['url'] ?? null;
    }

    /**
     * Get the time ago for the notification.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the notification type in human readable format.
     */
    public function getTypeLabelAttribute(): string
    {
        $types = [
            'App\Notifications\PlacementNotification' => 'Placement',
            'App\Notifications\MentorshipNotification' => 'Mentorship',
            'App\Notifications\DocumentNotification' => 'Document',
            'App\Notifications\SystemNotification' => 'System',
        ];

        return $types[$this->type] ?? class_basename($this->type);
    }

}
