<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
     * The users associated with this organization.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'position', 'is_primary_contact', 'is_active'])
            ->withTimestamps()
            ->wherePivot('is_active', true); // Only get active assignments by default
    }

      /**
     * Get the primary owner(s) of the organization.
     * Usually there's one owner, but could be multiple.
     */
    public function owners()
    {
        return $this->users()->wherePivot('role', 'owner');
    }

     /**
     * Get the primary contact person(s).
     */
    public function primaryContacts()
    {
        return $this->users()->wherePivot('is_primary_contact', true);
    }

    /**
     * Get the administrators of the organization.
     */
    public function admins()
    {
        return $this->users()->wherePivot('role', 'admin');
    }

    /**
     * Check if a user is associated with this organization.
     */
    public function hasUser(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

     /**
     * Check if a user is an owner of this organization.
     */
    public function isOwner(User $user): bool
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'owner')
            ->exists();
    }

     /**
     * Check if a user is an admin of this organization.
     */
    public function isAdmin(User $user): bool
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }


    /**
     * Get the placements for this organization.
     */
    public function placements()
    {
        return $this->hasMany(Placement::class);
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

     /**
     * Scope to get verified organizations.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to get active organizations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get organizations by industry.
     */
    public function scopeByIndustry($query, $industry)
    {
        return $query->where('industry', $industry);
    }

    /**
     * Scope to get organizations by county.
     */
    public function scopeByCounty($query, $county)
    {
        return $query->where('county', $county);
    }
}
