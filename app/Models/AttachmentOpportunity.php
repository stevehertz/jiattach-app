<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttachmentOpportunity extends Model
{
    use HasFactory, SoftDeletes;

     protected $fillable = [
        'organization_id',
        'title',
        'slug',
        'description',
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
}
