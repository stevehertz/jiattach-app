<div>
    {{-- Stop trying to control. --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Employers Management</h3>
            <div class="card-tools">
                <a href="{{ route('admin.employers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Employer
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="Search employers...">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <select class="form-control" wire:model.live="filterVerified">
                        <option value="">All Status</option>
                        <option value="yes">Verified</option>
                        <option value="no">Unverified</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select class="form-control" wire:model.live="filterActive">
                        <option value="">All Active</option>
                        <option value="yes">Active</option>
                        <option value="no">Inactive</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select class="form-control" wire:model.live="filterCounty">
                        <option value="">All Counties</option>
                        @foreach($counties as $county)
                            <option value="{{ $county }}">{{ $county }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select class="form-control" wire:model.live="perPage">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <button class="btn btn-secondary btn-block" wire:click="clearFilters">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employer</th>
                            <th>Organization</th>
                            <th>Role</th>
                            <th>Contact</th>
                            <th>County</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employers as $employer)
                            <tr>
                                <td>{{ $loop->iteration + ($employers->currentPage() - 1) * $employers->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            @if($employer->profile_photo_path)
                                                <img src="{{ $employer->profile_photo_url }}" 
                                                     alt="{{ $employer->full_name }}"
                                                     class="img-circle elevation-2"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="avatar-initials bg-info img-circle elevation-2 d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px; color: white; font-weight: bold; font-size: 14px;">
                                                    {{ $employer->initials() }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>
                                                <a href="{{ route('admin.employers.show', $employer) }}" 
                                                   class="text-decoration-none">
                                                    {{ $employer->full_name }}
                                                </a>
                                            </strong>
                                            <br>
                                            <small class="text-muted">{{ $employer->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($employer->organizations->isNotEmpty())
                                        @foreach($employer->organizations->take(2) as $org)
                                            <span class="badge badge-success mr-1">
                                                {{ $org->name }}
                                            </span>
                                        @endforeach
                                        @if($employer->organizations->count() > 2)
                                            <span class="badge badge-info">
                                                +{{ $employer->organizations->count() - 2 }} more
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">No organization</span>
                                    @endif
                                </td>
                                <td>
                                    @if($employer->organizations->isNotEmpty())
                                        @php
                                            $primaryOrg = $employer->organizations->first();
                                            $role = $primaryOrg->pivot->role ?? 'member';
                                        @endphp
                                        <span class="badge badge-{{ $role === 'owner' ? 'primary' : ($role === 'admin' ? 'info' : 'secondary') }}">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($employer->phone)
                                        <div><i class="fas fa-phone mr-1"></i>{{ $employer->phone }}</div>
                                    @endif
                                    @if($employer->email)
                                        <div><i class="fas fa-envelope mr-1"></i><small>{{ $employer->email }}</small></div>
                                    @endif
                                </td>
                                <td>
                                    @if($employer->county)
                                        <span class="badge badge-outline-info">
                                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $employer->county }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $employer->is_active ? 'success' : 'danger' }}">
                                        {{ $employer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    @if($employer->is_verified)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle mr-1"></i> Verified
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock mr-1"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.employers.show', $employer) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.employers.edit', $employer) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if(!$employer->is_verified)
                                            <button class="btn btn-sm btn-success" 
                                                    wire:click="verifyEmployer({{ $employer->id }})"
                                                    title="Verify">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        
                                        <button class="btn btn-sm btn-{{ $employer->is_active ? 'secondary' : 'primary' }}" 
                                                wire:click="toggleActive({{ $employer->id }})"
                                                title="{{ $employer->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $employer->is_active ? 'ban' : 'check-circle' }}"></i>
                                        </button>
                                        
                                        <button class="btn btn-sm btn-danger" 
                                                wire:click="confirmDelete({{ $employer->id }})"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-user-tie fa-3x mb-3"></i>
                                        <p>No employers found</p>
                                        @if($search || $filterVerified || $filterActive || $filterCounty)
                                            <button class="btn btn-outline-secondary btn-sm" wire:click="clearFilters">
                                                <i class="fas fa-times mr-1"></i> Clear Filters
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-muted small">
                            <strong>Showing {{ $employers->firstItem() ?? 0 }} to {{ $employers->lastItem() ?? 0 }} 
                            of {{ $employers->total() }} employers</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            {{ $employers->links('vendor.pagination.adminlte') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Script -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('confirm-delete', (data) => {
                Swal.fire({
                    title: 'Delete Employer?',
                    text: data.message || 'Are you sure you want to delete this employer?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.deleteEmployer(data.employerId);
                    }
                });
            });

            Livewire.on('notify', (data) => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });

                Toast.fire({
                    icon: data.type || 'success',
                    title: data.message || 'Action completed'
                });
            });
        });
    </script>
</div>
