<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organization extends Model
{
    use HasFactory, SoftDeletes, LogsModelActivity;

    protected $fillable = [
        'user_id',
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

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
        
    }

    /**
     * Get the placements for this organization.
     */
    public function placements()
    {
        return $this->hasMany(Placement::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function opportunities()
    {
        return $this->hasMany(AttachmentOpportunity::class);
    }

    /**
     * Calculate remaining slots for the current intake.
     */
    public function getAvailableSlotsAttribute(): int
    {
        $activePlacementsCount = $this->placements()
            ->where('status', 'placed')
            ->count();

        return max(0, $this->max_students_per_intake - $activePlacementsCount);
    }

    /**
     * Check if the organization can host more students.
     */
    public function hasCapacity(): bool
    {
        return $this->available_slots > 0;
    }
}
