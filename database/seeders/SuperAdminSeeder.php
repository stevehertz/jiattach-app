<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // 1. Ensure the Role exists (Safety check)
        // We use 'web' guard usually, but Spatie defaults to config
        $roleName = 'super-admin';
        if (!Role::where('name', $roleName)->exists()) {
            Role::create(['name' => $roleName]);
        }

         // 2. Create the Admin User
        // We use firstOrCreate to prevent duplicates if you run db:seed multiple times
        $admin = User::firstOrCreate(
            ['email' => 'admin@jiattach.com'], // Check this email
            [
                'first_name' => 'System',
                'last_name'  => 'Admin',
                'password'   => Hash::make('password'), // Change this in production!
                
                // Required Personal Info (using reserved system values)
                'phone'         => '0700000000',
                'national_id'   => 'ADMIN001',
                'date_of_birth' => '1990-01-01',
                'gender'        => 'prefer_not_to_say',
                
                // Location Defaults
                'county'       => 'Nairobi',
                'constituency' => 'Central',
                'ward'         => 'Central Business District',
                
                // Disability Status (Default to none)
                'disability_status' => 'none',
                'disability_details' => null,
                
                // Bio
                'bio' => 'Super Administrator for Jiattach Platform.',
                
                // Account Status
                'is_active'          => true,
                'is_verified'        => true,
                'email_verified_at'  => Carbon::now(),
                'verification_token' => null, 
                'last_login_at'      => Carbon::now(),
            ]
        );

        // 3. Assign the Spatie Role
        if (!$admin->hasRole($roleName)) {
            $admin->assignRole($roleName);
            $this->command->info('Super Admin role assigned successfully.');
        } else {
            $this->command->info('User already has Super Admin role.');
        }

        $this->command->info('Super Admin created! Email: admin@jiattach.com | Password: password');
    }
}
