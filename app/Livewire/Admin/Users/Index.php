<?php

namespace App\Livewire\Admin\Users;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 20;
    public $selectedUsers = [];
    public $selectAll = false;
    public $showFilters = false;
    public $showBulkActions = false;

    // Add these properties at the top with other properties
    public $showDeleteModal = false;
    public $userToDelete = null;
    public $deleteConfirmation = '';
    public $deleteType = 'single'; // 'single' or 'bulk'

    protected $listeners = ['refreshUsers' => '$refresh'];

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedUsers = $this->getUsersQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function updatedSelectedUsers()
    {
        $this->selectAll = false;
        $this->showBulkActions = count($this->selectedUsers) > 0;
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

    public function getUsersQuery()
    {
        return User::query()
            ->with(['roles'])
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhereHas('studentProfile', function ($q) use ($search) {
                            $q->where('student_reg_number', 'like', '%' . $search . '%')
                                ->orWhere('institution_name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('mentor', function ($q) use ($search) {
                            $q->where('company', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('employer', function ($q) use ($search) {
                            $q->where('company_name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($this->roleFilter, function ($query, $role) {
                $query->role($role);
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($this->statusFilter === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->when($this->statusFilter === 'verified', function ($query) {
                $query->where('is_verified', true);
            })
            ->when($this->statusFilter === 'unverified', function ($query) {
                $query->where('is_verified', false);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function getUsersProperty()
    {
        return $this->getUsersQuery()->paginate($this->perPage);
    }

    public function getRolesProperty()
    {
        return Role::all()->pluck('name', 'name');
    }

    public function getStatsProperty()
    {
        return [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'verified' => User::where('is_verified', true)->count(),
            'students' => User::role('student')->count(),
            'employers' => User::role('employer')->count(),
            'mentors' => User::role('mentor')->count(),
            'admins' => User::role('admin')->count(),
            'today' => User::whereDate('created_at', today())->count(),
        ];
    }

    public function getRoleDistributionProperty()
    {
        return [
            'students' => User::role('student')->count(),
            'employers' => User::role('employer')->count(),
            'mentors' => User::role('mentor')->count(),
            'admins' => User::role('admin')->count(),
        ];
    }

    public function getMonthlyRegistrationsProperty()
    {
        // Get registrations for the last 12 months
        $months = [];
        $counts = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');

            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $months[] = $monthName;
            $counts[] = $count;
        }

        return [
            'months' => $months,
            'counts' => $counts,
        ];
    }

    public function activateUser($userId)
    {
        $user = User::findOrFail($userId);

        // Prevent activating/deactivating super-admin
        if ($this->isSuperAdmin($user)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot modify super-admin user!'
            ]);
            return;
        }

        // Prevent modifying yourself
        if ($user->id === Auth::id()) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'You cannot modify your own account from here!'
            ]);
            return;
        }

        $user->update(['is_active' => true]);

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'User activated successfully!'
        ]);
    }

    public function deactivateUser($userId)
    {
        $user = User::findOrFail($userId);

        // Prevent activating/deactivating super-admin
        if ($this->isSuperAdmin($user)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot modify super-admin user!'
            ]);
            return;
        }

        // Prevent modifying yourself
        if ($user->id === Auth::id()) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'You cannot modify your own account from here!'
            ]);
            return;
        }

        $user->update(['is_active' => false]);

        $this->dispatch('show-toast', [
            'type' => 'warning',
            'message' => 'User deactivated successfully!'
        ]);
    }

    public function verifyUser($userId)
    {
        $user = User::findOrFail($userId);

        // Prevent modifying super-admin
        if ($this->isSuperAdmin($user)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot modify super-admin user!'
            ]);
            return;
        }

        $user->update(['is_verified' => true]);

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'User verified successfully!'
        ]);
    }

    public function assignRole($userId, $role)
    {
        $user = User::findOrFail($userId);

        // Prevent modifying super-admin
        if ($this->isSuperAdmin($user)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot modify super-admin user!'
            ]);
            return;
        }

        // Prevent modifying yourself
        if ($user->id === Auth::id()) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'You cannot modify your own account!'
            ]);
            return;
        }


        $user->syncRoles([$role]);

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Role assigned successfully!'
        ]);
    }

    // Add this helper method
    private function isSuperAdmin(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    // Add this method to check if user can be modified
    public function canModifyUser($userId): bool
    {
        $user = User::find($userId);
        if (!$user) return false;

        // Cannot modify super-admin or yourself
        return !$this->isSuperAdmin($user) && $user->id !== Auth::id();
    }

    public function viewUser($userId)
    {
        // If viewing own profile, redirect to profile page
        if ($userId == Auth::id()) {
            return redirect()->route('admin.profile'); // You need to create this route
        }

        return redirect()->route('admin.users.show', $userId);
    }

    public function bulkActivate()
    {
        // Filter out super-admins and current user
        $filteredUsers = User::whereIn('id', $this->selectedUsers)
            ->whereNot('id', Auth::id())
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'super-admin');
            })
            ->pluck('id')
            ->toArray();
        if (count($filteredUsers) > 0) {
            User::whereIn('id', $filteredUsers)->update(['is_active' => true]);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Selected users activated successfully!'
            ]);
        } else {
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => 'No eligible users to activate!'
            ]);
        }

        $this->selectedUsers = [];
        $this->showBulkActions = false;
    }

    public function bulkDeactivate()
    {
        // Filter out super-admins and current user
        $filteredUsers = User::whereIn('id', $this->selectedUsers)
            ->whereNot('id', Auth::id())
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'super-admin');
            })
            ->pluck('id')
            ->toArray();

        if (count($filteredUsers) > 0) {
            User::whereIn('id', $filteredUsers)->update(['is_active' => false]);

            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => 'Selected users deactivated successfully!'
            ]);
        } else {
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => 'No eligible users to deactivate!'
            ]);
        }

        $this->selectedUsers = [];
        $this->showBulkActions = false;
    }



    public function bulkDelete()
    {

        // Filter out super-admins and current user
        $filteredUsers = User::whereIn('id', $this->selectedUsers)
            ->whereNot('id', Auth::id())
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'super-admin');
            })
            ->pluck('id')
            ->toArray();

        if (count($filteredUsers) > 0) {

            // Prevent deleting yourself
            // $this->selectedUsers = array_diff($this->selectedUsers, [Auth::id()]);
            User::whereIn('id', $filteredUsers)->delete();
            $this->selectedUsers = [];
            $this->showBulkActions = false;

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Selected users deleted successfully!'
            ]);
        } else {
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => 'No eligible users to delete!'
            ]);
            return;
        }
    }

    public function exportUsers($format = 'csv')
    {
        // This would be implemented for actual export
        $this->dispatch('show-toast', [
            'type' => 'info',
            'message' => 'Export feature coming soon!'
        ]);
    }

    // Add these methods
    public function confirmDelete($userId = null)
    {
        if ($userId) {
            // Single user deletion
            $this->userToDelete = User::find($userId);
            $this->deleteType = 'single';
        } else {
            // Bulk deletion - prevent deleting yourself
            $this->selectedUsers = array_diff($this->selectedUsers, [Auth::id()]);
            $this->deleteType = 'bulk';
        }

        $this->showDeleteModal = true;
    }

    // In your Livewire component
    public function getCanDeleteProperty()
    {
        $confirmation = trim($this->deleteConfirmation);

        if ($this->deleteType === 'single') {
            return strtoupper($confirmation) === 'DELETE';
        } elseif ($this->deleteType === 'bulk') {
            $expected = 'DELETE ' . count($this->selectedUsers);
            return strtoupper($confirmation) === strtoupper($expected);
        }

        return false;
    }

    // In your Livewire component (Index.php)
    public function deleteUser()
    {
        $confirmation = trim($this->deleteConfirmation);

        if ($this->deleteType === 'single' && $this->userToDelete) {
            // Final validation
            if (strtoupper($confirmation) !== 'DELETE') {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => 'Please type "DELETE" to confirm deletion.'
                ]);
                return;
            }

            // Check if user is trying to delete themselves
            if ($this->userToDelete->id === Auth::id()) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => 'You cannot delete your own account!'
                ]);
                $this->resetDeleteModal();
                return;
            }

            // Delete the user
            $userName = $this->userToDelete->full_name;
            $this->userToDelete->delete();

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => "User '{$userName}' deleted successfully!"
            ]);
        } elseif ($this->deleteType === 'bulk' && !empty($this->selectedUsers)) {
            $expected = 'DELETE ' . count($this->selectedUsers);

            // Final validation
            if (strtoupper($confirmation) !== strtoupper($expected)) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => 'Please type "' . $expected . '" to confirm deletion.'
                ]);
                return;
            }

            // Bulk delete logic...
        }

        $this->resetDeleteModal();
        $this->dispatch('refreshUsers');
    }

    public function resetDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->userToDelete = null;
        $this->deleteConfirmation = '';
    }

    /**
     * Check if user has related records that should prevent deletion.
     */
    private function hasRelatedRecords(User $user): bool
    {
        // Check employer opportunities
        if ($user->employer && $user->employer->opportunities()->count() > 0) {
            return true;
        }

        // Check applications
        if ($user->applications()->count() > 0) {
            return true;
        }

        // Check mentor relationships
        if ($user->mentor && $user->mentor->mentorships()->count() > 0) {
            return true;
        }

        // Check as mentee
        if ($user->mentorshipsAsMentee()->count() > 0) {
            return true;
        }

        return false;
    }

    public function render()
    {
        return view('livewire.admin.users.index', [
            'users' => $this->users,
            'roles' => $this->roles,
            'stats' => $this->stats,
            'roleDistribution' => $this->roleDistribution,
            'monthlyRegistrations' => $this->monthlyRegistrations,
        ]);
    }
}
