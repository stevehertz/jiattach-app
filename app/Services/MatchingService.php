<?php

namespace App\Services;

use App\Models\User;
use App\Models\AttachmentOpportunity;

class MatchingService
{
    /**
     * Calculate match score between a Student and an Opportunity
     */
    public function calculateScore(User $student, AttachmentOpportunity $opportunity): float
    {
        if (!$student->hasRole('student') || !$student->studentProfile) {
            return 0;
        }

        $profile = $student->studentProfile;
        $score = 0;
        $totalWeight = 100;

        // 1. Course Relevance (40%)
        // Check if student's course name roughly matches required courses
        // Note: In a production system, use IDs. Here we use string matching for flexibility.
        $courseMatch = false;
        if (!empty($opportunity->courses_required)) {
            foreach ($opportunity->courses_required as $reqCourse) {
                if (stripos($profile->course_name, $reqCourse) !== false) {
                    $courseMatch = true;
                    break;
                }
            }
        } else {
            // If no specific course required, give partial points
            $courseMatch = true;
            $score -= 20; // Penalty for generic application
        }

        if ($courseMatch) $score += 40;

        // 2. GPA/Performance (20%)
        if ($opportunity->min_gpa) {
            if ($profile->cgpa >= $opportunity->min_gpa) {
                $score += 20;
            } else {
                // Partial credit if close (within 0.5)
                if (($opportunity->min_gpa - $profile->cgpa) <= 0.5) {
                    $score += 10;
                }
            }
        } else {
            $score += 20; // No GPA requirement = full points
        }

        // 3. Location (20%)
        $locationMatch = false;
        if ($opportunity->work_type === 'remote') {
            $locationMatch = true;
        } elseif ($student->county === $opportunity->county) {
            $locationMatch = true;
        } elseif (str_contains(strtolower($profile->preferred_location), strtolower($opportunity->location ?? ''))) {
            $locationMatch = true;
        }

        if ($locationMatch) $score += 20;

        // 4. Skills (20%)
        if (!empty($opportunity->skills_required) && !empty($profile->skills)) {
            $requiredSkills = array_map('strtolower', $opportunity->skills_required);
            $studentSkills = array_map('strtolower', $profile->skills);

            $intersect = array_intersect($requiredSkills, $studentSkills);
            $skillCount = count($opportunity->skills_required);

            if ($skillCount > 0) {
                $skillRatio = count($intersect) / $skillCount;
                $score += min(20, $skillRatio * 20);
            }
        }

        return min(100, max(0, $score));
    }
}
