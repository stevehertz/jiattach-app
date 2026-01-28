<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MentorshipSession extends Model
{
    use HasFactory, LogsModelActivity;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mentorship_id',
        'title',
        'description',
        'session_type',
        'status',
        'scheduled_start_time',
        'scheduled_end_time',
        'actual_start_time',
        'actual_end_time',
        'duration_minutes',
        'meeting_type',
        'meeting_link',
        'meeting_location',
        'agenda',
        'topics_covered',
        'notes',
        'action_items',
        'homework_assigned',
        'mentor_feedback',
        'mentee_feedback',
        'mentor_rating',
        'mentee_rating',
        'mentor_rating_comment',
        'mentee_rating_comment',
        'is_paid',
        'session_cost',
        'payment_status',
        'payment_date',
        'payment_reference',
        'requires_follow_up',
        'follow_up_date',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'reschedule_count',
        'reminder_sent_at',
        'follow_up_reminder_sent_at',
        'attachments',
        'technical_issues',
        'was_recorded',
        'recording_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'topics_covered' => 'array',
        'attachments' => 'array',
        'scheduled_start_time' => 'datetime',
        'scheduled_end_time' => 'datetime',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'payment_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'follow_up_reminder_sent_at' => 'datetime',
        'follow_up_date' => 'datetime',
        'duration_minutes' => 'integer',
        'mentor_rating' => 'decimal:2',
        'mentee_rating' => 'decimal:2',
        'session_cost' => 'decimal:2',
        'is_paid' => 'boolean',
        'was_recorded' => 'boolean',
        'requires_follow_up' => 'boolean',
        'reschedule_count' => 'integer',
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
        'session_type_label',
        'status_label',
        'meeting_type_label',
        'payment_status_label',
        'cancelled_by_label',
        'is_upcoming',
        'is_ongoing',
        'is_completed',
        'is_cancelled',
        'is_missed',
        'duration_hours',
        'actual_duration_minutes',
        'actual_duration_hours',
        'start_delay_minutes',
        'end_delay_minutes',
        'was_on_time',
        'is_overdue',
        'is_past_due',
        'minutes_until_start',
        'minutes_until_end',
        'time_status',
        'can_start',
        'can_complete',
        'can_cancel',
        'can_reschedule',
        'can_rate',
        'has_mentor_rated',
        'has_mentee_rated',
        'average_rating',
        'requires_action',
        'next_action',
        'is_free_session',
        'cost_formatted',
        'recording_available',
        'attendance_status',
        'meeting_details',
        'calendar_event_data',
    ];

    /**
     * Get the mentorship that owns the session.
     */
    public function mentorship(): BelongsTo
    {
        return $this->belongsTo(Mentorship::class);
    }

    /**
     * Get the mentor through mentorship.
     */
    public function mentor()
    {
        return $this->hasOneThrough(
            Mentor::class,
            Mentorship::class,
            'id', // Foreign key on mentorships table
            'id', // Foreign key on mentors table
            'mentorship_id', // Local key on mentorship_sessions table
            'mentor_id' // Local key on mentorships table
        );
    }

    /**
     * Get the mentee through mentorship.
     */
    public function mentee()
    {
        return $this->hasOneThrough(
            User::class,
            Mentorship::class,
            'id', // Foreign key on mentorships table
            'id', // Foreign key on users table
            'mentorship_id', // Local key on mentorship_sessions table
            'mentee_id' // Local key on mentorships table
        );
    }

    /**
     * Get session type label.
     */
    protected function sessionTypeLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'initial' => 'Initial Session',
                    'regular' => 'Regular Session',
                    'milestone' => 'Milestone Session',
                    'feedback' => 'Feedback Session',
                    'emergency' => 'Emergency Session',
                    'wrap_up' => 'Wrap-up Session',
                ];
                return $labels[$this->session_type] ?? $this->session_type;
            },
        );
    }

    /**
     * Get status label.
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'scheduled' => 'Scheduled',
                    'confirmed' => 'Confirmed',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                    'missed' => 'Missed',
                    'rescheduled' => 'Rescheduled',
                ];
                return $labels[$this->status] ?? $this->status;
            },
        );
    }

    /**
     * Get meeting type label.
     */
    protected function meetingTypeLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'video' => 'Video Call',
                    'phone' => 'Phone Call',
                    'in_person' => 'In Person',
                    'hybrid' => 'Hybrid',
                ];
                return $labels[$this->meeting_type] ?? $this->meeting_type;
            },
        );
    }

    /**
     * Get payment status label.
     */
    protected function paymentStatusLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ];
                return $labels[$this->payment_status] ?? $this->payment_status;
            },
        );
    }

    /**
     * Get cancelled by label.
     */
    protected function cancelledByLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'mentor' => 'Mentor',
                    'mentee' => 'Mentee',
                    'system' => 'System',
                ];
                return $labels[$this->cancelled_by] ?? $this->cancelled_by;
            },
        );
    }

    /**
     * Check if session is upcoming.
     */
    protected function isUpcoming(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, ['scheduled', 'confirmed']) && $this->scheduled_start_time > now(),
        );
    }

    /**
     * Check if session is ongoing.
     */
    protected function isOngoing(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'in_progress',
        );
    }

    /**
     * Check if session is completed.
     */
    protected function isCompleted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'completed',
        );
    }

    /**
     * Check if session is cancelled.
     */
    protected function isCancelled(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'cancelled',
        );
    }

    /**
     * Check if session was missed.
     */
    protected function isMissed(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'missed',
        );
    }

    /**
     * Get duration in hours.
     */
    protected function durationHours(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->duration_minutes / 60,
        );
    }

    /**
     * Calculate actual duration in minutes.
     */
    protected function actualDurationMinutes(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->actual_start_time || !$this->actual_end_time) {
                    return null;
                }
                return $this->actual_start_time->diffInMinutes($this->actual_end_time);
            },
        );
    }

    /**
     * Calculate actual duration in hours.
     */
    protected function actualDurationHours(): Attribute
    {
        return Attribute::make(
            get: function () {
                $minutes = $this->actual_duration_minutes;
                return $minutes ? $minutes / 60 : null;
            },
        );
    }

    /**
     * Calculate start delay in minutes.
     */
    protected function startDelayMinutes(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->actual_start_time) {
                    return null;
                }
                return max(0, $this->actual_start_time->diffInMinutes($this->scheduled_start_time));
            },
        );
    }

    /**
     * Calculate end delay in minutes.
     */
    protected function endDelayMinutes(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->actual_end_time) {
                    return null;
                }
                $scheduledEnd = $this->scheduled_end_time;
                if (!$scheduledEnd) {
                    $scheduledEnd = $this->scheduled_start_time->copy()->addMinutes($this->duration_minutes);
                }
                return max(0, $this->actual_end_time->diffInMinutes($scheduledEnd));
            },
        );
    }

    /**
     * Check if session was on time.
     */
    protected function wasOnTime(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->start_delay_minutes !== null && $this->start_delay_minutes <= 5,
        );
    }

    /**
     * Check if session is overdue (past scheduled end time but not completed).
     */
    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_completed || $this->is_cancelled || $this->is_missed) {
                    return false;
                }

                $scheduledEnd = $this->scheduled_end_time ??
                    $this->scheduled_start_time->copy()->addMinutes($this->duration_minutes);

                return now() > $scheduledEnd;
            },
        );
    }

    /**
     * Check if session is past due (missed start time).
     */
    protected function isPastDue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->scheduled_start_time < now() && in_array($this->status, ['scheduled', 'confirmed']),
        );
    }

    /**
     * Calculate minutes until session starts.
     */
    protected function minutesUntilStart(): Attribute
    {
        return Attribute::make(
            get: fn () => now()->diffInMinutes($this->scheduled_start_time, false) * -1,
        );
    }

    /**
     * Calculate minutes until session ends.
     */
    protected function minutesUntilEnd(): Attribute
    {
        return Attribute::make(
            get: function () {
                $scheduledEnd = $this->scheduled_end_time ??
                    $this->scheduled_start_time->copy()->addMinutes($this->duration_minutes);
                return now()->diffInMinutes($scheduledEnd, false) * -1;
            },
        );
    }

     /**
     * Get time status.
     */
    protected function timeStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_completed) return 'completed';
                if ($this->is_cancelled) return 'cancelled';
                if ($this->is_missed) return 'missed';
                if ($this->is_ongoing) return 'in_progress';
                if ($this->is_overdue) return 'overdue';
                if ($this->is_past_due) return 'past_due';
                if ($this->minutes_until_start <= 60) return 'starting_soon';
                if ($this->minutes_until_start <= 1440) return 'upcoming_today';
                if ($this->minutes_until_start <= 10080) return 'upcoming_this_week';
                return 'future';
            },
        );
    }

    /**
     * Check if session can be started.
     */
    protected function canStart(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_upcoming && $this->minutes_until_start >= -15, // Allow 15 minutes early start
        );
    }

    /**
     * Check if session can be completed.
     */
    protected function canComplete(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'in_progress' || ($this->is_upcoming && $this->is_past_due),
        );
    }

    /**
     * Check if session can be cancelled.
     */
    protected function canCancel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_upcoming && $this->minutes_until_start > 60, // Can cancel up to 1 hour before
        );
    }

    /**
     * Check if session can be rescheduled.
     */
    protected function canReschedule(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_upcoming && $this->minutes_until_start > 1440, // Can reschedule up to 24 hours before
        );
    }

    /**
     * Check if session can be rated.
     */
    protected function canRate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_completed && (!$this->has_mentor_rated || !$this->has_mentee_rated),
        );
    }

    /**
     * Check if mentor has rated the session.
     */
    protected function hasMentorRated(): Attribute
    {
        return Attribute::make(
            get: fn () => !is_null($this->mentee_rating),
        );
    }

    /**
     * Check if mentee has rated the session.
     */
    protected function hasMenteeRated(): Attribute
    {
        return Attribute::make(
            get: fn () => !is_null($this->mentor_rating),
        );
    }

    /**
     * Calculate average rating.
     */
    protected function averageRating(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ratings = array_filter([$this->mentor_rating, $this->mentee_rating]);
                if (empty($ratings)) {
                    return null;
                }
                return array_sum($ratings) / count($ratings);
            },
        );
    }

    /**
     * Check if session requires action.
     */
    protected function requiresAction(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_past_due && !$this->is_ongoing) return true;
                if ($this->requires_follow_up && !$this->follow_up_date) return true;
                if ($this->follow_up_date && $this->follow_up_date < now()) return true;
                if ($this->is_paid && $this->payment_status === 'pending') return true;
                return false;
            },
        );
    }

    /**
     * Get next action required.
     */
    protected function nextAction(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_past_due && !$this->is_ongoing) return 'Start or cancel session';
                if ($this->requires_follow_up && !$this->follow_up_date) return 'Schedule follow-up';
                if ($this->follow_up_date && $this->follow_up_date < now()) return 'Complete follow-up';
                if ($this->is_paid && $this->payment_status === 'pending') return 'Process payment';
                if ($this->can_rate) return 'Rate session';
                return null;
            },
        );
    }

    /**
     * Check if session is free.
     */
    protected function isFreeSession(): Attribute
    {
        return Attribute::make(
            get: fn () => !$this->is_paid || $this->session_cost == 0,
        );
    }

    /**
     * Get formatted cost.
     */
    protected function costFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_free_session) {
                    return 'Free';
                }
                return 'KSh ' . number_format($this->session_cost, 2);
            },
        );
    }

    /**
     * Check if recording is available.
     */
    protected function recordingAvailable(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->was_recorded && !empty($this->recording_url),
        );
    }

    /**
     * Get attendance status.
     */
    protected function attendanceStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_cancelled) return 'cancelled';
                if ($this->is_missed) return 'missed';
                if ($this->is_completed) {
                    if ($this->start_delay_minutes > 15) return 'late';
                    if ($this->start_delay_minutes <= 5) return 'on_time';
                    return 'slightly_late';
                }
                return 'pending';
            },
        );
    }

    /**
     * Get meeting details.
     */
    protected function meetingDetails(): Attribute
    {
        return Attribute::make(
            get: function () {
                $details = [];

                if ($this->meeting_type === 'video' && $this->meeting_link) {
                    $details['link'] = $this->meeting_link;
                    $details['platform'] = $this->detectMeetingPlatform();
                } elseif ($this->meeting_type === 'in_person' && $this->meeting_location) {
                    $details['location'] = $this->meeting_location;
                    $details['address'] = $this->meeting_location;
                } elseif ($this->meeting_type === 'phone') {
                    $details['type'] = 'Phone Call';
                }

                $details['type_label'] = $this->meeting_type_label;
                return $details;
            },
        );
    }

    /**
     * Get calendar event data.
     */
    protected function calendarEventData(): Attribute
    {
        return Attribute::make(
            get: function () {
                return [
                    'title' => "Mentorship Session: {$this->title}",
                    'description' => $this->description ?? 'Mentorship session',
                    'start' => $this->scheduled_start_time->toIso8601String(),
                    'end' => $this->scheduled_end_time->toIso8601String(),
                    'location' => $this->meeting_location ?? $this->meeting_link ?? 'Online',
                    'status' => $this->status,
                    'color' => $this->getCalendarColor(),
                ];
            },
        );
    }

    /**
     * Scope a query to only include upcoming sessions.
     */
    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['scheduled', 'confirmed'])
                    ->where('scheduled_start_time', '>', now());
    }

    /**
     * Scope a query to only include past sessions.
     */
    public function scopePast($query)
    {
        return $query->where('scheduled_start_time', '<', now());
    }

    /**
     * Scope a query to only include completed sessions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include cancelled sessions.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include missed sessions.
     */
    public function scopeMissed($query)
    {
        return $query->where('status', 'missed');
    }

    /**
     * Scope a query to only include sessions requiring follow-up.
     */
    public function scopeRequiringFollowUp($query)
    {
        return $query->where('requires_follow_up', true)
                    ->where(function ($q) {
                        $q->whereNull('follow_up_date')
                          ->orWhere('follow_up_date', '<', now());
                    });
    }

    /**
     * Scope a query to only include sessions with pending payment.
     */
    public function scopePendingPayment($query)
    {
        return $query->where('is_paid', true)
                    ->where('payment_status', 'pending');
    }

    /**
     * Scope a query to only include sessions for a specific mentorship.
     */
    public function scopeForMentorship($query, $mentorshipId)
    {
        return $query->where('mentorship_id', $mentorshipId);
    }

}
