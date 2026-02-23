<x-layouts.app>

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Dashboard
                    </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-home mr-1"></i>Home
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            @php

                $upcomingDeadlines = Cache::remember(
                    'dashboard.upcoming_deadlines',
                    300,
                    fn() => App\Models\AttachmentOpportunity::where('status', 'published')
                        ->where('deadline', '>', now())
                        ->where('deadline', '<', now()->addDays(7))
                        ->with('organization')
                        ->orderBy('deadline')
                        ->take(5)
                        ->get(),
                );

                // Cache expensive queries for 5 minutes
                $totalUsers = Cache::remember('dashboard.total_users', 300, fn() => App\Models\User::count());

                $totalStudents = Cache::remember(
                    'dashboard.total_students',
                    300,
                    fn() => App\Models\StudentProfile::count(),
                );

                $totalEmployers = Cache::remember(
                    'dashboard.total_employers',
                    300,
                    fn() => App\Models\Organization::count(),
                );

                $totalMentors = Cache::remember('dashboard.total_mentors', 300, fn() => App\Models\Mentor::count());

                $activeOpportunities = Cache::remember(
                    'dashboard.active_opportunities',
                    300,
                    fn() => App\Models\AttachmentOpportunity::where('status', 'published')
                        ->where('deadline', '>', now())
                        ->count(),
                );

                $pendingApplications = Cache::remember(
                    'dashboard.pending_applications',
                    300,
                    fn() => App\Models\Application::whereIn('status', ['submitted', 'under_review'])->count(),
                );

                $successfulPlacements = Cache::remember(
                    'dashboard.successful_placements',
                    300,
                    fn() => App\Models\Application::where('status', 'hired')->count(),
                );

                $activeMentorships = Cache::remember(
                    'dashboard.active_mentorships',
                    300,
                    fn() => App\Models\Mentorship::where('status', 'active')->count(),
                );

                // Recent activities
                $recentApplications = Cache::remember(
                    'dashboard.recent_applications',
                    300,
                    fn() => App\Models\Application::with(['student.studentProfile', 'opportunity.organization'])
                        ->latest()
                        ->take(5)
                        ->get(),
                );

                $recentMentorships = Cache::remember(
                    'dashboard.recent_mentorships',
                    300,
                    fn() => App\Models\Mentorship::with(['mentor', 'mentee']) // Change from ['mentor.user', 'student.user']
                        ->latest()
                        ->take(5)
                        ->get(),
                );

                $activeToday = Cache::remember(
                    'dashboard.active_today',
                    300,
                    fn() => App\Models\ActivityLog::whereDate('created_at', today())
                        ->distinct('causer_id')
                        ->count('causer_id'),
                );

                // System health
                $dbSize = Cache::remember('dashboard.db_size', 3600, function () {
                    $tables = DB::select('SHOW TABLE STATUS');
                    return collect($tables)->sum('Data_length');
                });

                $dbSizeMB = round($dbSize / 1024 / 1024, 2);

                $serverLoad = function_exists('sys_getloadavg') ? sys_getloadavg()[0] : 0;

                // Chart data for last 6 months
                $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('M Y'));

                $studentGrowth = $months->map(
                    fn($month, $index) => App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'student'))
                        ->where(
                            'created_at',
                            '<=',
                            now()
                                ->subMonths(5 - $index)
                                ->endOfMonth(),
                        )
                        ->count(),
                );

                 
                $employerGrowth = $months->map(
                    fn($month, $index) => App\Models\Organization::where(
                        'created_at',
                        '<=',
                        now()
                            ->subMonths(5 - $index)
                            ->endOfMonth(),
                    )->count(),
                );

                $applicationTrend = $months->map(
                    fn($month, $index) => App\Models\Application::whereMonth(
                        'created_at',
                        now()->subMonths(5 - $index)->month,
                    )
                        ->whereYear('created_at', now()->subMonths(5 - $index)->year)
                        ->count(),
                );

                $placementTrend = $months->map(
                    fn($month, $index) => App\Models\Application::where('status', 'hired')
                        ->whereMonth('updated_at', now()->subMonths(5 - $index)->month)
                        ->whereYear('updated_at', now()->subMonths(5 - $index)->year)
                        ->count(),
                );

            @endphp

            <!-- Welcome Row -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-calendar-check mr-2"></i>
                        <strong>Welcome back, {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}!</strong>
                        @if ($upcomingDeadlines->count() > 0)
                            There are <strong>{{ $upcomingDeadlines->count() }}</strong> opportunities with deadlines
                            this week.
                        @else
                            No upcoming deadlines this week.
                        @endif
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

        <!-- Small boxes (Stat boxes) - First Row -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($totalUsers) }}</h3>
                        <p>Total Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                        View All Users <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($totalStudents) }}</h3>
                        <p>Total Students</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <a href="{{ route('admin.students.index') }}" class="small-box-footer">
                        Manage Students <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($totalEmployers) }}</h3>
                        <p>Organizations</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <a href="{{ route('admin.organizations.index') }}" class="small-box-footer">
                        View Organizations <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ number_format($totalMentors) }}</h3>
                        <p>Mentors</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <a href="{{ route('admin.mentorships.index') }}" class="small-box-footer">
                        View Mentors <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Small boxes (Stat boxes) - Second Row -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-teal">
                    <div class="inner">
                        <h3>{{ number_format($activeOpportunities) }}</h3>
                        <p>Active Opportunities</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <a href="{{ route('admin.opportunities.index') }}" class="small-box-footer">
                        View Opportunities <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h3>{{ number_format($pendingApplications) }}</h3>
                        <p>Pending Applications</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <a href="{{ route('admin.applications.pending') }}" class="small-box-footer">
                        Review Applications <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($successfulPlacements) }}</h3>
                        <p>Successful Placements</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <a href="{{ route('admin.placements.index') }}" class="small-box-footer">
                        View Placements <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ number_format($activeMentorships) }}</h3>
                        <p>Active Mentorships</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <a href="{{ route('admin.mentorships.active') }}" class="small-box-footer">
                        View Mentorships <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main row -->
        <div class="row">

            <!-- Left col (Charts and Tables) -->
            <div class="col-lg-8">

                <!-- Custom tabs (Charts with tabs)-->
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Platform Analytics
                        </h3>
                        <div class="card-tools">
                            <ul class="nav nav-pills ml-auto" id="chartTabs">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#growth-chart" data-toggle="tab">
                                        <i class="fas fa-users mr-1"></i> Growth
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#applications-chart" data-toggle="tab">
                                        <i class="fas fa-file-alt mr-1"></i> Applications
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Growth Chart -->
                            <div class="tab-pane active" id="growth-chart">
                                <div class="chart" style="height: 300px;">
                                    <canvas id="growthChartCanvas"></canvas>
                                </div>
                            </div>

                            <!-- Applications Chart -->
                            <div class="tab-pane" id="applications-chart">
                                <div class="chart" style="height: 300px;">
                                    <canvas id="applicationsChartCanvas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Applications Table -->
                <div class="card card-success card-outline">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt mr-1"></i>
                            Recent Applications
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.applications.index') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-eye mr-1"></i> View All
                            </a>
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Opportunity</th>
                                    <th>Organization</th>
                                    <th>Applied</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentApplications as $application)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-initials bg-{{ ['primary', 'success', 'info', 'warning', 'danger'][rand(0, 4)] }} mr-2"
                                                    style="width: 30px; height: 30px; line-height: 30px; text-align: center; color: white; font-weight: bold; font-size: 12px;">
                                                    {{ getInitials($application->student?->full_name ?? 'Unknown') }}
                                                </div>
                                                <div>
                                                    {{ $application->student?->full_name ?? 'N/A' }}
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $application->student?->studentProfile?->course_name ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ Str::limit($application->opportunity?->title ?? 'N/A', 30) }}</strong>
                                        </td>
                                        <td>{{ $application->opportunity?->organization?->name ?? 'N/A' }}</td>
                                        <td>
                                            <span data-toggle="tooltip"
                                                title="{{ $application->created_at->format('M d, Y H:i') }}">
                                                {{ $application->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'submitted' => 'info',
                                                    'under_review' => 'primary',
                                                    'shortlisted' => 'warning',
                                                    'interview_scheduled' => 'purple',
                                                    'hired' => 'success',
                                                    'rejected' => 'danger',
                                                    'withdrawn' => 'secondary',
                                                ];
                                                $color = $statusColors[$application->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $color }}">
                                                {{ str_replace('_', ' ', ucfirst($application->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.applications.show', $application) }}"
                                                class="btn btn-sm btn-outline-info" data-toggle="tooltip"
                                                title="View Application">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No applications yet</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Mentorships Table -->
                <div class="card card-success card-outline">
                    <div class="card-header border-0">
                        <h3 class="card-title">
                            <i class="fas fa-handshake mr-1"></i>
                            Recent Mentorships
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.mentorships.index') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-eye mr-1"></i> View All
                            </a>
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Mentor</th>
                                    <th>Student</th>
                                    <th>Started</th>
                                    <th>Status</th>
                                    <th>Sessions</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentMentorships as $mentorship)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-initials bg-primary mr-2" ...>
                                                    {{ getInitials($mentorship->mentor?->full_name ?? 'Unknown') }}
                                                </div>
                                                {{ $mentorship->mentor?->full_name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-initials bg-info mr-2" ...>
                                                    {{ getInitials($mentorship->mentee?->full_name ?? 'Unknown') }}
                                                </div>
                                                {{ $mentorship->mentee?->full_name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>{{ $mentorship->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'active' => 'success',
                                                    'pending' => 'warning',
                                                    'completed' => 'info',
                                                    'cancelled' => 'danger',
                                                ];
                                                $color = $statusColors[$mentorship->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $color }}">
                                                {{ ucfirst($mentorship->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $mentorship->sessions_count ?? 0 }} sessions
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.mentorships.show', $mentorship) }}"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No mentorships yet</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

            </div><!--.col-lg-8-->

            <!-- Right col (Widgets) -->
            <div class="col-lg-4">
                <!-- Upcoming Deadlines -->
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Upcoming Deadlines
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-warning">{{ $upcomingDeadlines->count() }}</span>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <ul class="products-list product-list-in-card pl-2 pr-2">
                            @forelse($upcomingDeadlines as $opportunity)
                                <li class="item">
                                    <div class="product-img">
                                        <span
                                            class="badge badge-{{ $opportunity->days_until_deadline <= 2 ? 'danger' : 'warning' }}"
                                            style="font-size: 14px; padding: 8px;">
                                            {{ $opportunity->days_until_deadline }}d
                                        </span>
                                    </div>
                                    <div class="product-info">
                                        <a href="{{ route('admin.opportunities.show', $opportunity) }}"
                                            class="product-title">
                                            {{ Str::limit($opportunity->title, 25) }}
                                        </a>
                                        <span class="product-description">
                                            <i class="fas fa-building mr-1"></i>
                                            {{ $opportunity->organization?->name ?? 'N/A' }}
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $opportunity->application_deadline->format('M d, Y') }}
                                            </small>
                                        </span>
                                    </div>
                                </li>
                            @empty
                                <li class="item">
                                    <div class="product-info text-center py-3">
                                        <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">No upcoming deadlines</p>
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    @if ($upcomingDeadlines->count() > 0)
                        <div class="card-footer text-center">
                            <a href="{{ route('admin.opportunities.index') }}" class="text-success">
                                View All Opportunities <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Quick Stats Cards -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box bg-gradient-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Active Today</span>
                                <span class="info-box-number">{{ number_format($activeToday) }}</span>
                                <span class="progress-description">
                                    {{ round(($activeToday / max($totalUsers, 1)) * 100, 1) }}% of total users
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box bg-gradient-success">
                            <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Placement Rate</span>
                                @php
                                    $totalApplications = App\Models\Application::count();
                                    $placementRate =
                                        $totalApplications > 0
                                            ? round(($successfulPlacements / $totalApplications) * 100, 1)
                                            : 0;
                                @endphp
                                <span class="info-box-number">{{ $placementRate }}%</span>
                                <span class="progress-description">
                                    {{ number_format($successfulPlacements) }} placements
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Health -->
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-server mr-1"></i>
                            System Health
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="progress-group mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Database Storage</span>
                                <span>{{ $dbSizeMB }} MB / {{ config('database.limit', 1024) }} MB</span>
                            </div>
                            <div class="progress progress-sm">
                                @php
                                    $storagePercent = min(
                                        100,
                                        round(($dbSizeMB / max(config('database.limit', 1024), 1)) * 100, 1),
                                    );
                                @endphp
                                <div class="progress-bar bg-{{ $storagePercent > 80 ? 'danger' : ($storagePercent > 60 ? 'warning' : 'success') }}"
                                    style="width: {{ $storagePercent }}%"></div>
                            </div>
                        </div>

                        <div class="progress-group mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Server Load</span>
                                <span>{{ number_format($serverLoad, 2) }} / 8</span>
                            </div>
                            <div class="progress progress-sm">
                                @php
                                    $loadPercent = min(100, round(($serverLoad / 8) * 100, 1));
                                @endphp
                                <div class="progress-bar bg-{{ $loadPercent > 80 ? 'danger' : ($loadPercent > 60 ? 'warning' : 'success') }}"
                                    style="width: {{ $loadPercent }}%"></div>
                            </div>
                        </div>

                        <div class="progress-group mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Cache Hit Rate</span>
                                <span>94%</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" style="width: 94%"></div>
                            </div>
                        </div>

                        <hr>

                        <div class="row text-center">
                            <div class="col-4">
                                <h5 class="text-success">
                                    {{ number_format(App\Models\ActivityLog::whereDate('created_at', today())->count()) }}
                                </h5>
                                <small class="text-muted">Events Today</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-info">
                                    {{ number_format(App\Models\Notification::whereNull('read_at')->count()) }}
                                </h5>
                                <small class="text-muted">Unread Notifications</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-warning">
                                    {{ number_format(App\Models\LoginAttempt::where('success', false)->whereDate('created_at', today())->count()) }}
                                </h5>
                                <small class="text-muted">Failed Logins</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <a href="{{ route('admin.system-health') }}" class="text-success">
                            View Full System Status <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity Feed -->
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history mr-1"></i>
                            Recent Activity
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="timeline" style="margin: 15px;">
                            @php
                                $activities = Cache::remember(
                                    'dashboard.recent_activities',
                                    300,
                                    fn() => App\Models\ActivityLog::with('causer')->latest()->take(10)->get(),
                                );
                            @endphp
                            @foreach ($activities->groupBy(fn($item) => $item->created_at->format('Y-m-d')) as $date => $dayActivities)
                                <div class="time-label">
                                    <span class="bg-success">
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

                                @foreach ($dayActivities as $activity)
                                    @php
                                        // Determine icon and color based on event or description
                                        $icon = 'fa-circle';
                                        $color = 'info';
                                        $description = $activity->description;
                                        $properties = $activity->properties ?? [];

                                        // Parse description and properties for better display
                                        if (str_contains($activity->description, 'login')) {
                                            $icon = 'fa-sign-in-alt';
                                            $color = 'success';
                                        } elseif (str_contains($activity->description, 'logout')) {
                                            $icon = 'fa-sign-out-alt';
                                            $color = 'warning';
                                        } elseif (str_contains($activity->description, 'created')) {
                                            $icon = 'fa-plus-circle';
                                            $color = 'success';
                                        } elseif (str_contains($activity->description, 'updated')) {
                                            $icon = 'fa-edit';
                                            $color = 'info';
                                        } elseif (str_contains($activity->description, 'deleted')) {
                                            $icon = 'fa-trash';
                                            $color = 'danger';
                                        } elseif (str_contains($activity->description, 'upload')) {
                                            $icon = 'fa-upload';
                                            $color = 'primary';
                                        } elseif (str_contains($activity->description, 'download')) {
                                            $icon = 'fa-download';
                                            $color = 'secondary';
                                        } elseif (str_contains($activity->description, 'placement')) {
                                            $icon = 'fa-briefcase';
                                            $color = 'purple';
                                        } elseif (str_contains($activity->description, 'mentor')) {
                                            $icon = 'fa-handshake';
                                            $color = 'teal';
                                        }
                                    @endphp

                                    <div>
                                        <i class="fas {{ $icon }} bg-{{ $color }}"></i>
                                        <div class="timeline-item">
                                            <span class="time">
                                                <i class="fas fa-clock"></i>
                                                {{ $activity->created_at->format('h:i A') }}
                                            </span>
                                            <h3 class="timeline-header">
                                                @if ($activity->causer)
                                                    <a href="{{ route('admin.users.show', $activity->causer_id) }}"
                                                        class="text-{{ $color }}">
                                                        <strong>{{ $activity->causer->name }}</strong>
                                                    </a>
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif

                                                {{-- Format the description --}}
                                                @if (str_contains($activity->description, 'login'))
                                                    logged in to the system
                                                @elseif(str_contains($activity->description, 'logout'))
                                                    logged out of the system
                                                @elseif(str_contains($activity->description, 'created user'))
                                                    created a new user account
                                                @elseif(str_contains($activity->description, 'updated user'))
                                                    updated user information
                                                @elseif(str_contains($activity->description, 'deleted user'))
                                                    deleted a user account
                                                @elseif(str_contains($activity->description, 'created opportunity'))
                                                    posted a new opportunity
                                                @elseif(str_contains($activity->description, 'updated opportunity'))
                                                    updated an opportunity
                                                @elseif(str_contains($activity->description, 'deleted opportunity'))
                                                    deleted an opportunity
                                                @elseif(str_contains($activity->description, 'uploaded document'))
                                                    uploaded a document
                                                @elseif(str_contains($activity->description, 'downloaded document'))
                                                    downloaded a document
                                                @elseif(str_contains($activity->description, 'placement'))
                                                    updated a placement record
                                                @elseif(str_contains($activity->description, 'mentorship'))
                                                    updated a mentorship relationship
                                                @elseif(str_contains($activity->description, 'settings'))
                                                    updated system settings
                                                @else
                                                    {{ $activity->description }}
                                                @endif
                                            </h3>

                                            {{-- Format properties in a human-readable way --}}
                                            @if (!empty($properties))
                                                <div class="timeline-body">
                                                    @if (isset($properties['email']))
                                                        <span class="badge badge-info mr-1">
                                                            <i class="fas fa-envelope mr-1"></i>
                                                            {{ $properties['email'] }}
                                                        </span>
                                                    @endif

                                                    @if (isset($properties['role']))
                                                        <span class="badge badge-primary mr-1">
                                                            <i class="fas fa-user-tag mr-1"></i>
                                                            {{ $properties['role'] }}
                                                        </span>
                                                    @endif

                                                    @if (isset($properties['status']))
                                                        <span
                                                            class="badge badge-{{ $properties['status'] === 'active' ? 'success' : 'secondary' }} mr-1">
                                                            <i class="fas fa-circle mr-1"></i>
                                                            {{ ucfirst($properties['status']) }}
                                                        </span>
                                                    @endif

                                                    @if (isset($properties['changes']))
                                                        <div class="mt-2">
                                                            <small class="text-muted">
                                                                <i class="fas fa-pencil-alt mr-1"></i>
                                                                Changes:
                                                                @foreach ($properties['changes'] as $field => $change)
                                                                    @if (is_array($change))
                                                                        {{ $field }}
                                                                        ({{ $change['old'] ?? 'none' }} →
                                                                        {{ $change['new'] ?? 'none' }})
                                                                    @else
                                                                        {{ $field }} updated
                                                                    @endif
                                                                    @if (!$loop->last)
                                                                        ,
                                                                    @endif
                                                                @endforeach
                                                            </small>
                                                        </div>
                                                    @endif

                                                    @if (isset($properties['ip_address']))
                                                        <div class="mt-1">
                                                            <small class="text-muted">
                                                                <i class="fas fa-network-wired mr-1"></i>
                                                                IP: {{ $properties['ip_address'] }}
                                                            </small>
                                                        </div>
                                                    @endif

                                                    @if (isset($properties['user_agent']))
                                                        <div class="mt-1">
                                                            <small class="text-muted">
                                                                <i class="fas fa-desktop mr-1"></i>
                                                                {{ \Illuminate\Support\Str::limit($properties['user_agent'], 50) }}
                                                            </small>
                                                        </div>
                                                    @endif

                                                    {{-- For placement activities --}}
                                                    @if (isset($properties['student_name']))
                                                        <span class="badge badge-info mr-1">
                                                            <i class="fas fa-user-graduate mr-1"></i>
                                                            {{ $properties['student_name'] }}
                                                        </span>
                                                    @endif

                                                    @if (isset($properties['organization_name']))
                                                        <span class="badge badge-warning mr-1">
                                                            <i class="fas fa-building mr-1"></i>
                                                            {{ $properties['organization_name'] }}
                                                        </span>
                                                    @endif

                                                    @if (isset($properties['match_score']))
                                                        <span class="badge badge-success mr-1">
                                                            <i class="fas fa-percent mr-1"></i> Match:
                                                            {{ $properties['match_score'] }}%
                                                        </span>
                                                    @endif

                                                    {{-- For document activities --}}
                                                    @if (isset($properties['document_name']))
                                                        <span class="badge badge-secondary mr-1">
                                                            <i class="fas fa-file mr-1"></i>
                                                            {{ $properties['document_name'] }}
                                                        </span>
                                                    @endif

                                                    {{-- For any additional data, show as key-value pairs --}}
                                                    @if (count($properties) > 0 &&
                                                            !isset($properties['changes']) &&
                                                            !isset($properties['email']) &&
                                                            !isset($properties['role']))
                                                        <div class="mt-2">
                                                            <small class="text-muted">
                                                                @foreach ($properties as $key => $value)
                                                                    @if (!is_array($value) && !in_array($key, ['ip_address', 'user_agent', 'url', 'method']))
                                                                        <span class="mr-2">
                                                                            <strong>{{ str_replace('_', ' ', ucfirst($key)) }}:</strong>
                                                                            {{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}
                                                                        </span>
                                                                    @endif
                                                                @endforeach
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                            <div>
                                                <i class="fas fa-clock bg-gray"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <a href="{{ route('admin.activity-logs') }}" class="text-success">
                            View All Activity <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

@push('scripts')
    <script>
        $(function() {
            'use strict'

            // Growth Chart
            const growthCtx = document.getElementById('growthChartCanvas').getContext('2d')
            new Chart(growthCtx, {
                type: 'line',
                data: {
                    labels: @json($months),
                    datasets: [{
                            label: 'Students',
                            backgroundColor: 'rgba(60,141,188,0.1)',
                            borderColor: 'rgba(60,141,188,1)',
                            pointBackgroundColor: '#3b8bba',
                            pointBorderColor: 'rgba(60,141,188,1)',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(60,141,188,1)',
                            data: @json($studentGrowth),
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Employers',
                            backgroundColor: 'rgba(0,166,90,0.1)',
                            borderColor: 'rgba(0,166,90,1)',
                            pointBackgroundColor: '#00a65a',
                            pointBorderColor: 'rgba(0,166,90,1)',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(0,166,90,1)',
                            data: @json($employerGrowth),
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            })

            // Applications Chart
            const applicationsCtx = document.getElementById('applicationsChartCanvas').getContext('2d')
            new Chart(applicationsCtx, {
                type: 'bar',
                data: {
                    labels: @json($months),
                    datasets: [{
                            label: 'Applications',
                            backgroundColor: 'rgba(60,141,188,0.8)',
                            borderColor: 'rgba(60,141,188,1)',
                            data: @json($applicationTrend),
                            borderRadius: 4
                        },
                        {
                            label: 'Placements',
                            backgroundColor: 'rgba(0,166,90,0.8)',
                            borderColor: 'rgba(0,166,90,1)',
                            data: @json($placementTrend),
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            })

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip()

            // Auto-refresh data every 5 minutes
            setInterval(function() {
                Livewire.dispatch('refreshDashboard')
            }, 300000)
        })
    </script>
@endpush
