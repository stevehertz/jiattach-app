<?php

namespace App\Livewire\Admin\Opportunities;

use App\Models\AttachmentOpportunity;
use App\Models\Organization;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public AttachmentOpportunity $opportunity;

    // Basic Information
    public $title = '';
    public $organization_id = '';
    public $type = 'attachment';
    public $work_type = 'contract';
    public $description = '';
    public $responsibilities = '';
    public $requirements = '';
    public $benefits = '';

    // Location & Duration
    public $duration_months = 3;
    public $start_date;
    public $end_date;
    public $work_type_location = 'onsite'; // onsite, remote, hybrid
    public $location = '';
    public $county = '';
    public $town = '';

    // Application Details
    public $deadline;
    public $slots_available = 5;
    public $requires_portfolio = false;
    public $requires_cover_letter = true;

    // Stipend & Compensation
    public $stipend;
    public $stipend_frequency = 'monthly';
    public $other_benefits = [];

    // Requirements
    public $min_gpa;
    public $min_year_of_study;
    public $skills_required = [];
    public $preferred_skills = [];
    public $courses_required = [];

    // Status & UI State
    public $status = 'draft';
    public $isProcessing = false;
    public $activeTab = 'basic';

    // Skill inputs
    public $newSkill = '';
    public $newPreferredSkill = '';
    public $newCourse = '';
    public $newBenefitInput = '';

    protected $listeners = ['add-other-benefit' => 'handleAddBenefit'];

    protected $rules = [
        'title' => 'required|string|max:255',
        'organization_id' => 'required|exists:organizations,id',
        'type' => 'required|string',
        'work_type' => 'required|string',
        'description' => 'required|string|min:100',
        'responsibilities' => 'nullable|string',
        'requirements' => 'nullable|string',
        'benefits' => 'nullable|string',
        'duration_months' => 'required|integer|min:1',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'deadline' => 'required|date',
        'slots_available' => 'required|integer|min:1',
        'stipend' => 'nullable|numeric|min:0',
        'min_gpa' => 'nullable|numeric|between:0,4',
        'min_year_of_study' => 'nullable|integer|min:1|max:6',
    ];

     public function mount(AttachmentOpportunity $opportunity)
    {
        $this->opportunity = $opportunity;

        // Load existing data
        $this->title = $opportunity->title;
        $this->organization_id = $opportunity->organization_id;
        $this->type = $opportunity->type;
        $this->work_type = $opportunity->work_type;
        $this->description = $opportunity->description;
        $this->responsibilities = $opportunity->responsibilities;
        $this->requirements = $opportunity->requirements;
        $this->benefits = $opportunity->benefits;
        $this->duration_months = $opportunity->duration_months;
        $this->start_date = $opportunity->start_date?->format('Y-m-d');
        $this->end_date = $opportunity->end_date?->format('Y-m-d');
        $this->deadline = $opportunity->deadline?->format('Y-m-d');
        $this->location = $opportunity->location;
        $this->county = $opportunity->county;
        $this->town = $opportunity->town;
        $this->slots_available = $opportunity->slots_available;
        $this->requires_portfolio = $opportunity->requires_portfolio ?? false;
        $this->requires_cover_letter = $opportunity->requires_cover_letter ?? true;
        $this->stipend = $opportunity->stipend;
        $this->stipend_frequency = $opportunity->stipend_frequency ?? 'monthly';
        $this->min_gpa = $opportunity->min_gpa;
        $this->min_year_of_study = $opportunity->min_year_of_study;
        $this->status = $opportunity->status;

        // Handle arrays
        $this->skills_required = $opportunity->skills_required ?? [];
        $this->preferred_skills = $opportunity->preferred_skills ?? [];
        $this->courses_required = $opportunity->courses_required ?? [];
        $this->other_benefits = $opportunity->other_benefits ?? [];

        // Determine work type location for UI
        if ($opportunity->work_type === 'remote') {
            $this->work_type_location = 'remote';
        } elseif ($opportunity->work_type === 'hybrid') {
            $this->work_type_location = 'hybrid';
        } else {
            $this->work_type_location = 'onsite';
        }
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
        return [
            'attachment' => 'Industrial Attachment',
            'internship' => 'Internship',
            'traineeship' => 'Traineeship',
            'volunteer' => 'Volunteer',
        ];
    }

    public function getWorkTypesProperty()
    {
        return [
            'full-time' => 'Full Time',
            'part-time' => 'Part Time',
            'contract' => 'Contract',
            'temporary' => 'Temporary',
        ];
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

    public function previousTab()
    {
        $tabs = ['basic', 'details', 'requirements', 'review'];
        $currentIndex = array_search($this->activeTab, $tabs);
        if ($currentIndex > 0) {
            $this->activeTab = $tabs[$currentIndex - 1];
        }
    }

    public function isTabComplete(string $tab): bool
    {
        return match($tab) {
            'basic' => !empty($this->title) && !empty($this->organization_id) && strlen($this->description) >= 100,
            'details' => !empty($this->duration_months) && !empty($this->start_date) && !empty($this->end_date) && !empty($this->deadline),
            'requirements' => true,
            'review' => $this->isTabComplete('basic') && $this->isTabComplete('details'),
            default => false
        };
    }

    public function getCompletionPercentage(): int
    {
        $filled = 0;
        $fields = ['title', 'organization_id', 'description', 'duration_months', 'start_date', 'end_date', 'deadline', 'slots_available'];
        foreach ($fields as $field) {
            if (!empty($this->$field)) $filled++;
        }
        if (strlen($this->description) < 100) $filled--;
        return (int) round(($filled / count($fields)) * 100);
    }

    // Skills Management
    public function addRequiredSkill($skill = null)
    {
        $skillToAdd = $skill ?: $this->newSkill;
        
        if (!empty($skillToAdd) && !in_array(trim($skillToAdd), $this->skills_required)) {
            $this->skills_required[] = trim($skillToAdd);
            $this->newSkill = '';
        }
    }

    public function removeRequiredSkill($index)
    {
        unset($this->skills_required[$index]);
        $this->skills_required = array_values($this->skills_required);
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

    public function addCourse()
    {
        if (!empty($this->newCourse) && !in_array(trim($this->newCourse), $this->courses_required)) {
            $this->courses_required[] = trim($this->newCourse);
            $this->newCourse = '';
        }
    }

    public function removeCourse($index)
    {
        unset($this->courses_required[$index]);
        $this->courses_required = array_values($this->courses_required);
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

    // Update work type based on location selection
    public function updatedWorkTypeLocation($value)
    {
        $this->work_type = match($value) {
            'remote' => 'remote',
            'hybrid' => 'hybrid',
            default => 'contract', // or whatever default
        };
    }

    // Save Actions
    public function saveAsDraft()
    {
        $this->status = 'draft';
        $this->updateOpportunity();
    }

    public function submitForApproval()
    {
        $this->status = 'pending_approval';
        $this->updateOpportunity();
    }

    public function publishDirectly()
    {
        $this->status = 'open';
        $this->updateOpportunity();
    }

    public function updateOpportunity()
    {
        $this->isProcessing = true;

        try {
            $validated = $this->validate();

            // Handle slug update if title changed
            $slug = $this->opportunity->slug;
            if ($this->opportunity->title !== $this->title) {
                $slug = Str::slug($this->title);
                $count = 1;
                while (AttachmentOpportunity::where('slug', $slug)->where('id', '!=', $this->opportunity->id)->exists()) {
                    $slug = Str::slug($this->title) . '-' . $count++;
                }
            }

            // Update opportunity
            $this->opportunity->update([
                'title' => $validated['title'],
                'organization_id' => $validated['organization_id'],
                'type' => $validated['type'],
                'work_type' => $validated['work_type'],
                'description' => $validated['description'],
                'responsibilities' => $validated['responsibilities'],
                'requirements' => $validated['requirements'],
                'benefits' => $validated['benefits'],
                'duration_months' => $validated['duration_months'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'deadline' => $this->deadline,
                'location' => $this->location,
                'county' => $this->county,
                'town' => $this->town,
                'slots_available' => $validated['slots_available'],
                'requires_portfolio' => $this->requires_portfolio,
                'requires_cover_letter' => $this->requires_cover_letter,
                'stipend' => $this->stipend,
                'stipend_frequency' => $this->stipend_frequency,
                'min_gpa' => $this->min_gpa,
                'min_year_of_study' => $this->min_year_of_study,
                'skills_required' => !empty($this->skills_required) ? $this->skills_required : null,
                'preferred_skills' => !empty($this->preferred_skills) ? $this->preferred_skills : null,
                'courses_required' => !empty($this->courses_required) ? $this->courses_required : null,
                'other_benefits' => !empty($this->other_benefits) ? $this->other_benefits : null,
                'slug' => $slug,
                'status' => $this->status,
                'published_at' => $this->status === 'open' && !$this->opportunity->published_at ? now() : $this->opportunity->published_at,
            ]);

            $message = match ($this->status) {
                'draft' => 'Opportunity updated and saved as draft!',
                'pending_approval' => 'Opportunity updated and submitted for approval!',
                'open' => 'Opportunity updated and published successfully!',
                default => 'Opportunity updated successfully!'
            };

            session()->flash('message', $message);
            session()->flash('alert-type', 'success');

            return redirect()->route('admin.opportunities.show', $this->opportunity);

        } catch (\Exception $e) {
            $this->dispatch('notify',
                type: 'error',
                message: 'Error updating opportunity: ' . $e->getMessage()
            );
            $this->isProcessing = false;
        }
    }


    public function render()
    {
        return view('livewire.admin.opportunities.edit');
    }
}
