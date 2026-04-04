<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Clear existing courses
        Course::truncate();

        $courses = [
            // ============ ENGINEERING COURSES ============
            ['name' => 'Civil Engineering', 'code' => 'CE101', 'category' => 'engineering'],
            ['name' => 'Mechanical Engineering', 'code' => 'ME101', 'category' => 'engineering'],
            ['name' => 'Electrical Engineering', 'code' => 'EE101', 'category' => 'engineering'],
            ['name' => 'Electronic Engineering', 'code' => 'ECE101', 'category' => 'engineering'],
            ['name' => 'Chemical Engineering', 'code' => 'CHE101', 'category' => 'engineering'],
            ['name' => 'Petroleum Engineering', 'code' => 'PE101', 'category' => 'engineering'],
            ['name' => 'Software Engineering', 'code' => 'SE101', 'category' => 'engineering'],
            ['name' => 'Computer Engineering', 'code' => 'CE201', 'category' => 'engineering'],
            ['name' => 'Mechatronic Engineering', 'code' => 'MTE101', 'category' => 'engineering'],

            // ============ CONSTRUCTION & BUILDING COURSES ============
            ['name' => 'Construction Management', 'code' => 'CM101', 'category' => 'construction'],
            ['name' => 'Building Construction', 'code' => 'BC101', 'category' => 'construction'],
            ['name' => 'Quantity Surveying', 'code' => 'QS101', 'category' => 'construction'],
            ['name' => 'Architecture', 'code' => 'ARCH101', 'category' => 'construction'],
            ['name' => 'Land Surveying', 'code' => 'LS101', 'category' => 'construction'],
            ['name' => 'Urban and Regional Planning', 'code' => 'URP101', 'category' => 'construction'],
            ['name' => 'Construction Technology', 'code' => 'CT101', 'category' => 'construction'],
            ['name' => 'Building Technology', 'code' => 'BT101', 'category' => 'construction'],
            ['name' => 'Interior Design', 'code' => 'ID101', 'category' => 'construction'],
            ['name' => 'Structural Engineering', 'code' => 'SE201', 'category' => 'construction'],

            // ============ ICT & COMPUTER SCIENCE ============
            ['name' => 'Computer Science', 'code' => 'CS101', 'category' => 'ict'],
            ['name' => 'Information Technology', 'code' => 'IT101', 'category' => 'ict'],
            ['name' => 'Information Systems', 'code' => 'IS101', 'category' => 'ict'],
            ['name' => 'Data Science', 'code' => 'DS101', 'category' => 'ict'],
            ['name' => 'Cybersecurity', 'code' => 'CSEC101', 'category' => 'ict'],
            ['name' => 'Network Administration', 'code' => 'NA101', 'category' => 'ict'],
            ['name' => 'Web Development', 'code' => 'WD101', 'category' => 'ict'],
            ['name' => 'Mobile Application Development', 'code' => 'MAD101', 'category' => 'ict'],
            ['name' => 'Cloud Computing', 'code' => 'CC101', 'category' => 'ict'],

            // ============ BUSINESS COURSES ============
            ['name' => 'Business Administration', 'code' => 'BA101', 'category' => 'business'],
            ['name' => 'Accounting', 'code' => 'ACC101', 'category' => 'business'],
            ['name' => 'Finance', 'code' => 'FIN101', 'category' => 'business'],
            ['name' => 'Marketing', 'code' => 'MKT101', 'category' => 'business'],
            ['name' => 'Human Resource Management', 'code' => 'HRM101', 'category' => 'business'],
            ['name' => 'Economics', 'code' => 'ECO101', 'category' => 'business'],
            ['name' => 'Entrepreneurship', 'code' => 'ENT101', 'category' => 'business'],
            ['name' => 'Supply Chain Management', 'code' => 'SCM101', 'category' => 'business'],
            ['name' => 'Project Management', 'code' => 'PM101', 'category' => 'business'],

            // ============ HEALTH SCIENCES ============
            ['name' => 'Medicine', 'code' => 'MED101', 'category' => 'health'],
            ['name' => 'Nursing', 'code' => 'NUR101', 'category' => 'health'],
            ['name' => 'Pharmacy', 'code' => 'PHA101', 'category' => 'health'],
            ['name' => 'Clinical Medicine', 'code' => 'CM201', 'category' => 'health'],
            ['name' => 'Public Health', 'code' => 'PH101', 'category' => 'health'],
            ['name' => 'Dentistry', 'code' => 'DEN101', 'category' => 'health'],
            ['name' => 'Medical Laboratory Science', 'code' => 'MLS101', 'category' => 'health'],
            ['name' => 'Physiotherapy', 'code' => 'PT101', 'category' => 'health'],
            ['name' => 'Nutrition and Dietetics', 'code' => 'ND101', 'category' => 'health'],

            // ============ HOSPITALITY & TOURISM ============
            ['name' => 'Hospitality Management', 'code' => 'HM101', 'category' => 'hospitality'],
            ['name' => 'Tourism Management', 'code' => 'TM101', 'category' => 'hospitality'],
            ['name' => 'Culinary Arts', 'code' => 'CA101', 'category' => 'hospitality'],
            ['name' => 'Hotel Management', 'code' => 'HOT101', 'category' => 'hospitality'],
            ['name' => 'Event Management', 'code' => 'EM101', 'category' => 'hospitality'],

            // ============ AGRICULTURE ============
            ['name' => 'Agriculture', 'code' => 'AGR101', 'category' => 'agriculture'],
            ['name' => 'Horticulture', 'code' => 'HORT101', 'category' => 'agriculture'],
            ['name' => 'Agribusiness', 'code' => 'AGB101', 'category' => 'agriculture'],
            ['name' => 'Animal Science', 'code' => 'AS101', 'category' => 'agriculture'],
            ['name' => 'Environmental Science', 'code' => 'ENV101', 'category' => 'agriculture'],
            ['name' => 'Forestry', 'code' => 'FOR101', 'category' => 'agriculture'],

            // ============ EDUCATION ============
            ['name' => 'Education', 'code' => 'EDU101', 'category' => 'education'],
            ['name' => 'Early Childhood Education', 'code' => 'ECE201', 'category' => 'education'],
            ['name' => 'Special Education', 'code' => 'SPED101', 'category' => 'education'],
            ['name' => 'Educational Psychology', 'code' => 'EPSY101', 'category' => 'education'],

            // ============ ARTS & SOCIAL SCIENCES ============
            ['name' => 'Communication and Media', 'code' => 'COM101', 'category' => 'arts'],
            ['name' => 'Journalism', 'code' => 'JOUR101', 'category' => 'arts'],
            ['name' => 'Psychology', 'code' => 'PSY101', 'category' => 'social_science'],
            ['name' => 'Sociology', 'code' => 'SOC101', 'category' => 'social_science'],
            ['name' => 'Law', 'code' => 'LAW101', 'category' => 'social_science'],
            ['name' => 'International Relations', 'code' => 'IR101', 'category' => 'social_science'],
            ['name' => 'Graphic Design', 'code' => 'GD101', 'category' => 'arts'],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }

        $this->command->info('Courses seeded: ' . count($courses));
    }
}
