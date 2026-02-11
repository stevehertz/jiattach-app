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

    // Status
    public $status = 'draft';

    // Skill input
    public $newSkill = '';
    public $newPreferredSkill = '';
    public $newPreferredCourse = '';

    protected $listeners = ['refreshEmployers' => '$refresh'];

    // Or make it a regular property that's set in mount
    // public $employersList = [];

    public function mount()
    {
        $this->start_date = now()->addWeek()->format('Y-m-d');
        $this->end_date = now()->addMonths(3)->format('Y-m-d');
        $this->application_deadline = now()->addDays(14)->format('Y-m-d');

        // Load employers in mount
    }

    /**
     * Computed property to get Organizations for the dropdown
     */
    public function getOrganizationsProperty()
    {
        return Organization::with('user')
            ->orderBy('name') // Assuming 'name' is the field in Organizations
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

    public function addRequiredSkill()
    {
        if (!empty($this->newSkill) && !in_array($this->newSkill, $this->required_skills)) {
            $this->required_skills[] = trim($this->newSkill);
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
        if (!empty($this->newPreferredSkill) && !in_array($this->newPreferredSkill, $this->preferred_skills)) {
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
        if (!empty($this->newPreferredCourse) && !in_array($this->newPreferredCourse, $this->preferred_courses)) {
            $this->preferred_courses[] = trim($this->newPreferredCourse);
            $this->newPreferredCourse = '';
        }
    }

    public function removePreferredCourse($index)
    {
        unset($this->preferred_courses[$index]);
        $this->preferred_courses = array_values($this->preferred_courses);
    }

    public function addOtherBenefit($benefit)
    {
        if (!empty($benefit) && !in_array($benefit, $this->other_benefits)) {
            $this->other_benefits[] = $benefit;
        }
    }

    public function removeOtherBenefit($index)
    {
        unset($this->other_benefits[$index]);
        $this->other_benefits = array_values($this->other_benefits);
    }

    public function saveDraft()
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
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'organization_id' => 'required|exists:organizations,id', // Updated table name
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
            'status' => 'required|string',
        ]);

        // Generate slug from title
        $slug = Str::slug($this->title);
        $count = 1;
        while (AttachmentOpportunity::where('slug', $slug)->exists()) {
            $slug = Str::slug($this->title) . '-' . $count++;
        }

        // Prepare array data
        $arrayData = [
            'required_skills' => !empty($this->required_skills) ? $this->required_skills : null,
            'preferred_skills' => !empty($this->preferred_skills) ? $this->preferred_skills : null,
            'preferred_courses' => !empty($this->preferred_courses) ? $this->preferred_courses : null,
            'target_institutions' => !empty($this->target_institutions) ? $this->target_institutions : null,
            'other_benefits' => !empty($this->other_benefits) ? $this->other_benefits : null,
        ];

        // Create opportunity
        $opportunity = AttachmentOpportunity::create(array_merge($validated, [
            'slug' => $slug,
            'required_skills' => $arrayData['required_skills'],
            'preferred_skills' => $arrayData['preferred_skills'],
            'preferred_courses' => $arrayData['preferred_courses'],
            'target_institutions' => $arrayData['target_institutions'],
            'other_benefits' => $arrayData['other_benefits'],
            'slots_filled' => 0,
            'views' => 0,
            'applications_count' => 0,
            'published_at' => $this->status === 'published' ? now() : null,
        ]));

        $message = match ($this->status) {
            'draft' => 'Opportunity saved as draft!',
            'pending_approval' => 'Opportunity submitted for approval!',
            'published' => 'Opportunity published successfully!',
            default => 'Opportunity created!'
        };

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => $message
        ]);

        // Redirect based on status
        if ($this->status === 'published') {
            return redirect()->route('admin.opportunities.show', $opportunity->id);
        } elseif ($this->status === 'pending_approval') {
            return redirect()->route('admin.opportunities.pending');
        } else {
            return redirect()->route('admin.opportunities.edit', $opportunity->id);
        }
    }

    public function render()
    {
        return view('livewire.admin.opportunities.create', [
            'organizations' => $this->organizations,
            'counties' => getKenyanCounties(),
            'opportunityTypes' => getOpportunityTypes(),
            'employmentTypes' => getEmploymentTypes(),
            'commonSkills' => getCommonSkills(),
        ]);
    }
}
