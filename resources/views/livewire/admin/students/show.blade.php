<div>
    {{-- Success is as dangerous as failure. --}}

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <!-- Profile Card -->
                    <div class="card card-primary">
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
                                <span class="badge badge-success">Student</span>
                                @if ($student->is_verified)
                                    <span class="badge badge-info ml-1">Verified</span>
                                @endif
                                @if ($student->is_active)
                                    <span class="badge badge-success ml-1">Active</span>
                                @else
                                    <span class="badge badge-secondary ml-1">Inactive</span>
                                @endif
                            </p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Email</b>
                                    <a class="float-right">{{ $student->email }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Phone</b>
                                    <span class="float-right">
                                        {{ $student->phone ? formatPhoneNumber($student->phone) : 'N/A' }}
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Date of Birth</b>
                                    <span class="float-right">
                                        {{ $student->date_of_birth ? formatDate($student->date_of_birth) : 'N/A' }}
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Gender</b>
                                    <span class="float-right text-capitalize">{{ $student->gender ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Location</b>
                                    <span class="float-right">
                                        {{ $student->county ?? 'N/A' }}{{ $student->town ? ', ' . $student->town : '' }}
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Joined</b>
                                    <span class="float-right">{{ formatDateTime($student->created_at) }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Last Login</b>
                                    <span class="float-right">
                                        {{ $student->last_login_at ? formatDateTime($student->last_login_at) : 'Never' }}
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
                                    <a href="mailto:{{ $student->email }}" class="btn btn-primary btn-block">
                                        <i class="fas fa-envelope mr-1"></i> Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            @if ($student->is_active)
                                <button wire:click="toggleActive" wire:confirm="Deactivate this student?"
                                    class="btn btn-warning btn-block mb-2">
                                    <i class="fas fa-ban mr-1"></i> Deactivate Student
                                </button>
                            @else
                                <button wire:click="toggleActive" class="btn btn-success btn-block mb-2">
                                    <i class="fas fa-check mr-1"></i> Activate Student
                                </button>
                            @endif

                            @if (!$student->is_verified)
                                <button wire:click="verifyUser" class="btn btn-info btn-block mb-2">
                                    <i class="fas fa-check-circle mr-1"></i> Verify Account
                                </button>
                            @endif

                            <div class="dropdown mb-2">
                                <button class="btn btn-secondary btn-block dropdown-toggle" type="button"
                                    data-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-exchange-alt mr-1"></i> Change Status
                                </button>
                                <div class="dropdown-menu w-100">
                                    @foreach (['seeking', 'applied', 'interviewing', 'placed', 'completed'] as $status)
                                        @if ($student->studentProfile?->attachment_status !== $status)
                                            <button class="dropdown-item"
                                                wire:click="updateAttachmentStatus('{{ $status }}')"
                                                wire:confirm="Change status to {{ ucfirst($status) }}?">
                                                <i
                                                    class="fas fa-{{ $status === 'placed' ? 'briefcase' : ($status === 'seeking' ? 'search' : 'file-alt') }} mr-2"></i>
                                                {{ ucfirst($status) }}
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <button wire:click="deleteStudent"
                                wire:confirm="Are you sure you want to delete this student? This action cannot be undone."
                                class="btn btn-danger btn-block">
                                <i class="fas fa-trash mr-1"></i> Delete Student
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Student Profile Information -->
                    @if ($student->studentProfile)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Academic Information</h3>
                                <div class="card-tools">
                                    <span
                                        class="badge badge-{{ $student->studentProfile->attachment_status === 'placed' ? 'success' : ($student->studentProfile->attachment_status === 'seeking' ? 'warning' : 'info') }}">
                                        {{ $student->studentProfile->attachment_status_label }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="callout callout-info">
                                            <h5><i class="fas fa-graduation-cap mr-1"></i> Registration Details</h5>
                                            <p><strong>Registration Number:</strong><br>
                                                {{ $student->studentProfile->student_reg_number ?? 'N/A' }}</p>

                                            <p><strong>Institution:</strong><br>
                                                {{ $student->studentProfile->institution_name ?? 'N/A' }}</p>

                                            <p><strong>Institution Type:</strong><br>
                                                {{ $student->studentProfile->institution_type_label ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="callout callout-success">
                                            <h5><i class="fas fa-book mr-1"></i> Course Details</h5>
                                            <p><strong>Course:</strong><br>
                                                {{ $student->studentProfile->course_name ?? 'N/A' }}</p>

                                            <p><strong>Course Level:</strong><br>
                                                {{ $student->studentProfile->course_level_label ?? 'N/A' }}</p>

                                            <p><strong>Year of Study:</strong><br>
                                                Year {{ $student->studentProfile->year_of_study ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="callout callout-warning">
                                            <h5><i class="fas fa-chart-line mr-1"></i> Academic Performance</h5>
                                            <p><strong>CGPA:</strong><br>
                                                @if ($student->studentProfile->cgpa)
                                                    <span
                                                        class="badge badge-{{ $student->studentProfile->cgpa >= 3.0 ? 'success' : ($student->studentProfile->cgpa >= 2.0 ? 'warning' : 'danger') }}">
                                                        {{ number_format($student->studentProfile->cgpa, 2) }}
                                                    </span>
                                                    <span class="text-muted">
                                                        ({{ $student->studentProfile->cgpa_percentage ?? '0' }}%)
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </p>

                                            <p><strong>Expected Graduation:</strong><br>
                                                {{ $student->studentProfile->expected_graduation_year ?? 'N/A' }}</p>

                                            <p><strong>Years to Graduation:</strong><br>
                                                {{ $student->studentProfile->years_to_graduation ?? 'N/A' }} year(s)
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="callout callout-primary">
                                            <h5><i class="fas fa-briefcase mr-1"></i> Attachment Details</h5>
                                            <p><strong>Status:</strong><br>
                                                <span
                                                    class="badge badge-{{ $student->studentProfile->attachment_status === 'placed' ? 'success' : ($student->studentProfile->attachment_status === 'seeking' ? 'warning' : 'info') }}">
                                                    {{ $student->studentProfile->attachment_status_label }}
                                                </span>
                                            </p>

                                            @if ($student->studentProfile->attachment_status === 'placed')
                                                <p><strong>Attachment Period:</strong><br>
                                                    {{ formatDate($student->studentProfile->attachment_start_date) }}
                                                    to
                                                    {{ formatDate($student->studentProfile->attachment_end_date) }}
                                                </p>
                                                <p><strong>Duration:</strong><br>
                                                    {{ $student->studentProfile->attachment_duration ?? '0' }} months
                                                </p>
                                                @if ($student->studentProfile->is_currently_attached)
                                                    <p><strong>Remaining:</strong><br>
                                                        {{ $student->studentProfile->getRemainingAttachmentDays() ?? '0' }}
                                                        days
                                                    </p>
                                                @endif
                                            @endif

                                            <p><strong>Preferred Duration:</strong><br>
                                                {{ $student->studentProfile->preferred_attachment_duration ?? '0' }}
                                                months
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Skills and Interests -->
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-tools mr-1"></i> Skills
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                @if ($student->studentProfile->skills && is_array($student->studentProfile->skills))
                                                    @foreach ($student->studentProfile->skills as $skill)
                                                        <span
                                                            class="badge badge-primary mr-1 mb-1">{{ $skill }}</span>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted">No skills listed</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-heart mr-1"></i> Interests
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                @if ($student->studentProfile->interests && is_array($student->studentProfile->interests))
                                                    @foreach ($student->studentProfile->interests as $interest)
                                                        <span
                                                            class="badge badge-secondary mr-1 mb-1">{{ $interest }}</span>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted">No interests listed</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Documents -->
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <i class="fas fa-file mr-1"></i> Documents
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="info-box bg-info">
                                                            <span class="info-box-icon"><i
                                                                    class="fas fa-file-alt"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">CV</span>
                                                                @if ($student->studentProfile->cv_url)
                                                                    <a href="{{ $student->studentProfile->cv_url }}"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-light mt-2">
                                                                        <i class="fas fa-download mr-1"></i> Download
                                                                    </a>
                                                                @else
                                                                    <span class="badge badge-warning">Not
                                                                        uploaded</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-box bg-success">
                                                            <span class="info-box-icon"><i
                                                                    class="fas fa-file-contract"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Transcript</span>
                                                                @if ($student->studentProfile->transcript_url)
                                                                    <a href="{{ $student->studentProfile->transcript_url }}"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-light mt-2">
                                                                        <i class="fas fa-download mr-1"></i> Download
                                                                    </a>
                                                                @else
                                                                    <span class="badge badge-warning">Not
                                                                        uploaded</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-box bg-warning">
                                                            <span class="info-box-icon"><i
                                                                    class="fas fa-map-marker-alt"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text">Preferred Location</span>
                                                                <span class="info-box-number">
                                                                    {{ $student->studentProfile->preferred_location ?? 'Anywhere' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Academic Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <h5><i class="icon fas fa-exclamation-triangle"></i> No Student Profile</h5>
                                    <p>This user doesn't have a student profile. They may have registered as a different
                                        user type.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Applications -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-alt mr-1"></i> Recent Applications
                                @if ($student->applications->count() > 0)
                                    <span class="badge badge-info">{{ $student->applications->count() }}</span>
                                @endif
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($student->applications->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Opportunity</th>
                                                <th>Company</th>
                                                <th>Status</th>
                                                <th>Applied Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($student->applications as $application)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $application->opportunity?->title ?? 'N/A' }}</strong>
                                                        <div class="text-muted small">
                                                            {{ $application->opportunity?->opportunity_type_label ?? '' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {{ $application->opportunity?->employer?->company_name ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        {!! getApplicationStatusBadge($application->status) !!}
                                                        @if ($application->interview_scheduled_at)
                                                            <div class="text-muted small">
                                                                Interview:
                                                                {{ formatDate($application->interview_scheduled_at) }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ formatDate($application->submitted_at ?? $application->created_at) }}
                                                        <div class="text-muted small">
                                                            {{ timeAgo($application->submitted_at ?? $application->created_at) }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if ($application->opportunity)
                                                            <a href="{{ route('admin.opportunities.show', $application->opportunity->id) }}"
                                                                class="btn btn-sm btn-info" title="View Opportunity">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if ($student->applications->count() > 10)
                                    <div class="text-center mt-3">
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            View All Applications ({{ $student->applications->count() }})
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No applications found</h5>
                                    <p class="text-muted">This student hasn't applied for any opportunities yet.</p>
                                    @if ($student->studentProfile?->attachment_status === 'seeking')
                                        <a href="{{ route('admin.opportunities.index') }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-search mr-1"></i> Browse Opportunities
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- System Info -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> System Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>User ID:</strong> {{ $student->id }}</p>
                                    <p><strong>Account Type:</strong>
                                        <span class="badge badge-success">Student</span>
                                    </p>
                                    <p><strong>Email Verified:</strong>
                                        @if ($student->email_verified_at)
                                            <span class="badge badge-success">Yes -
                                                {{ formatDate($student->email_verified_at) }}
                                            </span>
                                        @else
                                            <span class="badge badge-warning">No</span>
                                        @endif
                                    </p>
                                    <p><strong>Two-Factor Enabled:</strong>
                                        @if ($student->two_factor_secret)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Account Created:</strong> {{ formatDateTime($student->created_at) }}</p>
                                    <p><strong>Last Updated:</strong> {{ formatDateTime($student->updated_at) }}</p>
                                    <p><strong>Profile Picture:</strong>
                                        @if ($student->profile)
                                            <span class="badge badge-success">Uploaded</span>
                                        @else
                                            <span class="badge badge-secondary">Not uploaded</span>
                                        @endif
                                    </p>
                                    <p><strong>Profile Completeness:</strong>
                                        @if ($student->studentProfile)
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-success"
                                                    style="width: {{ $student->studentProfile->profile_completeness }}%">
                                                    {{ $student->studentProfile->profile_completeness }}%
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge badge-danger">No Profile</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

                // Confirm before delete
                window.addEventListener('confirm', event => {
                    if (!confirm(event.detail.message)) {
                        event.preventDefault();
                    }
                });
            });
        </script>
    @endpush

</div>
