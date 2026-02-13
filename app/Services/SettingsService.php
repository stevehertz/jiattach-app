<?php

namespace App\Services;

use App\Models\SystemSetting;

class SettingsService
{
    public function __construct()
    {
        // Ensure default settings exist
        SystemSetting::ensureDefaults();
    }

    /**
     * Cache key for settings.
     */
    protected string $cacheKey = 'system_settings';

    /**
     * Cache duration in minutes.
     */
    protected int $cacheDuration = 60;

    /**
     * Get all settings with caching.
     *
     * @return array
     */
    public function getAll(): array
    {
        return cache()->remember($this->cacheKey, $this->cacheDuration, function () {
            return SystemSetting::getAllGrouped();
        });
    }

    /**
     * Get setting by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $settings = $this->getAll();

        // Parse dot notation (e.g., 'general.site_name')
        if (strpos($key, '.') !== false) {
            $parts = explode('.', $key);
            $group = $parts[0];
            $settingKey = $parts[1];

            return $settings[$group][$settingKey]['value'] ?? $default;
        }

        // Search through all groups
        foreach ($settings as $group => $groupSettings) {
            if (isset($groupSettings[$key])) {
                return $groupSettings[$key]['value'];
            }
        }

        return $default;
    }

    /**
     * Set setting value.
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @param string|null $description
     * @param bool $isPublic
     * @return bool
     */
    public function set(
        string $key,
        $value,
        string $type = 'string',
        string $group = 'general',
        ?string $description = null,
        bool $isPublic = false
    ): bool {
        $result = SystemSetting::setValue($key, $value, $type, $group, $description, $isPublic);

        if ($result) {
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Update multiple settings.
     *
     * @param array $settings
     * @return bool
     */
    public function updateMultiple(array $settings): bool
    {
        $result = SystemSetting::updateMultiple($settings);

        if ($result) {
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Get settings by group.
     *
     * @param string $group
     * @return array
     */
    public function getGroup(string $group): array
    {
        $allSettings = $this->getAll();
        return $allSettings[$group] ?? [];
    }

    /**
     * Get general settings.
     *
     * @return array
     */
    public function getGeneral(): array
    {
        return $this->getGroup('general');
    }

    /**
     * Get email settings.
     *
     * @return array
     */
    public function getEmail(): array
    {
        return $this->getGroup('email');
    }

    /**
     * Clear settings cache.
     *
     * @return void
     */
    public function clearCache(): void
    {
        cache()->forget($this->cacheKey);
    }

    /**
     * Get public settings for frontend.
     *
     * @return array
     */
    public function getPublic(): array
    {
        return cache()->remember("{$this->cacheKey}_public", $this->cacheDuration, function () {
            return SystemSetting::getPublicSettings();
        });
    }

    /**
     * Check if maintenance mode is enabled.
     *
     * @return bool
     */
    public function isMaintenanceMode(): bool
    {
        return (bool) $this->get('general.maintenance_mode', false);
    }

     /**
     * Enable maintenance mode.
     *
     * @return bool
     */
    public function enableMaintenanceMode(): bool
    {
        return $this->set('maintenance_mode', true, 'boolean', 'general', 'Enable maintenance mode', false);
    }

    /**
     * Disable maintenance mode.
     *
     * @return bool
     */
    public function disableMaintenanceMode(): bool
    {
        return $this->set('maintenance_mode', false, 'boolean', 'general', 'Enable maintenance mode', false);
    }

    /**
     * Get site configuration.
     *
     * @return array
     */
    public function getSiteConfig(): array
    {
        return [
            'name' => $this->get('general.site_name', config('app.name')),
            'email' => $this->get('general.site_email', 'support@jiattach.co.ke'),
            'phone' => $this->get('general.site_phone', '+254 700 123 456'),
            'address' => $this->get('general.site_address', 'Nairobi, Kenya'),
            'description' => $this->get('general.site_description', 'Jiattach - Platform for tertiary students in Kenya to secure attachments and internships'),
            'timezone' => $this->get('general.timezone', config('app.timezone')),
            'currency' => $this->get('general.currency', 'KES'),
            'language' => $this->get('general.language', config('app.locale')),
            'logo' => $this->get('general.site_logo', ''),
            'favicon' => $this->get('general.site_favicon', ''),
            'copyright' => $this->get('general.copyright_text', 'Copyright © ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.'),
            'date_format' => $this->get('general.date_format', 'd/m/Y'),
            'time_format' => $this->get('general.time_format', 'H:i'),
            'maintenance_mode' => $this->get('general.maintenance_mode', false),
        ];
    }
}
