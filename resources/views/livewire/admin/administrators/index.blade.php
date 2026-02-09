<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <section class="content">
        <div class="container-fluid">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $totalAdministrators }}</h3>
                            <p>Total Administrators</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $activeAdministrators }}</h3>
                            <p>Active Administrators</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <a href="{{ route('admin.administrators.index') }}?statusFilter=active"
                            class="small-box-footer">
                            View Active <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $inactiveAdministrators }}</h3>
                            <p>Inactive Administrators</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <a href="{{ route('admin.administrators.index') }}?statusFilter=inactive"
                            class="small-box-footer">
                            View Inactive <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $superAdmins }}</h3>
                            <p>Super Administrators</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <a href="{{ route('admin.administrators.index') }}?roleFilter=super_admin"
                            class="small-box-footer">
                            View Super Admins <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Administrators</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.administrators.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Administrator
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                    placeholder="Search administrators...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="statusFilter" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="roleFilter" class="form-control">
                                    <option value="">All Roles</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">
                                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="perPage" class="form-control">
                                    <option value="10">10 per page</option>
                                    <option value="15">15 per page</option>
                                    <option value="25">25 per page</option>
                                    <option value="50">50 per page</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            @if (count($selectedAdministrators) > 0)
                                <div class="input-group">
                                    <select wire:model="bulkAction" class="form-control">
                                        <option value="">Bulk Actions</option>
                                        <option value="activate">Activate Selected</option>
                                        <option value="deactivate">Deactivate Selected</option>
                                        <option value="delete">Delete Selected</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-warning" type="button" wire:click="executeBulkAction">
                                            <i class="fas fa-play"></i> Apply
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Administrators Table -->
            <div class="card">
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
                                    <th wire:click="sortBy('first_name')" style="cursor: pointer;">
                                        Administrator
                                        @if ($sortBy === 'first_name')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('email')" style="cursor: pointer;">
                                        Contact
                                        @if ($sortBy === 'email')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                        Joined
                                        @if ($sortBy === 'created_at')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th>Roles & Permissions</th>
                                    <th wire:click="sortBy('is_active')" style="cursor: pointer;">
                                        Status
                                        @if ($sortBy === 'is_active')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($administrators as $admin)
                                    @php
                                        $primaryRole = $admin->getRoleNames()->first();
                                        $roleBadgeColor = match ($primaryRole) {
                                            'super_admin' => 'danger',
                                            'admin' => 'success',
                                            'moderator' => 'info',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="icheck-primary">
                                                <input type="checkbox" wire:model="selectedAdministrators"
                                                    value="{{ $admin->id }}" id="admin_{{ $admin->id }}">
                                                <label for="admin_{{ $admin->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    {!! getUserAvatar($admin, 40) !!}
                                                </div>
                                                <div>
                                                    <strong>{{ $admin->full_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">ID: {{ $admin->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <a href="mailto:{{ $admin->email }}" class="text-primary">
                                                    <i class="fas fa-envelope mr-1"></i> {{ $admin->email }}
                                                </a>
                                                <br>
                                                @if ($admin->phone)
                                                    <small class="text-muted">
                                                        <i class="fas fa-phone mr-1"></i>
                                                        {{ formatPhoneNumber($admin->phone) }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">No phone</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            {{ formatDate($admin->created_at) }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $admin->created_at->diffForHumans() }}
                                            </small>
                                            @if ($admin->last_login_at)
                                                <br>
                                                <small class="text-info">
                                                    <i class="fas fa-sign-in-alt"></i> Last login:
                                                    {{ timeAgo($admin->last_login_at) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                <span class="badge badge-{{ $roleBadgeColor }}">
                                                    {{ ucfirst(str_replace('_', ' ', $primaryRole)) }}
                                                </span>
                                                @if ($admin->roles->count() > 1)
                                                    <span class="badge badge-light">
                                                        +{{ $admin->roles->count() - 1 }} more
                                                    </span>
                                                @endif
                                            </div>
                                            <small class="text-muted"> 
                                                {{ $admin->roles->pluck('name')->implode(', ') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if ($admin->is_active)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Active
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times-circle"></i> Inactive
                                                </span>
                                            @endif
                                            <br>
                                            @if ($admin->is_verified)
                                                <small class="badge badge-info">Verified</small>
                                            @else
                                                <small class="badge badge-warning">Unverified</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.administrators.show', $admin) }}"
                                                    class="btn btn-info btn-sm" title="View Profile">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.administrators.edit', $admin) }}"
                                                    class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-secondary btn-sm dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <button class="dropdown-item" type="button"
                                                            wire:click="toggleStatus({{ $admin->id }})">
                                                            @if ($admin->is_active)
                                                                <i class="fas fa-user-times text-warning"></i>
                                                                Deactivate
                                                            @else
                                                                <i class="fas fa-user-check text-success"></i> Activate
                                                            @endif
                                                        </button>
                                                        <button class="dropdown-item" type="button"
                                                            wire:click="openRoleModal({{ $admin->id }})">
                                                            <i class="fas fa-user-tag text-primary"></i> Manage Roles
                                                        </button>
                                                        @if ($admin->id !== auth()->id())
                                                            <div class="dropdown-divider"></div>
                                                            <button class="dropdown-item text-danger" type="button"
                                                                wire:click="openDeleteModal({{ $admin->id }})">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-user-shield fa-3x mb-3"></i>
                                                <h4>No Administrators Found</h4>
                                                <p>Create your first administrator or adjust your filters.</p>
                                                <a href="{{ route('admin.administrators.create') }}"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create New Administrator
                                                </a>
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
                            Showing {{ $administrators->firstItem() ?? 0 }} to {{ $administrators->lastItem() ?? 0 }}
                            of {{ $administrators->total() }} entries
                        </span>
                    </div>
                    <div class="float-right">
                        {{ $administrators->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Role Management Modal -->
    @if ($showRoleModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1"
            role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Manage Roles</h5>
                        <button type="button" class="close" wire:click="closeRoleModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select Roles</label>
                            @foreach ($availableRoles as $roleValue => $roleLabel)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input"
                                        id="role_{{ $roleValue }}" wire:model="selectedUserRoles"
                                        value="{{ $roleValue }}">
                                    <label class="custom-control-label" for="role_{{ $roleValue }}">
                                        {{ $roleLabel }}
                                        @if ($roleValue === 'super_admin')
                                            <span class="badge badge-danger ml-2">Full Access</span>
                                        @elseif($roleValue === 'admin')
                                            <span class="badge badge-success ml-2">Admin Access</span>
                                        @elseif($roleValue === 'moderator')
                                            <span class="badge badge-info ml-2">Limited Access</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                            @error('selectedUserRoles')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Role Descriptions:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Super Admin:</strong> Full system access, can manage all administrators</li>
                                <li><strong>Admin:</strong> Full access to system features</li>
                                <li><strong>Moderator:</strong> Limited access for content moderation</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeRoleModal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="saveRoles">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @push('styles')
        <style>
            .cursor-pointer {
                cursor: pointer;
            }

            .custom-control-input:checked~.custom-control-label::before {
                border-color: #28a745;
                background-color: #28a745;
            }
        </style>
    @endpush

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title">Delete Administrator</h5>
                        <button type="button" class="close" wire:click="closeDeleteModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <h5>Warning: This action cannot be undone!</h5>
                        </div>

                        @if($userToDelete)
                            <div class="text-center mb-4">
                                {!! getUserAvatar($userToDelete, 80) !!}
                                <h4 class="mt-3">{{ $userToDelete->full_name }}</h4>
                                <p class="text-muted">{{ $userToDelete->email }}</p>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-shield-alt"></i>
                                <strong>Security Check:</strong>
                                <p class="mb-0">Type the administrator's email to confirm deletion:</p>
                                <div class="mt-2">
                                    <input type="text" class="form-control" id="confirmEmailIndex"
                                           placeholder="Type: {{ $userToDelete->email }}">
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" id="confirmDeleteBtnIndex" class="btn btn-danger" disabled>
                            <i class="fas fa-trash"></i> Delete Administrator
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            function setupDeleteConfirmIndex() {
                const input = document.getElementById('confirmEmailIndex');
                const btn = document.getElementById('confirmDeleteBtnIndex');
                if (!input || !btn) return;

                // Extract expected email from placeholder - more robust
                let placeholder = input.getAttribute('placeholder') || '';
                let expected = placeholder.replace('Type: ', '').trim();
                
                console.log('Expected email:', expected);
                console.log('Input value:', input.value);
                
                // Update button state - normalize comparison
                const updateButtonState = () => {
                    const isMatch = input.value.trim().toLowerCase() === expected.toLowerCase();
                    console.log('Is match:', isMatch);
                    btn.disabled = !isMatch;
                };
                
                updateButtonState();
                
                // Add direct input listener (no cloning issues)
                input.addEventListener('input', updateButtonState);
                
                // Add click listener to delete button
                btn.onclick = function(e) {
                    e.preventDefault();
                    const isMatch = input.value.trim().toLowerCase() === expected.toLowerCase();
                    if (isMatch) {
                        @this.call('confirmDelete', input.value);
                    } else {
                        alert('Please type the email address correctly to confirm deletion.');
                    }
                };
            }

            document.addEventListener('livewire:load', function () {
                setTimeout(() => setupDeleteConfirmIndex(), 100);
            });
            
            // Also run on modal open - watch for when modal appears
            const observer = new MutationObserver(() => {
                const modal = document.querySelector('.modal');
                if (modal && modal.style.display === 'block') {
                    setupDeleteConfirmIndex();
                }
            });
            
            observer.observe(document.body, { attributes: true, subtree: true });
        </script>
    @endpush

</div>
