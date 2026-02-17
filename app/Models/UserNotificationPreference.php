<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_type',
        'channels',
        'is_enabled'
    ];

    protected $casts = [
        'channels' => 'array',
        'is_enabled' => 'boolean'
    ];

    /**
     * Get the user that owns the preference
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Available notification types
     */
    public static function getNotificationTypes(): array
    {
        return [
            'placement_match' => 'Placement Match Found',
            'placement_offered' => 'Placement Offered',
            'placement_confirmed' => 'Placement Confirmed',
            'mentorship_request' => 'Mentorship Request',
            'mentorship_session' => 'Mentorship Session',
            'mentorship_reminder' => 'Session Reminder',
            'document_approved' => 'Document Approved',
            'document_rejected' => 'Document Rejected',
            'system_alert' => 'System Alert',
            'opportunity_deadline' => 'Opportunity Deadline'
        ];
    }

    /**
     * Available channels
     */
    public static function getAvailableChannels(): array
    {
        return [
            'email' => 'Email',
            'sms' => 'SMS',
            'push' => 'Push Notification',
            'in_app' => 'In-App Notification'
        ];
    }
}
