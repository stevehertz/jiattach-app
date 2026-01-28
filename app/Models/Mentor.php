<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mentor extends Model
{
    use HasFactory, SoftDeletes, LogsModelActivity;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'job_title',
        'company',
        'years_of_experience',
        'areas_of_expertise',
        'industries',
        'linkedin_profile',
        'twitter_profile',
        'website',
        'bio',
        'mentoring_philosophy',
        'max_mentees',
        'current_mentees',
        'availability',
        'mentoring_focus',
        'is_verified',
        'is_featured',
        'hourly_rate',
        'offers_free_sessions',
        'free_sessions_per_month',
        'preferred_meeting_times',
        'meeting_preference',
        'session_duration_minutes',
        'languages',
        'certifications',
        'education_background',
        'total_sessions_conducted',
        'average_rating',
        'total_reviews',
        'successful_mentees',
        'verified_at',
        'featured_at',
    ];

    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'areas_of_expertise' => 'array',
        'industries' => 'array',
        'preferred_meeting_times' => 'array',
        'languages' => 'array',
        'certifications' => 'array',
        'education_background' => 'array',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'offers_free_sessions' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'max_mentees' => 'integer',
        'current_mentees' => 'integer',
        'years_of_experience' => 'integer',
        'free_sessions_per_month' => 'integer',
        'session_duration_minutes' => 'integer',
        'total_sessions_conducted' => 'integer',
        'average_rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'successful_mentees' => 'integer',
        'verified_at' => 'datetime',
        'featured_at' => 'datetime',
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
        'availability_label',
        'mentoring_focus_label',
        'meeting_preference_label',
        'experience_level',
        'is_available_for_new_mentees',
        'slots_available',
        'acceptance_rate',
        'hourly_rate_formatted',
        'is_free_mentor',
        'free_sessions_available',
        'full_title',
        'expertise_list',
        'industry_list',
        'language_list',
        'certification_list',
        'education_list',
        'rating_stars',
        'profile_completeness',
        'can_accept_mentee',
        'next_available_slot',
        'is_popular',
        'is_premium_mentor',
    ];

     /**
     * Get the user that owns the mentor profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


     /**
     * Get the mentorships for this mentor.
     */
    public function mentorships(): HasMany
    {
        return $this->hasMany(Mentorship::class, 'mentor_id');
    }

    /**
     * Get active mentorships.
     */
    public function activeMentorships(): HasMany
    {
        return $this->mentorships()->where('status', 'active');
    }

    /**
     * Get completed mentorships.
     */
    public function completedMentorships(): HasMany
    {
        return $this->mentorships()->where('status', 'completed');
    }

    /**
     * Get mentorship sessions.
     */
    public function sessions()
    {
        return $this->hasManyThrough(
            MentorshipSession::class,
            Mentorship::class,
            'mentor_id', // Foreign key on mentorships table
            'mentorship_id', // Foreign key on mentorship_sessions table
            'id', // Local key on mentors table
            'id' // Local key on mentorships table
        );
    }
    
   
    /**
     * Get availability label.
     */
    protected function availabilityLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'available' => 'Available',
                    'limited' => 'Limited Availability',
                    'fully_booked' => 'Fully Booked',
                    'unavailable' => 'Currently Unavailable',
                ];
                return $labels[$this->availability] ?? $this->availability;
            },
        );
    }

    /**
     * Get mentoring focus label.
     */
    protected function mentoringFocusLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'career_development' => 'Career Development',
                    'technical_skills' => 'Technical Skills',
                    'leadership' => 'Leadership',
                    'entrepreneurship' => 'Entrepreneurship',
                    'industry_specific' => 'Industry Specific',
                    'general' => 'General Mentoring',
                ];
                return $labels[$this->mentoring_focus] ?? $this->mentoring_focus;
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
     * Get experience level based on years of experience.
     */
    protected function experienceLevel(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->years_of_experience >= 15) return 'Expert';
                if ($this->years_of_experience >= 10) return 'Senior';
                if ($this->years_of_experience >= 5) return 'Mid-Level';
                if ($this->years_of_experience >= 2) return 'Junior';
                return 'Entry Level';
            },
        );
    }

    /**
     * Check if mentor is available for new mentees.
     */
    protected function isAvailableForNewMentees(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->availability === 'available' && $this->slots_available > 0,
        );
    }

    /**
     * Calculate available slots.
     */
    protected function slotsAvailable(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, $this->max_mentees - $this->current_mentees),
        );
    }

    /**
     * Calculate acceptance rate.
     */
    protected function acceptanceRate(): Attribute
    {
        return Attribute::make(
            get: function () {
                $totalRequests = $this->mentorships()->count();
                if ($totalRequests === 0) return 100;
                
                $accepted = $this->mentorships()->whereIn('status', ['active', 'completed'])->count();
                return round(($accepted / $totalRequests) * 100, 2);
            },
        );
    }

    /**
     * Get formatted hourly rate.
     */
    protected function hourlyRateFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->hourly_rate) {
                    return 'Free';
                }
                return 'KSh ' . number_format($this->hourly_rate, 2) . '/hour';
            },
        );
    }

    /**
     * Check if mentor offers free sessions.
     */
    protected function isFreeMentor(): Attribute
    {
        return Attribute::make(
            get: fn () => !$this->hourly_rate || $this->offers_free_sessions,
        );
    }

    /**
     * Get free sessions available this month.
     */
    protected function freeSessionsAvailable(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->offers_free_sessions ? $this->free_sessions_per_month : 0,
        );
    }

    /**
     * Get full title (job title at company).
     */
    protected function fullTitle(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->job_title} at {$this->company}",
        );
    }

    /**
     * Get expertise as comma-separated list.
     */
    protected function expertiseList(): Attribute
    {
        return Attribute::make(
            get: fn () => is_array($this->areas_of_expertise) ? implode(', ', $this->areas_of_expertise) : null,
        );
    }

    /**
     * Get industries as comma-separated list.
     */
    protected function industryList(): Attribute
    {
        return Attribute::make(
            get: fn () => is_array($this->industries) ? implode(', ', $this->industries) : null,
        );
    }

    /**
     * Get languages as comma-separated list.
     */
    protected function languageList(): Attribute
    {
        return Attribute::make(
            get: fn () => is_array($this->languages) ? implode(', ', $this->languages) : null,
        );
    }

    /**
     * Get certifications as comma-separated list.
     */
    protected function certificationList(): Attribute
    {
        return Attribute::make(
            get: fn () => is_array($this->certifications) ? implode(', ', $this->certifications) : null,
        );
    }

    /**
     * Get education as formatted list.
     */
    protected function educationList(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!is_array($this->education_background) || empty($this->education_background)) {
                    return null;
                }
                
                return array_map(function ($education) {
                    return "{$education['degree']} in {$education['field']} - {$education['institution']} ({$education['year']})";
                }, $this->education_background);
            },
        );
    }

    /**
     * Get rating stars (1-5).
     */
    protected function ratingStars(): Attribute
    {
        return Attribute::make(
            get: fn () => round($this->average_rating * 2) / 2, // Returns 0, 0.5, 1, 1.5, ..., 5
        );
    }

    /**
     * Calculate profile completeness percentage.
     */
    protected function profileCompleteness(): Attribute
    {
        return Attribute::make(
            get: function () {
                $requiredFields = [
                    'job_title',
                    'company',
                    'years_of_experience',
                    'areas_of_expertise',
                    'industries',
                    'bio',
                    'mentoring_philosophy',
                ];
                
                $optionalFields = [
                    'linkedin_profile',
                    'twitter_profile',
                    'website',
                    'hourly_rate',
                    'languages',
                    'certifications',
                    'education_background',
                ];
                
                $filledCount = 0;
                
                // Check required fields (weight: 70%)
                foreach ($requiredFields as $field) {
                    if (!empty($this->$field)) {
                        $filledCount += 1;
                    }
                }
                $requiredScore = ($filledCount / count($requiredFields)) * 70;
                
                // Check optional fields (weight: 30%)
                $filledCount = 0;
                foreach ($optionalFields as $field) {
                    if (!empty($this->$field)) {
                        $filledCount += 1;
                    }
                }
                $optionalScore = ($filledCount / count($optionalFields)) * 30;
                
                return (int) round($requiredScore + $optionalScore);
            },
        );
    }

    /**
     * Check if mentor can accept a new mentee.
     */
    protected function canAcceptMentee(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_available_for_new_mentees && $this->is_verified && $this->profile_completeness >= 80,
        );
    }

    /**
     * Get next available slot (simplified calculation).
     */
    protected function nextAvailableSlot(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->is_available_for_new_mentees) {
                    return null;
                }
                
                // Simple calculation: next available in 2-7 days
                return now()->addDays(rand(2, 7))->startOfHour();
            },
        );
    }

    /**
     * Check if mentor is popular.
     */
    protected function isPopular(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_reviews > 10 && $this->average_rating >= 4.5,
        );
    }

    /**
     * Check if mentor is premium (paid).
     */
    protected function isPremiumMentor(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->hourly_rate > 0 && $this->is_verified,
        );
    }

    /**
     * Scope a query to only include available mentors.
     */
    public function scopeAvailable($query)
    {
        return $query->where('availability', 'available')
                    ->whereRaw('current_mentees < max_mentees');
    }

    /**
     * Scope a query to only include verified mentors.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include featured mentors.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include free mentors.
     */
    public function scopeFree($query)
    {
        return $query->whereNull('hourly_rate')
                    ->orWhere('hourly_rate', 0)
                    ->orWhere('offers_free_sessions', true);
    }

    /**
     * Scope a query to only include mentors by expertise.
     */
    public function scopeByExpertise($query, $expertise)
    {
        return $query->whereJsonContains('areas_of_expertise', $expertise);
    }

    /**
     * Scope a query to only include mentors by industry.
     */
    public function scopeByIndustry($query, $industry)
    {
        return $query->whereJsonContains('industries', $industry);
    }

    /**
     * Scope a query to only include mentors by focus area.
     */
    public function scopeByFocus($query, $focus)
    {
        return $query->where('mentoring_focus', $focus);
    }

    /**
     * Scope a query to only include mentors with minimum rating.
     */
    public function scopeWithRatingAbove($query, $rating)
    {
        return $query->where('average_rating', '>=', $rating);
    }

    /**
     * Scope a query to only include mentors with minimum experience.
     */
    public function scopeWithExperienceAbove($query, $years)
    {
        return $query->where('years_of_experience', '>=', $years);
    }

    /**
     * Scope a query to only include mentors by meeting preference.
     */
    public function scopeByMeetingPreference($query, $preference)
    {
        return $query->where('meeting_preference', $preference);
    }

    /**
     * Verify the mentor.
     */
    public function verify(): bool
    {
        if ($this->is_verified) {
            throw new \Exception('Mentor is already verified.');
        }

        $this->is_verified = true;
        $this->verified_at = now();
        return $this->save();
    }

    /**
     * Unverify the mentor.
     */
    public function unverify(string $reason = null): bool
    {
        if (!$this->is_verified) {
            throw new \Exception('Mentor is not verified.');
        }

        $this->is_verified = false;
        $this->verified_at = null;
        return $this->save();
    }

    /**
     * Feature the mentor.
     */
    public function feature(): bool
    {
        if (!$this->is_verified) {
            throw new \Exception('Only verified mentors can be featured.');
        }

        $this->is_featured = true;
        $this->featured_at = now();
        return $this->save();
    }

    /**
     * Unfeature the mentor.
     */
    public function unfeature(): bool
    {
        if (!$this->is_featured) {
            throw new \Exception('Mentor is not featured.');
        }

        $this->is_featured = false;
        $this->featured_at = null;
        return $this->save();
    }

    /**
     * Add a new mentee.
     */
    public function addMentee(): bool
    {
        if ($this->current_mentees >= $this->max_mentees) {
            throw new \Exception('Mentor has reached maximum mentee capacity.');
        }

        $this->current_mentees++;
        
        // Update availability if slots are filled
        if ($this->current_mentees >= $this->max_mentees) {
            $this->availability = 'fully_booked';
        } elseif ($this->current_mentees >= $this->max_mentees * 0.8) {
            $this->availability = 'limited';
        }
        
        return $this->save();
    }

    /**
     * Remove a mentee.
     */
    public function removeMentee(): bool
    {
        if ($this->current_mentees <= 0) {
            throw new \Exception('Mentor has no mentees to remove.');
        }

        $this->current_mentees--;
        
        // Update availability
        if ($this->current_mentees < $this->max_mentees * 0.8) {
            $this->availability = 'available';
        }
        
        return $this->save();
    }

    /**
     * Update rating after a review.
     */
    public function updateRating(float $newRating): bool
    {
        $this->total_reviews++;
        $this->average_rating = (($this->average_rating * ($this->total_reviews - 1)) + $newRating) / $this->total_reviews;
        return $this->save();
    }

    /**
     * Increment successful mentees count.
     */
    public function incrementSuccessfulMentees(): bool
    {
        $this->successful_mentees++;
        return $this->save();
    }

    /**
     * Increment total sessions conducted.
     */
    public function incrementSessionsConducted(): bool
    {
        $this->total_sessions_conducted++;
        return $this->save();
    }

    /**
     * Add expertise area.
     */
    public function addExpertise(string $expertise): bool
    {
        $expertiseList = $this->areas_of_expertise ?? [];
        
        if (!in_array($expertise, $expertiseList)) {
            $expertiseList[] = $expertise;
            $this->areas_of_expertise = $expertiseList;
            return $this->save();
        }
        
        return true;
    }

    /**
     * Remove expertise area.
     */
    public function removeExpertise(string $expertise): bool
    {
        $expertiseList = $this->areas_of_expertise ?? [];
        
        if (($key = array_search($expertise, $expertiseList)) !== false) {
            unset($expertiseList[$key]);
            $this->areas_of_expertise = array_values($expertiseList);
            return $this->save();
        }
        
        return true;
    }

    /**
     * Add industry.
     */
    public function addIndustry(string $industry): bool
    {
        $industryList = $this->industries ?? [];
        
        if (!in_array($industry, $industryList)) {
            $industryList[] = $industry;
            $this->industries = $industryList;
            return $this->save();
        }
        
        return true;
    }

    /**
     * Remove industry.
     */
    public function removeIndustry(string $industry): bool
    {
        $industryList = $this->industries ?? [];
        
        if (($key = array_search($industry, $industryList)) !== false) {
            unset($industryList[$key]);
            $this->industries = array_values($industryList);
            return $this->save();
        }
        
        return true;
    }

    /**
     * Add certification.
     */
    public function addCertification(array $certification): bool
    {
        $certifications = $this->certifications ?? [];
        
        // Check if certification already exists
        $exists = false;
        foreach ($certifications as $cert) {
            if ($cert['name'] === $certification['name'] && $cert['issuer'] === $certification['issuer']) {
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            $certifications[] = $certification;
            $this->certifications = $certifications;
            return $this->save();
        }
        
        return true;
    }

    /**
     * Get mentor's monthly statistics.
     */
    public function getMonthlyStats(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // This would typically query the database
        // For now, returning sample data
        return [
            'sessions_conducted' => rand(5, 20),
            'new_mentees' => rand(0, 3),
            'hours_mentored' => rand(10, 40),
            'revenue' => $this->hourly_rate ? $this->hourly_rate * rand(10, 40) : 0,
            'average_session_rating' => rand(35, 50) / 10, // 3.5 to 5.0
        ];
    }

    /**
     * Get mentor's upcoming schedule.
     */
    public function getUpcomingSchedule($days = 7): array
    {
        // This would typically query mentorship sessions
        // For now, returning sample data
        $schedule = [];
        $startDate = now();
        
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $daySessions = rand(0, 3);
            
            if ($daySessions > 0) {
                $schedule[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('l'),
                    'sessions' => $daySessions,
                    'times' => ['9:00 AM', '2:00 PM', '4:00 PM'], // Sample times
                ];
            }
        }
        
        return $schedule;
    }

    /**
     * Check if mentor has specific expertise.
     */
    public function hasExpertise(string $expertise): bool
    {
        if (empty($this->areas_of_expertise) || !is_array($this->areas_of_expertise)) {
            return false;
        }
        
        return in_array(strtolower($expertise), array_map('strtolower', $this->areas_of_expertise));
    }

    /**
     * Check if mentor works in specific industry.
     */
    public function worksInIndustry(string $industry): bool
    {
        if (empty($this->industries) || !is_array($this->industries)) {
            return false;
        }
        
        return in_array(strtolower($industry), array_map('strtolower', $this->industries));
    }

    /**
     * Get mentor's success rate.
     */
    public function getSuccessRateAttribute(): float
    {
        $totalMentorships = $this->mentorships()->count();
        
        if ($totalMentorships === 0) {
            return 0;
        }
        
        $successful = $this->mentorships()->where('status', 'completed')->count();
        return ($successful / $totalMentorships) * 100;
    }

    /**
     * Get mentor's response time (average hours).
     */
    public function getAverageResponseTimeAttribute(): float
    {
        // This would typically calculate from mentorship requests
        // For now, returning sample data
        return rand(4, 48); // 4 to 48 hours
    }

    public function reviews()
    {
        return $this->hasManyThrough(
            MentorshipReview::class,
            Mentorship::class,
            'mentor_id', // Foreign key on mentorships table
            'mentorship_id', // Foreign key on mentorship_reviews table
            'id', // Local key on mentors table
            'id' // Local key on mentorships table
        )->where('review_type', 'mentee_to_mentor'); // Only mentee reviews of mentor
    }
    
    /**
     * Get published mentor reviews.
     */
    public function publishedReviews()
    {
        return $this->reviews()->where('status', 'published');
    }
    
    /**
     * Get featured mentor reviews.
     */
    public function featuredReviews()
    {
        return $this->publishedReviews()->where('is_featured', true);
    }
    
    /**
     * Get average rating from reviews.
     */
    public function getAverageReviewRatingAttribute(): ?float
    {
        $reviews = $this->publishedReviews()->get();
        
        if ($reviews->isEmpty()) {
            return null;
        }
        
        return $reviews->avg('overall_rating');
    }
    
    /**
     * Get review statistics.
     */
    public function getReviewStatsAttribute(): array
    {
        $reviews = $this->publishedReviews()->get();
        
        return [
            'total' => $reviews->count(),
            'average' => $reviews->avg('overall_rating'),
            'positive' => $reviews->where('overall_rating', '>=', 4.0)->count(),
            'neutral' => $reviews->whereBetween('overall_rating', [2.1, 3.9])->count(),
            'negative' => $reviews->where('overall_rating', '<=', 2.0)->count(),
            'featured' => $reviews->where('is_featured', true)->count(),
            'with_response' => $reviews->whereNotNull('response')->count(),
        ];
    }
}
