<?php

namespace App\Models;

use App\Enums\InterviewOutcomeEnum;
use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class InterviewOutcome extends Model
{
    use HasFactory, SoftDeletes, LogsModelActivity;

    protected $fillable = [
        'interview_id',
        'application_id',
        'student_id',
        'organization_id',
        'recorded_by',
        'outcome',
        'rating',
        'feedback',
        'notes',
        'strengths',
        'areas_for_improvement',
        'skills_assessment',
        'decision_reason',
        'decision_date',
        'next_steps',
        'follow_up_date',
        'follow_up_required',
        'metadata',
    ];

    protected $casts = [
        'strengths' => 'array',
        'areas_for_improvement' => 'array',
        'skills_assessment' => 'array',
        'metadata' => 'array',
        'decision_date' => 'datetime',
        'follow_up_date' => 'date',
        'follow_up_required' => 'boolean',
        'rating' => 'integer',
    ];

      /**
     * Get the interview this outcome belongs to.
     */
    public function interview()
    {
        return $this->belongsTo(Interview::class);
    }

    /**
     * Get the application this outcome belongs to.
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the student this outcome is for.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the organization this outcome is for.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user who recorded this outcome.
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get the outcome enum instance.
     */
    public function getOutcomeEnumAttribute(): ?InterviewOutcomeEnum
    {
        return InterviewOutcomeEnum::tryFrom($this->outcome);
    }

    /**
     * Get the outcome badge HTML.
     */
    public function getOutcomeBadgeAttribute(): string
    {
        $enum = $this->outcome_enum;
        if (!$enum) {
            return '<span class="badge badge-secondary">Unknown</span>';
        }
        
        return '<span class="badge badge-' . $enum->color() . ' p-2">
                    <i class="fas ' . $enum->icon() . ' mr-1"></i>
                    ' . $enum->label() . '
                </span>';
    }

    /**
     * Get the formatted rating stars.
     */
    public function getRatingStarsAttribute(): string
    {
        if (!$this->rating) {
            return 'N/A';
        }
        
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<i class="fas fa-star text-warning"></i>';
            } else {
                $stars .= '<i class="far fa-star text-warning"></i>';
            }
        }
        
        return $stars;
    }

    /**
     * Get the skills assessment summary.
     */
    public function getSkillsAssessmentSummaryAttribute(): array
    {
        if (!$this->skills_assessment) {
            return [];
        }
        
        $summary = [];
        foreach ($this->skills_assessment as $skill => $rating) {
            $summary[] = [
                'skill' => $skill,
                'rating' => $rating,
                'stars' => $this->generateStars($rating),
            ];
        }
        
        return $summary;
    }

    private function generateStars($rating)
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars .= '<i class="fas fa-star text-warning" style="font-size: 12px;"></i>';
            } else {
                $stars .= '<i class="far fa-star text-warning" style="font-size: 12px;"></i>';
            }
        }
        return $stars;
    }

    /**
     * Scope to get positive outcomes.
     */
    public function scopePositive($query)
    {
        $positiveOutcomes = array_filter(
            InterviewOutcomeEnum::cases(),
            fn($outcome) => $outcome->isPositive()
        );
        
        return $query->whereIn('outcome', array_map(fn($o) => $o->value, $positiveOutcomes));
    }

    /**
     * Scope to get negative outcomes.
     */
    public function scopeNegative($query)
    {
        $negativeOutcomes = array_filter(
            InterviewOutcomeEnum::cases(),
            fn($outcome) => $outcome->isNegative()
        );
        
        return $query->whereIn('outcome', array_map(fn($o) => $o->value, $negativeOutcomes));
    }

    /**
     * Scope to get outcomes with follow-up required.
     */
    public function scopeNeedsFollowUp($query)
    {
        return $query->where('follow_up_required', true)
                     ->where('follow_up_date', '>=', now());
    }
}
