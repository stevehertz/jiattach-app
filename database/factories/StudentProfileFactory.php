<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentProfile>
 */
class StudentProfileFactory extends Factory
{
    protected $model = StudentProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random course from the database
        $course = Course::inRandomOrder()->first();

        $institutionTypes = ['university', 'college', 'polytechnic', 'technical'];
        $courseLevels = ['certificate', 'diploma', 'bachelor', 'masters', 'phd'];
        $attachmentStatuses = ['seeking', 'applied', 'interviewing', 'placed', 'completed'];

        $yearOfStudy = $this->faker->numberBetween(1, 4);
        $currentYear = date('Y');
        $expectedGraduation = $currentYear + (4 - $yearOfStudy);

        $skills = $this->faker->randomElements([
            'PHP',
            'Laravel',
            'JavaScript',
            'React',
            'Vue.js',
            'Python',
            'Java',
            'SQL',
            'Git',
            'Docker',
            'AWS',
            'Project Management',
            'Communication',
            'Leadership',
            'Problem Solving',
            'Teamwork',
            'AutoCAD',
            'Revit',
            'Construction Management',
            'Quantity Surveying'
        ], rand(3, 6));

        $interests = $this->faker->randomElements([
            'Web Development',
            'Mobile Apps',
            'AI/ML',
            'Cloud Computing',
            'Construction',
            'Architecture',
            'Business Analysis',
            'Data Science',
            'Digital Marketing',
            'Graphic Design',
            'UI/UX Design',
            'Cybersecurity'
        ], rand(2, 4));

        return [
            'user_id' => User::factory()->create()->id,
            'student_reg_number' => strtoupper($this->faker->unique()->bothify('???#######')),
            'institution_name' => $this->faker->randomElement([
                'University of Nairobi',
                'Kenyatta University',
                'Jomo Kenyatta University of Agriculture and Technology',
                'Moi University',
                'Egerton University',
                'Strathmore University',
                'Daystar University',
                'Technical University of Kenya',
                'Multimedia University',
                'KCA University'
            ]),
            'institution_type' => $this->faker->randomElement($institutionTypes),
            'course_name' => $course ? $course->name : 'Computer Science',
            'course_level' => $this->faker->randomElement($courseLevels),
            'year_of_study' => $yearOfStudy,
            'expected_graduation_year' => $expectedGraduation,
            'cgpa' => $this->faker->randomFloat(2, 2.0, 4.0),
            'skills' => $skills,
            'interests' => $interests,
            'cv_url' => null,
            'transcript_url' => null,
            'school_letter_url' => null,
            'attachment_status' => $this->faker->randomElement($attachmentStatuses),
            'attachment_start_date' => null,
            'attachment_end_date' => null,
            'preferred_attachment_duration' => $this->faker->numberBetween(2, 6),
            'preferred_location' => $this->faker->randomElement(['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Remote']),
        ];
    }

    public function seeking(): static
    {
        return $this->state(fn(array $attributes) => [
            'attachment_status' => 'seeking',
        ]);
    }

    public function placed(): static
    {
        return $this->state(fn(array $attributes) => [
            'attachment_status' => 'placed',
            'attachment_start_date' => now(),
            'attachment_end_date' => now()->addMonths(3),
        ]);
    }

    public function withSpecificCourse(string $courseName): static
    {
        return $this->state(fn(array $attributes) => [
            'course_name' => $courseName,
        ]);
    }
}
