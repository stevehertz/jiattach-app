<?php

namespace App\Livewire\Admin\Mentorship;

use App\Models\Mentorship;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Completed extends Component
{
    use WithPagination;
    
    public $search = '';
    public $mentorFilter = '';
    public $menteeFilter = '';
    public $dateRange = '';
    public $ratingFilter = '';
    public $sortBy = 'completed_at';
    public $sortDirection = 'desc';
    public $perPage = 15;
    
    public $selectedMentorships = [];
    public $selectAll = false;
    
    // Statistics
    public $totalCompleted;
    public $thisMonthCompleted;
    public $lastMonthCompleted;
    public $averageDuration;
    public $successRate;
    
    public function mount()
    {
        $this->loadStatistics();
    }
    
    public function loadStatistics()
    {
        $this->totalCompleted = Mentorship::where('status', 'completed')->count();
        
        $this->thisMonthCompleted = Mentorship::where('status', 'completed')
            ->whereMonth('completed_at', Carbon::now()->month)
            ->whereYear('completed_at', Carbon::now()->year)
            ->count();
            
        $this->lastMonthCompleted = Mentorship::where('status', 'completed')
            ->whereMonth('completed_at', Carbon::now()->subMonth()->month)
            ->whereYear('completed_at', Carbon::now()->subMonth()->year)
            ->count();
        
        // Calculate average duration
        $completedMentorships = Mentorship::where('status', 'completed')
            ->whereNotNull('start_date')
            ->whereNotNull('completed_at')
            ->get();
            
        if ($completedMentorships->count() > 0) {
            $totalWeeks = $completedMentorships->sum(function($mentorship) {
                return $mentorship->start_date->diffInWeeks($mentorship->completed_at);
            });
            $this->averageDuration = round($totalWeeks / $completedMentorships->count(), 1);
        } else {
            $this->averageDuration = 0;
        }
        
        // Calculate success rate (mentorships with reviews)
        $withReviews = Mentorship::where('status', 'completed')
            ->has('reviews')
            ->count();
            
        $this->successRate = $this->totalCompleted > 0 ? 
            round(($withReviews / $this->totalCompleted) * 100, 1) : 0;
    }
    
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedMentorships = $this->completedMentorships->pluck('id')->toArray();
        } else {
            $this->selectedMentorships = [];
        }
    }
    
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortBy = $field;
    }
    
    public function reopenMentorship($mentorshipId)
    {
        $mentorship = Mentorship::findOrFail($mentorshipId);
        $mentorship->update(['status' => 'active']);
        
        $this->loadStatistics();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Mentorship reopened successfully']);
    }
    
    public function archiveSelected()
    {
        if (empty($this->selectedMentorships)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Please select mentorships to archive']);
            return;
        }
        
        Mentorship::whereIn('id', $this->selectedMentorships)->update(['is_archived' => true]);
        
        $this->selectedMentorships = [];
        $this->selectAll = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Selected mentorships archived']);
    }
    
    public function getCompletedMentorshipsProperty()
    {
        $query = Mentorship::query()
            ->with(['mentor.user', 'mentee.user', 'reviews'])
            ->where('status', 'completed');
            
        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('completion_notes', 'like', '%' . $this->search . '%')
                  ->orWhereHas('mentor.user', function ($q) {
                      $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('mentee.user', function ($q) {
                      $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        // Mentor filter
        if ($this->mentorFilter) {
            $query->whereHas('mentor.user', function ($q) {
                $q->where('id', $this->mentorFilter);
            });
        }
        
        // Mentee filter
        if ($this->menteeFilter) {
            $query->whereHas('mentee.user', function ($q) {
                $q->where('id', $this->menteeFilter);
            });
        }
        
        // Date range filter
        if ($this->dateRange) {
            switch ($this->dateRange) {
                case 'today':
                    $query->whereDate('completed_at', Carbon::today());
                    break;
                case 'this_week':
                    $query->whereBetween('completed_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereBetween('completed_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                    break;
                case 'last_month':
                    $query->whereBetween('completed_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()]);
                    break;
                case 'last_3_months':
                    $query->where('completed_at', '>=', Carbon::now()->subMonths(3));
                    break;
            }
        }
        
        // Rating filter
        if ($this->ratingFilter) {
            $query->whereHas('reviews', function ($q) {
                $q->where('overall_rating', '>=', $this->ratingFilter);
            });
        }
        
        return $query->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
    }
    
    public function getMentorsProperty()
    {
        return \App\Models\User::whereHas('mentor')
            ->with('mentor')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'mentor_id' => $user->mentor->id
                ];
            });
    }
    
    public function getMenteesProperty()
    {
        return \App\Models\User::whereHas('studentProfile')
            ->with('studentProfile')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'institution' => $user->studentProfile->institution_name ?? 'N/A'
                ];
            });
    }
    public function render()
    {
        return view('livewire.admin.mentorship.completed', [
            'completedMentorships' => $this->completedMentorships,
            'mentors' => $this->mentors,
            'mentees' => $this->mentees,
            'totalCompleted' => $this->totalCompleted,
            'thisMonthCompleted' => $this->thisMonthCompleted,
            'lastMonthCompleted' => $this->lastMonthCompleted,
            'averageDuration' => $this->averageDuration,
            'successRate' => $this->successRate,
        ]);
    }
}
