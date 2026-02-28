<x-layouts.student>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">My Placement Status</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Status</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            @if($placement && $placement->status == 'placed')
                <!-- STATE 1: PLACED (User has started work) -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-success card-outline shadow-sm">
                            <div class="card-body box-profile">
                                <div class="text-center mb-3">
                                    <div class="icon-circle bg-success mx-auto shadow-sm" style="width: 80px; height: 80px; display:flex; align-items:center; justify-content:center; border-radius:50%">
                                        <i class="fas fa-check fa-3x text-white"></i>
                                    </div>
                                </div>
                                <h3 class="profile-username text-center font-weight-bold">Placed Successfully</h3>
                                <p class="text-muted text-center">{{ $placement->organization->name }}</p>

                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Start Date</b> <span class="float-right">{{ $placement->start_date->format('d M, Y') }}</span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Duration</b> <span class="float-right">{{ $placement->duration_months }} Months</span>
                                    </li>
                                </ul>
                                <button class="btn btn-outline-success btn-block">Download Attachment Letter</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h3 class="card-title font-weight-bold">Placement Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="text-muted small text-uppercase">Department</label>
                                        <div class="font-weight-bold">{{ $placement->department ?? 'General' }}</div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="text-muted small text-uppercase">Supervisor</label>
                                        <div class="font-weight-bold">{{ $placement->supervisor_name ?? 'Pending Assignment' }}</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="alert alert-light border">
                                            <i class="fas fa-info-circle text-info mr-2"></i>
                                            Your logbook will be enabled on the start date.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($application && $application->status == 'accepted')
                <!-- STATE 2: APPLICATION ACCEPTED (Waiting for Admin) -->
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card card-warning card-outline shadow">
                            <div class="card-body text-center p-5">
                                <div class="icon-circle bg-warning mx-auto mb-4 shadow-sm" style="width: 100px; height: 100px; display:flex; align-items:center; justify-content:center; border-radius:50%">
                                    <i class="fas fa-clock fa-4x text-white"></i>
                                </div>
                                
                                <h2 class="font-weight-bold text-dark">Application Accepted!</h2>
                                <p class="lead text-muted mb-4">
                                    Thank you for accepting the match with 
                                    <strong class="text-warning">{{ $application->opportunity->organization->name }}</strong>.
                                </p>

                                <div class="alert alert-info text-left mb-4">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Next Steps:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Your acceptance has been recorded</li>
                                        <li>An administrator will review your application</li>
                                        <li>You will be notified once the placement is confirmed</li>
                                        <li>Expected response time: 24-48 hours</li>
                                    </ul>
                                </div>

                                <div class="row justify-content-center mb-4">
                                    <div class="col-md-10">
                                        <div class="bg-light p-4 rounded text-left">
                                            <h6 class="font-weight-bold mb-3">Application Details:</h6>
                                            <div class="row">
                                                <div class="col-sm-6 mb-2">
                                                    <small class="text-muted d-block">Organization:</small>
                                                    <b>{{ $application->opportunity->organization->name }}</b>
                                                </div>
                                                <div class="col-sm-6 mb-2">
                                                    <small class="text-muted d-block">Position:</small>
                                                    <b>{{ $application->opportunity->title }}</b>
                                                </div>
                                                <div class="col-sm-6 mb-2">
                                                    <small class="text-muted d-block">Location:</small>
                                                    <b>{{ $application->opportunity->county ?? $application->opportunity->location }}</b>
                                                </div>
                                                <div class="col-sm-6 mb-2">
                                                    <small class="text-muted d-block">Match Score:</small>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 mr-2" style="height: 8px;">
                                                            <div class="progress-bar bg-success" style="width: {{ $application->match_score }}%"></div>
                                                        </div>
                                                        <span class="font-weight-bold">{{ $application->match_score }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if($application->accepted_at)
                                            <hr>
                                            <div class="text-center text-muted small">
                                                <i class="fas fa-check-circle text-success mr-1"></i>
                                                Accepted on {{ $application->accepted_at->format('d M, Y \a\t h:i A') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('student.placement.timeline') }}" class="btn btn-primary px-5 shadow">
                                        <i class="fas fa-chart-line mr-2"></i> View Timeline
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($application && in_array($application->status, ['reviewing', 'shortlisted', 'offered']))
                <!-- STATE 3: APPLICATION UNDER REVIEW (Admin is processing) -->
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card card-info card-outline shadow">
                            <div class="card-body text-center p-5">
                                <div class="icon-circle bg-info mx-auto mb-4 shadow-sm" style="width: 100px; height: 100px; display:flex; align-items:center; justify-content:center; border-radius:50%">
                                    <i class="fas fa-search fa-4x text-white"></i>
                                </div>
                                
                                <h2 class="font-weight-bold text-dark">Application Under Review</h2>
                                <p class="lead text-muted mb-4">
                                    Your application for 
                                    <strong class="text-info">{{ $application->opportunity->organization->name }}</strong> 
                                    is being reviewed.
                                </p>

                                <div class="alert alert-info text-left mb-4">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Current Status:</strong>
                                    <span class="badge badge-{{ $application->status_badge }} ml-2 p-2">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                    <p class="mt-2 mb-0">An administrator is reviewing your application. You'll be notified once there's an update.</p>
                                </div>

                                <div class="row justify-content-center mb-4">
                                    <div class="col-md-10">
                                        <div class="bg-light p-4 rounded text-left">
                                            <h6 class="font-weight-bold mb-3">Application Summary:</h6>
                                            <div class="row">
                                                <div class="col-sm-6 mb-2">
                                                    <small class="text-muted d-block">Organization:</small>
                                                    <b>{{ $application->opportunity->organization->name }}</b>
                                                </div>
                                                <div class="col-sm-6 mb-2">
                                                    <small class="text-muted d-block">Position:</small>
                                                    <b>{{ $application->opportunity->title }}</b>
                                                </div>
                                                <div class="col-sm-12 mb-2">
                                                    <small class="text-muted d-block">Status Timeline:</small>
                                                    <div class="d-flex justify-content-between mt-2">
                                                        <div class="text-center">
                                                            <div class="circle bg-success"></div>
                                                            <small>Submitted</small>
                                                        </div>
                                                        <div class="text-center">
                                                            <div class="circle {{ in_array($application->status, ['reviewing', 'shortlisted', 'offered']) ? 'bg-success' : 'bg-secondary' }}"></div>
                                                            <small>Review</small>
                                                        </div>
                                                        <div class="text-center">
                                                            <div class="circle {{ $application->status == 'offered' ? 'bg-success' : 'bg-secondary' }}"></div>
                                                            <small>Offer</small>
                                                        </div>
                                                        <div class="text-center">
                                                            <div class="circle bg-secondary"></div>
                                                            <small>Placement</small>
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
                </div>

            @elseif($pendingMatch)
                <!-- STATE 4: MATCH FOUND (Action Required) -->
                <div class="card card-primary card-outline shadow">
                    <div class="card-body text-center p-5">
                        <div class="icon-circle bg-primary mx-auto mb-4 shadow-sm" style="width: 100px; height: 100px; display:flex; align-items:center; justify-content:center; border-radius:50%">
                            <i class="fas fa-handshake fa-4x text-white"></i>
                        </div>
                        
                        <h2 class="font-weight-bold text-dark">We found a match for you!</h2>
                        <p class="lead text-muted mb-4">
                            The system has matched your profile with an opportunity at 
                            <strong class="text-primary">{{ $pendingMatch->opportunity->organization->name }}</strong>.
                        </p>

                        <div class="row justify-content-center mb-4">
                            <div class="col-md-8">
                                <div class="bg-light p-3 rounded text-left">
                                    <div class="row">
                                        <div class="col-sm-6"><small class="text-muted">Role:</small> <b>{{ $pendingMatch->opportunity->title }}</b></div>
                                        <div class="col-sm-6"><small class="text-muted">Location:</small> <b>{{ $pendingMatch->opportunity->county ?? $pendingMatch->opportunity->location }}</b></div>
                                    </div>
                                    @if($pendingMatch->match_score)
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-muted">Match Score:</small>
                                            <div class="progress mt-1" style="height: 10px;">
                                                <div class="progress-bar bg-success" style="width: {{ $pendingMatch->match_score }}%"></div>
                                            </div>
                                            <span class="small font-weight-bold">{{ $pendingMatch->match_score }}%</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <!-- Decline Button with Modal Trigger -->
                            <button type="button" class="btn btn-outline-danger mr-2 px-4" data-toggle="modal" data-target="#declineMatchModal">
                                <i class="fas fa-times-circle mr-2"></i> Decline Match
                            </button>
                            
                            <!-- Accept Button with Modal Trigger -->
                            <button type="button" class="btn btn-success px-5 shadow" data-toggle="modal" data-target="#acceptMatchModal">
                                <i class="fas fa-check-circle mr-2"></i> Accept Placement
                            </button>
                        </div>
                    </div>
                </div>

            @else
                <!-- STATE 5: SYSTEM MATCHING (In Progress) -->
                <div class="row justify-content-center mt-5">
                    <div class="col-md-8 text-center">
                        <div class="mb-4 position-relative d-inline-block">
                            <i class="fas fa-cog fa-spin fa-5x text-secondary opacity-25"></i>
                            <i class="fas fa-search position-absolute text-primary" style="font-size: 2rem; bottom: -5px; right: -5px;"></i>
                        </div>
                        
                        <h2 class="font-weight-bold text-gray-800">System is Analyzing Your Profile</h2>
                        <p class="text-muted lead">
                            Our algorithm is currently scanning available opportunities to find the best match for your skills and location.
                        </p>
                        
                        <div class="card bg-light border-0 mt-4 mx-auto" style="max-width: 500px;">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-left mb-3">Matching Criteria:</h6>
                                <ul class="list-unstyled text-left small mb-0">
                                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Course: {{ Auth::user()->studentProfile->course_name ?? 'Not set' }}</li>
                                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Location: {{ Auth::user()->studentProfile->preferred_location ?? Auth::user()->county ?? 'Not set' }}</li>
                                    <li><i class="fas fa-check text-success mr-2"></i> Skills: {{ count(Auth::user()->studentProfile->skills ?? []) }} skills listed</li>
                                </ul>
                            </div>
                        </div>

                        <form action="{{ route('student.placement.request') }}" method="POST" class="mt-4">
                            @csrf
                            <button class="btn btn-primary shadow-sm">
                                <i class="fas fa-bolt mr-1"></i> I need placement urgently
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </section>

    <!-- Accept Match Modal -->
    <div class="modal fade" id="acceptMatchModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('student.placement.accept', $pendingMatch->id ?? 0) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle mr-2"></i>
                            Confirm Acceptance
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>You are about to accept the match with <strong>{{ $pendingMatch->opportunity->organization->name ?? '' }}</strong> for the position of <strong>{{ $pendingMatch->opportunity->title ?? '' }}</strong>.</p>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            By accepting this match, an administrator will be notified and will contact you with next steps including documentation and start date confirmation.
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirmTerms" name="confirm_terms" required>
                                <label class="custom-control-label" for="confirmTerms">
                                    I confirm that I want to accept this placement opportunity
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check mr-1"></i> Yes, Accept Match
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Decline Match Modal -->
    <div class="modal fade" id="declineMatchModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('student.placement.decline', $pendingMatch->id ?? 0) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-times-circle mr-2"></i>
                            Decline Match
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Please let us know why you're declining this match:</p>
                        
                        <div class="form-group">
                            <label>Reason for declining</label>
                            <select name="reason" class="form-control" required>
                                <option value="">Select a reason...</option>
                                <option value="better_offer">Found a better opportunity</option>
                                <option value="location_conflict">Location not suitable</option>
                                <option value="dates_not_suitable">Dates don't work for me</option>
                                <option value="personal_reasons">Personal reasons</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Additional feedback (optional)</label>
                            <textarea name="feedback" class="form-control" rows="3" placeholder="Tell us more..."></textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Declining this match means the system will continue searching for other opportunities. You may not get another match with this specific organization.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times mr-1"></i> Confirm Decline
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
    <style>
        .circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin: 0 auto 5px;
        }
        .icon-circle {
            transition: transform 0.3s;
        }
        .icon-circle:hover {
            transform: scale(1.05);
        }
    </style>
    @endpush
</x-layouts.student>