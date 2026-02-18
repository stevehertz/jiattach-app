<?php

namespace App\Livewire\Admin\SystemHealth;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public $systemInfo = [];
    public $serverInfo = [];
    public $databaseInfo = [];
    public $cacheInfo = [];
    public $storageInfo = [];
    public $queueInfo = [];
    public $securityInfo = [];
    public $performanceMetrics = [];

    public $refreshInterval = 60; // seconds
    public $lastRefresh;

    protected $listeners = ['refreshSystemHealth'];

    public function mount()
    {
        $this->lastRefresh = now();
        $this->loadSystemHealth();
    }

    public function loadSystemHealth()
    {
        $this->loadSystemInfo();
        $this->loadServerInfo();
        $this->loadDatabaseInfo();
        $this->loadCacheInfo();
        $this->loadStorageInfo();
        $this->loadQueueInfo();
        $this->loadSecurityInfo();
        $this->loadPerformanceMetrics();

        $this->lastRefresh = now();
    }

    private function loadSystemInfo()
    {
        $this->systemInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'server_name' => $_SERVER['SERVER_NAME'] ?? 'N/A',
            'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'N/A',
            'server_port' => $_SERVER['SERVER_PORT'] ?? 'N/A',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'maintenance_mode' => app()->isDownForMaintenance() ? 'Enabled' : 'Disabled',
            'url' => config('app.url'),
        ];
    }

    private function loadServerInfo()
    {
        $load = $this->getSystemLoad();
        $memory = $this->getMemoryUsage();
        $disk = $this->getDiskUsage();

        $this->serverInfo = [
            'os' => php_uname('s') . ' ' . php_uname('r'),
            'hostname' => php_uname('n'),
            'architecture' => php_uname('m'),
            'uptime' => $this->getUptime(),
            'load_average' => $load,
            'memory' => $memory,
            'disk' => $disk,
            'cpu_cores' => $this->getCpuCores(),
            'processes' => $this->getProcessCount(),
        ];
    }

    /**
 * Get system load average with cross-platform support
 */
private function getSystemLoad(): array
{
    // Unix/Linux systems
    if (function_exists('sys_getloadavg')) {
        $load = sys_getloadavg();
        return [
            '1min' => $load[0] ?? 0,
            '5min' => $load[1] ?? 0,
            '15min' => $load[2] ?? 0,
        ];
    }

    // Windows fallback using WMIC (if available)
    if (stripos(PHP_OS, 'WIN') === 0) {
        try {
            $wmic = shell_exec('wmic cpu get loadpercentage /value');
            if ($wmic && preg_match('/LoadPercentage=(\d+)/', $wmic, $matches)) {
                $percentage = (float) $matches[1];
                return [
                    '1min' => $percentage,
                    '5min' => $percentage,
                    '15min' => $percentage,
                ];
            }
        } catch (\Exception $e) {
            // Silently fail
        }
    }

    // Default fallback
    return [
        '1min' => 0,
        '5min' => 0,
        '15min' => 0,
    ];
}

/**
 * Get uptime with cross-platform support
 */
private function getUptime()
{
    // Linux
    if (file_exists('/proc/uptime') && is_readable('/proc/uptime')) {
        $uptime = file_get_contents('/proc/uptime');
        $uptime = explode(' ', $uptime)[0];
        return Carbon::now()->subSeconds((int) $uptime)->diffForHumans();
    }

    // Windows
    if (stripos(PHP_OS, 'WIN') === 0 && function_exists('shell_exec')) {
        $uptime = shell_exec('net stats workstation | find "Statistics since"');
        if ($uptime) {
            return 'Windows (check manually)';
        }
    }

    return 'Unknown';
}

/**
 * Get CPU cores with cross-platform support
 */
private function getCpuCores()
{
    // Linux
    if (is_readable('/proc/cpuinfo')) {
        $cpuinfo = file_get_contents('/proc/cpuinfo');
        preg_match_all('/^processor/m', $cpuinfo, $matches);
        return count($matches[0]);
    }

    // Windows
    if (stripos(PHP_OS, 'WIN') === 0 && function_exists('shell_exec')) {
        $wmic = shell_exec('wmic cpu get NumberOfCores /value');
        if ($wmic && preg_match('/NumberOfCores=(\d+)/', $wmic, $matches)) {
            return (int) $matches[1];
        }
    }

    // Fallback to 1
    return 1;
}

/**
 * Get process count with cross-platform support
 */
