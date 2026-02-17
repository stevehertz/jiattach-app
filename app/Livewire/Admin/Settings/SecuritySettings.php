<?php

namespace App\Livewire\Admin\Settings;

use App\Models\LoginAttempt;
use App\Models\SecuritySetting;
use App\Models\User;
use App\Models\UserTwoFactorSetting;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class SecuritySettings extends Component
{
    use WithPagination, LogsActivity;

    // Active tab
    public $activeTab = 'password-policy';

    // Settings
    public $settings = [];
    public $originalSettings = [];

    // Password Policy
    public $passwordMinLength = 8;
    public $passwordRequireMixedCase = true;
    public $passwordRequireNumbers = true;
    public $passwordRequireSymbols = false;
    public $passwordExpiryDays = 90;
    public $passwordHistoryCount = 5;
    public $maxLoginAttempts = 5;
    public $lockoutTime = 15; // minutes

    // Session Management
    public $sessionLifetime = 120; // minutes
    public $sessionTimeout = 30; // minutes
    public $singleDeviceSession = false;
    public $rememberMeLifetime = 43200; // minutes (30 days)

    // 2FA Settings
    public $requireTwoFactor = false;
    public $twoFactorForRoles = [];
    public $twoFactorMethods = ['app' => true, 'email' => true, 'sms' => false];

    // Audit & Logging
    public $logAllEvents = true;
    public $logAuthEvents = true;
    public $logModelEvents = true;
    public $retentionDays = 90;

    // User 2FA Management
    public $search = '';
    public $selectedUser = null;
    public $showUser2FAModal = false;
    public $userTwoFactorStatus = false;
    public $userTwoFactorMethod = 'app';
    public $userRecoveryCodes = [];

    // IP Whitelist
    public $ipWhitelist = [];
    public $newIpAddress = '';
    public $ipDescription = '';

    // Security Logs Filters
    public $logSearch = '';
    public $logEvent = '';
    public $logDateFrom = '';
    public $logDateTo = '';

    protected $listeners = [
        'refresh' => '$refresh',
        'confirmClearLogs' => 'clearLogs'
    ];

    public function mount()
    {
        $this->loadSettings();
        $this->loadIpWhitelist();
        $this->originalSettings = $this->settings;
    }

    /**
     * Load security settings from database
     */
    public function loadSettings()
    {
        $dbSettings = SecuritySetting::orderBy('sort_order')->get();
        
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

            // Also set individual properties for easier form binding
            $propertyName = $this->getPropertyName($setting->key);
            if ($propertyName) {
                $this->{$propertyName} = $setting->getTypedValue();
            }
        }
    }

    /**
     * Load IP whitelist from cache/settings
     */
    public function loadIpWhitelist()
    {
        $this->ipWhitelist = Cache::get('ip_whitelist', []);
    }

    /**
     * Get property name from setting key
     */
    private function getPropertyName($key)
    {
        return match($key) {
            'password.min_length' => 'passwordMinLength',
            'password.require_mixed_case' => 'passwordRequireMixedCase',
            'password.require_numbers' => 'passwordRequireNumbers',
            'password.require_symbols' => 'passwordRequireSymbols',
            'password.expiry_days' => 'passwordExpiryDays',
            'password.history_count' => 'passwordHistoryCount',
            'login.max_attempts' => 'maxLoginAttempts',
            'login.lockout_time' => 'lockoutTime',
            'session.lifetime' => 'sessionLifetime',
            'session.timeout' => 'sessionTimeout',
            'session.single_device' => 'singleDeviceSession',
            'session.remember_me_lifetime' => 'rememberMeLifetime',
            '2fa.require' => 'requireTwoFactor',
            '2fa.roles' => 'twoFactorForRoles',
            'audit.log_all' => 'logAllEvents',
            'audit.log_auth' => 'logAuthEvents',
            'audit.log_models' => 'logModelEvents',
            'audit.retention_days' => 'retentionDays',
            default => null
        };
    }

    /**
     * Save password policy settings
     */
    public function savePasswordPolicy()
    {
        $this->validate([
            'passwordMinLength' => 'required|integer|min:6|max:128',
            'passwordExpiryDays' => 'required|integer|min:0|max:365',
            'passwordHistoryCount' => 'required|integer|min:0|max:24',
            'maxLoginAttempts' => 'required|integer|min:1|max:50',
            'lockoutTime' => 'required|integer|min:1|max:1440'
        ]);

        $settings = [
            'password.min_length' => $this->passwordMinLength,
            'password.require_mixed_case' => $this->passwordRequireMixedCase,
            'password.require_numbers' => $this->passwordRequireNumbers,
            'password.require_symbols' => $this->passwordRequireSymbols,
            'password.expiry_days' => $this->passwordExpiryDays,
            'password.history_count' => $this->passwordHistoryCount,
            'login.max_attempts' => $this->maxLoginAttempts,
            'login.lockout_time' => $this->lockoutTime
        ];

        $this->saveSettings($settings, 'password policy');
    }

    /**
     * Save session settings
     */
    public function saveSessionSettings()
    {
        $this->validate([
            'sessionLifetime' => 'required|integer|min:5|max:43200',
            'sessionTimeout' => 'required|integer|min:0|max:1440',
            'rememberMeLifetime' => 'required|integer|min:0|max:43200'
        ]);

        $settings = [
            'session.lifetime' => $this->sessionLifetime,
            'session.timeout' => $this->sessionTimeout,
            'session.single_device' => $this->singleDeviceSession,
            'session.remember_me_lifetime' => $this->rememberMeLifetime
        ];

        $this->saveSettings($settings, 'session management');
    }

    /**
     * Save 2FA settings
     */
    public function saveTwoFactorSettings()
    {
        $settings = [
            '2fa.require' => $this->requireTwoFactor,
            '2fa.roles' => $this->twoFactorForRoles
        ];

        $this->saveSettings($settings, 'two-factor authentication');

        // If 2FA is required, notify users
        if ($this->requireTwoFactor) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => '2FA is now required. Users without 2FA enabled will be prompted on next login.'
            ]);
        }
    }

    /**
     * Save audit settings
     */
    public function saveAuditSettings()
    {
        $this->validate([
            'retentionDays' => 'required|integer|min:30|max:730'
        ]);

        $settings = [
            'audit.log_all' => $this->logAllEvents,
            'audit.log_auth' => $this->logAuthEvents,
            'audit.log_models' => $this->logModelEvents,
            'audit.retention_days' => $this->retentionDays
        ];

        $this->saveSettings($settings, 'audit logging');
    }

    /**
     * Save settings to database
     */
    private function saveSettings($settings, $logDescription)
    {
        $changed = [];
        
        foreach ($settings as $key => $value) {
            $setting = SecuritySetting::where('key', $key)->first();
            
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
                'security_settings_updated',
                ['changes' => $changed],
                'security'
            );
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => count($changed) . ' security settings updated successfully.'
        ]);
    }

    /**
     * Add IP to whitelist
     */
    public function addIpToWhitelist()
    {
        $this->validate([
            'newIpAddress' => 'required|ip',
            'ipDescription' => 'nullable|string|max:255'
        ]);

        $ipWhitelist = $this->ipWhitelist;
        $ipWhitelist[] = [
            'ip' => $this->newIpAddress,
            'description' => $this->ipDescription,
            'added_at' => now()->toDateTimeString(),
            'added_by' => auth()->user()->email
        ];

        Cache::forever('ip_whitelist', $ipWhitelist);
        $this->ipWhitelist = $ipWhitelist;

        $this->logActivity(
            "Added IP to whitelist: {$this->newIpAddress}",
            'ip_whitelist_added',
            ['ip' => $this->newIpAddress, 'description' => $this->ipDescription],
            'security'
        );

        $this->reset(['newIpAddress', 'ipDescription']);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'IP address added to whitelist.'
        ]);
    }

    /**
     * Remove IP from whitelist
     */
    public function removeIpFromWhitelist($index)
    {
        $removedIp = $this->ipWhitelist[$index]['ip'] ?? 'unknown';
        
        $ipWhitelist = $this->ipWhitelist;
        unset($ipWhitelist[$index]);
        $this->ipWhitelist = array_values($ipWhitelist);

        Cache::forever('ip_whitelist', $this->ipWhitelist);

        $this->logActivity(
            "Removed IP from whitelist: {$removedIp}",
            'ip_whitelist_removed',
            ['ip' => $removedIp],
            'security'
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'IP address removed from whitelist.'
        ]);
    }

    /**
     * View user 2FA settings
     */
    public function viewUser2FA($userId)
    {
        $this->selectedUser = User::find($userId);
        
        if ($this->selectedUser) {
            $twoFactor = $this->selectedUser->twoFactorSetting ?? 
                        UserTwoFactorSetting::create(['user_id' => $userId]);
            
            $this->userTwoFactorStatus = $twoFactor->is_enabled;
            $this->userTwoFactorMethod = $twoFactor->method ?? 'app';
            $this->userRecoveryCodes = $twoFactor->recovery_codes ?? [];
            $this->showUser2FAModal = true;
        }
    }

    /**
     * Disable user 2FA
     */
    public function disableUser2FA()
    {
        if ($this->selectedUser) {
            $twoFactor = $this->selectedUser->twoFactorSetting;
            if ($twoFactor) {
                $twoFactor->update([
                    'is_enabled' => false,
                    'secret' => null,
                    'recovery_codes' => null
                ]);

                $this->logActivity(
                    "Disabled 2FA for user: {$this->selectedUser->email}",
                    'user_2fa_disabled',
                    ['user_id' => $this->selectedUser->id, 'user_email' => $this->selectedUser->email],
                    'security'
                );

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => '2FA disabled for user.'
                ]);
            }
        }
        
        $this->showUser2FAModal = false;
    }

    /**
     * Generate new recovery codes for user
     */
    public function generateRecoveryCodes()
    {
        if ($this->selectedUser && $this->selectedUser->twoFactorSetting) {
            $codes = $this->selectedUser->twoFactorSetting->generateRecoveryCodes();
            $this->userRecoveryCodes = $codes;

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'New recovery codes generated.'
            ]);
        }
    }

    /**
     * Clear security logs
     */
    public function confirmClearLogs()
    {
        $this->dispatch('confirm-action', [
            'title' => 'Clear Security Logs',
            'message' => 'Are you sure you want to clear all security logs? This action cannot be undone.',
            'event' => 'confirmClearLogs'
        ]);
    }

    public function clearLogs()
    {
        LoginAttempt::truncate();
        
        $this->logActivity(
            'Cleared all security logs',
            'security_logs_cleared',
            [],
            'security'
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Security logs cleared successfully.'
        ]);
    }

    /**
     * Get recent login attempts
     */
    public function getLoginAttempts()
    {
        return LoginAttempt::query()
            ->when($this->logSearch, function ($query) {
                $query->where('email', 'like', '%' . $this->logSearch . '%')
                    ->orWhere('ip_address', 'like', '%' . $this->logSearch . '%');
            })
            ->when($this->logEvent !== '', function ($query) {
                $query->where('success', $this->logEvent === 'success');
            })
            ->when($this->logDateFrom, function ($query) {
                $query->whereDate('attempted_at', '>=', $this->logDateFrom);
            })
            ->when($this->logDateTo, function ($query) {
                $query->whereDate('attempted_at', '<=', $this->logDateTo);
            })
            ->orderBy('attempted_at', 'desc')
            ->paginate(20);
    }

    /**
     * Get users without 2FA
     */
    public function getUsersWithout2FA()
    {
        return User::whereDoesntHave('twoFactorSetting', function ($query) {
                $query->where('is_enabled', true);
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);
    }

    /**
     * Reset settings to defaults
     */
    public function resetToDefaults()
    {
        if (!auth()->user()->hasRole('super-admin')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Only super administrators can reset settings.'
            ]);
            return;
        }

        $this->dispatch('confirm-action', [
            'title' => 'Reset Security Settings',
            'message' => 'Are you sure you want to reset all security settings to defaults?',
            'event' => 'confirmResetDefaults'
        ]);
    }

    public function confirmResetDefaults()
    {
        $settings = SecuritySetting::all();
        foreach ($settings as $setting) {
            $defaultValue = $setting->value['default'] ?? null;
            if ($defaultValue !== null) {
                $setting->update(['value' => ['value' => $defaultValue]]);
            }
        }

        $this->loadSettings();
        $this->originalSettings = $this->settings;

        $this->logActivity(
            'Reset security settings to defaults',
            'security_settings_reset',
            ['reset_by' => auth()->user()->email],
            'security'
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Security settings have been reset to defaults.'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.settings.security-settings', [
            'loginAttempts' => $this->getLoginAttempts(),
            'usersWithout2FA' => $this->getUsersWithout2FA(),
            'stats' => [
                'failed_attempts_today' => LoginAttempt::where('success', false)
                    ->whereDate('attempted_at', today())
                    ->count(),
                'successful_logins_today' => LoginAttempt::where('success', true)
                    ->whereDate('attempted_at', today())
                    ->count(),
                'unique_ips_today' => LoginAttempt::whereDate('attempted_at', today())
                    ->distinct('ip_address')
                    ->count('ip_address'),
                'users_with_2fa' => UserTwoFactorSetting::where('is_enabled', true)->count(),
                'total_users' => User::count(),
            ]
        ]);
    }
}
