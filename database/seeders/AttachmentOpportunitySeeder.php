<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\AttachmentOpportunity;
use App\Models\Course;

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

        $opportunitiesCount = 0;

        foreach ($organizations as $org) {
            // Create opportunities based on organization industry
            $industryOpportunities = $this->getOpportunitiesByIndustry($org->industry);

            foreach ($industryOpportunities as $oppData) {
                // Get courses based on the opportunity type
                $courses = $this->getCoursesForOpportunity($oppData['type'], $oppData['industry']);
                $courseNames = $courses->pluck('name')->toArray();

                // Use Eloquent create() which will properly handle array casting
                AttachmentOpportunity::create([
                    'organization_id' => $org->id,
                    'title' => $oppData['title'],
                    'description' => $oppData['description'],
                    'responsibilities' => $oppData['responsibilities'],
                    'type' => 'internship',
                    'work_type' => $this->getRandomWorkType(),
                    'location' => $org->county,
                    'county' => $org->county,
                    'min_gpa' => $this->getRandomGpa(),
                    'skills_required' => $oppData['skills'],
                    'courses_required' => $courseNames, // This will be automatically JSON encoded
                    'start_date' => now()->addWeeks(rand(2, 6)),
                    'end_date' => now()->addMonths(rand(3, 6)),
                    'duration_months' => rand(3, 6),
                    'stipend' => $this->getRandomStipend(),
                    'slots_available' => rand(2, 8),
                    'deadline' => now()->addWeeks(rand(1, 4)),
                    'status' => $this->getRandomStatus(),
                ]);

                $opportunitiesCount++;
            }
        }

        $this->command->info('Attachment opportunities seeded: ' . $opportunitiesCount);
    }

    private function getOpportunitiesByIndustry($industry)
    {
        $opportunities = [
            'Technology' => [
                [
                    'title' => 'Software Development Intern',
                    'description' => 'Join our tech team to work on exciting projects using modern technologies.',
                    'responsibilities' => '<ul><li>Write clean, maintainable code</li><li>Participate in code reviews</li><li>Debug and test applications</li><li>Collaborate with senior developers</li></ul>',
                    'skills' => ['PHP', 'Laravel', 'JavaScript', 'React', 'Git', 'MySQL'],
                    'type' => 'software',
                    'industry' => 'Technology'
                ],
                [
                    'title' => 'IT Support Intern',
                    'description' => 'Provide technical support and assist with IT infrastructure management.',
                    'responsibilities' => '<ul><li>Provide technical support to staff</li><li>Manage IT assets</li><li>Assist with network maintenance</li><li>Document IT procedures</li></ul>',
                    'skills' => ['Networking', 'Windows', 'Linux', 'Hardware', 'Troubleshooting', 'Customer Service'],
                    'type' => 'it_support',
                    'industry' => 'Technology'
                ],
                [
                    'title' => 'Data Science Intern',
                    'description' => 'Work with data scientists to analyze data and build machine learning models.',
                    'responsibilities' => '<ul><li>Clean and analyze datasets</li><li>Build predictive models</li><li>Create data visualizations</li><li>Present findings to stakeholders</li></ul>',
                    'skills' => ['Python', 'SQL', 'Machine Learning', 'Statistics', 'Data Visualization', 'Pandas'],
                    'type' => 'data_science',
                    'industry' => 'Technology'
                ]
            ],
            'Construction' => [
                [
                    'title' => 'Civil Engineering Intern',
                    'description' => 'Assist in construction projects, site supervision, and project planning.',
                    'responsibilities' => '<ul><li>Assist in site supervision</li><li>Help with project documentation</li><li>Conduct quality control checks</li><li>Support project managers</li></ul>',
                    'skills' => ['AutoCAD', 'Project Management', 'Site Supervision', 'Quality Control', 'MS Project'],
                    'type' => 'civil',
                    'industry' => 'Construction'
                ],
                [
                    'title' => 'Quantity Surveying Intern',
                    'description' => 'Learn cost estimation, procurement, and contract administration.',
                    'responsibilities' => '<ul><li>Assist in cost estimation</li><li>Help with procurement</li><li>Support contract administration</li><li>Maintain cost records</li></ul>',
                    'skills' => ['Cost Estimation', 'Procurement', 'Contract Management', 'MS Excel', 'Quantity Takeoff'],
                    'type' => 'quantity_surveying',
                    'industry' => 'Construction'
                ],
                [
                    'title' => 'Architecture Intern',
                    'description' => 'Work with architects on design projects and construction documentation.',
                    'responsibilities' => '<ul><li>Create design drawings</li><li>Assist in 3D modeling</li><li>Prepare presentation materials</li><li>Support design reviews</li></ul>',
                    'skills' => ['AutoCAD', 'Revit', 'SketchUp', 'Photoshop', '3D Modeling', 'Design'],
                    'type' => 'architecture',
                    'industry' => 'Construction'
                ]
            ],
            'Finance' => [
                [
                    'title' => 'Finance Intern',
                    'description' => 'Assist with financial analysis, reporting, and accounting tasks.',
                    'responsibilities' => '<ul><li>Assist with financial statements</li><li>Support audit processes</li><li>Analyze financial data</li><li>Prepare reports</li></ul>',
                    'skills' => ['Excel', 'Financial Analysis', 'Accounting', 'QuickBooks', 'Data Analysis'],
                    'type' => 'finance',
                    'industry' => 'Finance'
                ]
            ],
            'Healthcare' => [
                [
                    'title' => 'Healthcare Administration Intern',
                    'description' => 'Support healthcare operations and administrative functions.',
                    'responsibilities' => '<ul><li>Assist with patient records</li><li>Support administrative tasks</li><li>Help with scheduling</li><li>Coordinate with departments</li></ul>',
                    'skills' => ['Medical Terminology', 'Healthcare Systems', 'Organization', 'Communication'],
                    'type' => 'healthcare',
                    'industry' => 'Healthcare'
                ]
            ],
            'Hospitality' => [
                [
                    'title' => 'Hospitality Management Intern',
                    'description' => 'Learn hotel operations, customer service, and event management.',
                    'responsibilities' => '<ul><li>Assist with front desk operations</li><li>Support event planning</li><li>Help with customer service</li><li>Learn hospitality systems</li></ul>',
                    'skills' => ['Customer Service', 'Event Planning', 'Hotel Management', 'Communication'],
                    'type' => 'hospitality',
                    'industry' => 'Hospitality'
                ]
            ],
            'Agriculture' => [
                [
                    'title' => 'Agricultural Intern',
                    'description' => 'Work on agricultural projects, research, and farm management.',
                    'responsibilities' => '<ul><li>Assist with farm operations</li><li>Support research projects</li><li>Help with crop management</li><li>Collect and analyze data</li></ul>',
                    'skills' => ['Agriculture', 'Research', 'Data Collection', 'Farm Management'],
                    'type' => 'agriculture',
                    'industry' => 'Agriculture'
                ]
            ]
        ];

        return $opportunities[$industry] ?? [
            [
                'title' => 'General Intern',
                'description' => 'Learn various aspects of our business operations.',
                'responsibilities' => '<ul><li>Support daily operations</li><li>Assist team members</li><li>Learn business processes</li><li>Complete assigned tasks</li></ul>',
                'skills' => ['Communication', 'Teamwork', 'Organization', 'MS Office'],
                'type' => 'general',
                'industry' => $industry
            ]
        ];
    }

    private function getCoursesForOpportunity($type, $industry)
    {
        $query = Course::query();

        switch ($industry) {
            case 'Technology':
                $query->whereIn('category', ['ict', 'engineering']);
                break;
            case 'Construction':
                $query->whereIn('category', ['construction', 'engineering']);
                break;
            case 'Finance':
                $query->whereIn('category', ['business']);
                break;
            case 'Healthcare':
                $query->whereIn('category', ['health']);
                break;
            case 'Hospitality':
                $query->whereIn('category', ['hospitality']);
                break;
            case 'Agriculture':
                $query->whereIn('category', ['agriculture', 'environment']);
                break;
            default:
                $query->whereIn('category', ['business', 'ict', 'engineering']);
        }

        return $query->inRandomOrder()->limit(rand(2, 4))->get();
    }

    private function getRandomWorkType()
    {
        $types = ['onsite', 'hybrid', 'remote'];
        return $types[array_rand($types)];
    }

    private function getRandomGpa()
    {
        $options = [null, 2.5, 3.0, 3.5];
        return $options[array_rand($options)];
    }

    private function getRandomStipend()
    {
        $stipends = [5000, 10000, 15000, 20000, 25000, 30000];
        return $stipends[array_rand($stipends)];
    }

    private function getRandomStatus()
    {
        $statuses = ['open', 'open', 'open', 'open', 'closed']; // More open than closed
        return $statuses[array_rand($statuses)];
    }
}
