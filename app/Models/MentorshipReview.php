<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MentorshipReview extends Model
{
     //
    use HasFactory, LogsModelActivity;
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mentorship_id',
        'mentorship_session_id',
        'reviewer_id',
        'reviewee_id',
        'review_type',
        'overall_rating',
        'overall_comment',
        'knowledge_rating',
        'communication_rating',
        'preparation_rating',
        'engagement_rating',
        'value_rating',
        'professionalism_rating',
        'recommendation_rating',
        'strengths',
        'areas_for_improvement',
        'key_takeaways',
        'suggestions',
        'goals_achieved',
        'goals_comment',
        'achieved_goals',
        'pending_goals',
        'is_session_review',
        'session_specific_feedback',
        'session_met_expectations',
        'is_anonymous',
        'is_public',
        'is_featured',
        'allow_response',
        'status',
        'response',
        'responded_at',
        'flag_reason',
        'flagged_by',
        'flagged_at',
        'helpful_count',
        'not_helpful_count',
        'helpful_users',
        'not_helpful_users',
        'is_verified',
        'verified_by',
        'verified_at',
        'requires_moderation',
        'moderation_notes',
        'submitted_at',
        'published_at',
        'edited_at',
        'edit_count',
        'relationship_status_at_review',
        'weeks_into_mentorship',
        'sessions_completed_at_review',
        'tags',
        'reviewer_role',
        'reviewee_role',
        'additional_context',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'overall_rating' => 'decimal:2',
        'knowledge_rating' => 'decimal:2',
        'communication_rating' => 'decimal:2',
        'preparation_rating' => 'decimal:2',
        'engagement_rating' => 'decimal:2',
        'value_rating' => 'decimal:2',
        'professionalism_rating' => 'decimal:2',
        'recommendation_rating' => 'decimal:2',
        'achieved_goals' => 'array',
        'pending_goals' => 'array',
        'helpful_users' => 'array',
        'not_helpful_users' => 'array',
        'tags' => 'array',
        'goals_achieved' => 'boolean',
        'is_session_review' => 'boolean',
        'session_met_expectations' => 'boolean',
        'is_anonymous' => 'boolean',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'allow_response' => 'boolean',
        'is_verified' => 'boolean',
        'requires_moderation' => 'boolean',
        'submitted_at' => 'datetime',
        'published_at' => 'datetime',
        'edited_at' => 'datetime',
        'flagged_at' => 'datetime',
        'verified_at' => 'datetime',
        'responded_at' => 'datetime',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'edit_count' => 'integer',
        'weeks_into_mentorship' => 'integer',
        'sessions_completed_at_review' => 'integer',
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
        'review_type_label',
        'status_label',
        'relationship_status_label',
        'is_submitted',
        'is_published',
        'is_flagged',
        'is_draft',
        'is_hidden',
        'is_archived',
        'has_response',
        'is_helpful',
        'helpfulness_score',
        'average_category_rating',
        'is_positive_review',
        'is_negative_review',
        'is_neutral_review',
        'rating_stars',
        'days_since_submission',
        'can_edit',
        'can_delete',
        'can_publish',
        'can_flag',
        'can_respond',
        'reviewer_name',
        'reviewee_name',
        'review_summary',
        'detailed_ratings',
        'rating_percentages',
    ];

    /**
     * Get the mentorship that owns the review.
     */
    public function mentorship(): BelongsTo
    {
        return $this->belongsTo(Mentorship::class);
    }

    
    /**
     * Get the session being reviewed (if applicable).
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(MentorshipSession::class, 'mentorship_session_id');
    }

    /**
     * Get the reviewer (user).
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get the reviewee (user).
     */
    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    /**
     * Get the user who flagged the review.
     */
    public function flaggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }

    /**
     * Get the user who verified the review.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the reviewer's profile based on review type.
     */
    public function reviewerProfile()
    {
        if ($this->review_type === 'mentor_to_mentee') {
            return $this->hasOneThrough(
                Mentor::class,
                User::class,
                'id',
                'user_id',
                'reviewer_id',
                'id'
            );
        } else {
            return $this->hasOneThrough(
                StudentProfile::class,
                User::class,
                'id',
                'user_id',
                'reviewer_id',
                'id'
            );
        }
    }

    /**
     * Get the reviewee's profile based on review type.
     */
    public function revieweeProfile()
    {
        if ($this->review_type === 'mentee_to_mentor') {
            return $this->hasOneThrough(
                Mentor::class,
                User::class,
                'id',
                'user_id',
                'reviewee_id',
                'id'
            );
        } else {
            return $this->hasOneThrough(
                StudentProfile::class,
                User::class,
                'id',
                'user_id',
                'reviewee_id',
                'id'
            );
        }
    }

    /**
     * Get review type label.
     */
    protected function reviewTypeLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'mentor_to_mentee' => 'Mentor Reviewing Mentee',
                    'mentee_to_mentor' => 'Mentee Reviewing Mentor',
                    'mutual' => 'Mutual Review',
                    'system' => 'System Review',
                ];
                return $labels[$this->review_type] ?? $this->review_type;
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
                    'draft' => 'Draft',
                    'submitted' => 'Submitted',
                    'published' => 'Published',
                    'flagged' => 'Flagged',
                    'hidden' => 'Hidden',
                    'archived' => 'Archived',
                ];
                return $labels[$this->status] ?? $this->status;
            },
        );
    }

    /**
     * Get relationship status label.
     */
    protected function relationshipStatusLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'active' => 'Active',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                    'paused' => 'Paused',
                ];
                return $labels[$this->relationship_status_at_review] ?? $this->relationship_status_at_review;
            },
        );
    }

    /**
     * Check if review is submitted.
     */
    protected function isSubmitted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'submitted',
        );
    }

    /**
     * Check if review is published.
     */
    protected function isPublished(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'published',
        );
    }

    /**
     * Check if review is flagged.
     */
    protected function isFlagged(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'flagged',
        );
    }

    /**
     * Check if review is draft.
     */
    protected function isDraft(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'draft',
        );
    }

    /**
     * Check if review is hidden.
     */
    protected function isHidden(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'hidden',
        );
    }

    /**
     * Check if review is archived.
     */
    protected function isArchived(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 'archived',
        );
    }

    /**
     * Check if review has a response.
     */
    protected function hasResponse(): Attribute
    {
        return Attribute::make(
            get: fn () => !empty($this->response) && $this->responded_at !== null,
        );
    }

    /**
     * Check if review is helpful (more helpful than not helpful).
     */
    protected function isHelpful(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->helpful_count > $this->not_helpful_count,
        );
    }

    /**
     * Calculate helpfulness score.
     */
    protected function helpfulnessScore(): Attribute
    {
        return Attribute::make(
            get: function () {
                $total = $this->helpful_count + $this->not_helpful_count;
                if ($total === 0) return 0;
                return ($this->helpful_count / $total) * 100;
            },
        );
    }

    /**
     * Calculate average of all category ratings.
     */
    protected function averageCategoryRating(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ratings = array_filter([
                    $this->knowledge_rating,
                    $this->communication_rating,
                    $this->preparation_rating,
                    $this->engagement_rating,
                    $this->value_rating,
                    $this->professionalism_rating,
                    $this->recommendation_rating,
                ]);
                
                if (empty($ratings)) {
                    return $this->overall_rating;
                }
                
                return array_sum($ratings) / count($ratings);
            },
        );
    }

    /**
     * Check if review is positive (rating >= 4).
     */
    protected function isPositiveReview(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->overall_rating >= 4.0,
        );
    }

    /**
     * Check if review is negative (rating <= 2).
     */
    protected function isNegativeReview(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->overall_rating <= 2.0,
        );
    }

    /**
     * Check if review is neutral (rating between 2 and 4).
     */
    protected function isNeutralReview(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->overall_rating > 2.0 && $this->overall_rating < 4.0,
        );
    }

    /**
     * Get rating stars (1-5 with half stars).
     */
    protected function ratingStars(): Attribute
    {
        return Attribute::make(
            get: fn () => round($this->overall_rating * 2) / 2,
        );
    }

    /**
     * Calculate days since submission.
     */
    protected function daysSinceSubmission(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->submitted_at ? $this->submitted_at->diffInDays() : null,
        );
    }

    /**
     * Check if review can be edited.
     */
    protected function canEdit(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_draft || ($this->is_submitted && $this->edit_count < 3),
        );
    }

    /**
     * Check if review can be deleted.
     */
    protected function canDelete(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_draft || $this->is_submitted,
        );
    }

    /**
     * Check if review can be published.
     */
    protected function canPublish(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_submitted && !$this->requires_moderation && $this->overall_rating > 0,
        );
    }

    /**
     * Check if review can be flagged.
     */
    protected function canFlag(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_published && !$this->is_flagged && !$this->is_anonymous,
        );
    }

    /**
     * Check if reviewee can respond.
     */
    protected function canRespond(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_published && $this->allow_response && !$this->has_response,
        );
    }

    /**
     * Get reviewer's display name (anonymous or real).
     */
    protected function reviewerName(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_anonymous) {
                    return 'Anonymous Reviewer';
                }
                return $this->reviewer->full_name ?? 'Unknown';
            },
        );
    }

    /**
     * Get reviewee's display name.
     */
    protected function revieweeName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->reviewee->full_name ?? 'Unknown',
        );
    }

    /**
     * Get review summary.
     */
    protected function reviewSummary(): Attribute
    {
        return Attribute::make(
            get: function () {
                $summary = $this->overall_comment;
                
                if (strlen($summary) > 200) {
                    $summary = substr($summary, 0, 197) . '...';
                }
                
                return $summary;
            },
        );
    }

    /**
     * Get detailed ratings as array.
     */
    protected function detailedRatings(): Attribute
    {
        return Attribute::make(
            get: function () {
                return [
                    'knowledge' => [
                        'rating' => $this->knowledge_rating,
                        'label' => 'Knowledge & Expertise',
                        'percent' => $this->knowledge_rating ? ($this->knowledge_rating / 5) * 100 : 0,
                    ],
                    'communication' => [
                        'rating' => $this->communication_rating,
                        'label' => 'Communication Skills',
                        'percent' => $this->communication_rating ? ($this->communication_rating / 5) * 100 : 0,
                    ],
                    'preparation' => [
                        'rating' => $this->preparation_rating,
                        'label' => 'Preparation',
                        'percent' => $this->preparation_rating ? ($this->preparation_rating / 5) * 100 : 0,
                    ],
                    'engagement' => [
                        'rating' => $this->engagement_rating,
                        'label' => 'Engagement & Support',
                        'percent' => $this->engagement_rating ? ($this->engagement_rating / 5) * 100 : 0,
                    ],
                    'value' => [
                        'rating' => $this->value_rating,
                        'label' => 'Value Provided',
                        'percent' => $this->value_rating ? ($this->value_rating / 5) * 100 : 0,
                    ],
                    'professionalism' => [
                        'rating' => $this->professionalism_rating,
                        'label' => 'Professionalism',
                        'percent' => $this->professionalism_rating ? ($this->professionalism_rating / 5) * 100 : 0,
                    ],
                    'recommendation' => [
                        'rating' => $this->recommendation_rating,
                        'label' => 'Would Recommend',
                        'percent' => $this->recommendation_rating ? ($this->recommendation_rating / 5) * 100 : 0,
                    ],
                ];
            },
        );
    }

    /**
     * Get rating percentages for display.
     */
    protected function ratingPercentages(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ratings = $this->detailed_ratings;
                $percentages = [];
                
                foreach ($ratings as $key => $rating) {
                    if ($rating['rating']) {
                        $percentages[$key] = $rating['percent'];
                    }
                }
                
                return $percentages;
            },
        );
    }

    /**
     * Scope a query to only include published reviews.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include public reviews.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true)->where('status', 'published');
    }

    /**
     * Scope a query to only include featured reviews.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('status', 'published');
    }

    /**
     * Scope a query to only include verified reviews.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include mentor reviews.
     */
    public function scopeMentorReviews($query)
    {
        return $query->where('review_type', 'mentee_to_mentor');
    }

    /**
     * Scope a query to only include mentee reviews.
     */
    public function scopeMenteeReviews($query)
    {
        return $query->where('review_type', 'mentor_to_mentee');
    }

    /**
     * Scope a query to only include reviews with minimum rating.
     */
    public function scopeWithRatingAbove($query, $rating)
    {
        return $query->where('overall_rating', '>=', $rating);
    }

    /**
     * Scope a query to only include reviews for a specific mentorship.
     */
    public function scopeForMentorship($query, $mentorshipId)
    {
        return $query->where('mentorship_id', $mentorshipId);
    }

    /**
     * Scope a query to only include reviews by a specific reviewer.
     */
    public function scopeByReviewer($query, $reviewerId)
    {
        return $query->where('reviewer_id', $reviewerId);
    }

    /**
     * Scope a query to only include reviews for a specific reviewee.
     */
    public function scopeForReviewee($query, $revieweeId)
    {
        return $query->where('reviewee_id', $revieweeId);
    }

    /**
     * Scope a query to only include session reviews.
     */
    public function scopeSessionReviews($query)
    {
        return $query->where('is_session_review', true);
    }

    /**
     * Scope a query to only include positive reviews.
     */
    public function scopePositive($query)
    {
        return $query->where('overall_rating', '>=', 4.0);
    }

    /**
     * Scope a query to only include negative reviews.
     */
    public function scopeNegative($query)
    {
        return $query->where('overall_rating', '<=', 2.0);
    }

    /**
     * Submit the review.
     */
    public function submit(): bool
    {
        if ($this->status !== 'draft') {
            throw new \Exception('Review has already been submitted.');
        }

        $this->status = 'submitted';
        $this->submitted_at = now();
        
        // Auto-publish if no moderation required
        if (!$this->requires_moderation) {
            $this->publish();
        }
        
        return $this->save();
    }

    /**
     * Publish the review.
     */
    public function publish(): bool
    {
        if ($this->status !== 'submitted') {
            throw new \Exception('Only submitted reviews can be published.');
        }

        $this->status = 'published';
        $this->published_at = now();
        
        // Update mentor/mentee ratings if applicable
        $this->updateRevieweeRating();
        
        return $this->save();
    }

    /**
     * Flag the review.
     */
    public function flag(string $reason, User $flaggedBy): bool
    {
        if (!$this->can_flag) {
            throw new \Exception('Review cannot be flagged.');
        }

        $this->status = 'flagged';
        $this->flag_reason = $reason;
        $this->flagged_by = $flaggedBy->id;
        $this->flagged_at = now();
        
        return $this->save();
    }

    /**
     * Unflag the review.
     */
    public function unflag(): bool
    {
        if ($this->status !== 'flagged') {
            throw new \Exception('Review is not flagged.');
        }

        $this->status = 'published';
        $this->flag_reason = null;
        $this->flagged_by = null;
        $this->flagged_at = null;
        
        return $this->save();
    }

    /**
     * Hide the review.
     */
    public function hide(string $reason = null): bool
    {
        $this->status = 'hidden';
        if ($reason) {
            $this->flag_reason = $reason;
        }
        
        return $this->save();
    }

    /**
     * Archive the review.
     */
    public function archive(): bool
    {
        $this->status = 'archived';
        return $this->save();
    }

    /**
     * Add a response from the reviewee.
     */
    public function addResponse(string $response): bool
    {
        if (!$this->can_respond) {
            throw new \Exception('Cannot add response to this review.');
        }

        $this->response = $response;
        $this->responded_at = now();
        
        return $this->save();
    }

    /**
     * Mark review as helpful by a user.
     */
    public function markHelpful(User $user): bool
    {
        $helpfulUsers = $this->helpful_users ?? [];
        $notHelpfulUsers = $this->not_helpful_users ?? [];
        
        // Check if user already marked not helpful and remove
        if (($key = array_search($user->id, $notHelpfulUsers)) !== false) {
            unset($notHelpfulUsers[$key]);
            $this->not_helpful_users = array_values($notHelpfulUsers);
            $this->not_helpful_count = max(0, $this->not_helpful_count - 1);
        }
        
        // Add to helpful if not already
        if (!in_array($user->id, $helpfulUsers)) {
            $helpfulUsers[] = $user->id;
            $this->helpful_users = $helpfulUsers;
            $this->helpful_count++;
        }
        
        return $this->save();
    }

    /**
     * Mark review as not helpful by a user.
     */
    public function markNotHelpful(User $user): bool
    {
        $helpfulUsers = $this->helpful_users ?? [];
        $notHelpfulUsers = $this->not_helpful_users ?? [];
        
        // Check if user already marked helpful and remove
        if (($key = array_search($user->id, $helpfulUsers)) !== false) {
            unset($helpfulUsers[$key]);
            $this->helpful_users = array_values($helpfulUsers);
            $this->helpful_count = max(0, $this->helpful_count - 1);
        }
        
        // Add to not helpful if not already
        if (!in_array($user->id, $notHelpfulUsers)) {
            $notHelpfulUsers[] = $user->id;
            $this->not_helpful_users = $notHelpfulUsers;
            $this->not_helpful_count++;
        }
        
        return $this->save();
    }

    /**
     * Verify the review.
     */
    public function verify(User $verifiedBy): bool
    {
        if ($this->is_verified) {
            throw new \Exception('Review is already verified.');
        }

        $this->is_verified = true;
        $this->verified_by = $verifiedBy->id;
        $this->verified_at = now();
        
        return $this->save();
    }

    /**
     * Feature the review.
     */
    public function feature(): bool
    {
        if (!$this->is_published) {
            throw new \Exception('Only published reviews can be featured.');
        }

        $this->is_featured = true;
        return $this->save();
    }

    /**
     * Unfeature the review.
     */
    public function unfeature(): bool
    {
        $this->is_featured = false;
        return $this->save();
    }

    /**
     * Edit the review.
     */
    public function edit(array $data): bool
    {
        if (!$this->can_edit) {
            throw new \Exception('Review cannot be edited.');
        }

        $this->fill($data);
        $this->edited_at = now();
        $this->edit_count++;
        
        return $this->save();
    }

    /**
     * Update reviewee's rating based on this review.
     */
    private function updateRevieweeRating(): void
    {
        if (!$this->is_published || $this->is_session_review) {
            return;
        }

        // Update mentor rating if this is a mentee reviewing mentor
        if ($this->review_type === 'mentee_to_mentor' && $this->reviewee) {
            $mentor = $this->reviewee->mentor;
            if ($mentor) {
                $mentor->updateRating($this->overall_rating);
            }
        }
        
        // Update mentee rating if this is a mentor reviewing mentee
        // (You might want to track mentee ratings differently)
    }

    /**
     * Get review sentiment analysis.
     */
    public function getSentimentAttribute(): string
    {
        if ($this->overall_rating >= 4.0) return 'positive';
        if ($this->overall_rating <= 2.0) return 'negative';
        return 'neutral';
    }

    /**
     * Get review readability score (simplified).
     */
    public function getReadabilityScoreAttribute(): float
    {
        $text = $this->overall_comment ?? '';
        
        if (empty($text)) {
            return 0;
        }
        
        // Simple readability calculation based on word and sentence count
        $words = str_word_count($text);
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $sentenceCount = count($sentences);
        
        if ($sentenceCount === 0) {
            return 0;
        }
        
        $averageWordsPerSentence = $words / $sentenceCount;
        
        // Simple scoring: lower words per sentence = better readability
        $score = 100 - min(($averageWordsPerSentence - 10) * 5, 100);
        
        return max(0, min(100, $score));
    }

    /**
     * Get review impact score.
     */
    public function getImpactScoreAttribute(): float
    {
        $score = 0;
        
        // Base score from rating
        $score += $this->overall_rating * 10;
        
        // Bonus for detailed feedback
        if (!empty($this->strengths)) $score += 5;
        if (!empty($this->areas_for_improvement)) $score += 5;
        if (!empty($this->suggestions)) $score += 5;
        
        // Bonus for response
        if ($this->has_response) $score += 10;
        
        // Bonus for helpfulness
        $score += min($this->helpful_count * 2, 20);
        
        // Penalty for negative rating
        if ($this->is_negative_review) $score -= 10;
        
        return max(0, $score);
    }

    /**
     * Get formatted goals achieved.
     */
    public function getFormattedAchievedGoalsAttribute(): array
    {
        $goals = $this->achieved_goals ?? [];
        $formatted = [];
        
        foreach ($goals as $goal) {
            if (is_string($goal)) {
                $formatted[] = ['description' => $goal, 'achieved' => true];
            } elseif (is_array($goal)) {
                $formatted[] = array_merge($goal, ['achieved' => true]);
            }
        }
        
        return $formatted;
    }

     /**
     * Generate review preview for display.
     */
    public function getPreviewAttribute(): array
    {
        return [
            'id' => $this->id,
            'reviewer_name' => $this->reviewer_name,
            'reviewee_name' => $this->reviewee_name,
            'rating' => $this->overall_rating,
            'rating_stars' => $this->rating_stars,
            'summary' => $this->review_summary,
            'date' => $this->published_at ? $this->published_at->format('M d, Y') : null,
            'is_verified' => $this->is_verified,
            'is_featured' => $this->is_featured,
            'helpful_count' => $this->helpful_count,
            'has_response' => $this->has_response,
            'sentiment' => $this->sentiment,
        ];
    }
}
