<?php

namespace App\Services;

use App\Models\Application;
use App\Models\AttachmentOpportunity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentMatchingService
{
    /**
     * Find matching opportunities for a specific student
     */
    public function findMatchesForStudent(User $student, int $limit = 10): array
    {
        if (!$student->hasRole('student') || !$student->studentProfile) {
            return [];
        }

        $profile = $student->studentProfile;

        // Get active opportunities
        $opportunities = AttachmentOpportunity::where('status', 'open')
            ->where('deadline', '>=', now())
            ->with('organization')
            ->get();

        $matches = [];

        foreach ($opportunities as $opportunity) {
            $score = $this->calculateMatchScore($student, $profile, $opportunity);

            // Only consider matches above 50%
            if ($score >= 50) {
                $matches[] = [
                    'opportunity' => $opportunity,
                    'score' => $score,
                    'details' => $this->getMatchDetails($student, $profile, $opportunity)
                ];
            }
        }

        // Sort by score descending
        usort($matches, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($matches, 0, $limit);
    }

    /**
     * Calculate match score based on your algorithm
     * Score = Course 40% + GPA 20% + Location 20% + Skills 20%
     */
    public function calculateMatchScore(User $student, $profile, AttachmentOpportunity $opportunity): float
    {
        $score = 0;
        $totalWeight = 0;

        // Course Match (40%)
        $courseWeight = 40;
        $courseScore = $this->calculateCourseMatch($profile->course_name, $opportunity->courses_required ?? []);
        $score += ($courseScore * $courseWeight);
        $totalWeight += $courseWeight;

        // GPA Match (20%)
        $gpaWeight = 20;
        $gpaScore = $this->calculateGpaMatch($profile->cgpa, $opportunity->min_gpa);
        $score += ($gpaScore * $gpaWeight);
        $totalWeight += $gpaWeight;

        // Location Match (20%)
        $locationWeight = 20;
        $locationScore = $this->calculateLocationMatch(
            $profile->preferred_location ?? $student->county,
            $opportunity->location ?? $opportunity->county
        );
        $score += ($locationScore * $locationWeight);
        $totalWeight += $locationWeight;

        // Skills Match (20%)
        $skillsWeight = 20;
        $skillsScore = $this->calculateSkillsMatch($profile->skills ?? [], $opportunity->skills_required ?? []);
        $score += ($skillsScore * $skillsWeight);
        $totalWeight += $skillsWeight;

        // Return weighted percentage
        return $totalWeight > 0 ? round(($score / $totalWeight), 2) : 0;
    }

    /**
     * Calculate course match score (0-100)
     */
    private function calculateCourseMatch(string $studentCourse, ?array $requiredCourses): float
    {
        if (empty($requiredCourses)) {
            return 100; // No course requirement = full marks
        }

        // Convert to lowercase for comparison
        $studentCourseLower = strtolower($studentCourse);

        foreach ($requiredCourses as $required) {
            $requiredLower = strtolower($required);

            // Exact match
            if ($studentCourseLower === $requiredLower) {
                return 100;
            }

            // Partial match (e.g., "Computer Science" matches "Computer")
            if (
                str_contains($studentCourseLower, $requiredLower) ||
                str_contains($requiredLower, $studentCourseLower)
            ) {
                return 80;
            }
        }

        return 0;
    }

    /**
     * Calculate GPA match score (0-100)
     */
    private function calculateGpaMatch(?float $studentGpa, ?float $minGpa): float
    {
        if (!$minGpa) {
            return 100; // No minimum requirement
        }

        if (!$studentGpa || $studentGpa < $minGpa) {
            return 0; // Below minimum
        }

        // Calculate how much above minimum (max 4.0)
        $gpaRange = 4.0 - $minGpa;
        if ($gpaRange <= 0) {
            return 100;
        }

        $studentAdvantage = $studentGpa - $minGpa;
        $score = min(100, ($studentAdvantage / $gpaRange) * 100 + 50);

        return round($score, 2);
    }

    /**
     * Calculate location match score (0-100)
     */
    private function calculateLocationMatch(?string $studentLocation, ?string $opportunityLocation): float
    {
        if (!$studentLocation || !$opportunityLocation) {
            return 50; // Partial match if one is missing
        }

        $studentLocation = strtolower($studentLocation);
        $opportunityLocation = strtolower($opportunityLocation);

        // Exact match
        if ($studentLocation === $opportunityLocation) {
            return 100;
        }

        // Check if student location is within opportunity location or vice versa
        if (
            str_contains($opportunityLocation, $studentLocation) ||
            str_contains($studentLocation, $opportunityLocation)
        ) {
            return 75;
        }

        return 0;
    }

    /**
     * Calculate skills match score (0-100)
     */
    private function calculateSkillsMatch(array $studentSkills, array $requiredSkills): float
    {
        if (empty($requiredSkills)) {
            return 100;
        }

        if (empty($studentSkills)) {
            return 0;
        }

        $studentSkillsLower = array_map('strtolower', $studentSkills);
        $requiredSkillsLower = array_map('strtolower', $requiredSkills);

        $matchedSkills = 0;
        foreach ($requiredSkillsLower as $requiredSkill) {
            foreach ($studentSkillsLower as $studentSkill) {
                if (
                    str_contains($studentSkill, $requiredSkill) ||
                    str_contains($requiredSkill, $studentSkill)
                ) {
                    $matchedSkills++;
                    break;
                }
            }
        }

        return round(($matchedSkills / count($requiredSkills)) * 100, 2);
    }

    /**
     * Get detailed breakdown of match
     */
    public function getMatchDetails(User $student, $profile, AttachmentOpportunity $opportunity): array
    {
        return [
            'course' => [
                'score' => $this->calculateCourseMatch($profile->course_name, $opportunity->courses_required ?? []),
                'weight' => 40
            ],
            'gpa' => [
                'score' => $this->calculateGpaMatch($profile->cgpa, $opportunity->min_gpa),
                'weight' => 20
            ],
            'location' => [
                'score' => $this->calculateLocationMatch(
                    $profile->preferred_location ?? $student->county,
                    $opportunity->location ?? $opportunity->county
                ),
                'weight' => 20
            ],
            'skills' => [
                'score' => $this->calculateSkillsMatch($profile->skills ?? [], $opportunity->skills_required ?? []),
                'weight' => 20
            ]
        ];
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
                $existingApplication = Application::where('user_id', $student->id)
                    ->where('attachment_opportunity_id', $match['opportunity']->id)
                    ->first();

                if (!$existingApplication) {
                    $application = Application::create([
                        'user_id' => $student->id,
                        'student_id' => $student->id,
                        'attachment_opportunity_id' => $match['opportunity']->id,
                        'match_score' => $match['score'],
                        'status' => 'pending',
                        'submitted_at' => now(),
                    ]);

                    $savedMatches[] = $application;

                    // Log individual match creation (optional - can be verbose)
                    activity_log(
                        "Match created: {$match['opportunity']->title} ({$match['score']}%)",
                        'match_created',
                        [
                            'application_id' => $application->id,
                            'opportunity_id' => $match['opportunity']->id,
                            'match_score' => $match['score'],
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

            // Log the batch activity using your custom helper
            activity_log(
                'System matches created for student: ' . $student->full_name,
                'matches_created',
                [
                    'matches_count' => count($savedMatches),
                    'student_id' => $student->id,
                    'opportunity_ids' => collect($savedMatches)->pluck('attachment_opportunity_id')->toArray()
                ],
                'matching' // log_name
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

    /**
     * Run matching for all seeking students (for cron job)
     */
    public function runMatchingForAllSeekingStudents(): array
    {
        $results = [
            'total_processed' => 0,
            'matches_created' => 0,
            'errors' => []
        ];

        // Get all students seeking attachment
        $students = User::role('student')
            ->whereHas('studentProfile', function ($query) {
                $query->where('attachment_status', 'seeking');
            })
            ->with('studentProfile')
            ->get();

        foreach ($students as $student) {
            try {
                $matches = $this->findMatchesForStudent($student, 5); // Top 5 matches

                if (!empty($matches)) {
                    $saved = $this->saveMatchesForStudent($student, $matches);
                    $results['matches_created'] += count($saved);
                }

                $results['total_processed']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Student {$student->id}: " . $e->getMessage();
            }
        }

        return $results;
    }
}
