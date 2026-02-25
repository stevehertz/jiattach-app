<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AttachmentOpportunity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'title',
        'slug',
        'description',
        'published_at',
        'responsibilities',
        'type',
        'work_type',
        'location',
        'county',
        'min_gpa',
        'skills_required',
        'courses_required',
        'start_date',
        'end_date',
        'duration_months',
        'stipend',
        'slots_available',
        'deadline',
        'status',
    ];

    protected $casts = [
        'skills_required' => 'array',
        'courses_required' => 'array', // Stores IDs of Course model
        'start_date' => 'date',
        'end_date' => 'date',
        'deadline' => 'date',
        'stipend' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Boot function to auto-generate slug.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($opportunity) {
            $opportunity->slug = Str::slug($opportunity->title . '-' . Str::random(6));
        });
    }

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function placements()
    {
        return $this->hasMany(Placement::class);
    }

    // Accessors & Helpers
    public function getIsOpenAttribute()
    {
        return $this->status === 'open' && $this->deadline >= now();
    }

    public function getDaysRemainingAttribute()
    {
        return max(0, now()->diffInDays($this->deadline, false));
    }

    /**
     * Check if opportunity is active.
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === 'published' && !$this->application_deadline_passed && $this->slots_remaining > 0,
        );
    }

    /**
     * Check if opportunity is published.
     */
    protected function isPublished(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === 'published',
        );
    }

    /**
     * Check if opportunity is closed.
     */
    protected function isClosed(): Attribute
    {
        return Attribute::make(
            get: fn() => in_array($this->status, ['closed', 'filled', 'cancelled']),
        );
    }

    /**
     * Check if opportunity is draft.
     */
    protected function isDraft(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === 'draft',
        );
    }

    /**
     * Check if opportunity is pending approval.
     */
    protected function isPendingApproval(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === 'pending_approval',
        );
    }

    /**
     * Scope a query to only include active opportunities.
     */
    public function scopeActive($query)
    {
        return $query
            ->where('status', 'published')
            ->whereDate('deadline', '>=', now())
            ->where('slots_available', '>', 0);
    }
}
