<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    <!-- Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        @if ($viewType === 'pending')
                            Pending Review Applications
                        @elseif($viewType === 'interviewing')
                            Interview Stage Applications
                        @elseif($viewType === 'offers')
                            Offer Stage Applications
                        @elseif($viewType === 'hired')
                            Hired/Placed Applications
                        @elseif($viewType === 'rejected')
                            Rejected Applications
                        @else
                            All Applications
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.applications.index') }}">Applications</a>
                        </li>
                        <li class="breadcrumb-item active">
                            @if ($viewType === 'pending')
                                Pending
                            @elseif($viewType === 'interviewing')
                                Interviewing
                            @elseif($viewType === 'offers')
                                Offers
                            @elseif($viewType === 'hired')
                                Hired
                            @elseif($viewType === 'rejected')
                                Rejected
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
                            <i class="fas fa-file-alt"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total</span>
                            <span class="info-box-number">{{ $stats['total'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning elevation-1">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending</span>
                            <span class="info-box-number">{{ $stats['pending'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary elevation-1">
                            <i class="fas fa-calendar-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Interviewing</span>
                            <span class="info-box-number">{{ $stats['interviewing'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-indigo elevation-1">
                            <i class="fas fa-handshake"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Offers</span>
                            <span class="info-box-number">{{ $stats['offers'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1">
                            <i class="fas fa-user-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Hired</span>
                            <span class="info-box-number">{{ $stats['hired'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger elevation-1">
                            <i class="fas fa-calendar-day"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Today</span>
                            <span class="info-box-number">{{ $stats['today'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Action Cards -->
            @if ($selectedApplications && count($selectedApplications) > 0)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">
                                            <i class="fas fa-check-square mr-2"></i>
                                            {{ count($selectedApplications) }} application(s) selected
                                        </h5>
                                        <small class="text-muted">Choose an action to perform on selected items</small>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success"
                                            wire:click="$set('bulkAction', 'shortlist')"
                                            wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-list-check mr-1"></i> Shortlist
                                        </button>
                                        <button type="button" class="btn btn-warning"
                                            wire:click="$set('bulkAction', 'schedule_interview')"
                                            wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-calendar-alt mr-1"></i> Schedule Interview
                                        </button>
                                        <button type="button" class="btn btn-info"
                                            wire:click="$set('bulkAction', 'send_offer')"
                                            wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-handshake mr-1"></i> Send Offer
                                        </button>
                                        <button type="button" class="btn btn-danger"
                                            wire:click="$set('bulkAction', 'reject')"
                                            wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-times-circle mr-1"></i> Reject
                                        </button>
                                        <button type="button" class="btn btn-secondary"
                                            wire:click="$set('bulkAction', 'archive')"
                                            wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-archive mr-1"></i> Archive
                                        </button>
                                        <button type="button" class="btn btn-primary"
                                            wire:click="$set('bulkAction', 'export')"
                                            wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-download mr-1"></i> Export
                                        </button>
                                        <button type="button" class="btn btn-default"
                                            wire:click="$set('selectedApplications', [])">
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
                        @if ($viewType === 'pending')
                            Pending Review Applications ({{ $stats['pending'] }})
                        @elseif($viewType === 'interviewing')
                            Interview Stage Applications ({{ $stats['interviewing'] }})
                        @elseif($viewType === 'offers')
                            Offer Stage Applications ({{ $stats['offers'] }})
                        @elseif($viewType === 'hired')
                            Hired/Placed Applications ({{ $stats['hired'] }})
                        @elseif($viewType === 'rejected')
                            Rejected Applications ({{ $stats['rejected'] }})
                        @else
                            All Applications ({{ $stats['total'] }})
                        @endif
                    </h3>

                    <div class="card-tools">
                        <div class="btn-group mr-2">
                            <a href="{{ route('admin.applications.index') }}"
                                class="btn btn-sm btn-outline-secondary {{ $viewType === 'all' ? 'active' : '' }}">
                                All
                            </a>
                            <a href="{{ route('admin.applications.pending') }}"
                                class="btn btn-sm btn-outline-warning {{ $viewType === 'pending' ? 'active' : '' }}">
                                <i class="fas fa-clock mr-1"></i> Pending
                                @if ($stats['pending'] > 0)
                                    <span class="badge badge-danger ml-1">{{ $stats['pending'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.applications.interviewing') }}"
                                class="btn btn-sm btn-outline-primary {{ $viewType === 'interviewing' ? 'active' : '' }}">
                                <i class="fas fa-calendar-check mr-1"></i> Interviewing
                                @if ($stats['interviewing'] > 0)
                                    <span class="badge badge-primary ml-1">{{ $stats['interviewing'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.applications.offers') }}"
                                class="btn btn-sm btn-outline-info {{ $viewType === 'offers' ? 'active' : '' }}">
                                <i class="fas fa-handshake mr-1"></i> Offers
                                @if ($stats['offers'] > 0)
                                    <span class="badge badge-info ml-1">{{ $stats['offers'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.applications.hired') }}"
                                class="btn btn-sm btn-outline-success {{ $viewType === 'hired' ? 'active' : '' }}">
                                <i class="fas fa-user-check mr-1"></i> Hired
                                @if ($stats['hired'] > 0)
                                    <span class="badge badge-success ml-1">{{ $stats['hired'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.applications.rejected') }}"
                                class="btn btn-sm btn-outline-danger {{ $viewType === 'rejected' ? 'active' : '' }}">
                                <i class="fas fa-times-circle mr-1"></i> Rejected
                                @if ($stats['rejected'] > 0)
                                    <span class="badge badge-danger ml-1">{{ $stats['rejected'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.applications.analytics') }}"
                                class="btn btn-sm btn-outline-dark">
                                <i class="fas fa-chart-line mr-1"></i> Analytics
                            </a>
                        </div>

                        <div class="input-group input-group-sm" style="width: 300px;">
                            <input type="text" wire:model.live.debounce.500ms="search"
                                class="form-control float-right" placeholder="Search applications...">
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
                                    <option value="submitted,under_review">Pending Review</option>
                                    <option value="shortlisted">Shortlisted</option>
                                    <option value="interview_scheduled,interview_completed">Interview Stage</option>
                                    <option value="offer_sent,offer_accepted,offer_rejected">Offer Stage</option>
                                    <option value="hired">Hired</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="withdrawn">Withdrawn</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Opportunity</label>
                                <select wire:model.live="opportunityFilter" class="form-control">
                                    <option value="">All Opportunities</option>
                                    @foreach ($opportunities as $opportunity)
                                        <option value="{{ $opportunity->id }}">{{ $opportunity->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" wire:model.live="dateFrom" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" wire:model.live="dateTo" class="form-control">
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
                                <label>Minimum CGPA</label>
                                <select wire:model.live="cgpaMin" class="form-control">
                                    <option value="">Any CGPA</option>
                                    <option value="2.0">2.0+</option>
                                    <option value="2.5">2.5+</option>
                                    <option value="3.0">3.0+</option>
                                    <option value="3.5">3.5+</option>
                                    <option value="4.0">4.0</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Maximum CGPA</label>
                                <select wire:model.live="cgpaMax" class="form-control">
                                    <option value="">Any CGPA</option>
                                    <option value="2.0">Up to 2.0</option>
                                    <option value="2.5">Up to 2.5</option>
                                    <option value="3.0">Up to 3.0</option>
                                    <option value="3.5">Up to 3.5</option>
                                    <option value="4.0">Up to 4.0</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Institution</label>
                                <select wire:model.live="institutionFilter" class="form-control">
                                    <option value="">All Institutions</option>
                                    @foreach ($this->institutions as $institution)
                                        <option value="{{ $institution }}">{{ $institution }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Course</label>
                                <select wire:model.live="courseFilter" class="form-control">
                                    <option value="">All Courses</option>
                                    @foreach ($this->courses as $course)
                                        <option value="{{ $course }}">{{ $course }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Year of Study</label>
                                <select wire:model.live="yearOfStudyFilter" class="form-control">
                                    <option value="">Any Year</option>
                                    <option value="1">First Year</option>
                                    <option value="2">Second Year</option>
                                    <option value="3">Third Year</option>
                                    <option value="4">Fourth Year</option>
                                    <option value="5">Fifth Year</option>
                                    <option value="6">Sixth Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Skill</label>
                                <select wire:model.live="skillFilter" class="form-control">
                                    <option value="">Any Skill</option>
                                    @foreach ($this->skills as $skill)
                                        <option value="{{ $skill }}">{{ $skill }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sort By</label>
                                <select wire:model.live="sortField" class="form-control">
                                    <option value="created_at">Date Applied</option>
                                    <option value="submitted_at">Date Submitted</option>
                                    <option value="reviewed_at">Date Reviewed</option>
                                    <option value="interview_scheduled_at">Interview Date</option>
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="button" class="btn btn-sm btn-primary"
                                        wire:click="applyBulkAction"
                                        {{ count($selectedApplications) === 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-bolt mr-1"></i> Apply to Selected
                                        ({{ count($selectedApplications) }})
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
                                                wire:model="selectAll">
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
                                    <th>Opportunity</th>
                                    <th>Student Details</th>
                                    <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                        Applied
                                        @if ($sortField === 'created_at')
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
                                    <th>Documents</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($applications as $application)
                                    <tr wire:key="application-{{ $application->id }}"
                                        class="{{ $application->is_archived ? 'table-secondary' : '' }}">
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="application-{{ $application->id }}"
                                                    value="{{ $application->id }}" wire:model="selectedApplications">
                                                <label class="custom-control-label"
                                                    for="application-{{ $application->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>#{{ $application->id }}</strong>
                                            @if ($application->is_archived)
                                                <div>
                                                    <small class="badge badge-secondary">Archived</small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    @php
                                                        $initials = getInitials($application->student->full_name);
                                                        $colors = [
                                                            'primary',
                                                            'success',
                                                            'info',
                                                            'warning',
                                                            'danger',
                                                            'secondary',
                                                        ];
                                                        $color =
                                                            $colors[
                                                                crc32($application->student->email) % count($colors)
                                                            ];
                                                    @endphp
                                                    <div class="avatar-initials bg-{{ $color }} img-circle"
                                                        style="width: 40px; height: 40px; line-height: 40px; text-align: center; color: white; font-weight: bold; font-size: 14px;">
                                                        {{ $initials }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>{{ $application->student->full_name }}</strong>
                                                    <div class="text-muted small">{{ $application->student->email }}
                                                    </div>
                                                    <div class="text-muted small">
                                                        {{ formatPhoneNumber($application->student->phone) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $application->opportunity->title }}</strong>
                                            <div class="text-muted small">
                                                {{ $application->opportunity->organization->name }}
                                            </div>
                                            <div class="text-muted small">
                                                <i
                                                    class="fas fa-{{ $application->opportunity->is_remote ? 'wifi' : 'building' }} mr-1"></i>
                                                {{ $application->opportunity->location_type }}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($application->studentProfile)
                                                <div class="small">
                                                    <strong>{{ $application->studentProfile->institution_name }}</strong><br>
                                                    {{ $application->studentProfile->course_name }} (Year
                                                    {{ $application->studentProfile->year_of_study }})<br>
                                                    CGPA:
                                                    <strong>{{ $application->studentProfile->cgpa ?? 'N/A' }}</strong><br>
                                                    @if ($application->studentProfile->skills && count($application->studentProfile->skills) > 0)
                                                        <span class="text-muted">
                                                            Skills:
                                                            {{ implode(', ', array_slice($application->studentProfile->skills, 0, 3)) }}
                                                            @if (count($application->studentProfile->skills) > 3)
                                                                +{{ count($application->studentProfile->skills) - 3 }}
                                                                more
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">No profile data</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                {{ formatDate($application->created_at) }}
                                            </div>
                                            <small class="text-muted">{{ timeAgo($application->created_at) }}</small>

                                            @if ($application->submitted_at)
                                                <div class="text-muted small">
                                                    Submitted: {{ formatDate($application->submitted_at) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            {!! getApplicationStatusBadge($application->status) !!}

                                            @if ($application->interview_scheduled_at)
                                                <div class="text-muted small mt-1">
                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                    {{ formatDate($application->interview_scheduled_at) }}
                                                </div>
                                            @endif

                                            @if ($application->offer_sent_at)
                                                <div class="text-muted small mt-1">
                                                    <i class="fas fa-handshake mr-1"></i>
                                                    Offer sent {{ timeAgo($application->offer_sent_at) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                @if ($application->cv_url)
                                                    <a href="{{ asset('storage/' . $application->cv_url) }}"
                                                        target="_blank" class="btn btn-xs btn-danger mr-1"
                                                        title="View CV">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @endif

                                                @if ($application->transcript_url)
                                                    <a href="{{ asset('storage/' . $application->transcript_url) }}"
                                                        target="_blank" class="btn btn-xs btn-info mr-1"
                                                        title="View Transcript">
                                                        <i class="fas fa-file-alt"></i>
                                                    </a>
                                                @endif

                                                @if ($application->additional_documents && count($application->additional_documents) > 0)
                                                    <span class="badge badge-secondary"
                                                        title="{{ count($application->additional_documents) }} additional documents">
                                                        +{{ count($application->additional_documents) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info"
                                                    wire:click="viewApplication({{ $application->id }})"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Quick Status Actions -->
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="fas fa-bolt"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <!-- Status-specific actions -->
                                                        @if (in_array($application->status, ['submitted', 'under_review']))
                                                            <button class="dropdown-item text-success"
                                                                wire:click="updateStatus({{ $application->id }}, 'shortlisted')"
                                                                wire:confirm="Shortlist this application?">
                                                                <i class="fas fa-list-check mr-2"></i> Shortlist
                                                            </button>
                                                            <button class="dropdown-item text-danger"
                                                                wire:click="updateStatus({{ $application->id }}, 'rejected')"
                                                                wire:confirm="Reject this application?">
                                                                <i class="fas fa-times-circle mr-2"></i> Reject
                                                            </button>
                                                        @endif

                                                        @if (in_array($application->status, ['shortlisted', 'under_review']))
                                                            <button class="dropdown-item text-warning"
                                                                wire:click="scheduleInterview({{ $application->id }})">
                                                                <i class="fas fa-calendar-alt mr-2"></i> Schedule
                                                                Interview
                                                            </button>
                                                        @endif

                                                        @if (in_array($application->status, ['interview_completed', 'shortlisted']))
                                                            <button class="dropdown-item text-info"
                                                                wire:click="sendOffer({{ $application->id }})">
                                                                <i class="fas fa-handshake mr-2"></i> Send Offer
                                                            </button>
                                                        @endif

                                                        @if ($application->status === 'interview_scheduled')
                                                            <button class="dropdown-item text-success"
                                                                wire:click="updateStatus({{ $application->id }}, 'interview_completed')"
                                                                wire:confirm="Mark interview as completed?">
                                                                <i class="fas fa-check-circle mr-2"></i> Complete
                                                                Interview
                                                            </button>
                                                        @endif

                                                        @if ($application->status === 'offer_sent')
                                                            <button class="dropdown-item text-success"
                                                                wire:click="updateStatus({{ $application->id }}, 'offer_accepted')"
                                                                wire:confirm="Mark offer as accepted?">
                                                                <i class="fas fa-check mr-2"></i> Accept Offer
                                                            </button>
                                                            <button class="dropdown-item text-danger"
                                                                wire:click="updateStatus({{ $application->id }}, 'offer_rejected')"
                                                                wire:confirm="Mark offer as rejected?">
                                                                <i class="fas fa-times mr-2"></i> Reject Offer
                                                            </button>
                                                        @endif

                                                        @if ($application->status === 'offer_accepted')
                                                            <button class="dropdown-item text-success"
                                                                wire:click="updateStatus({{ $application->id }}, 'hired')"
                                                                wire:confirm="Mark student as hired?">
                                                                <i class="fas fa-user-check mr-2"></i> Mark as Hired
                                                            </button>
                                                        @endif

                                                        <div class="dropdown-divider"></div>

                                                        <!-- Common actions -->
                                                        <a href="{{ route('admin.applications.edit', $application->id) }}"
                                                            class="dropdown-item">
                                                            <i class="fas fa-edit mr-2"></i> Edit
                                                        </a>

                                                        <div class="dropdown-divider"></div>

                                                        <button class="dropdown-item text-danger"
                                                            wire:click="deleteApplication({{ $application->id }})"
                                                            wire:confirm="Delete this application?">
                                                            <i class="fas fa-trash mr-2"></i> Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No applications found</h5>
                                            @if (
                                                $search ||
                                                    $statusFilter ||
                                                    $opportunityFilter ||
                                                    $dateFrom ||
                                                    $dateTo ||
                                                    $cgpaMin ||
                                                    $cgpaMax ||
                                                    $institutionFilter ||
                                                    $courseFilter ||
                                                    $yearOfStudyFilter ||
                                                    $skillFilter)
                                                <p class="text-muted">Try adjusting your search or filters</p>
                                                <button wire:click="resetFilters" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-redo mr-1"></i> Reset Filters
                                                </button>
                                            @else
                                                <p class="text-muted">No applications have been submitted yet.</p>
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
                                Showing {{ $applications->firstItem() ?? 0 }} to {{ $applications->lastItem() ?? 0 }}
                                of {{ $applications->total() }} entries
                            </span>
                            @if (count($selectedApplications) > 0)
                                <span class="badge badge-primary ml-2">
                                    {{ count($selectedApplications) }} selected
                                </span>
                            @endif
                        </div>
                        <div>
                            @if ($applications->hasPages())
                                {{ $applications->links() }}
                            @endif
                        </div>
                        <div>
                            <button type="button" class="btn btn-default" wire:click="$toggle('showFilters')">
                                <i class="fas fa-filter mr-1"></i>
                                {{ $showFilters ? 'Hide Filters' : 'Show Filters' }}
                            </button>
                            <a href="{{ route('admin.applications.analytics') }}" class="btn btn-dark ml-2">
                                <i class="fas fa-chart-line mr-1"></i> Analytics
                            </a>
                            <a href="{{ route('admin.applications.reports') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-download mr-1"></i> Reports
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
                            @if ($bulkAction === 'shortlist')
                                <i class="fas fa-list-check text-success mr-2"></i> Shortlist Applications
                            @elseif($bulkAction === 'reject')
                                <i class="fas fa-times-circle text-danger mr-2"></i> Reject Applications
                            @elseif($bulkAction === 'schedule_interview')
                                <i class="fas fa-calendar-alt text-warning mr-2"></i> Schedule Interviews
                            @elseif($bulkAction === 'send_offer')
                                <i class="fas fa-handshake text-info mr-2"></i> Send Offers
                            @elseif($bulkAction === 'archive')
                                <i class="fas fa-archive text-secondary mr-2"></i> Archive Applications
                            @elseif($bulkAction === 'export')
                                <i class="fas fa-download text-primary mr-2"></i> Export Applications
                            @endif
                        </h5>
                        <button type="button" class="close" wire:click="$set('showBulkActionModal', false)">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if ($bulkAction === 'shortlist')
                            <p>Are you sure you want to shortlist <strong>{{ count($selectedApplications) }}</strong>
                                selected application(s)?</p>
                            <p class="text-muted">Shortlisted applications will move to the next review stage.</p>
                        @elseif($bulkAction === 'reject')
                            <p>Are you sure you want to reject <strong>{{ count($selectedApplications) }}</strong>
                                selected application(s)?</p>
                            <p class="text-muted">Rejected applications will be moved to the rejected list.</p>
                        @elseif($bulkAction === 'schedule_interview')
                            <p>You are about to schedule interviews for
                                <strong>{{ count($selectedApplications) }}</strong> selected application(s).</p>
                            <p class="text-muted">You will be redirected to the bulk interview scheduling page.</p>
                        @elseif($bulkAction === 'send_offer')
                            <p>You are about to send offers to <strong>{{ count($selectedApplications) }}</strong>
                                selected application(s).</p>
                            <p class="text-muted">You will be redirected to the bulk offer sending page.</p>
                        @elseif($bulkAction === 'archive')
                            <p>Are you sure you want to archive <strong>{{ count($selectedApplications) }}</strong>
                                selected application(s)?</p>
                            <p class="text-muted">Archived applications will be moved to the archive section.</p>
                        @elseif($bulkAction === 'export')
                            <p>You are about to export <strong>{{ count($selectedApplications) }}</strong> selected
                                application(s).</p>
                            <p class="text-muted">The data will be downloaded as a CSV file.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                            wire:click="$set('showBulkActionModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="applyBulkAction">
                            @if ($bulkAction === 'shortlist')
                                <i class="fas fa-check mr-1"></i> Yes, Shortlist
                            @elseif($bulkAction === 'reject')
                                <i class="fas fa-times mr-1"></i> Yes, Reject
                            @elseif($bulkAction === 'schedule_interview')
                                <i class="fas fa-calendar-check mr-1"></i> Continue to Schedule
                            @elseif($bulkAction === 'send_offer')
                                <i class="fas fa-paper-plane mr-1"></i> Continue to Send Offers
                            @elseif($bulkAction === 'archive')
                                <i class="fas fa-archive mr-1"></i> Yes, Archive
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
            #advancedFilters {
                transition: all 0.3s ease;
            }
            .dropdown-menu {
                min-width: 200px;
            }
            .custom-checkbox .custom-control-label::before {
                border-radius: 3px;
            }
            .custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
                background-color: #007bff;
                border-color: #007bff;
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

                // Reset filters function
                Livewire.on('reset-filters', () => {
                    $('input[type="date"]').val('');
                    $('select').val('');
                });
            });

            // Function to reset all filters
            function resetFilters() {
                Livewire.dispatch('reset-filters');
            }
        </script>
    @endpush

</div>
