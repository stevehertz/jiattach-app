<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Traits\LogsModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory, SoftDeletes, LogsModelActivity;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'body',
        'type',
        'category',
        'variables',
        'is_active',
        'is_default',
        'usage_count',
        'last_used_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
    ];

    protected $appends = [
        'type_label',
        'category_label',
        'preview',
        'variable_list',
        'usage_label',
    ];

     /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
                $count = 1;
                while (static::where('slug', $template->slug)->exists()) {
                    $template->slug = Str::slug($template->name) . '-' . $count++;
                }
            }
        });

        static::saving(function ($template) {
            // Ensure only one default template per category
            if ($template->is_default) {
                static::where('category', $template->category)
                    ->where('id', '!=', $template->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get the user who created the template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the template.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get type label.
     */
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'system' => 'System Template',
                    'custom' => 'Custom Template',
                    'auto' => 'Auto-generated',
                ];
                return $labels[$this->type] ?? ucfirst($this->type);
            }
        );
    }

    /**
     * Get category label.
     */
    protected function categoryLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $labels = [
                    'welcome' => 'Welcome Email',
                    'application' => 'Application Emails',
                    'interview' => 'Interview Emails',
                    'offer' => 'Offer Emails',
                    'mentorship' => 'Mentorship Emails',
                    'exchange' => 'Exchange Program Emails',
                    'notification' => 'System Notifications',
                    'general' => 'General Emails',
                ];
                return $labels[$this->category] ?? ucfirst($this->category);
            }
        );
    }

    /**
     * Get preview of email body.
     */
    protected function preview(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::limit(strip_tags($this->body), 150),
        );
    }

    /**
     * Get variable list as comma-separated string.
     */
    protected function variableList(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->variables) || !is_array($this->variables)) {
                    return '';
                }
                return implode(', ', array_keys($this->variables));
            }
        );
    }

    /**
     * Get usage label.
     */
    protected function usageLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->usage_count === 0) {
                    return 'Never used';
                }
                if ($this->last_used_at) {
                    return "Used {$this->usage_count} times, last on " . $this->last_used_at->format('M d, Y');
                }
                return "Used {$this->usage_count} times";
            }
        );
    }

    /**
     * Scope a query to only include active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include default templates.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include templates by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include templates by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get template for a specific category.
     */
    public static function getForCategory(string $category, bool $activeOnly = true): ?self
    {
        $query = self::byCategory($category);

        if ($activeOnly) {
            $query->active();
        }

        // Try to get default template first
        $template = $query->default()->first();

        // If no default, get any active template
        if (!$template && $activeOnly) {
            $template = $query->first();
        }

        return $template;
    }

    /**
     * Compile template with variables.
     */
    public function compile(array $variables = []): array
    {
        $subject = $this->subject;
        $body = $this->body;

        foreach ($variables as $key => $value) {
            $placeholder = "{{{$key}}}";
            $subject = str_replace($placeholder, $value, $subject);
            $body = str_replace($placeholder, $value, $body);
        }

        // Increment usage count
        $this->incrementUsage();

        return [
            'subject' => $subject,
            'body' => $body,
            'template' => $this,
        ];
    }

    /**
     * Increment usage count and update last used timestamp.
     */
    public function incrementUsage(): void
    {
        $this->update([
            'usage_count' => $this->usage_count + 1,
            'last_used_at' => now(),
        ]);
    }

    /**
     * Duplicate the template.
     */
    public function duplicate(array $overrides = []): self
    {
        $attributes = $this->getAttributes();

        // Remove primary key and timestamps
        unset($attributes['id'], $attributes['created_at'], $attributes['updated_at'], $attributes['deleted_at']);

        // Apply overrides
        $attributes = array_merge($attributes, $overrides);

        // Ensure it's not marked as default
        $attributes['is_default'] = false;
        $attributes['slug'] = $attributes['slug'] . '-copy-' . Str::random(4);
        $attributes['name'] = $attributes['name'] . ' (Copy)';

        return self::create($attributes);
    }

    /**
     * Get available variables for this template category.
     */
    public static function getAvailableVariables(string $category): array
    {
        $variables = [
            'welcome' => [
                'user_name' => 'Full name of the user',
                'user_email' => 'Email address of the user',
                'account_type' => 'Type of account (Student, Employer, etc.)',
                'platform_name' => 'Name of the platform (Jiattach)',
                'login_url' => 'URL to login page',
                'support_email' => 'Support email address',
                'current_date' => 'Current date',
            ],
            'application' => [
                'student_name' => 'Name of the student',
                'opportunity_title' => 'Title of the opportunity',
                'company_name' => 'Name of the employer company',
                'application_date' => 'Date application was submitted',
                'application_status' => 'Current status of application',
                'application_id' => 'Unique application ID',
                'interview_date' => 'Date of interview (if scheduled)',
                'interview_location' => 'Location of interview',
                'offer_details' => 'Details of offer (if applicable)',
            ],
            'interview' => [
                'student_name' => 'Name of the student',
                'employer_name' => 'Name of the employer/company',
                'opportunity_title' => 'Title of the position',
                'interview_date' => 'Date of the interview',
                'interview_time' => 'Time of the interview',
                'interview_type' => 'Type of interview (in-person, virtual, phone)',
                'interview_location' => 'Location or meeting link',
                'interview_notes' => 'Additional notes about the interview',
                'company_contact' => 'Contact person at company',
                'company_phone' => 'Company phone number',
                'preparation_notes' => 'Notes for preparation',
            ],
            'offer' => [
                'student_name' => 'Name of the student',
                'employer_name' => 'Name of the employer',
                'opportunity_title' => 'Title of the position',
                'start_date' => 'Employment start date',
                'stipend_amount' => 'Monthly stipend amount',
                'stipend_currency' => 'Currency (KES)',
                'duration_months' => 'Duration in months',
                'location' => 'Work location',
                'supervisor_name' => 'Name of supervisor',
                'supervisor_email' => 'Email of supervisor',
                'acceptance_deadline' => 'Deadline to accept offer',
                'offer_id' => 'Unique offer ID',
            ],
        ];

        return $variables[$category] ?? [
            'user_name' => 'Full name of the user',
            'user_email' => 'Email address of the user',
            'platform_name' => 'Name of the platform',
            'current_date' => 'Current date',
            'current_year' => 'Current year',
        ];
    }
}
