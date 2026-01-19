<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mentorship extends Model
{
    use HasFactory, LogsModelActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mentor_id',
        'mentee_id',
        'title',
        'description',
        'goals',
        'topics',
        'status',
        'meeting_preference',
        'duration_weeks',
        'meetings_per_month',
        'meeting_duration_minutes',
        'start_date',
        'end_date',
        'requested_at',
        'approved_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'completion_notes',
        'hourly_rate',
        'is_paid',
        'payment_status',
        'availability',
        'expectations',
        'mentor_expectations',
        'mentee_expectations',
        'is_confidential',
        'experience_level',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'goals' => 'array',
        'topics' => 'array',
        'availability' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'hourly_rate' => 'decimal:2',
        'is_paid' => 'boolean',
        'is_confidential' => 'boolean',
        'duration_weeks' => 'integer',
        'meetings_per_month' => 'integer',
        'meeting_duration_minutes' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status_label',
        'meeting_preference_label',
        'experience_level_label',
        'payment_status_label',
        'is_active',
        'is_pending',
        'is_completed',
        'is_cancelled',
        'total_meetings',
        'meetings_completed',
        'meetings_remaining',
        'progress_percentage',
        'weeks_remaining',
        'duration_months',
        'estimated_cost',
        'can_start',
        'can_complete',
        'can_cancel',
        'can_request_payment',
    ];
}
