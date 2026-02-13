<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemSetting extends Model
{
    use HasFactory, LogsModelActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

     /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

     /**
     * Get setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set setting value by key.
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @param string|null $description
     * @param bool $isPublic
     * @return bool
     */
    public static function setValue(
        string $key,
        $value,
        string $type = 'string',
        string $group = 'general',
        ?string $description = null,
        bool $isPublic = false
    ): bool {
        $setting = static::where('key', $key)->first();

        if ($setting) {
            $setting->value = self::prepareValue($value, $type);
            $setting->type = $type;
            $setting->group = $group;
            if ($description) {
                $setting->description = $description;
            }
            $setting->is_public = $isPublic;
            return $setting->save();
        }

        return static::create([
            'key' => $key,
            'value' => self::prepareValue($value, $type),
            'type' => $type,
            'group' => $group,
            'description' => $description,
            'is_public' => $isPublic,
        ]) !== null;
    }

    /**
     * Prepare value for storage based on type.
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    private static function prepareValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'array':
            case 'json':
                return json_encode($value);
            case 'integer':
                return (int) $value;
            default:
                return (string) $value;
        }
    }

    /**
     * Cast value from storage based on type.
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    private static function castValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'array':
            case 'json':
                return json_decode($value, true) ?: [];
            case 'integer':
                return (int) $value;
            default:
                return $value;
        }
    }

     /**
     * Get all settings grouped by group.
     *
     * @return array
     */
    public static function getAllGrouped(): array
    {
        $settings = static::all();
        $grouped = [];

        foreach ($settings as $setting) {
            $grouped[$setting->group][$setting->key] = [
                'value' => self::castValue($setting->value, $setting->type),
                'type' => $setting->type,
                'description' => $setting->description,
                'is_public' => $setting->is_public,
            ];
        }

        return $grouped;
    }

    /**
     * Get settings by group.
     *
     * @param string $group
     * @return array
     */
    public static function getByGroup(string $group): array
    {
        $settings = static::where('group', $group)->get();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = [
                'value' => self::castValue($setting->value, $setting->type),
                'type' => $setting->type,
                'description' => $setting->description,
                'is_public' => $setting->is_public,
            ];
        }

        return $result;
    }

    /**
     * Update multiple settings at once.
     *
     * @param array $settings
     * @return bool
     */
    public static function updateMultiple(array $settings): bool
    {
        try {
            foreach ($settings as $key => $value) {
                $setting = static::where('key', $key)->first();

                if ($setting) {
                    $setting->value = self::prepareValue($value, $setting->type);
                    $setting->save();
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update settings: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get public settings for frontend.
     *
     * @return array
     */
    public static function getPublicSettings(): array
    {
        $settings = static::where('is_public', true)->get();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = self::castValue($setting->value, $setting->type);
        }

        return $result;
    }

    /**
     * Check if a setting exists.
     *
     * @param string $key
     * @return bool
     */
    public static function exists(string $key): bool
    {
        return static::where('key', $key)->exists();
    }

    // Add this method to the SystemSetting model
    public static function ensureDefaults()
    {
        $defaults = [
            // General Settings
            'site_name' => [
                'value' => config('app.name'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'The name of the application',
                'is_public' => true,
            ],
            'site_email' => [
                'value' => 'support@jiattach.co.ke',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Primary email address for the site',
                'is_public' => true,
            ],
            'site_phone' => [
                'value' => '+254 700 123 456',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Primary phone number for the site',
                'is_public' => true,
            ],
            'site_address' => [
                'value' => 'Nairobi, Kenya',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Physical address of the organization',
                'is_public' => true,
            ],
            'site_description' => [
                'value' => 'Jiattach - Platform for tertiary students in Kenya to secure attachments and internships',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Brief description of the platform',
                'is_public' => true,
            ],
            'timezone' => [
                'value' => 'Africa/Nairobi',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default timezone for the application',
                'is_public' => false,
            ],
            'date_format' => [
                'value' => 'd/m/Y',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default date format',
                'is_public' => false,
            ],
            'time_format' => [
                'value' => 'H:i',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default time format',
                'is_public' => false,
            ],
            'currency' => [
                'value' => 'KES',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default currency',
                'is_public' => true,
            ],
            'language' => [
                'value' => 'en',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default language',
                'is_public' => false,
            ],
            'maintenance_mode' => [
                'value' => '0',
                'type' => 'boolean',
                'group' => 'general',
                'description' => 'Enable maintenance mode',
                'is_public' => false,
            ],
            'site_logo' => [
                'value' => '',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Site logo URL',
                'is_public' => true,
            ],
            'site_favicon' => [
                'value' => '',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Site favicon URL',
                'is_public' => true,
            ],
            'copyright_text' => [
                'value' => 'Copyright © ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Copyright text for the footer',
                'is_public' => true,
            ],
        ];

        foreach ($defaults as $key => $default) {
            if (!self::where('key', $key)->exists()) {
                self::create([
                    'key' => $key,
                    'value' => $default['value'],
                    'type' => $default['type'],
                    'group' => $default['group'],
                    'description' => $default['description'],
                    'is_public' => $default['is_public'],
                ]);
            }
        }
    }

}
