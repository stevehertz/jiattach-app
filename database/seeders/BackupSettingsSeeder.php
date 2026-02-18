<?php

namespace Database\Seeders;

use App\Models\BackupSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BackupSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $settings = [
            // General Settings
            [
                'key' => 'backup.enabled',
                'name' => 'Enable Automatic Backups',
                'description' => 'Enable or disable automatic scheduled backups',
                'type' => 'boolean',
                'value' => json_encode(['value' => true, 'default' => true]),
                'category' => 'general',
                'sort_order' => 1
            ],
            [
                'key' => 'backup.frequency',
                'name' => 'Backup Frequency',
                'description' => 'How often to run automatic backups',
                'type' => 'select',
                'options' => json_encode(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly']),
                'value' => json_encode(['value' => 'daily', 'default' => 'daily']),
                'category' => 'schedule',
                'sort_order' => 10
            ],
            [
                'key' => 'backup.time',
                'name' => 'Backup Time',
                'description' => 'Time of day to run backups (24-hour format)',
                'type' => 'text',
                'value' => json_encode(['value' => '02:00', 'default' => '02:00']),
                'category' => 'schedule',
                'sort_order' => 11
            ],
            [
                'key' => 'backup.day',
                'name' => 'Backup Day',
                'description' => 'Day of week for weekly backups',
                'type' => 'select',
                'options' => json_encode([
                    'monday' => 'Monday',
                    'tuesday' => 'Tuesday',
                    'wednesday' => 'Wednesday',
                    'thursday' => 'Thursday',
                    'friday' => 'Friday',
                    'saturday' => 'Saturday',
                    'sunday' => 'Sunday'
                ]),
                'value' => json_encode(['value' => 'monday', 'default' => 'monday']),
                'category' => 'schedule',
                'sort_order' => 12
            ],
            [
                'key' => 'backup.date',
                'name' => 'Backup Date',
                'description' => 'Day of month for monthly backups',
                'type' => 'number',
                'value' => json_encode(['value' => 1, 'default' => 1]),
                'category' => 'schedule',
                'sort_order' => 13
            ],
            [
                'key' => 'backup.retention_days',
                'name' => 'Retention Days',
                'description' => 'Number of days to keep backups',
                'type' => 'number',
                'value' => json_encode(['value' => 30, 'default' => 30]),
                'category' => 'schedule',
                'sort_order' => 14
            ],
            [
                'key' => 'backup.max_files',
                'name' => 'Maximum Backup Files',
                'description' => 'Maximum number of backup files to keep',
                'type' => 'number',
                'value' => json_encode(['value' => 10, 'default' => 10]),
                'category' => 'schedule',
                'sort_order' => 15
            ],
            [
                'key' => 'backup.disk',
                'name' => 'Storage Disk',
                'description' => 'Where to store backup files',
                'type' => 'select',
                'options' => json_encode(['local' => 'Local Storage', 's3' => 'Amazon S3', 'ftp' => 'FTP Server']),
                'value' => json_encode(['value' => 'local', 'default' => 'local']),
                'category' => 'destination',
                'sort_order' => 20
            ],
            [
                'key' => 'backup.path',
                'name' => 'Backup Path',
                'description' => 'Directory path for backups',
                'type' => 'text',
                'value' => json_encode(['value' => 'backups', 'default' => 'backups']),
                'category' => 'destination',
                'sort_order' => 21
            ],
            [
                'key' => 'backup.compress',
                'name' => 'Compress Backups',
                'description' => 'Compress backup files to save space',
                'type' => 'boolean',
                'value' => json_encode(['value' => true, 'default' => true]),
                'category' => 'destination',
                'sort_order' => 22
            ],
            [
                'key' => 'backup.notify_success',
                'name' => 'Notify on Success',
                'description' => 'Send notification when backup completes successfully',
                'type' => 'boolean',
                'value' => json_encode(['value' => true, 'default' => true]),
                'category' => 'notification',
                'sort_order' => 30
            ],
            [
                'key' => 'backup.notify_failure',
                'name' => 'Notify on Failure',
                'description' => 'Send notification when backup fails',
                'type' => 'boolean',
                'value' => json_encode(['value' => true, 'default' => true]),
                'category' => 'notification',
                'sort_order' => 31
            ],
            [
                'key' => 'backup.notification_emails',
                'name' => 'Notification Emails',
                'description' => 'Email addresses to receive backup notifications',
                'type' => 'text',
                'value' => json_encode(['value' => '', 'default' => '']),
                'category' => 'notification',
                'sort_order' => 32
            ],
        ];

        foreach ($settings as $setting) {
            BackupSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
