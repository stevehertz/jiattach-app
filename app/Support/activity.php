<?php

use App\Traits\LogsActivity;

if (! function_exists('activity_log')) {
    function activity_log(
        string $description,
        ?string $event = null,
        array $properties = [],
        ?string $logName = null
    ) {
        return LogsActivity::logActivity(
            $description,
            $event,
            $properties,
            $logName
        );
    }
}