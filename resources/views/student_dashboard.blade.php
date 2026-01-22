<x-layouts.student>
    @php
        $user = Auth::user();
        $profile = $user->studentProfile;
        $placement = $user->placements()->latest()->first();
        $profileCompleteness = $profile ? $profile->profile_completeness : 0;

        // Define theme color based on status
        $statusTheme = 'primary';
        if($placement) {
            $statusTheme = match($placement->status) {
                'placed' => 'success',
                'processing' => 'warning',
                default => 'primary'
            };
        }
    @endphp

    <div class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="m-0 text-dark font-weight-bold">My Dashboard</h1>
                <div class="text-muted small">{{ now()->format('l, jS F Y') }}</div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <!-- 1. HERO SECTION: Dynamic based on Placement State -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0 bg-gradient-{{ $statusTheme }} text-white mb-4 overflow-hidden">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2 class="font-weight-bold">Hello, {{ $user->first_name }}! 👋</h2>
                                    @if($placement && $placement->status == 'placed')
                                        <p class="lead">You are officially placed at <strong>{{ $placement->organization->name }}</strong>.</p>
                                        <a href="{{ route('student.placement.status') }}" class="btn btn-light text-success font-weight-bold shadow-sm">
                                            View Placement Details <i class="fas fa-arrow-right ml-2"></i>
                                        </a>
                                    @elseif($placement && $placement->status == 'processing')
                                        <p class="lead text-white-50">Hang tight! We are currently matching your profile with the best organizations.</p>
                                        <div class="badge badge-warning p-2 px-3 shadow-sm text-dark">
                                            <i class="fas fa-sync fa-spin mr-2"></i> Status: Processing Placement
                                        </div>
                                    @else
                                        <p class="lead text-white-50">Focus on your exams. We are searching for your perfect attachment opportunity.</p>
                                        @if($profileCompleteness < 80)
                                            <div class="alert bg-white text-dark border-0 shadow-sm mt-3" style="border-radius: 12px;">
                                                <i class="fas fa-exclamation-circle text-warning mr-2"></i>
                                                <strong>Boost your chances!</strong> Complete your profile to 80% to get priority matching.
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <div class="col-md-4 d-none d-md-block text-right">
                                    <i class="fas fa-user-graduate fa-8x opacity-2" style="position: absolute; right: -20px; top: -10px; opacity: 0.1;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- LEFT COLUMN -->
                <div class="col-lg-8">

                    <!-- 2. THE JOURNEY (Visual Stepper) -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title font-weight-bold mb-0">Placement Journey</h5>
                        </div>
                        <div class="card-body pt-0">
                            <div class="position-relative py-4">
                                <!-- The Line -->
                                <div class="progress" style="height: 4px; position: absolute; top: 50%; left: 10%; right: 10%; transform: translateY(-50%); z-index: 1;">
                                    @php
                                        $progressWidth = '0%';
                                        if($profile) $progressWidth = '33%';
                                        if($placement && $placement->status == 'processing') $progressWidth = '66%';
                                        if($placement && $placement->status == 'placed') $progressWidth = '100%';
                                    @endphp
                                    <div class="progress-bar bg-success" style="width: {{ $progressWidth }}"></div>
                                </div>

                                <!-- The Dots -->
                                <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                                    <div class="text-center">
                                        <div class="bg-success text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center shadow" style="width:40px; height:40px;"><i class="fas fa-check"></i></div>
                                        <small class="font-weight-bold d-block">Registered</small>
                                    </div>
                                    <div class="text-center">
                                        <div class="bg-{{ $profileCompleteness >= 80 ? 'success' : 'info' }} text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center shadow" style="width:40px; height:40px;"><i class="fas fa-file-alt"></i></div>
                                        <small class="font-weight-bold d-block">Profile</small>
                                    </div>
                                    <div class="text-center">
                                        <div class="bg-{{ $placement ? ($placement->status == 'placed' ? 'success' : 'warning') : 'secondary' }} text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center shadow" style="width:40px; height:40px;"><i class="fas fa-cog"></i></div>
                                        <small class="font-weight-bold d-block">Processing</small>
                                    </div>
                                    <div class="text-center">
                                        <div class="bg-{{ ($placement && $placement->status == 'placed') ? 'success' : 'secondary' }} text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center shadow" style="width:40px; height:40px;"><i class="fas fa-briefcase"></i></div>
                                        <small class="font-weight-bold d-block">Placed</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. COMBINED UPDATES FEED -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title font-weight-bold mb-0">Recent Updates & Activity</h5>
                        </div>
                        <div class="card-body p-0">
                            @php
                                $notifications = Auth::user()->notifications()->latest()->limit(5)->get();
                            @endphp

                            <div class="list-group list-group-flush">
                                @forelse($notifications as $notification)
                                    <div class="list-group-item px-4 border-0 mb-1" style="background: #f8f9fa; border-radius: 8px; margin: 0 15px 10px 15px;">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3 bg-white p-2 rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-bell text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-0 font-weight-bold">{{ $notification->data['title'] ?? 'System Update' }}</h6>
                                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-0 text-muted small">{{ $notification->data['message'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <p class="text-muted mb-0">No new updates found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="card-footer bg-white text-center border-0">
                            <a href="{{ route('student.notifications') }}" class="text-primary font-weight-bold">See All History</a>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div  class="col-lg-4">

                    <!-- 4. PROFILE COMPLETENESS (Redesigned as Action List) -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-3">Profile Score</h5>
                            <div class="d-flex align-items-center mb-4">
                                <div class="mr-3">
                                    <div style="width: 60px; height: 60px; border: 5px solid #f3f3f3; border-top: 5px solid {{ $profileCompleteness >= 80 ? '#28a745' : '#ffc107' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                        {{ $profileCompleteness }}%
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-{{ $profileCompleteness >= 80 ? 'success' : 'warning' }}">
                                        {{ $profileCompleteness >= 80 ? 'Good to go!' : 'Needs attention' }}
                                    </h6>
                                    <small class="text-muted">A complete profile attracts more organizations.</small>
                                </div>
                            </div>

                            @if($profile && $profile->getMissingFields())
                                <h6 class="small font-weight-bold text-uppercase text-muted mb-2">To-Do List</h6>
                                <div class="list-group list-group-flush mb-3">
                                    @foreach($profile->getMissingFields() as $missing)
                                        <a href="{{ route('student.profile.edit') }}" class="list-group-item list-group-item-action px-0 py-2 border-0 d-flex align-items-center">
                                            <i class="far fa-square mr-2 text-muted"></i>
                                            <span class="small text-dark">{{ $missing['label'] }}</span>
                                            <i class="fas fa-chevron-right ml-auto text-muted small"></i>
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            <a href="{{ route('student.profile.edit') }}" class="btn btn-outline-{{ $statusTheme }} btn-block btn-sm font-weight-bold">
                                Update My Profile
                            </a>
                        </div>
                    </div>

                    <!-- 5. IMPORTANT CONTACTS -->
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title font-weight-bold mb-0">Need Help?</h5>
                        </div>
                        <div class="card-body">
                            @if($placement && $placement->admin)
                                <p class="small text-muted mb-3">Your dedicated placement coordinator:</p>
                                <div class="d-flex align-items-center p-2 rounded" style="background: #f8f9fa;">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                        {{ getInitials($placement->admin->name) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 font-weight-bold small">{{ $placement->admin->name }}</h6>
                                        <a href="mailto:{{ $placement->admin->email }}" class="small">Contact Support</a>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-2">
                                    <i class="fas fa-headset text-muted fa-2x mb-2"></i>
                                    <p class="small text-muted mb-0">Our support team is always here for you.</p>
                                    <a href="mailto:support@jiattach.co.ke" class="small font-weight-bold">support@jiattach.co.ke</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.student>
