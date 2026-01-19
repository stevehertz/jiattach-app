<x-layouts.student>

    @if (Auth::user()->studentProfile)
        <p class="text-muted mb-0">
            <i class="fas fa-graduation-cap mr-1"></i>
            {{ Auth::user()->studentProfile->institution_name ?? 'Institution not set' }}
            |
            <i class="fas fa-book mr-1"></i>
            {{ Auth::user()->studentProfile->course_name ?? 'Course not set' }}
        </p>
    @endif

    <!-- And in the avatar section -->
    @php
        $initials = getInitials(Auth::user()->full_name);
        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
        $color = $colors[crc32(Auth::user()->email) % count($colors)] ?? 'primary';
    @endphp

    <div class="avatar-initials bg-{{ $color }} img-circle elevation-3"
        style="width: 100px; height: 100px; line-height: 100px; font-size: 2.5rem; margin: 0 auto;">
        {{ $initials }}
    </div>

    <!-- Profile Progress Card - Fixed -->
    @php
        $profile = Auth::user()->studentProfile;
        $profileCompleteness = $profile ? $profile->profile_completeness : 0;

        // Get missing fields
        $missingFields = $profile ? $profile->getMissingFields() : [];

        // Get progress breakdown
        $progressBreakdown = $profile ? $profile->getProfileProgressBreakdown() : [];

        // Check documents
        $hasCV = $profile && $profile->cv_url;
        $hasTranscript = $profile && $profile->transcript_url;
    @endphp

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Student Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('student.dashboard') }}">
                                Home
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Student Dashboard
                        </li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Welcome Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card student-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-2">Welcome back, {{ Auth::user()->full_name }}! 👋</h4>
                                    <p class="text-muted mb-0">
                                        @if (Auth::user()->studentProfile)
                                            <i class="fas fa-graduation-cap mr-1"></i>
                                            {{ Auth::user()->studentProfile->institution_name }}
                                            |
                                            <i class="fas fa-book mr-1"></i>
                                            {{ Auth::user()->studentProfile->course_name }}
                                        @endif
                                    </p>
                                    <div class="mt-3">
                                        @php
                                            // Check placement status
                                            $placementStatus =
                                                Auth::user()->studentProfile->attachment_status ?? 'seeking';
                                            $placement = Auth::user()->placements()->latest()->first();
                                        @endphp

                                        @if ($placement && $placement->status == 'placed')
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                <strong>Congratulations!</strong> You have been placed at
                                                <strong>{{ $placement->organization->name ?? 'an organization' }}</strong>
                                            </div>
                                        @elseif($placement && $placement->status == 'processing')
                                            <div class="alert alert-warning">
                                                <i class="fas fa-clock mr-2"></i>
                                                <strong>Your placement is being processed</strong> by our team.
                                                You'll be notified once a match is found.
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                <strong>Welcome to Jiattach!</strong> Our team will find a suitable
                                                placement for you.
                                                Focus on your studies while we handle the search.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    @php
                                        $initials = getInitials(Auth::user()->full_name);
                                        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                                        $color = $colors[crc32(Auth::user()->email) % count($colors)];
                                    @endphp
                                    <div class="avatar-initials bg-{{ $color }} img-circle elevation-3"
                                        style="width: 100px; height: 100px; line-height: 100px; font-size: 2.5rem; margin: 0 auto;">
                                        {{ $initials }}
                                    </div>
                                    <p class="mt-2 mb-0">
                                        <small class="text-muted">
                                            <i class="fas fa-user-graduate mr-1"></i>
                                            Student ID:
                                            {{ Auth::user()->studentProfile->student_reg_number ?? 'Not Set' }}
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Placement Focus Section -->
            <div class="row mb-4">
                <!-- Current Placement Status -->
                <div class="col-lg-8">
                    <div class="card student-card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-briefcase mr-2"></i>
                                My Placement Status
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <!-- Status Step 1: Registration -->
                                <div class="col-3">
                                    <div class="mb-3">
                                        <div class="avatar-initials bg-success img-circle mx-auto mb-2"
                                            style="width: 60px; height: 60px; line-height: 60px;">
                                            <i class="fas fa-user-check"></i>
                                        </div>
                                        <h6 class="font-weight-bold">Registered</h6>
                                        <small class="text-muted">Completed</small>
                                        <div class="mt-2">
                                            <span class="badge badge-success p-1">
                                                {{ Auth::user()->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Step 2: Profile Review -->
                                <div class="col-3">
                                    <div class="mb-3">
                                        <div class="avatar-initials bg-{{ Auth::user()->studentProfile ? 'info' : 'secondary' }} img-circle mx-auto mb-2"
                                            style="width: 60px; height: 60px; line-height: 60px;">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <h6 class="font-weight-bold">Profile Review</h6>
                                        <small class="text-muted">
                                            @if (Auth::user()->studentProfile && Auth::user()->studentProfile->profile_completeness >= 80)
                                                Complete
                                            @elseif(Auth::user()->studentProfile)
                                                In Progress
                                            @else
                                                Pending
                                            @endif
                                        </small>
                                        <div class="mt-2">
                                            @if (Auth::user()->studentProfile)
                                                <div class="progress progress-thin mx-auto" style="width: 80%">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                        style="width: {{ Auth::user()->studentProfile->profile_completeness }}%">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Step 3: Admin Assignment -->
                                <div class="col-3">
                                    <div class="mb-3">
                                        <div class="avatar-initials bg-{{ $placement ? 'warning' : 'secondary' }} img-circle mx-auto mb-2"
                                            style="width: 60px; height: 60px; line-height: 60px;">
                                            <i class="fas fa-users-cog"></i>
                                        </div>
                                        <h6 class="font-weight-bold">Admin Processing</h6>
                                        <small class="text-muted">
                                            @if ($placement && $placement->status == 'processing')
                                                In Progress
                                            @elseif($placement && $placement->status == 'placed')
                                                Completed
                                            @else
                                                Waiting
                                            @endif
                                        </small>
                                        <div class="mt-2">
                                            @if ($placement && $placement->admin)
                                                <small class="text-muted">
                                                    <i class="fas fa-user-tie mr-1"></i>
                                                    {{ $placement->admin->name }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Step 4: Placement -->
                                <div class="col-3">
                                    <div class="mb-3">
                                        <div class="avatar-initials bg-{{ $placement && $placement->status == 'placed' ? 'success' : 'secondary' }} img-circle mx-auto mb-2"
                                            style="width: 60px; height: 60px; line-height: 60px;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <h6 class="font-weight-bold">Placement</h6>
                                        <small class="text-muted">
                                            @if ($placement && $placement->status == 'placed')
                                                Confirmed
                                            @else
                                                Awaiting
                                            @endif
                                        </small>
                                        <div class="mt-2">
                                            @if ($placement && $placement->status == 'placed')
                                                <span class="badge badge-success p-1">
                                                    {{ $placement->organization->name ?? 'Organization' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Placement Details -->
                            @if ($placement)
                                <div class="mt-4 p-3 border rounded bg-light">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="font-weight-bold">Placement Details</h6>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-1">
                                                    <i class="fas fa-calendar-alt text-primary mr-2"></i>
                                                    <strong>Status:</strong>
                                                    <span
                                                        class="badge badge-{{ $placement->status == 'placed' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($placement->status) }}
                                                    </span>
                                                </li>
                                                @if ($placement->organization)
                                                    <li class="mb-1">
                                                        <i class="fas fa-building text-primary mr-2"></i>
                                                        <strong>Organization:</strong>
                                                        {{ $placement->organization->name }}
                                                    </li>
                                                @endif
                                                @if ($placement->start_date)
                                                    <li class="mb-1">
                                                        <i class="fas fa-play-circle text-primary mr-2"></i>
                                                        <strong>Start Date:</strong>
                                                        {{ $placement->start_date->format('F d, Y') }}
                                                    </li>
                                                @endif
                                                @if ($placement->end_date)
                                                    <li class="mb-1">
                                                        <i class="fas fa-stop-circle text-primary mr-2"></i>
                                                        <strong>End Date:</strong>
                                                        {{ $placement->end_date->format('F d, Y') }}
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="font-weight-bold">Admin Contact</h6>
                                            @if ($placement->admin)
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="avatar-initials bg-info img-circle mr-3"
                                                        style="width: 40px; height: 40px; line-height: 40px;">
                                                        {{ getInitials($placement->admin->name) }}
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 font-weight-bold">{{ $placement->admin->name }}
                                                        </p>
                                                        <small class="text-muted">Placement Coordinator</small>
                                                    </div>
                                                </div>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="mb-1">
                                                        <i class="fas fa-envelope text-primary mr-2"></i>
                                                        {{ $placement->admin->email }}
                                                    </li>
                                                    @if ($placement->admin->phone)
                                                        <li class="mb-1">
                                                            <i class="fas fa-phone text-primary mr-2"></i>
                                                            {{ $placement->admin->phone }}
                                                        </li>
                                                    @endif
                                                </ul>
                                            @else
                                                <p class="text-muted mb-0">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    Admin will be assigned shortly
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Stats & Actions -->
                <div class="col-lg-4">
                    <!-- Profile Completeness -->
                    <div class="card student-card mb-4">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-chart-line mr-2"></i>
                                Profile Progress
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="position-relative d-inline-block">
                                    <div class="avatar-initials @if ($profileCompleteness >= 80) bg-success @elseif($profileCompleteness >= 50) bg-warning @else bg-danger @endif img-circle"
                                        style="width: 100px; height: 100px; line-height: 100px; font-size: 2rem;">
                                        {{ $profileCompleteness }}%
                                    </div>
                                    @if ($profileCompleteness < 100)
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                {{ 100 - $profileCompleteness }}% remaining
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Progress Breakdown -->
                            @if ($profileCompleteness < 100)
                                <div class="mb-4">
                                    <h6 class="font-weight-bold mb-3">Progress Breakdown</h6>
                                    @php
                                        $breakdown = Auth::user()->studentProfile->getProfileProgressBreakdown();
                                    @endphp

                                    @foreach ($breakdown as $category)
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="font-weight-bold">{{ $category['label'] }}</span>
                                                <span class="text-muted">{{ $category['percentage'] }}%</span>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar
                                                    @if ($category['percentage'] == 100) bg-success
                                                    @elseif($category['percentage'] >= 50) bg-warning
                                                    @else bg-danger @endif"
                                                    role="progressbar" style="width: {{ $category['percentage'] }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                {{ $category['completed'] }}/{{ $category['total'] }} fields complete
                                            </small>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Missing Fields -->
                            @php
                                $missingFields = Auth::user()->studentProfile->getMissingFields();
                            @endphp

                            @if (!empty($missingFields))
                                <div class="mt-3">
                                    <p class="text-muted mb-2">
                                        <small>
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            Complete these to improve placement matching:
                                        </small>
                                    </p>
                                    <div class="row">
                                        @foreach ($missingFields as $missing)
                                            <div class="col-md-6 mb-2">
                                                <div
                                                    class="card border-{{ $missing['priority'] == 'high' ? 'danger' : 'warning' }} border-left-3">
                                                    <div class="card-body py-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="mr-3">
                                                                @if ($missing['priority'] == 'high')
                                                                    <i
                                                                        class="fas fa-exclamation-triangle text-danger"></i>
                                                                @else
                                                                    <i class="fas fa-info-circle text-warning"></i>
                                                                @endif
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-0">{{ $missing['label'] }}</h6>
                                                                <small class="text-muted">
                                                                    @if ($missing['field'] == 'cv_url')
                                                                        Upload your CV/Resume
                                                                    @elseif($missing['field'] == 'skills')
                                                                        Add at least one skill
                                                                    @else
                                                                        Fill in this information
                                                                    @endif
                                                                </small>
                                                            </div>
                                                            <div>
                                                                <a href="{{ route('student.profile.edit') }}#{{ $missing['field'] }}"
                                                                    class="btn btn-sm btn-{{ $missing['priority'] == 'high' ? 'danger' : 'warning' }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Document Status -->
                            @php
                                $documents = [
                                    'cv_url' => 'CV/Resume',
                                    'transcript_url' => 'Academic Transcript',
                                ];
                                $uploadedDocs = [];
                                foreach ($documents as $field => $label) {
                                    if (Auth::user()->studentProfile->isFieldComplete($field)) {
                                        $uploadedDocs[] = $label;
                                    }
                                }
                            @endphp

                            @if (!empty($uploadedDocs))
                                <div class="mt-3">
                                    <h6 class="font-weight-bold mb-2">✅ Uploaded Documents</h6>
                                    <div class="d-flex flex-wrap">
                                        @foreach ($uploadedDocs as $doc)
                                            <span class="badge badge-success mr-2 mb-2 p-2">
                                                <i class="fas fa-file-pdf mr-1"></i> {{ $doc }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Action Button -->
                            <div class="mt-4">
                                @if ($profileCompleteness < 100)
                                    <a href="{{ route('student.profile.edit') }}" class="btn btn-primary btn-block">
                                        <i class="fas fa-user-edit mr-2"></i>
                                        Complete Your Profile ({{ $profileCompleteness }}%)
                                    </a>
                                @else
                                    <a href="{{ route('student.profile.edit') }}" class="btn btn-success btn-block">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Profile Complete! Update if needed
                                    </a>
                                @endif

                                @if ($profileCompleteness < 80)
                                    <p class="text-center text-muted mt-2 mb-0">
                                        <small>
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Profiles with 80%+ completion get priority placement
                                        </small>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Updates -->
            <div class="row">
                <div class="col-lg-4">
                    <div class="card student-card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-history mr-2"></i>
                                Recent Activity
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            @php
                                // Get recent activity logs for this student
                                $recentActivity = \App\Models\ActivityLog::where('causer_id', Auth::id())
                                    ->latest()
                                    ->limit(5)
                                    ->get();
                            @endphp

                            @if ($recentActivity->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach ($recentActivity as $activity)
                                        <div class="list-group-item border-0">
                                            <div class="d-flex">
                                                <div class="mr-3">
                                                    <i
                                                        class="fas fa-{{ $activity->icon }} text-{{ $activity->event == 'created' ? 'success' : ($activity->event == 'updated' ? 'warning' : 'info') }}"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $activity->description }}</h6>
                                                    <small class="text-muted">
                                                        <i class="far fa-clock mr-1"></i>
                                                        {{ $activity->getTimeAgoAttribute() }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No recent activity</p>
                                </div>
                            @endif

                            @if ($recentActivity->count() > 0)
                                <div class="card-footer text-center">
                                    <a href="{{ route('student.activity') }}" class="btn btn-sm btn-outline-primary">
                                        View All Activity
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card student-card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-newspaper mr-2"></i>
                                Placement Updates
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            @php
                                // Get placement-related notifications
                                $placementUpdates = Auth::user()
                                    ->notifications()
                                    ->where('type', 'placement')
                                    ->orWhere('data', 'like', '%placement%')
                                    ->latest()
                                    ->limit(5)
                                    ->get();
                            @endphp

                            @if ($placementUpdates->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach ($placementUpdates as $notification)
                                        <div class="list-group-item border-0">
                                            <div class="d-flex">
                                                <div class="mr-3">
                                                    <div class="avatar-initials bg-primary img-circle"
                                                        style="width: 35px; height: 35px; line-height: 35px;">
                                                        <i class="fas fa-briefcase text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        {{ $notification->data['title'] ?? 'Placement Update' }}</h6>
                                                    <p class="mb-1 text-muted small">
                                                        {{ Str::limit($notification->data['message'] ?? '', 60) }}
                                                    </p>
                                                    <small class="text-muted">
                                                        <i class="far fa-clock mr-1"></i>
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                                @if (!$notification->read_at)
                                                    <div class="ml-3">
                                                        <span class="badge badge-danger">New</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No placement updates yet</p>
                                    <small class="text-muted">
                                        Updates will appear here when admins process your placement
                                    </small>
                                </div>
                            @endif

                            @if ($placementUpdates->count() > 0)
                                <div class="card-footer text-center">
                                    <a href="{{ route('student.notifications') }}"
                                        class="btn btn-sm btn-outline-primary">
                                        View All Updates
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card student-card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-calendar-day mr-2"></i>
                                Important Dates
                            </h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3 pb-2 border-bottom">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="font-weight-bold mb-1">Registration Date</h6>
                                            <small class="text-muted">When you joined Jiattach</small>
                                        </div>
                                        <div class="text-right">
                                            <span class="badge badge-light">
                                                {{ Auth::user()->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </li>

                                @if (Auth::user()->studentProfile && Auth::user()->studentProfile->expected_graduation_year)
                                    <li class="mb-3 pb-2 border-bottom">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="font-weight-bold mb-1">Expected Graduation</h6>
                                                <small class="text-muted">Target completion year</small>
                                            </div>
                                            <div class="text-right">
                                                <span class="badge badge-light">
                                                    {{ Auth::user()->studentProfile->expected_graduation_year }}
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                @endif

                                @if ($placement && $placement->start_date)
                                    <li class="mb-3 pb-2 border-bottom">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="fontweight-bold mb-1">Placement Start</h6>
                                                <small class="text-muted">When placement begins</small>
                                            </div>
                                            <div class="text-right">
                                                <span class="badge badge-light">
                                                    {{ $placement->start_date->format('M d, Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                @endif

                                @if (Auth::user()->studentProfile && Auth::user()->studentProfile->attachment_start_date)
                                    <li class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="font-weight-bold mb-1">Attachment Period</h6>
                                                <small class="text-muted">Scheduled dates</small>
                                            </div>
                                            <div class="text-right">
                                                <small class="text-muted">
                                                    {{ Auth::user()->studentProfile->attachment_start_date->format('M d') }}
                                                    -
                                                    {{ Auth::user()->studentProfile->attachment_end_date->format('M d, Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call to Action Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card student-card bg-gradient-light">
                        <div class="card-body text-center">
                            <h4 class="mb-3">
                                <i class="fas fa-hands-helping text-primary mr-2"></i>
                                How Jiattach Works for You
                            </h4>
                            <div class="row mt-4">
                                <div class="col-md-3 mb-3">
                                    <div class="avatar-initials bg-primary img-circle mx-auto mb-3"
                                        style="width: 70px; height: 70px; line-height: 70px;">
                                        <i class="fas fa-user-plus fa-lg"></i>
                                    </div>
                                    <h6 class="font-weight-bold">You Register</h6>
                                    <p class="text-muted small mb-0">
                                        Complete your profile with academic details and preferences
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="avatar-initials bg-info img-circle mx-auto mb-3"
                                        style="width: 70px; height: 70px; line-height: 70px;">
                                        <i class="fas fa-bell fa-lg"></i>
                                    </div>
                                    <h6 class="font-weight-bold">Admins Get Notified</h6>
                                    <p class="text-muted small mb-0">
                                        Our team receives your registration and starts searching
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="avatar-initials bg-warning img-circle mx-auto mb-3"
                                        style="width: 70px; height: 70px; line-height: 70px;">
                                        <i class="fas fa-search fa-lg"></i>
                                    </div>
                                    <h6 class="font-weight-bold">We Search for You</h6>
                                    <p class="text-muted small mb-0">
                                        We match your profile with our partner organizations
                                    </p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="avatar-initials bg-success img-circle mx-auto mb-3"
                                        style="width: 70px; height: 70px; line-height: 70px;">
                                        <i class="fas fa-briefcase fa-lg"></i>
                                    </div>
                                    <h6 class="font-weight-bold">You Get Placed</h6>
                                    <p class="text-muted small mb-0">
                                        We secure your attachment while you focus on studies
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="#" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-edit mr-2"></i>
                                    Complete Your Profile Now
                                </a>
                                <p class="text-muted mt-2 mb-0">
                                    <small>
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Complete profiles get priority in placement matching
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            $(function() {
                // Initialize tooltips
                $('[data-toggle="tooltip"]').tooltip();

                // Auto-update placement status every 30 seconds
                function updatePlacementStatus() {
                    $.ajax({
                        url: '{{ route('student.placement.status') }}',
                        method: 'GET',
                        success: function(response) {
                            if (response.updated) {
                                // Reload the page to show updated status
                                location.reload();
                            }
                        }
                    });
                }

                // Update every 30 seconds if on placement processing
                @if ($placement && $placement->status == 'processing')
                    setInterval(updatePlacementStatus, 30000);
                @endif

                // Mark notifications as read when viewed
                $('.list-group-item').on('click', function() {
                    const notificationId = $(this).data('notification-id');
                    if (notificationId) {
                        $.ajax({
                            url: '/student/notifications/' + notificationId + '/read',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                    }
                });
            });
        </script>
    @endpush

</x-layouts.student>
