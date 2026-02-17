<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'type',
        'options',
        'value',
        'category',
        'sort_order',
        'is_system',
        'is_active'
    ];

    protected $casts = [
        'options' => 'array',
        'value' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

      /**
     * Get setting value with type casting
     */
    public function getTypedValue()
    {
        $value = $this->value['value'] ?? null;
        
        return match($this->type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Set setting value
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = json_encode(['value' => $value]);
    }

    /**
     * Scope by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
