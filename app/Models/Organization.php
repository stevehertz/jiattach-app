<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory, SoftDeletes, LogsModelActivity;
    
    protected $fillable = [
        'name',
        'type',
        'industry',
        'email',
        'phone',
        'website',
        'address',
        'county',
        'constituency',
        'ward',
        'contact_person_name',
        'contact_person_email',
        'contact_person_phone',
        'contact_person_position',
        'description',
        'departments',
        'max_students_per_intake',
        'is_active',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'departments' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the placements for this organization.
     */
    public function placements()
    {
        return $this->hasMany(Placement::class);
    }
}
