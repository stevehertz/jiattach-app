<div>
    <!-- Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        @if ($viewType === 'active')
                            Active Placements
                        @elseif($viewType === 'upcoming')
                            Upcoming Placements
                        @elseif($viewType === 'completed')
                            Completed Placements
                        @elseif($viewType === 'cancelled')
                            Cancelled Placements
                        @elseif($viewType === 'pending')
                            Pending Placements
                        @else
                            All Placements
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.placements.index') }}">Placements</a></li>
                        <li class="breadcrumb-item active">
                            @if ($viewType === 'active')
                                Active
                            @elseif($viewType === 'upcoming')
                                Upcoming
                            @elseif($viewType === 'completed')
                                Completed
                            @elseif($viewType === 'cancelled')
                                Cancelled
                            @elseif($viewType === 'pending')
                                Pending
                            @else
                                All
                            @endif
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1">
                            <i class="fas fa-briefcase"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total</span>
                            <span class="info-box-number">{{ $stats['total'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1">
                            <i class="fas fa-user-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active</span>
                            <span class="info-box-number">{{ $stats['active'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning elevation-1">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Upcoming</span>
                            <span class="info-box-number">{{ $stats['upcoming'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary elevation-1">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Completed</span>
                            <span class="info-box-number">{{ $stats['completed'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary elevation-1">
                            <i class="fas fa-hourglass-half"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending</span>
                            <span class="info-box-number">{{ $stats['pending'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger elevation-1">
                            <i class="fas fa-times-circle"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Cancelled</span>
                            <span class="info-box-number">{{ $stats['cancelled'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats Row -->
            <div class="row">
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total_students'] }}</h3>
                            <p>Unique Students Placed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['total_organizations'] }}</h3>
                            <p>Partner Organizations</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($stats['avg_duration'] ?? 0, 1) }}</h3>
                            <p>Avg. Duration (Days)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>KES {{ number_format($stats['avg_stipend'] ?? 0) }}</h3>
                            <p>Avg. Monthly Stipend</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions Bar -->
            @if ($selectedPlacements && count($selectedPlacements) > 0)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">
                                            <i class="fas fa-check-square mr-2"></i>
                                            {{ count($selectedPlacements) }} placement(s) selected
                                        </h5>
                                        <small class="text-muted">Choose an action to perform on selected items</small>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success"
                                            wire:click="$set('bulkAction', 'complete')"
                                            wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-check-circle mr-1"></i> Mark Complete
                                        </button>
                                        <button type="button" class="btn btn-warning"
                                            wire:click="$set('bulkAction', 'extend')"
                                            wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-clock mr-1"></i> Extend Duration
                                        </button>
                                        <button type="button" class="btn btn-info"
                                            wire:click="$set('bulkAction', 'send_reminder')"
                                            wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-bell mr-1"></i> Send Reminder
                                        </button>
                                        <button type="button" class="btn btn-danger"
                                            wire:click="$set('bulkAction', 'cancel')"
                                            wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-times-circle mr-1"></i> Cancel
                                        </button>
                                        <button type="button" class="btn btn-primary"
                                            wire:click="$set('bulkAction', 'generate_reports')"
                                            wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-file-pdf mr-1"></i> Generate Reports
                                        </button>
                                        <button type="button" class="btn btn-secondary"
                                            wire:click="$set('bulkAction', 'export')"
                                            wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-download mr-1"></i> Export
                                        </button>
                                        <button type="button" class="btn btn-default"
                                            wire:click="$set('selectedPlacements', [])">
                                            <i class="fas fa-times mr-1"></i> Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        @if ($viewType === 'active')
                            Active Placements ({{ $stats['active'] }})
                        @elseif($viewType === 'upcoming')
                            Upcoming Placements ({{ $stats['upcoming'] }})
                        @elseif($viewType === 'completed')
                            Completed Placements ({{ $stats['completed'] }})
                        @elseif($viewType === 'cancelled')
                            Cancelled Placements ({{ $stats['cancelled'] }})
                        @elseif($viewType === 'pending')
                            Pending Placements ({{ $stats['pending'] }})
                        @else
                            All Placements ({{ $stats['total'] }})
                        @endif
                    </h3>

                    <div class="card-tools">
                        <div class="btn-group mr-2">
                            <a href="{{ route('admin.placements.index') }}"
                                class="btn btn-sm btn-outline-secondary {{ $viewType === 'all' ? 'active' : '' }}">
                                All
                            </a>
                            <a href="{{ route('admin.placements.active') }}"
                                class="btn btn-sm btn-outline-success {{ $viewType === 'active' ? 'active' : '' }}">
                                <i class="fas fa-user-check mr-1"></i> Active
                                @if ($stats['active'] > 0)
                                    <span class="badge badge-success ml-1">{{ $stats['active'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.placements.upcoming') }}"
                                class="btn btn-sm btn-outline-warning {{ $viewType === 'upcoming' ? 'active' : '' }}">
                                <i class="fas fa-clock mr-1"></i> Upcoming
                                @if ($stats['upcoming'] > 0)
                                    <span class="badge badge-warning ml-1">{{ $stats['upcoming'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.placements.completed') }}"
                                class="btn btn-sm btn-outline-info {{ $viewType === 'completed' ? 'active' : '' }}">
                                <i class="fas fa-check-circle mr-1"></i> Completed
                                @if ($stats['completed'] > 0)
                                    <span class="badge badge-info ml-1">{{ $stats['completed'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.placements.pending') }}"
                                class="btn btn-sm btn-outline-primary {{ $viewType === 'pending' ? 'active' : '' }}">
                                <i class="fas fa-hourglass-half mr-1"></i> Pending
                                @if ($stats['pending'] > 0)
                                    <span class="badge badge-primary ml-1">{{ $stats['pending'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.placements.cancelled') }}"
                                class="btn btn-sm btn-outline-danger {{ $viewType === 'cancelled' ? 'active' : '' }}">
                                <i class="fas fa-times-circle mr-1"></i> Cancelled
                                @if ($stats['cancelled'] > 0)
                                    <span class="badge badge-danger ml-1">{{ $stats['cancelled'] }}</span>
                                @endif
                            </a>
                        </div>

                        <div class="input-group input-group-sm" style="width: 300px;">
                            <input type="text" wire:model.live.debounce.500ms="search"
                                class="form-control float-right" placeholder="Search placements...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" wire:click="$set('search', '')"
                                    title="Clear search">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button type="button" class="btn btn-default" wire:click="$toggle('showFilters')">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Filters -->
                <div class="card-body border-bottom @if (!$showFilters) d-none @endif"
                    id="filterSection">
                    <div class="row">
                        <!-- Basic Filters -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select wire:model.live="statusFilter" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="placed">Placed</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Organization</label>
                                <select wire:model.live="organizationFilter" class="form-control">
                                    <option value="">All Organizations</option>
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Opportunity</label>
                                <select wire:model.live="opportunityFilter" class="form-control">
                                    <option value="">All Opportunities</option>
                                    @foreach ($opportunities as $opp)
                                        <option value="{{ $opp->id }}">{{ $opp->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Student</label>
                                <select wire:model.live="studentFilter" class="form-control">
                                    <option value="">All Students</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date From</label>
                                <input type="date" wire:model.live="dateFrom" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>End Date To</label>
                                <input type="date" wire:model.live="dateTo" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Department</label>
                                <select wire:model.live="departmentFilter" class="form-control">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept }}">{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Supervisor</label>
                                <input type="text" wire:model.live="supervisorFilter" class="form-control"
                                    placeholder="Supervisor name">
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Filters Toggle -->
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-link btn-sm" data-toggle="collapse"
                                data-target="#advancedFilters">
                                <i class="fas fa-chevron-down mr-1"></i> Advanced Filters
                            </button>
                        </div>
                    </div>

                    <!-- Advanced Filters Content -->
                    <div class="row collapse" id="advancedFilters">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Min Stipend (KES)</label>
                                <input type="number" wire:model.live="minStipend" class="form-control"
                                    placeholder="e.g., 10000">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Max Stipend (KES)</label>
                                <input type="number" wire:model.live="maxStipend" class="form-control"
                                    placeholder="e.g., 50000">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Min Duration (Days)</label>
                                <input type="number" wire:model.live="durationMin" class="form-control"
                                    placeholder="e.g., 30">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Max Duration (Days)</label>
                                <input type="number" wire:model.live="durationMax" class="form-control"
                                    placeholder="e.g., 90">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sort By</label>
                                <select wire:model.live="sortField" class="form-control">
                                    <option value="created_at">Date Created</option>
                                    <option value="start_date">Start Date</option>
                                    <option value="end_date">End Date</option>
                                    <option value="stipend">Stipend</option>
                                    <option value="duration_days">Duration</option>
                                    <option value="placement_confirmed_at">Confirmed Date</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sort Direction</label>
                                <select wire:model.live="sortDirection" class="form-control">
                                    <option value="desc">Newest First</option>
                                    <option value="asc">Oldest First</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Results Per Page</label>
                                <select wire:model.live="perPage" class="form-control">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="button" class="btn btn-sm btn-primary"
                                        wire:click="applyBulkAction"
                                        {{ count($selectedPlacements) === 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-bolt mr-1"></i> Apply to Selected
                                        ({{ count($selectedPlacements) }})
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-default" wire:click="resetFilters">
                                        <i class="fas fa-redo mr-1"></i> Reset All Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="selectAll"
                                                wire:model.live="selectAll">
                                            <label class="custom-control-label" for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('id')" style="cursor: pointer;">
                                        ID
                                        @if ($sortField === 'id')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </th>
                                    <th>Student</th>
                                    <th>Organization</th>
                                    <th>Opportunity</th>
                                    <th>Department/Supervisor</th>
                                    <th wire:click="sortBy('start_date')" style="cursor: pointer;">
                                        Duration
                                        @if ($sortField === 'start_date')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </th>
                                    <th>Progress</th>
                                    <th wire:click="sortBy('stipend')" style="cursor: pointer;">
                                        Stipend
                                        @if ($sortField === 'stipend')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('status')" style="cursor: pointer;">
                                        Status
                                        @if ($sortField === 'status')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($placements as $placement)
                                    <tr wire:key="placement-{{ $placement->id }}"
                                        class="{{ $placement->status === 'cancelled' ? 'table-danger' : ($placement->status === 'completed' ? 'table-secondary' : '') }}">
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="placement-{{ $placement->id }}"
                                                    value="{{ $placement->id }}" wire:model.live="selectedPlacements"
                                                    wire:key="checkbox-{{ $placement->id }}">
                                                <label class="custom-control-label"
                                                    for="placement-{{ $placement->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>#{{ $placement->id }}</strong>
                                            @if($placement->application_id)
                                                <div>
                                                    <small class="text-muted">App: #{{ $placement->application_id }}</small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    @php
                                                        $initials = $placement->student ? getInitials($placement->student->full_name) : 'N/A';
                                                        $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                                                        $color = $placement->student ? $colors[crc32($placement->student->email) % count($colors)] : 'secondary';
                                                    @endphp
                                                    <div class="avatar-initials bg-{{ $color }} img-circle"
                                                        style="width: 40px; height: 40px; line-height: 40px; text-align: center; color: white; font-weight: bold; font-size: 14px;">
                                                        {{ $initials }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>{{ $placement->student?->full_name ?? 'N/A' }}</strong>
                                                    <div class="text-muted small">{{ $placement->student?->email ?? 'No email' }}</div>
                                                    @if($placement->student?->studentProfile)
                                                        <div class="text-muted small">
                                                            {{ $placement->student->studentProfile->course_name ?? 'No course' }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $placement->organization?->name ?? 'N/A' }}</strong>
                                            @if($placement->organization?->is_verified)
                                                <i class="fas fa-check-circle text-success ml-1" title="Verified"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $placement->opportunity?->title ?? 'N/A' }}</strong>
                                            <div class="text-muted small">
                                                {{ $placement->opportunity?->type ?? 'N/A' }} -
                                                {{ $placement->opportunity?->work_type ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <strong>Dept:</strong> {{ $placement->department ?? 'N/A' }}<br>
                                                <strong>Sup:</strong> {{ $placement->supervisor_name ?? 'N/A' }}
                                                @if($placement->supervisor_contact)
                                                    <br><small>{{ $placement->supervisor_contact }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <strong>Start:</strong> {{ $placement->start_date?->format('M d, Y') ?? 'N/A' }}<br>
                                                <strong>End:</strong> {{ $placement->end_date?->format('M d, Y') ?? 'N/A' }}
                                                @if($placement->duration_days)
                                                    <br><span class="badge badge-info">{{ $placement->duration_days }} days</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($placement->status === 'placed')
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $placement->progress_percentage >= 75 ? 'success' : ($placement->progress_percentage >= 50 ? 'info' : ($placement->progress_percentage >= 25 ? 'warning' : 'primary')) }}"
                                                        style="width: {{ $placement->progress_percentage }}%">
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $placement->progress_percentage }}% complete
                                                </small>
                                                @if($placement->is_active)
                                                    <br><small class="text-success">
                                                        <i class="fas fa-clock mr-1"></i>{{ $placement->remaining_days }} days left
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($placement->stipend)
                                                <strong>KES {{ number_format($placement->stipend, 2) }}</strong>
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'secondary',
                                                    'processing' => 'info',
                                                    'placed' => 'success',
                                                    'completed' => 'primary',
                                                    'cancelled' => 'danger',
                                                ];
                                                $color = $statusColors[$placement->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $color }} p-2">
                                                {{ $placement->status_label }}
                                            </span>
                                            @if($placement->placement_confirmed_at)
                                                <div class="text-muted small mt-1">
                                                    Confirmed: {{ $placement->placement_confirmed_at->format('M d, Y') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info"
                                                    wire:click="viewPlacement({{ $placement->id }})"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-primary"
                                                    wire:click="editPlacement({{ $placement->id }})"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- Quick Actions Dropdown -->
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="fas fa-bolt"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        @if($placement->status === 'placed')
                                                            <button class="dropdown-item text-success"
                                                                wire:click="generateReport({{ $placement->id }})">
                                                                <i class="fas fa-file-pdf mr-2"></i> Generate Report
                                                            </button>
                                                            <button class="dropdown-item text-warning"
                                                                wire:click="$set('bulkAction', 'extend')"
                                                                wire:click="$set('showBulkActionModal', true)">
                                                                <i class="fas fa-clock mr-2"></i> Extend Duration
                                                            </button>
                                                            <button class="dropdown-item text-info"
                                                                wire:click="sendReminder({{ $placement->id }})">
                                                                <i class="fas fa-bell mr-2"></i> Send Reminder
                                                            </button>
                                                            <div class="dropdown-divider"></div>
                                                            <button class="dropdown-item text-success"
                                                                wire:click="transitionStatus({{ $placement->id }}, 'completed')"
                                                                wire:confirm="Mark this placement as completed?">
                                                                <i class="fas fa-check-circle mr-2"></i> Mark Completed
                                                            </button>
                                                        @endif

                                                        @if(in_array($placement->status, ['pending', 'processing']))
                                                            <button class="dropdown-item text-success"
                                                                wire:click="transitionStatus({{ $placement->id }}, 'placed')"
                                                                wire:confirm="Activate this placement?">
                                                                <i class="fas fa-play mr-2"></i> Activate
                                                            </button>
                                                        @endif

                                                        @if(in_array($placement->status, ['pending', 'processing', 'placed']))
                                                            <button class="dropdown-item text-danger"
                                                                wire:click="transitionStatus({{ $placement->id }}, 'cancelled')"
                                                                wire:confirm="Cancel this placement?">
                                                                <i class="fas fa-times-circle mr-2"></i> Cancel
                                                            </button>
                                                        @endif

                                                        <div class="dropdown-divider"></div>

                                                        <button class="dropdown-item text-danger"
                                                            wire:click="deletePlacement({{ $placement->id }})"
                                                            wire:confirm="Are you sure you want to delete this placement? This action cannot be undone.">
                                                            <i class="fas fa-trash mr-2"></i> Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-5">
                                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No placements found</h5>
                                            @if ($search || $statusFilter || $organizationFilter || $opportunityFilter || $studentFilter || $dateFrom || $dateTo || $departmentFilter || $supervisorFilter || $minStipend || $maxStipend)
                                                <p class="text-muted">Try adjusting your search or filters</p>
                                                <button wire:click="resetFilters" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-redo mr-1"></i> Reset Filters
                                                </button>
                                            @else
                                                <p class="text-muted">No placements have been created yet.</p>
                                                <a href="{{ route('admin.placements.create') }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus mr-1"></i> Create New Placement
                                                </a>
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
                                Showing {{ $placements->firstItem() ?? 0 }} to {{ $placements->lastItem() ?? 0 }}
                                of {{ $placements->total() }} entries
                            </span>
                            @if (count($selectedPlacements) > 0)
                                <span class="badge badge-primary ml-2">
                                    {{ count($selectedPlacements) }} selected
                                </span>
                            @endif
                        </div>
                        <div>
                            @if ($placements->hasPages())
                                {{ $placements->links() }}
                            @endif
                        </div>
                        <div>
                            <button type="button" class="btn btn-default" wire:click="$toggle('showFilters')">
                                <i class="fas fa-filter mr-1"></i>
                                {{ $showFilters ? 'Hide Filters' : 'Show Filters' }}
                            </button>
                            <a href="{{ route('admin.placements.analytics') }}" class="btn btn-dark ml-2">
                                <i class="fas fa-chart-line mr-1"></i> Analytics
                            </a>
                            <a href="{{ route('admin.placements.create') }}" class="btn btn-success ml-2">
                                <i class="fas fa-plus mr-1"></i> New Placement
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Action Modal -->
    @if ($showBulkActionModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if ($bulkAction === 'complete')
                                <i class="fas fa-check-circle text-success mr-2"></i> Complete Placements
                            @elseif($bulkAction === 'cancel')
                                <i class="fas fa-times-circle text-danger mr-2"></i> Cancel Placements
                            @elseif($bulkAction === 'extend')
                                <i class="fas fa-clock text-warning mr-2"></i> Extend Placements
                            @elseif($bulkAction === 'send_reminder')
                                <i class="fas fa-bell text-info mr-2"></i> Send Reminders
                            @elseif($bulkAction === 'generate_reports')
                                <i class="fas fa-file-pdf text-primary mr-2"></i> Generate Reports
                            @elseif($bulkAction === 'export')
                                <i class="fas fa-download text-secondary mr-2"></i> Export Placements
                            @endif
                        </h5>
                        <button type="button" class="close" wire:click="$set('showBulkActionModal', false)">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if ($bulkAction === 'complete')
                            <p>Are you sure you want to mark <strong>{{ count($selectedPlacements) }}</strong>
                                selected placement(s) as completed?</p>
                            <p class="text-muted">This will update the status and record completion dates.</p>
                        @elseif($bulkAction === 'cancel')
                            <p>Are you sure you want to cancel <strong>{{ count($selectedPlacements) }}</strong>
                                selected placement(s)?</p>
                            <p class="text-muted">This will update student profiles to seeking status again.</p>
                        @elseif($bulkAction === 'extend')
                            <p>You are about to extend the duration for <strong>{{ count($selectedPlacements) }}</strong>
                                selected placement(s).</p>
                            <p class="text-muted">You will be redirected to the bulk extension page.</p>
                        @elseif($bulkAction === 'send_reminder')
                            <p>Send reminders for <strong>{{ count($selectedPlacements) }}</strong>
                                selected placement(s).</p>
                            <p class="text-muted">Reminders will be sent to both students and supervisors.</p>
                        @elseif($bulkAction === 'generate_reports')
                            <p>Generate reports for <strong>{{ count($selectedPlacements) }}</strong>
                                selected placement(s).</p>
                            <p class="text-muted">You will be redirected to the report generation page.</p>
                        @elseif($bulkAction === 'export')
                            <p>You are about to export <strong>{{ count($selectedPlacements) }}</strong>
                                selected placement(s).</p>
                            <p class="text-muted">The data will be downloaded as a CSV file.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                            wire:click="$set('showBulkActionModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="applyBulkAction">
                            @if ($bulkAction === 'complete')
                                <i class="fas fa-check mr-1"></i> Yes, Complete
                            @elseif($bulkAction === 'cancel')
                                <i class="fas fa-times mr-1"></i> Yes, Cancel
                            @elseif($bulkAction === 'extend')
                                <i class="fas fa-clock mr-1"></i> Continue to Extend
                            @elseif($bulkAction === 'send_reminder')
                                <i class="fas fa-bell mr-1"></i> Send Reminders
                            @elseif($bulkAction === 'generate_reports')
                                <i class="fas fa-file-pdf mr-1"></i> Continue to Reports
                            @elseif($bulkAction === 'export')
                                <i class="fas fa-download mr-1"></i> Export CSV
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    @push('styles')
        <style>
            .avatar-initials {
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
            }
            .table-hover tbody tr:hover {
                background-color: rgba(0, 123, 255, 0.05);
            }
            .table-secondary {
                opacity: 0.7;
            }
            .table-danger {
                opacity: 0.8;
            }
            #advancedFilters {
                transition: all 0.3s ease;
            }
            .progress {
                margin-top: 5px;
                margin-bottom: 5px;
            }
            .small-box {
                margin-bottom: 15px;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Initialize collapse for advanced filters
                $('#advancedFilters').on('show.bs.collapse', function () {
                    $('[data-target="#advancedFilters"] i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                }).on('hide.bs.collapse', function () {
                    $('[data-target="#advancedFilters"] i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
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
            });
        </script>
    @endpush
</div>
