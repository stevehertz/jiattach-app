<div>
    <!-- Main Content -->
    <div class="container-fluid">
        <!-- Quick Stats Row -->
        <div class="row mb-2">
            <div class="col-md-3">
                <div class="card card-stats border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-primary-soft mr-3">
                                <i class="fas fa-calendar-alt text-primary"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0">Applied On</p>
                                <h5 class="mb-0">{{ $application->created_at->format('M d, Y') }}</h5>
                                <small class="text-muted">{{ $application->created_at->format('h:i A') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-success-soft mr-3">
                                <i class="fas fa-user-graduate text-success"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0">Student</p>
                                <h5 class="mb-0">{{ $application->student->full_name }}</h5>
                                <small
                                    class="text-muted">{{ $application->student->studentProfile?->institution_name ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-warning-soft mr-3">
                                <i class="fas fa-briefcase text-warning"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0">Opportunity</p>
                                <h5 class="mb-0">{{ Str::limit($application->opportunity->title, 25) }}</h5>
                                <small class="text-muted">{{ $application->opportunity->organization->name }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-info-soft mr-3">
                                <i class="fas fa-chart-line text-info"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0">Match Score</p>
                                <h5 class="mb-0">
                                    <span
                                        class="badge badge-{{ $matchAnalysis['overall'] >= 80 ? 'success' : ($matchAnalysis['overall'] >= 60 ? 'warning' : 'danger') }} p-2">
                                        {{ $matchAnalysis['overall'] }}%
                                    </span>
                                </h5>
                                <small class="text-muted">Based on
                                    {{ count($matchAnalysis['skills']['matched'] ?? []) }} skills matched</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="row mb-2">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="btn-group mb-2 mb-sm-0">
                                <button type="button" class="btn btn-outline-secondary"
                                    wire:click="goToPreviousApplication">
                                    <i class="fas fa-chevron-left mr-1"></i> Previous
                                </button>
                                <button type="button" class="btn btn-outline-secondary"
                                    wire:click="goToNextApplication">
                                    Next <i class="fas fa-chevron-right ml-1"></i>
                                </button>
                            </div>

                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary" wire:click="sendEmail">
                                    <i class="fas fa-envelope mr-1"></i> Email
                                </button>
                                <button type="button" class="btn btn-outline-success"
                                    wire:click="downloadDocument('cv')"
                                    @if (!$documentStatus['cv']['exists']) disabled @endif>
                                    <i class="fas fa-download mr-1"></i> CV
                                </button>
                                @if ($application->status != \App\Enums\ApplicationStatus::REJECTED)
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-warning dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-bolt mr-1"></i> Actions
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @if ($application->status === \App\Enums\ApplicationStatus::PENDING)
                                                <button class="dropdown-item" wire:click="markAsUnderReview">
                                                    <i class="fas fa-search text-primary mr-2"></i> Mark Under Review
                                                </button>
                                            @endif
                                            @if (in_array($application->status, [\App\Enums\ApplicationStatus::UNDER_REVIEW]))
                                                <button class="dropdown-item" wire:click="markAsShortlisted">
                                                    <i class="fas fa-calendar-alt text-warning mr-2"></i> Shortlist
                                                    Candidate
                                                </button>
                                            @endif

                                            <!-- In the dropdown menu section, update the condition for SHORTLISTED -->
                                            @if (in_array($application->status->value, [\App\Enums\ApplicationStatus::SHORTLISTED->value]))
                                                <button class="dropdown-item" wire:click="openInterviewModal">
                                                    <i class="fas fa-calendar-plus text-warning mr-2"></i> Schedule
                                                    Interview
                                                </button>
                                            @endif



                                            @if (in_array($application->status->value, [\App\Enums\ApplicationStatus::INTERVIEW_COMPLETED->value]))
                                                @if ($this->canSendOffer())
                                                    <button class="dropdown-item" wire:click="openOfferModal">
                                                        <i class="fas fa-handshake text-info mr-2"></i> Send Offer
                                                    </button>
                                                @else
                                                    <button class="dropdown-item disabled" disabled
                                                        title="Payment required before sending offer">
                                                        <i class="fas fa-handshake text-muted mr-2"></i> Send Offer
                                                        <small class="text-warning">(Payment Pending)</small>
                                                    </button>
                                                @endif
                                            @endif



                                            <div class="dropdown-divider"></div>
                                            <button class="dropdown-item text-danger"
                                                wire:click="openStatusModal('rejected')">
                                                <i class="fas fa-times-circle mr-2"></i> Reject
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Main Information -->
            <div class="col-lg-8">
                <!-- Application Tabs -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#overview" role="tab">
                                    <i class="fas fa-info-circle mr-1"></i>Overview
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#match-analysis" role="tab">
                                    <i class="fas fa-chart-pie mr-1"></i>Match Analysis
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#timeline" role="tab">
                                    <i class="fas fa-history mr-1"></i>Timeline
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#notes" role="tab">
                                    <i class="fas fa-sticky-note mr-1"></i>Notes
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Overview Tab -->
                            <div class="tab-pane active" id="overview" role="tabpanel">
                                <div class="row">
                                    <!-- Student Information -->
                                    <div class="col-md-6 mb-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-circle bg-primary mr-2">
                                                <span class="initials">{{ $application->student->initials() }}</span>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">{{ $application->student->full_name }}</h5>
                                                <small class="text-muted">Student</small>
                                            </div>
                                            @if ($application->student->is_verified)
                                                <i class="fas fa-check-circle text-success ml-2" title="Verified"></i>
                                            @endif
                                        </div>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="35%"><i
                                                        class="fas fa-envelope text-muted mr-2"></i>Email</td>
                                                <td><a
                                                        href="mailto:{{ $application->student->email }}">{{ $application->student->email }}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><i class="fas fa-phone text-muted mr-2"></i>Phone</td>
                                                <td>{{ formatPhoneNumber($application->student->phone) ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fas fa-map-marker-alt text-muted mr-2"></i>Location</td>
                                                <td>{{ $application->student->county ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><i class="fas fa-universal-access text-muted mr-2"></i>Disability
                                                </td>
                                                <td>
                                                    @if ($application->student->hasDisability())
                                                        <span
                                                            class="badge badge-warning">{{ $application->student->disability_status_label }}</span>
                                                        <i class="fas fa-info-circle text-muted ml-1"
                                                            title="{{ $application->student->disability_details }}"></i>
                                                    @else
                                                        <span class="text-muted">None specified</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!-- Academic Information -->
                                    <div class="col-md-6 mb-4">
                                        <h5 class="mb-3">
                                            <i class="fas fa-graduation-cap text-primary mr-2"></i>
                                            Academic
                                        </h5>
                                        @if ($application->student->studentProfile)
                                            @php $profile = $application->student->studentProfile; @endphp
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="40%">Institution</td>
                                                    <td><strong>{{ $profile->institution_name }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Course</td>
                                                    <td>{{ $profile->course_name }}
                                                        ({{ $profile->course_level_label }})</td>
                                                </tr>
                                                <tr>
                                                    <td>Reg Number</td>
                                                    <td>{{ $profile->student_reg_number ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Year/Graduation</td>
                                                    <td>Year {{ $profile->year_of_study }}
                                                        ({{ $profile->expected_graduation_year ?? 'N/A' }})</td>
                                                </tr>
                                                <tr>
                                                    <td>CGPA</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-{{ $profile->cgpa >= 3.0 ? 'success' : ($profile->cgpa >= 2.5 ? 'warning' : 'danger') }} p-2">
                                                            {{ $profile->cgpa ?? 'N/A' }} / 4.0
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        @endif
                                    </div>
                                </div>

                                <!-- Skills & Interests -->
                                @if ($profile && ($profile->skills || $profile->interests))
                                    <div class="row">
                                        @if ($profile->skills && count($profile->skills) > 0)
                                            <div class="col-md-6 mb-3">
                                                <h6 class="mb-2"><i class="fas fa-code text-info mr-2"></i>Skills
                                                </h6>
                                                <div>
                                                    @foreach ($profile->skills as $skill)
                                                        <span
                                                            class="badge badge-info p-2 mr-1 mb-1">{{ $skill }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        @if ($profile->interests && count($profile->interests) > 0)
                                            <div class="col-md-6 mb-3">
                                                <h6 class="mb-2"><i
                                                        class="fas fa-heart text-danger mr-2"></i>Interests</h6>
                                                <div>
                                                    @foreach ($profile->interests as $interest)
                                                        <span
                                                            class="badge badge-secondary p-2 mr-1 mb-1">{{ $interest }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Opportunity Details -->
                                <hr class="my-4">
                                <h5 class="mb-3"><i class="fas fa-briefcase text-primary mr-2"></i>Opportunity
                                    Details</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>{{ $application->opportunity->title }}</h6>
                                        <p class="text-muted">
                                            <i class="fas fa-building mr-1"></i>
                                            {{ $application->opportunity->organization->name }}
                                        </p>
                                    </div>
                                    <div class="col-md-6 text-md-right">
                                        <span
                                            class="badge badge-{{ $application->opportunity->is_open ? 'success' : 'danger' }} p-2">
                                            {{ $application->opportunity->is_open ? 'Open' : 'Closed' }}
                                        </span>
                                        @if ($application->opportunity->is_open)
                                            <small class="d-block text-muted mt-1">
                                                <i
                                                    class="fas fa-clock mr-1"></i>{{ $application->opportunity->days_remaining }}
                                                days remaining
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-3 col-6 mb-2">
                                        <small class="text-muted d-block">Type</small>
                                        <span>{{ ucfirst($application->opportunity->type) }}</span>
                                    </div>
                                    <div class="col-md-3 col-6 mb-2">
                                        <small class="text-muted d-block">Work Type</small>
                                        <span>{{ ucfirst($application->opportunity->work_type) }}</span>
                                    </div>
                                    <div class="col-md-3 col-6 mb-2">
                                        <small class="text-muted d-block">Location</small>
                                        <span>{{ $application->opportunity->location }}</span>
                                    </div>
                                    <div class="col-md-3 col-6 mb-2">
                                        <small class="text-muted d-block">Duration</small>
                                        <span>{{ $application->opportunity->duration_months }} months</span>
                                    </div>
                                </div>

                                @if ($application->opportunity->description)
                                    <div class="mt-3">
                                        <small class="text-muted d-block mb-1">Description</small>
                                        <p>{{ $application->opportunity->description }}</p>
                                    </div>
                                @endif

                                @if ($application->opportunity->skills_required && count($application->opportunity->skills_required) > 0)
                                    <div class="mt-3">
                                        <small class="text-muted d-block mb-1">Required Skills</small>
                                        <div>
                                            @foreach ($application->opportunity->skills_required as $skill)
                                                <span class="badge badge-info p-2 mr-1">{{ $skill }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Match Analysis Tab -->
                            <div class="tab-pane" id="match-analysis" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0 mb-3">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0">GPA Match</h6>
                                                    <span
                                                        class="badge badge-{{ $matchAnalysis['gpa']['match'] ? 'success' : 'danger' }}">
                                                        {{ $matchAnalysis['gpa']['match'] ? 'Match' : 'No Match' }}
                                                    </span>
                                                </div>
                                                <div class="progress mb-2" style="height: 25px;">
                                                    <div class="progress-bar bg-{{ $matchAnalysis['gpa']['match'] ? 'success' : 'danger' }}"
                                                        style="width: {{ ($matchAnalysis['gpa']['score'] / 4.0) * 100 }}%">
                                                        {{ $matchAnalysis['gpa']['score'] ?? '0' }} / 4.0
                                                    </div>
                                                </div>
                                                <small class="text-muted">Required:
                                                    {{ $matchAnalysis['gpa']['required'] ?? 'Not specified' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0 mb-3">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0">Location Match</h6>
                                                    <span
                                                        class="badge badge-{{ $matchAnalysis['location']['match'] ? 'success' : 'danger' }}">
                                                        {{ $matchAnalysis['location']['match'] ? 'Match' : 'No Match' }}
                                                    </span>
                                                </div>
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <td>Student:</td>
                                                        <td>{{ $matchAnalysis['location']['student'] ?? 'Not specified' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Opportunity:</td>
                                                        <td>{{ $matchAnalysis['location']['opportunity'] ?? 'Not specified' }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0 mb-3">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0">Course Match</h6>
                                                    <span
                                                        class="badge badge-{{ $matchAnalysis['course']['match'] ? 'success' : 'danger' }}">
                                                        {{ $matchAnalysis['course']['match'] ? 'Match' : 'No Match' }}
                                                    </span>
                                                </div>
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <td>Student:</td>
                                                        <td>{{ $matchAnalysis['course']['student'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Required:</td>
                                                        <td>{{ implode(', ', $matchAnalysis['course']['required']) }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0 mb-3">
                                            <div class="card-body">
                                                <h6 class="mb-3">Skills Match</h6>
                                                <div class="mb-2">
                                                    <span class="badge badge-info p-2">
                                                        {{ count($matchAnalysis['skills']['matched'] ?? []) }}/{{ count($matchAnalysis['skills']['required'] ?? []) }}
                                                        skills matched
                                                    </span>
                                                </div>
                                                @if (!empty($matchAnalysis['skills']['matched']))
                                                    <div class="mb-2">
                                                        <small class="text-muted d-block mb-1">Matched:</small>
                                                        @foreach ($matchAnalysis['skills']['matched'] as $skill)
                                                            <span
                                                                class="badge badge-success">{{ $skill }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if (!empty($matchAnalysis['skills']['missing']))
                                                    <div>
                                                        <small class="text-muted d-block mb-1">Missing:</small>
                                                        @foreach ($matchAnalysis['skills']['missing'] as $skill)
                                                            <span
                                                                class="badge badge-danger">{{ $skill }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline Tab -->
                            <div class="tab-pane" id="timeline" role="tabpanel">
                                <div class="timeline-modern">
                                    @php
                                        $groupedLogs = collect($activityLogs)->groupBy(
                                            fn($log) => $log['created_at']->format('Y-m-d'),
                                        );
                                    @endphp

                                    @forelse($groupedLogs as $date => $logs)
                                        <!-- Date Separator -->
                                        <div class="timeline-date-separator">
                                            <span class="badge badge-light p-2">
                                                @php
                                                    $carbonDate = \Carbon\Carbon::parse($date);
                                                    if ($carbonDate->isToday()) {
                                                        echo 'Today';
                                                    } elseif ($carbonDate->isYesterday()) {
                                                        echo 'Yesterday';
                                                    } else {
                                                        echo $carbonDate->format('M d, Y');
                                                    }
                                                @endphp
                                            </span>
                                        </div>

                                        @foreach ($logs as $log)
                                            <div class="timeline-item-modern">
                                                <div class="timeline-dot bg-{{ $log->color ?? 'secondary' }}">
                                                    <i class="fas {{ $log->icon ?? 'fa-circle' }}"></i>
                                                </div>
                                                <div class="timeline-content">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <div>
                                                            <strong>{{ $log->causer?->full_name ?? 'System' }}</strong>
                                                            <span class="text-muted mx-2">•</span>
                                                            <span
                                                                class="text-muted">{{ $log['created_at']->format('h:i A') }}</span>
                                                        </div>
                                                        @if (isset($log->properties['action']))
                                                            <span
                                                                class="badge badge-light">{{ ucfirst(str_replace('_', ' ', $log->properties['action'])) }}</span>
                                                        @endif
                                                    </div>
                                                    <p class="mb-2">{{ $log['description'] }}</p>

                                                    @if (isset($log->properties['metadata']) ||
                                                            isset($log->properties['old_status']) ||
                                                            isset($log->properties['new_status']))
                                                        <div class="small bg-light p-2 rounded">
                                                            @if (isset($log->properties['old_status_label']) && isset($log->properties['new_status_label']))
                                                                <div class="mb-1">
                                                                    <span
                                                                        class="badge badge-secondary">{{ $log->properties['old_status_label'] }}</span>
                                                                    <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                                    <span
                                                                        class="badge badge-{{ $log->color ?? 'primary' }}">{{ $log->properties['new_status_label'] }}</span>
                                                                </div>
                                                            @endif

                                                            @if (isset($log->properties['metadata']['interview_details']))
                                                                <div class="mt-2">
                                                                    <i
                                                                        class="fas fa-calendar-alt text-warning mr-1"></i>
                                                                    Interview:
                                                                    {{ $log->properties['metadata']['interview_details']['date'] }}
                                                                    at
                                                                    {{ $log->properties['metadata']['interview_details']['time'] }}
                                                                    ({{ ucfirst($log->properties['metadata']['interview_details']['type']) }})
                                                                </div>
                                                            @endif

                                                            @if (isset($log->properties['metadata']['offer_details']))
                                                                <div class="mt-2">
                                                                    <i class="fas fa-money-bill text-success mr-1"></i>
                                                                    Offer: KES
                                                                    {{ number_format($log->properties['metadata']['offer_details']['stipend']) }}
                                                                </div>
                                                            @endif

                                                            @if (isset($log->properties['notes']))
                                                                <div class="mt-2 text-muted">
                                                                    <i class="fas fa-comment mr-1"></i>
                                                                    {{ $log->properties['notes'] }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @empty
                                        <div class="text-center py-5">
                                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No timeline history found</p>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Interviews Section -->
                                @if ($application->interviews && $application->interviews->count() > 0)
                                    <div class="mt-4">
                                        <h6 class="mb-3">
                                            <i class="fas fa-calendar-alt text-warning mr-2"></i>
                                            Interviews
                                        </h6>
                                        @foreach ($application->interviews as $interview)
                                            <div class="card border-0 bg-light mb-3">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <span
                                                                class="badge badge-{{ $interview->status_badge }} mb-2">
                                                                {{ $interview->status }}
                                                            </span>
                                                            <h6 class="mb-2">
                                                                <i class="fas {{ $interview->type_icon }} mr-1"></i>
                                                                {{ ucfirst($interview->type) }} Interview
                                                            </h6>
                                                            <p class="mb-1">
                                                                <i class="fas fa-calendar mr-1"></i>
                                                                {{ $interview->scheduled_at->format('M d, Y h:i A') }}
                                                                ({{ $interview->duration_formatted }})
                                                            </p>
                                                            @if ($interview->meeting_details)
                                                                <p class="mb-1">
                                                                    <i class="fas fa-link mr-1"></i>
                                                                    {{ $interview->meeting_details }}
                                                                </p>
                                                            @endif
                                                            @if ($interview->notes)
                                                                <p class="mb-0 text-muted">{{ $interview->notes }}</p>
                                                            @endif
                                                        </div>
                                                        @if ($interview->status === 'scheduled' && $interview->isUpcoming())
                                                            <div class="btn-group">
                                                                <button class="btn btn-sm btn-success"
                                                                    wire:click="markInterviewCompleted({{ $interview->id }})">
                                                                    <i class="fas fa-check"></i> Complete
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>


                            <!-- Notes Tab -->
                            <div class="tab-pane" id="notes" role="tabpanel">
                                <div class="mb-4">
                                    <div class="input-group">
                                        <textarea wire:model="newNote" class="form-control" rows="2" placeholder="Add a note..."></textarea>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" wire:click="addNote">
                                                <i class="fas fa-plus mr-1"></i> Add Note
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="notes-list">
                                    @php
                                        // Filter notes from both activity logs and history
                                        $notes = collect($activityLogs)
                                            ->filter(function ($item) {
                                                // From activity logs with description containing 'note'
                                                if (
                                                    ($item['type'] ?? '') === 'activity' &&
                                                    (str_contains(strtolower($item['description'] ?? ''), 'note') ||
                                                        ($item['properties']['note'] ?? false))
                                                ) {
                                                    return true;
                                                }

                                                // From history with action 'note_added'
                                                if (
                                                    ($item['type'] ?? '') === 'history' &&
                                                    ($item['properties']['action'] ?? '') === 'note_added'
                                                ) {
                                                    return true;
                                                }

                                                return false;
                                            })
                                            ->sortByDesc('created_at');
                                    @endphp

                                    @forelse($notes as $note)
                                        <div class="note-item-modern p-3 border-bottom">
                                            <div class="d-flex justify-content-between mb-2">
                                                <strong>
                                                    @if ($note['type'] === 'activity')
                                                        {{ $note['causer']['full_name'] ?? 'System' }}
                                                    @else
                                                        {{ $note['causer'] ?? 'System' }}
                                                    @endif
                                                </strong>
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($note['created_at'])->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-0">
                                                @if ($note['type'] === 'activity')
                                                    {{ $note['properties']['note'] ?? $note['description'] }}
                                                @else
                                                    {{ $note['properties']['notes'] ?? ($note['properties']['metadata']['note'] ?? 'Note added') }}
                                                @endif
                                            </p>
                                            @if (isset($note['properties']['metadata']['opportunity_title']))
                                                <small class="text-muted">
                                                    <i class="fas fa-briefcase mr-1"></i>
                                                    {{ $note['properties']['metadata']['opportunity_title'] }}
                                                </small>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center py-4">
                                            <i class="fas fa-sticky-note fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No notes yet. Add your first note above.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column - Status & Actions -->
            <div class="col-lg-4">
                <!-- Status Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0"><i class="fas fa-info-circle text-primary mr-2"></i>Application Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="status-circle-wrapper">
                                <div class="status-circle status-{{ $application->status?->value ?? 'pending' }}">
                                    <i class="fas {{ $application->status?->icon() ?? 'fa-circle' }}"></i>
                                </div>
                            </div>
                            <h4 class="mt-3">
                                {{ $application->status?->label() ?? 'Pending' }}
                            </h4>
                        </div>

                        <div class="status-timeline-mini">
                            @php
                                $currentStatusValue = $application->status?->value ?? 'pending';
                                $statusKeys = array_keys($statusFlow);
                                $currentIndex = array_search($currentStatusValue, $statusKeys);
                            @endphp

                            @foreach ($statusFlow as $statusKey => $details)
                                @php
                                    $statusValue = $statusKey; // $statusKey is string like 'submitted', 'under_review', etc.
                                    $isCompleted = $loop->index <= $currentIndex;
                                    $isCurrent = $statusValue === $currentStatusValue;
                                @endphp
                                <div
                                    class="status-step {{ $isCompleted ? 'completed' : '' }} {{ $isCurrent ? 'current' : '' }}">
                                    <div class="step-indicator">
                                        <i class="fas {{ $details['icon'] }}"></i>
                                    </div>
                                    <div class="step-content">
                                        <span class="step-label">{{ $details['label'] }}</span>
                                        @if ($isCurrent)
                                            <span class="badge badge-primary ml-2">Current</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Documents Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-file-alt text-primary mr-2"></i>Documents</h5>
                        <span
                            class="badge badge-light">{{ collect($documentStatus)->where('exists', true)->count() }}/3</span>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach (['cv' => 'CV/Resume', 'transcript' => 'Academic Transcript', 'school_letter' => 'School Letter'] as $key => $label)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i
                                        class="fas fa-file-{{ $key === 'cv' ? 'pdf text-danger' : ($key === 'transcript' ? 'alt text-info' : 'signature text-warning') }} mr-2"></i>
                                    {{ $label }}
                                </div>
                                <div>
                                    @if ($documentStatus[$key]['exists'])
                                        <span class="badge badge-success mr-2">Uploaded</span>
                                        <button class="btn btn-sm btn-link"
                                            wire:click="downloadDocument('{{ $key }}')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    @else
                                        <span class="badge badge-danger">Missing</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Interview Card (if applicable) -->
                @if ($application->interview_details)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0"><i class="fas fa-calendar-alt text-warning mr-2"></i>Interview Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="35%"><i class="fas fa-calendar text-muted mr-2"></i>Date</td>
                                    <td>{{ \Carbon\Carbon::parse($application->interview_details['date'])->format('M d, Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-clock text-muted mr-2"></i>Time</td>
                                    <td>{{ $application->interview_details['time'] }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-video text-muted mr-2"></i>Type</td>
                                    <td><span
                                            class="badge badge-info">{{ ucfirst($application->interview_details['type']) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-map-marker-alt text-muted mr-2"></i>Location</td>
                                    <td>{{ $application->interview_details['location'] ?? 'N/A' }}</td>
                                </tr>
                            </table>
                            @if ($application->interview_details['notes'])
                                <hr>
                                <small class="text-muted">Notes:</small>
                                <p class="mb-0">{{ $application->interview_details['notes'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Payment Status Card -->
                @if ($application->status === \App\Enums\ApplicationStatus::INTERVIEW_COMPLETED)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0">
                                <i class="fas fa-credit-card text-primary mr-2"></i>
                                Payment Status
                            </h5>
                        </div>
                        <div class="card-body">
                            @php $paymentStatus = $this->getPaymentStatusAttribute(); @endphp
                            <div class="text-center mb-3">
                                <div class="status-circle-wrapper">
                                    <div class="status-circle status-{{ $paymentStatus['color'] }}"
                                        style="background: linear-gradient(135deg, {{ $paymentStatus['color'] === 'success' ? '#28a745' : ($paymentStatus['color'] === 'warning' ? '#ffc107' : ($paymentStatus['color'] === 'danger' ? '#dc3545' : '#17a2b8')) }} 0%, {{ $paymentStatus['color'] === 'success' ? '#20c997' : ($paymentStatus['color'] === 'warning' ? '#f4a81d' : ($paymentStatus['color'] === 'danger' ? '#c82333' : '#138496')) }} 100%);">
                                        <i class="fas {{ $paymentStatus['icon'] }} fa-2x text-white"></i>
                                    </div>
                                </div>
                                <h5 class="mt-3 mb-0">
                                    <span class="badge badge-{{ $paymentStatus['color'] }} p-2">
                                        <i class="fas {{ $paymentStatus['icon'] }} mr-1"></i>
                                        {{ $paymentStatus['label'] }}
                                    </span>
                                </h5>
                                @if (isset($paymentStatus['date']))
                                    <small class="text-muted d-block mt-2">
                                        Completed: {{ $paymentStatus['date']->format('d M, Y h:i A') }}
                                    </small>
                                @endif
                                @if (isset($paymentStatus['status']) && $paymentStatus['status'] === 'required')
                                    <div class="alert alert-warning mt-3 mb-0">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Payment is required before sending offer letter.
                                    </div>
                                @endif
                                @if (isset($paymentStatus['status']) && $paymentStatus['status'] === 'pending')
                                    <div class="alert alert-info mt-3 mb-0">
                                        <i class="fas fa-spinner fa-pulse mr-1"></i>
                                        Payment is pending. The student has initiated payment.
                                    </div>
                                @endif
                                @if (isset($paymentStatus['status']) && $paymentStatus['status'] === 'processing')
                                    <div class="alert alert-info mt-3 mb-0">
                                        <i class="fas fa-spinner fa-pulse mr-1"></i>
                                        Payment is being processed. Please wait for confirmation.
                                    </div>
                                @endif
                            </div>

                            @if ($application->payment_reference)
                                <hr>
                                <div class="small text-muted">
                                    <i class="fas fa-hashtag mr-1"></i>
                                    Payment Reference: <strong>{{ $application->payment_reference }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif


                <!-- Offer Card (if applicable) -->
                @if ($application->offer_details)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0"><i class="fas fa-handshake text-success mr-2"></i>Offer Details</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><i class="fas fa-money-bill text-muted mr-2"></i>Stipend</td>
                                    <td><strong>KES
                                            {{ number_format($application->offer_details['stipend'], 2) }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-check text-muted mr-2"></i>Start Date</td>
                                    <td>{{ \Carbon\Carbon::parse($application->offer_details['start_date'])->format('M d, Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-times text-muted mr-2"></i>End Date</td>
                                    <td>{{ \Carbon\Carbon::parse($application->offer_details['end_date'])->format('M d, Y') }}
                                    </td>
                                </tr>
                            </table>
                            @if ($application->offer_details['notes'])
                                <hr>
                                <small class="text-muted">Notes:</small>
                                <p class="mb-0">{{ $application->offer_details['notes'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Placement Card (if applicable) -->
                @if ($application->placement)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-briefcase text-success mr-2"></i>Placement</h5>
                            <a href="{{ route('admin.placements.show', $application->placement->id) }}"
                                class="btn btn-sm btn-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><i class="fas fa-user-tie text-muted mr-2"></i>Supervisor</td>
                                    <td>{{ $application->placement->supervisor_name }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-building text-muted mr-2"></i>Department</td>
                                    <td>{{ $application->placement->department }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-alt text-muted mr-2"></i>Duration</td>
                                    <td>{{ $application->placement->start_date->format('M d') }} -
                                        {{ $application->placement->end_date->format('M d, Y') }}</td>
                                </tr>
                            </table>
                            @if ($application->placement->is_active)
                                <div class="mt-3">
                                    <small class="text-muted d-block mb-1">Progress</small>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                            style="width: {{ $application->placement->progress_percentage }}%">
                                            {{ $application->placement->progress_percentage }}%
                                        </div>
                                    </div>
                                    <small class="text-muted mt-1 d-block">
                                        <i
                                            class="fas fa-clock mr-1"></i>{{ $application->placement->remaining_days }}
                                        days remaining
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>

    <!-- Modal Components (keep as is but enhance styling) -->
    @if ($showStatusModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white border-0">
                        <h5 class="modal-title"><i class="fas fa-sync-alt mr-2"></i>Update Application Status</h5>
                        <button type="button" class="close text-white" wire:click="$set('showStatusModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">New Status</label>
                            <select wire:model="newStatus" class="form-control form-control-lg">
                                <option value="">Select Status</option>
                                <option value="under_review">Under Review</option>
                                <option value="shortlisted">Shortlisted</option>
                                <option value="interview_scheduled">Interview Scheduled</option>
                                <option value="interview_completed">Interview Completed</option>
                                <option value="offer_sent">Offer Sent</option>
                                <option value="offer_accepted">Offer Accepted</option>
                                <option value="offer_rejected">Offer Rejected</option>
                                <option value="hired">Hired</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Notes (Optional)</label>
                            <textarea wire:model="statusNotes" class="form-control" rows="3"
                                placeholder="Add any notes about this status change..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary"
                            wire:click="$set('showStatusModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="updateStatus">
                            <i class="fas fa-check mr-1"></i> Update Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Schedule Interview Modal -->
    @if ($showInterviewModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-warning text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-plus mr-2"></i>Schedule Interview
                        </h5>
                        <button type="button" class="close text-white"
                            wire:click="$set('showInterviewModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Interview Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" wire:model="interviewDate"
                                        class="form-control @error('interviewDate') is-invalid @enderror"
                                        min="{{ now()->format('Y-m-d') }}">
                                    @error('interviewDate')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Interview Time <span
                                            class="text-danger">*</span></label>
                                    <input type="time" wire:model="interviewTime"
                                        class="form-control @error('interviewTime') is-invalid @enderror">
                                    @error('interviewTime')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Interview Type <span
                                            class="text-danger">*</span></label>
                                    <select wire:model="interviewType"
                                        class="form-control @error('interviewType') is-invalid @enderror">
                                        <option value="online">Online (Video Call)</option>
                                        <option value="phone">Phone Call</option>
                                        <option value="in_person">In Person</option>
                                    </select>
                                    @error('interviewType')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Duration (minutes) <span
                                            class="text-danger">*</span></label>
                                    <select wire:model="interviewDuration" class="form-control">
                                        <option value="15">15 minutes</option>
                                        <option value="30">30 minutes</option>
                                        <option value="45">45 minutes</option>
                                        <option value="60">1 hour</option>
                                        <option value="90">1.5 hours</option>
                                        <option value="120">2 hours</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if ($interviewType === 'online')
                            <div class="form-group">
                                <label class="font-weight-bold">Meeting Link <span
                                        class="text-danger">*</span></label>
                                <input type="url" wire:model="interviewMeetingLink"
                                    class="form-control @error('interviewMeetingLink') is-invalid @enderror"
                                    placeholder="https://meet.google.com/xxx or Zoom link">
                                @error('interviewMeetingLink')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        @if ($interviewType === 'phone')
                            <div class="form-group">
                                <label class="font-weight-bold">Phone Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" wire:model="interviewPhoneNumber"
                                    class="form-control @error('interviewPhoneNumber') is-invalid @enderror"
                                    placeholder="+254 XXX XXX XXX">
                                @error('interviewPhoneNumber')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        @if ($interviewType === 'in_person')
                            <div class="form-group">
                                <label class="font-weight-bold">Location <span class="text-danger">*</span></label>
                                <input type="text" wire:model="interviewLocation"
                                    class="form-control @error('interviewLocation') is-invalid @enderror"
                                    placeholder="Office address, building, room number">
                                @error('interviewLocation')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="font-weight-bold">Interviewer (Optional)</label>
                            <select wire:model="interviewerId" class="form-control">
                                <option value="">Select Interviewer</option>
                                @foreach ($application->opportunity->organization->users as $user)
                                    <option value="{{ $user->id }}">{{ $user->full_name }}
                                        ({{ $user->pivot->role }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Notes / Instructions</label>
                            <textarea wire:model="interviewNotes" class="form-control" rows="3"
                                placeholder="Any special instructions for the student..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary"
                            wire:click="$set('showInterviewModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-warning" wire:click="scheduleInterview">
                            <i class="fas fa-calendar-check mr-1"></i> Schedule Interview
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Keep other modals with similar styling improvements -->
    @push('styles')
        <style>
            /* Gradient Background */
            .bg-gradient-primary-to-secondary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .text-white-50 {
                color: rgba(255, 255, 255, 0.7);
            }

            .text-white-50:hover {
                color: rgba(255, 255, 255, 0.9);
            }

            /* Icon Circles */
            .icon-circle {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
            }

            .bg-primary-soft {
                background: rgba(102, 126, 234, 0.1);
            }

            .bg-success-soft {
                background: rgba(40, 167, 69, 0.1);
            }

            .bg-warning-soft {
                background: rgba(255, 193, 7, 0.1);
            }

            .bg-info-soft {
                background: rgba(23, 162, 184, 0.1);
            }

            /* Card Stats */
            .card-stats {
                transition: transform 0.2s;
            }

            .card-stats:hover {
                transform: translateY(-5px);
            }

            /* Avatar Circle */
            .avatar-circle {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .avatar-circle .initials {
                color: white;
                font-weight: bold;
                font-size: 1.2rem;
            }

            /* Status Circle */
            .status-circle-wrapper {
                display: flex;
                justify-content: center;
            }

            .status-circle {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2.5rem;
                color: white;
            }

            .status-pending {
                background: linear-gradient(135deg, #f6b23d 0%, #f4a81d 100%);
            }

            .status-under_review {
                background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            }

            .status-shortlisted {
                background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            }

            .status-interview_scheduled {
                background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            }

            .status-offer_sent {
                background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
            }

            .status-hired {
                background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            }

            .status-rejected {
                background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            }

            /* Modern Timeline */
            .timeline-modern {
                position: relative;
                padding-left: 20px;
            }

            .timeline-modern:before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 2px;
                background: #e9ecef;
            }

            .timeline-item-modern {
                position: relative;
                padding-left: 30px;
                margin-bottom: 30px;
            }

            .timeline-dot {
                position: absolute;
                left: -6px;
                top: 0;
                width: 14px;
                height: 14px;
                border-radius: 50%;
                border: 2px solid white;
                z-index: 1;
            }

            .timeline-dot.bg-primary {
                background: #667eea;
            }

            .timeline-dot.bg-success {
                background: #28a745;
            }

            .timeline-dot.bg-warning {
                background: #ffc107;
            }

            .timeline-dot.bg-danger {
                background: #dc3545;
            }

            .timeline-dot.bg-info {
                background: #17a2b8;
            }

            .timeline-content {
                background: #f8f9fa;
                border-radius: 10px;
                padding: 15px;
            }

            /* Mini Status Timeline */
            .status-timeline-mini {
                position: relative;
                padding-left: 30px;
            }

            .status-timeline-mini:before {
                content: '';
                position: absolute;
                left: 14px;
                top: 0;
                bottom: 0;
                width: 2px;
                background: #e9ecef;
            }

            .status-step {
                position: relative;
                margin-bottom: 20px;
                display: flex;
                align-items: flex-start;
            }

            .step-indicator {
                position: absolute;
                left: -30px;
                width: 28px;
                height: 28px;
                border-radius: 50%;
                background: #e9ecef;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #6c757d;
                z-index: 1;
            }

            .status-step.completed .step-indicator {
                background: #28a745;
                color: white;
            }

            .status-step.current .step-indicator {
                background: #667eea;
                color: white;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
            }

            .step-content {
                flex: 1;
                display: flex;
                align-items: center;
            }

            /* Note Items */
            .note-item-modern {
                transition: background-color 0.2s;
            }

            .note-item-modern:hover {
                background-color: #f8f9fa;
            }

            /* Tabs Styling */
            .nav-tabs .nav-link {
                border: none;
                color: #6c757d;
                font-weight: 500;
                padding: 0.75rem 1rem;
            }

            .nav-tabs .nav-link.active {
                color: #667eea;
                background: none;
                border-bottom: 2px solid #667eea;
            }

            .nav-tabs .nav-link i {
                font-size: 1rem;
            }

            /* Responsive Adjustments */
            @media (max-width: 768px) {
                .card-stats .card-body {
                    padding: 1rem;
                }

                .icon-circle {
                    width: 40px;
                    height: 40px;
                    font-size: 1rem;
                }
            }

            /* Timeline Date Separator */
            .timeline-date-separator {
                text-align: center;
                margin: 20px 0;
                position: relative;
            }

            .timeline-date-separator:before {
                content: '';
                position: absolute;
                left: 0;
                right: 0;
                top: 50%;
                height: 1px;
                background: #e9ecef;
                z-index: 0;
            }

            .timeline-date-separator .badge {
                position: relative;
                z-index: 1;
                padding: 0.5rem 1rem;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Toast notification handler
                Livewire.on('show-toast', (event) => {
                    toastr[event.type](event.message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000,
                        extendedTimeOut: 2000
                    });
                });

                // Auto-hide modals on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        @this.set('showStatusModal', false);
                        @this.set('showInterviewModal', false);
                        @this.set('showOfferModal', false);
                        @this.set('showPlacementModal', false);
                        @this.set('showFeedbackModal', false);
                    }
                });
            });
        </script>
    @endpush
</div>
