<?php

namespace App\Livewire\Admin\Opportunities;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Organization;
use App\Models\AttachmentOpportunity;

class Create extends Component
{
    // Basic Information
    public $title = '';
    public $organization_id = '';
    public $opportunity_type = 'attachment';
    public $employment_type = 'contract';
    public $description = '';
    public $responsibilities = '';
    public $requirements = '';
    public $benefits = '';

    // Location & Duration
    public $duration_months = 3;
    public $start_date;
    public $end_date;
    public $is_remote = false;
    public $is_hybrid = false;
    public $location = '';
    public $county = '';
    public $town = '';

    // Application Details
    public $application_deadline;
    public $slots_available = 5;
    public $requires_portfolio = false;
    public $requires_cover_letter = true;

    // Stipend & Compensation
    public $stipend;
    public $stipend_frequency = 'monthly';
    public $other_benefits = [];

    // Requirements
    public $min_cgpa;
    public $min_year_of_study;
    public $required_skills = [];
    public $preferred_skills = [];
    public $preferred_courses = [];
    public $target_institutions = [];

    // Status & UI State
    public $status = 'draft';
    public $isProcessing = false;
    public $activeTab = 'basic';

    // Skill inputs
    public $newSkill = '';
    public $newPreferredSkill = '';
    public $newPreferredCourse = '';
    public $newBenefitInput = '';

    protected $listeners = ['add-other-benefit' => 'handleAddBenefit'];

    protected $rules = [
        'title' => 'required|string|max:255',
        'organization_id' => 'required|exists:organizations,id',
        'opportunity_type' => 'required|string',
        'employment_type' => 'required|string',
        'description' => 'required|string|min:100',
        'responsibilities' => 'nullable|string',
        'requirements' => 'nullable|string',
        'benefits' => 'nullable|string',
        'duration_months' => 'required|integer|min:1',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after:start_date',
        'is_remote' => 'boolean',
        'is_hybrid' => 'boolean',
        'location' => 'nullable|string',
        'county' => 'nullable|string',
        'town' => 'nullable|string',
        'application_deadline' => 'required|date|after_or_equal:today',
        'slots_available' => 'required|integer|min:1',
        'requires_portfolio' => 'boolean',
        'requires_cover_letter' => 'boolean',
        'stipend' => 'nullable|numeric|min:0',
        'stipend_frequency' => 'nullable|string',
        'min_cgpa' => 'nullable|numeric|between:0,4',
        'min_year_of_study' => 'nullable|integer|min:1|max:6',
    ];

    public function mount()
    {
        $this->start_date = now()->addWeek()->format('Y-m-d');
        $this->end_date = now()->addMonths(3)->format('Y-m-d');
        $this->application_deadline = now()->addDays(14)->format('Y-m-d');
    }

