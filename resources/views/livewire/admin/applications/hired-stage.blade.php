<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    @push('styles')
        <style>
            .stats-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 15px;
                transition: transform 0.3s;
            }

            .stats-card:hover {
                transform: translateY(-5px);
            }

            .progress-circle {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 14px;
            }

            .status-badge {
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }

            .status-placed {
                background: #28a74520;
                color: #28a745;
            }

            .status-completed {
                background: #17a2b820;
                color: #17a2b8;
            }

            .status-cancelled {
                background: #dc354520;
                color: #dc3545;
            }

            .status-pending {
                background: #ffc10720;
                color: #ffc107;
            }
        </style>
    @endpush

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold">Hired/Placed Students</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.applications.index') }}">Applications</a>
                        </li>
                        <li class="breadcrumb-item active">Hired/Placed</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total_placed'] }}</h3>
                            <p>Total Placements</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['active_placements'] }}</h3>
                            <p>Active Placements</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['upcoming_placements'] }}</h3>
                            <p>Upcoming</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $stats['completed_placements'] }}</h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-flag-checkered"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $stats['cancelled_placements'] }}</h3>
                            <p>Cancelled</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $stats['total_organizations'] }}</h3>
                            <p>Organizations</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-filter mr-2 text-primary"></i> Filters
                        </h5>
                        <button class="btn btn-sm btn-outline-secondary" wire:click="resetFilters">
                            <i class="fas fa-undo-alt mr-1"></i> Reset Filters
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Status</label>
                            <select wire:model="statusFilter" class="form-control">
                                <option value="all">All Statuses</option>
                                <option value="placed">Active Placements</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Organization</label>
                            <select wire:model="organizationFilter" class="form-control">
                                <option value="">All Organizations</option>
                                @foreach ($organizations as $org)
                                    <option value="{{ $org->name }}">{{ $org->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Department</label>
                            <input type="text" wire:model="departmentFilter" class="form-control"
                                placeholder="Filter by department...">
                        </div>
                        <div class="col-md-3">
                            <label>Search</label>
                            <input type="text" wire:model="search" class="form-control"
                                placeholder="Search by student or organization...">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <label>Date From</label>
                            <input type="date" wire:model="dateFrom" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Date To</label>
                            <input type="date" wire:model="dateTo" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Sort By</label>
                            <select wire:model="sortBy" class="form-control">
                                <option value="created_at">Created Date</option>
                                <option value="student_name">Student Name</option>
                                <option value="organization_name">Organization</option>
                                <option value="start_date">Start Date</option>
                                <option value="end_date">End Date</option>
                                <option value="duration">Duration</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Sort Direction</label>
                            <select wire:model="sortDirection" class="form-control">
                                <option value="desc">Descending</option>
                                <option value="asc">Ascending</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="selectAll"
                                    wire:model="selectAll">
                                <label class="custom-control-label" for="selectAll">
                                    Select All ({{ $placements->total() }})
                                </label>
                            </div>
                        </div>
                        <div class="d-flex">
                            <select wire:model="bulkAction" class="form-control form-control-sm mr-2"
                                style="width: 200px;">
                                <option value="">Bulk Actions</option>
                                <option value="export">Export to CSV</option>
                                <option value="generate_reports">Generate Reports</option>
                            </select>
                            <button class="btn btn-sm btn-primary" wire:click="executeBulkAction">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Placements Table -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <th width="40">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="selectAllHeader"
                                            wire:model="selectAll">
                                        <label class="custom-control-label" for="selectAllHeader"></label>
                                    </div>
                                </th>
                                <th>Student</th>
                                <th>Organization</th>
                                <th>Placement Details</th>
                                <th>Supervisor</th>
                                <th>Duration</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($placements as $placement)
                                    @php
                                        $progress = $this->getProgressPercentage($placement);
                                        $startDate = \Carbon\Carbon::parse($placement->start_date);
                                        $endDate = \Carbon\Carbon::parse($placement->end_date);
                                        $today = now();
                                        $isActive =
                                            $placement->status === 'placed' && $today->between($startDate, $endDate);
                                        $isUpcoming = $placement->status === 'placed' && $today->lt($startDate);
                                        $isOverdue = $placement->status === 'placed' && $today->gt($endDate);
                                    @endphp
                                    <tr wire:key="{{ $placement->id }}"
                                        class="{{ $isOverdue ? 'bg-warning-soft' : '' }}">
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="placement_{{ $placement->id }}" value="{{ $placement->id }}"
                                                    wire:model="selectedApplications">
                                                <label class="custom-control-label"
                                                    for="placement_{{ $placement->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary-soft mr-2"
                                                    style="width: 40px; height: 40px; line-height: 40px;">
                                                    {{ substr($placement->student->full_name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $placement->student->full_name }}</strong><br>
                                                    <small class="text-muted">{{ $placement->student->email }}</small>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-graduation-cap"></i>
                                                        {{ $placement->student->studentProfile?->course_name ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $placement->organization->name }}</strong><br>
                                            @if ($placement->department)
                                                <small class="text-muted">
                                                    <i class="fas fa-building"></i> {{ $placement->department }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($placement->opportunity)
                                                <strong>{{ Str::limit($placement->opportunity->title, 30) }}</strong>
                                                <br>
                                            @endif
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt"></i>
                                                {{ $startDate->format('d M Y') }} -
                                                {{ $endDate->format('d M Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if ($placement->supervisor_name)
                                                <strong>{{ $placement->supervisor_name }}</strong><br>
                                                <small class="text-muted">
                                                    <i class="fas fa-phone"></i>
                                                    {{ $placement->supervisor_contact ?? 'N/A' }}
                                                </small>
                                            @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $startDate->diffInMonths($endDate) }} months</strong>
                                            <br>
                                            <small class="text-muted">{{ $placement->duration_days }} days</small>
                                        </td>
                                        <td>
                                            @if ($placement->status === 'placed')
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 mr-2" style="height: 8px;">
                                                        <div class="progress-bar progress-bar-striped {{ $progress == 100 ? 'bg-success' : ($isActive ? 'bg-info progress-bar-animated' : 'bg-warning') }}"
                                                            style="width: {{ $progress }}%"></div>
                                                    </div>
                                                    <span class="small font-weight-bold">{{ $progress }}%</span>
                                                </div>
                                                @if ($isUpcoming)
                                                    <small class="text-warning">Starts in
                                                        {{ $startDate->diffForHumans() }}</small>
                                                @elseif($isOverdue)
                                                    <small class="text-danger">Overdue by
                                                        {{ $endDate->diffForHumans() }}</small>
                                                @else
                                                    <small class="text-success">{{ $endDate->diffForHumans() }}
                                                        remaining</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($placement->status === 'placed')
                                                @if ($isActive)
                                                    <span class="status-badge status-placed">
                                                        <i class="fas fa-play-circle mr-1"></i> Active
                                                    </span>
                                                @elseif($isUpcoming)
                                                    <span class="status-badge status-pending">
                                                        <i class="fas fa-clock mr-1"></i> Upcoming
                                                    </span>
                                                @else
                                                    <span class="status-badge status-placed">
                                                        <i class="fas fa-pause-circle mr-1"></i> Placed
                                                    </span>
                                                @endif
                                            @elseif($placement->status === 'completed')
                                                <span class="status-badge status-completed">
                                                    <i class="fas fa-check-circle mr-1"></i> Completed
                                                </span>
                                            @elseif($placement->status === 'cancelled')
                                                <span class="status-badge status-cancelled">
                                                    <i class="fas fa-ban mr-1"></i> Cancelled
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-info"
                                                    wire:click="viewPlacement({{ $placement->id }})"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if ($placement->status === 'placed')
                                                    <button class="btn btn-sm btn-outline-primary"
                                                        wire:click="openUpdatePlacementModal({{ $placement->id }})"
                                                        title="Edit Placement">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success"
                                                        wire:click="openCompleteModal({{ $placement->id }})"
                                                        title="Mark as Completed">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        wire:click="openCancelModal({{ $placement->id }})"
                                                        title="Cancel Placement">
                                                        <i class="fas fa-times-circle"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No placements found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer bg-white border-0">
                        {{ $placements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Create Placement Modal -->
    @if ($showPlacementModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-briefcase mr-2"></i> Create Placement
                        </h5>
                        <button type="button" class="close text-white"
                            wire:click="$set('showPlacementModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Date <span class="text-danger">*</span></label>
                                    <input type="date" wire:model="placementStartDate" class="form-control">
                                    @error('placementStartDate')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Date <span class="text-danger">*</span></label>
                                    <input type="date" wire:model="placementEndDate" class="form-control">
                                    @error('placementEndDate')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Department</label>
                                    <input type="text" wire:model="placementDepartment" class="form-control"
                                        placeholder="e.g., IT Department">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Supervisor Name</label>
                                    <input type="text" wire:model="placementSupervisorName" class="form-control"
                                        placeholder="Supervisor's full name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Supervisor Contact</label>
                                    <input type="text" wire:model="placementSupervisorContact"
                                        class="form-control" placeholder="Phone or email">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Requirements</label>
                                    <div class="input-group mb-2">
                                        <input type="text" wire:model="newRequirement" class="form-control"
                                            placeholder="Add requirement..." wire:keydown.enter="addRequirement">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-primary" type="button"
                                                wire:click="addRequirement">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap">
                                        @foreach ($placementRequirements as $index => $req)
                                            <span class="badge badge-info p-2 mr-2 mb-2">
                                                {{ $req }}
                                                <i class="fas fa-times ml-2" style="cursor: pointer;"
                                                    wire:click="removeRequirement({{ $index }})"></i>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea wire:model="placementNotes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary"
                            wire:click="$set('showPlacementModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-success" wire:click="createPlacement"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fas fa-save mr-1"></i> Create Placement</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin mr-1"></i> Creating...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- View Placement Modal -->
    @if ($showPlacementViewModal && $selectedPlacement)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-info text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-briefcase mr-2"></i> Placement Details
                        </h5>
                        <button type="button" class="close text-white"
                            wire:click="$set('showPlacementViewModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Student Information</h6>
                                <p>
                                    <strong>{{ $selectedPlacement->student->full_name }}</strong><br>
                                    {{ $selectedPlacement->student->email }}<br>
                                    {{ $selectedPlacement->student->phone ?? 'N/A' }}
                                </p>
                                @if ($selectedPlacement->student->studentProfile)
                                    <small class="text-muted">
                                        <i class="fas fa-graduation-cap"></i>
                                        {{ $selectedPlacement->student->studentProfile->course_name }}<br>
                                        <i class="fas fa-university"></i>
                                        {{ $selectedPlacement->student->studentProfile->institution_name }}
                                    </small>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Organization Details</h6>
                                <p>
                                    <strong>{{ $selectedPlacement->organization->name }}</strong><br>
                                    @if ($selectedPlacement->department)
                                        Department: {{ $selectedPlacement->department }}<br>
                                    @endif
                                    @if ($selectedPlacement->supervisor_name)
                                        Supervisor: {{ $selectedPlacement->supervisor_name }}<br>
                                        Contact: {{ $selectedPlacement->supervisor_contact ?? 'N/A' }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-12">
                                <hr>
                                <h6 class="font-weight-bold">Placement Details</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">Start Date</small>
                                        <p><strong>{{ $selectedPlacement->start_date->format('d M Y') }}</strong>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">End Date</small>
                                        <p><strong>{{ $selectedPlacement->end_date->format('d M Y') }}</strong></p>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Duration</small>
                                        <p><strong>{{ $selectedPlacement->start_date->diffInMonths($selectedPlacement->end_date) }}
                                                months</strong></p>
                                    </div>
                                </div>

                                @if ($selectedPlacement->requirements && count($selectedPlacement->requirements) > 0)
                                    <div class="mt-3">
                                        <small class="text-muted">Requirements</small>
                                        <div class="d-flex flex-wrap mt-1">
                                            @foreach ($selectedPlacement->requirements as $req)
                                                <span
                                                    class="badge badge-info p-2 mr-2 mb-2">{{ $req }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if ($selectedPlacement->notes)
                                    <div class="mt-3">
                                        <small class="text-muted">Notes</small>
                                        <p class="bg-light p-2 rounded">{{ $selectedPlacement->notes }}</p>
                                    </div>
                                @endif

                                @if ($selectedPlacement->metadata && isset($selectedPlacement->metadata['completion_feedback']))
                                    <div class="mt-3">
                                        <small class="text-muted">Completion Feedback</small>
                                        <div class="bg-light p-2 rounded">
                                            <p class="mb-1">
                                                {{ $selectedPlacement->metadata['completion_feedback'] }}</p>
                                            <small class="text-muted">
                                                Rating:
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fas fa-star {{ $i <= ($selectedPlacement->metadata['completion_rating'] ?? 0) ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showPlacementViewModal', false)">
                            Close
                        </button>
                        @if ($selectedPlacement->status === 'placed')
                            <button class="btn btn-primary"
                                wire:click="openUpdatePlacementModal({{ $selectedPlacement->id }})"
                                wire:click="$set('showPlacementViewModal', false)">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Update Placement Modal -->
    @if ($showUpdatePlacementModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-edit mr-2"></i> Update Placement
                        </h5>
                        <button type="button" class="close text-white"
                            wire:click="$set('showUpdatePlacementModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" wire:model="updateStartDate" class="form-control">
                            @error('updateStartDate')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" wire:model="updateEndDate" class="form-control">
                            @error('updateEndDate')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select wire:model="updateStatus" class="form-control">
                                <option value="placed">Placed (Active)</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea wire:model="updateNotes" class="form-control" rows="3" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary"
                            wire:click="$set('showUpdatePlacementModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="updatePlacement">
                            <i class="fas fa-save mr-1"></i> Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Complete Placement Modal -->
    @if ($showCompleteModal && $selectedPlacementForComplete)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle mr-2"></i> Complete Placement
                        </h5>
                        <button type="button" class="close text-white"
                            wire:click="$set('showCompleteModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            You are about to mark this placement as completed for
                            <strong>{{ $selectedPlacementForComplete->student->full_name }}</strong>.
                        </div>
                        <div class="form-group">
                            <label>Rating (1-5)</label>
                            <div class="rating-input">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star fa-2x {{ $i <= $completionRating ? 'text-warning' : 'text-muted' }}"
                                        style="cursor: pointer;"
                                        wire:click="$set('completionRating', {{ $i }})"></i>
                                @endfor
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Feedback / Comments</label>
                            <textarea wire:model="completionFeedback" class="form-control" rows="3"
                                placeholder="Provide feedback about the student's performance..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Additional Notes</label>
                            <textarea wire:model="completionNotes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary"
                            wire:click="$set('showCompleteModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-success" wire:click="completePlacement">
                            <i class="fas fa-check-circle mr-1"></i> Mark as Completed
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Cancel Placement Modal -->
    @if ($showCancelModal && $selectedPlacementForCancel)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-ban mr-2"></i> Cancel Placement
                        </h5>
                        <button type="button" class="close text-white" wire:click="$set('showCancelModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            You are about to cancel the placement for
                            <strong>{{ $selectedPlacementForCancel->student->full_name }}</strong>.
                            This action will remove the student from this placement.
                        </div>
                        <div class="form-group">
                            <label>Reason for Cancellation <span class="text-danger">*</span></label>
                            <textarea wire:model="cancelReason" class="form-control" rows="3"
                                placeholder="Please provide a reason for cancellation..."></textarea>
                            @error('cancelReason')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary"
                            wire:click="$set('showCancelModal', false)">
                            Keep Placement
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="cancelPlacement">
                            <i class="fas fa-trash mr-1"></i> Cancel Placement
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
