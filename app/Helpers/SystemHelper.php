<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class SystemHelper
{
    /**
     * Get system health status
     */
    public static function getHealthStatus(): array
    {
        $status = [
            'overall' => 'healthy',
            'checks' => [],
            'issues' => 0,
        ];

        // Check disk space
        $diskUsage = disk_free_space('/') / disk_total_space('/') * 100;
        $status['checks']['disk_space'] = [
            'status' => $diskUsage > 90 ? 'critical' : ($diskUsage > 70 ? 'warning' : 'healthy'),
            'message' => 'Disk usage: ' . round($diskUsage, 2) . '%',
            'value' => $diskUsage,
        ];

        // Check memory usage
        $memoryUsage = memory_get_usage(true) / memory_get_peak_usage(true) * 100;
        $status['checks']['memory'] = [
            'status' => $memoryUsage > 90 ? 'critical' : ($memoryUsage > 70 ? 'warning' : 'healthy'),
            'message' => 'Memory usage: ' . round($memoryUsage, 2) . '%',
            'value' => $memoryUsage,
        ];

        // Check if debug mode is enabled
        $status['checks']['debug_mode'] = [
            'status' => config('app.debug') ? 'warning' : 'healthy',
            'message' => config('app.debug') ? 'Debug mode is enabled' : 'Debug mode is disabled',
            'value' => config('app.debug'),
        ];

        // Check database connection
        try {
            DB::connection()->getPdo();
            $status['checks']['database'] = [
                'status' => 'healthy',
                'message' => 'Database connection is OK',
                'value' => true,
            ];
        } catch (\Exception $e) {
            $status['checks']['database'] = [
                'status' => 'critical',
                'message' => 'Database connection failed: ' . $e->getMessage(),
                'value' => false,
            ];
        }

        // Count issues
        $status['issues'] = collect($status['checks'])->filter(function ($check) {
            return in_array($check['status'], ['critical', 'warning']);
        })->count();

        // Determine overall status
        if (collect($status['checks'])->contains('status', 'critical')) {
            $status['overall'] = 'critical';
        } elseif (collect($status['checks'])->contains('status', 'warning')) {
            $status['overall'] = 'warning';
        }

        return $status;
    }

    /**
     * Get system uptime
     */
    public static function getUptime(): string
    {
        if (function_exists('shell_exec')) {
            $uptime = shell_exec('uptime -p');
            return $uptime ? trim($uptime) : 'Unknown';
        }

        return 'Unknown';
    }

    /**
     * Get server load average
     */
    public static function getLoadAverage(): array
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return [
                '1min' => $load[0] ?? 0,
                '5min' => $load[1] ?? 0,
                '15min' => $load[2] ?? 0,
            ];
        }

        return ['1min' => 0, '5min' => 0, '15min' => 0];
    }

    /**
     * Get PHP information
     */
    public static function getPhpInfo(): array
    {
        return [
            'version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status()['opcache_enabled'],
            'extensions' => get_loaded_extensions(),
        ];
    }
}