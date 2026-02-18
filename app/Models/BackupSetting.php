<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupSetting extends Model
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

        return match ($this->type) {
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
        if (is_array($value) && isset($value['value'])) {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = json_encode(['value' => $value]);
        }
    }

    /**
     * Scope by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
