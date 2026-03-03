<?php

namespace App\Livewire\Admin\Organizations;

use App\Models\Organization;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $industry = '';
    public $status = '';
    public $verification = '';
    public $county = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 15;

    public $selectedOrganizations = [];
    public $selectAll = false;

    public $showFilters = false;
    public $showBulkActions = false;

    protected $listeners = [
        'deleteConfirmed' => 'deleteOrganization',
        'bulkDeleteConfirmed' => 'bulkDelete',
        'refreshOrganizations' => '$refresh'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'industry' => ['except' => ''],
        'status' => ['except' => ''],
        'verification' => ['except' => ''],
        'county' => ['except' => ''],
        'perPage' => ['except' => 15],
    ];

    public function mount()
    {
        $this->selectedOrganizations = [];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedOrganizations = $this->getFilteredOrganizations()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedOrganizations = [];
        }
    }

    public function updatedSelectedOrganizations()
    {
        $this->showBulkActions = !empty($this->selectedOrganizations);
    }

    public function getFilteredOrganizations()
    {
        return Organization::query()
            ->with(['users' => function ($query) {
                $query->wherePivot('role', 'owner')->orWherePivot('is_primary_contact', true);
            }])
            ->withCount(['users', 'opportunities', 'placements'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('industry', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->industry, function ($query) {
                $query->where('industry', $this->industry);
            })
            ->when($this->status !== '', function ($query) {
                $query->where('is_active', $this->status === 'active');
            })
            ->when($this->verification !== '', function ($query) {
                $query->where('is_verified', $this->verification === 'verified');
            })
            ->when($this->county, function ($query) {
                $query->where('county', $this->county);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->latest();
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    public function deleteOrganization($id)
    {
        $org = Organization::withCount(['users', 'opportunities', 'placements'])->find($id);

        if ($org) {
            // Check if organization has dependencies
            if ($org->users_count > 0 || $org->opportunities_count > 0 || $org->placements_count > 0) {
                $this->dispatch('toastr:error', message: 'Cannot delete organization with active users, opportunities, or placements.');
                return;
            }

            $org->delete(); // Soft delete
            $this->dispatch('toastr:success', message: 'Organization deleted successfully.');
        }

        $this->selectedOrganizations = array_diff($this->selectedOrganizations, [$id]);
    }

    public function confirmBulkDelete()
    {
        if (empty($this->selectedOrganizations)) {
            $this->dispatch('toastr:warning', message: 'Please select organizations to delete.');
            return;
        }

        $this->dispatch('show-bulk-delete-confirmation', count: count($this->selectedOrganizations));
    }

    public function bulkDelete()
    {
        $organizations = Organization::whereIn('id', $this->selectedOrganizations)
            ->withCount(['users', 'opportunities', 'placements'])
            ->get();

        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($organizations as $org) {
            if ($org->users_count == 0 && $org->opportunities_count == 0 && $org->placements_count == 0) {
                $org->delete();
                $deletedCount++;
            } else {
                $skippedCount++;
            }
        }

        $this->selectedOrganizations = [];
        $this->selectAll = false;
        $this->showBulkActions = false;

        $message = "{$deletedCount} organizations deleted successfully.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} organizations skipped (have dependencies).";
        }

        $this->dispatch('toastr:success', message: $message);
    }

    public function toggleStatus($id)
    {
        $org = Organization::find($id);
        if ($org) {
            $org->update(['is_active' => !$org->is_active]);
            $this->dispatch('toastr:success', message: 'Organization status updated.');
        }
    }

    public function toggleVerification($id)
    {
        $org = Organization::find($id);
        if ($org) {
            $org->update([
                'is_verified' => !$org->is_verified,
                'verified_at' => !$org->is_verified ? now() : null
            ]);
            $this->dispatch('toastr:success', message: 'Organization verification status updated.');
        }
    }

    public function export()
    {
        // return Excel::download(new OrganizationsExport($this->getFilteredOrganizations()->get()), 'organizations.xlsx');
        return '';
    }

    public function resetFilters()
    {
        $this->reset(['search', 'industry', 'status', 'verification', 'county', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function render()
    {
        $organizations = $this->getFilteredOrganizations()->paginate($this->perPage);

        $stats = [
            'total' => Organization::count(),
            'active' => Organization::where('is_active', true)->count(),
            'verified' => Organization::where('is_verified', true)->count(),
            'pending' => Organization::where('is_verified', false)->count(),
        ];

        $industries = Organization::distinct('industry')->pluck('industry')->filter()->values();
        $counties = Organization::distinct('county')->pluck('county')->filter()->values();

        return view('livewire.admin.organizations.index', [
            'organizations' => $organizations,
            'stats' => $stats,
            'industries' => $industries,
            'counties' => $counties,
        ]);
    }
}
