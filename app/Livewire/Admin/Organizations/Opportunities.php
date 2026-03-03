<?php

namespace App\Livewire\Admin\Organizations;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Organization;
use App\Models\AttachmentOpportunity;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class Opportunities extends Component
{
    use WithPagination;

    public Organization $organization;
    
    // Filters
    public $search = '';
    public $work_type_filter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 10;
    
    // Modal properties
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $editingOpportunity = null;
    
    // Form properties
    public $opportunity_id;
    public $title;
    public $description;
    public $responsibilities = [];
    public $newResponsibility = '';
    public $type;
    public $work_type;
    public $location;
    public $county;
    public $min_gpa;
    public $skills_required = [];
    public $newSkill = '';
    public $courses_required = [];
    public $start_date;
    public $end_date;
    public $duration_months;
    public $stipend;
    public $slots_available;
    public $deadline;
    public $status = 'draft';
    
    // UI helpers
    public $showResponsibilityInput = false;
    public $showSkillInput = false;
    public $showCourseSearch = false;
    public $courseSearch = '';
    public $availableCourses = [];

    protected $listeners = [
        'deleteConfirmed' => 'deleteOpportunity',
        'refresh' => '$refresh'
    ];

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'responsibilities' => 'nullable|array',
            'type' => 'required|in:attachment,internship,industrial_attachment,volunteer,graduate_trainee',
            'work_type' => 'required|in:onsite,remote,hybrid',
            'location' => 'required|string|max:255',
            'county' => 'nullable|string|max:255',
            'min_gpa' => 'nullable|numeric|min:0|max:4.0',
            'skills_required' => 'nullable|array',
            'courses_required' => 'nullable|array',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'duration_months' => 'nullable|integer|min:1',
            'stipend' => 'nullable|numeric|min:0',
            'slots_available' => 'required|integer|min:1',
            'deadline' => 'required|date|after:today',
            'status' => 'required|in:draft,pending_approval,published,closed,filled,cancelled',
        ];
    }

    protected $messages = [
        'deadline.after' => 'The deadline must be a future date.',
        'end_date.after' => 'The end date must be after the start date.',
        'slots_available.min' => 'At least 1 slot is required.',
        'type.required' => 'Please select an opportunity type.',
        'work_type.required' => 'Please select a work type.',
    ];

    public function mount(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedWorkTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedCourseSearch()
    {
        if (strlen($this->courseSearch) >= 2) {
            $this->availableCourses = Course::where('name', 'like', '%' . $this->courseSearch . '%')
                ->orWhere('code', 'like', '%' . $this->courseSearch . '%')
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->availableCourses = [];
        }
    }

    // Responsibility Management
    public function addResponsibility()
    {
        if (trim($this->newResponsibility)) {
            $this->responsibilities[] = $this->newResponsibility;
            $this->newResponsibility = '';
            $this->showResponsibilityInput = false;
        }
    }

    public function removeResponsibility($index)
    {
        unset($this->responsibilities[$index]);
        $this->responsibilities = array_values($this->responsibilities);
    }

    // Skills Management
    public function addSkill()
    {
        if (trim($this->newSkill)) {
            $this->skills_required[] = $this->newSkill;
            $this->newSkill = '';
            $this->showSkillInput = false;
        }
    }

    public function removeSkill($index)
    {
        unset($this->skills_required[$index]);
        $this->skills_required = array_values($this->skills_required);
    }

    // Course Management
    public function addCourse($courseId)
    {
        $course = Course::find($courseId);
        if ($course && !in_array($courseId, $this->courses_required)) {
            $this->courses_required[] = $courseId;
            $this->courseSearch = '';
            $this->availableCourses = [];
            $this->showCourseSearch = false;
        }
    }

    public function removeCourse($courseId)
    {
        $this->courses_required = array_values(array_diff($this->courses_required, [$courseId]));
    }

    public function getCourseName($courseId)
    {
        $course = Course::find($courseId);
        return $course ? $course->name . ' (' . $course->code . ')' : 'Unknown Course';
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($id)
    {
        $this->editingOpportunity = AttachmentOpportunity::findOrFail($id);
        $this->opportunity_id = $this->editingOpportunity->id;
        $this->title = $this->editingOpportunity->title;
        $this->description = $this->editingOpportunity->description;
        $this->responsibilities = $this->editingOpportunity->responsibilities ?? [];
        $this->type = $this->editingOpportunity->type;
        $this->work_type = $this->editingOpportunity->work_type;
        $this->location = $this->editingOpportunity->location;
        $this->county = $this->editingOpportunity->county;
        $this->min_gpa = $this->editingOpportunity->min_gpa;
        $this->skills_required = $this->editingOpportunity->skills_required ?? [];
        $this->courses_required = $this->editingOpportunity->courses_required ?? [];
        $this->start_date = $this->editingOpportunity->start_date?->format('Y-m-d');
        $this->end_date = $this->editingOpportunity->end_date?->format('Y-m-d');
        $this->duration_months = $this->editingOpportunity->duration_months;
        $this->stipend = $this->editingOpportunity->stipend;
        $this->slots_available = $this->editingOpportunity->slots_available;
        $this->deadline = $this->editingOpportunity->deadline?->format('Y-m-d');
        $this->status = $this->editingOpportunity->status;
        
        $this->showEditModal = true;
    }

    public function openDeleteModal($id)
    {
        $this->opportunity_id = $id;
        $this->showDeleteModal = true;
    }

    public function createOpportunity()
    {
        $this->validate();

        DB::transaction(function () {
            AttachmentOpportunity::create([
                'organization_id' => $this->organization->id,
                'title' => $this->title,
                'description' => $this->description,
                'responsibilities' => $this->responsibilities,
                'type' => $this->type,
                'work_type' => $this->work_type,
                'location' => $this->location,
                'county' => $this->county,
                'min_gpa' => $this->min_gpa,
                'skills_required' => $this->skills_required,
                'courses_required' => $this->courses_required,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'duration_months' => $this->duration_months,
                'stipend' => $this->stipend,
                'slots_available' => $this->slots_available,
                'deadline' => $this->deadline,
                'status' => $this->status,
                'published_at' => $this->status === 'published' ? now() : null,
            ]);
        });

        $this->dispatch('toastr:success', message: 'Opportunity created successfully.');
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function updateOpportunity()
    {
        $this->validate();

        DB::transaction(function () {
            $data = [
                'title' => $this->title,
                'description' => $this->description,
                'responsibilities' => $this->responsibilities,
                'type' => $this->type,
                'work_type' => $this->work_type,
                'location' => $this->location,
                'county' => $this->county,
                'min_gpa' => $this->min_gpa,
                'skills_required' => $this->skills_required,
                'courses_required' => $this->courses_required,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'duration_months' => $this->duration_months,
                'stipend' => $this->stipend,
                'slots_available' => $this->slots_available,
                'deadline' => $this->deadline,
                'status' => $this->status,
            ];

            // Update published_at if status changes to published
            if ($this->status === 'published' && $this->editingOpportunity->status !== 'published') {
                $data['published_at'] = now();
            }

            $this->editingOpportunity->update($data);
        });

        $this->dispatch('toastr:success', message: 'Opportunity updated successfully.');
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function deleteOpportunity()
    {
        $opportunity = AttachmentOpportunity::find($this->opportunity_id);
        
        if ($opportunity) {
            // Check if there are any applications
            if ($opportunity->applications()->count() > 0) {
                $this->dispatch('toastr:error', message: 'Cannot delete opportunity with existing applications.');
                $this->showDeleteModal = false;
                return;
            }
            
            $opportunity->delete();
            $this->dispatch('toastr:success', message: 'Opportunity deleted successfully.');
        }
        
        $this->showDeleteModal = false;
        $this->opportunity_id = null;
    }

    public function updateStatus($id, $newStatus)
    {
        $opportunity = AttachmentOpportunity::find($id);
        if ($opportunity) {
            $data = ['status' => $newStatus];
            
            if ($newStatus === 'published' && !$opportunity->published_at) {
                $data['published_at'] = now();
            }
            
            $opportunity->update($data);
            $this->dispatch('toastr:success', message: 'Opportunity status updated.');
        }
    }

    public function resetForm()
    {
        $this->reset([
            'opportunity_id', 'title', 'description', 'responsibilities', 'type',
            'work_type', 'location', 'county', 'min_gpa', 'skills_required',
            'courses_required', 'start_date', 'end_date', 'duration_months',
            'stipend', 'slots_available', 'deadline', 'status', 'editingOpportunity',
            'newResponsibility', 'newSkill', 'courseSearch', 'availableCourses'
        ]);
        $this->responsibilities = [];
        $this->skills_required = [];
        $this->courses_required = [];
        $this->status = 'draft';
    }

    public function getFilteredOpportunities()
    {
        return AttachmentOpportunity::where('organization_id', $this->organization->id)
            ->withCount('applications')
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('location', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function($query) {
                $query->where('status', $this->status);
            })
            ->when($this->type, function($query) {
                $query->where('type', $this->type);
            })
            ->when($this->work_type_filter, function($query) {
                $query->where('work_type', $this->work_type_filter);
            })
            ->when($this->county, function($query) {
                $query->where('county', $this->county);
            })
            ->when($this->dateFrom, function($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->latest();
    }

    public function render()
    {
        $opportunities = $this->getFilteredOpportunities()->paginate($this->perPage);
        
        $stats = [
            'total' => AttachmentOpportunity::where('organization_id', $this->organization->id)->count(),
            'published' => AttachmentOpportunity::where('organization_id', $this->organization->id)
                ->where('status', 'published')
                ->count(),
            'draft' => AttachmentOpportunity::where('organization_id', $this->organization->id)
                ->where('status', 'draft')
                ->count(),
            'pending' => AttachmentOpportunity::where('organization_id', $this->organization->id)
                ->where('status', 'pending_approval')
                ->count(),
            'total_applications' => AttachmentOpportunity::where('organization_id', $this->organization->id)
                ->withCount('applications')
                ->get()
                ->sum('applications_count'),
        ];

        $statuses = [
            'draft' => 'Draft',
            'pending_approval' => 'Pending Approval',
            'published' => 'Published',
            'closed' => 'Closed',
            'filled' => 'Filled',
            'cancelled' => 'Cancelled'
        ];

        $workTypes = [
            'onsite' => 'On-site',
            'remote' => 'Remote',
            'hybrid' => 'Hybrid'
        ];

        $opportunityTypes = [
            'attachment' => 'Attachment',
            'internship' => 'Internship',
            'industrial_attachment' => 'Industrial Attachment',
            'volunteer' => 'Volunteer',
            'graduate_trainee' => 'Graduate Trainee'
        ];

        $counties = [
            'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Kiambu', 'Machakos',
            'Uasin Gishu', 'Kericho', 'Kakamega', 'Kilifi', 'Kwale', 'Lamu',
            'Taita Taveta', 'Garissa', 'Wajir', 'Mandera', 'Marsabit', 'Isiolo',
            'Meru', 'Tharaka Nithi', 'Embu', 'Kitui', 'Makueni', 'Nyandarua',
            'Nyeri', 'Kirinyaga', 'Muranga', 'Nyamira', 'Kisii', 'Homa Bay',
            'Migori', 'Siaya', 'Vihiga', 'Bungoma', 'Busia', 'Trans Nzoia',
            'Elgeyo Marakwet', 'Nandi', 'Baringo', 'Laikipia', 'Samburu',
            'Turkana', 'West Pokot', 'Kajiado', 'Narok', 'Bomet'
        ];

        return view('livewire.admin.organizations.opportunities', [
            'opportunities' => $opportunities,
            'stats' => $stats,
            'statuses' => $statuses,
            'workTypes' => $workTypes,
            'opportunityTypes' => $opportunityTypes,
            'counties' => $counties,
        ]);
    }
}