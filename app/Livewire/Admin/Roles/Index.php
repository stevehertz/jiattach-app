<?php

namespace App\Livewire\Admin\Roles;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $perPage = 15;

    public $selectedRoles = [];
    public $selectAll = false;
    public $bulkAction = '';

    // Modal states
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showPermissionsModal = false;
    public $showDuplicateModal = false;

    // Form fields (using only Spatie columns)
    public $roleId;
    public $name = '';
    public $guardName = 'web';
    public $isProtected = false;

    // Permissions
    public $selectedPermissions = [];
    public $permissionGroups = [];
    public $availablePermissions = [];

    // Duplication
    public $duplicateName = '';
    public $includePermissions = true;

    // Statistics
    public $totalRoles;
    public $protectedRoles;
    public $userAssignedRoles;
    public $permissionCount;

    protected $listeners = [
        'refreshRoles' => '$refresh',
        'roleCreated' => 'handleRoleCreated',
        'roleUpdated' => 'handleRoleUpdated',
        'roleDeleted' => 'handleRoleDeleted',
        'openCreateModal' => 'openCreateModal',
        'openEditModal' => 'openEditModal',
        'openPermissionsModal' => 'openPermissionsModal',
        'openDuplicateModal' => 'openDuplicateModal',
        'openDeleteModal' => 'openDeleteModal',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 15],
    ];
    public function mount()
    {
        $this->loadStatistics();
        $this->loadAvailablePermissions();
        $this->groupPermissions();
    }

    public function loadStatistics()
    {
        $this->totalRoles = Role::count();
        $this->protectedRoles = Role::where('name', 'like', 'system-%')
            ->orWhere('name', 'super-admin')
            ->orWhere('name', 'admin')
            ->orWhere('name', 'moderator')
            ->count();

        // Count roles assigned to users
        $this->userAssignedRoles = DB::table('model_has_roles')
            ->distinct('role_id')
            ->count('role_id');

        $this->permissionCount = Permission::count();
    }

     public function loadAvailablePermissions()
    {
        $this->availablePermissions = Permission::orderBy('name')->get();
    }

    // Add this method to handle "Select All" for specific groups
    public function selectAllInGroup($group)
    {
        if (isset($this->permissionGroups[$group])) {
            $groupPermissionIds = collect($this->permissionGroups[$group])->pluck('id')->toArray();

            // Merge existing selections
            $this->selectedPermissions = array_unique(array_merge(
                $this->selectedPermissions,
                $groupPermissionIds
            ));
        }
    }

    // Add this method to deselect all in group
    public function deselectAllInGroup($group)
    {
        if (isset($this->permissionGroups[$group])) {
            $groupPermissionIds = collect($this->permissionGroups[$group])->pluck('id')->toArray();

            // Remove group permissions from selection
            $this->selectedPermissions = array_diff($this->selectedPermissions, $groupPermissionIds);
        }
    }

    public function groupPermissions()
    {
        $groups = [
            'user' => [],
            'student' => [],
            'employer' => [],
            'mentor' => [],
            'opportunity' => [],
            'application' => [],
            'mentorship' => [],
            'exchange' => [],
            'system' => [],
            'content' => [],
            'report' => [],
            'settings' => [],
            'other' => [],
        ];

        foreach ($this->availablePermissions as $permission) {
            $parts = explode('-', $permission->name);
            $group = count($parts) > 1 ? $parts[0] : 'system';

            // Clean up group name
            $group = str_replace(['-', '_'], ' ', $group);
            $group = trim($group);

            // Map common prefixes
            $groupMap = [
                'view' => 'user',
                'create' => 'user',
                'edit' => 'user',
                'delete' => 'user',
                'manage' => 'system',
                'verify' => 'user',
                'post' => 'opportunity',
                'apply' => 'application',
                'offer' => 'mentorship',
                'join' => 'exchange',
            ];

            if (isset($groupMap[$group])) {
                $group = $groupMap[$group];
            }

            if (!isset($groups[$group])) {
                $group = 'other';
            }

            $groups[$group][] = $permission;
        }

        // Remove empty groups
        $this->permissionGroups = array_filter($groups, function ($permissions) {
            return count($permissions) > 0;
        });
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Only select deletable roles
            $this->selectedRoles = $this->roles->filter(function ($role) {
                return $this->canDeleteRole($role->name);
            })->pluck('id')->toArray();
        } else {
            $this->selectedRoles = [];
        }
    }

    // Add this method to update bulk actions
    public function updatedSelectedRoles()
    {
        $this->selectAll = count($this->selectedRoles) === $this->roles->count();
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

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
        // Dispatch event to show modal
        $this->dispatch('showCreateModal');
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
        $this->dispatch('closeModal', ['modal' => 'createRoleModal']);
    }

    public function openEditModal($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);

        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->guardName = $role->guard_name;

        // Load from the database column instead of just the name-check logic
        $this->isProtected = (bool) $role->is_protected;


        // Ensure they are integers
        $this->selectedPermissions = $role->permissions->pluck('id')->map(fn($id) => (int)$id)->toArray();

        $this->showEditModal = true;
        $this->dispatch('showEditModal');
    }

    public function openPermissionsModal($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);

        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->isProtected = $this->isRoleProtected($role->name);

        // Ensure they are integers
        $this->selectedPermissions = $role->permissions->pluck('id')->map(fn($id) => (int)$id)->toArray();

        $this->showPermissionsModal = true;
        $this->dispatch('showPermissionsModal');
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
        $this->dispatch('closeModal', ['modal' => 'editRoleModal']);
    }

    public function openDuplicateModal($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);

        $this->roleId = $role->id;
        $this->name = $role->name;

        // Set duplicate name
        $this->duplicateName = $role->name . '-copy';
        $this->includePermissions = true;
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();

        $this->showDuplicateModal = true;
        // Dispatch event to show modal
        $this->dispatch('showDuplicateModal');
    }

     public function closeDuplicateModal()
    {
        $this->showDuplicateModal = false;
        $this->resetForm();
        $this->dispatch('closeModal', ['modal' => 'duplicateRoleModal']);
    }

    public function closePermissionsModal()
    {
        $this->showPermissionsModal = false;
        $this->resetForm();
        // $this->dispatch('closeModal', ['modal' => 'permissionsModal']);
    }

    public function openDeleteModal($roleId = null)
    {
        if ($roleId) {
            $this->selectedRoles = [$roleId];
        }

        $this->showDeleteModal = true;
        // Dispatch event to show modal
        $this->dispatch('showDeleteModal');
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedRoles = [];
        $this->dispatch('closeModal', ['modal' => 'deleteRoleModal']);
    }

    public function resetForm($resetPermissions = true)
    {
        $this->roleId = null;
        $this->name = '';
        $this->guardName = 'web';
        $this->isProtected = false;
        $this->duplicateName = '';
        $this->includePermissions = true;

        // Only reset permissions if specified
        if ($resetPermissions) {
            $this->selectedPermissions = [];
        }
    }

    public function createRole()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name', 'regex:/^[a-z0-9\-]+$/'],
            'guardName' => ['required', 'string', 'in:web,api'],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['exists:permissions,id'],
        ], [
            'name.unique' => 'A role with this name already exists.',
            'name.regex' => 'Role name can only contain lowercase letters, numbers, and hyphens.',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $this->name,
                'guard_name' => $this->guardName,
            ]);

            // Assign permissions if selected
            if (!empty($this->selectedPermissions)) {
                $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();
                $role->syncPermissions($permissions);
            }

            DB::commit();

            $this->closeCreateModal();
            $this->loadStatistics();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Role created successfully!'
            ]);

            $this->dispatch('roleCreated');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to create role: ' . $e->getMessage()
            ]);
        }
    }

    public function updateRole()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($this->roleId), 'regex:/^[a-z0-9\-]+$/'],
            'guardName' => ['required', 'string', 'in:web,api'],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::findOrFail($this->roleId);

        // Check if role is protected
        if ($this->isRoleProtected($role->name) && !Auth::user()->hasRole('super-admin')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Protected roles can only be modified by super administrators.'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            $role->update([
                'name' => $this->name,
                'guard_name' => $this->guardName,
                'is_protected' => $this->isProtected,
            ]);

            // Update permissions
            $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();
            $role->syncPermissions($permissions);

            DB::commit();

            $this->closeEditModal();
            $this->loadStatistics();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Role updated successfully!'
            ]);

            $this->dispatch('roleUpdated');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update role: ' . $e->getMessage()
            ]);
        }
    }

    public function duplicateRole()
    {
        $this->validate([
            'duplicateName' => ['required', 'string', 'max:255', 'unique:roles,name', 'regex:/^[a-z0-9\-]+$/'],
        ]);

        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $this->duplicateName,
                'guard_name' => 'web',
            ]);

            // Copy permissions if selected
            if ($this->includePermissions && !empty($this->selectedPermissions)) {
                $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();
                $role->syncPermissions($permissions);
            }

            DB::commit();

            $this->closeDuplicateModal();
            $this->loadStatistics();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Role duplicated successfully!'
            ]);

            $this->dispatch('roleCreated');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to duplicate role: ' . $e->getMessage()
            ]);
        }
    }

    public function updatePermissions()
    {
        $this->validate([
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::findOrFail($this->roleId);

        // Check if role is protected
        if ($this->isRoleProtected($role->name) && !Auth::user()->hasRole('super-admin')) {
            $this->dispatch('notify', [
                'type'  => 'error',
                'message' => 'Protected roles can only be modified by super administrators.'
            ]);
            return;
        }

        try {
            // 1. Standardize IDs to integers (Livewire often sends them as strings)
            $permissionIds = collect($this->selectedPermissions)
                ->map(fn($id) => (int)$id)
                ->toArray();

            // 2. Sync with IDs directly (more performant and reliable than passing the collection)
            $role->syncPermissions($permissionIds);

            // 3. CRITICAL: Clear the Spatie Permission Cache
            // Without this, $user->can() will still return false until the cache expires
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // Send success notification
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Role permissions updated successfully!'
            ]);

            $this->dispatch('roleUpdated');
            $this->dispatch('closeModal', ['modal' => 'permissionsModal']);

            $this->showPermissionsModal = false;
            $this->resetForm(); // Important to clear state

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update permissions: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteRoles()
    {
        if (empty($this->selectedRoles)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Please select roles to delete.'
            ]);
            return;
        }

        $roles = Role::whereIn('id', $this->selectedRoles)->get();

        // Check for protected roles
        $protectedRoles = [];
        foreach ($roles as $role) {
            if ($this->isRoleProtected($role->name)) {
                $protectedRoles[] = $role->name;
            }
        }

        if (!empty($protectedRoles) && !Auth::user()->hasRole('super-admin')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot delete protected roles: ' . implode(', ', $protectedRoles)
            ]);
            return;
        }

        // Check if roles have users assigned
        $assignedRoles = [];
        foreach ($roles as $role) {
            $userCount = DB::table('model_has_roles')->where('role_id', $role->id)->count();
            if ($userCount > 0) {
                $assignedRoles[] = [
                    'name' => $role->name,
                    'count' => $userCount
                ];
            }
        }

        if (!empty($assignedRoles)) {
            $message = 'The following roles have users assigned and cannot be deleted:<br>';
            foreach ($assignedRoles as $assignedRole) {
                $message .= "- {$assignedRole['name']} ({$assignedRole['count']} users)<br>";
            }

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => $message
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            // Delete role permissions first
            DB::table('role_has_permissions')->whereIn('role_id', $this->selectedRoles)->delete();

            // Delete the roles
            Role::whereIn('id', $this->selectedRoles)->delete();

            DB::commit();

            $this->closeDeleteModal();
            $this->selectedRoles = [];
            $this->selectAll = false;
            $this->loadStatistics();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Roles deleted successfully!'
            ]);

            $this->dispatch('roleDeleted');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to delete roles: ' . $e->getMessage()
            ]);
        }
    }

    // Update the executeBulkAction method to show appropriate actions
    public function executeBulkAction()
    {
        if (empty($this->selectedRoles)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Please select roles to perform this action.'
            ]);
            return;
        }

        // Check which actions are available based on selected roles
        $roles = Role::whereIn('id', $this->selectedRoles)->get();

        $hasProtectedRoles = $roles->contains(function ($role) {
            return !$this->canDeleteRole($role->name);
        });

        $hasAssignedRoles = false;
        foreach ($roles as $role) {
            if ($this->getRoleUsersCount($role->id) > 0) {
                $hasAssignedRoles = true;
                break;
            }
        }

        switch ($this->bulkAction) {
            case 'delete':
                if ($hasProtectedRoles) {
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'message' => 'Cannot delete protected roles. Please unselect protected roles and try again.'
                    ]);
                    return;
                }

                if ($hasAssignedRoles) {
                    $this->dispatch('notify', [
                        'type' => 'warning',
                        'message' => 'Some selected roles have users assigned. Please confirm deletion.'
                    ]);
                }

                $this->openDeleteModal();
                break;

            default:
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => 'Please select a valid action.'
                ]);
        }
    }

    public function isRoleProtected($role)
    {
        // If it's an object from DB
        if (isset($role->is_protected) && $role->is_protected) {
            return true;
        }

        // Hardcoded safety names
        $systemNames = ['super-admin', 'admin', 'moderator', 'student'];
        $name = is_string($role) ? $role : $role->name;

        return in_array($name, $systemNames) || Str::startsWith($name, 'system-');
    }

    // Add a method to check if role can be edited
    public function canEditRole($roleName)
    {
        $protectedEditable = ['student', 'employer', 'mentor', 'entrepreneur'];

        if (in_array($roleName, $protectedEditable)) {
            // These roles can have their permissions edited but not deleted
            return Auth::user()->hasAnyRole(['super-admin', 'admin']);
        }

        if ($roleName === 'super-admin') {
            return Auth::user()->hasRole('super-admin');
        }

        if (in_array($roleName, ['admin', 'moderator'])) {
            return Auth::user()->hasAnyRole(['super-admin', 'admin']);
        }

        return true;
    }

    // Add a method to check if role can be deleted
    public function canDeleteRole($roleName)
    {
        $undeletableRoles = [
            'super-admin',
            'admin',
            'moderator',
            'student',
            'employer',
            'mentor',
            'entrepreneur',
        ];

        if (in_array($roleName, $undeletableRoles)) {
            return false;
        }

        return !Str::startsWith($roleName, 'system-');
    }

    public function getRoleUsersCount($roleId)
    {
        return DB::table('model_has_roles')->where('role_id', $roleId)->count();
    }

    public function getRolePermissionsCount($roleId)
    {
        return DB::table('role_has_permissions')->where('role_id', $roleId)->count();
    }

    public function getRolesProperty()
    {
        return Role::query()
            ->withCount(['permissions'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('guard_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    // Add this method to handle removing a single permission
    public function removeSelectedPermission($permissionId)
    {
        $this->selectedPermissions = array_diff($this->selectedPermissions, [$permissionId]);
    }

    // Add event handlers
    public function handleRoleCreated()
    {
        $this->loadStatistics();
        $this->resetPage();
    }

    public function handleRoleUpdated()
    {
        $this->loadStatistics();
    }

    public function handleRoleDeleted()
    {
        $this->loadStatistics();
        $this->resetPage();
    }

    // Helper method to get display name (capitalize and add spaces)
    public function getDisplayName($roleName)
    {
        return ucwords(str_replace(['-', '_'], ' ', $roleName));
    }

    public function render()
    {
        return view('livewire.admin.roles.index', [
            'roles' => $this->roles,
            'totalRoles' => $this->totalRoles,
            'protectedRoles' => $this->protectedRoles,
            'userAssignedRoles' => $this->userAssignedRoles,
            'permissionCount' => $this->permissionCount,
            'permissionGroups' => $this->permissionGroups,
        ]);
    }
}
