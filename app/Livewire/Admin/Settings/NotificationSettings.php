<?php

namespace App\Livewire\Admin\Settings;

use App\Models\NotificationSetting;
use App\Models\NotificationTemplate;
use App\Models\UserNotificationPreference;
use App\Traits\LogsActivity;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationSettings extends Component
{
    use WithPagination, LogsActivity;

    // Active tab
    public $activeTab = 'general';

    // General Settings
    public $settings = [];
    public $originalSettings = [];

    // Template Management
    public $templateId;
    public $templateName;
    public $templateType;
    public $templateChannel;
    public $templateSubject;
    public $templateBody;
    public $templateDescription;
    public $templateVariables = [];
    public $templateIsActive = true;
    public $showTemplateModal = false;
    public $editingTemplate = false;

    // User Preferences Overview
    public $search = '';
    public $perPage = 10;

    // Bulk Actions
    public $selectedTemplates = [];
    public $selectAll = false;

    protected $listeners = [
        'refreshSettings' => '$refresh',
        'confirmTemplateDelete' => 'deleteTemplate'
    ];

    protected function rules()
    {
        return [
            'templateName' => 'required|string|max:255',
            'templateType' => 'required|in:placement,mentorship,system,alert',
            'templateChannel' => 'required|in:email,sms,push,in_app',
            'templateSubject' => 'required_if:templateChannel,email|max:255',
            'templateBody' => 'required|string',
            'templateDescription' => 'nullable|string|max:500',
            'templateIsActive' => 'boolean'
        ];
    }

    protected $messages = [
        'templateName.required' => 'The template name is required.',
        'templateType.required' => 'Please select a template type.',
        'templateChannel.required' => 'Please select a notification channel.',
        'templateSubject.required_if' => 'Subject is required for email templates.',
        'templateBody.required' => 'The template body is required.'
    ];

    public function mount()
    {
        $this->loadSettings();
        $this->originalSettings = $this->settings;
    }

    /**
     * Load all notification settings
     */
    public function loadSettings()
    {
        $dbSettings = NotificationSetting::orderBy('sort_order')->get();

        foreach ($dbSettings as $setting) {
            $this->settings[$setting->key] = [
                'id' => $setting->id,
                'value' => $setting->getTypedValue(),
                'type' => $setting->type,
                'name' => $setting->name,
                'description' => $setting->description,
                'options' => $setting->options,
                'category' => $setting->category
            ];
        }
    }

    /**
     * Save general settings
     */
    public function saveGeneralSettings()
    {
        $changedSettings = [];

        foreach ($this->settings as $key => $data) {
            // Check if value changed
            if (
                !isset($this->originalSettings[$key]) ||
                $this->originalSettings[$key]['value'] != $data['value']
            ) {
                $changedSettings[] = $key;
            }

            // Update the setting
            NotificationSetting::where('key', $key)->update([
                'value' => ['value' => $data['value']]
            ]);
        }

        $this->originalSettings = $this->settings;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => count($changedSettings) . ' notification settings updated successfully.'
        ]);

        // Use your custom activity logging trait
        if (!empty($changedSettings)) {
            $this->logActivity(
                'Updated notification settings',
                'settings_updated',
                [
                    'changed_settings' => $changedSettings,
                    'new_values' => collect($this->settings)->only($changedSettings)->toArray()
                ],
                'notification_settings'
            );
        }
    }

    /**
     * Reset general settings to defaults
     */
    public function resetToDefaults()
    {
        if (!auth()->user()->hasRole('super-admin')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Only super administrators can reset settings.'
            ]);
            return;
        }

        $settings = NotificationSetting::all();
        foreach ($settings as $setting) {
            $defaultValue = $setting->value['default'] ?? null;
            if ($defaultValue !== null) {
                $setting->update(['value' => ['value' => $defaultValue]]);
            }
        }

        $this->loadSettings();
        $this->originalSettings = $this->settings;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Settings have been reset to defaults.'
        ]);

        // Use your custom activity logging trait
        $this->logActivity(
            'Reset notification settings to defaults',
            'settings_reset',
            [
                'reset_by' => auth()->user()->email,
                'timestamp' => now()->toDateTimeString()
            ],
            'notification_settings'
        );
    }

    /**
     * Template Management Methods
     */
    public function createTemplate()
    {
        $this->resetTemplateFields();
        $this->editingTemplate = false;
        $this->showTemplateModal = true;
    }

    public function editTemplate($id)
    {
        $template = NotificationTemplate::findOrFail($id);

        $this->templateId = $template->id;
        $this->templateName = $template->name;
        $this->templateType = $template->type;
        $this->templateChannel = $template->channel;
        $this->templateSubject = $template->subject;
        $this->templateBody = $template->body;
        $this->templateDescription = $template->description;
        $this->templateVariables = $template->variables ?? [];
        $this->templateIsActive = $template->is_active;

        $this->editingTemplate = true;
        $this->showTemplateModal = true;
    }

    public function saveTemplate()
    {
        $this->validate();

        $data = [
            'name' => $this->templateName,
            'type' => $this->templateType,
            'channel' => $this->templateChannel,
            'subject' => $this->templateSubject,
            'body' => $this->templateBody,
            'description' => $this->templateDescription,
            'variables' => $this->extractVariables(),
            'is_active' => $this->templateIsActive,
            'is_system' => $this->editingTemplate ? false : true
        ];

        if ($this->editingTemplate) {
            $template = NotificationTemplate::find($this->templateId);
            $template->update($data);
            $message = 'Template updated successfully.';

            // Log the update
            $this->logActivity(
                "Updated notification template: {$this->templateName}",
                'template_updated',
                [
                    'template_id' => $template->id,
                    'template_name' => $this->templateName,
                    'type' => $this->templateType,
                    'channel' => $this->templateChannel,
                    'changes' => $template->getChanges()
                ],
                'notification_templates'
            );
        } else {
            $template = NotificationTemplate::create($data);
            $message = 'Template created successfully.';

            // Log the creation
            $this->logActivity(
                "Created notification template: {$this->templateName}",
                'template_created',
                [
                    'template_id' => $template->id,
                    'template_name' => $this->templateName,
                    'type' => $this->templateType,
                    'channel' => $this->templateChannel
                ],
                'notification_templates'
            );
        }

        $this->showTemplateModal = false;
        $this->resetTemplateFields();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    public function confirmTemplateDelete($id)
    {
        $template = NotificationTemplate::find($id);

        if ($template->is_system) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'System templates cannot be deleted.'
            ]);
            return;
        }

        $this->dispatch('confirm-delete', [
            'id' => $id,
            'message' => 'Are you sure you want to delete this template?'
        ]);
    }

    public function deleteTemplate($id)
    {
        $template = NotificationTemplate::find($id);

        if ($template->is_system) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'System templates cannot be deleted.'
            ]);
            return;
        }

        $templateName = $template->name;
        $template->delete();

        // Log the deletion
        $this->logActivity(
            "Deleted notification template: {$templateName}",
            'template_deleted',
            [
                'template_id' => $id,
                'template_name' => $templateName
            ],
            'notification_templates'
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Template deleted successfully.'
        ]);
    }

    public function bulkDeleteTemplates()
    {
        if (empty($this->selectedTemplates)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'No templates selected.'
            ]);
            return;
        }

        $systemTemplates = NotificationTemplate::whereIn('id', $this->selectedTemplates)
            ->where('is_system', true)
            ->count();

        if ($systemTemplates > 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'System templates cannot be deleted.'
            ]);
            return;
        }

        $templates = NotificationTemplate::whereIn('id', $this->selectedTemplates)->get();
        $templateIds = $this->selectedTemplates;
        $templateNames = $templates->pluck('name')->toArray();

        NotificationTemplate::whereIn('id', $this->selectedTemplates)->delete();

        // Log the bulk deletion
        $this->logActivity(
            "Bulk deleted " . count($templateIds) . " notification templates",
            'templates_bulk_deleted',
            [
                'template_ids' => $templateIds,
                'template_names' => $templateNames,
                'count' => count($templateIds)
            ],
            'notification_templates'
        );

        $this->selectedTemplates = [];
        $this->selectAll = false;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Selected templates deleted successfully.'
        ]);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedTemplates = NotificationTemplate::pluck('id')->toArray();
        } else {
            $this->selectedTemplates = [];
        }
    }

    public function toggleTemplateStatus($id)
    {
        $template = NotificationTemplate::find($id);
        $oldStatus = $template->is_active;
        $template->update(['is_active' => !$oldStatus]);

        // Log the status change
        $this->logActivity(
            "Changed template status: {$template->name} to " . (!$oldStatus ? 'active' : 'inactive'),
            'template_status_changed',
            [
                'template_id' => $template->id,
                'template_name' => $template->name,
                'old_status' => $oldStatus,
                'new_status' => !$oldStatus
            ],
            'notification_templates'
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Template status updated.'
        ]);
    }

    public function testTemplate($id)
    {
        $template = NotificationTemplate::find($id);

        // Generate sample data based on template type
        $sampleData = $this->generateSampleData($template->type);

        $parsed = $template->parse($sampleData);

        $this->dispatch('show-test-preview', [
            'subject' => $parsed->subject,
            'body' => $parsed->body
        ]);
    }

    /**
     * User Preferences Methods
     */
    public function getUserPreferences()
    {
        return UserNotificationPreference::with('user')
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate($this->perPage);
    }

    /**
     * Helper Methods
     */
    private function resetTemplateFields()
    {
        $this->reset([
            'templateId',
            'templateName',
            'templateType',
            'templateChannel',
            'templateSubject',
            'templateBody',
            'templateDescription',
            'templateVariables',
            'templateIsActive'
        ]);
        $this->templateIsActive = true;
        $this->resetValidation();
    }

    private function extractVariables()
    {
        preg_match_all('/\{\{([^}]+)\}\}/', $this->templateBody, $matches);

        $variables = [];
        if (!empty($matches[1])) {
            foreach ($matches[1] as $var) {
                $variables[trim($var)] = '{{' . trim($var) . '}}';
            }
        }

        // Also check subject for variables
        if ($this->templateSubject) {
            preg_match_all('/\{\{([^}]+)\}\}/', $this->templateSubject, $subjectMatches);
            if (!empty($subjectMatches[1])) {
                foreach ($subjectMatches[1] as $var) {
                    $variables[trim($var)] = '{{' . trim($var) . '}}';
                }
            }
        }

        return $variables;
    }

    private function generateSampleData($type)
    {
        return match ($type) {
            'placement' => [
                'student_name' => 'John Doe',
                'company_name' => 'Tech Corp Ltd',
                'position' => 'Software Developer Intern',
                'start_date' => now()->addDays(30)->format('Y-m-d'),
                'match_score' => '85%'
            ],
            'mentorship' => [
                'mentor_name' => 'Jane Smith',
                'student_name' => 'John Doe',
                'session_date' => now()->addDays(2)->format('Y-m-d H:i'),
                'meeting_link' => 'https://meet.google.com/abc-defg-hij'
            ],
            'system' => [
                'system_name' => config('app.name'),
                'date' => now()->format('Y-m-d H:i'),
                'admin_name' => 'Admin User'
            ],
            'alert' => [
                'alert_title' => 'System Maintenance',
                'alert_message' => 'The system will be down for maintenance on Sunday at 2 AM.',
                'severity' => 'High'
            ],
            default => [
                'name' => 'User',
                'email' => 'user@example.com'
            ]
        };
    }

    public function render()
    {
        return view('livewire.admin.settings.notification-settings', [
            'templates' => NotificationTemplate::orderBy('type')
                ->orderBy('name')
                ->paginate(10, pageName: 'templates-page'),
            'notificationTypes' => UserNotificationPreference::getNotificationTypes(),
            'availableChannels' => UserNotificationPreference::getAvailableChannels(),
            'userPreferences' => $this->getUserPreferences(),
            'stats' => [
                'total_templates' => NotificationTemplate::count(),
                'active_templates' => NotificationTemplate::where('is_active', true)->count(),
                'email_templates' => NotificationTemplate::where('channel', 'email')->count(),
                'sms_templates' => NotificationTemplate::where('channel', 'sms')->count(),
            ]
        ]);
    }
}
