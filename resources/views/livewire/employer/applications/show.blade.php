<div>
    {{-- Success is as dangerous as failure. --}}
    
    <!-- Application Header -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt mr-2"></i>
                        Application #{{ $application->id }}
                    </h3>
                    <div>
                        {!! $application->status_badge !!}
                        @if ($application->match_score)
                            <span
                                class="badge badge-{{ $application->match_score >= 80 ? 'success' : ($application->match_score >= 50 ? 'warning' : 'danger') }} ml-2 px-3 py-2">
                                <i class="fas fa-star mr-1"></i>
                                {{ $application->match_score }}% Match
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Application Progress</span>
                            <span>{{ $application->progress_percentage }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-{{ $application->status_color }}" role="progressbar"
                                style="width: {{ $application->progress_percentage }}%">
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'details' ? 'active' : '' }}"
                                wire:click="switchTab('details')" href="#">
                                <i class="fas fa-info-circle mr-1"></i> Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'student' ? 'active' : '' }}"
                                wire:click="switchTab('student')" href="#">
                                <i class="fas fa-user-graduate mr-1"></i> Student Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'history' ? 'active' : '' }}"
                                wire:click="switchTab('history')" href="#">
                                <i class="fas fa-history mr-1"></i> History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'interviews' ? 'active' : '' }}"
                                wire:click="switchTab('interviews')" href="#">
                                <i class="fas fa-calendar-check mr-1"></i> Interviews
                            </a>
                        </li>
                    </ul>

                    <!-- Details Tab -->
                    @if ($activeTab === 'details')
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Application Information</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Status:</th>
                                        <td>{!! $application->status_badge !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Opportunity:</th>
                                        <td>
                                            <strong>{{ $application->opportunity->title ?? 'N/A' }}</strong>
                                            <br>
                                            <small
                                                class="text-muted">{{ $application->opportunity->type ?? '' }}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Match Score:</th>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="font-weight-bold mr-2">{{ $application->match_score ?? 0 }}%</span>
                                                @if ($application->match_quality)
                                                    <span
                                                        class="badge badge-info">{{ $application->match_quality }}</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @if ($application->matched_criteria)
                                        <tr>
                                            <th>Matched Criteria:</th>
                                            <td>
                                                @foreach ($application->matched_criteria as $criteria)
                                                    <span class="badge badge-success mr-1 mb-1">
                                                        <i class="fas fa-check mr-1"></i>
                                                        {{ ucfirst($criteria) }}
                                                    </span>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Applied Date:</th>
                                        <td>{{ $application->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    @if ($application->reviewed_at)
                                        <tr>
                                            <th>Reviewed Date:</th>
                                            <td>{{ $application->reviewed_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    @endif
                                </table>

                                @if ($application->offer_details)
                                    <h5 class="mt-4">Offer Details</h5>
                                    <div class="alert alert-success">
                                        <table class="table table-sm table-borderless mb-0">
                                            @if ($application->offer_stipend)
                                                <tr>
                                                    <th width="40%">Stipend:</th>
                                                    <td>{{ $application->offer_stipend }}</td>
                                                </tr>
                                            @endif
                                            @if ($application->offer_start_date)
                                                <tr>
                                                    <th>Start Date:</th>
                                                    <td>{{ $application->offer_start_date }}</td>
                                                </tr>
                                            @endif
                                            @if ($application->offer_end_date)
                                                <tr>
                                                    <th>End Date:</th>
                                                    <td>{{ $application->offer_end_date }}</td>
                                                </tr>
                                            @endif
                                            @if (isset($application->offer_details['terms']))
                                                <tr>
                                                    <th>Terms:</th>
                                                    <td>{{ $application->offer_details['terms'] }}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                @endif

                                @if ($application->employer_notes)
                                    <h5 class="mt-4">Employer Notes</h5>
                                    <div class="alert alert-info">
                                        {{ $application->employer_notes }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h5>Actions</h5>
                                <div class="list-group">
                                    <!-- Available Status Transitions -->
                                    @foreach ($availableStatuses as $status)
                                        <button type="button" class="list-group-item list-group-item-action"
                                            wire:click="showStatusUpdate('{{ $status['value'] }}')">
                                            <i
                                                class="fas fa-{{ $status['icon'] ?? 'arrow-right' }} mr-2 text-{{ $status['color'] }}"></i>
                                            {{ $status['label'] }}
                                            @if (isset($status['description']))
                                                <br>
                                                <small class="text-muted">{{ $status['description'] }}</small>
                                            @endif
                                        </button>
                                    @endforeach

                                    @if (in_array($application->status->value, ['pending', 'under_review', 'reviewing', 'shortlisted']))
                                        <button type="button" class="list-group-item list-group-item-action"
                                            wire:click="showInterviewForm">
                                            <i class="fas fa-calendar-check mr-2 text-info"></i>
                                            Schedule Interview
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action"
                                            wire:click="showOfferForm">
                                            <i class="fas fa-handshake mr-2 text-success"></i>
                                            Send Offer
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action"
                                            wire:click="showRejectForm">
                                            <i class="fas fa-times-circle mr-2 text-danger"></i>
                                            Reject Application
                                        </button>
                                    @endif

                                    @if ($application->status === ApplicationStatus::OFFER_ACCEPTED || $application->status === ApplicationStatus::HIRED)
                                        @if (!$application->placement)
                                            <button type="button" class="list-group-item list-group-item-action"
                                                wire:click="showPlacementForm">
                                                <i class="fas fa-user-check mr-2 text-success"></i>
                                                Create Placement
                                            </button>
                                        @endif
                                    @endif
                                </div>

                                @if ($application->placement)
                                    <h5 class="mt-4">Placement Information</h5>
                                    <div class="alert alert-success">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <th width="40%">Status:</th>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $application->placement->status === 'placed' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($application->placement->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Start Date:</th>
                                                <td>{{ $application->placement->start_date?->format('M d, Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>End Date:</th>
                                                <td>{{ $application->placement->end_date?->format('M d, Y') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Student Tab -->
                    @if ($activeTab === 'student')
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Personal Information</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Full Name:</th>
                                        <td>{{ $application->student->full_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>
                                            <a href="mailto:{{ $application->student->email }}">
                                                {{ $application->student->email }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Phone:</th>
                                        <td>{{ $application->student->phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Gender:</th>
                                        <td>{{ $application->student->gender ? ucfirst($application->student->gender) : 'N/A' }}
                                        </td>
                                    </tr>
                                    @if ($application->student->county)
                                        <tr>
                                            <th>County:</th>
                                            <td>{{ $application->student->county }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                @if ($application->student->studentProfile)
                                    <h5>Academic Information</h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%">Institution:</th>
                                            <td>{{ $application->student->studentProfile->institution ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Course:</th>
                                            <td>{{ $application->student->studentProfile->course ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>GPA:</th>
                                            <td>{{ $application->student->studentProfile->gpa ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Year of Study:</th>
                                            <td>{{ $application->student->studentProfile->year_of_study ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        @if ($application->student->studentProfile->skills)
                                            <tr>
                                                <th>Skills:</th>
                                                <td>
                                                    @if (is_array($application->student->studentProfile->skills))
                                                        @foreach ($application->student->studentProfile->skills as $skill)
                                                            <span
                                                                class="badge badge-info mr-1 mb-1">{{ $skill }}</span>
                                                        @endforeach
                                                    @else
                                                        {{ $application->student->studentProfile->skills }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- History Tab -->
                    @if ($activeTab === 'history')
                        @if ($application->history->isNotEmpty())
                            <div class="timeline">
                                @foreach ($application->history as $history)
                                    <div class="time-label">
                                        <span class="bg-secondary">
                                            {{ $history->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <div>
                                        @php
                                            $iconMap = [
                                                'created' => 'fa-plus-circle bg-info',
                                                'status_changed' => 'fa-arrow-right bg-warning',
                                                'reviewed' => 'fa-check-circle bg-primary',
                                                'shortlisted' => 'fa-star bg-warning',
                                                'interview_scheduled' => 'fa-calendar-check bg-info',
                                                'interview_completed' => 'fa-check-double bg-success',
                                                'offer_sent' => 'fa-handshake bg-success',
                                                'offer_accepted' => 'fa-check-circle bg-success',
                                                'offer_rejected' => 'fa-times-circle bg-danger',
                                                'rejected' => 'fa-times-circle bg-danger',
                                                'placement_created' => 'fa-briefcase bg-primary',
                                                'hired' => 'fa-user-check bg-success',
                                            ];
                                            $icon = $iconMap[$history->action] ?? 'fa-circle bg-secondary';
                                        @endphp
                                        <i class="fas {{ $icon }}"></i>
                                        <div class="timeline-item">
                                            <span class="time">
                                                <i class="fas fa-clock"></i>
                                                {{ $history->created_at->format('H:i') }}
                                            </span>
                                            <h3 class="timeline-header">
                                                {{ ucfirst(str_replace('_', ' ', $history->action)) }}
                                                @if ($history->user)
                                                    <small>by {{ $history->user->full_name }}</small>
                                                @endif
                                            </h3>
                                            <div class="timeline-body">
                                                @if ($history->notes)
                                                    <p>{{ $history->notes }}</p>
                                                @endif
                                                @if ($history->old_status && $history->new_status)
                                                    <small class="text-muted">
                                                        Status changed from
                                                        <span
                                                            class="badge badge-secondary">{{ $history->old_status }}</span>
                                                        to
                                                        <span
                                                            class="badge badge-primary">{{ $history->new_status }}</span>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No history records available</p>
                            </div>
                        @endif
                    @endif

                    <!-- Interviews Tab -->
                    @if ($activeTab === 'interviews')
                        @if ($application->interviews->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Type</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($application->interviews as $interview)
                                            <tr>
                                                <td>{{ $interview->scheduled_at->format('M d, Y H:i') }}</td>
                                                <td>{{ ucfirst($interview->type) }}</td>
                                                <td>{{ $interview->location }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $interview->status === 'scheduled' ? 'info' : ($interview->status === 'completed' ? 'success' : 'secondary') }}">
                                                        {{ ucfirst($interview->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $interview->notes ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No interviews scheduled</p>
                                @if (in_array($application->status->value, ['pending', 'under_review', 'reviewing', 'shortlisted']))
                                    <button type="button" class="btn btn-primary" wire:click="showInterviewForm">
                                        <i class="fas fa-plus mr-1"></i> Schedule Interview
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Student Quick Info -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <div class="avatar-initials bg-info img-circle d-flex align-items-center justify-content-center mx-auto"
                            style="width: 80px; height: 80px; color: white; font-size: 28px;">
                            {{ $application->student->initials() ?? 'N/A' }}
                        </div>
                        <h5 class="profile-username text-center mt-2">
                            {{ $application->student->full_name ?? 'N/A' }}
                        </h5>
                        <p class="text-muted text-center">
                            {{ $application->student->studentProfile->course ?? 'Student' }}
                        </p>
                    </div>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Application ID</b>
                            <span class="float-right">#{{ $application->id }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Status</b>
                            <span class="float-right">{!! $application->status_badge !!}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Match Score</b>
                            <span class="float-right">
                                <span
                                    class="badge badge-{{ ($application->match_score ?? 0) >= 80 ? 'success' : 'warning' }}">
                                    {{ $application->match_score ?? 0 }}%
                                </span>
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Applied</b>
                            <span class="float-right">{{ $application->created_at->diffForHumans() }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Organization Info -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building mr-1"></i> Organization
                    </h3>
                </div>
                <div class="card-body">
                    <strong>{{ $organization->name }}</strong>
                    <br>
                    <small class="text-muted">{{ $organization->industry ?? '' }}</small>
                    @if ($organization->county)
                        <br>
                        <small class="text-muted">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            {{ $organization->county }}
                        </small>
                    @endif
                </div>
            </div>

            <!-- Payment Info -->
            @if ($application->paymentTransaction)
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-money-bill mr-1"></i> Payment
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span
                                        class="badge badge-{{ $application->paymentTransaction->status === 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($application->paymentTransaction->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Amount:</th>
                                <td>KSh {{ number_format($application->paymentTransaction->amount ?? 0, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Status Update Modal -->
    @if ($showStatusModal)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="fas fa-edit mr-2"></i>Update Status
                        </h5>
                        <button type="button" class="close text-white" wire:click="$set('showStatusModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form wire:submit="updateStatus">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>New Status</label>
                                <select class="form-control" wire:model="newStatus">
                                    @foreach ($availableStatuses as $status)
                                        <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea class="form-control" wire:model="statusNotes" rows="3"
                                    placeholder="Add notes about this status change..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                wire:click="$set('showStatusModal', false)">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save mr-1"></i> Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Include other modals (Interview, Offer, Reject, Placement) similar to ApplicationsList -->
    <!-- Interview Modal -->
    @if ($showInterviewModal)
        <!-- Same as ApplicationsList interview modal -->
    @endif

    <!-- Offer Modal -->
    @if ($showOfferModal)
        <!-- Same as ApplicationsList offer modal -->
    @endif

    <!-- Reject Modal -->
    @if ($showRejectModal)
        <!-- Same as ApplicationsList reject modal -->
    @endif

    <!-- Placement Modal -->
    @if ($showPlacementModal)
        <!-- Same as ApplicationsList placement modal -->
    @endif

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('notify', (data) => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    Toast.fire({
                        icon: data[0].type || 'success',
                        title: data[0].message || 'Action completed'
                    });
                });
            });
        </script>
    @endpush

</div>
