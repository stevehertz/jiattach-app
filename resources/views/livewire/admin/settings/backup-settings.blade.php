<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-2 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $stats['total_backups'] }}</h3>
                                        <p>Total Backups</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-database"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $stats['successful_backups'] }}</h3>
                                        <p>Successful</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ $stats['failed_backups'] }}</h3>
                                        <p>Failed</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $stats['total_size'] ? number_format($stats['total_size'] / 1048576, 2) : 0 }}
                                            MB</h3>
                                        <p>Total Size</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-hdd"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3>{{ $stats['backups_this_week'] }}</h3>
                                        <p>This Week</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-calendar-week"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($stats['last_backup'])
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        <strong>Last Backup:</strong>
                                        {{ $stats['last_backup']->created_at->format('F j, Y, g:i a') }}
                                        ({{ $stats['last_backup']->created_at->diffForHumans() }})
                                        @if ($stats['last_backup']->file_size)
                                            - Size: {{ $stats['last_backup']->formatted_size }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="row">
            <div class="col-12">
                <div class="card card-success card-outline">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="backup-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'backups' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'backups')" href="#backups" role="tab">
                                    <i class="fas fa-history mr-2"></i>Backup History
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'schedule' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'schedule')" href="#schedule" role="tab">
                                    <i class="fas fa-clock mr-2"></i>Schedule Settings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'destination' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'destination')" href="#destination" role="tab">
                                    <i class="fas fa-cloud-upload-alt mr-2"></i>Destination
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'notifications' ? 'active' : '' }}"
                                    wire:click="$set('activeTab', 'notifications')" href="#notifications"
                                    role="tab">
                                    <i class="fas fa-bell mr-2"></i>Notifications
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="row">
            <div class="col-12">
                <!-- Backup History Tab -->
                @if ($activeTab === 'backups')
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-2"></i>
                                Backup History
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-success mr-2" wire:click="createBackup"
                                    wire:loading.attr="disabled" {{ $backupInProgress ? 'disabled' : '' }}>
                                    @if ($backupInProgress)
                                        <i class="fas fa-spinner fa-spin mr-1"></i> Creating...
                                    @else
                                        <i class="fas fa-plus-circle mr-1"></i> Create New Backup
                                    @endif
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" wire:click="cleanOldBackups"
                                    wire:loading.attr="disabled">
                                    <i class="fas fa-broom mr-1"></i> Clean Old Backups
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($backupInProgress)
                                <div class="progress mb-3">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                        role="progressbar" style="width: {{ $backupProgress }}%"></div>
                                </div>
                                <p class="text-center">{{ $backupStatus }}</p>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" placeholder="Search backups..."
                                        wire:model.live.debounce.300ms="search">
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" wire:model.live="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="completed">Completed</option>
                                        <option value="failed">Failed</option>
                                        <option value="in_progress">In Progress</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" class="form-control" wire:model.live="dateFrom"
                                        placeholder="From">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" class="form-control" wire:model.live="dateTo"
                                        placeholder="To">
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Size</th>
                                            <th>Disk</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Date</th>
                                            <th width="200">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($backups as $backup)
                                            <tr>
                                                <td>
                                                    <i class="fas fa-archive mr-2 text-muted"></i>
                                                    {{ $backup->file_name }}
                                                </td>
                                                <td>{{ $backup->formatted_size }}</td>
                                                <td>{{ ucfirst($backup->disk) }}</td>
                                                <td>
                                                    <span class="badge {{ $backup->status_badge }}">
                                                        {{ ucfirst($backup->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $backup->creator?->name ?? 'System' }}</td>
                                                <td>{{ $backup->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        @if ($backup->status === 'completed')
                                                            <button type="button" class="btn btn-sm btn-info"
                                                                wire:click="downloadBackup({{ $backup->id }})"
                                                                title="Download">
                                                                <i class="fas fa-download"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-warning"
                                                                wire:click="confirmRestore({{ $backup->id }})"
                                                                title="Restore">
                                                                <i class="fas fa-undo-alt"></i>
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            wire:click="confirmDelete({{ $backup->id }})"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @if ($backup->error_message)
                                                <tr class="bg-light">
                                                    <td colspan="7" class="text-danger">
                                                        <small><i class="fas fa-exclamation-triangle mr-2"></i>Error:
                                                            {{ $backup->error_message }}</small>
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">No backups found</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $backups->links() }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Schedule Settings Tab -->
                @if ($activeTab === 'schedule')
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clock mr-2"></i>
                                Backup Schedule Settings
                            </h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveScheduleSettings">
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="backupEnabled"
                                        wire:model="backupEnabled">
                                    <label class="custom-control-label" for="backupEnabled">
                                        <strong>Enable Automatic Backups</strong>
                                    </label>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Backup Frequency</label>
                                            <select class="form-control" wire:model="backupFrequency">
                                                @foreach ($frequencies as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Backup Time</label>
                                            <input type="time"
                                                class="form-control @error('backupTime') is-invalid @enderror"
                                                wire:model="backupTime">
                                            @error('backupTime')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                @if ($backupFrequency === 'weekly')
                                    <div class="form-group">
                                        <label>Backup Day</label>
                                        <select class="form-control" wire:model="backupDay">
                                            @foreach ($days as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                @if ($backupFrequency === 'monthly')
                                    <div class="form-group">
                                        <label>Backup Day of Month</label>
                                        <select class="form-control" wire:model="backupDate">
                                            @for ($i = 1; $i <= 31; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Retention Period (Days)</label>
                                            <input type="number"
                                                class="form-control @error('backupRetentionDays') is-invalid @enderror"
                                                wire:model="backupRetentionDays" min="1" max="365">
                                            @error('backupRetentionDays')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="text-muted">How many days to keep backups</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Maximum Backup Files</label>
                                            <input type="number"
                                                class="form-control @error('backupMaxFiles') is-invalid @enderror"
                                                wire:model="backupMaxFiles" min="1" max="100">
                                            @error('backupMaxFiles')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="text-muted">Maximum number of backups to keep</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                        <i class="fas fa-save mr-2"></i> Save Schedule Settings
                                    </button>
                                </div>
                            </form>

                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Current Schedule:</strong>
                                @if ($backupEnabled)
                                    Automatic backups are scheduled
                                    @if ($backupFrequency === 'daily')
                                        daily at {{ $backupTime }}
                                    @elseif($backupFrequency === 'weekly')
                                        every {{ $days[$backupDay] ?? 'Monday' }} at {{ $backupTime }}
                                    @else
                                        on day {{ $backupDate }} of each month at {{ $backupTime }}
                                    @endif
                                @else
                                    Automatic backups are disabled
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Destination Settings Tab -->
                @if ($activeTab === 'destination')
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>
                                Backup Destination Settings
                            </h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveDestinationSettings">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Storage Disk</label>
                                            <select class="form-control" wire:model="backupDisk">
                                                @foreach ($availableDisks as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Where to store the backup files</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Backup Path</label>
                                            <input type="text"
                                                class="form-control @error('backupPath') is-invalid @enderror"
                                                wire:model="backupPath" placeholder="backups">
                                            @error('backupPath')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="text-muted">Directory path within the selected disk</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="backupCompress"
                                        wire:model="backupCompress">
                                    <label class="custom-control-label" for="backupCompress">
                                        <strong>Compress Backup Files</strong>
                                    </label>
                                    <p class="text-muted">Compress backup files to save storage space</p>
                                </div>

                                @if ($backupDisk === 's3')
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Note:</strong> Make sure your S3 credentials are configured in the .env
                                        file.
                                    </div>
                                @endif

                                @if ($backupDisk === 'ftp')
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Note:</strong> Make sure your FTP credentials are configured in the .env
                                        file.
                                    </div>
                                @endif

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                        <i class="fas fa-save mr-2"></i> Save Destination Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Notifications Tab -->
                @if ($activeTab === 'notifications')
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bell mr-2"></i>
                                Backup Notification Settings
                            </h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveNotificationSettings">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="custom-control custom-switch mb-3">
                                            <input type="checkbox" class="custom-control-input" id="notifyOnSuccess"
                                                wire:model="notifyOnSuccess">
                                            <label class="custom-control-label" for="notifyOnSuccess">
                                                <strong>Notify on Success</strong>
                                            </label>
                                            <p class="text-muted">Send notification when backup completes successfully
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-switch mb-3">
                                            <input type="checkbox" class="custom-control-input" id="notifyOnFailure"
                                                wire:model="notifyOnFailure">
                                            <label class="custom-control-label" for="notifyOnFailure">
                                                <strong>Notify on Failure</strong>
                                            </label>
                                            <p class="text-muted">Send notification when backup fails</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Notification Email</label>
                                    <input type="email"
                                        class="form-control @error('notificationEmails') is-invalid @enderror"
                                        wire:model="notificationEmails" placeholder="admin@example.com">
                                    @error('notificationEmails')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">Email address to receive backup notifications</small>
                                </div>

                                <div class="form-group">
                                    <label>Notification Channels</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="channelMail"
                                                    value="mail" wire:model="notificationChannels">
                                                <label class="custom-control-label" for="channelMail">
                                                    Email
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="channelDatabase" value="database"
                                                    wire:model="notificationChannels">
                                                <label class="custom-control-label" for="channelDatabase">
                                                    Database (In-app)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="channelSlack"
                                                    value="slack" wire:model="notificationChannels">
                                                <label class="custom-control-label" for="channelSlack">
                                                    Slack
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                        <i class="fas fa-save mr-2"></i> Save Notification Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Restore Confirmation Modal -->
        @if ($confirmingRestore)
            <div class="modal fade show" id="restoreModal" tabindex="-1"
                style="display: block; background: rgba(0,0,0,0.5);" wire:ignore.self>
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Confirm Restore
                            </h5>
                            <button type="button" class="close" wire:click="$set('confirmingRestore', false)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Warning:</strong> Restoring a backup will overwrite your current database. This
                                action cannot be undone.</p>
                            <p>Are you sure you want to continue?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                wire:click="$set('confirmingRestore', false)">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-warning" wire:click="restoreBackup"
                                wire:loading.attr="disabled">
                                <i class="fas fa-undo-alt mr-2"></i> Restore Backup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Confirmation Modal -->
        <div wire:ignore.self id="confirmActionModal" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Confirm Action
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="confirm-action-message">
                        <!-- Message will be injected via JavaScript -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-warning" id="confirm-action-button">
                            <i class="fas fa-check mr-2"></i> Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', function() {
                // Handle confirm action
                Livewire.on('confirm-action', (data) => {
                    $('#confirm-action-message').html(data.message);
                    $('#confirm-action-button').attr('data-event', data.event);
                    if (data.data) {
                        $('#confirm-action-button').attr('data-params', JSON.stringify(data.data));
                    }
                    $('#confirmActionModal').modal('show');
                });

                $('#confirm-action-button').on('click', function() {
                    let event = $(this).attr('data-event');
                    let params = $(this).attr('data-params');
                    if (params) {
                        Livewire.dispatch(event, JSON.parse(params));
                    } else {
                        Livewire.dispatch(event);
                    }
                    $('#confirmActionModal').modal('hide');
                });

                // Handle notifications
                Livewire.on('notify', (data) => {
                    toastr[data.type](data.message);
                });

                // Clean up modal on close
                $('#restoreModal').on('hidden.bs.modal', function() {
                    Livewire.dispatch('$set', {
                        confirmingRestore: false
                    });
                });
            });
        </script>
    @endpush

</div>
