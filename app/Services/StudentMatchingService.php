<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\AttachmentOpportunity;
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

        // Get active opportunities with eager loading
        $opportunities = AttachmentOpportunity::where('status', 'open')
            ->where('deadline', '>=', now())
            ->with(['organization' => function($query) {
                $query->where('is_verified', true); // Only verified organizations
            }])
            ->whereHas('organization', function($query) {
                $query->where('is_verified', true);
            })
            ->get();

        $matches = [];
        $exactMatches = [];
        $goodMatches = [];
        $potentialMatches = [];

        foreach ($opportunities as $opportunity) {
            $score = $this->calculateEnhancedMatchScore($student, $profile, $opportunity);
            $matchQuality = $this->determineMatchQuality($score, $opportunity, $profile);

            // Only consider matches with score >= 60% for better accuracy
            if ($score >= 60) {
                $matchData = [
                    'opportunity' => $opportunity,
                    'score' => $score,
                    'quality' => $matchQuality['level'],
                    'badge' => $matchQuality['badge'],
                    'details' => $this->getEnhancedMatchDetails($student, $profile, $opportunity),
                    'match_criteria' => $this->getMatchedCriteria($student, $profile, $opportunity)
                ];

                // Categorize matches by quality
                if ($score >= 85) {
                    $exactMatches[] = $matchData;
                } elseif ($score >= 70) {
                    $goodMatches[] = $matchData;
                } else {
                    $potentialMatches[] = $matchData;
                }
            }
        }

        // Merge matches in order: exact matches first, then good, then potential
        $matches = array_merge($exactMatches, $goodMatches, $potentialMatches);

        return array_slice($matches, 0, $limit);
    }

    /**
     * Enhanced match score calculation with more accurate weighting
     */
    public function calculateEnhancedMatchScore(User $student, $profile, AttachmentOpportunity $opportunity): float
    {
        $score = 0;
        $totalWeight = 0;
        $matchedCriteria = [];

        // 1. Course Match (35%) - Most important
        $courseWeight = 35;
        $courseScore = $this->calculateEnhancedCourseMatch($profile->course_name, $opportunity->courses_required ?? []);
        $score += ($courseScore * $courseWeight);
        $totalWeight += $courseWeight;
        if ($courseScore >= 70) $matchedCriteria[] = 'course';

        // 2. Location Match (25%) - Very important for accessibility
        $locationWeight = 25;
        $locationScore = $this->calculateEnhancedLocationMatch(
            $profile->preferred_location ?? $student->county,
            $opportunity->location ?? $opportunity->county,
            $student->county // Student's home county
        );
        $score += ($locationScore * $locationWeight);
        $totalWeight += $locationWeight;
        if ($locationScore >= 70) $matchedCriteria[] = 'location';

        // 3. Skills Match (20%) - Important for job fit
        $skillsWeight = 20;
        $skillsScore = $this->calculateEnhancedSkillsMatch(
            $profile->skills ?? [],
            $opportunity->skills_required ?? []
        );
        $score += ($skillsScore * $skillsWeight);
        $totalWeight += $skillsWeight;
        if ($skillsScore >= 70) $matchedCriteria[] = 'skills';

        // 4. GPA Match (10%) - Minimum requirement
        $gpaWeight = 10;
        $gpaScore = $this->calculateEnhancedGpaMatch($profile->cgpa, $opportunity->min_gpa);
        $score += ($gpaScore * $gpaWeight);
        $totalWeight += $gpaWeight;
        if ($gpaScore >= 70) $matchedCriteria[] = 'gpa';

        // 5. Year of Study Match (10%) - Ensure student is at right level
        $yearWeight = 10;
        $yearScore = $this->calculateYearOfStudyMatch($profile->year_of_study, $opportunity->preferred_year_of_study ?? null);
        $score += ($yearScore * $yearWeight);
        $totalWeight += $yearWeight;
        if ($yearScore >= 70) $matchedCriteria[] = 'year';

        // Calculate final score
        $finalScore = $totalWeight > 0 ? round(($score / $totalWeight), 2) : 0;

        // Bonus points for having multiple criteria matched
        $criteriaCount = count($matchedCriteria);
        if ($criteriaCount >= 4) {
            $finalScore = min(100, $finalScore + 5); // Bonus for matching most criteria
        }

        return $finalScore;
    }

    /**
     * Enhanced course match with better partial matching
     */
    private function calculateEnhancedCourseMatch(string $studentCourse, ?array $requiredCourses): float
    {
        if (empty($requiredCourses)) {
            return 80; // No course requirement, but not perfect match
        }

        $studentCourseLower = strtolower(trim($studentCourse));
        $studentWords = explode(' ', $studentCourseLower);

        $bestMatch = 0;

        foreach ($requiredCourses as $required) {
            $requiredLower = strtolower(trim($required));

            // Exact match
            if ($studentCourseLower === $requiredLower) {
                return 100;
            }

            // Check if required course is a subset or superset
            if (str_contains($studentCourseLower, $requiredLower) ||
                str_contains($requiredLower, $studentCourseLower)) {
                $bestMatch = max($bestMatch, 90);
                continue;
            }

            // Check word-by-word matching (e.g., "Computer Science" vs "Computer Technology")
            $requiredWords = explode(' ', $requiredLower);
            $commonWords = array_intersect($studentWords, $requiredWords);

            if (!empty($commonWords)) {
                $matchPercentage = (count($commonWords) / max(count($studentWords), count($requiredWords))) * 100;
                $bestMatch = max($bestMatch, min(85, $matchPercentage));
            }

            // Check for related fields (e.g., "IT" vs "Computer Science")
            if ($this->areRelatedFields($studentCourseLower, $requiredLower)) {
                $bestMatch = max($bestMatch, 75);
            }
        }

        return $bestMatch;
    }

    /**
     * Enhanced location match with county-level accuracy
     */
    private function calculateEnhancedLocationMatch(?string $preferredLocation, ?string $opportunityLocation, ?string $homeCounty): float
    {
        // If no location preferences, assume neutral
        if (!$opportunityLocation) {
            return 60;
        }

        $preferredLocation = strtolower($preferredLocation ?? '');
        $opportunityLocation = strtolower($opportunityLocation);
        $homeCounty = strtolower($homeCounty ?? '');

        // Exact match with preferred location
        if ($preferredLocation && $preferredLocation === $opportunityLocation) {
            return 100;
        }

        // Match with home county (students often prefer near home)
        if ($homeCounty && $homeCounty === $opportunityLocation) {
            return 90;
        }

        // Check if opportunity location contains preferred location or vice versa
        if ($preferredLocation) {
            if (str_contains($opportunityLocation, $preferredLocation) ||
                str_contains($preferredLocation, $opportunityLocation)) {
                return 80;
            }
        }

        // Check for nearby counties (you can expand this with actual geographic data)
        if ($this->areNearbyCounties($opportunityLocation, $homeCounty ?: $preferredLocation)) {
            return 70;
        }

        // Remote work or flexible location
        if ($opportunityLocation === 'remote' || $opportunityLocation === 'online') {
            return 75;
        }

        // No match
        return 0;
    }

    /**
     * Enhanced skills match with weighted importance
     */
    private function calculateEnhancedSkillsMatch(array $studentSkills, array $requiredSkills): float
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
                if (str_contains($studentSkill, $requiredSkill) ||
                    str_contains($requiredSkill, $studentSkill)) {
                    $bestMatchForSkill = max($bestMatchForSkill, 0.8);
                }

                // Related skills (e.g., "PHP" vs "Laravel")
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

        // Calculate weighted score: exact matches count fully, partial matches count partially
        $totalMatchScore = ($matchedSkills + $partialMatches) / $totalRequired * 100;

        return round($totalMatchScore, 2);
    }

    /**
     * Enhanced GPA match with better scaling
     */
    private function calculateEnhancedGpaMatch(?float $studentGpa, ?float $minGpa): float
    {
        if (!$minGpa) {
            return 70; // No minimum requirement
        }

        if (!$studentGpa) {
            return 0; // No GPA provided
        }

        if ($studentGpa < $minGpa) {
            return 0; // Below minimum
        }

        // Calculate how much above minimum
        $gpaRange = 4.0 - $minGpa;
        if ($gpaRange <= 0) {
            return 100;
        }

        $studentAdvantage = $studentGpa - $minGpa;

        // Scale: min requirement = 60%, perfect = 100%
        $score = 60 + (($studentAdvantage / $gpaRange) * 40);

        return round(min(100, $score), 2);
    }

    /**
     * Calculate year of study match
     */
    private function calculateYearOfStudyMatch(int $studentYear, ?string $preferredYear): float
    {
        if (!$preferredYear) {
            return 80; // No preference
        }

        // Parse preferred year range (e.g., "2-4" or "3")
        if (str_contains($preferredYear, '-')) {
            [$min, $max] = explode('-', $preferredYear);
            if ($studentYear >= (int)$min && $studentYear <= (int)$max) {
                return 100;
            }
        } elseif ((int)$preferredYear === $studentYear) {
            return 100;
        }

        // Check if close to preferred year
        $diff = abs($studentYear - (int)$preferredYear);
        if ($diff === 1) {
            return 70;
        }

        return 0;
    }

    /**
     * Determine match quality level
     */
    private function determineMatchQuality(float $score, $opportunity, $profile): array
    {
        if ($score >= 85) {
            return [
                'level' => 'exact',
                'badge' => 'success',
                'label' => 'Excellent Match'
            ];
        } elseif ($score >= 70) {
            return [
                'level' => 'good',
                'badge' => 'info',
                'label' => 'Good Match'
            ];
        } else {
            return [
                'level' => 'potential',
                'badge' => 'warning',
                'label' => 'Potential Match'
            ];
        }
    }

    /**
     * Get matched criteria for display
     */
    private function getMatchedCriteria(User $student, $profile, AttachmentOpportunity $opportunity): array
    {
        $matched = [];

        // Check course match
        if ($this->calculateEnhancedCourseMatch($profile->course_name, $opportunity->courses_required ?? []) >= 70) {
            $matched[] = 'Course';
        }

        // Check location match
        if ($this->calculateEnhancedLocationMatch(
            $profile->preferred_location ?? $student->county,
            $opportunity->location ?? $opportunity->county,
            $student->county
        ) >= 70) {
            $matched[] = 'Location';
        }

        // Check skills match
        if ($this->calculateEnhancedSkillsMatch($profile->skills ?? [], $opportunity->skills_required ?? []) >= 70) {
            $matched[] = 'Skills';
        }

        // Check GPA match
        if ($this->calculateEnhancedGpaMatch($profile->cgpa, $opportunity->min_gpa) >= 70) {
            $matched[] = 'GPA';
        }

        return $matched;
    }

    /**
     * Get enhanced detailed breakdown of match
     */
    public function getEnhancedMatchDetails(User $student, $profile, AttachmentOpportunity $opportunity): array
    {
        return [
            'course' => [
                'score' => $this->calculateEnhancedCourseMatch($profile->course_name, $opportunity->courses_required ?? []),
                'weight' => 35,
                'student_value' => $profile->course_name,
                'required_value' => $opportunity->courses_required ?? ['Any'],
                'status' => $this->getMatchStatus($this->calculateEnhancedCourseMatch($profile->course_name, $opportunity->courses_required ?? []))
            ],
            'location' => [
                'score' => $this->calculateEnhancedLocationMatch(
                    $profile->preferred_location ?? $student->county,
                    $opportunity->location ?? $opportunity->county,
                    $student->county
                ),
                'weight' => 25,
                'student_value' => $profile->preferred_location ?? $student->county,
                'required_value' => $opportunity->location ?? $opportunity->county ?? 'Any',
                'status' => $this->getMatchStatus($this->calculateEnhancedLocationMatch(
                    $profile->preferred_location ?? $student->county,
                    $opportunity->location ?? $opportunity->county,
                    $student->county
                ))
            ],
            'skills' => [
                'score' => $this->calculateEnhancedSkillsMatch($profile->skills ?? [], $opportunity->skills_required ?? []),
                'weight' => 20,
                'student_value' => count($profile->skills ?? []) . ' skills',
                'required_value' => count($opportunity->skills_required ?? []) . ' required',
                'matched_count' => $this->countMatchedSkills($profile->skills ?? [], $opportunity->skills_required ?? []),
                'status' => $this->getMatchStatus($this->calculateEnhancedSkillsMatch($profile->skills ?? [], $opportunity->skills_required ?? []))
            ],
            'gpa' => [
                'score' => $this->calculateEnhancedGpaMatch($profile->cgpa, $opportunity->min_gpa),
                'weight' => 10,
                'student_value' => $profile->cgpa ?? 'Not provided',
                'required_value' => $opportunity->min_gpa ?? 'None',
                'status' => $this->getMatchStatus($this->calculateEnhancedGpaMatch($profile->cgpa, $opportunity->min_gpa))
            ],
            'year_of_study' => [
                'score' => $this->calculateYearOfStudyMatch($profile->year_of_study, $opportunity->preferred_year_of_study ?? null),
                'weight' => 10,
                'student_value' => 'Year ' . $profile->year_of_study,
                'required_value' => $opportunity->preferred_year_of_study ?? 'Any',
                'status' => $this->getMatchStatus($this->calculateYearOfStudyMatch($profile->year_of_study, $opportunity->preferred_year_of_study ?? null))
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
     * Check if two fields are related (e.g., "IT" and "Computer Science")
     */
    private function areRelatedFields(string $field1, string $field2): bool
    {
        $relatedPairs = [
            ['it', 'information technology', 'computer'],
            ['computer science', 'cs', 'computing'],
            ['business', 'commerce', 'management'],
            ['engineering', 'engineer', 'technology'],
            ['nursing', 'health', 'medical'],
            ['accounting', 'finance', 'economics'],
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
            // Programming languages
            ['php', 'laravel', 'symfony'],
            ['javascript', 'typescript', 'node', 'react', 'vue', 'angular'],
            ['python', 'django', 'flask'],
            ['java', 'spring', 'kotlin'],

            // Design
            ['photoshop', 'illustrator', 'figma', 'ui', 'ux', 'design'],

            // Database
            ['mysql', 'sql', 'database', 'postgresql', 'mongodb'],

            // Cloud
            ['aws', 'cloud', 'azure', 'devops'],
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
     * Check if counties are nearby (simplified - you can expand with actual data)
     */
    private function areNearbyCounties(string $county1, string $county2): bool
    {
        if (empty($county1) || empty($county2)) {
            return false;
        }

        // Define nearby county groups (simplified - expand based on actual geography)
        $nearbyGroups = [
            ['nairobi', 'kiambu', 'kajiado', 'machakos'],
            ['mombasa', 'kwale', 'kilifi', 'tana river'],
            ['kisumu', 'homabay', 'siaya', 'kakamega'],
            ['nakuru', 'narok', 'baringo', 'kericho'],
        ];

        foreach ($nearbyGroups as $group) {
            $county1InGroup = in_array(strtolower($county1), $group);
            $county2InGroup = in_array(strtolower($county2), $group);

            if ($county1InGroup && $county2InGroup) {
                return true;
            }
        }

        return false;
    }

    /**
     * Save matches as applications (with enhanced tracking)
     */
    public function saveMatchesForStudent(User $student, array $matches): array
    {
        $savedMatches = [];

        DB::beginTransaction();

        try {
            foreach ($matches as $match) {
                // Check if application already exists
                $existingApplication = Application::where('user_id', $student->id)
                    ->where('attachment_opportunity_id', $match['opportunity']->id)
                    ->first();

                if (!$existingApplication) {
                    $application = Application::create([
                        'user_id' => $student->id,
                        'student_id' => $student->id,
                        'attachment_opportunity_id' => $match['opportunity']->id,
                        'match_score' => $match['score'],
                        'match_quality' => $match['quality'] ?? 'potential',
                        'matched_criteria' => $match['match_criteria'] ?? [],
                        'status' => ApplicationStatus::PENDING,
                        'submitted_at' => now(),
                    ]);

                    $savedMatches[] = $application;

                    // Log individual match creation
                    activity_log(
                        "Match created: {$match['opportunity']->title} ({$match['score']}%) - {$match['quality']}",
                        'match_created',
                        [
                            'application_id' => $application->id,
                            'opportunity_id' => $match['opportunity']->id,
                            'match_score' => $match['score'],
                            'match_quality' => $match['quality'],
                            'matched_criteria' => $match['match_criteria'],
                            'student_id' => $student->id
                        ],
                        'matching'
                    );
                }
            }

            if (!empty($savedMatches)) {
                $student->studentProfile->update([
                    'attachment_status' => 'applied'
                ]);

                // Log status change
                activity_log(
                    'Student status updated to applied after matching',
                    'status_changed',
                    [
                        'student_id' => $student->id,
                        'old_status' => 'seeking',
                        'new_status' => 'applied',
                        'matches_count' => count($savedMatches)
                    ],
                    'student_profile'
                );
            }

            DB::commit();

            // Log the batch activity
            activity_log(
                'System matches created for student: ' . $student->full_name,
                'matches_created',
                [
                    'matches_count' => count($savedMatches),
                    'student_id' => $student->id,
                    'opportunity_ids' => collect($savedMatches)->pluck('attachment_opportunity_id')->toArray(),
                    'match_qualities' => collect($savedMatches)->pluck('match_quality')->toArray()
                ],
                'matching'
            );

            return $savedMatches;
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the failure
            activity_log(
                'Failed to save matches for student: ' . $student->full_name,
                'matches_failed',
                [
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                    'matches_attempted' => count($matches)
                ],
                'matching'
            );

            Log::error('Failed to save matches: ' . $e->getMessage());
            throw $e;
        }
    }
}
