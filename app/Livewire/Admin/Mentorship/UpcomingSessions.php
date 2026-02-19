<?php

namespace App\Livewire\Admin\Mentorship;

use App\Models\Mentorship;
use App\Models\MentorshipSession;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class UpcomingSessions extends Component
{
    use WithPagination;
    
    public $search = '';
    public $mentorshipFilter = '';
    public $statusFilter = '';
    public $dateRange = '';
    public $sortBy = 'scheduled_start_time';
    public $sortDirection = 'asc';
    public $perPage = 15;
    
    public $selectedSessions = [];
    public $selectAll = false;
    
    // Statistics
    public $totalUpcoming;
    public $todaySessions;
    public $tomorrowSessions;
    public $thisWeekSessions;
    
    public function mount()
    {
        $this->loadStatistics();
    }
    
    public function loadStatistics()
    {
        $this->totalUpcoming = MentorshipSession::whereIn('status', ['scheduled', 'confirmed'])
            ->where('scheduled_start_time', '>', now())
            ->count();
            
        $this->todaySessions = MentorshipSession::whereIn('status', ['scheduled', 'confirmed'])
            ->whereDate('scheduled_start_time', Carbon::today())
            ->count();
            
        $this->tomorrowSessions = MentorshipSession::whereIn('status', ['scheduled', 'confirmed'])
            ->whereDate('scheduled_start_time', Carbon::tomorrow())
            ->count();
            
        $this->thisWeekSessions = MentorshipSession::whereIn('status', ['scheduled', 'confirmed'])
            ->whereBetween('scheduled_start_time', [now(), now()->endOfWeek()])
            ->count();
    }
    
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedSessions = $this->upcomingSessions->pluck('id')->toArray();
        } else {
            $this->selectedSessions = [];
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
    
    public function confirmSession($sessionId)
    {
        $session = MentorshipSession::findOrFail($sessionId);
        $session->update(['status' => 'confirmed']);
        
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Session confirmed']);
    }
    
    public function cancelSession($sessionId)
    {
        $session = MentorshipSession::findOrFail($sessionId);
        $session->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id()
        ]);
        
        $this->loadStatistics();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Session cancelled']);
    }
    
    public function markAsMissed($sessionId)
    {
        $session = MentorshipSession::findOrFail($sessionId);
        $session->update(['status' => 'missed']);
        
        $this->loadStatistics();
        $this->dispatch('notify', ['type' => 'warning', 'message' => 'Session marked as missed']);
    }
    
    public function getUpcomingSessionsProperty()
    {
        $query = MentorshipSession::query()
            ->with(['mentorship.mentor.user', 'mentorship.mentee.user'])
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where('scheduled_start_time', '>', now());
            
        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('mentorship.mentor.user', function ($q) {
                      $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('mentorship.mentee.user', function ($q) {
                      $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        // Mentorship filter
        if ($this->mentorshipFilter) {
            $query->where('mentorship_id', $this->mentorshipFilter);
        }
        
        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        
        // Date range filter
        if ($this->dateRange) {
            switch ($this->dateRange) {
                case 'today':
                    $query->whereDate('scheduled_start_time', Carbon::today());
                    break;
                case 'tomorrow':
                    $query->whereDate('scheduled_start_time', Carbon::tomorrow());
                    break;
                case 'this_week':
                    $query->whereBetween('scheduled_start_time', [now(), now()->endOfWeek()]);
                    break;
                case 'next_week':
                    $query->whereBetween('scheduled_start_time', [now()->startOfWeek()->addWeek(), now()->endOfWeek()->addWeek()]);
                    break;
                case 'this_month':
                    $query->whereBetween('scheduled_start_time', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
            }
        }
        
        return $query->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
    }
    
    public function getMentorshipsProperty()
    {
        return Mentorship::where('status', 'active')
            ->with(['mentor.user', 'mentee.user'])
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.mentorship.upcoming-sessions', [
            'upcomingSessions' => $this->upcomingSessions,
            'mentorships' => $this->mentorships,
            'totalUpcoming' => $this->totalUpcoming,
            'todaySessions' => $this->todaySessions,
            'tomorrowSessions' => $this->tomorrowSessions,
            'thisWeekSessions' => $this->thisWeekSessions,
        ]);
    }
}
