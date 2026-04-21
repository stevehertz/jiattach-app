<div>
    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Alert for students without profile -->
            @if (!$student->studentProfile)
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Incomplete Profile!</h5>
                    This user doesn't have a complete student profile. They may have registered but not completed their
                    profile setup.
                </div>
            @endif

            <div class="row">
                <!-- Left Column - Profile Card & Quick Actions -->
                <div class="col-md-4">
                    <!-- Profile Card -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                @php
                                    $initials = getInitials($student->full_name);
                                    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                                    $color = $colors[crc32($student->email) % count($colors)];
                                @endphp
                                <div class="avatar-initials bg-{{ $color }} img-circle elevation-2"
                                    style="width: 100px; height: 100px; line-height: 100px; text-align: center; color: white; font-weight: bold; font-size: 36px; margin: 0 auto 15px;">
                                    {{ $initials }}
                                </div>
                            </div>

                            <h3 class="profile-username text-center">{{ $student->full_name }}</h3>

                            <p class="text-muted text-center">
                                @if ($student->studentProfile)
                                    <span
                                        class="badge badge-{{ $student->studentProfile->attachment_status === 'placed' ? 'success' : ($student->studentProfile->attachment_status === 'seeking' ? 'warning' : 'info') }} p-2">
                                        <i
                                            class="fas fa-{{ $student->studentProfile->attachment_status === 'placed' ? 'briefcase' : ($student->studentProfile->attachment_status === 'seeking' ? 'search' : 'file-alt') }} mr-1"></i>
                                        {{ $student->studentProfile->attachment_status_label }}
                                    </span>
                                @endif
                                @if ($student->is_verified)
                                    <span class="badge badge-info p-2 ml-1"><i class="fas fa-check-circle mr-1"></i>
                                        Verified</span>
                                @endif
                                @if ($student->is_active)
                                    <span class="badge badge-success p-2 ml-1"><i class="fas fa-circle mr-1"></i>
                                        Active</span>
                                @else
                                    <span class="badge badge-secondary p-2 ml-1"><i class="fas fa-circle mr-1"></i>
                                        Inactive</span>
                                @endif
                            </p>

                            <!-- Profile Completion Progress -->
                            @if ($student->studentProfile)
                                <div class="progress-group mb-3">
                                    <span class="progress-text">Profile Completion</span>
                                    <span
                                        class="float-right text-bold">{{ $student->studentProfile->profile_completeness }}%</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-success"
                                            style="width: {{ $student->studentProfile->profile_completeness }}%">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <i class="fas fa-envelope mr-2 text-muted"></i>
                                    <b>Email</b>
                                    <a href="mailto:{{ $student->email }}" class="float-right">{{ $student->email }}</a>
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-phone mr-2 text-muted"></i>
                                    <b>Phone</b>
                                    <span class="float-right">
                                        @if ($student->phone)
                                            <a
                                                href="tel:{{ $student->phone }}">{{ formatPhoneNumber($student->phone) }}</a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-calendar-alt mr-2 text-muted"></i>
                                    <b>Date of Birth</b>
                                    <span class="float-right">
                                        {{ $student->date_of_birth ? formatDate($student->date_of_birth) : 'N/A' }}
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-venus-mars mr-2 text-muted"></i>
                                    <b>Gender</b>
                                    <span class="float-right text-capitalize">{{ $student->gender ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-map-marker-alt mr-2 text-muted"></i>
                                    <b>Location</b>
                                    <span class="float-right">
                                        {{ $student->county ?? 'N/A' }}{{ $student->town ? ', ' . $student->town : '' }}
                                    </span>
                                </li>
                                @if ($student->disability_status && $student->disability_status !== 'none')
                                    <li class="list-group-item">
                                        <i class="fas fa-wheelchair mr-2 text-muted"></i>
                                        <b>Disability</b>
                                        <span class="float-right">
                                            <span
                                                class="badge badge-info">{{ $student->disability_status_label }}</span>
                                        </span>
                                    </li>
                                @endif
                                <li class="list-group-item">
                                    <i class="fas fa-clock mr-2 text-muted"></i>
                                    <b>Member Since</b>
                                    <span class="float-right">{{ formatDate($student->created_at) }}</span>
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-sign-in-alt mr-2 text-muted"></i>
                                    <b>Last Login</b>
                                    <span class="float-right">
                                        {{ $student->last_login_at ? timeAgo($student->last_login_at) : 'Never' }}
                                    </span>
                                </li>
                            </ul>

                            <div class="row">
                                <div class="col-6">
                                    <a href="{{ route('admin.students.index') }}" class="btn btn-default btn-block">
                                        <i class="fas fa-arrow-left mr-1"></i> Back
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="mailto:{{ $student->email }}" class="btn btn-primary btn-block" target="_blank">
                                        <i class="fas fa-envelope mr-1"></i> Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    {{-- <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt mr-1"></i> Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Activate/Deactivate -->
                            @if ($student->is_active)
                                <button wire:click="confirmDeactivation" class="btn btn-warning btn-block mb-2">
                                    <i class="fas fa-ban mr-1"></i> Deactivate Student
                                </button>
                            @else
                                <button wire:click="confirmActivation" class="btn btn-success btn-block mb-2">
                                    <i class="fas fa-check mr-1"></i> Activate Student
                                </button>
                            @endif

                            <!-- Verify Account -->
                            @if (!$student->is_verified)
                                <button wire:click="confirmVerification" class="btn btn-info btn-block mb-2">
                                    <i class="fas fa-check-circle mr-1"></i> Verify Account
                                </button>
                            @endif

                            <!-- Match Student Button (Only for seeking students) -->
                            @if ($student->studentProfile && $student->studentProfile->attachment_status === 'seeking')
                                <button wire:click="matchStudent({{ $student->id }})" class="btn btn-primary btn-block mb-2">
                                    <i class="fas fa-magic mr-1"></i> Find Matches
                                </button>
                            @endif

                            <!-- Change Status Dropdown -->
                            <div class="dropdown mb-2">
                                <button class="btn btn-secondary btn-block dropdown-toggle" type="button"
                                    data-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-exchange-alt mr-1"></i> Change Status
                                </button>
                                <div class="dropdown-menu w-100">
                                    @foreach (['seeking', 'applied', 'interviewing', 'placed', 'completed'] as $status)
                                        @if (!$student->studentProfile || $student->studentProfile->attachment_status !== $status)
                                            <button class="dropdown-item"
                                                wire:click="confirmStatusChange('{{ $status }}')">
                                                <i
                                                    class="fas fa-{{ $status === 'placed' ? 'briefcase' : ($status === 'seeking' ? 'search' : 'file-alt') }} mr-2"></i>
                                                {{ ucfirst($status) }}
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- Danger Zone -->
                            <button wire:click="confirmDelete" class="btn btn-danger btn-block">
                                <i class="fas fa-trash mr-1"></i> Delete Student
                            </button>
                        </div>
                    </div> --}}

                    <!-- Documents Card -->
                    @if ($student->studentProfile)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-file mr-1"></i> Documents
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-file-alt text-info mr-2"></i>
                                            <span>CV/Resume</span>
                                        </div>
                                        @if ($student->studentProfile->cv_url)
                                            <a href="{{ $student->studentProfile->cv_url }}"
                                                target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else
                                            <span class="badge badge-warning">Not uploaded</span>
                                        @endif
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-file-contract text-success mr-2"></i>
                                            <span>Academic Transcript</span>
                                        </div>
                                        @if ($student->studentProfile->transcript_url)
                                            <a href="{{ $student->studentProfile->transcript_url }}"
                                                target="_blank" class="btn btn-sm btn-success">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else
                                            <span class="badge badge-warning">Not uploaded</span>
                                        @endif
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-envelope-open-text text-warning mr-2"></i>
                                            <span>School Letter</span>
                                        </div>
                                        @if ($student->studentProfile->school_letter_url)
                                            <a href="{{ $student->studentProfile->school_letter_url }}"
                                                target="_blank" class="btn btn-sm btn-warning">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else
                                            <span class="badge badge-warning">Not uploaded</span>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Detailed Information -->
                <div class="col-md-8">
                    <!-- Tabs Navigation -->
                    <div class="card card-primary card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="student-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="academic-tab" data-toggle="pill" href="#academic"
                                        role="tab" aria-controls="academic" aria-selected="true">
                                        <i class="fas fa-graduation-cap mr-1"></i> Academic
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="skills-tab" data-toggle="pill" href="#skills"
                                        role="tab" aria-controls="skills" aria-selected="false">
                                        <i class="fas fa-tools mr-1"></i> Skills & Interests
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="applications-tab" data-toggle="pill"
                                        href="#applications" role="tab" aria-controls="applications"
                                        aria-selected="false">
                                        <i class="fas fa-file-alt mr-1"></i> Applications
                                        @if ($student->studentApplications->count() > 0)
                                            <span
                                                class="badge badge-info ml-1">{{ $student->studentApplications->count() }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="mentorship-tab" data-toggle="pill" href="#mentorship"
                                        role="tab" aria-controls="mentorship" aria-selected="false">
                                        <i class="fas fa-users mr-1"></i> Mentorship
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="activity-tab" data-toggle="pill" href="#activity"
                                        role="tab" aria-controls="activity" aria-selected="false">
                                        <i class="fas fa-history mr-1"></i> Activity
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content" id="student-tabs-content">
                                <!-- Academic Tab -->
                                <div class="tab-pane fade show active" id="academic" role="tabpanel"
                                    aria-labelledby="academic-tab">
                                    @if ($student->studentProfile)
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-box bg-light">
                                                    <span class="info-box-icon bg-info"><i
                                                            class="fas fa-id-card"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Registration Number</span>
                                                        <span
                                                            class="info-box-number">{{ $student->studentProfile->student_reg_number ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-box bg-light">
                                                    <span class="info-box-icon bg-success"><i
                                                            class="fas fa-university"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Institution</span>
                                                        <span
                                                            class="info-box-number">{{ $student->studentProfile->institution_name ?? 'N/A' }}</span>
                                                        <span
                                                            class="info-box-text small">{{ $student->studentProfile->institution_type_label ?? '' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-box bg-light">
                                                    <span class="info-box-icon bg-warning"><i
                                                            class="fas fa-book"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Course</span>
                                                        <span
                                                            class="info-box-number">{{ $student->studentProfile->course_name ?? 'N/A' }}</span>
                                                        <span
                                                            class="info-box-text small">{{ $student->studentProfile->course_level_label ?? '' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-box bg-light">
                                                    <span class="info-box-icon bg-danger"><i
                                                            class="fas fa-calendar-alt"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Year of Study</span>
                                                        <span class="info-box-number">Year
                                                            {{ $student->studentProfile->year_of_study ?? 'N/A' }}</span>
                                                        <span class="info-box-text small">Expected Grad:
                                                            {{ $student->studentProfile->expected_graduation_year ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-box bg-light">
                                                    <span class="info-box-icon bg-primary"><i
                                                            class="fas fa-chart-line"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">CGPA</span>
                                                        @if ($student->studentProfile->cgpa)
                                                            <span
                                                                class="info-box-number">{{ number_format($student->studentProfile->cgpa, 2) }}
                                                                / 4.0</span>
                                                            <div class="progress progress-xs">
                                                                <div class="progress-bar bg-{{ $student->studentProfile->cgpa >= 3.0 ? 'success' : ($student->studentProfile->cgpa >= 2.0 ? 'warning' : 'danger') }}"
                                                                    style="width: {{ ($student->studentProfile->cgpa / 4.0) * 100 }}%">
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="info-box-number text-muted">Not
                                                                provided</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-box bg-light">
                                                    <span class="info-box-icon bg-secondary"><i
                                                            class="fas fa-map-pin"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Preferred Location</span>
                                                        <span
                                                            class="info-box-number">{{ $student->studentProfile->preferred_location ?? 'Anywhere' }}</span>
                                                        <span class="info-box-text small">Duration:
                                                            {{ $student->studentProfile->preferred_attachment_duration ?? 'Not specified' }}
                                                            months</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Attachment Timeline -->
                                        @if ($student->studentProfile->attachment_start_date || $student->studentProfile->attachment_end_date)
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-timeline mr-1"></i> Attachment Timeline
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4 text-center">
                                                            <p class="text-muted mb-0">Start Date</p>
                                                            <p class="h5">
                                                                {{ formatDate($student->studentProfile->attachment_start_date) ?? 'N/A' }}
                                                            </p>
                                                        </div>
                                                        <div class="col-md-4 text-center">
                                                            <p class="text-muted mb-0">End Date</p>
                                                            <p class="h5">
                                                                {{ formatDate($student->studentProfile->attachment_end_date) ?? 'N/A' }}
                                                            </p>
                                                        </div>
                                                        <div class="col-md-4 text-center">
                                                            <p class="text-muted mb-0">Duration</p>
                                                            <p class="h5">
                                                                {{ $student->studentProfile->attachment_duration ?? '0' }}
                                                                months
                                                            </p>
                                                        </div>
                                                    </div>
                                                    @if ($student->studentProfile->is_currently_attached)
                                                        <div class="progress mt-3">
                                                            <div class="progress-bar bg-success progress-bar-striped"
                                                                style="width: {{ $student->studentProfile->getProgressPercentageAttribute() }}%">
                                                                {{ $student->studentProfile->getProgressPercentageAttribute() }}%
                                                                Complete
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-warning">
                                            <h5><i class="icon fas fa-exclamation-triangle"></i> No Student Profile
                                            </h5>
                                            <p>This user doesn't have a student profile. They may have registered as a
                                                different user type or haven't completed their profile setup.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Skills & Interests Tab -->
                                <div class="tab-pane fade" id="skills" role="tabpanel"
                                    aria-labelledby="skills-tab">
                                    @if ($student->studentProfile)
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h3 class="card-title">
                                                            <i class="fas fa-tools mr-1"></i> Skills
                                                            @if ($student->studentProfile->skills && count($student->studentProfile->skills) > 0)
                                                                <span
                                                                    class="badge badge-primary ml-1">{{ count($student->studentProfile->skills) }}</span>
                                                            @endif
                                                        </h3>
                                                    </div>
                                                    <div class="card-body">
                                                        @if (
                                                            $student->studentProfile->skills &&
                                                                is_array($student->studentProfile->skills) &&
                                                                count($student->studentProfile->skills) > 0)
                                                            @foreach ($student->studentProfile->skills as $skill)
                                                                <span
                                                                    class="badge badge-primary p-2 mr-2 mb-2">{{ $skill }}</span>
                                                            @endforeach
                                                        @else
                                                            <p class="text-muted text-center py-3">
                                                                <i class="fas fa-info-circle mr-1"></i> No skills
                                                                listed
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h3 class="card-title">
                                                            <i class="fas fa-heart mr-1"></i> Interests
                                                            @if ($student->studentProfile->interests && count($student->studentProfile->interests) > 0)
                                                                <span
                                                                    class="badge badge-secondary ml-1">{{ count($student->studentProfile->interests) }}</span>
                                                            @endif
                                                        </h3>
                                                    </div>
                                                    <div class="card-body">
                                                        @if (
                                                            $student->studentProfile->interests &&
                                                                is_array($student->studentProfile->interests) &&
                                                                count($student->studentProfile->interests) > 0)
                                                            @foreach ($student->studentProfile->interests as $interest)
                                                                <span
                                                                    class="badge badge-secondary p-2 mr-2 mb-2">{{ $interest }}</span>
                                                            @endforeach
                                                        @else
                                                            <p class="text-muted text-center py-3">
                                                                <i class="fas fa-info-circle mr-1"></i> No interests
                                                                listed
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Profile Completeness Breakdown -->
                                        @if (method_exists($student->studentProfile, 'getProfileProgressBreakdown'))
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-chart-pie mr-1"></i> Profile Completion
                                                        Breakdown
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    @php
                                                        $breakdown = $student->studentProfile->getProfileProgressBreakdown();
                                                    @endphp
                                                    @foreach ($breakdown as $category => $data)
                                                        <div class="mb-3">
                                                            <div class="d-flex justify-content-between">
                                                                <span>{{ $data['label'] }}</span>
                                                                <span>{{ $data['percentage'] }}%</span>
                                                            </div>
                                                            <div class="progress progress-sm">
                                                                <div class="progress-bar bg-{{ $data['percentage'] >= 80 ? 'success' : ($data['percentage'] >= 50 ? 'warning' : 'danger') }}"
                                                                    style="width: {{ $data['percentage'] }}%"></div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-warning">
                                            <h5><i class="icon fas fa-exclamation-triangle"></i> No Skills Data</h5>
                                            <p>Student profile is incomplete. Skills and interests cannot be displayed.
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Applications Tab -->
                                <div class="tab-pane fade" id="applications" role="tabpanel"
                                    aria-labelledby="applications-tab">
                                    @if ($student->studentApplications && $student->studentApplications->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Opportunity</th>
                                                        <th>Organization</th>
                                                        <th>Match Score</th>
                                                        <th>Status</th>
                                                        <th>Applied Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($student->studentApplications as $application)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $application->opportunity?->title ?? 'N/A' }}</strong>
                                                                <div class="text-muted small">
                                                                    {{ $application->opportunity?->type ?? '' }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                {{ $application->organization?->name ?? 'N/A' }}
                                                            </td>
                                                            <td>
                                                                @if ($application->match_score)
                                                                    <span
                                                                        class="badge badge-{{ $application->match_score >= 85 ? 'success' : ($application->match_score >= 70 ? 'info' : 'warning') }} p-2">
                                                                        {{ $application->match_score }}%
                                                                    </span>
                                                                    @if ($application->match_quality)
                                                                        <div
                                                                            class="text-muted small mt-1 text-capitalize">
                                                                            {{ $application->match_quality }}
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <span class="text-muted">N/A</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                 {!! getApplicationStatusBadge($application->status) !!}
                                                            </td>
                                                            <td>
                                                                {{ formatDate($application->submitted_at ?? $application->created_at) }}
                                                                <div class="text-muted small">
                                                                    {{ timeAgo($application->submitted_at ?? $application->created_at) }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group">
                                                                    @if ($application->opportunity)
                                                                        <a href="{{ route('admin.opportunities.show', $application->opportunity->id) }}"
                                                                            class="btn btn-sm btn-info"
                                                                            title="View Opportunity">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                    @endif
                                                                    @if ($application->status === 'offered' && !$application->placement)
                                                                        <button class="btn btn-sm btn-success"
                                                                            title="Create Placement"
                                                                            wire:click="createPlacement({{ $application->id }})">
                                                                            <i class="fas fa-briefcase"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        @if ($student->studentApplications->count() > 10)
                                            <div class="text-center mt-3">
                                                <a href="{{ route('admin.applications.index', ['student_id' => $student->id]) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-list mr-1"></i> View All Applications
                                                    ({{ $student->studentApplications->count() }})
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-5">
                                            <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                                            <h5 class="text-muted">No Applications Found</h5>
                                            <p class="text-muted mb-3">This student hasn't applied for any
                                                opportunities
                                                yet.</p>
                                            @if ($student->studentProfile && $student->studentProfile->attachment_status === 'seeking')
                                                <button wire:click="matchStudent({{ $student->id }})"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-magic mr-1"></i> Find Matches
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Mentorship Tab -->
                                <div class="tab-pane fade" id="mentorship" role="tabpanel"
                                    aria-labelledby="mentorship-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-user-tie mr-1"></i> Active Mentorships
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    @if ($student->activeMentorships && $student->activeMentorships->count() > 0)
                                                        @foreach ($student->activeMentorships as $mentorship)
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="avatar-initials bg-info img-circle mr-3 d-flex align-items-center justify-content-center"
                                                                    style="width: 40px; height: 40px; color: white; font-weight: bold;">
                                                                    {{ getInitials($mentorship->mentor->full_name) }}
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <strong>{{ $mentorship->mentor->full_name }}</strong>
                                                                    <div class="text-muted small">
                                                                        {{ $mentorship->title }}
                                                                    </div>
                                                                    <div class="progress progress-xs mt-1">
                                                                        <div class="progress-bar bg-success"
                                                                            style="width: {{ $mentorship->progress_percentage }}%">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span
                                                                    class="badge badge-success">{{ $mentorship->progress_percentage }}%</span>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <p class="text-muted text-center py-3">
                                                            <i class="fas fa-info-circle mr-1"></i> No active
                                                            mentorships
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-history mr-1"></i> Completed Mentorships
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    @if ($student->mentorshipsAsMentee && $student->mentorshipsAsMentee->where('status', 'completed')->count() > 0)
                                                        @foreach ($student->mentorshipsAsMentee->where('status', 'completed') as $mentorship)
                                                            <div class="d-flex align-items-center mb-3">
                                                                <div class="avatar-initials bg-secondary img-circle mr-3 d-flex align-items-center justify-content-center"
                                                                    style="width: 40px; height: 40px; color: white; font-weight: bold;">
                                                                    {{ getInitials($mentorship->mentor->full_name) }}
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <strong>{{ $mentorship->mentor->full_name }}</strong>
                                                                    <div class="text-muted small">
                                                                        {{ $mentorship->title }}
                                                                    </div>
                                                                </div>
                                                                <span class="badge badge-secondary">Completed</span>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <p class="text-muted text-center py-3">
                                                            <i class="fas fa-info-circle mr-1"></i> No completed
                                                            mentorships
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($student->mentor)
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <strong>Note:</strong> This user is also a registered mentor with
                                            {{ $student->mentor->mentorships->count() }} mentorship relationships.
                                        </div>
                                    @endif
                                </div>

                                <!-- Activity Tab -->
                                <div class="tab-pane fade" id="activity" role="tabpanel"
                                    aria-labelledby="activity-tab">
                                    <div class="timeline">
                                        @php
                                            $activities = $student->activityLogs()->latest()->take(20)->get();
                                        @endphp

                                        @forelse ($activities as $activity)
                                            @php
                                                $date = $activity->created_at->format('Y-m-d');
                                            @endphp
                                            @if ($loop->first || $activities[$loop->index - 1]->created_at->format('Y-m-d') !== $date)
                                                <!-- Timeline time label -->
                                                <div class="time-label">
                                                    <span
                                                        class="bg-primary">{{ $activity->created_at->format('M d, Y') }}</span>
                                                </div>
                                            @endif

                                            <div>
                                                <i
                                                    class="fas {{ $activity->icon ?? 'fa-history' }} bg-{{ $activity->event === 'created' ? 'success' : ($activity->event === 'updated' ? 'warning' : ($activity->event === 'deleted' ? 'danger' : 'info')) }}"></i>
                                                <div class="timeline-item">
                                                    <span class="time">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ $activity->created_at->format('h:i A') }}
                                                    </span>
                                                    <h3 class="timeline-header">
                                                        <span class="text-capitalize">{{ $activity->log_name }}</span>
                                                        @if ($activity->causer)
                                                            <small class="ml-2">by
                                                                {{ $activity->causer->full_name }}</small>
                                                        @endif
                                                    </h3>
                                                    <div class="timeline-body">
                                                        {{ $activity->description }}
                                                        @if ($activity->event === 'updated' && isset($activity->properties['changes']))
                                                            <div class="mt-2">
                                                                <button class="btn btn-xs btn-default" type="button"
                                                                    data-toggle="collapse"
                                                                    data-target="#changes-{{ $activity->id }}"
                                                                    aria-expanded="false">
                                                                    <i class="fas fa-code-branch mr-1"></i> View
                                                                    Changes
                                                                </button>
                                                                <div class="collapse mt-2"
                                                                    id="changes-{{ $activity->id }}">
                                                                    <div class="card card-body p-2 bg-light">
                                                                        <pre class="mb-0 small">{{ json_encode($activity->properties['changes'], JSON_PRETTY_PRINT) }}</pre>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-5">
                                                <i class="fas fa-history fa-4x text-muted mb-3"></i>
                                                <h5 class="text-muted">No Activity Logs</h5>
                                                <p class="text-muted">This student hasn't performed any activities yet.
                                                </p>
                                            </div>
                                        @endforelse

                                        @if ($activities->count() >= 20)
                                            <div class="text-center mt-3">
                                                <a href="{{ route('admin.activity-logs', ['causer_id' => $student->id]) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-history mr-1"></i> View All Activity
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Match Student Modal -->
    @if ($showMatchModal && $selectedStudentForMatch)
        <div class="modal fade show" id="matchModal" tabindex="-1" role="dialog" aria-modal="true"
            wire:ignore.self style="display: block; background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title">
                            <i class="fas fa-magic mr-2"></i> Find Matches for
                            {{ $selectedStudentForMatch->full_name }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            wire:click="$set('showMatchModal', false)">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <!-- Modal Body with max-height and scrolling -->
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        @if ($matchLoading)
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <h5>Finding matches...</h5>
                                <p class="text-muted">Analyzing opportunities based on student's profile</p>
                            </div>
                        @else
                            <!-- Student Profile Summary - Sticky within modal -->
                            <div class="sticky-top bg-light p-3 mb-4 rounded" style="top: 0; z-index: 10;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-user mr-1"></i> Student Profile Summary
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Course:</strong>
                                                <p class="mb-0">
                                                    {{ $selectedStudentForMatch->studentProfile->course_name }}</p>
                                            </div>
                                            <div class="col-md-2">
                                                <strong>Year:</strong>
                                                <p class="mb-0">Year
                                                    {{ $selectedStudentForMatch->studentProfile->year_of_study }}</p>
                                            </div>
                                            <div class="col-md-2">
                                                <strong>CGPA:</strong>
                                                <p class="mb-0">
                                                    {{ $selectedStudentForMatch->studentProfile->cgpa ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-2">
                                                <strong>Location:</strong>
                                                <p class="mb-0">
                                                    {{ $selectedStudentForMatch->studentProfile->preferred_location ?? ($selectedStudentForMatch->county ?? 'Anywhere') }}
                                                </p>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Skills:</strong>
                                                <p class="mb-0">
                                                    @if ($selectedStudentForMatch->studentProfile->skills && count($selectedStudentForMatch->studentProfile->skills) > 0)
                                                        {{ implode(', ', array_slice($selectedStudentForMatch->studentProfile->skills, 0, 3)) }}
                                                        @if (count($selectedStudentForMatch->studentProfile->skills) > 3)
                                                            +{{ count($selectedStudentForMatch->studentProfile->skills) - 3 }}
                                                            more
                                                        @endif
                                                    @else
                                                        No skills listed
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (count($studentMatches) > 0)
                                <!-- Selection Controls - Sticky below summary -->
                                <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-2 rounded border"
                                    style="position: sticky; top: 100px; z-index: 9;">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm"
                                            wire:click="clearSelectedMatches" wire:loading.attr="disabled">
                                            <i class="fas fa-times mr-1"></i> Clear Selection
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm"
                                            wire:click="selectAllMatches" wire:loading.attr="disabled">
                                            <i class="fas fa-check-double mr-1"></i> Select All
                                        </button>
                                    </div>
                                    <div>
                                        <span class="text-muted" wire:key="selected-count">
                                            <span x-data="{ count: @entangle('selectedMatches') }" x-text="count.length"></span> of
                                            {{ count($studentMatches) }} selected
                                        </span>
                                        @if (count($studentMatches) >= 10)
                                            <span class="badge badge-info ml-2">
                                                <i class="fas fa-info-circle mr-1"></i> Showing top 10 matches
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Scrollable Matches Container -->
                                <div class="matches-container"
                                    style="max-height: calc(70vh - 200px); overflow-y: auto;">
                                    <div class="row">
                                        @foreach ($studentMatches as $index => $match)
                                            <div class="col-md-6 mb-3"
                                                wire:key="match-{{ $index }}-{{ $match['opportunity']->id }}">
                                                <div
                                                    class="card card-{{ $match['score'] >= 85 ? 'success' : ($match['score'] >= 70 ? 'info' : 'warning') }} card-outline h-100">
                                                    <div class="card-header">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h5 class="card-title mb-0">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox"
                                                                        class="custom-control-input"
                                                                        wire:model="selectedMatches"
                                                                        value="{{ $index }}"
                                                                        id="match-{{ $index }}">
                                                                    <label
                                                                        class="custom-control-label font-weight-bold"
                                                                        for="match-{{ $index }}"
                                                                        style="cursor: pointer;">
                                                                        {{ \Str::limit($match['opportunity']->title, 40) }}
                                                                    </label>
                                                                </div>
                                                            </h5>
                                                            <span
                                                                class="badge badge-{{ $match['score'] >= 85 ? 'success' : ($match['score'] >= 70 ? 'info' : 'warning') }} p-2">
                                                                <i class="fas fa-star mr-1"></i>
                                                                {{ $match['score'] }}%
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-7">
                                                                <p class="mb-1 small">
                                                                    <i class="fas fa-building mr-1 text-muted"></i>
                                                                    {{ $match['opportunity']->organization->name }}
                                                                </p>
                                                                <p class="mb-1 small">
                                                                    <i
                                                                        class="fas fa-map-marker-alt mr-1 text-muted"></i>
                                                                    {{ $match['opportunity']->location ?? ($match['opportunity']->county ?? 'N/A') }}
                                                                    @if ($match['opportunity']->work_type === 'remote')
                                                                        <span
                                                                            class="badge badge-info ml-1">Remote</span>
                                                                    @endif
                                                                </p>
                                                                <p class="mb-1 small">
                                                                    <i class="fas fa-clock mr-1 text-muted"></i>
                                                                    Deadline:
                                                                    {{ $match['opportunity']->deadline ? $match['opportunity']->deadline->format('M d, Y') : 'N/A' }}
                                                                </p>
                                                                <p class="mb-1 small">
                                                                    <i class="fas fa-users mr-1 text-muted"></i>
                                                                    {{ $match['opportunity']->slots_available }} slots
                                                                    available
                                                                </p>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="progress-group mb-2">
                                                                    <span class="progress-text small">Match
                                                                        Details</span>
                                                                    @if (isset($match['match_criteria']) && count($match['match_criteria']) > 0)
                                                                        <div>
                                                                            @foreach ($match['match_criteria'] as $criteria)
                                                                                <span
                                                                                    class="badge badge-success mr-1 mb-1"
                                                                                    style="font-size: 0.7rem;">{{ $criteria }}</span>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                @if (isset($match['details']))
                                                                    <button class="btn btn-sm btn-default btn-block"
                                                                        type="button" data-toggle="collapse"
                                                                        data-target="#details-{{ $index }}"
                                                                        aria-expanded="false">
                                                                        <i class="fas fa-chart-pie mr-1"></i> View
                                                                        Breakdown
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Match Details Breakdown - Collapsible -->
                                                        @if (isset($match['details']))
                                                            <div class="collapse mt-2"
                                                                id="details-{{ $index }}">
                                                                <div class="card card-body p-2 bg-light">
                                                                    <div class="row">
                                                                        @foreach ($match['details'] as $key => $detail)
                                                                            @if (isset($detail['score']))
                                                                                <div class="col-md-6">
                                                                                    <div class="mb-1">
                                                                                        <div
                                                                                            class="d-flex justify-content-between small">
                                                                                            <span
                                                                                                class="text-capitalize">{{ $key }}:</span>
                                                                                            <span
                                                                                                class="badge badge-{{ $detail['status'] === 'excellent' ? 'success' : ($detail['status'] === 'good' ? 'info' : ($detail['status'] === 'fair' ? 'warning' : 'danger')) }}">
                                                                                                {{ $detail['score'] }}%
                                                                                            </span>
                                                                                        </div>
                                                                                        <div
                                                                                            class="progress progress-xs">
                                                                                            <div class="progress-bar bg-{{ $detail['status'] === 'excellent' ? 'success' : ($detail['status'] === 'good' ? 'info' : ($detail['status'] === 'fair' ? 'warning' : 'danger')) }}"
                                                                                                style="width: {{ $detail['score'] }}%">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card-footer py-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar-alt mr-1"></i>
                                                            Posted:
                                                            {{ $match['opportunity']->created_at ? $match['opportunity']->created_at->diffForHumans() : 'N/A' }}
                                                        </small>
                                                        <div class="float-right">
                                                            <a href="{{ route('admin.opportunities.show', $match['opportunity']->id) }}"
                                                                target="_blank" class="btn btn-xs btn-default">
                                                                <i class="fas fa-external-link-alt mr-1"></i> View
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Add separator after every 2 items on large screens -->
                                            @if (($index + 1) % 2 == 0 && !$loop->last)
                                                <div class="w-100 d-none d-md-block"></div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <!-- Show total count at bottom -->
                                    @if (count($studentMatches) > 0)
                                        <div class="text-center text-muted small mt-3">
                                            <i class="fas fa-list mr-1"></i> Showing {{ count($studentMatches) }}
                                            matches
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Matches Found</h5>
                                    <p class="text-muted mb-3">No suitable opportunities found for this student at the
                                        moment.</p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-primary" wire:click="refreshMatches">
                                            <i class="fas fa-sync-alt mr-1"></i> Refresh
                                        </button>
                                        <a href="{{ route('admin.opportunities.create') }}" class="btn btn-success">
                                            <i class="fas fa-plus-circle mr-1"></i> Create New Opportunity
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Modal Footer - Sticky at bottom -->
                    @if (!$matchLoading && count($studentMatches) > 0)
                        <div class="modal-footer">
                            <div class="d-flex justify-content-between w-100">
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Selected: <span x-data="{ count: @entangle('selectedMatches') }" x-text="count.length"></span>
                                        of {{ count($studentMatches) }}
                                    </small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-default mr-2" data-dismiss="modal"
                                        wire:click="$set('showMatchModal', false)">
                                        <i class="fas fa-times mr-1"></i> Cancel
                                    </button>
                                    <button type="button" class="btn btn-primary" wire:click="saveMatches"
                                        wire:loading.attr="disabled" x-data="{ count: @entangle('selectedMatches') }"
                                        :disabled="count.length === 0">
                                        <span wire:loading.remove wire:target="saveMatches">
                                            <i class="fas fa-save mr-1"></i>
                                            Save Selected Matches (<span x-text="count.length"></span>)
                                        </span>
                                        <span wire:loading wire:target="saveMatches">
                                            <i class="fas fa-spinner fa-spin mr-1"></i> Saving...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    @push('styles')
        <style>
            /* Modal scrolling styles */
            .modal-dialog-scrollable .modal-body {
                max-height: calc(100vh - 200px);
                overflow-y: auto;
            }

            /* Custom scrollbar for better UX */
            .modal-body::-webkit-scrollbar {
                width: 8px;
            }

            .modal-body::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 4px;
            }

            .modal-body::-webkit-scrollbar-thumb {
                background: #888;
                border-radius: 4px;
            }

            .modal-body::-webkit-scrollbar-thumb:hover {
                background: #555;
            }

            /* Matches container scrollbar */
            .matches-container::-webkit-scrollbar {
                width: 6px;
            }

            .matches-container::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 3px;
            }

            .matches-container::-webkit-scrollbar-thumb {
                background: #ccc;
                border-radius: 3px;
            }

            .matches-container::-webkit-scrollbar-thumb:hover {
                background: #999;
            }

            /* Sticky elements */
            .sticky-top {
                position: sticky;
                top: 0;
                z-index: 10;
                background-color: #f8f9fa;
                border-bottom: 1px solid #dee2e6;
            }

            /* Card hover effect */
            .card:hover {
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                transition: box-shadow 0.3s ease;
            }

            /* Disabled button state */
            .btn-primary:disabled {
                opacity: 0.65;
                cursor: not-allowed;
            }

            /* Modal backdrop */
            .modal-backdrop {
                opacity: 0.5;
            }

            /* Ensure modal is above backdrop */
            .modal {
                background-color: transparent;
                z-index: 1050;
            }

            .avatar-initials {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
            }

            .timeline {
                position: relative;
                margin: 0 0 30px 0;
                padding: 0;
                list-style: none;
            }

            .timeline:before {
                content: '';
                position: absolute;
                top: 0;
                bottom: 0;
                width: 4px;
                background: #ddd;
                left: 31px;
                margin: 0;
                border-radius: 2px;
            }

            .timeline>div {
                position: relative;
                margin-right: 10px;
                margin-bottom: 15px;
            }

            .timeline>div>.timeline-item {
                margin-top: 0;
                background: #fff;
                color: #444;
                margin-left: 60px;
                margin-right: 15px;
                padding: 0;
                position: relative;
                border-radius: 3px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .timeline>div>.timeline-item>.time {
                color: #999;
                float: right;
                padding: 10px;
                font-size: 12px;
            }

            .timeline>div>.timeline-item>.timeline-header {
                margin: 0;
                color: #555;
                border-bottom: 1px solid #f4f4f4;
                padding: 10px;
                font-size: 16px;
                line-height: 1.1;
            }

            .timeline>div>.timeline-item>.timeline-body {
                padding: 10px;
            }

            .timeline>div>.fa,
            .timeline>div>.fas,
            .timeline>div>.far,
            .timeline>div>.fab {
                width: 30px;
                height: 30px;
                background: #adb5bd;
                border-radius: 50%;
                line-height: 30px;
                font-size: 15px;
                color: #fff;
                text-align: center;
                position: absolute;
                top: 0;
            }

            .time-label>span {
                font-weight: 600;
                padding: 5px;
                display: inline-block;
                background-color: #fff;
                border-radius: 4px;
                margin-left: 20px;
            }

            .time-label {
                margin-left: 20px !important;
            }

            .progress-xs {
                height: 6px;
            }

            .modal-xl {
                max-width: 90%;
            }

            @media (min-width: 1200px) {
                .modal-xl {
                    max-width: 1140px;
                }
            }

            .card-outline.card-success {
                border-top: 3px solid #28a745;
            }

            .card-outline.card-info {
                border-top: 3px solid #17a2b8;
            }

            .card-outline.card-warning {
                border-top: 3px solid #ffc107;
            }

            .nav-tabs .nav-link.active {
                border-bottom: 2px solid #007bff;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                // SweetAlert Confirmation
                Livewire.on('swal:confirm', (data) => {
                    Swal.fire({
                        title: data.title,
                        text: data.text,
                        icon: data.icon || 'warning',
                        showCancelButton: true,
                        confirmButtonColor: data.danger ? '#d33' : '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: data.confirmButtonText || 'Yes',
                        cancelButtonText: data.cancelButtonText || 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (data.params) {
                                Livewire.dispatch(data.method, ...data.params);
                            } else {
                                Livewire.dispatch(data.method);
                            }
                        }
                    });
                });

                // SweetAlert Success
                Livewire.on('swal:success', (data) => {
                    Swal.fire({
                        title: data.title || 'Success!',
                        text: data.text,
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: true
                    }).then(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    });
                });

                // SweetAlert Error
                Livewire.on('swal:error', (data) => {
                    Swal.fire({
                        title: data.title || 'Error!',
                        text: data.text,
                        icon: 'error',
                        timer: 5000,
                        showConfirmButton: true
                    });
                });

                // SweetAlert Warning
                Livewire.on('swal:warning', (data) => {
                    Swal.fire({
                        title: data.title || 'Warning!',
                        text: data.text,
                        icon: 'warning',
                        timer: 4000,
                        showConfirmButton: true
                    });
                });

                // SweetAlert Info
                Livewire.on('swal:info', (data) => {
                    Swal.fire({
                        title: data.title || 'Info',
                        text: data.text,
                        icon: 'info',
                        timer: 3000,
                        showConfirmButton: true
                    });
                });

                // Toast notification handlers (for non-critical notifications)
                Livewire.on('toastr:success', ({ message }) => {
                    toastr.success(message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000
                    });
                });

                Livewire.on('toastr:error', ({ message }) => {
                    toastr.error(message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000
                    });
                });

                Livewire.on('toastr:warning', ({ message }) => {
                    toastr.warning(message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000
                    });
                });

                Livewire.on('toastr:info', ({ message }) => {
                    toastr.info(message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000
                    });
                });

                // Redirect after delete
                Livewire.on('redirect-after-delete', (data) => {
                    setTimeout(() => {
                        window.location.href = data.url;
                    }, 2000);
                });

                // Force refresh counts when selection changes
                Livewire.on('selection-updated', () => {
                    Livewire.dispatch('$refresh');
                });

                // Handle modal visibility
                Livewire.on('showMatchModal', () => {
                    $('#matchModal').modal('show');
                });

                Livewire.on('hideMatchModal', () => {
                    $('#matchModal').modal('hide');
                });
            });

            // Clean up modal backdrop when Livewire updates
            document.addEventListener('livewire:updated', () => {
                if (!document.getElementById('matchModal')?.classList.contains('show')) {
                    $('.modal-backdrop').remove();
                }
            });
        </script>
    @endpush
</div>
