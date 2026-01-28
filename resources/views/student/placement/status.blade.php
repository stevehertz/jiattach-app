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

            @elseif($pendingMatch)
                <!-- STATE 2: MATCH FOUND (Action Required) -->
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
                                        <div class="col-sm-6"><small class="text-muted">Location:</small> <b>{{ $pendingMatch->opportunity->county }}</b></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <!-- In a real scenario, these would allow Accepting/Declining -->
                            <button class="btn btn-outline-danger mr-2 px-4">Decline Match</button>
                            <button class="btn btn-success px-5 shadow">
                                <i class="fas fa-check-circle mr-2"></i> Accept Placement
                            </button>
                        </div>
                    </div>
                </div>

            @else
                <!-- STATE 3: SYSTEM MATCHING (In Progress) -->
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
                                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Course: {{ Auth::user()->studentProfile->course_name }}</li>
                                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Location: {{ Auth::user()->studentProfile->preferred_location ?? Auth::user()->county }}</li>
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
</x-layouts.student>