private function getProcessCount()
{
    // Linux
    if (is_readable('/proc/stat')) {
        $stat = file_get_contents('/proc/stat');
        preg_match('/processes\s+(\d+)/', $stat, $matches);
        return (int) ($matches[1] ?? 0);
    }

    // Windows
    if (stripos(PHP_OS, 'WIN') === 0 && function_exists('shell_exec')) {
        $tasklist = shell_exec('tasklist /fo csv');
        if ($tasklist) {
            return count(explode("\n", $tasklist)) - 2; // Approximate
        }
    }

    return 0;
}

    private function loadDatabaseInfo()
    {
        try {
            $connection = config('database.default');
            $driver = config("database.connections.{$connection}.driver");
            $database = config("database.connections.{$connection}.database");

            $tables = DB::select('SHOW TABLES');
            $tableCount = count($tables);

            $tableSizes = [];
            $totalSize = 0;

            if ($driver === 'mysql') {
                $result = DB::select("
                    SELECT
                        table_name AS `Table`,
                        ROUND((data_length + index_length) / 1024 / 1024, 2) AS `Size (MB)`,
                        table_rows AS `Rows`
                    FROM information_schema.TABLES
                    WHERE table_schema = ?
                    ORDER BY (data_length + index_length) DESC
                ", [$database]);

                foreach ($result as $table) {
                    $tableSizes[] = [
                        'name' => $table->Table,
                        'size' => $table->{'Size (MB)'},
                        'rows' => $table->Rows,
                    ];
                    $totalSize += $table->{'Size (MB)'};
                }
            }

            $this->databaseInfo = [
                'driver' => $driver,
                'database' => $database,
                'connection' => $connection,
                'table_count' => $tableCount,
                'total_size' => round($totalSize, 2),
                'table_sizes' => $tableSizes,
                'connection_status' => 'Connected',
            ];
        } catch (\Exception $e) {
            $this->databaseInfo = [
                'connection_status' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    private function loadCacheInfo()
    {
        $driver = config('cache.default');
        $stats = [];

        try {
            switch ($driver) {
                case 'redis':
                    $redis = app('redis');
                    $info = $redis->info();
                    $stats = [
                        'used_memory' => round($info['used_memory'] / 1024 / 1024, 2) . ' MB',
                        'total_connections' => $info['total_connections_received'],
                        'connected_clients' => $info['connected_clients'],
                        'keys' => count($redis->keys('*')),
                    ];
                    break;

                case 'file':
                    $cachePath = storage_path('framework/cache/data');
                    $size = $this->getDirectorySize($cachePath);
                    $stats = [
                        'storage' => round($size / 1024 / 1024, 2) . ' MB',
                        'path' => $cachePath,
                    ];
                    break;

                case 'database':
                    $count = DB::table('cache')->count();
                    $stats = [
                        'cache_entries' => $count,
                    ];
                    break;
            }
        } catch (\Exception $e) {
            $stats = ['error' => $e->getMessage()];
        }

        $this->cacheInfo = [
            'driver' => $driver,
            'stats' => $stats,
            'hit_rate' => $this->getCacheHitRate(),
        ];
    }

    private function loadStorageInfo()
    {
        $disks = ['local', 'public', 's3'];
        $storageData = [];

        foreach ($disks as $disk) {
            if (config("filesystems.disks.{$disk}")) {
                try {
                    $total = disk_total_space(storage_path());
                    $free = disk_free_space(storage_path());
                    $used = $total - $free;

                    $storageData[$disk] = [
                        'driver' => config("filesystems.disks.{$disk}.driver"),
                        'total' => round($total / 1024 / 1024 / 1024, 2) . ' GB',
                        'used' => round($used / 1024 / 1024 / 1024, 2) . ' GB',
                        'free' => round($free / 1024 / 1024 / 1024, 2) . ' GB',
                        'percentage' => round(($used / $total) * 100, 2),
                        'root' => config("filesystems.disks.{$disk}.root") ?? 'N/A',
                    ];
                } catch (\Exception $e) {
                    $storageData[$disk] = [
                        'error' => $e->getMessage(),
                    ];
                }
            }
        }

        $this->storageInfo = $storageData;
    }

    private function loadQueueInfo()
    {
        $queueConnection = config('queue.default');

        try {
            switch ($queueConnection) {
                case 'database':
                    $pending = DB::table('jobs')->count();
                    $failed = DB::table('failed_jobs')->count();
                    break;

                case 'redis':
                    $pending = app('redis')->llen('queues:default');
                    $failed = app('redis')->llen('failed');
                    break;

                default:
                    $pending = 0;
                    $failed = 0;
            }

            $this->queueInfo = [
                'driver' => $queueConnection,
                'pending_jobs' => $pending,
                'failed_jobs' => $failed,
                'workers' => $this->getQueueWorkers(),
            ];
        } catch (\Exception $e) {
            $this->queueInfo = [
                'error' => $e->getMessage(),
            ];
        }
    }

    private function loadSecurityInfo()
    {
        $this->securityInfo = [
            'app_key_set' => !empty(config('app.key')),
            'https_enabled' => request()->secure(),
            'session_driver' => config('session.driver'),
            'session_lifetime' => config('session.lifetime') . ' minutes',
            'cors_enabled' => config('cors.paths') ? 'Yes' : 'No',
            'hsts_enabled' => config('hsts.enabled') ? 'Yes' : 'No',
            'csp_enabled' => config('csp.enabled') ? 'Yes' : 'No',
            'xss_protection' => 'Enabled',
            'content_type_options' => 'nosniff',
            'frame_options' => config('session.secure') ? 'DENY' : 'SAMEORIGIN',
        ];
    }

    private function loadPerformanceMetrics()
    {
        $this->performanceMetrics = [
            'response_time' => $this->getResponseTime(),
            'memory_peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status()['opcache_enabled'],
            'realpath_cache_size' => round(realpath_cache_size() / 1024, 2) . ' KB',
            'realpath_cache_entries' => realpath_cache_size() ? realpath_cache_size() / 512 : 0,
            'database_queries' => DB::getQueryLog() ? count(DB::getQueryLog()) : 0,
        ];
    }

    // Helper Methods
    private function getMemoryUsage()
    {
        if (function_exists('memory_get_usage')) {
            $mem = memory_get_usage(true);
            $total = $this->getServerMemoryLimit();

            return [
                'used' => round($mem / 1024 / 1024, 2) . ' MB',
                'total' => $total ? round($total / 1024 / 1024, 2) . ' MB' : 'Unknown',
                'percentage' => $total ? round(($mem / $total) * 100, 2) : 0,
            ];
        }

        return ['error' => 'Memory info not available'];
    }

    private function getDiskUsage()
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;

        return [
            'total' => round($total / 1024 / 1024 / 1024, 2) . ' GB',
            'used' => round($used / 1024 / 1024 / 1024, 2) . ' GB',
            'free' => round($free / 1024 / 1024 / 1024, 2) . ' GB',
            'percentage' => round(($used / $total) * 100, 2),
        ];
    }

    // private function getUptime()
    // {
    //     if (file_exists('/proc/uptime')) {
    //         $uptime = file_get_contents('/proc/uptime');
    //         $uptime = explode(' ', $uptime)[0];
    //         return Carbon::now()->subSeconds($uptime)->diffForHumans();
    //     }

    //     return 'Unknown';
    // }

    // private function getCpuCores()
    // {
    //     if (is_readable('/proc/cpuinfo')) {
    //         $cpuinfo = file_get_contents('/proc/cpuinfo');
    //         preg_match_all('/^processor/m', $cpuinfo, $matches);
    //         return count($matches[0]);
    //     }

    //     return 1;
    // }

    // private function getProcessCount()
    // {
    //     if (is_readable('/proc/stat')) {
    //         $stat = file_get_contents('/proc/stat');
    //         preg_match('/processes\s+(\d+)/', $stat, $matches);
    //         return $matches[1] ?? 0;
    //     }

    //     return 0;
    // }

    private function getDirectorySize($path)
    {
        $size = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    private function getCacheHitRate()
    {
        // This would require custom cache monitoring
        // For now, return a placeholder
        return 'N/A';
    }

    private function getQueueWorkers()
    {
        // Check if queue workers are running
        // This is platform-dependent
        return 'Check manually';
    }

    private function getResponseTime()
    {
        $start = defined('LARAVEL_START') ? LARAVEL_START : microtime(true);
        return round((microtime(true) - $start) * 1000, 2) . ' ms';
    }

    private function getServerMemoryLimit()
    {
        $limit = ini_get('memory_limit');
        if (preg_match('/^(\d+)(.)$/', $limit, $matches)) {
            if ($matches[2] == 'G') {
                return $matches[1] * 1024 * 1024 * 1024;
            } elseif ($matches[2] == 'M') {
                return $matches[1] * 1024 * 1024;
            } elseif ($matches[2] == 'K') {
                return $matches[1] * 1024;
            }
        }
        return 0;
    }

    public function refreshSystemHealth()
    {
        $this->loadSystemHealth();
    }

    public function clearCache($type = 'all')
    {
        try {
            switch ($type) {
                case 'application':
                    Artisan::call('cache:clear');
                    $message = 'Application cache cleared successfully';
                    break;

                case 'config':
                    Artisan::call('config:clear');
                    $message = 'Configuration cache cleared successfully';
                    break;

                case 'route':
                    Artisan::call('route:clear');
                    $message = 'Route cache cleared successfully';
                    break;

                case 'view':
                    Artisan::call('view:clear');
                    $message = 'View cache cleared successfully';
                    break;

                case 'all':
                    Artisan::call('optimize:clear');
                    $message = 'All caches cleared successfully';
                    break;

                default:
                    $message = 'Invalid cache type';
            }

            $this->dispatchBrowserEvent('notify', [
                'type' => 'success',
                'message' => $message
            ]);

            $this->loadSystemHealth();
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Error clearing cache: ' . $e->getMessage()
            ]);
        }
    }

    public function runOptimization()
    {
        try {
            Artisan::call('optimize');

            $this->dispatchBrowserEvent('notify', [
                'type' => 'success',
                'message' => 'Application optimized successfully'
            ]);

            $this->loadSystemHealth();
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Error optimizing application: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.system-health.index');
    }
}
