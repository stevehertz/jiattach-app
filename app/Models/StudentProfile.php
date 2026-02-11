<?php

namespace App\Models;

use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentProfile extends Model
{
    use HasFactory, LogsModelActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'student_reg_number',
        'institution_name',
        'institution_type',
        'course_name',
        'course_level',
        'year_of_study',
        'expected_graduation_year',
        'cgpa',
        'skills',
        'interests',
        'cv_url',
        'transcript_url',
        'school_letter_url',
        'attachment_status',
        'attachment_start_date',
        'attachment_end_date',
        'preferred_attachment_duration',
        'preferred_location',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cgpa' => 'decimal:2',
        'skills' => 'array',
        'interests' => 'array',
        'attachment_start_date' => 'date',
        'attachment_end_date' => 'date',
        'year_of_study' => 'integer',
        'expected_graduation_year' => 'integer',
        'preferred_attachment_duration' => 'integer',
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
        'full_course_name',
        'institution_type_label',
        'course_level_label',
        'attachment_status_label',
        'attachment_duration',
        'is_currently_attached',
        'years_to_graduation',
        'cgpa_percentage',
        'skill_list',
        'interest_list',
    ];

    /**
     * Get the user that owns the student profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Get the full course name with level.
     */
    protected function fullCourseName(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->course_name} ({$this->course_level_label})",
        );
    }

    /**
     * Get the institution type label.
     */
    protected function institutionTypeLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'university' => 'University',
                    'college' => 'College',
                    'polytechnic' => 'Polytechnic',
                    'technical' => 'Technical Institute',
                ];
                return $labels[$this->institution_type] ?? $this->institution_type;
            },
        );
    }

    /**
     * Get the course level label.
     */
    protected function courseLevelLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'certificate' => 'Certificate',
                    'diploma' => 'Diploma',
                    'bachelor' => 'Bachelor\'s Degree',
                    'masters' => 'Master\'s Degree',
                    'phd' => 'PhD',
                ];
                return $labels[$this->course_level] ?? $this->course_level;
            },
        );
    }

    /**
     * Get the attachment status label.
     */
    protected function attachmentStatusLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'seeking' => 'Seeking Attachment',
                    'applied' => 'Applied',
                    'interviewing' => 'Interviewing',
                    'placed' => 'Placed',
                    'completed' => 'Completed',
                ];
                return $labels[$this->attachment_status] ?? $this->attachment_status;
            },
        );
    }

    /**
     * Get the attachment duration in months.
     */
    protected function attachmentDuration(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->attachment_start_date || !$this->attachment_end_date) {
                    return null;
                }

                $start = $this->attachment_start_date;
                $end = $this->attachment_end_date;

                return $start->diffInMonths($end);
            },
        );
    }

    /**
     * Check if student is currently attached.
     */
    protected function isCurrentlyAttached(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->attachment_status !== 'placed') {
                    return false;
                }

                if (!$this->attachment_start_date || !$this->attachment_end_date) {
                    return false;
                }

                $now = now();
                return $now->between($this->attachment_start_date, $this->attachment_end_date);
            },
        );
    }

    /**
     * Calculate years to graduation.
     */
    protected function yearsToGraduation(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->expected_graduation_year - now()->year,
        );
    }

    /**
     * Get CGPA as percentage.
     */
    protected function cgpaPercentage(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->cgpa ? round(($this->cgpa / 4.0) * 100, 2) : null,
        );
    }

    /**
     * Get skills as comma-separated list.
     */
    protected function skillList(): Attribute
    {
        return Attribute::make(
            get: fn() => is_array($this->skills) ? implode(', ', $this->skills) : null,
        );
    }

    /**
     * Get interests as comma-separated list.
     */
    protected function interestList(): Attribute
    {
        return Attribute::make(
            get: fn() => is_array($this->interests) ? implode(', ', $this->interests) : null,
        );
    }

    /**
     * Get the academic year label.
     */
    public function getAcademicYearAttribute(): string
    {
        $labels = [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
            6 => 'Sixth Year',
        ];

        return $labels[$this->year_of_study] ?? "Year {$this->year_of_study}";
    }

    /**
     * Get the academic progress percentage.
     */
    public function getAcademicProgressAttribute(): float
    {
        $totalYears = match ($this->course_level) {
            'certificate' => 1,
            'diploma' => 2,
            'bachelor' => 4,
            'masters' => 2,
            'phd' => 3,
            default => 4,
        };

        return min(100, ($this->year_of_study / $totalYears) * 100);
    }

    /**
     * Scope a query to only include students seeking attachment.
     */
    public function scopeSeekingAttachment($query)
    {
        return $query->where('attachment_status', 'seeking');
    }

    /**
     * Scope a query to only include students currently attached.
     */
    public function scopeCurrentlyAttached($query)
    {
        return $query->where('attachment_status', 'placed')
            ->where('attachment_start_date', '<=', now())
            ->where('attachment_end_date', '>=', now());
    }

    /**
     * Scope a query to only include students by institution type.
     */
    public function scopeByInstitutionType($query, $type)
    {
        return $query->where('institution_type', $type);
    }

    /**
     * Scope a query to only include students by course level.
     */
    public function scopeByCourseLevel($query, $level)
    {
        return $query->where('course_level', $level);
    }

    /**
     * Scope a query to only include students by graduation year.
     */
    public function scopeGraduatingIn($query, $year)
    {
        return $query->where('expected_graduation_year', $year);
    }

    /**
     * Scope a query to only include students with CGPA above a threshold.
     */
    public function scopeWithCGPAAbove($query, $cgpa)
    {
        return $query->where('cgpa', '>=', $cgpa);
    }

    /**
     * Scope a query to only include students with specific skills.
     */
    public function scopeWithSkills($query, array $skills)
    {
        return $query->whereJsonContains('skills', $skills);
    }

    /**
     * Scope a query to only include students by preferred location.
     */
    public function scopeByPreferredLocation($query, $location)
    {
        return $query->where('preferred_location', 'like', "%{$location}%");
    }

    /**
     * Scope a query to only include students by attachment status.
     */
    public function scopeByAttachmentStatus($query, $status)
    {
        return $query->where('attachment_status', $status);
    }

    /**
     * Check if student has a specific skill.
     */
    public function hasSkill(string $skill): bool
    {
        if (empty($this->skills) || !is_array($this->skills)) {
            return false;
        }

        return in_array(strtolower($skill), array_map('strtolower', $this->skills));
    }

    /**
     * Add a skill to the student's skills array.
     */
    public function addSkill(string $skill): void
    {
        $skills = $this->skills ?? [];

        if (!in_array($skill, $skills)) {
            $skills[] = $skill;
            $this->skills = $skills;
        }
    }

    /**
     * Remove a skill from the student's skills array.
     */
    public function removeSkill(string $skill): void
    {
        $skills = $this->skills ?? [];

        if (($key = array_search($skill, $skills)) !== false) {
            unset($skills[$key]);
            $this->skills = array_values($skills);
        }
    }

    /**
     * Check if student has a specific interest.
     */
    public function hasInterest(string $interest): bool
    {
        if (empty($this->interests) || !is_array($this->interests)) {
            return false;
        }

        return in_array(strtolower($interest), array_map('strtolower', $this->interests));
    }

    /**
     * Add an interest to the student's interests array.
     */
    public function addInterest(string $interest): void
    {
        $interests = $this->interests ?? [];

        if (!in_array($interest, $interests)) {
            $interests[] = $interest;
            $this->interests = $interests;
        }
    }

    /**
     * Remove an interest from the student's interests array.
     */
    public function removeInterest(string $interest): void
    {
        $interests = $this->interests ?? [];

        if (($key = array_search($interest, $interests)) !== false) {
            unset($interests[$key]);
            $this->interests = array_values($interests);
        }
    }

    /**
     * Update attachment status with validation.
     */
    public function updateAttachmentStatus(string $status, ?\DateTime $startDate = null, ?\DateTime $endDate = null): bool
    {
        $validStatuses = ['seeking', 'applied', 'interviewing', 'placed', 'completed'];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid attachment status: {$status}");
        }

        if ($status === 'placed' && (!$startDate || !$endDate)) {
            throw new \InvalidArgumentException('Start date and end date are required for placed status');
        }

        $this->attachment_status = $status;

        if ($startDate) {
            $this->attachment_start_date = $startDate;
        }

        if ($endDate) {
            $this->attachment_end_date = $endDate;
        }

        return $this->save();
    }

    /**
     * Calculate remaining attachment days.
     */
    public function getRemainingAttachmentDays(): ?int
    {
        if (!$this->is_currently_attached) {
            return null;
        }

        return now()->diffInDays($this->attachment_end_date, false);
    }

    /**
     * Get the student's current academic stage.
     */
    public function getAcademicStageAttribute(): string
    {
        if ($this->is_currently_attached) {
            return 'On Attachment';
        }

        if ($this->attachment_status === 'completed') {
            return 'Completed Attachment';
        }

        if ($this->attachment_status === 'placed') {
            return 'Upcoming Attachment';
        }

        return 'Academic Studies';
    }

    // /**
    //  * Get the student's profile completeness percentage.
    //  */
    // public function getProfileCompletenessAttribute(): int
    // {
    //     $requiredFields = [
    //         'student_reg_number',
    //         'institution_name',
    //         'institution_type',
    //         'course_name',
    //         'course_level',
    //         'year_of_study',
    //         'expected_graduation_year',
    //         'skills',
    //         'interests',
    //         'cv_url',
    //         'preferred_location',
    //     ];

    //     $filledCount = 0;
    //     foreach ($requiredFields as $field) {
    //         if (!empty($this->$field)) {
    //             $filledCount++;
    //         }
    //     }

    //     return (int) round(($filledCount / count($requiredFields)) * 100);
    // }

    /**
     * Calculate profile completeness with weights for each field.
     */
    public function calculateProfileCompleteness(): int
    {
        $weights = [
            'student_reg_number' => 10,    // 10%
            'institution_name' => 10,      // 10%
            'institution_type' => 5,       // 5%
            'course_name' => 10,           // 10%
            'course_level' => 5,           // 5%
            'year_of_study' => 5,          // 5%
            'expected_graduation_year' => 5, // 5%
            'cgpa' => 5,                   // 5%
            'skills' => 15,                // 15% - Important for matching
            'interests' => 5,              // 5%
            'cv_url' => 10,                // 10% - Document
            'transcript_url' => 5,         // 5% - Document
            'preferred_location' => 5,     // 5%
            'preferred_attachment_duration' => 5, // 5%
        ];

        $totalWeight = array_sum($weights);
        $achievedWeight = 0;

        foreach ($weights as $field => $weight) {
            if ($this->isFieldComplete($field)) {
                $achievedWeight += $weight;
            }
        }

        return min(100, (int) round(($achievedWeight / $totalWeight) * 100));
    }

    /**
     * Get the student's profile completeness percentage.
     */
    public function getProfileCompletenessAttribute(): int
    {
        // Use the weighted calculation for more accuracy
        return $this->calculateProfileCompleteness();
    }

    /**
     * Check if a specific field is complete.
     */
    public function isFieldComplete(string $field): bool
    {
        $value = $this->$field;

        if (empty($value)) {
            return false;
        }

        // Special checks for specific fields
        switch ($field) {
            case 'skills':
            case 'interests':
                return is_array($value) && count($value) > 0;

            case 'cgpa':
                return is_numeric($value) && $value > 0;

            case 'year_of_study':
                return is_numeric($value) && $value > 0;

            case 'expected_graduation_year':
                return is_numeric($value) && $value >= date('Y');

            default:
                return !empty(trim($value));
        }
    }

    /**
     * Get profile progress breakdown by category.
     */
    public function getProfileProgressBreakdown(): array
    {
        $categories = [
            'personal' => [
                'label' => 'Personal Information',
                'fields' => ['student_reg_number'],
                'weight' => 10,
            ],
            'academic' => [
                'label' => 'Academic Details',
                'fields' => [
                    'institution_name',
                    'institution_type',
                    'course_name',
                    'course_level',
                    'year_of_study',
                    'expected_graduation_year',
                    'cgpa'
                ],
                'weight' => 45,
            ],
            'skills' => [
                'label' => 'Skills & Interests',
                'fields' => ['skills', 'interests'],
                'weight' => 20,
            ],
            'documents' => [
                'label' => 'Documents',
                'fields' => ['cv_url', 'transcript_url'],
                'weight' => 15,
            ],
            'preferences' => [
                'label' => 'Placement Preferences',
                'fields' => ['preferred_location', 'preferred_attachment_duration'],
                'weight' => 10,
            ],
        ];

        $breakdown = [];

        foreach ($categories as $key => $category) {
            $completed = 0;
            $totalFields = count($category['fields']);

            foreach ($category['fields'] as $field) {
                if ($this->isFieldComplete($field)) {
                    $completed++;
                }
            }

            $percentage = $totalFields > 0 ? ($completed / $totalFields) * 100 : 0;

            $breakdown[$key] = [
                'label' => $category['label'],
                'completed' => $completed,
                'total' => $totalFields,
                'percentage' => (int) round($percentage),
                'weight' => $category['weight'],
                'fields' => $this->getFieldStatuses($category['fields']),
            ];
        }

        return $breakdown;
    }

    /**
     * Get field statuses for a list of fields.
     */
    private function getFieldStatuses(array $fields): array
    {
        $statuses = [];

        foreach ($fields as $field) {
            $statuses[$field] = [
                'label' => $this->getFieldLabel($field),
                'completed' => $this->isFieldComplete($field),
                'value' => $this->$field,
            ];
        }

        return $statuses;
    }

    /**
     * Get human-readable label for a field.
     */
    private function getFieldLabel(string $field): string
    {
        $labels = [
            'student_reg_number' => 'Student Registration Number',
            'institution_name' => 'Institution Name',
            'institution_type' => 'Institution Type',
            'course_name' => 'Course Name',
            'course_level' => 'Course Level',
            'year_of_study' => 'Year of Study',
            'expected_graduation_year' => 'Expected Graduation Year',
            'cgpa' => 'CGPA',
            'skills' => 'Skills',
            'interests' => 'Interests',
            'cv_url' => 'CV/Resume',
            'transcript_url' => 'Academic Transcript',
            'preferred_location' => 'Preferred Location',
            'preferred_attachment_duration' => 'Preferred Duration',
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    /**
     * Get the missing fields for profile completion.
     */
    public function getMissingFields(): array
    {
        $missing = [];
        $requiredFields = [
            'student_reg_number',
            'institution_name',
            'course_name',
            'year_of_study',
            'skills',
            'cv_url',
        ];

        foreach ($requiredFields as $field) {
            if (!$this->isFieldComplete($field)) {
                $missing[] = [
                    'field' => $field,
                    'label' => $this->getFieldLabel($field),
                    'priority' => in_array($field, ['student_reg_number', 'institution_name', 'course_name']) ? 'high' : 'medium',
                ];
            }
        }

        return $missing;
    }

    /**
     * Resets attachment status if a placement is cancelled.
     */
    public function resetStatus(): void
    {
        $this->update([
            'attachment_status' => 'seeking',
            'attachment_start_date' => null,
            'attachment_end_date' => null,
        ]);
    }

    /**
     * Helper to get the actual file path for documents.
     */
    public function getDocumentUrl(string $type): ?string
    {
        $field = $type . '_url';
        return $this->$field ? asset('storage/' . $this->$field) : null;
    }
}
