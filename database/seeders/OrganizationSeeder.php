<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // 1. Update the role name here to match what you actually use ('employer' or 'company')
        $targetRole = 'employer';

        $companyUsers = User::role($targetRole)->doesntHave('organization')->get();

        // 2. If no users exist, create them using the fixed Factory
        if ($companyUsers->isEmpty()) {
            $companyUsers = User::factory(5)->create()->each(function ($user) use ($targetRole) {
                $user->assignRole($targetRole);
            });
        }

        // 3. Create an organization for each user
        foreach ($companyUsers as $user) {
            Organization::create([
                'user_id' => $user->id,
                // Using first_name and last_name since 'name' doesn't exist on User
                'name' => $user->first_name . ' ' . $user->last_name . ' Solutions',
                'type' => 'company',
                'industry' => 'Technology',
                'email' => $user->email,
                'phone' => $user->phone ?? fake()->phoneNumber(),
                'website' => 'https://example.com',
                'address' => 'Nairobi, Kenya',
                'county' => 'Nairobi',
                'constituency' => 'Westlands',
                'ward' => 'Parklands',
                'description' => 'A leading technology firm specializing in software development.',
                'max_students_per_intake' => 5,
                'is_active' => true,
                'is_verified' => true,
                'verified_at' => now(),
            ]);
        }
    }
}
