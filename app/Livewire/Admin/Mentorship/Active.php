<?php

namespace App\Livewire\Admin\Mentorship;

use App\Models\Mentorship;
use Livewire\Component;
use Livewire\WithPagination;

class Active extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'start_date';
    public $sortDirection = 'asc';
    public $perPage = 15;

    public function getActiveMentorshipsProperty()
    {
        return Mentorship::query()
            ->with(['mentor.user', 'mentee.user', 'sessions'])
            ->where('status', 'active')
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
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
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

    public function render()
    {
        return view('livewire.admin.mentorship.active', [
            'activeMentorships' => $this->activeMentorships,
        ]);
    }
}
