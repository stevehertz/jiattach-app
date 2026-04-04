<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Application;
use App\Models\AttachmentOpportunity;
use App\Models\Course;
use App\Models\Organization;
use App\Models\Placement;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear existing data
        $this->command->info('Clearing existing data...');

         // Truncate tables in correct order to avoid foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Application::truncate();
        Placement::truncate();
        AttachmentOpportunity::truncate();
        StudentProfile::truncate();
        Organization::truncate();
        User::truncate();
        Course::truncate();
        Role::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Starting fresh seeding...');

        $this->call([
            RoleSeeder::class,
            SuperAdminSeeder::class,
            CourseSeeder::class,
            OrganizationSeeder::class,
            AttachmentOpportunitySeeder::class,
        ]);
    }
}
