<div>

    <section class="content">
        <div class="container-fluid">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($stats['total']) }}</h3>
                            <p>Total Organizations</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            <i class="fas fa-arrow-circle-right"></i> View All
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($stats['active']) }}</h3>
                            <p>Active Organizations</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="#" wire:click="$set('status', 'active')" class="small-box-footer">
                            <i class="fas fa-arrow-circle-right"></i> Filter Active
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ number_format($stats['verified']) }}</h3>
                            <p>Verified</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <a href="#" wire:click="$set('verification', 'verified')" class="small-box-footer">
                            <i class="fas fa-arrow-circle-right"></i> Filter Verified
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($stats['pending']) }}</h3>
                            <p>Pending Verification</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="#" wire:click="$set('verification', 'pending')" class="small-box-footer">
                            <i class="fas fa-arrow-circle-right"></i> Filter Pending
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building mr-2"></i>
                        Registered Organizations
                        <span class="badge badge-info ml-2">{{ $organizations->total() }}</span>
                    </h3>

                    <div class="card-tools">
                        <!-- Filter Toggle -->
                        <button type="button" class="btn btn-tool" wire:click="$toggle('showFilters')">
                            <i class="fas fa-filter"></i>
                        </button>

                        <!-- Export Button -->
                        <button type="button" class="btn btn-tool" wire:click="export" wire:loading.attr="disabled">
                            <i class="fas fa-download"></i>
                        </button>

                        <!-- Add New Button -->
                        <a href="{{ route('admin.organizations.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                @if ($showFilters)
                    <div class="card-body border-bottom">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Industry</label>
                                    <select wire:model.live="industry" class="form-control form-control-sm">
                                        <option value="">All Industries</option>
                                        @foreach ($industries as $ind)
                                            <option value="{{ $ind }}">{{ $ind }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select wire:model.live="status" class="form-control form-control-sm">
                                        <option value="">All</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Verification</label>
                                    <select wire:model.live="verification" class="form-control form-control-sm">
                                        <option value="">All</option>
                                        <option value="verified">Verified</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>County</label>
                                    <select wire:model.live="county" class="form-control form-control-sm">
                                        <option value="">All Counties</option>
                                        @foreach ($counties as $cty)
                                            <option value="{{ $cty }}">{{ $cty }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date Range</label>
                                    <div class="input-group input-group-sm">
                                        <input type="date" wire:model.live="dateFrom" class="form-control"
                                            placeholder="From">
                                        <input type="date" wire:model.live="dateTo" class="form-control"
                                            placeholder="To">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button wire:click="resetFilters" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-undo"></i> Reset Filters
                                </button>

                                <span class="ml-2 text-muted">
                                    Showing {{ $organizations->firstItem() }} - {{ $organizations->lastItem() }}
                                    of {{ $organizations->total() }} results
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Search Bar -->
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input wire:model.live.debounce.300ms="search" type="text" class="form-control"
                                    placeholder="Search by name, email, phone, or industry...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                        data-toggle="dropdown">
                                        {{ $perPage }} per page
                                    </button>
                                    <div class="dropdown-menu">
                                        @foreach ([10, 15, 25, 50, 100] as $size)
                                            <a class="dropdown-item {{ $perPage == $size ? 'active' : '' }}"
                                                href="#"
                                                wire:click.prevent="$set('perPage', {{ $size }})">
                                                {{ $size }} per page
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($showBulkActions)
                            <div class="col-md-4 text-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-danger"
                                        wire:click="confirmBulkDelete">
                                        <i class="fas fa-trash"></i> Delete Selected
                                        ({{ count($selectedOrganizations) }})
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-head-fixed">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="selectAll"
                                                wire:model.live="selectAll">
                                            <label class="custom-control-label" for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th>Organization</th>
                                    <th>Owners/Contacts</th>
                                    <th>Industry</th>
                                    <th>Location</th>
                                    <th>Statistics</th>
                                    <th>Status</th>
                                    <th class="text-center" width="180">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($organizations as $org)
                                    <tr wire:key="org-{{ $org->id }}">
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="org_{{ $org->id }}"
                                                    wire:model.live="selectedOrganizations"
                                                    value="{{ $org->id }}">
                                                <label class="custom-control-label"
                                                    for="org_{{ $org->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40 mr-3">
                                                    <div class="symbol-label bg-light-primary">
                                                        <i class="fas fa-building text-primary"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>{{ $org->name }}</strong><br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-envelope"></i> {{ $org->email }}<br>
                                                        <i class="fas fa-phone"></i> {{ $org->phone ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @forelse($org->users->take(2) as $user)
                                                <div>
                                                    <i class="fas fa-user-circle"></i>
                                                    {{ $user->full_name }}
                                                    @if ($user->pivot->role == 'owner')
                                                        <span class="badge badge-primary">Owner</span>
                                                    @endif
                                                    @if ($user->pivot->is_primary_contact)
                                                        <span class="badge badge-info">Primary</span>
                                                    @endif
                                                </div>
                                            @empty
                                                <span class="text-muted">No users assigned</span>
                                            @endforelse
                                            @if ($org->users_count > 2)
                                                <small class="text-muted">+{{ $org->users_count - 2 }} more</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $org->industry ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            {{ $org->county ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $org->constituency ?? '' }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <small><i class="fas fa-briefcase"></i>
                                                    {{ $org->opportunities_count }}
                                                    Opportunities</small>
                                                <small><i class="fas fa-users"></i> {{ $org->placements_count }}
                                                    Placements</small>
                                                <small><i class="fas fa-user-friends"></i> {{ $org->users_count }}
                                                    Members</small>
                                            </div>
                                        </td>
                                        <td>
                                            <!-- Status Toggles -->
                                            <div class="d-flex flex-column gap-2">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="active_{{ $org->id }}"
                                                        wire:change="toggleStatus({{ $org->id }})"
                                                        {{ $org->is_active ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="active_{{ $org->id }}">
                                                        <span
                                                            class="badge {{ $org->is_active ? 'badge-success' : 'badge-secondary' }}">
                                                            {{ $org->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </label>
                                                </div>

                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="verified_{{ $org->id }}"
                                                        wire:change="toggleVerification({{ $org->id }})"
                                                        {{ $org->is_verified ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="verified_{{ $org->id }}">
                                                        <span
                                                            class="badge {{ $org->is_verified ? 'badge-success' : 'badge-warning' }}">
                                                            {{ $org->is_verified ? 'Verified' : 'Pending' }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>

                                            @if ($org->verified_at)
                                                <small class="text-muted d-block mt-1">
                                                    Verified: {{ $org->verified_at->format('d M Y') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('admin.organizations.show', $org->id) }}"
                                                    class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.organizations.edit', $org->id) }}"
                                                    class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <a href="" class="btn btn-sm btn-success"
                                                    title="View Opportunities">
                                                    <i class="fas fa-briefcase"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    wire:click="confirmDelete({{ $org->id }})" title="Delete"
                                                    {{ $org->users_count > 0 || $org->opportunities_count > 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                            <h5>No Organizations Found</h5>
                                            <p class="text-muted">
                                                @if ($search || $industry || $status || $verification || $county)
                                                    Try adjusting your filters or
                                                    <a href="#" wire:click="resetFilters">clear all filters</a>
                                                @else
                                                    Get started by creating your first organization
                                                @endif
                                            </p>
                                            @if (!$search && !$industry && !$status && !$verification && !$county)
                                                <a href="{{ route('admin.organizations.create') }}"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Add Organization
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer -->
                <div class="card-footer clearfix">
                    <div class="row">
                        <div class="col-sm-6">
                            <span class="text-muted">
                                Showing {{ $organizations->firstItem() }} to {{ $organizations->lastItem() }}
                                of {{ $organizations->total() }} entries
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-right">
                                {{ $organizations->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SweetAlert2 Confirmation -->
    @push('scripts')
        <script>
            Livewire.on('show-delete-confirmation', ({
                id
            }) => {
                Swal.fire({
                    title: 'Delete Organization?',
                    text: "This action cannot be undone. All related data will be permanently removed.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('deleteConfirmed', {
                            id: id
                        });
                    }
                });
            });

            Livewire.on('show-bulk-delete-confirmation', ({
                count
            }) => {
                Swal.fire({
                    title: 'Bulk Delete',
                    text: `Are you sure you want to delete ${count} selected organization(s)? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete them!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('bulkDeleteConfirmed');
                    }
                });
            });

            Livewire.on('toastr:success', ({
                message
            }) => {
                toastr.success(message, 'Success', {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 3000
                });
            });

            Livewire.on('toastr:error', ({
                message
            }) => {
                toastr.error(message, 'Error', {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 5000
                });
            });

            Livewire.on('toastr:warning', ({
                message
            }) => {
                toastr.warning(message, 'Warning', {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 4000
                });
            });
        </script>
    @endpush

    @push('styles')

    <style>
        
    </style>

    

    @endpush
</div>
