<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <div class="content">
        <div class="container-fluid">
            <!-- Control Panel -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs mr-1"></i>
                        Control Panel
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Auto Refresh (seconds)</label>
                                <select wire:model="refreshInterval" class="form-control">
                                    <option value="30">30 seconds</option>
                                    <option value="60">1 minute</option>
                                    <option value="300">5 minutes</option>
                                    <option value="600">10 minutes</option>
                                    <option value="0">Disabled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button wire:click="refreshSystemHealth" class="btn btn-primary">
                                    <i class="fas fa-sync-alt mr-1"></i> Refresh Now
                                </button>
                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-broom mr-1"></i> Clear Cache
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" wire:click="clearCache('all')">
                                        <i class="fas fa-trash-alt mr-2"></i> All Caches
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" wire:click="clearCache('application')">
                                        <i class="fas fa-cube mr-2"></i> Application Cache
                                    </a>
                                    <a class="dropdown-item" href="#" wire:click="clearCache('config')">
                                        <i class="fas fa-cog mr-2"></i> Config Cache
                                    </a>
                                    <a class="dropdown-item" href="#" wire:click="clearCache('route')">
                                        <i class="fas fa-route mr-2"></i> Route Cache
                                    </a>
                                    <a class="dropdown-item" href="#" wire:click="clearCache('view')">
                                        <i class="fas fa-eye mr-2"></i> View Cache
                                    </a>
                                </div>
                                <button wire:click="runOptimization" class="btn btn-warning">
                                    <i class="fas fa-rocket mr-1"></i> Optimize
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 text-muted">
                        <small>
                            <i class="fas fa-clock mr-1"></i>
                            Last refreshed: {{ $lastRefresh ? $lastRefresh->format('Y-m-d H:i:s') : 'Never' }}
                            @if ($refreshInterval > 0)
                                | Auto refresh every {{ $refreshInterval }} seconds
                            @endif
                        </small>
                    </div>
                </div>
            </div>

            <!-- System Status Overview -->
            <div class="row mb-3">
                <!-- Laravel Status -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $systemInfo['laravel_version'] ?? 'N/A' }}</h3>
                            <p>Laravel Version</p>
                        </div>
                        <div class="icon">
                            <i class="fab fa-laravel"></i>
                        </div>
                    </div>
                </div>

                <!-- PHP Status -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $systemInfo['php_version'] ?? 'N/A' }}</h3>
                            <p>PHP Version</p>
                        </div>
                        <div class="icon">
                            <i class="fab fa-php"></i>
                        </div>
                    </div>
                </div>

                <!-- Database Status -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $databaseInfo['table_count'] ?? '0' }}</h3>
                            <p>Database Tables</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                </div>

                <!-- Memory Status -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $serverInfo['memory']['percentage'] ?? '0' }}%</h3>
                            <p>Memory Usage</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-memory"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Information Cards -->
            <div class="row">
                <!-- System Information -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-1"></i>
                                System Information
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach ($systemInfo as $key => $value)
                                        <tr>
                                            <td style="width: 40%">
                                                <strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong>
                                            </td>
                                            <td>
                                                @if (is_bool($value))
                                                    <span class="badge badge-{{ $value ? 'success' : 'danger' }}">
                                                        {{ $value ? 'Yes' : 'No' }}
                                                    </span>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Server Information -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-server mr-1"></i>
                                Server Information
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach ($serverInfo as $key => $value)
                                        <tr>
                                            <td style="width: 40%">
                                                <strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong>
                                            </td>
                                            <td>
                                                @if (is_array($value))
                                                    @if ($key === 'load_average')
                                                        @foreach ($value as $loadKey => $loadValue)
                                                            <span
                                                                class="badge badge-{{ $loadValue > 1 ? 'danger' : ($loadValue > 0.7 ? 'warning' : 'success') }}">
                                                                {{ $loadKey }}:
                                                                {{ number_format($loadValue, 2) }}
                                                            </span>
                                                        @endforeach
                                                    @elseif($key === 'memory' || $key === 'disk')
                                                        <div>
                                                            <span
                                                                class="badge badge-{{ $value['percentage'] > 90 ? 'danger' : ($value['percentage'] > 70 ? 'warning' : 'success') }}">
                                                                {{ $value['percentage'] }}%
                                                            </span>
                                                            <small class="text-muted ml-2">
                                                                {{ $value['used'] }} of {{ $value['total'] }}
                                                            </small>
                                                        </div>
                                                    @else
                                                        {{ json_encode($value) }}
                                                    @endif
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row -->
            <div class="row">
                <!-- Database Information -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-database mr-1"></i>
                                Database Information
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach ($databaseInfo as $key => $value)
                                        @if (!is_array($value) && $key !== 'table_sizes')
                                            <tr>
                                                <td style="width: 40%">
                                                    <strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong>
                                                </td>
                                                <td>
                                                    @if (str_contains(strtolower($value), 'error'))
                                                        <span class="badge badge-danger">{{ $value }}</span>
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>

                            @if (!empty($databaseInfo['table_sizes']) && count($databaseInfo['table_sizes']) > 0)
                                <div class="p-3 border-top">
                                    <h6>Table Sizes (Top 10):</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Table</th>
                                                    <th>Size (MB)</th>
                                                    <th>Rows</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (array_slice($databaseInfo['table_sizes'], 0, 10) as $table)
                                                    <tr>
                                                        <td>{{ $table['name'] }}</td>
                                                        <td>{{ number_format($table['size'], 2) }}</td>
                                                        <td>{{ number_format($table['rows']) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Cache Information -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt mr-1"></i>
                                Cache Information
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach ($cacheInfo as $key => $value)
                                        <tr>
                                            <td style="width: 40%">
                                                <strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong>
                                            </td>
                                            <td>
                                                @if (is_array($value))
                                                    @foreach ($value as $subKey => $subValue)
                                                        <div>
                                                            <strong>{{ ucwords(str_replace('_', ' ', $subKey)) }}:</strong>
                                                            {{ $subValue }}
                                                        </div>
                                                    @endforeach
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Third Row -->
            <div class="row">
                <!-- Storage Information -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-hdd mr-1"></i>
                                Storage Information
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach ($storageInfo as $disk => $info)
                                        <tr>
                                            <td style="width: 40%">
                                                <strong>{{ strtoupper($disk) }} Disk</strong>
                                                <small
                                                    class="d-block text-muted">{{ $info['driver'] ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                @if (isset($info['error']))
                                                    <span class="badge badge-danger">Error:
                                                        {{ $info['error'] }}</span>
                                                @else
                                                    <div class="mb-1">
                                                        <span
                                                            class="badge badge-{{ $info['percentage'] > 90 ? 'danger' : ($info['percentage'] > 70 ? 'warning' : 'success') }}">
                                                            {{ $info['percentage'] }}% used
                                                        </span>
                                                    </div>
                                                    <div class="progress progress-sm mb-1">
                                                        <div class="progress-bar bg-{{ $info['percentage'] > 90 ? 'danger' : ($info['percentage'] > 70 ? 'warning' : 'success') }}"
                                                            style="width: {{ $info['percentage'] }}%"></div>
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $info['used'] }} of {{ $info['total'] }}
                                                        ({{ $info['free'] }} free)
                                                    </small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tachometer-alt mr-1"></i>
                                Performance Metrics
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach ($performanceMetrics as $key => $value)
                                        <tr>
                                            <td style="width: 40%">
                                                <strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong>
                                            </td>
                                            <td>
                                                @if (is_bool($value))
                                                    <span class="badge badge-{{ $value ? 'success' : 'danger' }}">
                                                        {{ $value ? 'Enabled' : 'Disabled' }}
                                                    </span>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fourth Row -->
            <div class="row">
                <!-- Queue Information -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tasks mr-1"></i>
                                Queue Information
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach ($queueInfo as $key => $value)
                                        <tr>
                                            <td style="width: 40%">
                                                <strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong>
                                            </td>
                                            <td>
                                                @if (str_contains(strtolower($value), 'error'))
                                                    <span class="badge badge-danger">{{ $value }}</span>
                                                @elseif($key === 'pending_jobs' || $key === 'failed_jobs')
                                                    <span
                                                        class="badge badge-{{ $value > 0 ? 'warning' : 'success' }}">
                                                        {{ $value }}
                                                    </span>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Security Information -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Security Information
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach ($securityInfo as $key => $value)
                                        <tr>
                                            <td style="width: 40%">
                                                <strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong>
                                            </td>
                                            <td>
                                                @if (is_bool($value))
                                                    <span class="badge badge-{{ $value ? 'success' : 'danger' }}">
                                                        {{ $value ? 'Yes' : 'No' }}
                                                    </span>
                                                @elseif(in_array(strtolower($value), ['yes', 'enabled', 'true']))
                                                    <span class="badge badge-success">{{ $value }}</span>
                                                @elseif(in_array(strtolower($value), ['no', 'disabled', 'false']))
                                                    <span class="badge badge-danger">{{ $value }}</span>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health Status Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-heartbeat mr-1"></i>
                        System Health Status
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span
                                    class="info-box-icon bg-{{ $systemInfo['debug_mode'] === 'Disabled' ? 'success' : 'danger' }}">
                                    <i class="fas fa-bug"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Debug Mode</span>
                                    <span class="info-box-number">{{ $systemInfo['debug_mode'] ?? 'Unknown' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                @php
                                    $memoryStatus = $serverInfo['memory']['percentage'] ?? 0;
                                    $memoryColor =
                                        $memoryStatus > 90 ? 'danger' : ($memoryStatus > 70 ? 'warning' : 'success');
                                @endphp
                                <span class="info-box-icon bg-{{ $memoryColor }}">
                                    <i class="fas fa-memory"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Memory Usage</span>
                                    <span class="info-box-number">{{ $memoryStatus }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                @php
                                    $diskStatus = $serverInfo['disk']['percentage'] ?? 0;
                                    $diskColor =
                                        $diskStatus > 90 ? 'danger' : ($diskStatus > 70 ? 'warning' : 'success');
                                @endphp
                                <span class="info-box-icon bg-{{ $diskColor }}">
                                    <i class="fas fa-hdd"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Disk Usage</span>
                                    <span class="info-box-number">{{ $diskStatus }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span
                                    class="info-box-icon bg-{{ $performanceMetrics['opcache_enabled'] ?? false ? 'success' : 'warning' }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">OPCache</span>
                                    <span class="info-box-number">
                                        {{ $performanceMetrics['opcache_enabled'] ?? false ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            // Auto-refresh functionality
            let refreshInterval = @js($refreshInterval);
            let refreshTimer = null;

            function setupAutoRefresh() {
                if (refreshTimer) {
                    clearInterval(refreshTimer);
                }

                if (refreshInterval > 0) {
                    refreshTimer = setInterval(() => {
                        @this.refreshSystemHealth();
                    }, refreshInterval * 1000);
                }
            }

            // Update interval when component updates
            Livewire.on('refreshSystemHealth', () => {
                setupAutoRefresh();
            });

            // Initialize on component load
            document.addEventListener('livewire:initialized', () => {
                setupAutoRefresh();
            });

            // Cleanup on component unmount
            document.addEventListener('livewire:navigate', () => {
                if (refreshTimer) {
                    clearInterval(refreshTimer);
                }
            });

            // Listen for notify events
            Livewire.on('notify', (event) => {
                toastr[event.type](event.message);
            });
        </script>
    @endpush
</div>
