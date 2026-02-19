<?php

namespace App\Livewire\Admin\Mentors;

use App\Models\Mentor;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $expertiseFilter = '';
    public $industryFilter = '';
    public $experienceFilter = '';
    public $ratingFilter = '';
    public $verificationFilter = '';
    public $featuredFilter = '';
    public $availabilityFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 20;
    public $selectedMentors = [];
    public $selectAll = false;
    public $showFilters = false;
    public $showBulkActions = false;
    public $viewType = 'all'; // 'all', 'verified', 'featured', 'available'

    protected $listeners = ['refreshMentors' => '$refresh'];

    public function mount($viewType = 'all')
    {
        $this->viewType = $viewType;
        $this->applyViewFilters();
    }

    protected function applyViewFilters()
    {
        switch ($this->viewType) {
            case 'verified':
                $this->verificationFilter = 'verified';
                break;
            case 'featured':
                $this->featuredFilter = 'featured';
                break;
            case 'available':
                $this->availabilityFilter = 'available';
                break;
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedMentors = $this->getMentorsQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedMentors = [];
        }
    }

    public function updatedSelectedMentors()
    {
        $this->selectAll = false;
        $this->showBulkActions = count($this->selectedMentors) > 0;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function getMentorsQuery()
    {
        $query = Mentor::with(['user', 'mentorships', 'reviews'])
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('job_title', 'like', '%' . $search . '%')
                        ->orWhere('company', 'like', '%' . $search . '%')
                        ->orWhere('bio', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($this->expertiseFilter, function ($query, $expertise) {
                $query->whereJsonContains('areas_of_expertise', $expertise);
            })
            ->when($this->industryFilter, function ($query, $industry) {
                $query->whereJsonContains('industries', $industry);
            })
            ->when($this->experienceFilter === 'entry', function ($query) {
                $query->where('years_of_experience', '<', 2);
            })
            ->when($this->experienceFilter === 'junior', function ($query) {
                $query->whereBetween('years_of_experience', [2, 4]);
            })
            ->when($this->experienceFilter === 'mid', function ($query) {
                $query->whereBetween('years_of_experience', [5, 9]);
            })
            ->when($this->experienceFilter === 'senior', function ($query) {
                $query->where('years_of_experience', '>=', 10);
            })
            ->when($this->ratingFilter, function ($query, $rating) {
                $query->where('average_rating', '>=', $rating);
            })
            ->when($this->verificationFilter === 'verified', function ($query) {
                $query->where('is_verified', true);
            })
            ->when($this->verificationFilter === 'unverified', function ($query) {
                $query->where('is_verified', false);
            })
            ->when($this->featuredFilter === 'featured', function ($query) {
                $query->where('is_featured', true);
            })
            ->when($this->featuredFilter === 'not_featured', function ($query) {
                $query->where('is_featured', false);
            })
            ->when($this->availabilityFilter === 'available', function ($query) {
                $query->where('availability', 'available')
                    ->whereRaw('current_mentees < max_mentees');
            })
            ->when($this->availabilityFilter === 'limited', function ($query) {
                $query->where('availability', 'limited');
            })
            ->when($this->availabilityFilter === 'full', function ($query) {
                $query->where('availability', 'fully_booked');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

    public function getMentorsProperty()
    {
        return $this->getMentorsQuery()->paginate($this->perPage);
    }

    public function getStatsProperty()
    {
        $total = Mentor::count();
        $verified = Mentor::where('is_verified', true)->count();
        $featured = Mentor::where('is_featured', true)->count();
        $available = Mentor::where('availability', 'available')
            ->whereRaw('current_mentees < max_mentees')
            ->count();
        $activeMentorships = \App\Models\Mentorship::where('status', 'active')->count();
        $totalSessions = Mentor::sum('total_sessions_conducted');

        return [
            'total' => $total,
            'verified' => $verified,
            'featured' => $featured,
            'available' => $available,
            'active_mentorships' => $activeMentorships,
            'total_sessions' => $totalSessions,
            'today' => Mentor::whereDate('created_at', today())->count(),
        ];
    }

    public function getExpertisesProperty()
    {
        $allExpertises = Mentor::whereNotNull('areas_of_expertise')
            ->pluck('areas_of_expertise')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return $allExpertises;
    }

    public function getIndustriesProperty()
    {
        $allIndustries = Mentor::whereNotNull('industries')
            ->pluck('industries')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return $allIndustries;
    }

    public function verifyMentor($mentorId)
    {
        $mentor = Mentor::findOrFail($mentorId);
        $mentor->verify();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Mentor verified successfully!'
        ]);

        $this->dispatch('refreshMentors');
    }

    public function unverifyMentor($mentorId)
    {
        $mentor = Mentor::findOrFail($mentorId);
        $mentor->unverify();

        $this->dispatch('show-toast', [
            'type' => 'warning',
            'message' => 'Mentor unverified successfully!'
        ]);

        $this->dispatch('refreshMentors');
    }

    public function featureMentor($mentorId)
    {
        $mentor = Mentor::findOrFail($mentorId);
        $mentor->feature();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Mentor featured successfully!'
        ]);

        $this->dispatch('refreshMentors');
    }

    public function unfeatureMentor($mentorId)
    {
        $mentor = Mentor::findOrFail($mentorId);
        $mentor->unfeature();

        $this->dispatch('show-toast', [
            'type' => 'warning',
            'message' => 'Mentor unfeatured successfully!'
        ]);

        $this->dispatch('refreshMentors');
    }

    public function deleteMentor($mentorId)
    {
        $mentor = Mentor::findOrFail($mentorId);

        // Check if mentor has active mentorships
        if ($mentor->activeMentorships()->count() > 0) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot delete mentor with active mentorships.'
            ]);
            return;
        }

        // Delete the associated user
        if ($mentor->user) {
            $mentor->user->delete();
        }

        $mentor->delete();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Mentor deleted successfully!'
        ]);

        $this->dispatch('refreshMentors');
    }

    public function bulkVerify()
    {
        Mentor::whereIn('id', $this->selectedMentors)->update(['is_verified' => true]);
        $this->selectedMentors = [];
        $this->showBulkActions = false;

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Selected mentors verified successfully!'
        ]);
    }

    public function bulkUnverify()
    {
        Mentor::whereIn('id', $this->selectedMentors)->update(['is_verified' => false]);
        $this->selectedMentors = [];
        $this->showBulkActions = false;

        $this->dispatch('show-toast', [
            'type' => 'warning',
            'message' => 'Selected mentors unverified successfully!'
        ]);
    }

    public function bulkFeature()
    {
        Mentor::whereIn('id', $this->selectedMentors)->update(['is_featured' => true]);
        $this->selectedMentors = [];
        $this->showBulkActions = false;

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Selected mentors featured successfully!'
        ]);
    }

    public function viewMentor($mentorId)
    {
        return redirect()->route('admin.mentors.show', $mentorId);
    }
    public function render()
    {
        return view('livewire.admin.mentors.index', [
            'mentors' => $this->mentors,
            'stats' => $this->stats,
            'expertises' => $this->expertises,
            'industries' => $this->industries,
        ]);
    }
}
