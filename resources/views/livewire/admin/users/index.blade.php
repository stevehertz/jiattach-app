<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1">
                            <i class="fas fa-users"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Users</span>
                            <span class="info-box-number">{{ $stats['total'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1">
                            <i class="fas fa-user-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active Users</span>
                            <span class="info-box-number">{{ $stats['active'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning elevation-1">
                            <i class="fas fa-user-graduate"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Students</span>
                            <span class="info-box-number">{{ $stats['students'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger elevation-1">
                            <i class="fas fa-building"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Employers</span>
                            <span class="info-box-number">{{ $stats['employers'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Users List</h3>

                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="form-control float-right" placeholder="Search by name, email, phone...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" wire:click="$set('search', '')"
                                    title="Clear search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions Bar -->
                @if ($showBulkActions)
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted">
                                    <i class="fas fa-check-circle text-primary mr-1"></i>
                                    {{ count($selectedUsers) }} user(s) selected
                                </span>
                            </div>
                            <div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm" wire:click="bulkActivate">
                                        <i class="fas fa-check mr-1"></i> Activate
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" wire:click="bulkDeactivate">
                                        <i class="fas fa-ban mr-1"></i> Deactivate
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" wire:click="confirmDelete">
                                        <i class="fas fa-trash mr-1"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Filters -->
                <div class="card-body border-bottom @if (!$showFilters) d-none @endif" id="filterSection">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Role</label>
                                <select wire:model.live="roleFilter" class="form-control">
                                    <option value="">All Roles</option>
                                    @foreach ($roles as $roleName => $roleLabel)
                                        <option value="{{ $roleName }}">{{ ucfirst($roleName) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select wire:model.live="statusFilter" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active">Active Only</option>
                                    <option value="inactive">Inactive Only</option>
                                    <option value="verified">Verified Only</option>
                                    <option value="unverified">Unverified Only</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Items Per Page</label>
                                <select wire:model.live="perPage" class="form-control">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" wire:model="selectAll" id="selectAll"
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('first_name')" style="cursor: pointer;">
                                        User
                                        @if ($sortField === 'first_name')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                        Joined
                                        @if ($sortField === 'created_at')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr wire:key="user-{{ $user->id }}">
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" wire:model="selectedUsers"
                                                    value="{{ $user->id }}" id="user_{{ $user->id }}"
                                                    class="custom-control-input">
                                                <label class="custom-control-label"
                                                    for="user_{{ $user->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    @php
                                                        $initials = getInitials($user->full_name);
                                                        $colors = [
                                                            'primary',
                                                            'success',
                                                            'info',
                                                            'warning',
                                                            'danger',
                                                            'secondary',
                                                        ];
                                                        $color = $colors[crc32($user->email) % count($colors)];
                                                    @endphp
                                                    <div class="avatar-initials bg-{{ $color }} img-circle"
                                                        style="width: 40px; height: 40px; line-height: 40px; text-align: center; color: white; font-weight: bold; font-size: 14px;">
                                                        {{ $initials }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>{{ $user->full_name }}</strong>
                                                    <div class="text-muted small">
                                                        @if ($user->studentProfile)
                                                            <i class="fas fa-graduation-cap mr-1"></i>
                                                            {{ $user->studentProfile->student_reg_number ?: 'Student' }}
                                                        @elseif($user->employer)
                                                            <i class="fas fa-building mr-1"></i>
                                                            {{ $user->employer->company_name ?: 'Employer' }}
                                                        @elseif($user->mentor)
                                                            <i class="fas fa-user-tie mr-1"></i>
                                                            {{ $user->mentor->job_title ?: 'Mentor' }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $user->email }}
                                            @if ($user->email_verified_at)
                                                <span class="badge badge-success badge-sm ml-1">Email Verified</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->phone)
                                                {{ formatPhoneNumber($user->phone) }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach ($user->roles as $role)
                                                @php
                                                    $roleColors = [
                                                        'admin' => 'danger',
                                                        'student' => 'success',
                                                        'employer' => 'primary',
                                                        'mentor' => 'warning',
                                                    ];
                                                    $color = $roleColors[$role->name] ?? 'secondary';
                                                @endphp
                                                <span
                                                    class="badge badge-{{ $color }}">{{ ucfirst($role->name) }}</span>
                                            @endforeach
                                            @if ($user->roles->isEmpty())
                                                <span class="badge badge-secondary">No Role</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif

                                            @if ($user->is_verified)
                                                <span class="badge badge-info ml-1">Verified</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                {{ formatDate($user->created_at) }}
                                            </div>
                                            <small class="text-muted">{{ timeAgo($user->created_at) }}</small>
                                        </td>
                                        <td>
                                            <!-- In your user table row actions -->
                                            @if ($this->canModifyUser($user->id))
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info"
                                                        wire:click="viewUser({{ $user->id }})"
                                                        title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-secondary dropdown-toggle"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            @if ($user->is_active)
                                                                <button class="dropdown-item text-warning"
                                                                    wire:click="deactivateUser({{ $user->id }})">
                                                                    <i class="fas fa-ban mr-2"></i> Deactivate
                                                                </button>
                                                            @else
                                                                <button class="dropdown-item text-success"
                                                                    wire:click="activateUser({{ $user->id }})">
                                                                    <i class="fas fa-check mr-2"></i> Activate
                                                                </button>
                                                            @endif

                                                            @if (!$user->is_verified)
                                                                <button class="dropdown-item text-info"
                                                                    wire:click="verifyUser({{ $user->id }})">
                                                                    <i class="fas fa-check-circle mr-2"></i> Verify
                                                                    Account
                                                                </button>
                                                            @endif

                                                            <div class="dropdown-divider"></div>

                                                            <h6 class="dropdown-header">Change Role</h6>
                                                            @foreach ($roles as $roleName => $roleLabel)
                                                                @if (!$user->hasRole($roleName))
                                                                    <button class="dropdown-item"
                                                                        wire:click="assignRole({{ $user->id }}, '{{ $roleName }}')">
                                                                        <i class="fas fa-user-tag mr-2"></i>
                                                                        {{ ucfirst($roleName) }}
                                                                    </button>
                                                                @endif
                                                            @endforeach

                                                            <!-- Delete Option -->
                                                            <div class="dropdown-divider"></div>
                                                            <button class="dropdown-item text-danger"
                                                                wire:click="confirmDelete({{ $user->id }})">
                                                                <i class="fas fa-trash mr-2"></i> Delete User
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- View only for protected users -->
                                                <button type="button" class="btn btn-info btn-sm"
                                                    wire:click="viewUser({{ $user->id }})" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                @if ($user->id === Auth::id())
                                                    <span class="badge badge-info ml-2">Current User</span>
                                                @endif
                                                @if ($this->isSuperAdmin($user))
                                                    <span class="badge badge-danger ml-2">Super Admin</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No users found</h5>
                                            @if ($search || $roleFilter || $statusFilter)
                                                <p class="text-muted">Try adjusting your search or filters</p>
                                                <button
                                                    wire:click="$set(['search' => '', 'roleFilter' => '', 'statusFilter' => ''])"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-times mr-1"></i> Clear Filters
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="card-footer clearfix">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">
                                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }}
                                of {{ $users->total() }} entries
                            </span>
                        </div>
                        <div>
                            @if ($users->hasPages())
                                {{ $users->links() }}
                            @endif
                        </div>
                        <div>
                            <button type="button" class="btn btn-default" wire:click="$toggle('showFilters')">
                                <i class="fas fa-filter mr-1"></i>
                                {{ $showFilters ? 'Hide Filters' : 'Show Filters' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Distribution Chart -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Users by Role
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <canvas id="roleChart" height="200"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <div class="mt-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge badge-success mr-2"
                                                style="width: 15px; height: 15px;"></span>
                                            <span>Students: {{ $roleDistribution['students'] }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge badge-primary mr-2"
                                                style="width: 15px; height: 15px;"></span>
                                            <span>Employers: {{ $roleDistribution['employers'] }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge badge-warning mr-2"
                                                style="width: 15px; height: 15px;"></span>
                                            <span>Mentors: {{ $roleDistribution['mentors'] }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge badge-danger mr-2"
                                                style="width: 15px; height: 15px;"></span>
                                            <span>Admins: {{ $roleDistribution['admins'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line mr-1"></i>
                                Monthly Registration
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="registrationChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-{{ $deleteType === 'bulk' ? 'warning' : 'danger' }}">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            @if ($deleteType === 'single')
                                Delete User: {{ $userToDelete->full_name ?? '' }}
                            @else
                                Delete Selected Users
                            @endif
                        </h5>
                        <button type="button" class="close text-white" wire:click="resetDeleteModal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if ($deleteType === 'single')
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <strong>Warning:</strong> You are about to delete this user permanently.
                            </div>

                            <!-- User details -->
                            @if ($userToDelete)
                                <div class="user-info mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="mr-3">
                                            @php
                                                $initials = getInitials($userToDelete->full_name);
                                                $colors = [
                                                    'primary',
                                                    'success',
                                                    'info',
                                                    'warning',
                                                    'danger',
                                                    'secondary',
                                                ];
                                                $color = $colors[crc32($userToDelete->email) % count($colors)];
                                            @endphp
                                            <div class="avatar-initials bg-{{ $color }} img-circle"
                                                style="width: 50px; height: 50px; line-height: 50px; text-align: center; color: white; font-weight: bold; font-size: 18px;">
                                                {{ $initials }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $userToDelete->full_name }}</h6>
                                            <small class="text-muted">{{ $userToDelete->email }}</small>
                                            <div class="mt-1">
                                                @foreach ($userToDelete->roles as $role)
                                                    <span
                                                        class="badge badge-primary">{{ ucfirst($role->name) }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <p>This action will:</p>
                            <ul>
                                <li>Permanently delete the user account</li>
                                <li>Remove all associated data</li>
                                <li>Cannot be undone</li>
                            </ul>

                            <!-- Confirmation input -->
                            <div class="form-group">
                                <label for="deleteConfirmation">
                                    Type <strong>DELETE</strong> to confirm:
                                </label>
                                <input type="text" wire:model.live="deleteConfirmation" id="deleteConfirmation"
                                    class="form-control {{ strtoupper(trim($deleteConfirmation)) === 'DELETE' ? 'is-valid' : ($deleteConfirmation ? 'is-invalid' : '') }}"
                                    placeholder="Type DELETE here">

                                @if ($deleteConfirmation && strtoupper(trim($deleteConfirmation)) !== 'DELETE')
                                    <div class="invalid-feedback">
                                        Please type exactly "DELETE" (case-insensitive)
                                    </div>
                                @elseif(strtoupper(trim($deleteConfirmation)) === 'DELETE')
                                    <div class="valid-feedback">
                                        ✓ Confirmed! Click Delete User to proceed.
                                    </div>
                                @endif
                            </div>
                        @else
                            <!-- Bulk delete -->
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <strong>Warning:</strong> You are about to delete {{ count($selectedUsers) }} user(s)
                                permanently.
                            </div>

                            <p>This action will:</p>
                            <ul>
                                <li>Permanently delete {{ count($selectedUsers) }} user accounts</li>
                                <li>Remove all associated data for these users</li>
                                <li>Cannot be undone</li>
                            </ul>

                            @php
                                $expectedText = 'DELETE ' . count($selectedUsers);
                                $isConfirmed = strtoupper(trim($deleteConfirmation)) === strtoupper($expectedText);
                            @endphp

                            <div class="form-group">
                                <label for="deleteConfirmation">
                                    Type <strong>{{ $expectedText }}</strong> to confirm:
                                </label>
                                <input type="text" wire:model.live="deleteConfirmation" id="deleteConfirmation"
                                    class="form-control {{ $isConfirmed ? 'is-valid' : ($deleteConfirmation ? 'is-invalid' : '') }}"
                                    placeholder="Type {{ $expectedText }} here">

                                @if ($deleteConfirmation && !$isConfirmed)
                                    <div class="invalid-feedback">
                                        Please type exactly "{{ $expectedText }}" (case-insensitive)
                                    </div>
                                @elseif($isConfirmed)
                                    <div class="valid-feedback">
                                        ✓ Confirmed! Click Delete {{ count($selectedUsers) }} Users to proceed.
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="resetDeleteModal">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </button>

                        @php
                            $canDeleteSingle =
                                $deleteType === 'single' && strtoupper(trim($deleteConfirmation)) === 'DELETE';
                            $canDeleteBulk =
                                $deleteType === 'bulk' &&
                                strtoupper(trim($deleteConfirmation)) === strtoupper('DELETE ' . count($selectedUsers));
                        @endphp

                        @if ($deleteType === 'single')
                            <button type="button" class="btn btn-danger" wire:click="deleteUser"
                                {{ !$canDeleteSingle ? 'disabled' : '' }}>
                                <i class="fas fa-trash mr-1"></i> Delete User
                            </button>
                        @else
                            <button type="button" class="btn btn-warning" wire:click="deleteUser"
                                {{ !$canDeleteBulk ? 'disabled' : '' }}>
                                <i class="fas fa-trash mr-1"></i> Delete {{ count($selectedUsers) }} Users
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif


    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Initialize Role Distribution Chart
                function initRoleChart() {
                    const ctx = document.getElementById('roleChart').getContext('2d');
                    window.roleChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Students', 'Employers', 'Mentors', 'Admins'],
                            datasets: [{
                                data: [
                                    @this.roleDistribution.students,
                                    @this.roleDistribution.employers,
                                    @this.roleDistribution.mentors,
                                    @this.roleDistribution.admins
                                ],
                                backgroundColor: [
                                    '#28a745',
                                    '#007bff',
                                    '#ffc107',
                                    '#dc3545'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }

                // Initialize Monthly Registration Chart
                function initRegistrationChart() {
                    const ctx = document.getElementById('registrationChart').getContext('2d');
                    window.registrationChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($monthlyRegistrations['months']),
                            datasets: [{
                                label: 'New Users',
                                data: @json($monthlyRegistrations['counts']),
                                borderColor: '#007bff',
                                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }

                // Initialize charts when component is loaded
                initRoleChart();
                initRegistrationChart();

                // Reinitialize charts when component updates
                Livewire.on('refreshUsers', () => {
                    // Destroy old charts
                    if (window.roleChart) window.roleChart.destroy();
                    if (window.registrationChart) window.registrationChart.destroy();

                    // Reinitialize with updated data
                    initRoleChart();
                    initRegistrationChart();
                });

                // Toast notification handler
                Livewire.on('show-toast', (event) => {
                    toastr[event.type](event.message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000
                    });
                });

                // Initialize toastr
                if (typeof toastr !== 'undefined') {
                    toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
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
                }

                // Focus on confirmation input when modal opens
                Livewire.on('showDeleteModal', () => {
                    setTimeout(() => {
                        const input = document.getElementById('deleteConfirmation');
                        if (input) {
                            input.focus();
                        }
                    }, 100);
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .avatar-initials {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                font-weight: bold;
            }

            .badge-sm {
                font-size: 0.7em;
                padding: 0.2em 0.4em;
            }

            .table-hover tbody tr:hover {
                background-color: rgba(0, 123, 255, 0.05);
            }

            .btn-group-sm .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }

            .dropdown-item {
                cursor: pointer;
            }

            .dropdown-item:hover {
                background-color: #f8f9fa;
            }

            .chart-container {
                position: relative;
                height: 200px;
                width: 100%;
            }

            /* Modal Styles */
            .modal-backdrop {
                opacity: 0.5;
            }

            .modal.show {
                display: block;
                background-color: rgba(0, 0, 0, 0.5);
            }

            .btn:disabled {
                cursor: not-allowed;
                opacity: 0.65;
            }

            .user-info {
                border-left: 4px solid #dc3545;
                padding-left: 15px;
                background-color: #f8f9fa;
                border-radius: 4px;
                padding: 15px;
            }

            /* Checkbox styles */
            .custom-control-input:checked~.custom-control-label::before {
                background-color: #007bff;
                border-color: #007bff;
            }

            .custom-checkbox {
                margin-bottom: 0;
            }
        </style>
    @endpush
</div>
