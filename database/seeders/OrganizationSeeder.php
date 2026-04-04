<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\DB;

class OrganizationSeeder extends Seeder
{
    protected $faker;
    protected $attachedUsers = []; // Track which users are attached to which organizations

    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear the organization_user table to start fresh
        DB::table('organization_user')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $targetRole = 'employer';

        // First, ensure we have the employer role
        $employerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'employer', 'guard_name' => 'web']);

        // Create organizations with different industries
        $industries = [
            'Technology' => 5,
            'Construction' => 5,
            'Finance' => 3,
            'Healthcare' => 3,
            'Hospitality' => 2,
            'Agriculture' => 2,
        ];

        $totalOrganizations = 0;
        $this->attachedUsers = [];

        foreach ($industries as $industry => $count) {
            $organizations = Organization::factory($count)
                ->verified()
                ->active()
                ->byIndustry($industry)
                ->create();

            foreach ($organizations as $organization) {
                // Create a unique owner for this organization
                $owner = $this->createUniqueUser($employerRole);

                // Attach owner to organization - check if already attached
                if (!$this->isUserAttachedToOrganization($owner->id, $organization->id)) {
                    $organization->users()->syncWithoutDetaching([$owner->id => [
                        'role' => 'owner',
                        'position' => $this->getRandomPosition($industry, 'owner'),
                        'is_primary_contact' => true,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]]);
                    $this->markUserAttached($owner->id, $organization->id);
                }

                // Add 2-4 staff members with unique emails
                $staffCount = rand(2, 4);
                for ($i = 0; $i < $staffCount; $i++) {
                    // Create a unique staff user
                    $staff = $this->createUniqueUser($employerRole);

                    // Attach staff to organization - check if already attached
                    if (!$this->isUserAttachedToOrganization($staff->id, $organization->id)) {
                        $organization->users()->syncWithoutDetaching([$staff->id => [
                            'role' => 'admin',
                            'position' => $this->getRandomPosition($industry, 'staff'),
                            'is_primary_contact' => false,
                            'is_active' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]]);
                        $this->markUserAttached($staff->id, $organization->id);
                    }
                }

                $totalOrganizations++;
            }
        }

        $this->command->info('Organizations seeded: ' . $totalOrganizations);
        $this->command->info('Total users created: ' . User::count());
        $this->command->info('Total employer users: ' . User::role('employer')->count());

        // Show some statistics about organization-user relationships
        $orgUserCount = DB::table('organization_user')->count();
        $this->command->info('Total organization-user relationships: ' . $orgUserCount);
    }

    /**
     * Create a unique user with unique email and phone
     */
    private function createUniqueUser($role)
    {
        $email = $this->generateUniqueEmail();
        $phone = $this->generateUniquePhone();

        $user = User::factory()->create([
            'email' => $email,
            'phone' => $phone,
            'national_id' => $this->generateUniqueNationalId(),
        ]);

        $user->assignRole($role);

        return $user;
    }

    /**
     * Generate a unique email
     */
    private function generateUniqueEmail()
    {
        $domains = ['company.com', 'org.co.ke', 'enterprise.ke', 'business.co.ke', 'firm.ke', 'industries.ke'];
        $name = $this->faker->userName() . rand(100, 999);
        $domain = $this->faker->randomElement($domains);

        return $name . '@' . $domain;
    }

    /**
     * Generate a unique phone number
     */
    private function generateUniquePhone()
    {
        // Generate Kenyan phone numbers
        $prefixes = ['0710', '0711', '0712', '0713', '0714', '0715', '0716', '0717', '0718', '0719',
                     '0720', '0721', '0722', '0723', '0724', '0725', '0726', '0727', '0728', '0729',
                     '0730', '0731', '0732', '0733', '0734', '0735', '0736', '0737', '0738', '0739',
                     '0740', '0741', '0742', '0743', '0744', '0745', '0746', '0747', '0748', '0749'];

        $prefix = $this->faker->randomElement($prefixes);
        $number = $this->faker->numerify('######');

        return '+' . $prefix . $number;
    }

    /**
     * Generate a unique national ID
     */
    private function generateUniqueNationalId()
    {
        return $this->faker->unique()->numerify('########');
    }

    /**
     * Check if a user is already attached to an organization
     */
    private function isUserAttachedToOrganization($userId, $organizationId)
    {
        $key = $userId . '-' . $organizationId;
        return isset($this->attachedUsers[$key]);
    }

    /**
     * Mark a user as attached to an organization
     */
    private function markUserAttached($userId, $organizationId)
    {
        $key = $userId . '-' . $organizationId;
        $this->attachedUsers[$key] = true;
    }

    private function getRandomPosition($industry, $type = 'staff')
    {
        $ownerPositions = [
            'Technology' => ['CTO', 'CEO', 'Technical Director', 'Founder', 'President', 'Chief Technology Officer'],
            'Construction' => ['Managing Director', 'CEO', 'Director of Construction', 'Founder', 'President', 'Chief Operations Officer'],
            'Finance' => ['CEO', 'Finance Director', 'Managing Partner', 'Founder', 'President', 'Chief Financial Officer'],
            'Healthcare' => ['Medical Director', 'CEO', 'Managing Director', 'Founder', 'Chief Medical Officer', 'Hospital Administrator'],
            'Hospitality' => ['CEO', 'General Manager', 'Managing Director', 'Founder', 'President', 'Chief Operating Officer'],
            'Agriculture' => ['CEO', 'Farm Director', 'Managing Director', 'Founder', 'President', 'Chief Agricultural Officer'],
        ];

        $staffPositions = [
            'Technology' => ['Tech Lead', 'Senior Developer', 'HR Manager', 'Project Manager', 'Software Engineer', 'System Administrator', 'DevOps Engineer', 'QA Engineer', 'UI/UX Designer'],
            'Construction' => ['Project Manager', 'Site Supervisor', 'Quantity Surveyor', 'Construction Manager', 'Safety Officer', 'Civil Engineer', 'Architect', 'Structural Engineer', 'Site Foreman'],
            'Finance' => ['Finance Manager', 'Accountant', 'Financial Analyst', 'Compliance Officer', 'Auditor', 'Investment Analyst', 'Risk Manager', 'Credit Analyst', 'Tax Specialist'],
            'Healthcare' => ['Nursing Manager', 'HR Coordinator', 'Administrator', 'Clinical Officer', 'Lab Manager', 'Pharmacy Manager', 'Radiology Tech', 'Physiotherapist', 'Nutritionist'],
            'Hospitality' => ['Operations Manager', 'Front Office Manager', 'HR Manager', 'Event Coordinator', 'Chef', 'Restaurant Manager', 'Housekeeping Manager', 'Sales Manager', 'Concierge'],
            'Agriculture' => ['Farm Manager', 'Agronomist', 'Operations Manager', 'Quality Control Manager', 'Research Officer', 'Extension Officer', 'Procurement Officer', 'Logistics Coordinator', 'Irrigation Specialist'],
        ];

        if ($type === 'owner') {
            $positions = $ownerPositions[$industry] ?? ['Director', 'CEO', 'Managing Director', 'Founder'];
        } else {
            $positions = $staffPositions[$industry] ?? ['Manager', 'Supervisor', 'Coordinator', 'Assistant', 'Specialist'];
        }

        return $this->faker->randomElement($positions);
    }
}
