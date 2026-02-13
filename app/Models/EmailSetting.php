<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailSetting extends Model
{
    use HasFactory, SoftDeletes, LogsModelActivity;

    protected $fillable = [
        'key',
        'value',
        'group',
        'category',
        'type',
        'description',
        'is_public',
        'is_encrypted',
        'sort_order',
        'validation_rules',
        'options',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_encrypted' => 'boolean',
        'validation_rules' => 'array',
        'options' => 'array',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'type_label',
        'category_label',
        'formatted_value',
        'validation_rules_array',
    ];

    /**
     * Get the type label.
     */
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'string' => 'Text',
                    'boolean' => 'Yes/No',
                    'integer' => 'Number',
                    'array' => 'List',
                    'json' => 'JSON',
                    'email' => 'Email',
                    'url' => 'URL',
                    'password' => 'Password',
                ];
                return $labels[$this->type] ?? ucfirst($this->type);
            }
        );
    }

    /**
     * Get the category label.
     */
    protected function categoryLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'smtp' => 'SMTP Settings',
                    'notification' => 'Notifications',
                    'template' => 'Templates',
                    'security' => 'Security',
                    'general' => 'General',
                ];
                return $labels[$this->category] ?? ucfirst($this->category);
            }
        );
    }

    /**
     * Get formatted value based on type.
     */
    protected function formattedValue(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_encrypted && $this->value) {
                    return '••••••••'; // Hide encrypted values
                }

                return match($this->type) {
                    'boolean' => $this->value ? 'Yes' : 'No',
                    'array' => is_array($this->value) ? implode(', ', $this->value) : $this->value,
                    default => $this->value,
                };
            }
        );
    }

    /**
     * Get validation rules as array.
     */
    protected function validationRulesArray(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->validation_rules ?? [],
        );
    }

    /**
     * Scope a query by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query by group.
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope a query to only include public settings.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include encrypted settings.
     */
    public function scopeEncrypted($query)
    {
        return $query->where('is_encrypted', true);
    }

    /**
     * Get setting value with decryption if needed.
     */
    public function getDecryptedValue(): mixed
    {
        if ($this->is_encrypted && $this->value) {
            return decrypt($this->value);
        }

        return $this->castValue($this->value, $this->type);
    }

    /**
     * Set value with encryption if needed.
     */
    public function setEncryptedValue($value): bool
    {
        if ($this->is_encrypted && $value) {
            $this->value = encrypt($value);
        } else {
            $this->value = $this->prepareValue($value, $this->type);
        }

        return $this->save();
    }

    /**
     * Cast value based on type.
     */
    private function castValue($value, string $type): mixed
    {
        return match($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'array', 'json' => json_decode($value, true) ?? [],
            default => $value,
        };
    }

    /**
     * Prepare value for storage based on type.
     */
    private function prepareValue($value, string $type): mixed
    {
        return match($type) {
            'boolean' => $value ? '1' : '0',
            'array', 'json' => json_encode($value),
            default => (string) $value,
        };
    }

    /**
     * Get all email settings grouped by category.
     */
    public static function getAllGrouped(): array
    {
        $settings = self::orderBy('sort_order')->get();
        $grouped = [];

        foreach ($settings as $setting) {
            $grouped[$setting->category][$setting->key] = [
                'value' => $setting->getDecryptedValue(),
                'type' => $setting->type,
                'label' => $setting->description ?? $setting->key,
                'options' => $setting->options,
                'validation' => $setting->validation_rules_array,
                'is_public' => $setting->is_public,
                'is_encrypted' => $setting->is_encrypted,
            ];
        }

        return $grouped;
    }

    /**
     * Get email settings by category.
     */
    public static function getByCategory(string $category): array
    {
        $settings = self::byCategory($category)->orderBy('sort_order')->get();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->getDecryptedValue();
        }

        return $result;
    }

    /**
     * Update multiple email settings.
     */
    public static function updateSettings(array $settings): bool
    {
        try {
            foreach ($settings as $key => $value) {
                $setting = self::where('key', $key)->first();

                if ($setting) {
                    $setting->setEncryptedValue($value);
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update email settings: ' . $e->getMessage());
            return false;
        }
    }
}
