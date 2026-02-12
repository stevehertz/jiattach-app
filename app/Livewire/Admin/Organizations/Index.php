<?php

namespace App\Livewire\Admin\Organizations;

use App\Models\Organization;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    protected $listeners = ['deleteConfirmed' => 'deleteOrganization'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        // Dispatch event to frontend to show SweetAlert
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    public function deleteOrganization($id)
    {
        $org = Organization::find($id);
        if ($org) {
            $org->delete(); // Soft delete
            $this->dispatch('toastr:success', message: 'Organization deleted successfully.');
        }
    }


    public function render()
    {
        $organizations = Organization::query()
            ->with('user') // Eager load owner
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.organizations.index', [
            'organizations' => $organizations
        ]);
    }
}
