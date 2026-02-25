<div>
    <!-- Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Application #{{ $application->id }}
                        <small class="text-muted">- {{ $application->opportunity->title }}</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.applications.index') }}">Applications</a>
                        </li>
                        <li class="breadcrumb-item active">#{{ $application->id }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Action Buttons -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default"
                                            wire:click="goToPreviousApplication">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </button>
                                        <button type="button" class="btn btn-default" wire:click="goToNextApplication">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <span class="ml-3">
                                        {!! getApplicationStatusBadge($application->status, 'large') !!}
                                    </span>
                                </div>
                                <div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info" wire:click="sendEmail">
                                            <i class="fas fa-envelope mr-1"></i> Email Student
                                        </button>
                                        <button type="button" class="btn btn-success"
                                            wire:click="downloadDocument('cv')"
                                            @if (!$documentStatus['cv']['exists']) disabled @endif>
                                            <i class="fas fa-download mr-1"></i> Download CV
                                        </button>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-warning dropdown-toggle"
                                                data-toggle="dropdown">
                                                <i class="fas fa-bolt mr-1"></i> Quick Actions
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if (in_array($application->status, ['submitted', 'under_review']))
                                                    <button class="dropdown-item text-success"
                                                        wire:click="openStatusModal('shortlisted')">
                                                        <i class="fas fa-list-check mr-2"></i> Shortlist
                                                    </button>
                                                    <button class="dropdown-item text-primary"
                                                        wire:click="openStatusModal('under_review')">
                                                        <i class="fas fa-search mr-2"></i> Mark Under Review
                                                    </button>
                                                @endif

                                                @if (in_array($application->status, ['shortlisted', 'under_review', 'interview_completed']))
                                                    <button class="dropdown-item text-warning"
                                                        wire:click="openInterviewModal">
                                                        <i class="fas fa-calendar-alt mr-2"></i> Schedule Interview
                                                    </button>
                                                @endif

                                                @if (in_array($application->status, ['interview_completed', 'shortlisted']))
                                                    <button class="dropdown-item text-info" wire:click="openOfferModal">
                                                        <i class="fas fa-handshake mr-2"></i> Send Offer
                                                    </button>
                                                @endif

                                                @if ($application->status === 'offer_accepted')
                                                    <button class="dropdown-item text-success"
                                                        wire:click="openPlacementModal">
                                                        <i class="fas fa-briefcase mr-2"></i> Create Placement
                                                    </button>
                                                @endif

                                                <div class="dropdown-divider"></div>

                                                <button class="dropdown-item text-danger"
                                                    wire:click="openStatusModal('rejected')">
                                                    <i class="fas fa-times-circle mr-2"></i> Reject
                                                </button>
                                                <button class="dropdown-item" wire:click="openFeedbackModal('general')">
                                                    <i class="fas fa-comment mr-2"></i> Send Feedback
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column - Student & Application Details -->
                <div class="col-md-8">
                    <!-- Student Information Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-graduate mr-2"></i>
                                Student Information
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.users.show', $application->student->id) }}"
                                    class="btn btn-sm btn-default">
                                    <i class="fas fa-external-link-alt mr-1"></i> View Full Profile
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%">Full Name:</th>
                                            <td>
                                                <strong>{{ $application->student->full_name }}</strong>
                                                @if ($application->student->is_verified)
                                                    <i class="fas fa-check-circle text-success ml-1"
                                                        title="Verified"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td>
                                                <a
                                                    href="mailto:{{ $application->student->email }}">{{ $application->student->email }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Phone:</th>
                                            <td>{{ formatPhoneNumber($application->student->phone) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Gender:</th>
                                            <td>{{ ucfirst($application->student->gender) ?? 'Not specified' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%">Date of Birth:</th>
                                            <td>{{ $application->student->date_of_birth?->format('M d, Y') ?? 'Not specified' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>County:</th>
                                            <td>{{ $application->student->county ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Disability:</th>
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
                                        <tr>
                                            <th>Joined:</th>
                                            <td>{{ $application->student->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if ($application->student->bio)
                                <div class="mt-3">
                                    <h6>Bio:</h6>
                                    <p class="text-muted">{{ $application->student->bio }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Academic Information Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-graduation-cap mr-2"></i>
                                Academic Information
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($application->student->studentProfile)
                                @php $profile = $application->student->studentProfile; @endphp
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th width="40%">Institution:</th>
                                                <td>
                                                    <strong>{{ $profile->institution_name }}</strong>
                                                    <small
                                                        class="text-muted d-block">{{ $profile->institution_type_label }}</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Reg Number:</th>
                                                <td>{{ $profile->student_reg_number ?? 'Not specified' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Course:</th>
                                                <td>
                                                    <strong>{{ $profile->course_name }}</strong>
                                                    <small
                                                        class="text-muted d-block">{{ $profile->course_level_label }}</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Year of Study:</th>
                                                <td>{{ $profile->year_of_study ?? 'Not specified' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th width="40%">CGPA:</th>
                                                <td>
                                                    @if ($profile->cgpa)
                                                        <span
                                                            class="badge badge-{{ $profile->cgpa >= 3.0 ? 'success' : ($profile->cgpa >= 2.5 ? 'warning' : 'danger') }} p-2">
                                                            {{ $profile->cgpa }} / 4.0
                                                        </span>
                                                        <small
                                                            class="text-muted ml-1">({{ $profile->cgpa_percentage }}%)</small>
                                                    @else
                                                        <span class="text-muted">Not specified</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Grad Year:</th>
                                                <td>{{ $profile->expected_graduation_year ?? 'Not specified' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Attachment Status:</th>
                                                <td>
                                                    @if ($profile->attachment_status)
                                                        <span
                                                            class="badge badge-{{ $profile->attachment_status === 'placed' ? 'success' : 'info' }}">
                                                            {{ $profile->attachment_status_label }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Not specified</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Pref. Location:</th>
                                                <td>{{ $profile->preferred_location ?? 'Not specified' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if ($profile->skills && count($profile->skills) > 0)
                                    <div class="mt-3">
                                        <h6>Skills:</h6>
                                        <div>
                                            @foreach ($profile->skills as $skill)
                                                <span
                                                    class="badge badge-info p-2 mr-1 mb-1">{{ $skill }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if ($profile->interests && count($profile->interests) > 0)
                                    <div class="mt-2">
                                        <h6>Interests:</h6>
                                        <div>
                                            @foreach ($profile->interests as $interest)
                                                <span
                                                    class="badge badge-secondary p-2 mr-1 mb-1">{{ $interest }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                <p class="text-muted">No academic profile found</p>
                            @endif
                        </div>
                    </div>

                    <!-- Match Analysis Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-2"></i>
                                Match Analysis
                            </h3>
                            <div class="card-tools">
                                <span
                                    class="badge badge-{{ $matchAnalysis['overall'] >= 80 ? 'success' : ($matchAnalysis['overall'] >= 60 ? 'warning' : 'danger') }} p-2">
                                    Match Score: {{ $matchAnalysis['overall'] }}%
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>GPA Match</h6>
                                    <div class="progress mb-3" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $matchAnalysis['gpa']['match'] ? 'success' : 'danger' }}"
                                            style="width: {{ ($matchAnalysis['gpa']['score'] / 4.0) * 100 }}%">
                                            {{ $matchAnalysis['gpa']['score'] }} / 4.0
                                        </div>
                                    </div>
                                    <p class="small">
                                        Required: {{ $matchAnalysis['gpa']['required'] ?? 'Not specified' }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Location Match</h6>
                                    <div class="mb-3">
                                        @if ($matchAnalysis['location']['match'])
                                            <span class="badge badge-success p-2">
                                                <i class="fas fa-check mr-1"></i> Match
                                            </span>
                                        @else
                                            <span class="badge badge-danger p-2">
                                                <i class="fas fa-times mr-1"></i> No Match
                                            </span>
                                        @endif
                                    </div>
                                    <p class="small">
                                        Student: {{ $matchAnalysis['location']['student'] ?? 'Not specified' }}<br>
                                        Opportunity: {{ $matchAnalysis['location']['opportunity'] ?? 'Not specified' }}
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Course Match</h6>
                                    <div class="mb-3">
                                        @if ($matchAnalysis['course']['match'])
                                            <span class="badge badge-success p-2">
                                                <i class="fas fa-check mr-1"></i> Match
                                            </span>
                                        @else
                                            <span class="badge badge-danger p-2">
                                                <i class="fas fa-times mr-1"></i> No Match
                                            </span>
                                        @endif
                                    </div>
                                    <p class="small">
                                        Student: {{ $matchAnalysis['course']['student'] }}<br>
                                        Required: {{ implode(', ', $matchAnalysis['course']['required']) }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Skills Match</h6>
                                    <div class="mb-2">
                                        <span class="badge badge-info p-2">
                                            {{ count($matchAnalysis['skills']['matched']) }}/{{ count($matchAnalysis['skills']['required']) }}
                                            skills matched
                                        </span>
                                    </div>
                                    @if (count($matchAnalysis['skills']['matched']) > 0)
                                        <div class="small">
                                            <strong>Matched:</strong>
                                            @foreach ($matchAnalysis['skills']['matched'] as $skill)
                                                <span class="badge badge-success">{{ $skill }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if (count(array_diff($matchAnalysis['skills']['required'], $matchAnalysis['skills']['matched'])) > 0)
                                        <div class="small mt-1">
                                            <strong>Missing:</strong>
                                            @foreach (array_diff($matchAnalysis['skills']['required'], $matchAnalysis['skills']['matched']) as $skill)
                                                <span class="badge badge-danger">{{ $skill }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Opportunity Details Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-briefcase mr-2"></i>
                                Opportunity Details
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.opportunities.show', $application->opportunity->id) }}"
                                    class="btn btn-sm btn-default">
                                    <i class="fas fa-external-link-alt mr-1"></i> View Opportunity
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>{{ $application->opportunity->title }}</h5>
                                    <p class="text-muted">
                                        <i class="fas fa-building mr-1"></i>
                                        {{ $application->opportunity->organization->name }}
                                    </p>
                                </div>
                                <div class="col-md-6 text-right">
                                    <span
                                        class="badge badge-{{ $application->opportunity->is_open ? 'success' : 'danger' }} p-2">
                                        {{ $application->opportunity->is_open ? 'Open' : 'Closed' }}
                                    </span>
                                    @if ($application->opportunity->is_open)
                                        <small class="d-block text-muted mt-1">
                                            {{ $application->opportunity->days_remaining }} days remaining
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Type:</strong>
                                    <p class="text-muted">{{ ucfirst($application->opportunity->type) }}</p>
                                </div>
                                <div class="col-md-4">
                                    <strong>Work Type:</strong>
                                    <p class="text-muted">{{ ucfirst($application->opportunity->work_type) }}</p>
                                </div>
                                <div class="col-md-4">
                                    <strong>Location:</strong>
                                    <p class="text-muted">{{ $application->opportunity->location }}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Duration:</strong>
                                    <p class="text-muted">{{ $application->opportunity->duration_months }} months</p>
                                </div>
                                <div class="col-md-4">
                                    <strong>Stipend:</strong>
                                    <p class="text-muted">
                                        @if ($application->opportunity->stipend)
                                            KES {{ number_format($application->opportunity->stipend, 2) }}
                                        @else
                                            Not specified
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <strong>Slots:</strong>
                                    <p class="text-muted">{{ $application->opportunity->slots_available }} available
                                    </p>
                                </div>
                            </div>

                            @if ($application->opportunity->description)
                                <div class="mt-3">
                                    <strong>Description:</strong>
                                    <p class="text-muted">{{ $application->opportunity->description }}</p>
                                </div>
                            @endif

                            @if ($application->opportunity->responsibilities)
                                <div class="mt-2">
                                    <strong>Responsibilities:</strong>
                                    <p class="text-muted">{{ $application->opportunity->responsibilities }}</p>
                                </div>
                            @endif

                            @if ($application->opportunity->skills_required && count($application->opportunity->skills_required) > 0)
                                <div class="mt-2">
                                    <strong>Skills Required:</strong>
                                    <div>
                                        @foreach ($application->opportunity->skills_required as $skill)
                                            <span class="badge badge-info p-2 mr-1">{{ $skill }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($application->opportunity->courses_required && count($application->opportunity->courses_required) > 0)
                                <div class="mt-2">
                                    <strong>Courses Required:</strong>
                                    <div>
                                        @foreach ($application->opportunity->courses_required as $course)
                                            <span class="badge badge-secondary p-2 mr-1">{{ $course }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Application Timeline Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-2"></i>
                                Application Timeline
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="timeline p-3">
                                @forelse($activityLogs as $log)
                                    <div class="time-label">
                                        <span class="bg-{{ $log->properties['color'] ?? 'secondary' }}">
                                            {{ $log->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <div>
                                        <i class="fas {{ $log->icon }}"></i>
                                        <div class="timeline-item">
                                            <span class="time">
                                                <i class="fas fa-clock"></i> {{ $log->created_at->format('H:i') }}
                                            </span>
                                            <h3 class="timeline-header">
                                                @if ($log->causer)
                                                    <a
                                                        href="{{ route('admin.users.show', $log->causer_id) }}">{{ $log->causer->full_name }}</a>
                                                @else
                                                    System
                                                @endif
                                                {{ $log->description }}
                                            </h3>
                                            @if ($log->properties && count($log->properties) > 0)
                                                <div class="timeline-body">
                                                    @foreach ($log->properties as $key => $value)
                                                        @if (!in_array($key, ['color', 'icon']) && !is_null($value) && $value !== '')
                                                            <small class="text-muted d-block">
                                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                @if (is_array($value))
                                                                    {{ json_encode($value) }}
                                                                @elseif($key === 'notes' || $key === 'note')
                                                                    "{{ $value }}"
                                                                @else
                                                                    {{ $value }}
                                                                @endif
                                                            </small>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="timeline-footer">
                                                <small class="text-muted">
                                                    IP: {{ $log->ip_address ?? 'N/A' }} |
                                                    Method: {{ $log->method ?? 'N/A' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No activity logs found</p>
                                    </div>
                                @endforelse
                                <div>
                                    <i class="fas fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-sticky-note mr-2"></i>
                                Notes
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-default"
                                    wire:click="$toggle('showNotes')">
                                    <i class="fas fa-plus mr-1"></i> Add Note
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($showNotes)
                                <div class="mb-3">
                                    <textarea wire:model="newNote" class="form-control" rows="3" placeholder="Add a note..."></textarea>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-primary btn-sm" wire:click="addNote">
                                            <i class="fas fa-save mr-1"></i> Save Note
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm"
                                            wire:click="$set('showNotes', false)">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="notes-list">
                                @forelse($activityLogs->where('description', 'note_added') as $note)
                                    <div class="note-item p-2 border-bottom">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $note->causer?->full_name ?? 'System' }}</strong>
                                            <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0">{{ $note->properties['note'] ?? '' }}</p>
                                    </div>
                                @empty
                                    <p class="text-muted">No notes yet</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Status & Actions -->
                <div class="col-md-4">
                    <!-- Application Status Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>
                                Application Status
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <h1 class="display-4">
                                    {!! getApplicationStatusBadge($application->status, 'large') !!}
                                </h1>
                            </div>

                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th>Applied:</th>
                                    <td>{{ $application->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                @if ($application->submitted_at)
                                    <tr>
                                        <th>Submitted:</th>
                                        <td></td>
                                    </tr>
                                @endif
                                @if ($application->reviewed_at)
                                    <tr>
                                        <th>Reviewed:</th>
                                        <td>{{ $application->reviewed_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endif
                                @if ($application->interview_scheduled_at)
                                    <tr>
                                        <th>Interview Scheduled:</th>
                                        <td>{{ $application->interview_scheduled_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endif
                                @if ($application->offer_sent_at)
                                    <tr>
                                        <th>Offer Sent:</th>
                                        <td>{{ $application->offer_sent_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endif
                                @if ($application->offer_response_at)
                                    <tr>
                                        <th>Offer Response:</th>
                                        <td>{{ $application->offer_response_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endif
                                @if ($application->hired_at)
                                    <tr>
                                        <th>Hired:</th>
                                        <td>{{ $application->hired_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endif
                            </table>

                            <hr>

                            <h6>Status Flow</h6>
                            <div class="status-flow">
                                @foreach ($statusFlow as $status => $details)
                                    @php
                                        $isCompleted =
                                            in_array($status, ['submitted', $application->status]) ||
                                            $loop->index <= array_search($application->status, array_keys($statusFlow));
                                        $isCurrent = $status === $application->status;
                                    @endphp
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="mr-2">
                                            @if ($isCompleted)
                                                <i class="{{ $details['icon'] }} text-{{ $details['color'] }}"></i>
                                            @else
                                                <i class="far fa-circle text-muted"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="{{ $isCurrent ? 'font-weight-bold' : '' }}">
                                                {{ $details['label'] }}
                                            </span>
                                        </div>
                                        @if ($isCurrent)
                                            <span class="badge badge-primary">Current</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Documents Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-alt mr-2"></i>
                                Documents
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-pdf text-danger mr-2"></i>
                                        CV/Resume
                                    </div>
                                    <div>
                                        @if ($documentStatus['cv']['exists'])
                                            <span class="badge badge-success mr-2">Uploaded</span>
                                            <button class="btn btn-xs btn-info" wire:click="downloadDocument('cv')">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        @else
                                            <span class="badge badge-danger">Missing</span>
                                        @endif
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-alt text-info mr-2"></i>
                                        Transcript
                                    </div>
                                    <div>
                                        @if ($documentStatus['transcript']['exists'])
                                            <span class="badge badge-success mr-2">Uploaded</span>
                                            <button class="btn btn-xs btn-info"
                                                wire:click="downloadDocument('transcript')">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        @else
                                            <span class="badge badge-danger">Missing</span>
                                        @endif
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-signature text-warning mr-2"></i>
                                        School Letter
                                    </div>
                                    <div>
                                        @if ($documentStatus['school_letter']['exists'])
                                            <span class="badge badge-success mr-2">Uploaded</span>
                                            <button class="btn btn-xs btn-info"
                                                wire:click="downloadDocument('school_letter')">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        @else
                                            <span class="badge badge-danger">Missing</span>
                                        @endif
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">
                                Last updated:
                                {{ $application->student->studentProfile?->updated_at?->diffForHumans() ?? 'Never' }}
                            </small>
                        </div>
                    </div>

                    <!-- Interview Details Card -->
                    @if ($application->interview_details)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-calendar-alt mr-2 text-warning"></i>
                                    Interview Details
                                </h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th>Date:</th>
                                        <td>{{ \Carbon\Carbon::parse($application->interview_details['date'])->format('M d, Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Time:</th>
                                        <td>{{ $application->interview_details['time'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Type:</th>
                                        <td>
                                            <span
                                                class="badge badge-info">{{ ucfirst($application->interview_details['type']) }}</span>
                                        </td>
                                    </tr>
                                    @if ($application->interview_details['location'])
                                        <tr>
                                            <th>Location:</th>
                                            <td>{{ $application->interview_details['location'] }}</td>
                                        </tr>
                                    @endif
                                    @if ($application->interview_details['notes'])
                                        <tr>
                                            <th>Notes:</th>
                                            <td>{{ $application->interview_details['notes'] }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Offer Details Card -->
                    @if ($application->offer_details)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-handshake mr-2 text-success"></i>
                                    Offer Details
                                </h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th>Stipend:</th>
                                        <td><strong>KES
                                                {{ number_format($application->offer_details['stipend'], 2) }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Start Date:</th>
                                        <td>{{ \Carbon\Carbon::parse($application->offer_details['start_date'])->format('M d, Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>End Date:</th>
                                        <td>{{ \Carbon\Carbon::parse($application->offer_details['end_date'])->format('M d, Y') }}
                                        </td>
                                    </tr>
                                    @if ($application->offer_details['notes'])
                                        <tr>
                                            <th>Notes:</th>
                                            <td>{{ $application->offer_details['notes'] }}</td>
                                        </tr>
                                    @endif
                                </table>

                                @if ($application->offer_details['terms'])
                                    <hr>
                                    <h6>Terms & Conditions</h6>
                                    <p class="small text-muted">{{ $application->offer_details['terms'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Placement Card -->
                    @if ($application->placement)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-briefcase mr-2 text-success"></i>
                                    Placement Details
                                </h3>
                                <div class="card-tools">
                                    <a href="{{ route('admin.placements.show', $application->placement->id) }}"
                                        class="btn btn-sm btn-default">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span
                                                class="badge badge-{{ $application->placement->status === 'placed' ? 'success' : 'info' }}">
                                                {{ $application->placement->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Supervisor:</th>
                                        <td>{{ $application->placement->supervisor_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Department:</th>
                                        <td>{{ $application->placement->department }}</td>
                                    </tr>
                                    <tr>
                                        <th>Duration:</th>
                                        <td>
                                            {{ $application->placement->start_date->format('M d, Y') }} -
                                            {{ $application->placement->end_date->format('M d, Y') }}
                                        </td>
                                    </tr>
                                    @if ($application->placement->is_active)
                                        <tr>
                                            <th>Progress:</th>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                                        style="width: {{ $application->placement->progress_percentage }}%">
                                                        {{ $application->placement->progress_percentage }}%
                                                    </div>
                                                </div>
                                                <small
                                                    class="text-muted">{{ $application->placement->remaining_days }}
                                                    days remaining</small>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Similar Applications Card -->
                    @if ($similarApplications && $similarApplications->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-users mr-2"></i>
                                    Similar Applications
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    @foreach ($similarApplications as $similar)
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $similar->student->full_name }}</strong><br>
                                                    <small class="text-muted">
                                                        {{ $similar->student->studentProfile?->institution_name ?? 'N/A' }}
                                                    </small>
                                                </div>
                                                <div class="text-right">
                                                    {!! getApplicationStatusBadge($similar->status) !!}<br>
                                                    <small
                                                        class="text-muted">{{ $similar->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <a href="{{ route('admin.applications.show', $similar->id) }}"
                                                    class="btn btn-xs btn-default">
                                                    <i class="fas fa-eye mr-1"></i> View
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    @if ($showStatusModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Update Application Status
                        </h5>
                        <button type="button" class="close" wire:click="$set('showStatusModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>New Status</label>
                            <select wire:model="newStatus" class="form-control">
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
                            @error('newStatus')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Notes (Optional)</label>
                            <textarea wire:model="statusNotes" class="form-control" rows="3"
                                placeholder="Add any notes about this status change..."></textarea>
                            @error('statusNotes')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" wire:click="$set('showStatusModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="updateStatus">
                            Update Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Interview Modal -->
    @if ($showInterviewModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-alt mr-2 text-warning"></i>
                            Schedule Interview
                        </h5>
                        <button type="button" class="close" wire:click="$set('showInterviewModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Interview Date</label>
                                    <input type="date" wire:model="interviewDate" class="form-control"
                                        min="{{ now()->format('Y-m-d') }}">
                                    @error('interviewDate')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Interview Time</label>
                                    <input type="time" wire:model="interviewTime" class="form-control">
                                    @error('interviewTime')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Interview Type</label>
                            <select wire:model="interviewType" class="form-control">
                                <option value="online">Online</option>
                                <option value="phone">Phone</option>
                                <option value="in_person">In Person</option>
                            </select>
                            @error('interviewType')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Location/Link</label>
                            <input type="text" wire:model="interviewLocation" class="form-control"
                                placeholder="{{ $interviewType === 'online' ? 'Meeting link' : 'Physical address' }}">
                            @error('interviewLocation')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Additional Notes</label>
                            <textarea wire:model="interviewNotes" class="form-control" rows="3"
                                placeholder="Any preparation instructions or additional details..."></textarea>
                            @error('interviewNotes')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
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

    <!-- Offer Modal -->
    @if ($showOfferModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-handshake mr-2 text-success"></i>
                            Send Offer
                        </h5>
                        <button type="button" class="close" wire:click="$set('showOfferModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Monthly Stipend (KES)</label>
                                    <input type="number" wire:model="offerStipend" class="form-control"
                                        step="1000" min="0">
                                    @error('offerStipend')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" wire:model="offerStartDate" class="form-control"
                                        min="{{ now()->format('Y-m-d') }}">
                                    @error('offerStartDate')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" wire:model="offerEndDate" class="form-control"
                                        min="{{ now()->addDays(1)->format('Y-m-d') }}">
                                    @error('offerEndDate')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Offer Notes</label>
                            <textarea wire:model="offerNotes" class="form-control" rows="2"
                                placeholder="Any additional information about the offer..."></textarea>
                            @error('offerNotes')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Terms and Conditions</label>
                            <textarea wire:model="offerTerms" class="form-control" rows="4"
                                placeholder="Enter the terms and conditions for this offer..."></textarea>
                            @error('offerTerms')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" wire:click="$set('showOfferModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-success" wire:click="sendOffer">
                            <i class="fas fa-paper-plane mr-1"></i> Send Offer
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Placement Modal -->
    @if ($showPlacementModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-briefcase mr-2 text-success"></i>
                            Create Placement
                        </h5>
                        <button type="button" class="close" wire:click="$set('showPlacementModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Supervisor Name</label>
                                    <input type="text" wire:model="placementSupervisorName" class="form-control"
                                        placeholder="Full name of supervisor">
                                    @error('placementSupervisorName')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Supervisor Contact</label>
                                    <input type="text" wire:model="placementSupervisorContact"
                                        class="form-control" placeholder="Email or phone number">
                                    @error('placementSupervisorContact')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Department/Unit</label>
                            <input type="text" wire:model="placementDepartment" class="form-control"
                                placeholder="e.g., IT Department, Marketing, etc.">
                            @error('placementDepartment')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" wire:model="placementStartDate" class="form-control">
                                    @error('placementStartDate')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" wire:model="placementEndDate" class="form-control">
                                    @error('placementEndDate')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Additional Notes</label>
                            <textarea wire:model="placementNotes" class="form-control" rows="3"
                                placeholder="Any additional information about the placement..."></textarea>
                            @error('placementNotes')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                            wire:click="$set('showPlacementModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-success" wire:click="createPlacement">
                            <i class="fas fa-check-circle mr-1"></i> Create Placement
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Feedback Modal -->
    @if ($showFeedbackModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-comment mr-2 text-info"></i>
                            Send Feedback
                        </h5>
                        <button type="button" class="close" wire:click="$set('showFeedbackModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Feedback Type</label>
                            <select wire:model="feedbackType" class="form-control">
                                <option value="general">General Feedback</option>
                                <option value="interview">Interview Feedback</option>
                                <option value="offer">Offer Related</option>
                                <option value="rejection">Rejection Reason</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea wire:model="feedbackMessage" class="form-control" rows="5"
                                placeholder="Enter your feedback message..."></textarea>
                            @error('feedbackMessage')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" wire:click="$set('showFeedbackModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-info" wire:click="sendFeedback">
                            <i class="fas fa-paper-plane mr-1"></i> Send Feedback
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    @push('styles')
        <style>
            .timeline {
                position: relative;
                margin: 0 0 30px 0;
                padding: 0;
                list-style: none;
            }

            .timeline:before {
                position: absolute;
                top: 0;
                bottom: 0;
                left: 30px;
                width: 3px;
                content: "";
                background-color: #e9ecef;
            }

            .timeline>div {
                position: relative;
                margin-right: 10px;
                margin-bottom: 15px;
            }

            .time-label>span {
                font-weight: 600;
                padding: 5px;
                display: inline-block;
                background-color: #fff;
                border-radius: 4px;
            }

            .timeline>div>.timeline-item {
                margin-top: 0;
                margin-left: 60px;
                margin-right: 15px;
                margin-bottom: 0;
                border-radius: 3px;
                background: #fff;
                border: 1px solid rgba(0, 0, 0, .125);
                padding: 10px;
                position: relative;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
            }

            .timeline>div>.timeline-item>.time {
                color: #999;
                float: right;
                font-size: 12px;
                padding: 10px;
            }

            .timeline>div>.timeline-item>.timeline-header {
                margin: 0;
                color: #555;
                border-bottom: 1px solid rgba(0, 0, 0, .05);
                padding: 10px;
                font-size: 16px;
                line-height: 1.1;
            }

            .timeline>div>.timeline-item>.timeline-body {
                padding: 10px;
            }

            .timeline>div>.timeline-item>.timeline-footer {
                padding: 10px;
            }

            .timeline>div>i {
                color: #fff;
                width: 30px;
                height: 30px;
                font-size: 15px;
                line-height: 30px;
                position: absolute;
                border-radius: 50%;
                text-align: center;
                left: 18px;
                top: 0;
            }

            .status-flow {
                max-height: 400px;
                overflow-y: auto;
                padding-right: 10px;
            }

            .note-item {
                transition: background-color 0.2s;
            }

            .note-item:hover {
                background-color: #f8f9fa;
            }

            .avatar-initials {
                width: 40px;
                height: 40px;
                line-height: 40px;
                text-align: center;
                border-radius: 50%;
                color: white;
                font-weight: bold;
                font-size: 16px;
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
                        timeOut: 5000
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
