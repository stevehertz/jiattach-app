<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Models\EmailSetting;
use Livewire\WithPagination;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class Email extends Component
{
    use WithPagination;

    public $smtp_host;
    public $smtp_port;
    public $smtp_encryption;
    public $smtp_username;
    public $smtp_password;
    public $mail_from_address;
    public $mail_from_name;
    public $mail_mailer;

    // Notification settings
    public $enable_email_notifications = true;
    public $send_welcome_email = true;
    public $send_application_updates = true;
    public $send_interview_notifications = true;
    public $send_offer_notifications = true;
    public $send_mentorship_notifications = true;
    public $send_exchange_program_notifications = true;
    public $send_system_notifications = true;

    // Email templates
    public $welcome_email_subject;
    public $application_submitted_subject;
    public $interview_scheduled_subject;
    public $offer_sent_subject;

    // Test email
    public $test_email;
    public $test_email_sent = false;
    public $test_email_error;

    public $show_password = false;
    public $password_changed = false;

    protected $queryString = ['page'];

    public function mount()
    {
        $this->loadEmailSettings();
    }

    private function loadEmailSettings()
    {
        // Load SMTP settings from database
        $smtpSettings = EmailSetting::byCategory('smtp')->get();

        foreach ($smtpSettings as $setting) {
            $key = str_replace('email_', '', $setting->key);
            if (property_exists($this, $key)) {
                $this->$key = $setting->getDecryptedValue();
            }
        }

        // Load notification settings
        $notificationSettings = EmailSetting::byCategory('notification')->get();

        foreach ($notificationSettings as $setting) {
            $key = str_replace('email_', '', $setting->key);
            if (property_exists($this, $key)) {
                $this->$key = (bool) $setting->value;
            }
        }

        // Load template settings
        $templateSettings = EmailSetting::byCategory('template')->get();

        foreach ($templateSettings as $setting) {
            $key = str_replace('email_', '', $setting->key);
            if (property_exists($this, $key)) {
                $this->$key = $setting->value;
            }
        }
    }

    public function rules()
    {
        return [
            'smtp_host' => ['required', 'string', 'max:255'],
            'smtp_port' => ['required', 'integer', 'between:1,65535'],
            'smtp_encryption' => ['required', 'string', 'in:tls,ssl,none'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255', 'min:8'],
            'mail_from_address' => ['required', 'email', 'max:255'],
            'mail_from_name' => ['required', 'string', 'max:255'],
            'mail_mailer' => ['required', 'string', 'in:smtp,mailgun,ses,postmark,sendmail,log'],

            // Notification settings
            'enable_email_notifications' => ['boolean'],
            'send_welcome_email' => ['boolean'],
            'send_application_updates' => ['boolean'],
            'send_interview_notifications' => ['boolean'],
            'send_offer_notifications' => ['boolean'],
            'send_mentorship_notifications' => ['boolean'],
            'send_exchange_program_notifications' => ['boolean'],
            'send_system_notifications' => ['boolean'],

            // Email templates
            'welcome_email_subject' => ['required', 'string', 'max:255'],
            'application_submitted_subject' => ['required', 'string', 'max:255'],
            'interview_scheduled_subject' => ['required', 'string', 'max:255'],
            'offer_sent_subject' => ['required', 'string', 'max:255'],

            // Test email
            'test_email' => ['nullable', 'email'],
        ];
    }

     public function saveEmailSettings()
    {
        $this->validate();

        try {
            // Save SMTP settings
            $this->saveSetting('smtp_host', 'email_smtp_host', $this->smtp_host, 'smtp');
            $this->saveSetting('smtp_port', 'email_smtp_port', $this->smtp_port, 'smtp');
            $this->saveSetting('smtp_encryption', 'email_smtp_encryption', $this->smtp_encryption, 'smtp');
            $this->saveSetting('smtp_username', 'email_smtp_username', $this->smtp_username, 'smtp');

            // Only update password if provided
            if ($this->password_changed && $this->smtp_password) {
                $this->saveSetting('smtp_password', 'email_smtp_password', $this->smtp_password, 'smtp', true);
                $this->password_changed = false;
            }

            $this->saveSetting('mail_from_address', 'mail_from_address', $this->mail_from_address, 'smtp');
            $this->saveSetting('mail_from_name', 'mail_from_name', $this->mail_from_name, 'smtp');
            $this->saveSetting('mail_mailer', 'mail_mailer', $this->mail_mailer, 'smtp');

            // Save notification settings
            $this->saveSetting('enable_email_notifications', 'email_enable_notifications', $this->enable_email_notifications, 'notification');
            $this->saveSetting('send_welcome_email', 'email_send_welcome', $this->send_welcome_email, 'notification');
            $this->saveSetting('send_application_updates', 'email_send_application_updates', $this->send_application_updates, 'notification');
            $this->saveSetting('send_interview_notifications', 'email_send_interview_notifications', $this->send_interview_notifications, 'notification');
            $this->saveSetting('send_offer_notifications', 'email_send_offer_notifications', $this->send_offer_notifications, 'notification');
            $this->saveSetting('send_mentorship_notifications', 'email_send_mentorship_notifications', $this->send_mentorship_notifications, 'notification');
            $this->saveSetting('send_exchange_program_notifications', 'email_send_exchange_program_notifications', $this->send_exchange_program_notifications, 'notification');
            $this->saveSetting('send_system_notifications', 'email_send_system_notifications', $this->send_system_notifications, 'notification');

            // Save template settings
            $this->saveSetting('welcome_email_subject', 'email_welcome_subject', $this->welcome_email_subject, 'template');
            $this->saveSetting('application_submitted_subject', 'email_application_submitted_subject', $this->application_submitted_subject, 'template');
            $this->saveSetting('interview_scheduled_subject', 'email_interview_scheduled_subject', $this->interview_scheduled_subject, 'template');
            $this->saveSetting('offer_sent_subject', 'email_offer_sent_subject', $this->offer_sent_subject, 'template');

            // Update email templates with new subjects
            $this->updateEmailTemplates();

            // Update Laravel mail configuration
            $this->updateLaravelMailConfig();

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Email settings saved successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Failed to save email settings: ' . $e->getMessage()
            ]);
        }
    }

    private function saveSetting($property, $key, $value, $category, $isEncrypted = false)
    {
        $setting = EmailSetting::where('key', $key)->first();

        if (!$setting) {
            // Create new setting if it doesn't exist
            $setting = new EmailSetting([
                'key' => $key,
                'group' => 'email',
                'category' => $category,
                'type' => $this->getTypeForProperty($property),
                'description' => $this->getDescriptionForKey($key),
                'is_public' => false,
                'is_encrypted' => $isEncrypted,
            ]);
        }

        if ($isEncrypted) {
            $setting->setEncryptedValue($value);
        } else {
            $setting->value = $value;
            $setting->save();
        }
    }

    private function getTypeForProperty($property)
    {
        if (in_array($property, [
            'enable_email_notifications',
            'send_welcome_email',
            'send_application_updates',
            'send_interview_notifications',
            'send_offer_notifications',
            'send_mentorship_notifications',
            'send_exchange_program_notifications',
            'send_system_notifications'
        ])) {
            return 'boolean';
        }

        if ($property === 'smtp_port') {
            return 'integer';
        }

        if ($property === 'smtp_password') {
            return 'password';
        }

        if (str_contains($property, 'subject')) {
            return 'string';
        }

        return 'string';
    }

    private function getDescriptionForKey($key)
    {
        $descriptions = [
            'email_smtp_host' => 'SMTP Host',
            'email_smtp_port' => 'SMTP Port',
            'email_smtp_encryption' => 'SMTP Encryption',
            'email_smtp_username' => 'SMTP Username',
            'email_smtp_password' => 'SMTP Password',
            'mail_from_address' => 'From Email Address',
            'mail_from_name' => 'From Name',
            'mail_mailer' => 'Mail Driver',
            'email_enable_notifications' => 'Enable Email Notifications',
            'email_send_welcome' => 'Send Welcome Emails',
            'email_send_application_updates' => 'Send Application Updates',
            'email_send_interview_notifications' => 'Send Interview Notifications',
            'email_send_offer_notifications' => 'Send Offer Notifications',
            'email_send_mentorship_notifications' => 'Send Mentorship Notifications',
            'email_send_exchange_program_notifications' => 'Send Exchange Program Notifications',
            'email_send_system_notifications' => 'Send System Notifications',
            'email_welcome_subject' => 'Welcome Email Subject',
            'email_application_submitted_subject' => 'Application Submitted Subject',
            'email_interview_scheduled_subject' => 'Interview Scheduled Subject',
            'email_offer_sent_subject' => 'Offer Sent Subject',
        ];

        return $descriptions[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    private function updateEmailTemplates()
    {
        // Update welcome email template subject
        $welcomeTemplate = EmailTemplate::where('category', 'welcome')->where('is_default', true)->first();
        if ($welcomeTemplate && $welcomeTemplate->subject !== $this->welcome_email_subject) {
            $welcomeTemplate->subject = $this->welcome_email_subject;
            $welcomeTemplate->save();
        }

        // Update application submitted template subject
        $appTemplate = EmailTemplate::where('category', 'application')->where('is_default', true)->first();
        if ($appTemplate && $appTemplate->subject !== $this->application_submitted_subject) {
            $appTemplate->subject = $this->application_submitted_subject;
            $appTemplate->save();
        }

        // Update interview scheduled template subject
        $interviewTemplate = EmailTemplate::where('category', 'interview')->where('is_default', true)->first();
        if ($interviewTemplate && $interviewTemplate->subject !== $this->interview_scheduled_subject) {
            $interviewTemplate->subject = $this->interview_scheduled_subject;
            $interviewTemplate->save();
        }

        // Update offer sent template subject
        $offerTemplate = EmailTemplate::where('category', 'offer')->where('is_default', true)->first();
        if ($offerTemplate && $offerTemplate->subject !== $this->offer_sent_subject) {
            $offerTemplate->subject = $this->offer_sent_subject;
            $offerTemplate->save();
        }
    }

    public function sendTestEmail()
    {
        $this->validate([
            'test_email' => ['required', 'email', 'max:255'],
        ]);

        try {
            // First save current settings to ensure we're testing with the right config
            $this->saveEmailSettings();

            // Send test email
            Mail::raw('This is a test email from Jiattach Platform to verify your email configuration is working correctly.', function ($message) {
                $message->to($this->test_email)
                        ->subject('Test Email - Jiattach Platform Configuration');
            });

            $this->test_email_sent = true;
            $this->test_email_error = null;
            
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Test email sent successfully to ' . $this->test_email . '!'
            ]);
        } catch (\Exception $e) {
            $this->test_email_sent = false;
            $this->test_email_error = $e->getMessage();
            
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ]);
        }
    }

    public function resetPassword()
    {
        $this->smtp_password = '';
        $this->password_changed = true;
        $this->show_password = false;
    }

    public function togglePasswordVisibility()
    {
        $this->show_password = !$this->show_password;
    }

    public function updated($property)
    {
        // Mark password as changed when user types in it
        if ($property === 'smtp_password') {
            $this->password_changed = true;
        }

        // Auto-save for certain fields
        $autoSaveFields = [
            'enable_email_notifications',
            'send_welcome_email',
            'send_application_updates',
            'send_interview_notifications',
            'send_offer_notifications',
            'send_mentorship_notifications',
            'send_exchange_program_notifications',
            'send_system_notifications',
        ];

        if (in_array($property, $autoSaveFields)) {
            $this->saveEmailSettings();
        }
    }

    private function updateLaravelMailConfig()
    {
        // Update the Laravel mail configuration dynamically
        config([
            'mail.default' => $this->mail_mailer,
            'mail.mailers.smtp.host' => $this->smtp_host,
            'mail.mailers.smtp.port' => $this->smtp_port,
            'mail.mailers.smtp.encryption' => $this->smtp_encryption,
            'mail.mailers.smtp.username' => $this->smtp_username,
            'mail.mailers.smtp.password' => $this->smtp_password,
            'mail.from.address' => $this->mail_from_address,
            'mail.from.name' => $this->mail_from_name,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.settings.email');
    }
}