    public function getOrganizationsProperty()
    {
        return Organization::with('user')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($org) {
                return [$org->id => $org->name . ' (' . ($org->user->email ?? 'N/A') . ')'];
            });
    }

    public function getCountiesProperty()
    {
        return getKenyanCounties();
    }

    public function getOpportunityTypesProperty()
    {
        return getOpportunityTypes();
    }

    public function getEmploymentTypesProperty()
    {
        return getEmploymentTypes();
    }

    public function getCommonSkillsProperty()
    {
        return getCommonSkills();
    }

    // Tab Navigation
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function nextTab()
    {
        $tabs = ['basic', 'details', 'requirements', 'review'];
        $currentIndex = array_search($this->activeTab, $tabs);
        if ($currentIndex < count($tabs) - 1) {
            $this->activeTab = $tabs[$currentIndex + 1];
        }
    }

    // Add this method to check if a tab is complete
    public function isTabComplete(string $tab): bool
    {
        return match ($tab) {
            'basic' => !empty($this->title)
                && !empty($this->organization_id)
                && strlen($this->description) >= 100,

            'details' => !empty($this->duration_months)
                && !empty($this->start_date)
                && !empty($this->end_date)
                && !empty($this->application_deadline),

            'requirements' => true, // Optional tab - always considered complete

            'review' => $this->isTabComplete('basic')
                && $this->isTabComplete('details'),

            default => false
        };
    }

    // Add this method for the completion percentage (used in review tab)
    public function getCompletionPercentage(): int
    {
        $totalFields = 8;
        $filledFields = 0;

        if (!empty($this->title)) $filledFields++;
        if (!empty($this->organization_id)) $filledFields++;
        if (strlen($this->description) >= 100) $filledFields++;
        if (!empty($this->duration_months)) $filledFields++;
        if (!empty($this->start_date)) $filledFields++;
        if (!empty($this->end_date)) $filledFields++;
        if (!empty($this->application_deadline)) $filledFields++;
        if (!empty($this->slots_available)) $filledFields++;

        return (int) round(($filledFields / $totalFields) * 100);
    }

    public function previousTab()
    {
        $tabs = ['basic', 'details', 'requirements', 'review'];
        $currentIndex = array_search($this->activeTab, $tabs);
        if ($currentIndex > 0) {
            $this->activeTab = $tabs[$currentIndex - 1];
        }
    }

    // Skills Management
    public function addRequiredSkill($skill = null)
    {
        $skillToAdd = $skill ?: $this->newSkill;

        if (!empty($skillToAdd) && !in_array(trim($skillToAdd), $this->required_skills)) {
            $this->required_skills[] = trim($skillToAdd);
            $this->newSkill = '';
        }
    }

    public function removeRequiredSkill($index)
    {
        unset($this->required_skills[$index]);
        $this->required_skills = array_values($this->required_skills);
    }

    public function addPreferredSkill()
    {
        if (!empty($this->newPreferredSkill) && !in_array(trim($this->newPreferredSkill), $this->preferred_skills)) {
            $this->preferred_skills[] = trim($this->newPreferredSkill);
            $this->newPreferredSkill = '';
        }
    }

    public function removePreferredSkill($index)
    {
        unset($this->preferred_skills[$index]);
        $this->preferred_skills = array_values($this->preferred_skills);
    }

    public function addPreferredCourse()
    {
        if (!empty($this->newPreferredCourse) && !in_array(trim($this->newPreferredCourse), $this->preferred_courses)) {
            $this->preferred_courses[] = trim($this->newPreferredCourse);
            $this->newPreferredCourse = '';
        }
    }

    public function removePreferredCourse($index)
    {
        unset($this->preferred_courses[$index]);
        $this->preferred_courses = array_values($this->preferred_courses);
    }

    // Benefits Management
    public function handleAddBenefit($benefit)
    {
        $this->addOtherBenefit($benefit);
    }

    public function addOtherBenefit($benefit = null)
    {
        $benefitToAdd = $benefit ?: $this->newBenefitInput;

        if (!empty($benefitToAdd) && !in_array($benefitToAdd, $this->other_benefits)) {
            $this->other_benefits[] = $benefitToAdd;
            $this->newBenefitInput = '';
        }
    }

    public function removeOtherBenefit($index)
    {
        unset($this->other_benefits[$index]);
        $this->other_benefits = array_values($this->other_benefits);
    }

    // Save Actions
    public function saveAsDraft()
    {
        $this->status = 'draft';
        $this->saveOpportunity();
    }

    public function submitForApproval()
    {
        $this->status = 'pending_approval';
        $this->saveOpportunity();
    }

    public function publishDirectly()
    {
        $this->status = 'published';
        $this->saveOpportunity();
    }

    public function saveOpportunity()
    {
        $this->isProcessing = true;

        try {
            $validated = $this->validate();

            // Generate unique slug
            $slug = Str::slug($this->title);
            $count = 1;
            while (AttachmentOpportunity::where('slug', $slug)->exists()) {
                $slug = Str::slug($this->title) . '-' . $count++;
            }

            // Prepare array data
            // Map application_deadline to deadline for database
            $opportunityData = [
                'title' => $validated['title'],
                'organization_id' => $validated['organization_id'],
                'description' => $validated['description'],
                'responsibilities' => $validated['responsibilities'] ?? null,
                'requirements' => $validated['requirements'] ?? null,
                'benefits' => $validated['benefits'] ?? null,
                'duration_months' => $validated['duration_months'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'deadline' => $this->application_deadline, // <-- Map here
                'is_remote' => $this->is_remote,
                'is_hybrid' => $this->is_hybrid,
                'location' => $this->location,
                'county' => $this->county,
                'town' => $this->town,
                'slots_available' => $validated['slots_available'],
                'requires_portfolio' => $this->requires_portfolio,
                'requires_cover_letter' => $this->requires_cover_letter,
                'stipend' => $this->stipend,
                'stipend_frequency' => $this->stipend_frequency,
                'min_gpa' => $this->min_cgpa, // Note: also mapping min_cgpa to min_gpa if needed
                'min_year_of_study' => $this->min_year_of_study,
                'slug' => $slug,
                'skills_required' => !empty($this->required_skills) ? $this->required_skills : null,
                'preferred_skills' => !empty($this->preferred_skills) ? $this->preferred_skills : null,
                'preferred_courses' => !empty($this->preferred_courses) ? $this->preferred_courses : null,
                'target_institutions' => !empty($this->target_institutions) ? $this->target_institutions : null,
                'other_benefits' => !empty($this->other_benefits) ? $this->other_benefits : null,
                'slots_filled' => 0,
                'views' => 0,
                'applications_count' => 0,
                'published_at' => $this->status === 'published' ? now() : null,
            ];

            $opportunity = AttachmentOpportunity::create($opportunityData);

            $message = match ($this->status) {
                'draft' => 'Opportunity saved as draft!',
                'pending_approval' => 'Opportunity submitted for approval!',
                'published' => 'Opportunity published successfully!',
                default => 'Opportunity created!'
            };

            $this->dispatch(
                'notify',
                type: 'success',
                message: $message
            );

            // Delay redirect slightly to show toast
            $this->dispatch(
                'redirect-after-save',
                url: match ($this->status) {
                    'published' => route('admin.opportunities.show', $opportunity),
                    'pending_approval' => route('admin.opportunities.pending'),
                    default => route('admin.opportunities.show', $opportunity),
                }
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error saving opportunity: ' . $e->getMessage()
            );
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view('livewire.admin.opportunities.create');
    }
}
