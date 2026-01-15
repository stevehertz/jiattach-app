<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Log an activity.
     */
    public static function logActivity($description, $event = null, $properties = [], $logName = null)
    {
        $log = [
            'log_name' => $logName ?? config('activitylog.default_log_name', 'default'),
            'description' => $description,
            'event' => $event,
            'properties' => $properties,
            'causer_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ];

        return ActivityLog::create($log);
    }

    /**
     * Log activity for this model.
     */
    public function logModelActivity($description, $event = null, $properties = [])
    {
        $log = [
            'log_name' => strtolower(class_basename($this)),
            'description' => $description,
            'event' => $event,
            'properties' => $properties,
            'subject_id' => $this->id,
            'subject_type' => get_class($this),
            'causer_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ];

        return ActivityLog::create($log);
    }
}