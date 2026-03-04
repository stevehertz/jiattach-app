<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    <section class="content">
        <div class="container-fluid">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $totalRoles }}</h3>
                            <p>Total Roles</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $protectedRoles }}</h3>
                            <p>Protected Roles</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            System Roles <i class="fas fa-lock"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $userAssignedRoles }}</h3>
                            <p>Assigned Roles</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            In Use <i class="fas fa-check-circle"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $permissionCount }}</h3>
                            <p>Total Permissions</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Permissions <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.row -->

            <!-- Quick Actions -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0"><i class="fas fa-cogs"></i> Role Management</h5>
                                    <p class="text-muted mb-0">Create, edit, and manage system roles and permissions</p>
                                </div>
                                <div>
                                    <button class="btn btn-primary" wire:click="openCreateModal">
                                        <i class="fas fa-plus"></i> Create New Role
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="location.reload()">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->

            <div class="row">
                <div class="col-12">
                    <!-- Search and Filters -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Search & Filter Roles</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" wire:model.live.debounce.300ms="search"
                                        class="form-control float-right" placeholder="Search roles...">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Sort By</label>
                                        <select wire:model.live="sortBy" class="form-control">
                                            <option value="name">Name</option>
                                            <option value="created_at">Creation Date</option>
                                            <option value="users_count">Users Assigned</option>
                                            <option value="permissions_count">Permissions Count</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Sort Direction</label>
                                        <select wire:model.live="sortDirection" class="form-control">
                                            <option value="asc">Ascending</option>
                                            <option value="desc">Descending</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Results Per Page</label>
                                        <select wire:model.live="perPage" class="form-control">
                                            <option value="10">10 per page</option>
                                            <option value="15">15 per page</option>
                                            <option value="25">25 per page</option>
                                            <option value="50">50 per page</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    @if (count($selectedRoles) > 0)
                                        <div class="form-group">
                                            <label>Bulk Actions</label>
                                            <div class="input-group">
                                                <select wire:model="bulkAction" class="form-control">
                                                    <option value="">Select Action</option>
                                                    <option value="delete">Delete Selected</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-danger" type="button"
                                                        wire:click="executeBulkAction">
                                                        <i class="fas fa-play"></i> Apply
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                        <!--/.card-body -->
                    </div>

                    <!-- Roles Table -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">System Roles</h3>
                            <div class="card-tools">
                                <span class="badge badge-info">{{ $roles->total() }} Roles</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <div class="icheck-primary">
                                                    <input type="checkbox" wire:model="selectAll" id="selectAll">
                                                    <label for="selectAll"></label>
                                                </div>
                                            </th>
                                            <th wire:click="sortBy('name')" style="cursor: pointer;">
                                                Role
                                                @if ($sortBy === 'name')
                                                    <i
                                                        class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                                @else
                                                    <i class="fas fa-sort"></i>
                                                @endif
                                            </th>
                                            <th wire:click="sortBy('users_count')" style="cursor: pointer;">
                                                Users
                                                @if ($sortBy === 'users_count')
                                                    <i
                                                        class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                                @else
                                                    <i class="fas fa-sort"></i>
                                                @endif
                                            </th>
                                            <th wire:click="sortBy('permissions_count')" style="cursor: pointer;">
                                                Permissions
                                                @if ($sortBy === 'permissions_count')
                                                    <i
                                                        class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                                @else
                                                    <i class="fas fa-sort"></i>
                                                @endif
                                            </th>

                                            <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                                Created
                                                @if ($sortBy === 'created_at')
                                                    <i
                                                        class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                                @else
                                                    <i class="fas fa-sort"></i>
                                                @endif
                                            </th>

                                            <th>Status</th>

                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($roles as $role)
                                            @php
                                                $isProtected = $this->isRoleProtected($role->name);
                                                $usersCount = $role->users_count;
                                                $permissionsCount = $role->permissions_count;
                                            @endphp
                                            <tr>
                                                <td>
                                                    @if (!$isProtected || auth()->user()->hasRole('super-admin'))
                                                        <div class="icheck-primary">
                                                            <input type="checkbox" wire:model="selectedRoles"
                                                                value="{{ $role->id }}"
                                                                id="role_{{ $role->id }}">
                                                            <label for="role_{{ $role->id }}"></label>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="mr-3">
                                                            <div class="avatar-initials bg-{{ $isProtected ? 'warning' : 'primary' }} img-circle elevation-1"
                                                                style="width: 40px; height: 40px; line-height: 40px; text-align: center; color: white; font-weight: bold; font-size: 14px;">
                                                                {{ strtoupper(substr($role->name, 0, 2)) }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <strong>{{ ucwords(str_replace(['-', '_'], ' ', $role->name)) }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <code>{{ $role->name }}</code>
                                                                @if ($isProtected)
                                                                    <span class="badge badge-warning ml-1">
                                                                        <i class="fas fa-shield-alt"></i> Protected
                                                                    </span>
                                                                @endif
                                                                <br>
                                                                Guard: <code>{{ $role->guard_name }}</code>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <h4 class="mb-0">{{ $role->users->count() }}</h4>
                                                        <small class="text-muted">Users</small>
                                                        @if ($usersCount > 0)
                                                            <br>
                                                            <a href="#" class="text-primary small">
                                                                <i class="fas fa-eye"></i> View Users
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <h4 class="mb-0">{{ $permissionsCount }}</h4>
                                                        <small class="text-muted">Permissions</small>
                                                        @if ($permissionsCount > 0)
                                                            <br>
                                                            <button class="btn btn-sm btn-outline-info mt-1"
                                                                wire:click="openPermissionsModal({{ $role->id }})">
                                                                <i class="fas fa-key"></i> View
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ formatDate($role->created_at) }}
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $role->created_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @if ($isProtected)
                                                        <span class="badge badge-warning">
                                                            <i class="fas fa-shield-alt"></i> Protected
                                                        </span>
                                                    @else
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check-circle"></i> Custom
                                                        </span>
                                                    @endif
                                                    <br>
                                                    @if ($usersCount > 0)
                                                        <small class="badge badge-primary">In Use</small>
                                                    @else
                                                        <small class="badge badge-secondary">Unassigned</small>
                                                    @endif
                                                </td>
                                                <td>

                                                    <div class="btn-group">
                                                        <!-- View Permissions Button -->
                                                        <button class="btn btn-info btn-sm"
                                                            wire:click="openPermissionsModal({{ $role->id }})"
                                                            title="Manage Permissions">
                                                            <i class="fas fa-key"></i>
                                                        </button>

                                                        <!-- Edit Button -->
                                                        <button class="btn btn-primary btn-sm"
                                                            wire:click="openEditModal({{ $role->id }})"
                                                            title="Edit Role"
                                                            @if (!$this->canEditRole($role->name)) disabled @endif>
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <!-- Duplicate Button -->
                                                        <button class="btn btn-success btn-sm"
                                                            wire:click="openDuplicateModal({{ $role->id }})"
                                                            title="Duplicate Role">
                                                            <i class="fas fa-copy"></i>
                                                        </button>

                                                        <!-- Delete Button -->
                                                        <button class="btn btn-danger btn-sm"
                                                            wire:click="openDeleteModal({{ $role->id }})"
                                                            title="Delete Role"
                                                            @if (!$this->canDeleteRole($role->name) || $usersCount > 0) disabled @endif>
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>

                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-user-tag fa-3x mb-3"></i>
                                                        <h4>No Roles Found</h4>
                                                        <p>No roles have been created yet.</p>
                                                        <button class="btn btn-primary" wire:click="openCreateModal">
                                                            <i class="fas fa-plus"></i> Create Your First Role
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            <div class="float-left">
                                <span class="text-muted">
                                    Showing {{ $roles->firstItem() ?? 0 }} to {{ $roles->lastItem() ?? 0 }}
                                    of {{ $roles->total() }} roles
                                </span>
                            </div>
                            <div class="float-right">
                                {{ $roles->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->

            <!-- Create Role Modal -->
            <div class="modal fade" id="createRoleModal" tabindex="-1" wire:ignore.self>
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title">
                                <i class="fas fa-plus"></i> Create New Role
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form wire:submit.prevent="createRole">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="roleName">Role Name *</label>
                                            <input type="text" wire:model="name" class="form-control"
                                                id="roleName" placeholder="e.g., content-manager" required>
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <small class="text-muted">
                                                Use lowercase letters, numbers, and hyphens only (e.g., content-manager)
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="guardName">Guard Name</label>
                                            <select wire:model="guardName" class="form-control" id="guardName">
                                                <option value="web">Web (Default)</option>
                                                <option value="api">API</option>
                                            </select>
                                            @error('guardName')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Permissions Section -->
                                <div class="form-group">
                                    <label>Permissions</label>
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Select Permissions for this Role</span>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    wire:click="$set('selectedPermissions', {{ json_encode($availablePermissions->pluck('id')->toArray()) }})">
                                                    Select All
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($permissionGroups as $group => $permissions)
                                                    @if (count($permissions) > 0)
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card card-outline card-primary">
                                                                <div class="card-header">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center">
                                                                        <h3 class="card-title mb-0">
                                                                            {{ ucfirst($group) }} Permissions
                                                                            <span
                                                                                class="badge badge-light ml-2">{{ count($permissions) }}</span>
                                                                        </h3>
                                                                        <div class="btn-group btn-group-sm">
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-outline-success"
                                                                                wire:click="selectAllInGroup('{{ $group }}')"
                                                                                title="Select All">
                                                                                <i class="fas fa-check-square"></i>
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-outline-danger"
                                                                                wire:click="deselectAllInGroup('{{ $group }}')"
                                                                                title="Deselect All">
                                                                                <i class="fas fa-times-circle"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body"
                                                                    style="max-height: 200px; overflow-y: auto;">
                                                                    @foreach ($permissions as $permission)
                                                                        <div class="form-check">
                                                                            <input type="checkbox"
                                                                                wire:model="selectedPermissions"
                                                                                value="{{ $permission->id }}"
                                                                                class="form-check-input"
                                                                                id="perm_create_{{ $permission->id }}">
                                                                            <label class="form-check-label"
                                                                                for="perm_create_{{ $permission->id }}">
                                                                                {{ $permission->name }}
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @error('selectedPermissions')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Role
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Role Modal -->
            <div class="modal fade" id="editRoleModal" tabindex="-1" wire:ignore.self>
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h4 class="modal-title">
                                <i class="fas fa-edit"></i> Edit Role
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form wire:submit.prevent="updateRole">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="editRoleName">Role Name *</label>
                                            <input type="text" wire:model="name" class="form-control"
                                                id="editRoleName" value="{{ $name }}" required>
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <small class="text-muted">
                                                Display: {{ ucwords(str_replace(['-', '_'], ' ', $name)) }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="editGuardName">Guard Name</label>
                                            <select wire:model="guardName" class="form-control" id="editGuardName">
                                                <option value="web">Web (Default)</option>
                                                <option value="api">API</option>
                                            </select>
                                            @error('guardName')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Role Status</label>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input"
                                                    wire:model.live="isProtected" id="editIsProtected"
                                                    {{ !auth()->user()->hasRole('super-admin') ? 'disabled' : '' }}>
                                                <!-- Only disable for non-admins -->

                                                <label class="custom-control-label" for="editIsProtected">
                                                    @if ($isProtected)
                                                        <span class="text-warning">Protected Role</span>
                                                    @else
                                                        <span class="text-success">Custom Role</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <small class="text-muted">
                                                @if ($isProtected)
                                                    Protected roles can only be modified by super administrators
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permissions Section -->
                                <div class="form-group">
                                    <label>Permissions</label>
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Select Permissions for this Role</span>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    wire:click="$set('selectedPermissions', {{ json_encode($availablePermissions->pluck('id')->toArray()) }})">
                                                    Select All
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($permissionGroups as $group => $permissions)
                                                    @if (count($permissions) > 0)
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card card-outline card-primary">
                                                                <div class="card-header">
                                                                    <h3 class="card-title">
                                                                        {{ ucfirst($group) }} Permissions
                                                                        <span
                                                                            class="badge badge-light ml-2">{{ count($permissions) }}</span>
                                                                    </h3>
                                                                </div>
                                                                <div class="card-body"
                                                                    style="max-height: 200px; overflow-y: auto;">
                                                                    @foreach ($permissions as $permission)
                                                                        <div class="form-check"
                                                                            wire:key="perm-{{ $roleId ?? 'new' }}-{{ $permission->id }}">
                                                                            <input type="checkbox"
                                                                                wire:model="selectedPermissions"
                                                                                value="{{ $permission->id }}"
                                                                                class="form-check-input"
                                                                                id="edit_perm_{{ $permission->id }}">
                                                                            <label class="form-check-label"
                                                                                for="edit_perm_{{ $permission->id }}">
                                                                                {{ $permission->name }}
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @error('selectedPermissions')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Role
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Permissions Management Modal -->
            <div class="modal fade" id="permissionsModal" tabindex="-1" wire:ignore.self>
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info">
                            <h4 class="modal-title">
                                <i class="fas fa-key"></i> Manage Permissions for:
                                {{ ucwords(str_replace(['-', '_'], ' ', $name)) }}
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form wire:submit.prevent="updatePermissions">
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Assign permissions to the
                                    <strong>{{ ucwords(str_replace(['-', '_'], ' ', $name)) }}</strong> role.
                                    @if ($isProtected && !auth()->user()->hasRole('super-admin'))
                                        <br><strong class="text-danger">Note:</strong> This is a protected role. Only
                                        super administrators can modify its permissions.
                                    @endif
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Available Permissions ({{ $permissionCount }} total)</span>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                wire:click="$set('selectedPermissions', {{ json_encode($availablePermissions->pluck('id')->toArray()) }})">
                                                Select All
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($permissionGroups as $group => $permissions)
                                                @if (count($permissions) > 0)
                                                    <div class="col-md-6 mb-3">
                                                        <div class="card card-outline card-primary">
                                                            <div class="card-header">
                                                                <h3 class="card-title">
                                                                    {{ ucfirst($group) }} Permissions
                                                                    <span
                                                                        class="badge badge-light ml-2">{{ count($permissions) }}</span>
                                                                </h3>
                                                            </div>
                                                            <div class="card-body"
                                                                style="max-height: 200px; overflow-y: auto;">
                                                                @foreach ($permissions as $permission)
                                                                    <div class="form-check"
                                                                        wire:key="perm-{{ $roleId ?? 'new' }}-{{ $permission->id }}">
                                                                        <input type="checkbox"
                                                                            wire:model="selectedPermissions"
                                                                            value="{{ $permission->id }}"
                                                                            class="form-check-input"
                                                                            id="edit_perm_{{ $permission->id }}">
                                                                        <label class="form-check-label"
                                                                            for="edit_perm_{{ $permission->id }}">
                                                                            {{ $permission->name }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Selected Permissions: {{ count($selectedPermissions) }}</label>
                                    <div class="selected-permissions">
                                        @if (count($selectedPermissions) > 0)
                                            @php
                                                $selectedPerms = $availablePermissions->whereIn(
                                                    'id',
                                                    $selectedPermissions,
                                                );
                                            @endphp
                                            <div class="d-flex flex-wrap">
                                                @foreach ($selectedPerms as $permission)
                                                    <span class="badge badge-primary m-1">
                                                        {{ $permission->name }}
                                                        <button type="button" class="btn btn-xs btn-link text-white"
                                                            wire:click="$wire.removeSelectedPermission({{ $permission->id }})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted">No permissions selected</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-info" wire:loading.attr="disabled"
                                    wire:target="updatePermissions" @if ($isProtected && !auth()->user()->hasRole('super-admin')) disabled @endif>
                                    <span wire:loading.remove wire:target="updatePermissions">
                                        <i class="fas fa-save"></i> Update Permissions
                                    </span>
                                    <span wire:loading wire:target="updatePermissions">
                                        <i class="fas fa-spinner fa-spin"></i> Updating...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Duplicate Role Modal -->
            <div class="modal fade" id="duplicateRoleModal" tabindex="-1" wire:ignore.self>
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-success">
                            <h4 class="modal-title">
                                <i class="fas fa-copy"></i> Duplicate Role
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form wire:submit.prevent="duplicateRole">
                            <div class="modal-body">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Duplicating role:
                                    <strong>{{ ucwords(str_replace(['-', '_'], ' ', $name)) }}</strong>
                                </div>

                                <div class="form-group">
                                    <label for="duplicateName">New Role Name *</label>
                                    <input type="text" wire:model="duplicateName" class="form-control"
                                        id="duplicateName" required>
                                    @error('duplicateName')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">
                                        Display:
                                        {{ $duplicateName ? ucwords(str_replace(['-', '_'], ' ', $duplicateName)) : '' }}
                                        <br>
                                        Use lowercase letters, numbers, and hyphens only
                                    </small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                            wire:model="includePermissions" id="includePermissions">
                                        <label class="custom-control-label" for="includePermissions">
                                            Copy permissions from original role
                                            <span
                                                class="badge badge-info ml-2">{{ count($selectedPermissions) }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-copy"></i> Duplicate Role
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteRoleModal" tabindex="-1" wire:ignore.self>
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h4 class="modal-title">
                                <i class="fas fa-trash"></i> Delete Role(s)
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @php
                                $rolesToDelete = \Spatie\Permission\Models\Role::whereIn('id', $selectedRoles)->get();
                                $protectedRoles = $rolesToDelete->filter(function ($role) {
                                    return $this->isRoleProtected($role->name);
                                });
                                $regularRoles = $rolesToDelete->filter(function ($role) {
                                    return !$this->isRoleProtected($role->name);
                                });
                            @endphp

                            @if ($protectedRoles->count() > 0 && !auth()->user()->hasRole('super-admin'))
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Warning!</strong> The following protected roles cannot be deleted:
                                    <ul class="mt-2">
                                        @foreach ($protectedRoles as $role)
                                            <li><strong>{{ $role->display_name ?? $role->name }}</strong></li>
                                        @endforeach
                                    </ul>
                                    Only super administrators can delete protected roles.
                                </div>
                            @endif

                            @if ($regularRoles->count() > 0)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Are you sure you want to delete the following role(s)?</strong>
                                    <ul class="mt-2">
                                        @foreach ($regularRoles as $role)
                                            @php
                                                $userCount = $this->getRoleUsersCount($role->id);
                                            @endphp
                                            <li>
                                                <strong>{{ $role->display_name ?? $role->name }}</strong>
                                                @if ($userCount > 0)
                                                    <span class="badge badge-danger ml-2">
                                                        {{ $userCount }} user(s) assigned
                                                    </span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>

                                    @if (
                                        $regularRoles->contains(function ($role) {
                                            return $this->getRoleUsersCount($role->id) > 0;
                                        }))
                                        <div class="mt-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="forceDelete"
                                                    wire:model="forceDelete">
                                                <label class="custom-control-label text-danger" for="forceDelete">
                                                    Force delete roles with assigned users (users will lose these roles)
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Note:</strong> This action cannot be undone. All permissions associated with
                                    these roles will also be removed.
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            @if ($regularRoles->count() > 0)
                                <button type="button" class="btn btn-danger" wire:click="deleteRoles">
                                    <i class="fas fa-trash"></i> Delete Selected
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript to handle modal events -->
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                console.log('Livewire roles component initialized');

                // Show modal events
                Livewire.on('showCreateModal', () => {
                    console.log('Showing create modal');
                    $('#createRoleModal').modal('show');
                });

                Livewire.on('showEditModal', () => {
                    console.log('Showing edit modal');
                    $('#editRoleModal').modal('show');
                });

                Livewire.on('showPermissionsModal', () => {
                    console.log('Showing permissions modal');
                    $('#permissionsModal').modal('show');
                });

                Livewire.on('showDuplicateModal', () => {
                    console.log('Showing duplicate modal');
                    $('#duplicateRoleModal').modal('show');
                });

                Livewire.on('showDeleteModal', () => {
                    console.log('Showing delete modal');
                    $('#deleteRoleModal').modal('show');
                });

                // Close modal events - FIXED
                // FIXED Close Modal Listener for Livewire
                Livewire.on('closeModal', (data) => {
                    // Livewire 3 sometimes wraps the payload in an array: [ {modal: 'id'} ]
                    // This line handles both data.modal AND data[0].modal
                    const modalId = data.modal || (data[0] && data[0].modal);

                    console.log('Attempting to close:', modalId);

                    if (!modalId) {
                        console.error('No modal ID provided in closeModal event', data);
                        return;
                    }

                    const modalElement = $('#' + modalId);

                    if (modalElement.length) {
                        // 1. Hide the modal using Bootstrap
                        modalElement.modal('hide');

                        // 2. Force cleanup (Bootstrap sometimes leaves backdrops if the DOM changes too fast)
                        setTimeout(() => {
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                            $('body').css('padding-right', '');
                        }, 300);
                    } else {
                        console.warn('Modal element not found:', modalId);
                    }
                });

                // Add a listener specifically for the permissions-updated event if you still want to use it
                Livewire.on('permissions-updated', () => {
                    $('#permissionsModal').modal('hide');
                    setTimeout(() => {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                    }, 400);
                });

                // Handle Bootstrap modal hidden events
                $('#createRoleModal').on('hidden.bs.modal', function() {
                    console.log('Create modal hidden');
                    @this.call('closeCreateModal');
                });

                $('#editRoleModal').on('hidden.bs.modal', function() {
                    console.log('Edit modal hidden');
                    @this.call('closeEditModal');
                });

                $('#permissionsModal').on('hidden.bs.modal', function() {
                    console.log('Permissions modal hidden');
                    @this.call('closePermissionsModal');
                });

                $('#duplicateRoleModal').on('hidden.bs.modal', function() {
                    console.log('Duplicate modal hidden');
                    @this.call('closeDuplicateModal');
                });

                $('#deleteRoleModal').on('hidden.bs.modal', function() {
                    console.log('Delete modal hidden');
                    @this.call('closeDeleteModal');
                });

                // Add validation regex for role name
                $('#roleName, #duplicateName, #editRoleName').on('input', function() {
                    $(this).val($(this).val().toLowerCase().replace(/[^a-z0-9\-]/g, ''));
                });

                // Prevent form submission on enter key in modals
                $('.modal').on('keydown', function(e) {
                    if (e.key === 'Enter' && $(e.target).is('input:not([type="submit"])')) {
                        e.preventDefault();
                        return false;
                    }
                });
            });

            // Toastr notifications handler
            document.addEventListener('livewire:notify', (event) => {
                const {
                    type,
                    message
                } = event.detail;
                console.log('Notification:', type, message);

                switch (type) {
                    case 'success':
                        toastr.success(message);
                        break;
                    case 'error':
                        toastr.error(message);
                        break;
                    case 'warning':
                        toastr.warning(message);
                        break;
                    case 'info':
                        toastr.info(message);
                        break;
                }
            });

            // Toastr configuration
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // And in JavaScript:
            window.addEventListener('permissions-updated', () => {
                $('#permissionsModal').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            });

            // Global modal cleanup
            $(document).on('hide.bs.modal', '.modal', function() {
                // Remove any leftover backdrops
                setTimeout(() => {
                    const backdrops = $('.modal-backdrop');
                    if (backdrops.length > 1) {
                        backdrops.not(':first').remove();
                    }
                }, 100);
            });
        </script>
    @endpush
</div>
