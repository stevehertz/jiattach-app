<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\AttachmentOpportunity;

class AttachmentOpportunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = Organization::all();

        if ($organizations->isEmpty()) {
            $this->call(OrganizationSeeder::class);
            $organizations = Organization::all();
        }

        foreach ($organizations as $org) {
            // Create 2 opportunities per organization
            for ($i = 1; $i <= 2; $i++) {
                AttachmentOpportunity::create([
                    'organization_id' => $org->id,
                    'title' => 'Software Developer Intern ' . $i,
                    'slug' => \Illuminate\Support\Str::slug($org->name . ' dev intern ' . $i . ' ' . uniqid()),
                    'description' => 'We are looking for a passionate software developer intern.',
                    'responsibilities' => '<ul><li>Write code</li><li>Test applications</li></ul>',
                    'type' => 'internship',
                    'work_type' => 'onsite',
                    'location' => 'Nairobi',
                    'county' => 'Nairobi',
                    'min_gpa' => 3.0,
                    'skills_required' => ['PHP', 'Laravel', 'JavaScript'],
                    'courses_required' => ['BS Computer Science', 'BS IT'],
                    'start_date' => now()->addWeeks(2),
                    'end_date' => now()->addMonths(3),
                    'duration_months' => 3,
                    'stipend' => 15000,
                    'slots_available' => 2,
                    'deadline' => now()->addWeek(),
                    'status' => 'open',
                ]);
            }
        }
    }
}
