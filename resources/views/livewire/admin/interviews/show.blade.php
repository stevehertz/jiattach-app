<div>
    {{-- Be like water. --}}
    <!-- Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Interview Details
                        <small class="text-muted">#{{ $interview->id }}</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.applications.index') }}">Applications</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.applications.interviewing') }}">Interview
                                Stage</a></li>
                        <li class="breadcrumb-item active">Interview #{{ $interview->id }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Quick Stats Row -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-stats border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-primary-soft mr-3">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0">Scheduled For</p>
                                    <h5 class="mb-0">{{ $interview->scheduled_at->format('M d, Y') }}</h5>
                                    <small class="text-muted">{{ $interview->scheduled_at->format('h:i A') }}</small>
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
                                    <i class="fas fa-clock text-success"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0">Duration</p>
                                    <h5 class="mb-0">{{ $interview->duration_formatted }}</h5>
                                    <small class="text-muted">Minutes: {{ $interview->duration_minutes }}</small>
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
                                    <i class="fas fa-user-tie text-info"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0">Interviewer</p>
                                    <h5 class="mb-0">{{ $interview->interviewer?->full_name ?? 'Not Assigned' }}</h5>
                                    <small class="text-muted">{{ $interview->interviewer?->email ?? '' }}</small>
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
                                    <i class="fas fa-tag text-warning"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0">Type</p>
                                    <h5 class="mb-0">{{ ucfirst($interview->type) }}</h5>
                                    <small class="text-muted">
                                        @if ($interview->type === 'online')
                                            <i class="fas fa-video mr-1"></i> Video Call
                                        @elseif($interview->type === 'phone')
                                            <i class="fas fa-phone mr-1"></i> Phone Call
                                        @else
                                            <i class="fas fa-building mr-1"></i> In Person
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="btn-group mb-2 mb-sm-0">
                                    <button type="button" class="btn btn-outline-secondary"
                                        wire:click="goToPreviousInterview">
                                        <i class="fas fa-chevron-left mr-1"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary"
                                        wire:click="goToNextInterview">
                                        Next <i class="fas fa-chevron-right ml-1"></i>
                                    </button>
                                </div>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary" wire:click="sendReminder"
                                        @if ($interview->reminder_sent_at) disabled @endif>
                                        <i class="fas fa-bell mr-1"></i>
                                        {{ $interview->reminder_sent_at ? 'Reminder Sent' : 'Send Reminder' }}
                                    </button>
                                    <button type="button" class="btn btn-outline-info" wire:click="goToApplication">
                                        <i class="fas fa-file-alt mr-1"></i> View Application
                                    </button>
                                    <button type="button" class="btn btn-outline-success" wire:click="goToStudent">
                                        <i class="fas fa-user-graduate mr-1"></i> View Student
                                    </button>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-warning dropdown-toggle"
                                            data-toggle="dropdown">
                                            <i class="fas fa-bolt mr-1"></i> Actions
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @if ($interview->status === \App\Enums\InterviewStatus::SCHEDULED)
                                                <button class="dropdown-item" wire:click="openRescheduleModal">
                                                    <i class="fas fa-calendar-alt text-warning mr-2"></i> Reschedule
                                                </button>
                                                <button class="dropdown-item" wire:click="openCompleteInterviewModal">
                                                    <i class="fas fa-check-circle text-success mr-2"></i> Mark Completed
                                                </button>
                                                <button class="dropdown-item" wire:click="markNoShow">
                                                    <i class="fas fa-user-slash text-danger mr-2"></i> Mark No Show
                                                </button>
                                                <div class="dropdown-divider"></div>
                                                <button class="dropdown-item" wire:click="openCancelModal">
                                                    <i class="fas fa-times-circle text-danger mr-2"></i> Cancel
                                                    Interview
                                                </button>
                                            @elseif($interview->status === \App\Enums\InterviewStatus::RESCHEDULED)
                                                <button class="dropdown-item" wire:click="openRescheduleModal">
                                                    <i class="fas fa-calendar-alt text-warning mr-2"></i> Reschedule
                                                    Again
                                                </button>
                                                <button class="dropdown-item" wire:click="openCompleteModal">
                                                    <i class="fas fa-check-circle text-success mr-2"></i> Mark
                                                    Completed
                                                </button>
                                            @elseif($interview->status === \App\Enums\InterviewStatus::CONFIRMED)
                                                <button class="dropdown-item" wire:click="openCompleteModal">
                                                    <i class="fas fa-check-circle text-success mr-2"></i> Mark
                                                    Completed
                                                </button>
                                            @endif
                                            <button class="dropdown-item" wire:click="openFeedbackModal">
                                                <i class="fas fa-comment text-info mr-2"></i> Add Feedback
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column - Main Information -->
                <div class="col-lg-8">
                    <!-- Interview Tabs -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0">
                            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#overview" role="tab">
                                        <i class="fas fa-info-circle mr-1"></i>Overview
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#participants" role="tab">
                                        <i class="fas fa-users mr-1"></i>Participants
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#timeline" role="tab">
                                        <i class="fas fa-history mr-1"></i>Timeline
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#notes" role="tab">
                                        <i class="fas fa-sticky-note mr-1"></i>Notes & Feedback
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
                                            <h5 class="mb-3">
                                                <i class="fas fa-user-graduate text-primary mr-2"></i>
                                                Student Information
                                            </h5>
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-circle bg-primary mr-3">
                                                    <span
                                                        class="initials">{{ getInitials($interview->application->student->full_name) }}</span>
                                                </div>
                                                <div>
                                                    <h5 class="mb-0">
                                                        {{ $interview->application->student->full_name }}</h5>
                                                    <small class="text-muted">Student</small>
                                                </div>
                                            </div>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="35%"><i
                                                            class="fas fa-envelope text-muted mr-2"></i>Email</td>
                                                    <td><a
                                                            href="mailto:{{ $interview->application->student->email }}">{{ $interview->application->student->email }}</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fas fa-phone text-muted mr-2"></i>Phone</td>
                                                    <td>{{ formatPhoneNumber($interview->application->student->phone) ?? 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fas fa-university text-muted mr-2"></i>Institution
                                                    </td>
                                                    <td>{{ $interview->application->student->studentProfile?->institution_name ?? 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fas fa-book text-muted mr-2"></i>Course</td>
                                                    <td>{{ $interview->application->student->studentProfile?->course_name ?? 'N/A' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fas fa-star text-muted mr-2"></i>CGPA</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-{{ $interview->application->student->studentProfile?->cgpa >= 3.0 ? 'success' : 'warning' }}">
                                                            {{ $interview->application->student->studentProfile?->cgpa ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>

                                        <!-- Interview Details -->
                                        <div class="col-md-6 mb-4">
                                            <h5 class="mb-3">
                                                <i class="fas fa-calendar-check text-warning mr-2"></i>
                                                Interview Details
                                            </h5>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="40%"><i
                                                            class="fas fa-calendar text-muted mr-2"></i>Date & Time
                                                    </td>
                                                    <td>
                                                        <strong>{{ $interview->scheduled_at->format('l, M d, Y') }}</strong><br>
                                                        <span
                                                            class="text-muted">{{ $interview->scheduled_at->format('h:i A') }}</span>
                                                        @if ($interview->scheduled_at->isToday())
                                                            <span class="badge badge-warning ml-2">Today</span>
                                                        @elseif($interview->scheduled_at->isFuture())
                                                            <span class="badge badge-info ml-2">
                                                                {{ $interview->scheduled_at->diffForHumans() }}
                                                            </span>
                                                        @elseif($interview->scheduled_at->isPast() && $interview->status !== 'completed')
                                                            <span class="badge badge-danger ml-2">Overdue</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fas fa-clock text-muted mr-2"></i>Duration</td>
                                                    <td>{{ $interview->duration_formatted }}</td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fas fa-tag text-muted mr-2"></i>Type</td>
                                                    <td>
                                                        <span class="badge badge-{{ $interview->type_badge }} p-2">
                                                            <i class="fas {{ $interview->type_icon }} mr-1"></i>
                                                            {{ ucfirst($interview->type) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fas fa-info-circle text-muted mr-2"></i>Status</td>
                                                    <td>{!! $interview->status_badge !!}</td>
                                                </tr>
                                                @if ($interview->meeting_details)
                                                    <tr>
                                                        <td><i class="fas fa-link text-muted mr-2"></i>Meeting Details
                                                        </td>
                                                        <td>
                                                            @if ($interview->type === 'online')
                                                                <a href="{{ $interview->meeting_link }}"
                                                                    target="_blank" class="text-primary">
                                                                    {{ $interview->meeting_link }}
                                                                </a>
                                                            @elseif($interview->type === 'phone')
                                                                <a
                                                                    href="tel:{{ $interview->phone_number }}">{{ $interview->phone_number }}</a>
                                                            @else
                                                                {{ $interview->location }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if ($interview->reminder_sent_at)
                                                    <tr>
                                                        <td><i class="fas fa-bell text-muted mr-2"></i>Reminder Sent
                                                        </td>
                                                        <td>{{ $interview->reminder_sent_at->format('M d, Y h:i A') }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Opportunity Details -->
                                    <hr class="my-4">
                                    <h5 class="mb-3">
                                        <i class="fas fa-briefcase text-primary mr-2"></i>
                                        Opportunity Details
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>{{ $interview->application->opportunity->title }}</h6>
                                            <p class="text-muted">
                                                <i class="fas fa-building mr-1"></i>
                                                {{ $interview->application->opportunity->organization->name }}
                                            </p>
                                        </div>
                                        <div class="col-md-6 text-md-right">
                                            <span
                                                class="badge badge-{{ $interview->application->opportunity->is_open ? 'success' : 'danger' }} p-2">
                                                {{ $interview->application->opportunity->is_open ? 'Open' : 'Closed' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-3 col-6 mb-2">
                                            <small class="text-muted d-block">Location</small>
                                            <span>{{ $interview->application->opportunity->location }}</span>
                                        </div>
                                        <div class="col-md-3 col-6 mb-2">
                                            <small class="text-muted d-block">Work Type</small>
                                            <span>{{ ucfirst($interview->application->opportunity->work_type) }}</span>
                                        </div>
                                        <div class="col-md-3 col-6 mb-2">
                                            <small class="text-muted d-block">Duration</small>
                                            <span>{{ $interview->application->opportunity->duration_months }}
                                                months</span>
                                        </div>
                                        <div class="col-md-3 col-6 mb-2">
                                            <small class="text-muted d-block">Stipend</small>
                                            <span>{{ formatCurrency($interview->application->opportunity->stipend) }}</span>
                                        </div>
                                    </div>

                                    <!-- Interview Notes -->
                                    @if ($interview->notes)
                                        <hr class="my-4">
                                        <h5 class="mb-3">
                                            <i class="fas fa-sticky-note text-info mr-2"></i>
                                            Interview Notes
                                        </h5>
                                        <div class="bg-light p-3 rounded">
                                            {{ $interview->notes }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Participants Tab -->
                                <div class="tab-pane" id="participants" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0 mb-3">
                                                <div class="card-body">
                                                    <h5 class="mb-3">
                                                        <i class="fas fa-user-graduate text-primary mr-2"></i>
                                                        Student
                                                    </h5>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="avatar-circle bg-primary mr-3"
                                                            style="width: 60px; height: 60px; line-height: 60px; font-size: 24px;">
                                                            {{ getInitials($interview->application->student->full_name) }}
                                                        </div>
                                                        <div>
                                                            <h5 class="mb-1">
                                                                {{ $interview->application->student->full_name }}</h5>
                                                            <p class="mb-1 text-muted">
                                                                {{ $interview->application->student->email }}</p>
                                                            <p class="mb-0 text-muted">
                                                                {{ formatPhoneNumber($interview->application->student->phone) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="small">
                                                        <strong>Academic Info:</strong><br>
                                                        {{ $interview->application->student->studentProfile?->institution_name }}<br>
                                                        {{ $interview->application->student->studentProfile?->course_name }}
                                                        (Year
                                                        {{ $interview->application->student->studentProfile?->year_of_study }})
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card bg-light border-0 mb-3">
                                                <div class="card-body">
                                                    <h5 class="mb-3">
                                                        <i class="fas fa-user-tie text-success mr-2"></i>
                                                        Interviewer
                                                    </h5>
                                                    @if ($interview->interviewer)
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="avatar-circle bg-success mr-3"
                                                                style="width: 60px; height: 60px; line-height: 60px; font-size: 24px;">
                                                                {{ getInitials($interview->interviewer->full_name) }}
                                                            </div>
                                                            <div>
                                                                <h5 class="mb-1">
                                                                    {{ $interview->interviewer->full_name }}</h5>
                                                                <p class="mb-1 text-muted">
                                                                    {{ $interview->interviewer->email }}</p>
                                                                <p class="mb-0 text-muted">
                                                                    {{ formatPhoneNumber($interview->interviewer->phone) }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="small">
                                                            <strong>Role:</strong>
                                                            {{ $interview->interviewer->getRoleNames()->first() ?? 'Interviewer' }}<br>
                                                            @if ($interview->interviewer->organizations->count() > 0)
                                                                <strong>Organization:</strong>
                                                                {{ $interview->interviewer->organizations->first()->name }}
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="text-center py-4">
                                                            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                                            <p class="text-muted">No interviewer assigned</p>
                                                            <button class="btn btn-sm btn-primary"
                                                                wire:click="editInterview({{ $interview->id }})">
                                                                <i class="fas fa-plus mr-1"></i> Assign Interviewer
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="card bg-light border-0">
                                                <div class="card-body">
                                                    <h5 class="mb-3">
                                                        <i class="fas fa-building text-info mr-2"></i>
                                                        Organization Representatives
                                                    </h5>
                                                    <div class="row">
                                                        @forelse($interview->application->opportunity->organization->users as $user)
                                                            <div class="col-md-4 mb-3">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar-circle bg-info mr-2"
                                                                        style="width: 40px; height: 40px; line-height: 40px;">
                                                                        {{ getInitials($user->full_name) }}
                                                                    </div>
                                                                    <div>
                                                                        <strong>{{ $user->full_name }}</strong>
                                                                        <div class="small text-muted">
                                                                            {{ $user->pivot->role }}</div>
                                                                        <div class="small">{{ $user->email }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="col-12 text-center py-3">
                                                                <p class="text-muted mb-0">No representatives found</p>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Timeline Tab -->
                                <div class="tab-pane" id="timeline" role="tabpanel">
                                    <div class="timeline-modern">
                                        @php
                                            $groupedEvents = collect($timelineEvents)->groupBy(
                                                fn($event) => \Carbon\Carbon::parse($event['created_at'])->format(
                                                    'Y-m-d',
                                                ),
                                            );
                                        @endphp

                                        @forelse($groupedEvents as $date => $events)
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

                                            @foreach ($events as $event)
                                                <div class="timeline-item-modern">
                                                    <div class="timeline-dot bg-{{ $event['color'] }}">
                                                        <i class="fas {{ $event['icon'] }}"></i>
                                                    </div>
                                                    <div class="timeline-content">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <div>
                                                                <strong>{{ $event['causer'] }}</strong>
                                                                <span class="text-muted mx-2">•</span>
                                                                <span class="text-muted">
                                                                    {{ \Carbon\Carbon::parse($event['created_at'])->format('h:i A') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <p class="mb-2">{{ $event['description'] }}</p>

                                                        @if (isset($event['properties']['notes']) && $event['properties']['notes'])
                                                            <div class="small bg-light p-2 rounded mt-2">
                                                                <i class="fas fa-quote-left text-muted mr-1"></i>
                                                                {{ $event['properties']['notes'] }}
                                                            </div>
                                                        @endif

                                                        @if (isset($event['properties']['metadata']['rating']))
                                                            <div class="mt-2">
                                                                <span class="text-muted small">Rating:</span>
                                                                {!! getRatingStars($event['properties']['metadata']['rating']) !!}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @empty
                                            <div class="text-center py-5">
                                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No timeline events found</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Notes & Feedback Tab -->
                                <div class="tab-pane" id="notes" role="tabpanel">
                                    <!-- Add Note Form -->
                                    <div class="mb-4">
                                        <div class="input-group">
                                            <textarea wire:model="newNote" class="form-control" rows="2" placeholder="Add a note or comment..."></textarea>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" wire:click="addNote">
                                                    <i class="fas fa-plus mr-1"></i> Add Note
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Notes List -->
                                    <div class="notes-list">
                                        @php
                                            $notes = collect($timelineEvents)
                                                ->filter(function ($event) {
                                                    return $event['type'] === 'history' &&
                                                        in_array($event['properties']['action'], [
                                                            'note_added',
                                                            'feedback_added',
                                                        ]);
                                                })
                                                ->sortByDesc('created_at');
                                        @endphp

                                        @forelse($notes as $note)
                                            <div class="note-item-modern p-3 border-bottom">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <strong>{{ $note['causer'] }}</strong>
                                                    <small
                                                        class="text-muted">{{ timeAgo($note['created_at']) }}</small>
                                                </div>
                                                <p class="mb-1">{{ $note['description'] }}</p>
                                                @if (isset($note['properties']['metadata']['type']))
                                                    <small class="badge badge-info">
                                                        {{ ucfirst($note['properties']['metadata']['type']) }}
                                                    </small>
                                                @endif
                                                @if (isset($note['properties']['metadata']['rating']))
                                                    <div class="mt-1">
                                                        {!! getRatingStars($note['properties']['metadata']['rating']) !!}
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="text-center py-4">
                                                <i class="fas fa-sticky-note fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No notes yet</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Status & Related Info -->
                <div class="col-lg-4">
                    <!-- Status Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle text-primary mr-2"></i>
                                Interview Status
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="status-circle-wrapper">
                                    <div class="status-circle status-{{ $interview->status }}">
                                        <i class="fas {{ $interview->type_icon }}"></i>
                                    </div>
                                </div>
                                <h4 class="mt-3">
                                    {{ $interview->status }}
                                </h4>
                                @if ($interview->status === 'scheduled' && $interview->scheduled_at->isToday())
                                    <span class="badge badge-warning p-2">Today</span>
                                @elseif($interview->status === 'scheduled' && $interview->scheduled_at->isFuture())
                                    <span
                                        class="badge badge-info p-2">{{ $interview->scheduled_at->diffForHumans() }}</span>
                                @elseif($interview->status === 'completed')
                                    <span class="badge badge-success p-2">Completed
                                        {{ timeAgo($interview->completed_at) }}</span>
                                @elseif($interview->status === 'cancelled')
                                    <span class="badge badge-danger p-2">Cancelled</span>
                                @endif
                            </div>

                            <!-- Quick Actions -->
                            <div class="d-grid gap-2">
                                @if ($interview->status === 'scheduled')
                                    <button class="btn btn-warning btn-block" wire:click="openRescheduleModal">
                                        <i class="fas fa-calendar-alt mr-2"></i> Reschedule
                                    </button>
                                    <button class="btn btn-success btn-block" wire:click="openCompleteModal">
                                        <i class="fas fa-check-circle mr-2"></i> Mark Completed
                                    </button>
                                    <button class="btn btn-danger btn-block" wire:click="openCancelModal">
                                        <i class="fas fa-times-circle mr-2"></i> Cancel Interview
                                    </button>
                                @elseif($interview->status === 'rescheduled')
                                    <button class="btn btn-success btn-block" wire:click="openCompleteModal">
                                        <i class="fas fa-check-circle mr-2"></i> Mark Completed
                                    </button>
                                @endif
                                <button class="btn btn-info btn-block" wire:click="sendReminder">
                                    <i class="fas fa-bell mr-2"></i> Send Reminder
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Application Summary Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt text-primary mr-2"></i>
                                Application Summary
                            </h5>
                            <a href="{{ route('admin.applications.show', $interview->application_id) }}"
                                class="btn btn-sm btn-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="45%"><i class="fas fa-hashtag text-muted mr-2"></i>Application ID
                                    </td>
                                    <td><strong>#{{ $interview->application_id }}</strong></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-alt text-muted mr-2"></i>Applied On</td>
                                    <td>{{ formatDate($interview->application->created_at) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-chart-line text-muted mr-2"></i>Match Score</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $interview->application->match_score >= 80 ? 'success' : ($interview->application->match_score >= 60 ? 'warning' : 'danger') }}">
                                            {{ $interview->application->match_score ?? 'N/A' }}%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-tag text-muted mr-2"></i>Status</td>
                                    <td>{!! getApplicationStatusBadge($interview->application->status) !!}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Documents Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt text-primary mr-2"></i>
                                Student Documents
                            </h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @php
                                $profile = $interview->application->student->studentProfile;
                                $documents = [
                                    'cv' => [
                                        'label' => 'CV/Resume',
                                        'icon' => 'fa-file-pdf text-danger',
                                        'url' => $profile?->cv_url,
                                    ],
                                    'transcript' => [
                                        'label' => 'Academic Transcript',
                                        'icon' => 'fa-file-alt text-info',
                                        'url' => $profile?->transcript_url,
                                    ],
                                    'school_letter' => [
                                        'label' => 'School Letter',
                                        'icon' => 'fa-file-signature text-warning',
                                        'url' => $profile?->school_letter_url,
                                    ],
                                ];
                            @endphp

                            @foreach ($documents as $key => $doc)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas {{ $doc['icon'] }} mr-2"></i>
                                        {{ $doc['label'] }}
                                    </div>
                                    <div>
                                        @if ($doc['url'])
                                            <a href="{{ asset('storage/' . $doc['url']) }}" target="_blank"
                                                class="btn btn-sm btn-link">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else
                                            <span class="badge badge-danger">Missing</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Similar Applications -->
                    @if ($similarApplications && $similarApplications->count() > 0)
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">
                                    <i class="fas fa-users text-primary mr-2"></i>
                                    Other Applicants
                                </h5>
                            </div>
                            <div class="list-group list-group-flush">
                                @foreach ($similarApplications as $app)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $app->student->full_name }}</strong>
                                                <div class="small text-muted">
                                                    {{ $app->student->studentProfile?->institution_name ?? 'N/A' }}
                                                </div>
                                            </div>
                                            <div>
                                                <span
                                                    class="badge badge-{{ $app->status?->color() ?? 'secondary' }} mr-2">
                                                    {{ $app->status?->label() ?? 'Pending' }}
                                                </span>
                                                <a href="{{ route('admin.applications.show', $app->id) }}"
                                                    class="btn btn-xs btn-default">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @if ($showRescheduleModal)
        @include('livewire.admin.interviews.partials.reschedule-modal')
    @endif

    @if ($showCancelModal)
        @include('livewire.admin.interviews.partials.cancel-modal')
    @endif

    @if ($showCompleteModal)
        @include('livewire.admin.interviews.partials.complete-modal')
    @endif

    @if ($showFeedbackModal)
        @include('livewire.admin.interviews.partials.feedback-modal')
    @endif

    @if ($showCompleteInterviewModal)
        @include('livewire.admin.interviews.partials.complete-interview-modal')
    @endif

    @push('styles')
        <style>
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
                background: rgba(0, 123, 255, 0.1);
            }

            .bg-success-soft {
                background: rgba(40, 167, 69, 0.1);
            }

            .bg-info-soft {
                background: rgba(23, 162, 184, 0.1);
            }

            .bg-warning-soft {
                background: rgba(255, 193, 7, 0.1);
            }

            .avatar-circle {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                font-size: 1.2rem;
            }

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

            .status-scheduled {
                background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            }

            .status-rescheduled {
                background: linear-gradient(135deg, #ffc107 0%, #d39e00 100%);
            }

            .status-confirmed {
                background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
            }

            .status-completed {
                background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            }

            .status-cancelled {
                background: linear-gradient(135deg, #dc3545 0%, #bd2130 100%);
            }

            .status-no_show {
                background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
            }

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

            .timeline-content {
                background: #f8f9fa;
                border-radius: 10px;
                padding: 15px;
            }

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
            }

            .note-item-modern {
                transition: background-color 0.2s;
            }

            .note-item-modern:hover {
                background-color: #f8f9fa;
            }

            .nav-tabs .nav-link {
                border: none;
                color: #6c757d;
                font-weight: 500;
            }

            .nav-tabs .nav-link.active {
                color: #007bff;
                background: none;
                border-bottom: 2px solid #007bff;
            }

            .modal.show {
                display: block;
                background-color: rgba(0, 0, 0, 0.5);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('show-toast', (data) => {
                    let toast = Array.isArray(data) ? data[0] : data;
                    toastr[toast.type](toast.message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000
                    });
                });

                // Auto-hide modals on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        @this.set('showRescheduleModal', false);
                        @this.set('showCancelModal', false);
                        @this.set('showCompleteModal', false);
                        @this.set('showFeedbackModal', false);
                    }
                });
            });
        </script>
    @endpush

</div>
