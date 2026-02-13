<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Log;

class General extends Component
{
    public $siteName;
    public $siteEmail;
    public $sitePhone;
    public $siteAddress;
    public $siteDescription;
    public $siteLogo;
    public $siteFavicon;
    public $timezone = 'Africa/Nairobi';
    public $dateFormat = 'd/m/Y';
    public $timeFormat = 'H:i';
    public $currency = 'KES';
    public $language = 'en';

    public $settings = [];

    // Validation rules
    protected $rules = [
        'settings.site_name' => 'required|string|max:255',
        'settings.site_email' => 'nullable|email|max:255',
        'settings.site_phone' => 'nullable|string|max:20',
        'settings.site_address' => 'nullable|string|max:500',
        'settings.site_description' => 'nullable|string|max:1000',
        'settings.timezone' => 'required|string|timezone',
        'settings.date_format' => 'required|string|in:d/m/Y,m/d/Y,Y-m-d,d M, Y',
        'settings.time_format' => 'required|string|in:H:i,h:i A',
        'settings.currency' => 'required|string|size:3',
        'settings.language' => 'required|string|in:en,sw',
        'settings.site_logo' => 'nullable|string|max:500',
        'settings.site_favicon' => 'nullable|string|max:500',
        'settings.copyright_text' => 'nullable|string|max:500',
        'settings.maintenance_mode' => 'boolean',
    ];

     protected $messages = [
        'settings.site_name.required' => 'Site name is required.',
        'settings.site_email.email' => 'Please enter a valid email address.',
        'settings.timezone.required' => 'Timezone is required.',
        'settings.currency.required' => 'Currency is required.',
        'settings.currency.size' => 'Currency must be 3 characters (e.g., KES, USD).',
        'settings.language.required' => 'Language is required.',
    ];


    public function mount(SettingsService $settingsService)
    {
        // Load current settings
        $generalSettings = $settingsService->getGeneral();

        // Initialize settings array
        foreach ($generalSettings as $key => $setting) {
            $this->settings[$key] = $setting['value'] ?? '';
        }

        // Set defaults for any missing settings
        $defaults = [
            'site_name' => config('app.name'),
            'site_email' => '',
            'site_phone' => '',
            'site_address' => '',
            'site_description' => '',
            'timezone' => config('app.timezone', 'Africa/Nairobi'),
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'currency' => 'KES',
            'language' => config('app.locale', 'en'),
            'site_logo' => '',
            'site_favicon' => '',
            'copyright_text' => 'Copyright © ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.',
            'maintenance_mode' => false,
        ];

        foreach ($defaults as $key => $defaultValue) {
            if (!isset($this->settings[$key])) {
                $this->settings[$key] = $defaultValue;
            }
        }

        // Convert boolean strings to actual booleans
        if (isset($this->settings['maintenance_mode'])) {
            $this->settings['maintenance_mode'] = (bool) $this->settings['maintenance_mode'];
        }
    }

    public function save(SettingsService $settingsService)
    {
        // Validate the settings
        $this->validate();

        try {
            // Update each setting in the database
            foreach ($this->settings as $key => $value) {

                // Skip maintenance_mode - it's handled separately as boolean
                if ($key === 'maintenance_mode') {
                    continue;
                }

                // Determine type based on key
                $type = 'string';
                if (in_array($key, ['site_description', 'site_address', 'copyright_text'])) {
                    $type = 'text';
                }

                $settingsService->set($key, $value, $type, 'general');
            }

            // Update maintenance mode as boolean
            $settingsService->set(
                'maintenance_mode',
                $this->settings['maintenance_mode'] ?? false,
                'boolean',
                'general'
            );

            // Clear cache to ensure fresh data
            $settingsService->clearCache();

            // Update app name in config if needed
            if (config('app.name') !== $this->settings['site_name']) {
                // You could update .env file here, but for safety we'll just update cache
                // In production, you'd want to update the .env file
                config(['app.name' => $this->settings['site_name']]);
            }

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'General settings saved successfully!'
            ]);

            // Refresh the page to show updated settings
            $this->dispatch('settings-saved');
        } catch (\Exception $e) {
            Log::error('Failed to save settings: ' . $e->getMessage());

            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Failed to save settings. Please try again.'
            ]);
        }
    }

     public function toggleMaintenanceMode(SettingsService $settingsService)
    {
        $this->settings['maintenance_mode'] = !($this->settings['maintenance_mode'] ?? false);

        $message = $this->settings['maintenance_mode']
            ? 'Maintenance mode enabled. The site is now in maintenance mode.'
            : 'Maintenance mode disabled. The site is now live.';

        $this->dispatch('show-toast', [
            'type' => $this->settings['maintenance_mode'] ? 'warning' : 'success',
            'message' => $message
        ]);
    }

    public function testEmail()
    {
        // This would send a test email
        $this->dispatch('show-toast', [
            'type' => 'info',
            'message' => 'Test email functionality will be implemented in the email settings section.'
        ]);
    }

    // Optional: Add method to update .env file
    private function updateEnv(array $data)
    {
        $envPath = base_path('.env');

        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);

            foreach ($data as $key => $value) {
                // Escape quotes in value
                $escapedValue = str_replace('"', '\"', $value);

                // Update or add the key
                if (strpos($envContent, "{$key}=") !== false) {
                    $envContent = preg_replace(
                        "/^{$key}=.*/m",
                        "{$key}=\"{$escapedValue}\"",
                        $envContent
                    );
                } else {
                    $envContent .= "\n{$key}=\"{$escapedValue}\"";
                }
            }

            file_put_contents($envPath, $envContent);
        }
    }


    public function render()
    {
        $timezones = \DateTimeZone::listIdentifiers();
        $currencies = [
            'KES' => 'KES - Kenyan Shilling',
            'USD' => 'USD - US Dollar',
            'EUR' => 'EUR - Euro',
            'GBP' => 'GBP - British Pound',
        ];
        $languages = [
            'en' => 'English',
            'sw' => 'Swahili',
        ];
        $dateFormats = [
            'd/m/Y' => 'DD/MM/YYYY (e.g., 31/12/2024)',
            'm/d/Y' => 'MM/DD/YYYY (e.g., 12/31/2024)',
            'Y-m-d' => 'YYYY-MM-DD (e.g., 2024-12-31)',
            'd M, Y' => 'DD Mon, YYYY (e.g., 31 Dec, 2024)',
        ];
        $timeFormats = [
            'H:i' => '24-hour format (e.g., 14:30)',
            'h:i A' => '12-hour format (e.g., 2:30 PM)',
        ];
        return view('livewire.admin.settings.general', compact(
            'timezones',
            'currencies',
            'languages',
            'dateFormats',
            'timeFormats'
        ));
    }
}
