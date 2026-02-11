<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <!-- Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Opportunity Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.opportunities.index') }}">Opportunities</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $opportunity->title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <!-- Opportunity Details Card -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ $opportunity->title }}</h3>
                            <div class="card-tools">
                                {!! getOpportunityStatusBadge($opportunity->status) !!}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-building mr-2"></i>Company Details</h5>
                                    <p><strong>Company:</strong> {{ $opportunity->organization->name ?? 'N/A' }}</p>
                                    <p><strong>Email:</strong> {{ $opportunity->organization->email ?? 'N/A' }}</p>
                                    <p><strong>Phone:</strong> {{ $opportunity->organization->phone ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-info-circle mr-2"></i>Opportunity Details</h5>
                                    <p><strong>Type:</strong> {{ $opportunity->opportunity_type_label }}</p>
                                    <p><strong>Employment Type:</strong> {{ $opportunity->employment_type_label }}</p>
                                    <p><strong>Duration:</strong> {{ $opportunity->duration_label }}</p>
                                </div>
                            </div>

                            <h5><i class="fas fa-map-marker-alt mr-2"></i>Location Details</h5>
                            <p><strong>Location Type:</strong> {{ $opportunity->location_type }}</p>
                            <p><strong>Full Location:</strong> {{ $opportunity->full_location }}</p>

                            <h5 class="mt-4"><i class="fas fa-calendar-alt mr-2"></i>Timeline</h5>
                            <p><strong>Start Date:</strong> {{ formatDate($opportunity->start_date) }}</p>
                            <p><strong>End Date:</strong> {{ formatDate($opportunity->end_date) }}</p>
                            <p><strong>Application Deadline:</strong>
                                <span
                                    class="{{ $opportunity->application_deadline_passed ? 'text-danger' : 'text-success' }}">
                                    {{ formatDate($opportunity->application_deadline) }}
                                    ({{ $opportunity->days_until_deadline }} days remaining)
                                </span>
                            </p>

                            <h5 class="mt-4"><i class="fas fa-money-bill-wave mr-2"></i>Stipend & Benefits</h5>
                            <p><strong>Stipend:</strong> {{ $opportunity->stipend_formatted }}</p>
                            @if ($opportunity->other_benefits && is_array($opportunity->other_benefits))
                                <p><strong>Other Benefits:</strong></p>
                                <ul>
                                    @foreach ($opportunity->other_benefits as $benefit)
                                        <li>{{ $benefit }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <h5 class="mt-4"><i class="fas fa-list-alt mr-2"></i>Description</h5>
                            <div class="bg-light p-3 rounded">
                                {!! nl2br(e($opportunity->description)) !!}
                            </div>

                            @if ($opportunity->responsibilities)
                                <h5 class="mt-4"><i class="fas fa-tasks mr-2"></i>Responsibilities</h5>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($opportunity->responsibilities)) !!}
                                </div>
                            @endif

                            @if ($opportunity->requirements)
                                <h5 class="mt-4"><i class="fas fa-list-check mr-2"></i>Requirements</h5>
                                <div class="bg-light p-3 rounded">
                                    {!! $opportunity->requirements !!}
                                </div>
                            @endif

                            @if ($opportunity->benefits)
                                <h5 class="mt-4"><i class="fas fa-gift mr-2"></i>Benefits</h5>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($opportunity->benefits)) !!}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Applications Card -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-alt mr-1"></i> Applications
                                <span class="badge badge-info">{{ $opportunity->applications->count() }}</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($opportunity->applications->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Status</th>
                                                <th>Applied Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($opportunity->applications as $application)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="mr-2">
                                                                @php
                                                                    $initials = getInitials(
                                                                        $application->student->full_name,
                                                                    );
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
                                                                            crc32($application->student->email) %
                                                                                count($colors)
                                                                        ];
                                                                @endphp
                                                                <div class="avatar-initials bg-{{ $color }} img-circle"
                                                                    style="width: 30px; height: 30px; line-height: 30px; text-align: center; color: white; font-weight: bold; font-size: 12px;">
                                                                    {{ $initials }}
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <strong>{{ $application->student->full_name }}</strong>
                                                                <div class="text-muted small">
                                                                    {{ $application->student->studentProfile?->student_reg_number ?? '' }}
                                                                </div>
                                                                <div class="text-muted small">
                                                                    {{ $application->student->studentProfile?->institution_name ?? '' }}
                                                                </div>
                                                            </div>
                                                        </div>
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
                                                        <a href="{{ route('admin.users.show', $application->student->id) }}"
                                                            class="btn btn-sm btn-info" title="View Student">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-sm btn-warning"
                                                            title="View Application">
                                                            <i class="fas fa-file-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No applications yet</h5>
                                    <p class="text-muted">No students have applied for this opportunity yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Quick Stats Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Stats</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Slots Status</span>
                                    <span class="info-box-number">
                                        {{ $opportunity->slots_filled }} / {{ $opportunity->slots_available }}
                                        @if ($opportunity->slots_available > 0)
                                            ({{ round(($opportunity->slots_filled / $opportunity->slots_available) * 100) }}%)
                                        @endif
                                    </span>
                                    <div class="progress">
                                        <div class="progress-bar bg-{{ $opportunity->slots_filled >= $opportunity->slots_available ? 'danger' : 'success' }}"
                                            style="width: {{ ($opportunity->slots_filled / max(1, $opportunity->slots_available)) * 100 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Applications</span>
                                    <span class="info-box-number">{{ $opportunity->applications_count }}</span>
                                </div>
                            </div>

                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-warning"><i class="fas fa-eye"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Views</span>
                                    <span class="info-box-number">{{ $opportunity->views }}</span>
                                </div>
                            </div>

                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-primary"><i class="fas fa-calendar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Duration</span>
                                    <span class="info-box-number">{{ $opportunity->duration_label }}</span>
                                </div>
                            </div>

                            @if ($opportunity->is_remote || $opportunity->is_hybrid)
                                <div class="info-box mb-3">
                                    <span class="info-box-icon bg-secondary"><i
                                            class="fas fa-laptop-house"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Work Mode</span>
                                        <span class="info-box-number">
                                            @if ($opportunity->is_remote && $opportunity->is_hybrid)
                                                Hybrid
                                            @elseif($opportunity->is_remote)
                                                Remote
                                            @elseif($opportunity->is_hybrid)
                                                Hybrid
                                            @else
                                                On-site
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <a href="{{ route('admin.opportunities.index') }}"
                                        class="btn btn-default btn-block">
                                        <i class="fas fa-arrow-left mr-1"></i> Back
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('admin.opportunities.edit', $opportunity->id) }}"
                                        class="btn btn-primary btn-block">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                </div>
                            </div>

                            <div class="mt-3">
                                @if ($opportunity->status === 'pending_approval')
                                    <button wire:click="publishOpportunity" wire:confirm="Publish this opportunity?"
                                        class="btn btn-success btn-block mb-2">
                                        <i class="fas fa-check mr-1"></i> Publish
                                    </button>
                                @endif

                                @if ($opportunity->status === 'published')
                                    <button wire:click="closeOpportunity" wire:confirm="Close this opportunity?"
                                        class="btn btn-warning btn-block mb-2">
                                        <i class="fas fa-times mr-1"></i> Close
                                    </button>

                                    <button wire:click="markAsFilled" wire:confirm="Mark as filled?"
                                        class="btn btn-info btn-block mb-2">
                                        <i class="fas fa-check-circle mr-1"></i> Mark as Filled
                                    </button>
                                @endif

                                @if (in_array($opportunity->status, ['published', 'pending_approval']))
                                    <button wire:click="cancelOpportunity" wire:confirm="Cancel this opportunity?"
                                        class="btn btn-danger btn-block mb-2">
                                        <i class="fas fa-ban mr-1"></i> Cancel
                                    </button>
                                @endif

                                @if (in_array($opportunity->status, ['draft', 'pending_approval']))
                                    <button wire:click="deleteOpportunity"
                                        wire:confirm="Delete this opportunity? This action cannot be undone."
                                        class="btn btn-danger btn-block">
                                        <i class="fas fa-trash mr-1"></i> Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Requirements Card -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Requirements & Skills</h3>
                        </div>
                        <div class="card-body">
                            @if ($opportunity->min_cgpa)
                                <p><strong>Minimum CGPA:</strong>
                                    <span class="badge badge-info">{{ $opportunity->min_cgpa }}</span>
                                </p>
                            @endif

                            @if ($opportunity->min_year_of_study)
                                <p><strong>Minimum Year of Study:</strong>
                                    <span class="badge badge-secondary">Year
                                        {{ $opportunity->min_year_of_study }}</span>
                                </p>
                            @endif

                            @if ($opportunity->requires_portfolio)
                                <p><strong>Portfolio Required:</strong>
                                    <span class="badge badge-warning">Yes</span>
                                </p>
                            @endif

                            @if ($opportunity->requires_cover_letter)
                                <p><strong>Cover Letter Required:</strong>
                                    <span class="badge badge-warning">Yes</span>
                                </p>
                            @endif

                            @if ($opportunity->required_skills && is_array($opportunity->required_skills))
                                <p><strong>Required Skills:</strong></p>
                                <div class="mb-2">
                                    @foreach ($opportunity->required_skills as $skill)
                                        <span class="badge badge-primary mr-1 mb-1">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($opportunity->preferred_skills && is_array($opportunity->preferred_skills))
                                <p><strong>Preferred Skills:</strong></p>
                                <div class="mb-2">
                                    @foreach ($opportunity->preferred_skills as $skill)
                                        <span class="badge badge-secondary mr-1 mb-1">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($opportunity->preferred_courses && is_array($opportunity->preferred_courses))
                                <p><strong>Preferred Courses:</strong></p>
                                <div>
                                    @foreach ($opportunity->preferred_courses as $course)
                                        <span class="badge badge-info mr-1 mb-1">{{ $course }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
