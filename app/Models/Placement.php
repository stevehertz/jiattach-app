<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Placement extends Model
{
    use HasFactory, SoftDeletes, LogsModelActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'admin_id',
        'organization_id',
        'attachment_opportunity_id',
        'status',
        'notes',
        'start_date',
        'end_date',
        'duration_days',
        'department',
        'supervisor_name',
        'supervisor_contact',
        'requirements',
        'stipend',
        'admin_notified_at',
        'student_notified_at',
        'placement_confirmed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'requirements' => 'array',
        'stipend' => 'decimal:2',
        'admin_notified_at' => 'datetime',
        'student_notified_at' => 'datetime',
        'placement_confirmed_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status_label',
        'duration_months',
        'is_active',
        'remaining_days',
        'progress_percentage',
    ];

    /**
     * Get the student for this placement.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the admin who assigned this placement.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the organization for this placement.
     */
    public function organization()
    {
        return $this->hasOne(Organization::class, 'id', 'organization_id');
    }

    /**
     * Get the attachment opportunity for this placement.
     */
    public function opportunity()
    {
        return $this->belongsTo(AttachmentOpportunity::class, 'attachment_opportunity_id');
    }

    // Also add these accessor methods to Placement model:
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'placed' => 'Placed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getIsActiveAttribute()
    {
        if ($this->status !== 'placed') {
            return false;
        }

        $now = now();
        return $this->start_date <= $now && $this->end_date >= $now;
    }

    public function getDurationMonthsAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        return $this->start_date->diffInMonths($this->end_date);
    }

    /**
     * Calculate the percentage of completion for the placement.
     */
    public function getProgressPercentageAttribute(): int
    {
        if (!$this->start_date || !$this->end_date || $this->status !== 'placed') {
            return 0;
        }

        $totalDays = $this->start_date->diffInDays($this->end_date);
        $daysElapsed = $this->start_date->diffInDays(now());

        if (now() < $this->start_date) return 0;
        if (now() > $this->end_date) return 100;

        return (int) round(($daysElapsed / max(1, $totalDays)) * 100);
    }

    /**
     * Calculate remaining days until the end of the attachment.
     */
    public function getRemainingDaysAttribute(): int
    {
        if (!$this->end_date || now() > $this->end_date) {
            return 0;
        }
        return (int) now()->diffInDays($this->end_date);
    }

    /**
     * Transition the placement to a new status with logging.
     */
    public function transitionTo(string $newStatus, ?string $note = null): bool
    {
        $this->status = $newStatus;
        if ($note) $this->notes = $note;

        if ($newStatus === 'placed') {
            $this->placement_confirmed_at = now();
        }

        return $this->save();
    }
}
