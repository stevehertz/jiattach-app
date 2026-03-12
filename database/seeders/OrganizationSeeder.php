<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $targetRole = 'employer';

        // Create 10 organizations with owners
        Organization::factory(10)
            ->verified()
            ->active()
            ->create()
            ->each(function ($organization) use ($targetRole) {
                // Get or create an owner user
                $owner = User::role($targetRole)
                    ->whereDoesntHave('organizations', function ($query) use ($organization) {
                        $query->where('role', 'owner');
                    })
                    ->inRandomOrder()
                    ->first();

                if (!$owner) {
                    $owner = User::factory()->create();
                    $owner->assignRole($targetRole);
                }

                // Attach owner
                $organization->users()->attach($owner->id, [
                    'role' => 'owner',
                    'position' => fake()->randomElement(['Director', 'CEO', 'Founder', 'Managing Director']),
                    'is_primary_contact' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Add additional staff (1-3 people)
                $staffCount = rand(1, 3);
                for ($i = 0; $i < $staffCount; $i++) {
                    $staff = User::role($targetRole)
                        ->whereDoesntHave('organizations')
                        ->inRandomOrder()
                        ->first();

                    if (!$staff) {
                        $staff = User::factory()->create();
                        $staff->assignRole($targetRole);
                    }

                    $organization->users()->attach($staff->id, [
                        'role' => 'admin',
                        'position' => fake()->randomElement(['HR Manager', 'Team Lead', 'Supervisor', 'Coordinator']),
                        'is_primary_contact' => false,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }
}
