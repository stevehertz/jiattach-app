<div>
    <!-- Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        @switch($viewType)
                            @case('today')
                                Today's Interviews
                                @break
                            @case('upcoming')
                                Upcoming Interviews
                                @break
                            @case('completed')
                                Completed Interviews
                                @break
                            @case('pending')
                                Pending Follow-up
                                @break
                            @default
                                Interview Stage
                        @endswitch
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.applications.index') }}">Applications</a></li>
                        <li class="breadcrumb-item active">Interview Stage</li>
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
                            <i class="fas fa-calendar-alt"></i>
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
                            <span class="info-box-text">Today</span>
                            <span class="info-box-number">{{ $stats['today'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary elevation-1">
                            <i class="fas fa-calendar-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Upcoming</span>
                            <span class="info-box-number">{{ $stats['upcoming'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1">
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
                        <span class="info-box-icon bg-danger elevation-1">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending</span>
                            <span class="info-box-number">{{ $stats['pending'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary elevation-1">
                            <i class="fas fa-times-circle"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">No Show</span>
                            <span class="info-box-number">{{ $stats['no_show'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick View Tabs -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link {{ $viewType === 'all' ? 'active' : '' }}"
                                       href="{{ route('admin.applications.interviewing') }}">
                                        All Interviews
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $viewType === 'today' ? 'active' : '' }}"
                                       href="#">
                                        <i class="fas fa-sun mr-1"></i> Today
                                        @if($stats['today'] > 0)
                                            <span class="badge badge-warning ml-1">{{ $stats['today'] }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $viewType === 'upcoming' ? 'active' : '' }}"
                                       href="#">
                                        <i class="fas fa-calendar-alt mr-1"></i> Upcoming
                                        @if($stats['upcoming'] > 0)
                                            <span class="badge badge-primary ml-1">{{ $stats['upcoming'] }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $viewType === 'completed' ? 'active' : '' }}"
                                       href="#">
                                        <i class="fas fa-check-circle mr-1"></i> Completed
                                        @if($stats['completed'] > 0)
                                            <span class="badge badge-success ml-1">{{ $stats['completed'] }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $viewType === 'pending' ? 'active' : '' }}"
                                       href="#">
                                        <i class="fas fa-clock mr-1"></i> Pending Follow-up
                                        @if($stats['pending'] > 0)
                                            <span class="badge badge-danger ml-1">{{ $stats['pending'] }}</span>
                                        @endif
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions Bar -->
            @if(count($selectedInterviews) > 0)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card card-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">
                                            <i class="fas fa-check-square mr-2"></i>
                                            {{ count($selectedInterviews) }} interview(s) selected
                                        </h5>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info"
                                                wire:click="$set('bulkAction', 'send_reminders')"
                                                wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-bell mr-1"></i> Send Reminders
                                        </button>
                                        <button type="button" class="btn btn-success"
                                                wire:click="$set('bulkAction', 'mark_completed')"
                                                wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-check mr-1"></i> Mark Completed
                                        </button>
                                        <button type="button" class="btn btn-primary"
                                                wire:click="$set('bulkAction', 'export')"
                                                wire:click="$set('showBulkActionModal', true)">
                                            <i class="fas fa-download mr-1"></i> Export
                                        </button>
                                        <button type="button" class="btn btn-default"
                                                wire:click="$set('selectedInterviews', [])">
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
                        @if($viewType === 'today')
                            Today's Interviews ({{ $stats['today'] }})
                        @elseif($viewType === 'upcoming')
                            Upcoming Interviews ({{ $stats['upcoming'] }})
                        @elseif($viewType === 'completed')
                            Completed Interviews ({{ $stats['completed'] }})
                        @elseif($viewType === 'pending')
                            Pending Follow-up ({{ $stats['pending'] }})
                        @else
                            All Interviews ({{ $stats['total'] }})
                        @endif
                    </h3>

                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 300px;">
                            <input type="text" wire:model.live.debounce.500ms="search"
                                   class="form-control float-right" placeholder="Search interviews...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" wire:click="$set('search', '')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body border-bottom">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Interview Type</label>
                                <select wire:model.live="interviewType" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="online">Online</option>
                                    <option value="phone">Phone</option>
                                    <option value="in_person">In Person</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select wire:model.live="interviewStatus" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="rescheduled">Rescheduled</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="no_show">No Show</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Organization</label>
                                <select wire:model.live="organizationFilter" class="form-control">
                                    <option value="">All Organizations</option>
                                    @foreach($organizations as $org)
                                        <option value="{{ $org->id }}">{{ $org->name }}</option>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sort By</label>
                                <select wire:model.live="sortField" class="form-control">
                                    <option value="scheduled_at">Interview Date</option>
                                    <option value="created_at">Created Date</option>
                                    <option value="type">Type</option>
                                    <option value="status">Status</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sort Direction</label>
                                <select wire:model.live="sortDirection" class="form-control">
                                    <option value="asc">Ascending</option>
                                    <option value="desc">Descending</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-default" wire:click="resetFilters">
                                <i class="fas fa-redo mr-1"></i> Reset Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Interviews Table -->
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
                                    <th>Student</th>
                                    <th>Opportunity</th>
                                    <th>Interview Details</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($interviews as $interview)
                                    <tr wire:key="interview-{{ $interview->id }}">
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                       id="interview-{{ $interview->id }}"
                                                       value="{{ $interview->id }}" wire:model="selectedInterviews">
                                                <label class="custom-control-label"
                                                       for="interview-{{ $interview->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    @php
                                                        $initials = getInitials($interview->application->student->full_name);
                                                        $colors = ['primary', 'success', 'info', 'warning'];
                                                        $color = $colors[crc32($interview->application->student->email) % count($colors)];
                                                    @endphp
                                                    <div class="avatar-initials bg-{{ $color }} img-circle"
                                                         style="width: 40px; height: 40px; line-height: 40px; text-align: center; color: white; font-weight: bold;">
                                                        {{ $initials }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>{{ $interview->application->student->full_name }}</strong>
                                                    <div class="text-muted small">{{ $interview->application->student->email }}</div>
                                                    <div class="text-muted small">
                                                        {{ $interview->application->student->studentProfile?->institution_name ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $interview->application->opportunity->title }}</strong>
                                            <div class="text-muted small">
                                                {{ $interview->application->opportunity->organization->name }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-building mr-1"></i>
                                                {{ $interview->application->opportunity->location }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-{{ $interview->scheduled_at->isToday() ? 'warning' : 'primary' }}">
                                                <i class="fas fa-calendar mr-1"></i>
                                                <strong>{{ $interview->scheduled_at->format('M d, Y') }}</strong>
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $interview->scheduled_at->format('h:i A') }}
                                                ({{ $interview->duration_formatted }})
                                            </div>
                                            @if($interview->meeting_details)
                                                <div class="text-muted small">
                                                    <i class="fas fa-{{ $interview->type === 'online' ? 'video' : ($interview->type === 'phone' ? 'phone' : 'map-marker-alt') }} mr-1"></i>
                                                    {{ $interview->meeting_details }}
                                                </div>
                                            @endif
                                            @if($interview->interviewer)
                                                <div class="text-muted small">
                                                    <i class="fas fa-user-tie mr-1"></i>
                                                    {{ $interview->interviewer->full_name }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $interview->type_badge }} p-2">
                                                <i class="fas {{ $interview->type_icon }} mr-1"></i>
                                                {{ ucfirst($interview->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            {!! $interview->status_badge !!}
                                            @if($interview->reminder_sent_at)
                                                <div class="text-muted small mt-1">
                                                    <i class="fas fa-bell mr-1"></i>
                                                    Reminder sent
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info"
                                                        wire:click="viewInterview({{ $interview->id }})"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <button type="button" class="btn btn-warning"
                                                        wire:click="editInterview({{ $interview->id }})"
                                                        title="Edit Interview">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                @if($interview->status === 'scheduled' && $interview->scheduled_at->isFuture())
                                                    <button type="button" class="btn btn-success"
                                                            wire:click="sendReminder({{ $interview->id }})"
                                                            title="Send Reminder">
                                                        <i class="fas fa-bell"></i>
                                                    </button>
                                                @endif

                                                @if($interview->status === 'scheduled' && $interview->scheduled_at->isPast())
                                                    <button type="button" class="btn btn-success"
                                                            wire:click="confirmComplete({{ $interview->id }})"
                                                            title="Mark Completed">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif

                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                                            data-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a href="{{ route('admin.applications.show', $interview->application_id) }}"
                                                           class="dropdown-item">
                                                            <i class="fas fa-file-alt mr-2"></i> View Application
                                                        </a>

                                                        <div class="dropdown-divider"></div>

                                                        @if($interview->status !== 'rescheduled')
                                                            <button class="dropdown-item text-warning"
                                                                    wire:click="confirmReschedule({{ $interview->id }})">
                                                                <i class="fas fa-calendar-alt mr-2"></i> Reschedule
                                                            </button>
                                                        @endif

                                                        @if($interview->status !== 'completed')
                                                            <button class="dropdown-item text-success"
                                                                    wire:click="confirmComplete({{ $interview->id }})">
                                                                <i class="fas fa-check-circle mr-2"></i> Mark Completed
                                                            </button>
                                                        @endif

                                                        @if($interview->status === 'scheduled')
                                                            <button class="dropdown-item text-danger"
                                                                    wire:click="markNoShow({{ $interview->id }})">
                                                                <i class="fas fa-user-slash mr-2"></i> Mark No Show
                                                            </button>
                                                        @endif

                                                        @if($interview->status !== 'cancelled')
                                                            <button class="dropdown-item text-danger"
                                                                    wire:click="confirmCancel({{ $interview->id }})">
                                                                <i class="fas fa-times-circle mr-2"></i> Cancel
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No interviews found</h5>
                                            @if($search || $interviewType || $interviewStatus || $organizationFilter || $dateFrom || $dateTo)
                                                <p class="text-muted">Try adjusting your filters</p>
                                                <button wire:click="resetFilters" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-redo mr-1"></i> Reset Filters
                                                </button>
                                            @else
                                                <p class="text-muted">No interviews scheduled yet.</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="card-footer clearfix">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">
                                Showing {{ $interviews->firstItem() ?? 0 }} to {{ $interviews->lastItem() ?? 0 }}
                                of {{ $interviews->total() }} interviews
                            </span>
                        </div>
                        <div>
                            {{ $interviews->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Interview Modal -->
    @if($showInterviewModal && $editingInterview)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Interview for {{ $editingInterview->application->student->full_name }}
                        </h5>
                        <button type="button" class="close" wire:click="$set('showInterviewModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" wire:model="interviewDate" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Time <span class="text-danger">*</span></label>
                                    <input type="time" wire:model="interviewTime" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Type <span class="text-danger">*</span></label>
                                    <select wire:model="interviewType_new" class="form-control">
                                        <option value="online">Online</option>
                                        <option value="phone">Phone</option>
                                        <option value="in_person">In Person</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Duration (minutes)</label>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select wire:model="interviewStatus_new" class="form-control">
                                        <option value="scheduled">Scheduled</option>
                                        <option value="rescheduled">Rescheduled</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="no_show">No Show</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if($interviewType_new === 'online')
                            <div class="form-group">
                                <label>Meeting Link</label>
                                <input type="url" wire:model="interviewMeetingLink" class="form-control"
                                       placeholder="https://meet.google.com/...">
                            </div>
                        @endif

                        @if($interviewType_new === 'phone')
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" wire:model="interviewPhoneNumber" class="form-control"
                                       placeholder="+254 XXX XXX XXX">
                            </div>
                        @endif

                        @if($interviewType_new === 'in_person')
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" wire:model="interviewLocation" class="form-control"
                                       placeholder="Office address, room number">
                            </div>
                        @endif

                        <div class="form-group">
                            <label>Interviewer</label>
                            <select wire:model="interviewerId" class="form-control">
                                <option value="">Select Interviewer</option>
                                @foreach($editingInterview->application->opportunity->organization->users as $user)
                                    <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea wire:model="interviewNotes" class="form-control" rows="3"
                                      placeholder="Additional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                wire:click="$set('showInterviewModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="updateInterview">
                            <i class="fas fa-save mr-1"></i> Update Interview
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Bulk Action Modal -->
    @if($showBulkActionModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if($bulkAction === 'send_reminders')
                                <i class="fas fa-bell text-info mr-2"></i> Send Reminders
                            @elseif($bulkAction === 'mark_completed')
                                <i class="fas fa-check text-success mr-2"></i> Mark as Completed
                            @elseif($bulkAction === 'export')
                                <i class="fas fa-download text-primary mr-2"></i> Export Interviews
                            @endif
                        </h5>
                        <button type="button" class="close" wire:click="$set('showBulkActionModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if($bulkAction === 'send_reminders')
                            <p>Send reminders for <strong>{{ count($selectedInterviews) }}</strong> selected interview(s)?</p>
                        @elseif($bulkAction === 'mark_completed')
                            <p>Mark <strong>{{ count($selectedInterviews) }}</strong> selected interview(s) as completed?</p>
                            <p class="text-muted">This will update the interview status and application progress.</p>
                        @elseif($bulkAction === 'export')
                            <p>Export <strong>{{ count($selectedInterviews) }}</strong> selected interview(s) to CSV?</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                wire:click="$set('showBulkActionModal', false)">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="applyBulkAction">
                            @if($bulkAction === 'send_reminders')
                                <i class="fas fa-bell mr-1"></i> Send Reminders
                            @elseif($bulkAction === 'mark_completed')
                                <i class="fas fa-check mr-1"></i> Mark Completed
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

            .nav-pills .nav-link.active {
                background-color: #007bff;
            }

            .nav-pills .nav-link {
                color: #495057;
            }

            .badge {
                font-size: 0.85rem;
                padding: 0.4rem 0.6rem;
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

                Livewire.on('confirm-action', (data) => {
                    Swal.fire({
                        icon: 'warning',
                        title: data[0].title,
                        text: data[0].text,
                        showCancelButton: true,
                        confirmButtonText: data[0].confirmButtonText,
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (data[0].action === 'reschedule') {
                                Livewire.dispatch('rescheduleInterview', { interviewId: data[0].interviewId });
                            } else if (data[0].action === 'cancel') {
                                Livewire.dispatch('cancelInterview', { interviewId: data[0].interviewId });
                            }
                        }
                    });
                });

                Livewire.on('show-complete-modal', (data) => {
                    Swal.fire({
                        title: 'Mark Interview as Completed',
                        input: 'textarea',
                        inputLabel: 'Feedback (Optional)',
                        inputPlaceholder: 'Enter any feedback or notes about the interview...',
                        showCancelButton: true,
                        confirmButtonText: 'Mark Completed',
                        cancelButtonText: 'Cancel',
                        inputValidator: (value) => {
                            // Optional validation
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.dispatch('completeInterview', {
                                interviewId: data[0].interviewId,
                                feedback: result.value
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
</div>