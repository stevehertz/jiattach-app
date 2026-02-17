<?php

namespace Database\Seeders;

use App\Models\SecuritySetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SecuritySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         $settings = [
            // Password Policy Settings
            [
                'key' => 'password.min_length',
                'name' => 'Minimum Password Length',
                'description' => 'Minimum number of characters required for passwords',
                'type' => 'number',
                'value' => ['value' => 8, 'default' => 8],
                'category' => 'password',
                'sort_order' => 1
            ],
            [
                'key' => 'password.require_mixed_case',
                'name' => 'Require Mixed Case',
                'description' => 'Require both uppercase and lowercase letters',
                'type' => 'boolean',
                'value' => ['value' => true, 'default' => true],
                'category' => 'password',
                'sort_order' => 2
            ],
            [
                'key' => 'password.require_numbers',
                'name' => 'Require Numbers',
                'description' => 'Require at least one number',
                'type' => 'boolean',
                'value' => ['value' => true, 'default' => true],
                'category' => 'password',
                'sort_order' => 3
            ],
            [
                'key' => 'password.require_symbols',
                'name' => 'Require Symbols',
                'description' => 'Require at least one special character',
                'type' => 'boolean',
                'value' => ['value' => false, 'default' => false],
                'category' => 'password',
                'sort_order' => 4
            ],
            [
                'key' => 'password.expiry_days',
                'name' => 'Password Expiry Days',
                'description' => 'Number of days before passwords expire (0 = never)',
                'type' => 'number',
                'value' => ['value' => 90, 'default' => 90],
                'category' => 'password',
                'sort_order' => 5
            ],
            [
                'key' => 'password.history_count',
                'name' => 'Password History Count',
                'description' => 'Number of previous passwords to prevent reuse',
                'type' => 'number',
                'value' => ['value' => 5, 'default' => 5],
                'category' => 'password',
                'sort_order' => 6
            ],
            [
                'key' => 'login.max_attempts',
                'name' => 'Max Login Attempts',
                'description' => 'Maximum failed login attempts before lockout',
                'type' => 'number',
                'value' => ['value' => 5, 'default' => 5],
                'category' => 'password',
                'sort_order' => 7
            ],
            [
                'key' => 'login.lockout_time',
                'name' => 'Lockout Time',
                'description' => 'Minutes to lock account after max attempts',
                'type' => 'number',
                'value' => ['value' => 15, 'default' => 15],
                'category' => 'password',
                'sort_order' => 8
            ],

            // Session Management Settings
            [
                'key' => 'session.lifetime',
                'name' => 'Session Lifetime',
                'description' => 'How long a session lasts in minutes',
                'type' => 'number',
                'value' => ['value' => 120, 'default' => 120],
                'category' => 'session',
                'sort_order' => 10
            ],
            [
                'key' => 'session.timeout',
                'name' => 'Session Timeout',
                'description' => 'Inactivity timeout in minutes (0 = disabled)',
                'type' => 'number',
                'value' => ['value' => 30, 'default' => 30],
                'category' => 'session',
                'sort_order' => 11
            ],
            [
                'key' => 'session.single_device',
                'name' => 'Single Device Session',
                'description' => 'Force logout from other devices on new login',
                'type' => 'boolean',
                'value' => ['value' => false, 'default' => false],
                'category' => 'session',
                'sort_order' => 12
            ],
            [
                'key' => 'session.remember_me_lifetime',
                'name' => 'Remember Me Lifetime',
                'description' => 'How long remember me tokens last in minutes',
                'type' => 'number',
                'value' => ['value' => 43200, 'default' => 43200],
                'category' => 'session',
                'sort_order' => 13
            ],

            // 2FA Settings
            [
                'key' => '2fa.require',
                'name' => 'Require 2FA',
                'description' => 'Require all users to enable two-factor authentication',
                'type' => 'boolean',
                'value' => ['value' => false, 'default' => false],
                'category' => '2fa',
                'sort_order' => 20
            ],
            [
                'key' => '2fa.roles',
                'name' => '2FA Required Roles',
                'description' => 'Roles that require 2FA',
                'type' => 'json',
                'value' => ['value' => ['super-admin', 'admin'], 'default' => ['super-admin', 'admin']],
                'category' => '2fa',
                'sort_order' => 21
            ],

            // Audit Settings
            [
                'key' => 'audit.log_all',
                'name' => 'Log All Events',
                'description' => 'Log all system events',
                'type' => 'boolean',
                'value' => ['value' => true, 'default' => true],
                'category' => 'audit',
                'sort_order' => 30
            ],
            [
                'key' => 'audit.log_auth',
                'name' => 'Log Authentication',
                'description' => 'Log login/logout events',
                'type' => 'boolean',
                'value' => ['value' => true, 'default' => true],
                'category' => 'audit',
                'sort_order' => 31
            ],
            [
                'key' => 'audit.log_models',
                'name' => 'Log Model Changes',
                'description' => 'Log create/update/delete on models',
                'type' => 'boolean',
                'value' => ['value' => true, 'default' => true],
                'category' => 'audit',
                'sort_order' => 32
            ],
            [
                'key' => 'audit.retention_days',
                'name' => 'Log Retention Days',
                'description' => 'How many days to keep logs',
                'type' => 'number',
                'value' => ['value' => 90, 'default' => 90],
                'category' => 'audit',
                'sort_order' => 33
            ],
        ];

        foreach ($settings as $setting) {
            SecuritySetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
