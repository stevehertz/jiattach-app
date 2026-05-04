<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <div class="row">
        <div class="col-md-12">
            <!-- Team Stats -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $organization->users()->count() }}</h3>
                            <p>Total Members</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $organization->users()->wherePivot('is_active', true)->count() }}</h3>
                            <p>Active Members</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $organization->owners()->count() }}</h3>
                            <p>Owners</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-crown"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $organization->admins()->count() }}</h3>
                            <p>Admins</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Members Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-2"></i>Team Members
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" wire:click="showAddMemberForm">
                            <i class="fas fa-plus mr-1"></i> Add Member
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                                    placeholder="Search members...">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control form-control-sm" wire:model.live="filterRole">
                                <option value="">All Roles</option>
                                <option value="owner">Owner</option>
                                <option value="admin">Admin</option>
                                <option value="member">Member</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control form-control-sm" wire:model.live="filterStatus">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control form-control-sm" wire:model.live="perPage">
                                <option value="10">10 per page</option>
                                <option value="25">25 per page</option>
                                <option value="50">50 per page</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Member</th>
                                    <th>Contact</th>
                                    <th>Role</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamMembers as $member)
                                    <tr class="{{ !$member->pivot->is_active ? 'table-secondary' : '' }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    <div class="avatar-initials bg-info img-circle d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px; color: white; font-size: 14px;">
                                                        {{ $member->initials() }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>{{ $member->full_name }}</strong>
                                                    @if ($member->pivot->is_primary_contact)
                                                        <span class="badge badge-warning ml-1" title="Primary Contact">
                                                            <i class="fas fa-star"></i>
                                                        </span>
                                                    @endif
                                                    @if ($member->id === auth()->id())
                                                        <span class="badge badge-info ml-1">You</span>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">{{ $member->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($member->phone)
                                                <div><i class="fas fa-phone mr-1 text-muted"></i>{{ $member->phone }}
                                                </div>
                                            @endif
                                            @if ($member->gender)
                                                <small class="text-muted">
                                                    <i
                                                        class="fas fa-{{ $member->gender === 'male' ? 'mars' : ($member->gender === 'female' ? 'venus' : 'genderless') }} mr-1"></i>
                                                    {{ ucfirst($member->gender) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $member->pivot->role === 'owner' ? 'primary' : ($member->pivot->role === 'admin' ? 'info' : 'secondary') }}">
                                                {{ ucfirst($member->pivot->role) }}
                                            </span>
                                        </td>
                                        <td>{{ $member->pivot->position ?? 'Not specified' }}</td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $member->pivot->is_active ? 'success' : 'danger' }}">
                                                {{ $member->pivot->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $member->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-warning"
                                                    wire:click="editMember({{ $member->id }})" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                @if ($member->id !== auth()->id())
                                                    <button type="button"
                                                        class="btn btn-{{ $member->pivot->is_active ? 'secondary' : 'success' }}"
                                                        wire:click="toggleActive({{ $member->id }})"
                                                        title="{{ $member->pivot->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i
                                                            class="fas fa-{{ $member->pivot->is_active ? 'ban' : 'check' }}"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-danger"
                                                        wire:click="confirmRemove({{ $member->id }})" title="Remove">
                                                        <i class="fas fa-user-minus"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No team members found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $teamMembers->links() }}
                        <div class="text-muted small">
                            @if ($teamMembers->total() > 0)
                                Showing {{ $teamMembers->firstItem() }} to {{ $teamMembers->lastItem() }}
                                of {{ $teamMembers->total() }} members
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Member Modal -->
    @if ($showAddForm)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-{{ $isEditing ? 'warning' : 'primary' }}">
                        <h5 class="modal-title">
                            <i class="fas fa-{{ $isEditing ? 'edit' : 'plus' }} mr-2"></i>
                            {{ $isEditing ? 'Edit Team Member' : 'Add New Team Member' }}
                        </h5>
                        <button type="button" class="close" wire:click="resetForm">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form wire:submit="saveMember">
                        <div class="modal-body">
                            <!-- Personal Information -->
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-user mr-2"></i>Personal Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('first_name') is-invalid @enderror"
                                            id="first_name" wire:model.live="first_name"
                                            placeholder="Enter first name">
                                        @error('first_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('last_name') is-invalid @enderror"
                                            id="last_name" wire:model.live="last_name" placeholder="Enter last name">
                                        @error('last_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input type="email"
                                            class="form-control @error('email') is-invalid @enderror" id="email"
                                            wire:model.live="email" placeholder="Enter email">
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="text"
                                            class="form-control @error('phone') is-invalid @enderror" id="phone"
                                            wire:model.live="phone" placeholder="+254 700 000 000">
                                        @error('phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">
                                            Password
                                            @if (!$isEditing)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            id="password" wire:model.live="password"
                                            placeholder="{{ $isEditing ? 'Leave blank to keep current' : 'Enter password' }}">
                                        @if ($isEditing)
                                            <small class="text-muted">Leave blank to keep current password</small>
                                        @endif
                                        @error('password')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirm Password</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            wire:model.live="password_confirmation" placeholder="Confirm password">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <select class="form-control @error('gender') is-invalid @enderror"
                                            id="gender" wire:model.live="gender">
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                        @error('gender')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Organization Role -->
                            <h6 class="text-muted mb-3 mt-4">
                                <i class="fas fa-building mr-2"></i>Organization Role
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role">Role <span class="text-danger">*</span></label>
                                        <select class="form-control @error('role') is-invalid @enderror"
                                            id="role" wire:model.live="role">
                                            <option value="member">Member</option>
                                            <option value="admin">Admin</option>
                                            <option value="owner">Owner</option>
                                        </select>
                                        @error('role')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="position">Position/Job Title</label>
                                        <input type="text"
                                            class="form-control @error('position') is-invalid @enderror"
                                            id="position" wire:model.live="position" placeholder="e.g., HR Manager">
                                        @error('position')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input"
                                                id="is_primary_contact" wire:model.live="is_primary_contact">
                                            <label class="custom-control-label" for="is_primary_contact">
                                                Primary Contact
                                            </label>
                                        </div>
                                        <small class="text-muted">Main point of contact for students</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="is_active"
                                                wire:model.live="is_active">
                                            <label class="custom-control-label" for="is_active">
                                                Active Account
                                            </label>
                                        </div>
                                        <small class="text-muted">Enable or disable account access</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" wire:click="resetForm">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveMember">
                                    <i class="fas fa-save mr-1"></i>
                                    {{ $isEditing ? 'Update Member' : 'Add Member' }}
                                </span>
                                <span wire:loading wire:target="saveMember">
                                    <i class="fas fa-spinner fa-spin mr-1"></i> Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif


    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Notification handler
                Livewire.on('notify', (data) => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });

                    Toast.fire({
                        icon: data[0].type || 'success',
                        title: data[0].message || 'Action completed'
                    });
                });

                // Confirm remove handler
                Livewire.on('confirm-remove', (data) => {
                    Swal.fire({
                        title: 'Remove Team Member?',
                        html: `Are you sure you want to remove <strong>${data[0].memberName}</strong> from the organization?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, remove!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            @this.removeMember(data[0].memberId);
                        }
                    });
                });
            });
        </script>
    @endpush
</div>
