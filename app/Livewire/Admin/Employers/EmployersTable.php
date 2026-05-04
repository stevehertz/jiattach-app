<?php

namespace App\Livewire\Admin\Employers;

use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class EmployersTable extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $filterVerified = '';

    #[Url]
    public $filterActive = '';

    #[Url]
    public $filterCounty = '';

    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterVerified' => ['except' => ''],
        'filterActive' => ['except' => ''],
        'filterCounty' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterVerified()
    {
        $this->resetPage();
    }

    public function updatingFilterActive()
    {
        $this->resetPage();
    }

    public function updatingFilterCounty()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterVerified = '';
        $this->filterActive = '';
        $this->filterCounty = '';
        $this->resetPage();
    }

    public function verifyEmployer($employerId)
    {
        $employer = User::findOrFail($employerId);
        $employer->update([
            'is_verified' => true,
            'email_verified_at' => $employer->email_verified_at ?? now(),
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Employer verified successfully.'
        ]);
    }

    public function toggleActive($employerId)
    {
        $employer = User::findOrFail($employerId);
        $employer->update(['is_active' => !$employer->is_active]);

        $status = $employer->is_active ? 'activated' : 'deactivated';
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Employer {$status} successfully."
        ]);
    }

    public function deleteEmployer($employerId)
    {
        $employer = User::findOrFail($employerId);

        // Remove organization associations
        $employer->organizations()->detach();
        $employer->removeRole('employer');
        $employer->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Employer deleted successfully.'
        ]);
    }

    public function confirmDelete($employerId)
    {
        $this->dispatch('confirm-delete', [
            'employerId' => $employerId,
            'message' => 'Are you sure you want to delete this employer? This action cannot be undone.'
        ]);
    }



    public function render()
    {
        $query = User::role('employer')
            ->with(['organizations' => function ($q) {
                $q->withPivot(['role', 'position', 'is_primary_contact', 'is_active'])
                    ->where('organization_user.is_active', true);
            }])
            ->withCount(['organizations as active_organizations_count' => function ($q) {
                $q->where('organization_user.is_active', true);
            }]);

        // Apply search
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('phone', 'like', $searchTerm)
                    ->orWhereHas('organizations', function ($orgQuery) use ($searchTerm) {
                        $orgQuery->where('name', 'like', $searchTerm);
                    });
            });
        }

        // Filter by verified status
        if ($this->filterVerified !== '') {
            $query->where('users.is_verified', $this->filterVerified === 'yes');
        }

        // Filter by active status
        if ($this->filterActive !== '') {
            $query->where('users.is_active', $this->filterActive === 'yes');
        }

        // Filter by county
        if ($this->filterCounty) {
            $query->where('users.county', $this->filterCounty);
        }

        $employers = $query->latest()
            ->paginate($this->perPage);

        // Get unique counties for filter dropdown
        $counties = User::role('employer')
            ->whereNotNull('county')
            ->distinct()
            ->pluck('county')
            ->toArray();
        return view('livewire.admin.employers.employers-table', [
            'employers' => $employers,
            'counties' => $counties,
        ]);
    }
}
