<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

     protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'success',
        'attempted_at'
    ];

    protected $casts = [
        'success' => 'boolean',
        'attempted_at' => 'datetime'
    ];

    /**
     * Get failed attempts for an email in the last X minutes
     */
    public static function getRecentFailedAttempts($email, $minutes = 60)
    {
        return self::where('email', $email)
            ->where('success', false)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Clean old login attempts
     */
    public static function cleanOldRecords($days = 30)
    {
        return self::where('attempted_at', '<', now()->subDays($days))->delete();
    }
}
