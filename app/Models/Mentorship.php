<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

     /**
     * Get the mentor (user).
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Get the mentee (user).
     */
    public function mentee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    /**
     * Get the mentor's profile.
     */
    public function mentorProfile()
    {
        return $this->hasOneThrough(
            Mentor::class,
            User::class,
            'id',
            'user_id',
            'mentor_id',
            'id'
        );
    }

      /**
     * Get the mentee's profile.
     */
    public function menteeProfile()
    {
        return $this->hasOneThrough(
            StudentProfile::class,
            User::class,
            'id',
            'user_id',
            'mentee_id',
            'id'
        );
    }

    /**
     * Get mentorship sessions.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(MentorshipSession::class);
    }

    /**
     * Get mentorship reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(MentorshipReview::class);
    }

    /**
     * Get status label.
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'requested' => 'Requested',
                    'pending_approval' => 'Pending Approval',
                    'active' => 'Active',
                    'paused' => 'Paused',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                    'rejected' => 'Rejected',
                ];
                return $labels[$this->status] ?? $this->status;
            },
        );
    }

    /**
     * Get meeting preference label.
     */
    protected function meetingPreferenceLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'video' => 'Video Call',
                    'phone' => 'Phone Call',
                    'in_person' => 'In Person',
                    'hybrid' => 'Hybrid',
                ];
                return $labels[$this->meeting_preference] ?? $this->meeting_preference;
            },
        );
    }

    /**
     * Get experience level label.
     */
    protected function experienceLevelLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'entry' => 'Entry Level',
                    'junior' => 'Junior',
                    'mid' => 'Mid Level',
                    'senior' => 'Senior',
                    'executive' => 'Executive',
                ];
                return $labels[$this->experience_level] ?? $this->experience_level;
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
                    'overdue' => 'Overdue',
                    'cancelled' => 'Cancelled',
                ];
                return $labels[$this->payment_status] ?? $this->payment_status;
            },
        );
    }

    /**
     * Check if mentorship is active.
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'active',
        );
    }

    /**
     * Check if mentorship is pending.
     */
    protected function isPending(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, ['requested', 'pending_approval']),
        );
    }

    /**
     * Check if mentorship is completed.
     */
    protected function isCompleted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'completed',
        );
    }

    /**
     * Check if mentorship is cancelled.
     */
    protected function isCancelled(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, ['cancelled', 'rejected']),
        );
    }

    /**
     * Calculate total meetings.
     */
    protected function totalMeetings(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->meetings_per_month * ($this->duration_weeks / 4),
        );
    }

    /**
     * Get completed meetings count.
     */
    protected function meetingsCompleted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->sessions()->where('status', 'completed')->count(),
        );
    }

    /**
     * Get remaining meetings count.
     */
    protected function meetingsRemaining(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, $this->total_meetings - $this->meetings_completed),
        );
    }

    /**
     * Calculate progress percentage.
     */
    protected function progressPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->total_meetings <= 0) {
                    return 0;
                }
                return min(100, ($this->meetings_completed / $this->total_meetings) * 100);
            },
        );
    }

    /**
     * Calculate weeks remaining.
     */
    protected function weeksRemaining(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->start_date || !$this->end_date) {
                    return $this->duration_weeks;
                }
                
                $remaining = now()->diffInWeeks($this->end_date, false);
                return max(0, $remaining);
            },
        );
    }

    /**
     * Get duration in months.
     */
    protected function durationMonths(): Attribute
    {
        return Attribute::make(
            get: fn () => ceil($this->duration_weeks / 4),
        );
    }

    /**
     * Calculate estimated cost.
     */
    protected function estimatedCost(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->hourly_rate) {
                    return 0;
                }
                
                $totalHours = ($this->meeting_duration_minutes / 60) * $this->total_meetings;
                return $totalHours * $this->hourly_rate;
            },
        );
    }

    /**
     * Check if mentorship can be started.
     */
    protected function canStart(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'pending_approval',
        );
    }

    /**
     * Check if mentorship can be completed.
     */
    protected function canComplete(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'active' && $this->progress_percentage >= 70,
        );
    }

    /**
     * Check if mentorship can be cancelled.
     */
    protected function canCancel(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, ['requested', 'pending_approval', 'active', 'paused']),
        );
    }

    /**
     * Check if payment can be requested.
     */
    protected function canRequestPayment(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_paid && $this->payment_status !== 'paid' && $this->meetings_completed > 0,
        );
    }

    /**
     * Scope a query to only include active mentorships.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include pending mentorships.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['requested', 'pending_approval']);
    }

    /**
     * Scope a query to only include completed mentorships.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include cancelled mentorships.
     */
    public function scopeCancelled($query)
    {
        return $query->whereIn('status', ['cancelled', 'rejected']);
    }

    /**
     * Scope a query to only include paid mentorships.
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * Scope a query to only include free mentorships.
     */
    public function scopeFree($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * Scope a query to only include mentorships for a specific mentor.
     */
    public function scopeForMentor($query, $mentorId)
    {
        return $query->where('mentor_id', $mentorId);
    }

    /**
     * Scope a query to only include mentorships for a specific mentee.
     */
    public function scopeForMentee($query, $menteeId)
    {
        return $query->where('mentee_id', $menteeId);
    }

    /**
     * Approve the mentorship request.
     */
    public function approve(): bool
    {
        if ($this->status !== 'requested' && $this->status !== 'pending_approval') {
            throw new \Exception('Mentorship cannot be approved at this stage.');
        }

        $this->status = 'pending_approval';
        $this->approved_at = now();
        return $this->save();
    }

    /**
     * Get the mentorship reviews.
     */
    // public function reviews(): HasMany
    // {
    //     return $this->hasMany(MentorshipReview::class);
    // }
    
    /**
     * Get mentor reviews (mentee reviewing mentor).
     */
    public function mentorReviews(): HasMany
    {
        return $this->reviews()->where('review_type', 'mentee_to_mentor');
    }
    
    /**
     * Get mentee reviews (mentor reviewing mentee).
     */
    public function menteeReviews(): HasMany
    {
        return $this->reviews()->where('review_type', 'mentor_to_mentee');
    }
    
    /**
     * Get published reviews.
     */
    public function publishedReviews(): HasMany
    {
        return $this->reviews()->where('status', 'published');
    }
    
    /**
     * Get average mentor rating.
     */
    public function getAverageMentorRatingAttribute(): ?float
    {
        $reviews = $this->mentorReviews()->where('status', 'published')->get();
        
        if ($reviews->isEmpty()) {
            return null;
        }
        
        return $reviews->avg('overall_rating');
    }
    
    /**
     * Get average mentee rating.
     */
    public function getAverageMenteeRatingAttribute(): ?float
    {
        $reviews = $this->menteeReviews()->where('status', 'published')->get();
        
        if ($reviews->isEmpty()) {
            return null;
        }
        
        return $reviews->avg('overall_rating');
    }
}
