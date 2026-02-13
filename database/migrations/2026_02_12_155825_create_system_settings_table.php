<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, text, boolean, integer, array, json
            $table->string('group')->default('general');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }

     /**
     * Insert default system settings.
     */
    private function insertDefaultSettings(): void
    {
        $defaultSettings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => config('app.name'),
                'type' => 'string',
                'group' => 'general',
                'description' => 'The name of the application',
                'is_public' => true,
            ],
            [
                'key' => 'site_email',
                'value' => 'support@jiattach.co.ke',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Primary email address for the site',
                'is_public' => true,
            ],
            [
                'key' => 'site_phone',
                'value' => '+254 700 123 456',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Primary phone number for the site',
                'is_public' => true,
            ],
            [
                'key' => 'site_address',
                'value' => 'Nairobi, Kenya',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Physical address of the organization',
                'is_public' => true,
            ],
            [
                'key' => 'site_description',
                'value' => 'Jiattach - Platform for tertiary students in Kenya to secure attachments and internships',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Brief description of the platform',
                'is_public' => true,
            ],
            [
                'key' => 'timezone',
                'value' => 'Africa/Nairobi',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default timezone for the application',
                'is_public' => false,
            ],
            [
                'key' => 'date_format',
                'value' => 'd/m/Y',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default date format',
                'is_public' => false,
            ],
            [
                'key' => 'time_format',
                'value' => 'H:i',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default time format',
                'is_public' => false,
            ],
            [
                'key' => 'currency',
                'value' => 'KES',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default currency',
                'is_public' => true,
            ],
            [
                'key' => 'language',
                'value' => 'en',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default language',
                'is_public' => false,
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'general',
                'description' => 'Enable maintenance mode',
                'is_public' => false,
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
                'description' => 'Site logo URL',
                'is_public' => true,
            ],
            [
                'key' => 'site_favicon',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
                'description' => 'Site favicon URL',
                'is_public' => true,
            ],
            [
                'key' => 'copyright_text',
                'value' => 'Copyright © ' . date('Y') . ' Jiattach. All rights reserved.',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Copyright text for the footer',
                'is_public' => true,
            ],
            
            // Email Settings
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@jiattach.co.ke',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Default from email address',
                'is_public' => false,
            ],
            [
                'key' => 'mail_from_name',
                'value' => config('app.name'),
                'type' => 'string',
                'group' => 'email',
                'description' => 'Default from name',
                'is_public' => false,
            ],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('system_settings')->insert($setting);
        }
    }
};
