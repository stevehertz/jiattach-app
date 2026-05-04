<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-2 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalApplications }}</h3>
                    <p>Total</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingCount }}</h3>
                    <p>Pending</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $reviewedCount }}</h3>
                    <p>Reviewed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $offeredCount }}</h3>
                    <p>Offered</p>
                </div>
                <div class="icon">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $acceptedCount }}</h3>
                    <p>Accepted</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $rejectedCount }}</h3>
                    <p>Rejected</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-file-alt mr-2"></i>Applications
            </h3>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                            placeholder="Search by name, email, opportunity...">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-control form-control-sm" wire:model.live="filterStatus">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="shortlisted">Shortlisted</option>
                        <option value="interviewing">Interviewing</option>
                        <option value="offered">Offered</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control form-control-sm" wire:model.live="filterOpportunity">
                        <option value="">All Opportunities</option>
                        @foreach ($opportunities as $opportunity)
                            <option value="{{ $opportunity->id }}">{{ $opportunity->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control form-control-sm" wire:model.live="filterScore">
                        <option value="">All Scores</option>
                        <option value="high">High (80-100%)</option>
                        <option value="medium">Medium (50-79%)</option>
                        <option value="low">Low (Below 50%)</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <select class="form-control form-control-sm" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th wire:click="sortBy('id')" style="cursor: pointer;">
                                ID
                                @if ($sortField === 'id')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Student</th>
                            <th>Opportunity</th>
                            <th wire:click="sortBy('match_score')" style="cursor: pointer;">
                                Match Score
                                @if ($sortField === 'match_score')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Status</th>
                            <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                Applied Date
                                @if ($sortField === 'created_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                            <tr>
                                <td>#{{ $application->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <div class="avatar-initials bg-info img-circle d-flex align-items-center justify-content-center"
                                                style="width: 35px; height: 35px; color: white; font-size: 12px;">
                                                {{ $application->student->initials() ?? 'N/A' }}
                                            </div>
                                        </div>
                                        <div>
                                            <strong>{{ $application->student->full_name ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $application->student->email ?? '' }}</small>
                                            @if ($application->student->studentProfile)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-graduation-cap mr-1"></i>
                                                    {{ $application->student->studentProfile->course ?? 'N/A' }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $application->opportunity->title ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $application->opportunity->type ?? 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    @php
                                        $score = $application->match_score ?? 0;
                                        $badgeClass = $score >= 80 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }} px-3 py-2">
                                        <h6 class="mb-0">{{ $score }}%</h6>
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge badge-{{ $application->status === 'pending'
                                            ? 'warning'
                                            : ($application->status === 'offered'
                                                ? 'primary'
                                                : ($application->status === 'accepted'
                                                    ? 'success'
                                                    : ($application->status === 'rejected'
                                                        ? 'danger'
                                                        : ($application->status === 'interviewing'
                                                            ? 'info'
                                                            : 'secondary')))) }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                    @if ($application->status === 'interviewing' && $application->interview_date)
                                        <br>
                                        <small class="text-info">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $application->interview_date->format('M d, Y H:i') }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $application->created_at->format('M d, Y') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $application->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <!-- View Button -->
                                        <button type="button" class="btn btn-info"
                                            wire:click="viewApplication({{ $application->id }})"
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Quick Status Updates -->
                                        @if ($application->status === 'pending')
                                            <button type="button" class="btn btn-secondary"
                                                wire:click="updateStatus({{ $application->id }}, 'reviewed')"
                                                title="Mark as Reviewed">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif

                                        @if (in_array($application->status, ['pending', 'reviewed', 'shortlisted']))
                                            <button type="button" class="btn btn-primary"
                                                wire:click="showInterviewForm({{ $application->id }})"
                                                title="Schedule Interview">
                                                <i class="fas fa-calendar-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-success"
                                                wire:click="showOfferForm({{ $application->id }})"
                                                title="Send Offer">
                                                <i class="fas fa-handshake"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger"
                                                wire:click="showRejectForm({{ $application->id }})" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        @if ($application->status === 'accepted')
                                            <button type="button" class="btn btn-success"
                                                wire:click="createPlacement({{ $application->id }})"
                                                title="Create Placement">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No applications found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $applications->links() }}
                <div class="text-muted small">
                    @if ($applications->total() > 0)
                        Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }}
                        of {{ $applications->total() }} applications
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- View Application Modal -->
    @if ($showViewModal && $viewingApplication)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title">
                            <i class="fas fa-file-alt mr-2"></i>
                            Application Details #{{ $viewingApplication->id }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="closeViewModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Student Information</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Name:</th>
                                        <td>{{ $viewingApplication->student->full_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>
                                            <a href="mailto:{{ $viewingApplication->student->email }}">
                                                {{ $viewingApplication->student->email }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Phone:</th>
                                        <td>{{ $viewingApplication->student->phone ?? 'N/A' }}</td>
                                    </tr>
                                    @if ($viewingApplication->student->studentProfile)
                                        <tr>
                                            <th>Institution:</th>
                                            <td>{{ $viewingApplication->student->studentProfile->institution ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Course:</th>
                                            <td>{{ $viewingApplication->student->studentProfile->course ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>GPA:</th>
                                            <td>{{ $viewingApplication->student->studentProfile->gpa ?? 'N/A' }}
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Application Information</h5>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Opportunity:</th>
                                        <td>{{ $viewingApplication->opportunity->title ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Match Score:</th>
                                        <td>
                                            <span
                                                class="badge badge-{{ ($viewingApplication->match_score ?? 0) >= 80 ? 'success' : 'warning' }}">
                                                {{ $viewingApplication->match_score ?? 0 }}%
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span
                                                class="badge badge-{{ $viewingApplication->status === 'pending' ? 'warning' : 'info' }}">
                                                {{ ucfirst($viewingApplication->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Applied:</th>
                                        <td>{{ $viewingApplication->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" wire:click="closeViewModal">
                            <i class="fas fa-times mr-1"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Offer Modal -->
    @if ($showOfferModal)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title">
                            <i class="fas fa-handshake mr-2"></i>Send Offer
                        </h5>
                        <button type="button" class="close text-white" wire:click="$set('showOfferModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form wire:submit="sendOffer">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Offer Message <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('offerMessage') is-invalid @enderror" wire:model="offerMessage" rows="4"
                                    placeholder="Write your offer message to the student..."></textarea>
                                @error('offerMessage')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                wire:click="$set('showOfferModal', false)">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane mr-1"></i> Send Offer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Interview Modal -->
    @if ($showInterviewModal)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-check mr-2"></i>Schedule Interview
                        </h5>
                        <button type="button" class="close text-white"
                            wire:click="$set('showInterviewModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form wire:submit="scheduleInterview">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Interview Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local"
                                    class="form-control @error('interviewDate') is-invalid @enderror"
                                    wire:model="interviewDate">
                                @error('interviewDate')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Interview Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('interviewType') is-invalid @enderror"
                                    wire:model="interviewType">
                                    <option value="in-person">In Person</option>
                                    <option value="virtual">Virtual</option>
                                    <option value="phone">Phone</option>
                                </select>
                                @error('interviewType')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Location/Link <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('interviewLocation') is-invalid @enderror"
                                    wire:model="interviewLocation" placeholder="Address or virtual meeting link">
                                @error('interviewLocation')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Additional Notes</label>
                                <textarea class="form-control @error('interviewNotes') is-invalid @enderror" wire:model="interviewNotes"
                                    rows="3" placeholder="Any additional information for the candidate..."></textarea>
                                @error('interviewNotes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                wire:click="$set('showInterviewModal', false)">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-calendar-check mr-1"></i> Schedule Interview
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Modal -->
    @if ($showRejectModal)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title">
                            <i class="fas fa-times-circle mr-2"></i>Reject Application
                        </h5>
                        <button type="button" class="close text-white" wire:click="$set('showRejectModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form wire:submit="rejectApplication">
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                This action cannot be undone.
                            </div>
                            <div class="form-group">
                                <label>Reason for Rejection <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('rejectionReason') is-invalid @enderror" wire:model="rejectionReason"
                                    rows="3" placeholder="Please provide a reason..."></textarea>
                                @error('rejectionReason')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                wire:click="$set('showRejectModal', false)">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times mr-1"></i> Reject Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
