<div>
    {{-- The Master doesn't talk, he acts. --}}
    <!-- Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        @if (request()->routeIs('admin.students.active'))
                            Active Students
                        @elseif(request()->routeIs('admin.students.seeking'))
                            Students Seeking Attachment
                        @elseif(request()->routeIs('admin.students.on-attachment'))
                            Students On Attachment
                        @else
                            All Students
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                        <li class="breadcrumb-item active">
                            @if (request()->routeIs('admin.students.active'))
                                Active
                            @elseif(request()->routeIs('admin.students.seeking'))
                                Seeking Attachment
                            @elseif(request()->routeIs('admin.students.on-attachment'))
                                On Attachment
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
                            <i class="fas fa-user-graduate"></i>
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
                            <i class="fas fa-search"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Seeking</span>
                            <span class="info-box-number">{{ $stats['seeking'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary elevation-1">
                            <i class="fas fa-briefcase"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">On Attachment</span>
                            <span class="info-box-number">{{ $stats['on_attachment'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger elevation-1">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Verified</span>
                            <span class="info-box-number">{{ $stats['verified'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-indigo elevation-1">
                            <i class="fas fa-user-plus"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Today</span>
                            <span class="info-box-number">{{ $stats['today'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        @if (request()->routeIs('admin.students.active'))
                            Active Students List
                        @elseif(request()->routeIs('admin.students.seeking'))
                            Students Seeking Attachment
                        @elseif(request()->routeIs('admin.students.on-attachment'))
                            Students Currently On Attachment
                        @else
                            All Students List
                        @endif
                    </h3>

                    <div class="card-tools">
                        <div class="btn-group mr-2">
                            <a href="{{ route('admin.students.index') }}"
                                class="btn btn-sm btn-outline-secondary {{ request()->routeIs('admin.students.index') ? 'active' : '' }}">
                                All
                            </a>
                            <a href="{{ route('admin.students.active') }}"
                                class="btn btn-sm btn-outline-success {{ request()->routeIs('admin.students.active') ? 'active' : '' }}">
                                Active
                            </a>
                            <a href="{{ route('admin.students.seeking') }}"
                                class="btn btn-sm btn-outline-warning {{ request()->routeIs('admin.students.seeking') ? 'active' : '' }}">
                                Seeking
                            </a>
                            <a href="{{ route('admin.students.on-attachment') }}"
                                class="btn btn-sm btn-outline-primary {{ request()->routeIs('admin.students.on-attachment') ? 'active' : '' }}">
                                On Attachment
                            </a>
                        </div>

                        @if ($showBulkActions)
                            <div class="btn-group mb-3">
                                <button class="btn btn-success" wire:click="matchAllSeekingStudents">
                                    <i class="fas fa-handshake mr-1"></i> Match All Seeking Students
                                </button>
                            </div>
                        @endif

                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="form-control float-right" placeholder="Search students...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" wire:click="$set('search', '')"
                                    title="Clear search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body border-bottom @if (!$showFilters) d-none @endif"
                    id="filterSection">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Institution</label>
                                <select wire:model.live="institutionFilter" class="form-control">
                                    <option value="">All Institutions</option>
                                    @foreach ($institutions as $institutionName => $institutionLabel)
                                        <option value="{{ $institutionName }}">{{ $institutionName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Course</label>
                                <select wire:model.live="courseFilter" class="form-control">
                                    <option value="">All Courses</option>
                                    @foreach ($courses as $courseName => $courseLabel)
                                        <option value="{{ $courseName }}">{{ $courseName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Year</label>
                                <select wire:model.live="yearFilter" class="form-control">
                                    <option value="">All Years</option>
                                    @foreach ($yearOptions as $yearValue => $yearLabel)
                                        <option value="{{ $yearValue }}">{{ $yearLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Attachment Status</label>
                                <select wire:model.live="attachmentStatusFilter" class="form-control">
                                    <option value="">All Status</option>
                                    @foreach ($attachmentStatusOptions as $statusValue => $statusLabel)
                                        <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Per Page</label>
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
                                    <th wire:click="sortBy('first_name')" style="cursor: pointer;">
                                        Student
                                        @if ($sortField === 'first_name')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </th>
                                    <th>Registration No.</th>
                                    <th>Institution & Course</th>
                                    <th>Year</th>
                                    <th>CGPA</th>
                                    <th>Attachment Status</th>
                                    <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                        Joined
                                        @if ($sortField === 'created_at')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </th>
                                    <th>Account Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr wire:key="student-{{ $student->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    @php
                                                        $initials = getInitials($student->full_name);
                                                        $colors = [
                                                            'primary',
                                                            'success',
                                                            'info',
                                                            'warning',
                                                            'danger',
                                                            'secondary',
                                                        ];
                                                        $color = $colors[crc32($student->email) % count($colors)];
                                                    @endphp
                                                    <div class="avatar-initials bg-{{ $color }} img-circle"
                                                        style="width: 40px; height: 40px; line-height: 40px; text-align: center; color: white; font-weight: bold; font-size: 14px;">
                                                        {{ $initials }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>{{ $student->full_name }}</strong>
                                                    <div class="text-muted small">{{ $student->email }}</div>
                                                    @if ($student->phone)
                                                        <div class="text-muted small">
                                                            {{ formatPhoneNumber($student->phone) }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($student->studentProfile?->student_reg_number)
                                                <span
                                                    class="badge badge-info">{{ $student->studentProfile->student_reg_number }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $student->studentProfile?->institution_name ?? 'N/A' }}</strong>
                                                <div class="text-muted small">
                                                    {{ $student->studentProfile?->course_name ?? 'N/A' }}</div>
                                                <div class="text-muted small">
                                                    {{ $student->studentProfile?->course_level_label ?? '' }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($student->studentProfile?->year_of_study)
                                                <span class="badge badge-secondary">
                                                    Year {{ $student->studentProfile->year_of_study }}
                                                </span>
                                                <div class="text-muted small">
                                                    Grad:
                                                    {{ $student->studentProfile->expected_graduation_year ?? 'N/A' }}
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($student->studentProfile?->cgpa)
                                                <span
                                                    class="badge badge-{{ $student->studentProfile->cgpa >= 3.0 ? 'success' : ($student->studentProfile->cgpa >= 2.0 ? 'warning' : 'danger') }}">
                                                    {{ number_format($student->studentProfile->cgpa, 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($student->studentProfile?->attachment_status)
                                                @php
                                                    $statusColors = [
                                                        'seeking' => 'warning',
                                                        'applied' => 'info',
                                                        'interviewing' => 'primary',
                                                        'placed' => 'success',
                                                        'completed' => 'secondary',
                                                    ];
                                                    $color =
                                                        $statusColors[$student->studentProfile->attachment_status] ??
                                                        'secondary';
                                                @endphp
                                                <span class="badge badge-{{ $color }}">
                                                    {{ $student->studentProfile->attachment_status_label }}
                                                </span>
                                                @if ($student->studentProfile->attachment_status === 'placed' && $student->studentProfile->is_currently_attached)
                                                    <div class="text-muted small">
                                                        {{ formatDate($student->studentProfile->attachment_start_date) }}
                                                        -
                                                        {{ formatDate($student->studentProfile->attachment_end_date) }}
                                                    </div>
                                                @endif
                                            @else
                                                <span class="badge badge-secondary">Not Set</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                {{ formatDate($student->created_at) }}
                                            </div>
                                            <small class="text-muted">{{ timeAgo($student->created_at) }}</small>
                                        </td>
                                        <td>
                                            @if ($student->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif

                                            @if ($student->is_verified)
                                                <span class="badge badge-info ml-1">Verified</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info"
                                                    wire:click="viewStudent({{ $student->id }})"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <!-- Add Match Button for Seeking Students -->
                                                @if ($student->studentProfile?->attachment_status === 'seeking')
                                                    <button type="button" class="btn btn-success"
                                                        wire:click="matchStudent({{ $student->id }})"
                                                        title="Find Matches for this Student">
                                                        <i class="fas fa-handshake"></i>
                                                    </button>
                                                @endif
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        @if ($student->is_active)
                                                            <button class="dropdown-item text-warning"
                                                                wire:click="toggleStudentActive({{ $student->id }})"
                                                                wire:confirm="Deactivate this student?">
                                                                <i class="fas fa-ban mr-2"></i> Deactivate
                                                            </button>
                                                        @else
                                                            <button class="dropdown-item text-success"
                                                                wire:click="toggleStudentActive({{ $student->id }})">
                                                                <i class="fas fa-check mr-2"></i> Activate
                                                            </button>
                                                        @endif

                                                        <div class="dropdown-divider"></div>


                                                        <h6 class="dropdown-header">Attachment Status</h6>
                                                        @foreach ($attachmentStatusOptions as $statusValue => $statusLabel)
                                                            @if ($student->studentProfile?->attachment_status !== $statusValue)
                                                                <button class="dropdown-item"
                                                                    wire:click="updateAttachmentStatus({{ $student->id }}, '{{ $statusValue }}')"
                                                                    wire:confirm="Change status to {{ $statusLabel }}?">
                                                                    <i
                                                                        class="fas fa-{{ $statusValue === 'placed' ? 'briefcase' : ($statusValue === 'seeking' ? 'search' : 'file-alt') }} mr-2"></i>
                                                                    {{ $statusLabel }}
                                                                </button>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No students found</h5>
                                            @if ($search || $institutionFilter || $courseFilter || $yearFilter || $attachmentStatusFilter)
                                                <p class="text-muted">Try adjusting your search or filters</p>
                                                <button
                                                    wire:click="$set(['search' => '', 'institutionFilter' => '', 'courseFilter' => '', 'yearFilter' => '', 'attachmentStatusFilter' => ''])"
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
                                Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }}
                                of {{ $students->total() }} entries
                            </span>
                        </div>
                        <div>
                            @if ($students->hasPages())
                                {{ $students->links() }}
                            @endif
                        </div>
                        <div>
                            <button type="button" class="btn btn-default" wire:click="$toggle('showFilters')">
                                <i class="fas fa-filter mr-1"></i>
                                {{ $showFilters ? 'Hide Filters' : 'Show Filters' }}
                            </button>
                            <button type="button" class="btn btn-primary ml-2" wire:click="exportStudents">
                                <i class="fas fa-download mr-1"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div wire:ignore.self class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="confirmModalBody">
                    <!-- Dynamic content will be set by JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Match Modal -->
    @if ($showMatchModal && $selectedStudentForMatch)
        <div class="modal fade show" id="matchModal" style="display: block; background-color: rgba(0,0,0,0.5);"
            tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-handshake mr-2"></i>
                            Find Matches for: {{ $selectedStudentForMatch->full_name }}
                        </h5>
                        <button type="button" class="close" wire:click="$set('showMatchModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Student Summary -->
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Course:</strong>
                                    {{ $selectedStudentForMatch->studentProfile->course_name }}
                                </div>
                                <div class="col-md-2">
                                    <strong>Year:</strong>
                                    {{ $selectedStudentForMatch->studentProfile->year_of_study }}
                                </div>
                                <div class="col-md-2">
                                    <strong>CGPA:</strong>
                                    {{ $selectedStudentForMatch->studentProfile->cgpa ?? 'N/A' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Location:</strong>
                                    {{ $selectedStudentForMatch->studentProfile->preferred_location ?? $selectedStudentForMatch->county }}
                                </div>
                                <div class="col-md-2">
                                    <strong>Skills:</strong>
                                    {{ count($selectedStudentForMatch->studentProfile->skills ?? []) }}
                                </div>
                            </div>
                        </div>

                        @if ($matchLoading)
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <h5>Finding matches...</h5>
                                <p class="text-muted">This may take a few moments</p>
                            </div>
                        @else
                            @if (empty($studentMatches))
                                <div class="text-center py-5">
                                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                                    <h5>No matches found</h5>
                                    <p class="text-muted">Try expanding search criteria or check if there are open
                                        opportunities</p>
                                    <button class="btn btn-primary" wire:click="findMatches">
                                        <i class="fas fa-sync-alt mr-1"></i> Retry
                                    </button>
                                </div>
                            @else
                                <div class="mb-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="mb-0">Found {{ count($studentMatches) }} potential matches
                                            </h6>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <button class="btn btn-sm btn-outline-primary mr-2"
                                                wire:click="findMatches">
                                                <i class="fas fa-sync-alt mr-1"></i> Refresh
                                            </button>
                                            <button class="btn btn-sm btn-success" wire:click="saveMatches"
                                                wire:loading.attr="disabled" wire:target="saveMatches">
                                                <span wire:loading.remove wire:target="saveMatches">
                                                    <i class="fas fa-save mr-1"></i> Save Selected
                                                    ({{ count($selectedMatches) }})
                                                </span>
                                                <span wire:loading wire:target="saveMatches">
                                                    <i class="fas fa-spinner fa-spin mr-1"></i> Saving...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="50">
                                                    <input type="checkbox" wire:model.live="selectedMatches"
                                                        value="all" onclick="toggleAllMatches(this)">
                                                </th>
                                                <th>Organization</th>
                                                <th>Opportunity</th>
                                                <th>Match Score</th>
                                                <th>Location</th>
                                                <th>Deadline</th>
                                                <th>Match Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($studentMatches as $index => $match)
                                                <tr
                                                    class="{{ in_array($index, $selectedMatches) ? 'table-success' : '' }}">
                                                    <td>
                                                        <input type="checkbox" wire:model.live="selectedMatches"
                                                            value="{{ $index }}">
                                                    </td>
                                                    <td>
                                                        <strong>{{ $match['opportunity']->organization->name }}</strong>
                                                        @if ($match['opportunity']->organization->is_verified)
                                                            <i class="fas fa-check-circle text-success ml-1"
                                                                title="Verified Organization"></i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="#"
                                                            class="text-primary">{{ $match['opportunity']->title }}</a>
                                                        <div class="text-muted small">
                                                            Slots: {{ $match['opportunity']->slots_available }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="progress flex-grow-1 mr-2"
                                                                style="height: 8px; width: 80px;">
                                                                <div class="progress-bar bg-{{ $match['score'] >= 80 ? 'success' : ($match['score'] >= 60 ? 'info' : 'warning') }}"
                                                                    style="width: {{ $match['score'] }}%"></div>
                                                            </div>
                                                            <span
                                                                class="badge badge-{{ $match['score'] >= 80 ? 'success' : ($match['score'] >= 60 ? 'info' : 'warning') }}">
                                                                {{ $match['score'] }}%
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>{{ $match['opportunity']->location ?? ($match['opportunity']->county ?? 'N/A') }}
                                                    </td>
                                                    <td>
                                                        @if ($match['opportunity']->deadline)
                                                            <span
                                                                class="badge badge-{{ $match['opportunity']->deadline->isPast() ? 'danger' : 'success' }}">
                                                                {{ $match['opportunity']->deadline->format('d M Y') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary">No deadline</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-info"
                                                            data-toggle="collapse"
                                                            data-target="#matchDetails-{{ $index }}">
                                                            <i class="fas fa-chart-pie"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr class="collapse" id="matchDetails-{{ $index }}">
                                                    <td colspan="7" class="bg-light">
                                                        <div class="p-3">
                                                            <h6 class="mb-3">Match Score Breakdown:</h6>
                                                            <div class="row">
                                                                @foreach ($match['details'] as $criteria => $data)
                                                                    <div class="col-md-3">
                                                                        <div class="card">
                                                                            <div class="card-body p-2">
                                                                                <h6
                                                                                    class="card-title text-capitalize mb-2">
                                                                                    {{ $criteria }}</h6>
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center">
                                                                                    <span
                                                                                        class="h4 mb-0 text-{{ $data['score'] >= 70 ? 'success' : ($data['score'] >= 50 ? 'info' : 'warning') }}">
                                                                                        {{ $data['score'] }}%
                                                                                    </span>
                                                                                    <span
                                                                                        class="text-muted small">Weight:
                                                                                        {{ $data['weight'] }}%</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            @if ($match['opportunity']->description)
                                                                <div class="mt-3">
                                                                    <strong>Description:</strong>
                                                                    <p class="text-muted small mb-0">
                                                                        {{ Str::limit($match['opportunity']->description, 300) }}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showMatchModal', false)">
                            Cancel
                        </button>
                        @if (!empty($studentMatches))
                            <button type="button" class="btn btn-success" wire:click="saveMatches"
                                wire:loading.attr="disabled" wire:target="saveMatches">
                                <span wire:loading.remove wire:target="saveMatches">
                                    <i class="fas fa-save mr-1"></i> Save Selected ({{ count($selectedMatches) }})
                                </span>
                                <span wire:loading wire:target="saveMatches">
                                    <i class="fas fa-spinner fa-spin mr-1"></i> Saving...
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @endif

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
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

            function toggleAllMatches(checkbox) {
                let checkboxes = document.querySelectorAll('input[type="checkbox"][wire\\:model\\.live="selectedMatches"]');
                checkboxes.forEach(cb => {
                    if (cb.value !== 'all') {
                        cb.checked = checkbox.checked;
                        cb.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });
            }

            document.addEventListener('livewire:initialized', () => {
                Livewire.on('confirm-action', (data) => {
                    $('#confirmModalBody').text(data.message);
                    $('#confirmModal').modal('show');

                    $('#confirmActionBtn').off('click').on('click', function() {
                        Livewire.dispatch(data.method);
                        $('#confirmModal').modal('hide');
                    });
                });
            });
        </script>
    @endpush
</div>
