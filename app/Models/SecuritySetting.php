<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecuritySetting extends Model
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
        // Since value is already cast to array, we can access it directly
        $value = $this->value['value'] ?? null;
        
        // Handle based on type
        return match($this->type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'json' => is_string($value) ? json_decode($value, true) : $value,
            default => $value,
        };
    }

    /**
     * Set setting value
     */
    public function setValueAttribute($value)
    {
        // If value is already an array with 'value' key, use it as is
        if (is_array($value) && isset($value['value'])) {
            $this->attributes['value'] = json_encode($value);
        } else {
            // Otherwise, wrap it in the expected format
            $this->attributes['value'] = json_encode(['value' => $value]);
        }
    }

    /**
     * Get the raw value without type casting
     */
    public function getRawValueAttribute()
    {
        return $this->value['value'] ?? null;
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
