<?php

namespace App\Livewire\Admin\Settings;

use App\Models\BackupHistory;
use App\Models\BackupSetting;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class BackupSettings extends Component
{
    use WithPagination, WithFileUploads, LogsActivity;

    // Active tab
    public $activeTab = 'backups';

    // Settings
    public $settings = [];
    public $originalSettings = [];

    // Backup Management
    public $backupName = '';
    public $backupInProgress = false;
    public $backupProgress = 0;
    public $backupStatus = '';

    // Schedule Settings
    public $backupEnabled = true;
    public $backupFrequency = 'daily'; // daily, weekly, monthly
    public $backupTime = '02:00';
    public $backupDay = 'monday'; // for weekly
    public $backupDate = '1'; // for monthly (day of month)
    public $backupRetentionDays = 30;
    public $backupMaxFiles = 10;

    // Destination Settings
    public $backupDisk = 'local';
    public $backupPath = 'backups';
    public $backupCompress = true;
    public $backupIncludeTables = [];
    public $backupExcludeTables = [];

    // Notification Settings
    public $notifyOnSuccess = true;
    public $notifyOnFailure = true;
    public $notificationEmails = '';
    public $notificationChannels = ['mail', 'database'];

    // Filters
    public $search = '';
    public $statusFilter = '';
    public $dateFrom = '';
    public $dateTo = '';

    // Confirmation
    public $confirmingRestore = false;
    public $restoreBackupId = null;

    protected $listeners = [
        'refresh' => '$refresh',
        'confirmDeleteBackup' => 'deleteBackup',
        'confirmRestoreBackup' => 'restoreBackup'
    ];

    protected $rules = [
        'backupFrequency' => 'required|in:daily,weekly,monthly',
        'backupTime' => 'required|date_format:H:i',
        'backupDay' => 'required_if:backupFrequency,weekly|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        'backupDate' => 'required_if:backupFrequency,monthly|integer|min:1|max:31',
        'backupRetentionDays' => 'required|integer|min:1|max:365',
        'backupMaxFiles' => 'required|integer|min:1|max:100',
        'backupDisk' => 'required|in:local,s3,ftp',
        'backupPath' => 'required|string',
        'notificationEmails' => 'nullable|email|max:255',
    ];

    public function mount()
    {
        $this->loadSettings();
        $this->loadBackupConfig();
        $this->originalSettings = $this->settings;
    }

    /**
     * Load backup settings from database
     */
    public function loadSettings()
    {
        $dbSettings = BackupSetting::orderBy('sort_order')->get();

        foreach ($dbSettings as $setting) {
            $this->settings[$setting->key] = [
                'id' => $setting->id,
                'value' => $setting->getTypedValue(),
                'type' => $setting->type,
                'name' => $setting->name,
                'description' => $setting->description,
                'options' => $setting->options,
                'category' => $setting->category
            ];
        }
    }

    /**
     * Load backup configuration
     */
    public function loadBackupConfig()
    {
        // Load tables for include/exclude
        $this->backupIncludeTables = config('backup.backup.source.tables.include', []);
        $this->backupExcludeTables = config('backup.backup.source.tables.exclude', []);

        // Load other settings
        $this->backupDisk = config('backup.backup.destination.disks')[0] ?? 'local';
        $this->backupPath = config('backup.backup.destination.filename_prefix', 'backups');
    }

    /**
     * Save backup schedule settings
     */
    public function saveScheduleSettings()
    {
        $this->validate([
            'backupFrequency' => 'required|in:daily,weekly,monthly',
            'backupTime' => 'required|date_format:H:i',
            'backupDay' => 'required_if:backupFrequency,weekly',
            'backupDate' => 'required_if:backupFrequency,monthly|integer|min:1|max:31',
            'backupRetentionDays' => 'required|integer|min:1|max:365',
            'backupMaxFiles' => 'required|integer|min:1|max:100',
        ]);

        // Update backup schedule in config or database
        $settings = [
            'backup.enabled' => $this->backupEnabled,
            'backup.frequency' => $this->backupFrequency,
            'backup.time' => $this->backupTime,
            'backup.day' => $this->backupDay,
            'backup.date' => $this->backupDate,
            'backup.retention_days' => $this->backupRetentionDays,
            'backup.max_files' => $this->backupMaxFiles,
        ];

        $this->saveSettings($settings, 'backup schedule');

        // Update cron job if needed
        $this->updateCronSchedule();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Backup schedule settings saved successfully.'
        ]);
    }

    /**
     * Save destination settings
     */
    public function saveDestinationSettings()
    {
        $this->validate([
            'backupDisk' => 'required|in:local,s3,ftp',
            'backupPath' => 'required|string',
            'backupCompress' => 'boolean',
        ]);

        $settings = [
            'backup.disk' => $this->backupDisk,
            'backup.path' => $this->backupPath,
            'backup.compress' => $this->backupCompress,
        ];

        $this->saveSettings($settings, 'backup destination');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Backup destination settings saved successfully.'
        ]);
    }

    /**
     * Save notification settings
     */
    public function saveNotificationSettings()
    {
        $this->validate([
            'notificationEmails' => 'nullable|email',
        ]);

        $settings = [
            'backup.notify_success' => $this->notifyOnSuccess,
            'backup.notify_failure' => $this->notifyOnFailure,
            'backup.notification_emails' => $this->notificationEmails,
        ];

        $this->saveSettings($settings, 'backup notifications');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Backup notification settings saved successfully.'
        ]);
    }

    /**
     * Save settings to database
     */
    private function saveSettings($settings, $logDescription)
    {
        $changed = [];

        foreach ($settings as $key => $value) {
            $setting = BackupSetting::where('key', $key)->first();

            if ($setting) {
                $oldValue = $setting->getTypedValue();
                $setting->update(['value' => ['value' => $value]]);

                if ($oldValue != $value) {
                    $changed[$key] = ['old' => $oldValue, 'new' => $value];
                }
            }
        }

        $this->loadSettings();

        if (!empty($changed)) {
            $this->logActivity(
                "Updated {$logDescription} settings",
                'backup_settings_updated',
                ['changes' => $changed],
                'backup'
            );
        }
    }

    /**
     * Update cron schedule based on settings
     */
    private function updateCronSchedule()
    {
        $dayMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 0
        ];

        $schedule = match ($this->backupFrequency) {
            'daily' => "0 {$this->backupTime} * * *",
            'weekly' => "0 {$this->backupTime} * * " . ($dayMap[$this->backupDay] ?? 1),
            'monthly' => "0 {$this->backupTime} {$this->backupDate} * *",
            default => "0 {$this->backupTime} * * *",
        };

        Cache::forever('backup_schedule', [
            'expression' => $schedule,
            'frequency' => $this->backupFrequency,
            'time' => $this->backupTime,
            'day' => $this->backupDay,
            'date' => $this->backupDate,
        ]);
    }


    /**
     * Create a new backup
     */
    public function createBackup()
    {
        $this->backupInProgress = true;
        $this->backupProgress = 0;
        $this->backupStatus = 'Starting backup process...';

        try {
            // Create backup history record
            $backup = BackupHistory::create([
                'file_name' => 'backup-' . now()->format('Y-m-d-H-i-s') . '.zip',
                'file_path' => $this->backupPath,
                'status' => 'in_progress',
                'created_by' => auth()->id(),
                'metadata' => [
                    'disk' => $this->backupDisk,
                    'compress' => $this->backupCompress,
                    'tables' => $this->backupIncludeTables,
                ]
            ]);

            $this->backupStatus = 'Running database backup...';
            $this->backupProgress = 25;

            // Run the backup command
            $exitCode = Artisan::call('backup:run', [
                '--only-db' => true,
                '--disable-notifications' => !$this->notifyOnSuccess,
            ]);

            $output = Artisan::output();

            if ($exitCode === 0) {
                $this->backupProgress = 100;
                $this->backupStatus = 'Backup completed successfully!';

                // Update backup history
                $backup->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'file_size' => $this->getLatestBackupSize(),
                ]);

                $this->logActivity(
                    'Created database backup',
                    'backup_created',
                    ['backup_id' => $backup->id, 'file_name' => $backup->file_name],
                    'backup'
                );

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Backup created successfully!'
                ]);
            } else {
                throw new \Exception($output);
            }
        } catch (\Exception $e) {
            $this->backupStatus = 'Backup failed: ' . $e->getMessage();

            if (isset($backup)) {
                $backup->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            $this->logActivity(
                'Backup failed',
                'backup_failed',
                ['error' => $e->getMessage()],
                'backup'
            );

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Backup failed: ' . $e->getMessage()
            ]);
        } finally {
            $this->backupInProgress = false;
        }
    }

    /**
     * Download a backup file
     */
    public function downloadBackup($backupId)
    {
        $backup = BackupHistory::findOrFail($backupId);

        if (!Storage::disk($backup->disk)->exists($backup->file_path . '/' . $backup->file_name)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Backup file not found.'
            ]);
            return;
        }

        $this->logActivity(
            "Downloaded backup: {$backup->file_name}",
            'backup_downloaded',
            ['backup_id' => $backup->id, 'file_name' => $backup->file_name],
            'backup'
        );

        return response()->download(
            Storage::disk($backup->disk)->path($backup->file_path . '/' . $backup->file_name)
        );
    }

    /**
     * Confirm restore backup
     */
    public function confirmRestore($backupId)
    {
        $this->restoreBackupId = $backupId;
        $this->confirmingRestore = true;
    }

    /**
     * Restore from backup
     */
    public function restoreBackup()
    {
        $backup = BackupHistory::find($this->restoreBackupId);

        if (!$backup) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Backup not found.'
            ]);
            $this->confirmingRestore = false;
            return;
        }

        $this->backupInProgress = true;
        $this->backupStatus = 'Starting restore process...';

        try {
            $filePath = Storage::disk($backup->disk)->path($backup->file_path . '/' . $backup->file_name);

            if (!file_exists($filePath)) {
                throw new \Exception('Backup file not found on disk.');
            }

            $this->backupStatus = 'Restoring database...';

            // Run the restore command (you'll need to implement this based on your backup format)
            // This is a simplified example - actual restore will depend on your backup structure

            $process = new Process(['mysql', '-u', env('DB_USERNAME'), '-p' . env('DB_PASSWORD'), env('DB_DATABASE'), '<', $filePath]);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new \Exception($process->getErrorOutput());
            }

            $backup->update([
                'metadata' => array_merge($backup->metadata ?? [], [
                    'restored_at' => now()->toDateTimeString(),
                    'restored_by' => auth()->id(),
                ])
            ]);

            $this->logActivity(
                "Restored database from backup: {$backup->file_name}",
                'backup_restored',
                ['backup_id' => $backup->id, 'file_name' => $backup->file_name],
                'backup'
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Database restored successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Restore failed: ' . $e->getMessage()
            ]);
        } finally {
            $this->backupInProgress = false;
            $this->confirmingRestore = false;
        }
    }

    /**
     * Confirm delete backup
     */
    public function confirmDelete($backupId)
    {
        $this->dispatch('confirm-action', [
            'title' => 'Delete Backup',
            'message' => 'Are you sure you want to delete this backup? This action cannot be undone.',
            'event' => 'confirmDeleteBackup',
            'data' => ['backupId' => $backupId]
        ]);
    }

    /**
     * Delete backup
     */
    public function deleteBackup($data)
    {
        $backupId = $data['backupId'] ?? null;
        $backup = BackupHistory::find($backupId);

        if ($backup) {
            // Delete file
            Storage::disk($backup->disk)->delete($backup->file_path . '/' . $backup->file_name);

            // Delete record
            $backup->delete();

            $this->logActivity(
                "Deleted backup: {$backup->file_name}",
                'backup_deleted',
                ['backup_id' => $backup->id, 'file_name' => $backup->file_name],
                'backup'
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Backup deleted successfully.'
            ]);
        }
    }

    /**
     * Clean old backups
     */
    public function cleanOldBackups()
    {
        $this->backupInProgress = true;

        try {
            Artisan::call('backup:clean');

            $this->logActivity(
                'Cleaned old backups',
                'backups_cleaned',
                [],
                'backup'
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Old backups cleaned successfully.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to clean backups: ' . $e->getMessage()
            ]);
        } finally {
            $this->backupInProgress = false;
        }
    }

    /**
     * Get latest backup size
     */
    private function getLatestBackupSize()
    {
        $files = Storage::disk($this->backupDisk)->files($this->backupPath);
        $latestFile = collect($files)->sortByDesc(function ($file) {
            return Storage::disk($this->backupDisk)->lastModified($file);
        })->first();

        if ($latestFile) {
            return Storage::disk($this->backupDisk)->size($latestFile);
        }

        return null;
    }

    /**
     * Get available disks
     */
    public function getAvailableDisks()
    {
        return [
            'local' => 'Local Storage',
            's3' => 'Amazon S3',
            'ftp' => 'FTP Server',
        ];
    }

    /**
     * Get backup history
     */
    public function getBackupHistory()
    {
        return BackupHistory::with('creator')
            ->when($this->search, function ($query) {
                $query->where('file_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    /**
     * Get backup statistics
     */
    public function getStats()
    {
        return [
            'total_backups' => BackupHistory::count(),
            'successful_backups' => BackupHistory::where('status', 'completed')->count(),
            'failed_backups' => BackupHistory::where('status', 'failed')->count(),
            'total_size' => BackupHistory::where('status', 'completed')->sum('file_size'),
            'last_backup' => BackupHistory::where('status', 'completed')->latest()->first(),
            'backups_today' => BackupHistory::whereDate('created_at', today())->count(),
            'backups_this_week' => BackupHistory::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];
    }



    public function render()
    {
        return view('livewire.admin.settings.backup-settings', [
            'backups' => $this->getBackupHistory(),
            'stats' => $this->getStats(),
            'availableDisks' => $this->getAvailableDisks(),
            'frequencies' => [
                'daily' => 'Daily',
                'weekly' => 'Weekly',
                'monthly' => 'Monthly',
            ],
            'days' => [
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
                'friday' => 'Friday',
                'saturday' => 'Saturday',
                'sunday' => 'Sunday',
            ],
        ]);
    }
}
