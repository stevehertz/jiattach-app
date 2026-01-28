<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'type', 'county', 'website', 'is_active'];
    
    // Helper to get formatted name
    public function getDisplayNameAttribute()
    {
        return "{$this->name} (" . ucfirst($this->type) . ")";
    }
}
