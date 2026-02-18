<div>
    {{-- The Master doesn't talk, he acts. --}}
    <div class="content">
        <div class="container-fluid">
            <!-- Filters Card -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter mr-1"></i>
                        Filters
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
                                <label>Log Name</label>
                                <select wire:model.live="log_name" class="form-control">
                                    <option value="">All Logs</option>
                                    @foreach ($logNames as $name)
                                        <option value="{{ $name }}">
                                            {{ ucfirst($name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Event Type</label>
                                <select wire:model.live="event" class="form-control">
                                    <option value="">All Events</option>
                                    @foreach ($events as $event)
                                        <option value="{{ $event }}">
                                            {{ ucfirst(str_replace('_', ' ', $event)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>User</label>
                                <select wire:model.live="user_id" class="form-control">
                                    <option value="">All Users</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->full_name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" wire:model.live="date_from" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" wire:model.live="date_to" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                    placeholder="Search description...">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-group w-100">
                                <button wire:click="resetFilters" class="btn btn-secondary w-100">
                                    <i class="fas fa-redo mr-1"></i> Reset Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($totalLogs) }}</h3>
                            <p>Total Logs</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-history"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($todayLogs) }}</h3>
                            <p>Today's Logs</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format(\App\Models\User::count()) }}</h3>
                            <p>Total Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($logs->count()) }}</h3>
                            <p>Current Page</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Logs Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activity Logs</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#" wire:click="$set('showClearModal', true)">
                                    <i class="fas fa-trash mr-2"></i> Clear Old Logs
                                </a>
                                <a class="dropdown-item" href="#" wire:click="exportLogs">
                                    <i class="fas fa-download mr-2"></i> Export as CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Description</th>
                                    <th>Event</th>
                                    <th>User</th>
                                    <th>IP Address</th>
                                    <th>Date</th>
                                    <th>Time Ago</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas {{ $log->icon }} mr-2"></i>
                                                {{ Str::limit($log->description, 50) }}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($log->event)
                                                <span
                                                    class="badge badge-{{ match ($log->event) {
                                                        'created' => 'success',
                                                        'updated' => 'warning',
                                                        'deleted' => 'danger',
                                                        default => 'info',
                                                    } }}">
                                                    {{ ucfirst($log->event) }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->causer)
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $initials = getInitials($log->causer->full_name);
                                                        $colors = [
                                                            'primary',
                                                            'success',
                                                            'info',
                                                            'warning',
                                                            'danger',
                                                            'secondary',
                                                        ];
                                                        $color = $colors[crc32($log->causer->email) % count($colors)];
                                                    @endphp
                                                    <div class="avatar-initials bg-{{ $color }} img-circle mr-2"
                                                        style="width: 30px; height: 30px; line-height: 30px; text-align: center; color: white; font-weight: bold; font-size: 12px;">
                                                        {{ $initials }}
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-weight-bold">
                                                            {{ $log->causer->full_name }}</div>
                                                        <div class="text-xs text-muted">
                                                            {{ $log->causer->email }}</div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light">{{ $log->ip_address ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <div class="text-sm">{{ formatDate($log->created_at) }}</div>
                                        </td>
                                        <td>
                                            <div class="text-xs text-muted">{{ $log->time_ago }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                            <h5>No activity logs found</h5>
                                            <p class="text-muted">Start using the system to generate activity
                                                logs</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        {{ $logs->links() }}
                    </div>
                    <div class="float-left">
                        Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }}
                        entries
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Logs Modal -->
    <div class="modal fade" id="clearLogsModal" tabindex="-1" role="dialog" aria-labelledby="clearLogsModalLabel"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clearLogsModalLabel">Clear Old Activity Logs</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>This will permanently delete activity logs older than the specified number of days.</p>
                    <div class="form-group">
                        <label for="days_to_clear">Delete logs older than (days):</label>
                        <input type="number" class="form-control" id="days_to_clear" wire:model="days_to_clear"
                            min="1" max="365" value="30" required>
                        <small class="form-text text-muted">Enter a number between 1 and 365 days</small>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        This action cannot be undone. Make sure you have backups if needed.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="clearOldLogs"
                        data-dismiss="modal">Clear Logs</button>
                </div>
            </div>
        </div>
    </div>

    @push('sscripts')
        <script>
            // Show modal when showClearModal is true
            Livewire.on('showClearModal', (show) => {
                if (show) {
                    $('#clearLogsModal').modal('show');
                } else {
                    $('#clearLogsModal').modal('hide');
                }
            });

            // Listen for notify events
            Livewire.on('notify', (event) => {
                toastr[event.type](event.message);
            });

            // Initialize modal when Livewire loads
            document.addEventListener('livewire:initialized', () => {
                // Show modal if showClearModal is true
                if (@this.showClearModal) {
                    $('#clearLogsModal').modal('show');
                }

                // Listen for modal close
                $('#clearLogsModal').on('hidden.bs.modal', function() {
                    @this.set('showClearModal', false);
                });
            });
        </script>
    @endpush
</div>
