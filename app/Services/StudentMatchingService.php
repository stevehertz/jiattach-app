<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\AttachmentOpportunity;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentMatchingService
{
    /**
     * Find matching opportunities for a specific student with enhanced accuracy
     */
    public function findMatchesForStudent(User $student, int $limit = 10): array
    {
        if (!$student->hasRole('student') || !$student->studentProfile) {
            return [];
        }

        $profile = $student->studentProfile;

        // Only match students who are seeking
        if ($profile->attachment_status !== 'seeking') {
            return [];
        }

        // Get the student's course from the Course model
        $studentCourse = Course::where('name', $profile->course_name)->first();

        // Get active opportunities with eager loading
        $opportunities = AttachmentOpportunity::where('status', 'open')
            ->where('deadline', '>=', now())
            ->where('slots_available', '>', 0)
            ->with(['organization' => function ($query) {
                $query->where('is_verified', true);
            }])
            ->whereHas('organization', function ($query) {
                $query->where('is_verified', true);
            })
            ->get();

        $matches = [];

        foreach ($opportunities as $opportunity) {

            $score = $this->calculateEnhancedMatchScore($student, $profile, $opportunity, $studentCourse);

            // Only consider matches with score >= 50% for better accuracy
            if ($score >= 50) {
                $matchData = [
                    'opportunity' => $opportunity,
                    'score' => $score,
                    'quality' => $this->determineMatchQuality($score),
                    'badge' => $this->getMatchBadge($score),
                    'details' => $this->getEnhancedMatchDetails($student, $profile, $opportunity, $studentCourse),
                    'match_criteria' => $this->getMatchedCriteria($student, $profile, $opportunity, $studentCourse)
                ];

                $matches[] = $matchData;
            }
        }

        // Sort by score descending
        usort($matches, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($matches, 0, $limit);
    }

    /**
     * Enhanced match score calculation with refined weighting
     */
    public function calculateEnhancedMatchScore(User $student, $profile, AttachmentOpportunity $opportunity, $studentCourse = null): float
    {
        $score = 0;
        $totalWeight = 0;

        // 1. Course Match (50%) - Most important factor
        $courseWeight = 50;
        $courseScore = $this->calculateCourseMatch($profile->course_name, $opportunity->courses_required ?? [], $studentCourse);
        $score += ($courseScore * $courseWeight / 100);
        $totalWeight += $courseWeight;

        // 2. Skills Match (30%) - Important for job fit
        $skillsWeight = 30;
        $skillsScore = $this->calculateSkillsMatch($profile->skills ?? [], $opportunity->skills_required ?? []);
        $score += ($skillsScore * $skillsWeight / 100);
        $totalWeight += $skillsWeight;

        // 3. Location Match (20%) - Important for accessibility
        $locationWeight = 20;
        $locationScore = $this->calculateLocationMatch(
            $profile->preferred_location ?? $student->county,
            $opportunity->location ?? $opportunity->county,
            $student->county,
            $opportunity->work_type
        );
        $score += ($locationScore * $locationWeight / 100);
        $totalWeight += $locationWeight;

        // Bonus: Year of Study Match (adds up to 5% bonus)
        $yearBonus = $this->calculateYearBonus($profile->year_of_study, $opportunity->preferred_year_of_study ?? null);
        $score += $yearBonus;

        // Bonus: Disability Accommodations (adds up to 3% bonus if accommodations available)
        if ($student->hasDisability() && $opportunity->disability_accommodations ?? false) {
            $score += 3;
        }

        return round(min(100, max(0, $score)), 2);
    }


    /**
     * Calculate course match with intelligent partial matching
     */
    private function calculateCourseMatch(string $studentCourse, array $requiredCourses, $studentCourseModel = null): float
    {
        if (empty($requiredCourses)) {
            return 80; // No course requirement, good match but not perfect
        }

        $studentCourseLower = strtolower(trim($studentCourse));

        // If we have the actual Course model, use it for better matching
        if ($studentCourseModel) {
            $studentCategory = $studentCourseModel->category;

            // Check if any required course matches the student's course category
            foreach ($requiredCourses as $required) {
                $requiredCourse = Course::where('name', $required)->first();

                // Exact match by ID if available
                if ($requiredCourse && $studentCourseModel->id === $requiredCourse->id) {
                    return 100;
                }

                // Category match (e.g., "Computer Science" and "Information Technology" both in ICT)
                if ($requiredCourse && $requiredCourse->category === $studentCategory) {
                    return 85;
                }
            }
        }

        // Fall back to string matching
        $bestMatch = 0;

        foreach ($requiredCourses as $required) {
            $requiredLower = strtolower(trim($required));

            // Exact match
            if ($studentCourseLower === $requiredLower) {
                return 100;
            }

            // Contains match
            if (str_contains($studentCourseLower, $requiredLower) || str_contains($requiredLower, $studentCourseLower)) {
                $bestMatch = max($bestMatch, 90);
                continue;
            }

            // Word-by-word matching
            $studentWords = explode(' ', $studentCourseLower);
            $requiredWords = explode(' ', $requiredLower);
            $commonWords = array_intersect($studentWords, $requiredWords);

            if (!empty($commonWords)) {
                $matchPercentage = (count($commonWords) / max(count($studentWords), count($requiredWords))) * 100;
                $bestMatch = max($bestMatch, min(80, $matchPercentage));
            }

            // Related fields check
            if ($this->areRelatedFields($studentCourseLower, $requiredLower)) {
                $bestMatch = max($bestMatch, 70);
            }
        }

        return $bestMatch;
    }

    /**
     * Calculate skills match with weighted importance
     */
    private function calculateSkillsMatch(array $studentSkills, array $requiredSkills): float
    {
        if (empty($requiredSkills)) {
            return 70; // No specific skills required
        }

        if (empty($studentSkills)) {
            return 0;
        }

        $studentSkillsLower = array_map('strtolower', $studentSkills);
        $requiredSkillsLower = array_map('strtolower', $requiredSkills);

        $matchedSkills = 0;
        $partialMatches = 0;
        $totalRequired = count($requiredSkills);

        foreach ($requiredSkillsLower as $requiredSkill) {
            $bestMatchForSkill = 0;

            foreach ($studentSkillsLower as $studentSkill) {
                // Exact match
                if ($studentSkill === $requiredSkill) {
                    $bestMatchForSkill = 1;
                    break;
                }

                // Contains match
                if (str_contains($studentSkill, $requiredSkill) || str_contains($requiredSkill, $studentSkill)) {
                    $bestMatchForSkill = max($bestMatchForSkill, 0.8);
                }

                // Related skills
                if ($this->areRelatedSkills($studentSkill, $requiredSkill)) {
                    $bestMatchForSkill = max($bestMatchForSkill, 0.6);
                }
            }

            if ($bestMatchForSkill >= 1) {
                $matchedSkills++;
            } elseif ($bestMatchForSkill >= 0.6) {
                $partialMatches += $bestMatchForSkill;
            }
        }

        // Calculate weighted score
        $totalMatchScore = (($matchedSkills + $partialMatches) / $totalRequired) * 100;

        // Bonus for having more skills than required (up to 10% bonus)
        if (count($studentSkills) > $totalRequired && $totalMatchScore > 70) {
            $bonus = min(10, (count($studentSkills) - $totalRequired) * 2);
            $totalMatchScore = min(100, $totalMatchScore + $bonus);
        }

        return round($totalMatchScore, 2);
    }

    /**
     * Calculate location match with flexibility
     */
    private function calculateLocationMatch(?string $preferredLocation, ?string $opportunityLocation, ?string $homeCounty, ?string $workType): float
    {
        // Remote work is always a good match
        if ($workType === 'remote') {
            return 100;
        }

        // No location preferences specified
        if (!$opportunityLocation && !$preferredLocation) {
            return 70;
        }

        $preferredLocation = strtolower($preferredLocation ?? '');
        $opportunityLocation = strtolower($opportunityLocation ?? '');
        $homeCounty = strtolower($homeCounty ?? '');

        // Exact match with preferred location
        if ($preferredLocation && $preferredLocation === $opportunityLocation) {
            return 100;
        }

        // Match with home county (students often prefer near home)
        if ($homeCounty && $homeCounty === $opportunityLocation) {
            return 95;
        }

        // Contains match
        if ($preferredLocation && (str_contains($opportunityLocation, $preferredLocation) || str_contains($preferredLocation, $opportunityLocation))) {
            return 85;
        }

        // Check for nearby counties
        if ($this->areNearbyCounties($opportunityLocation, $homeCounty ?: $preferredLocation)) {
            return 75;
        }

        // If student didn't specify preference, treat as flexible
        if (empty($preferredLocation)) {
            return 60;
        }

        return 0;
    }

    /**
     * Calculate year of study bonus (adds up to 5 points)
     */
    private function calculateYearBonus(int $studentYear, ?string $preferredYear): float
    {
        if (!$preferredYear) {
            return 5; // No preference, give full bonus
        }

        // Parse preferred year range (e.g., "2-4" or "3")
        if (str_contains($preferredYear, '-')) {
            [$min, $max] = explode('-', $preferredYear);
            if ($studentYear >= (int)$min && $studentYear <= (int)$max) {
                return 5;
            } elseif ($studentYear == (int)$min - 1 || $studentYear == (int)$max + 1) {
                return 2; // Close to range
            }
        } elseif ((int)$preferredYear === $studentYear) {
            return 5;
        } elseif (abs($studentYear - (int)$preferredYear) === 1) {
            return 2;
        }

        return 0;
    }

     /**
     * Determine match quality level
     */
    private function determineMatchQuality(float $score): array
    {
        if ($score >= 85) {
            return [
                'level' => 'excellent',
                'label' => 'Excellent Match',
                'description' => 'This opportunity is an excellent fit based on course, skills, and location.'
            ];
        } elseif ($score >= 70) {
            return [
                'level' => 'good',
                'label' => 'Good Match',
                'description' => 'This opportunity is a good fit with strong alignment.'
            ];
        } elseif ($score >= 55) {
            return [
                'level' => 'potential',
                'label' => 'Potential Match',
                'description' => 'This opportunity has potential but may require additional consideration.'
            ];
        } else {
            return [
                'level' => 'fair',
                'label' => 'Fair Match',
                'description' => 'This opportunity has some alignment but may not be ideal.'
            ];
        }
    }

    /**
     * Get match badge class
     */
    private function getMatchBadge(float $score): string
    {
        if ($score >= 85) return 'success';
        if ($score >= 70) return 'info';
        if ($score >= 55) return 'warning';
        return 'secondary';
    }

    /**
     * Get matched criteria for display
     */
    private function getMatchedCriteria(User $student, $profile, AttachmentOpportunity $opportunity, $studentCourse = null): array
    {
        $matched = [];

        // Check course match
        if ($this->calculateCourseMatch($profile->course_name, $opportunity->courses_required ?? [], $studentCourse) >= 70) {
            $matched[] = 'Course';
        }

        // Check skills match
        if ($this->calculateSkillsMatch($profile->skills ?? [], $opportunity->skills_required ?? []) >= 70) {
            $matched[] = 'Skills';
        }

        // Check location match
        if ($this->calculateLocationMatch(
            $profile->preferred_location ?? $student->county,
            $opportunity->location ?? $opportunity->county,
            $student->county,
            $opportunity->work_type
        ) >= 70) {
            $matched[] = 'Location';
        }

        // Check year match bonus
        if ($this->calculateYearBonus($profile->year_of_study, $opportunity->preferred_year_of_study ?? null) > 0) {
            $matched[] = 'Year Level';
        }

        return $matched;
    }

      /**
     * Get enhanced detailed breakdown of match
     */
    public function getEnhancedMatchDetails(User $student, $profile, AttachmentOpportunity $opportunity, $studentCourse = null): array
    {
        $courseScore = $this->calculateCourseMatch($profile->course_name, $opportunity->courses_required ?? [], $studentCourse);
        $skillsScore = $this->calculateSkillsMatch($profile->skills ?? [], $opportunity->skills_required ?? []);
        $locationScore = $this->calculateLocationMatch(
            $profile->preferred_location ?? $student->county,
            $opportunity->location ?? $opportunity->county,
            $student->county,
            $opportunity->work_type
        );

        return [
            'course' => [
                'score' => $courseScore,
                'weight' => 50,
                'student_value' => $profile->course_name,
                'required_value' => $opportunity->courses_required ?? ['Any'],
                'status' => $this->getMatchStatus($courseScore),
                'category' => $studentCourse ? $studentCourse->category : null
            ],
            'skills' => [
                'score' => $skillsScore,
                'weight' => 30,
                'student_value' => count($profile->skills ?? []) . ' skills',
                'required_value' => count($opportunity->skills_required ?? []) . ' required',
                'matched_count' => $this->countMatchedSkills($profile->skills ?? [], $opportunity->skills_required ?? []),
                'status' => $this->getMatchStatus($skillsScore)
            ],
            'location' => [
                'score' => $locationScore,
                'weight' => 20,
                'student_value' => $profile->preferred_location ?? $student->county ?? 'Anywhere',
                'required_value' => $opportunity->location ?? $opportunity->county ?? 'Anywhere',
                'work_type' => $opportunity->work_type,
                'status' => $this->getMatchStatus($locationScore)
            ]
        ];
    }

     /**
     * Get match status based on score
     */
    private function getMatchStatus(float $score): string
    {
        if ($score >= 80) return 'excellent';
        if ($score >= 60) return 'good';
        if ($score >= 40) return 'fair';
        return 'poor';
    }

    /**
     * Count matched skills
     */
    private function countMatchedSkills(array $studentSkills, array $requiredSkills): int
    {
        if (empty($requiredSkills) || empty($studentSkills)) {
            return 0;
        }

        $studentSkillsLower = array_map('strtolower', $studentSkills);
        $requiredSkillsLower = array_map('strtolower', $requiredSkills);

        $matched = 0;
        foreach ($requiredSkillsLower as $required) {
            foreach ($studentSkillsLower as $skill) {
                if (str_contains($skill, $required) || str_contains($required, $skill)) {
                    $matched++;
                    break;
                }
            }
        }

        return $matched;
    }

    /**
     * Check if two fields are related
     */
    private function areRelatedFields(string $field1, string $field2): bool
    {
        $relatedPairs = [
            ['it', 'information technology', 'computer'],
            ['computer science', 'cs', 'computing', 'software'],
            ['business', 'commerce', 'management', 'administration'],
            ['engineering', 'engineer', 'technology'],
            ['nursing', 'health', 'medical', 'clinical'],
            ['accounting', 'finance', 'economics'],
            ['construction', 'building', 'civil'],
            ['architecture', 'design', 'planning'],
        ];

        foreach ($relatedPairs as $pair) {
            $matchCount = 0;
            foreach ($pair as $term) {
                if (str_contains($field1, $term) || str_contains($field2, $term)) {
                    $matchCount++;
                }
            }
            if ($matchCount >= 2) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if two skills are related
     */
    private function areRelatedSkills(string $skill1, string $skill2): bool
    {
        $relatedSkills = [
            ['php', 'laravel', 'symfony', 'codeigniter'],
            ['javascript', 'typescript', 'node', 'react', 'vue', 'angular', 'jquery'],
            ['python', 'django', 'flask', 'fastapi'],
            ['java', 'spring', 'kotlin', 'android'],
            ['html', 'css', 'sass', 'less', 'bootstrap'],
            ['mysql', 'postgresql', 'mongodb', 'sqlite', 'database'],
            ['aws', 'cloud', 'azure', 'gcp', 'devops'],
            ['photoshop', 'illustrator', 'figma', 'design', 'ui', 'ux'],
            ['excel', 'spreadsheet', 'data analysis'],
            ['project management', 'agile', 'scrum', 'jira'],
        ];

        foreach ($relatedSkills as $group) {
            $foundInGroup = 0;
            foreach ($group as $term) {
                if (str_contains($skill1, $term) || str_contains($skill2, $term)) {
                    $foundInGroup++;
                }
            }
            if ($foundInGroup >= 2) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if counties are nearby
     */
    private function areNearbyCounties(string $county1, string $county2): bool
    {
        if (empty($county1) || empty($county2)) {
            return false;
        }

        $nearbyGroups = [
            ['nairobi', 'kiambu', 'kajiado', 'machakos'],
            ['mombasa', 'kwale', 'kilifi', 'tana river'],
            ['kisumu', 'homabay', 'siaya', 'kakamega', 'vihiga'],
            ['nakuru', 'narok', 'baringo', 'kericho', 'bomet'],
            ['meru', 'tharaka nithi', 'embu', 'kirinyaga'],
            ['nyeri', 'muranga', 'kiambu', 'kirinyaga'],
        ];

        $county1Lower = strtolower($county1);
        $county2Lower = strtolower($county2);

        foreach ($nearbyGroups as $group) {
            $county1InGroup = in_array($county1Lower, $group);
            $county2InGroup = in_array($county2Lower, $group);

            if ($county1InGroup && $county2InGroup) {
                return true;
            }
        }

        return false;
    }

    /**
     * Save matches as applications
     */
    public function saveMatchesForStudent(User $student, array $matches): array
    {
        $savedMatches = [];

        DB::beginTransaction();

        try {
            foreach ($matches as $match) {
                // Check if application already exists
                $existingApplication = Application::where('student_id', $student->id)
                    ->where('attachment_opportunity_id', $match['opportunity']->id)
                    ->first();

                if (!$existingApplication) {
                    $opportunity = $match['opportunity'];

                    $application = Application::create([
                        'student_id' => $student->id,
                        'attachment_opportunity_id' => $opportunity->id,
                        'organization_id' => $opportunity->organization_id,
                        'match_score' => $match['score'],
                        'match_quality' => $match['quality']['level'] ?? 'potential',
                        'matched_criteria' => $match['match_criteria'] ?? [],
                        'status' => ApplicationStatus::PENDING,
                        'submitted_at' => now(),
                    ]);

                    // Add history record
                    $application->addHistory(
                        'created',
                        $student->id,
                        $opportunity->organization_id,
                        null,
                        ApplicationStatus::PENDING->value,
                        "System match created with score {$match['score']}% ({$match['quality']['label']})",
                        [
                            'match_score' => $match['score'],
                            'match_quality' => $match['quality']['level'],
                            'matched_criteria' => $match['match_criteria'] ?? [],
                            'match_details' => $match['details'] ?? [],
                            'opportunity_id' => $opportunity->id,
                            'opportunity_title' => $opportunity->title,
                            'organization_id' => $opportunity->organization_id,
                            'organization_name' => $opportunity->organization->name ?? null,
                            'created_by' => auth()->user()?->email ?? 'system_matching',
                            'created_at' => now()->toDateTimeString(),
                        ]
                    );

                    $savedMatches[] = $application;

                    // Log individual match creation
                    activity_log(
                        "Match created: {$opportunity->title} ({$match['score']}%) - {$match['quality']['label']}",
                        'match_created',
                        [
                            'application_id' => $application->id,
                            'opportunity_id' => $opportunity->id,
                            'organization_id' => $opportunity->organization_id,
                            'match_score' => $match['score'],
                            'match_quality' => $match['quality']['level'],
                            'matched_criteria' => $match['match_criteria'],
                            'student_id' => $student->id,
                            'student_name' => $student->full_name
                        ],
                        'matching'
                    );
                }
            }

            if (!empty($savedMatches)) {
                $student->studentProfile->update([
                    'attachment_status' => 'applied'
                ]);

                activity_log(
                    'Student status updated to applied after matching',
                    'status_changed',
                    [
                        'student_id' => $student->id,
                        'student_name' => $student->full_name,
                        'old_status' => 'seeking',
                        'new_status' => 'applied',
                        'matches_count' => count($savedMatches),
                        'match_ids' => collect($savedMatches)->pluck('id')->toArray(),
                    ],
                    'student_profile'
                );
            }

            DB::commit();

            return $savedMatches;
        } catch (\Exception $e) {
            DB::rollBack();

            activity_log(
                'Failed to save matches for student: ' . $student->full_name,
                'matches_failed',
                [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'error' => $e->getMessage(),
                    'matches_attempted' => count($matches)
                ],
                'matching'
            );

            Log::error('Failed to save matches: ' . $e->getMessage());
            throw $e;
        }
    }

     /**
     * Run matching for all seeking students (bulk operation)
     */
    public function runMatchingForAllSeekingStudents(): array
    {
        $students = User::role('student')
            ->whereHas('studentProfile', function ($query) {
                $query->where('attachment_status', 'seeking');
            })
            ->get();

        $results = [
            'total_processed' => 0,
            'matches_created' => 0,
            'errors' => []
        ];

        foreach ($students as $student) {
            try {
                $matches = $this->findMatchesForStudent($student, 5);

                if (!empty($matches)) {
                    $saved = $this->saveMatchesForStudent($student, $matches);
                    $results['matches_created'] += count($saved);
                }

                $results['total_processed']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Student {$student->id} ({$student->full_name}): " . $e->getMessage();
                Log::error('Bulk matching error for student ' . $student->id . ': ' . $e->getMessage());
            }
        }

        return $results;
    }
}
