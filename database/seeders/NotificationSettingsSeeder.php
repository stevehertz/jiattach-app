<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use App\Models\NotificationTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $settings = [
            [
                'key' => 'email_notifications_enabled',
                'name' => 'Email Notifications',
                'description' => 'Enable or disable all email notifications',
                'type' => 'boolean',
                'value' => ['value' => true, 'default' => true],
                'category' => 'general',
                'sort_order' => 1
            ],
            [
                'key' => 'sms_notifications_enabled',
                'name' => 'SMS Notifications',
                'description' => 'Enable or disable all SMS notifications',
                'type' => 'boolean',
                'value' => ['value' => false, 'default' => false],
                'category' => 'general',
                'sort_order' => 2
            ],
            [
                'key' => 'push_notifications_enabled',
                'name' => 'Push Notifications',
                'description' => 'Enable or disable push notifications',
                'type' => 'boolean',
                'value' => ['value' => true, 'default' => true],
                'category' => 'general',
                'sort_order' => 3
            ],
            [
                'key' => 'notification_retention_days',
                'name' => 'Notification Retention',
                'description' => 'Number of days to keep notification history',
                'type' => 'number',
                'value' => ['value' => 30, 'default' => 30],
                'category' => 'general',
                'sort_order' => 4
            ],
            [
                'key' => 'daily_digest_enabled',
                'name' => 'Daily Digest',
                'description' => 'Send daily digest of notifications',
                'type' => 'boolean',
                'value' => ['value' => true, 'default' => true],
                'category' => 'general',
                'sort_order' => 5
            ],
            [
                'key' => 'daily_digest_time',
                'name' => 'Daily Digest Time',
                'description' => 'Time to send daily digest (24hr format)',
                'type' => 'text',
                'value' => ['value' => '08:00', 'default' => '08:00'],
                'category' => 'general',
                'sort_order' => 6
            ],
            [
                'key' => 'placement_notifications',
                'name' => 'Placement Notifications',
                'description' => 'Send notifications for placement activities',
                'type' => 'boolean',
                'value' => ['value' => true, 'default' => true],
                'category' => 'placement',
                'sort_order' => 10
            ],
            [
                'key' => 'mentorship_notifications',
                'name' => 'Mentorship Notifications',
                'description' => 'Send notifications for mentorship activities',
                'type' => 'boolean',
                'value' => ['value' => true, 'default' => true],
                'category' => 'mentorship',
                'sort_order' => 20
            ],
            [
                'key' => 'system_notifications',
                'name' => 'System Notifications',
                'description' => 'Send system alerts and announcements',
                'type' => 'boolean',
                'value' => ['value' => true, 'default' => true],
                'category' => 'system',
                'sort_order' => 30
            ],
            [
                'key' => 'reminder_notifications',
                'name' => 'Reminder Notifications',
                'description' => 'Send reminder notifications for deadlines and sessions',
                'type' => 'boolean',
                'value' => ['value' => true, 'default' => true],
                'category' => 'reminders',
                'sort_order' => 40
            ],
            [
                'key' => 'reminder_advance_time',
                'name' => 'Reminder Advance Time',
                'description' => 'How many minutes before to send reminders',
                'type' => 'number',
                'value' => ['value' => 60, 'default' => 60],
                'category' => 'reminders',
                'sort_order' => 41,
                'options' => ['15', '30', '60', '120', '1440']
            ],
        ];

        foreach ($settings as $setting) {
            NotificationSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        // Default Templates
        $templates = [
            [
                'name' => 'Placement Match Found',
                'type' => 'placement',
                'channel' => 'email',
                'subject' => 'New Placement Match Found for {{student_name}}',
                'body' => "Dear {{student_name}},\n\nWe have found a potential placement match for you!\n\nCompany: {{company_name}}\nPosition: {{position}}\nMatch Score: {{match_score}}\n\nPlease log in to your dashboard to review this opportunity.\n\nBest regards,\n{{system_name}} Team",
                'variables' => ['student_name', 'company_name', 'position', 'match_score', 'system_name'],
                'description' => 'Sent when a new placement match is found for a student',
                'is_system' => true
            ],
            [
                'name' => 'Placement Confirmed',
                'type' => 'placement',
                'channel' => 'email',
                'subject' => 'Placement Confirmed - {{company_name}}',
                'body' => "Dear {{student_name}},\n\nCongratulations! Your placement at {{company_name}} has been confirmed.\n\nStart Date: {{start_date}}\nSupervisor: {{supervisor_name}}\n\nPlease ensure all your documents are uploaded and verified before your start date.\n\nBest regards,\n{{system_name}} Team",
                'variables' => ['student_name', 'company_name', 'start_date', 'supervisor_name', 'system_name'],
                'description' => 'Sent when a placement is confirmed',
                'is_system' => true
            ],
            [
                'name' => 'Mentorship Session Reminder',
                'type' => 'mentorship',
                'channel' => 'email',
                'subject' => 'Upcoming Mentorship Session Reminder',
                'body' => "Dear {{name}},\n\nThis is a reminder of your upcoming mentorship session.\n\nDate & Time: {{session_date}}\nMeeting Link: {{meeting_link}}\n\nPlease be prepared and join on time.\n\nBest regards,\n{{system_name}} Team",
                'variables' => ['name', 'session_date', 'meeting_link', 'system_name'],
                'description' => 'Reminder for upcoming mentorship sessions',
                'is_system' => true
            ],
            [
                'name' => 'Document Approved',
                'type' => 'system',
                'channel' => 'email',
                'subject' => 'Document Approved - {{document_name}}',
                'body' => "Dear {{student_name}},\n\nYour document '{{document_name}}' has been approved.\n\nYou can now proceed with your placement application.\n\nBest regards,\n{{system_name}} Team",
                'variables' => ['student_name', 'document_name', 'system_name'],
                'description' => 'Sent when a student document is approved',
                'is_system' => true
            ],
            [
                'name' => 'Placement Deadline Alert',
                'type' => 'alert',
                'channel' => 'email',
                'subject' => 'Placement Deadline Approaching',
                'body' => "Dear {{student_name}},\n\nThis is a reminder that the placement opportunity '{{opportunity_title}}' is closing soon.\n\nDeadline: {{deadline_date}}\n\nDon't miss out on this opportunity!\n\nBest regards,\n{{system_name}} Team",
                'variables' => ['student_name', 'opportunity_title', 'deadline_date', 'system_name'],
                'description' => 'Alert for approaching placement deadlines',
                'is_system' => true
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}
