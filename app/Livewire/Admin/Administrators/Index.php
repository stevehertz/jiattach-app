<?php

namespace App\Livewire\Admin\Administrators;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $roleFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;

    public $selectedAdministrators = [];
    public $selectAll = false;

    // For bulk actions
    public $bulkAction = '';

    // Statistics
    public $totalAdministrators;
    public $activeAdministrators;
    public $inactiveAdministrators;
    public $superAdmins;

    // For role management modal
    public $showRoleModal = false;
    public $selectedUserId;
    public $selectedUserRoles = [];
    public $availableRoles = [];

    // Delete confirmation modal
    public $showDeleteModal = false;
    public $userToDeleteId = null;
    public $userToDelete = null;

    protected $listeners = [
        'confirmDelete' => 'confirmDelete',
    ];

    public function mount()
    {
        $this->loadStatistics();
        $this->loadAvailableRoles();
    }

    public function loadStatistics()
    {
        $this->totalAdministrators = User::role(['admin', 'super-admin'])->count();
        $this->activeAdministrators = User::role(['admin', 'super-admin'])->where('is_active', true)->count();
        $this->inactiveAdministrators = User::role(['admin', 'super-admin'])->where('is_active', false)->count();
        $this->superAdmins = User::role('super-admin')->count();
    }

    public function loadAvailableRoles()
    {
        $this->availableRoles = Role::whereIn('name', ['admin', 'super-admin', 'moderator'])
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($role) {
                return [$role->name => ucfirst(str_replace('_', ' ', $role->name))];
            })
            ->toArray();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedAdministrators = $this->administrators->pluck('id')->toArray();
        } else {
            $this->selectedAdministrators = [];
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

     public function toggleStatus($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => !$user->is_active]);
        
        $this->loadStatistics();
        $this->dispatch('notify', [
            'type' => 'success', 
            'message' => 'Administrator status updated'
        ]);
    }
    
    public function openRoleModal($userId)
    {
        $this->selectedUserId = $userId;
        $user = User::findOrFail($userId);
        $this->selectedUserRoles = $user->getRoleNames()->toArray();
        $this->showRoleModal = true;
    }

    // --- Delete modal methods ---
    public function openDeleteModal($userId)
    {
        $this->userToDeleteId = $userId;
        $this->userToDelete = User::find($userId);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->userToDeleteId = null;
        $this->userToDelete = null;
    }

    public function confirmDelete()
    {
        if (! $this->userToDeleteId) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'No administrator selected.']);
            return;
        }

        // Prevent deleting the last admin
        $adminCount = User::role(['super-admin', 'admin'])->count();
        if ($adminCount <= 1 && User::find($this->userToDeleteId)->hasAnyRole(['super-admin', 'admin'])) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Cannot delete the last administrator.']);
            $this->closeDeleteModal();
            return;
        }

        try {
            User::findOrFail($this->userToDeleteId)->delete();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Administrator deleted successfully']);
            $this->closeDeleteModal();
            $this->loadStatistics();
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Failed to delete administrator: ' . $e->getMessage()]);
        }
    }
    
    public function closeRoleModal()
    {
        $this->showRoleModal = false;
        $this->selectedUserId = null;
        $this->selectedUserRoles = [];
    }
    
    public function saveRoles()
    {
        $this->validate([
            'selectedUserRoles' => 'required|array|min:1',
            'selectedUserRoles.*' => Rule::in(array_keys($this->availableRoles)),
        ]);
        
        $user = User::findOrFail($this->selectedUserId);
        
        // Ensure at least one admin role remains
        $adminUsers = User::role(['admin', 'super-admin'])->count();
        $userHasAdminRole = $user->hasAnyRole(['admin', 'super-admin']);
        $newRolesIncludeAdmin = count(array_intersect($this->selectedUserRoles, ['admin', 'super-admin'])) > 0;
        
        if ($userHasAdminRole && !$newRolesIncludeAdmin && $adminUsers <= 1) {
            $this->dispatch('notify', [
                'type' => 'error', 
                'message' => 'Cannot remove the last administrator from the system.'
            ]);
            return;
        }
        
        $user->syncRoles($this->selectedUserRoles);
        
        $this->closeRoleModal();
        $this->loadStatistics();
        
        $this->dispatch('notify', [
            'type' => 'success', 
            'message' => 'Roles updated successfully'
        ]);
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedAdministrators)) {
            $this->dispatch('notify', [
                'type' => 'warning', 
                'message' => 'Please select administrators to perform this action.'
            ]);
            return;
        }
        
        switch ($this->bulkAction) {
            case 'activate':
                User::whereIn('id', $this->selectedAdministrators)
                    ->update(['is_active' => true]);
                $message = 'Selected administrators activated';
                break;
                
            case 'deactivate':
                // Check if we're trying to deactivate all admins
                $adminUsers = User::role(['admin', 'super-admin'])->whereIn('id', $this->selectedAdministrators)->count();
                $totalAdmins = User::role(['admin', 'super-admin'])->count();
                
                if ($adminUsers >= $totalAdmins) {
                    $this->dispatch('notify', [
                        'type' => 'error', 
                        'message' => 'Cannot deactivate all administrators.'
                    ]);
                    return;
                }
                
                User::whereIn('id', $this->selectedAdministrators)
                    ->update(['is_active' => false]);
                $message = 'Selected administrators deactivated';
                break;
                
            case 'delete':
                // Check if we're trying to delete all admins
                $adminUsers = User::role(['admin', 'super-admin'])->whereIn('id', $this->selectedAdministrators)->count();
                $totalAdmins = User::role(['admin', 'super-admin'])->count();
                
                if ($adminUsers >= $totalAdmins) {
                    $this->dispatch('notify', [
                        'type' => 'error', 
                        'message' => 'Cannot delete all administrators.'
                    ]);
                    return;
                }
                
                User::whereIn('id', $this->selectedAdministrators)->delete();
                $message = 'Selected administrators deleted';
                break;
                
            default:
                $this->dispatch('notify', [
                    'type' => 'warning', 
                    'message' => 'Please select a valid action.'
                ]);
                return;
        }
        
        $this->selectedAdministrators = [];
        $this->selectAll = false;
        $this->bulkAction = '';
        $this->loadStatistics();
        
        $this->dispatch('notify', [
            'type' => 'success', 
            'message' => $message
        ]);
    }
    
    public function getAdministratorsProperty()
    {
        return User::query()
            ->with('roles')
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'super-admin', 'moderator']);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->when($this->roleFilter, function ($query) {
                $query->whereHas('roles', function($q) {
                    $q->where('name', $this->roleFilter);
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }
    
    public function getRolesProperty()
    {
        return Role::whereIn('name', ['admin', 'super-admin', 'moderator'])
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.administrators.index', [
            'administrators' => $this->administrators,
            'roles' => $this->roles,
            'totalAdministrators' => $this->totalAdministrators,
            'activeAdministrators' => $this->activeAdministrators,
            'inactiveAdministrators' => $this->inactiveAdministrators,
            'superAdmins' => $this->superAdmins,
        ]);
    }
}
