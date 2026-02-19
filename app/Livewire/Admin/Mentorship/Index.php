<?php

namespace App\Livewire\Admin\Mentorship;

use App\Models\Mentorship;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;

    public $selectedMentorships = [];
    public $selectAll = false;

    // Statistics
    public $totalMentorships;
    public $activeMentorships;
    public $completedMentorships;
    public $pendingMentorships;

    public function mount()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $this->totalMentorships = Mentorship::count();
        $this->activeMentorships = Mentorship::where('status', 'active')->count();
        $this->completedMentorships = Mentorship::where('status', 'completed')->count();
        $this->pendingMentorships = Mentorship::whereIn('status', ['requested', 'pending_approval'])->count();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedMentorships = $this->mentorships->pluck('id')->toArray();
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

    public function deleteSelected()
    {
        if (empty($this->selectedMentorships)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Please select mentorships to delete']);
            return;
        }

        Mentorship::whereIn('id', $this->selectedMentorships)->delete();

        $this->selectedMentorships = [];
        $this->selectAll = false;
        $this->loadStatistics();

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Selected mentorships deleted successfully']);
    }

    public function updateStatus($mentorshipId, $status)
    {
        $mentorship = Mentorship::findOrFail($mentorshipId);

        $validStatuses = ['active', 'paused', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Invalid status']);
            return;
        }

        $mentorship->update(['status' => $status]);
        $this->loadStatistics();

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Mentorship status updated']);
    }

    public function getMentorshipsProperty()
    {
        return Mentorship::query()
            ->with(['mentor.user', 'mentee.user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
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
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('is_paid', $this->typeFilter === 'paid');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }


    public function render()
    {
        return view('livewire.admin.mentorship.index', [
            'totalMentorships' => $this->totalMentorships,
            'activeMentorships' => $this->activeMentorships,
            'completedMentorships' => $this->completedMentorships,
            'pendingMentorships' => $this->pendingMentorships,
            'mentorships' => $this->mentorships,
        ]);
    }
}
