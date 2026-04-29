<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}

    <!-- Organization Header -->
    @if ($organization)
        <div class="content-header bg-light border-bottom">
            <div class="container-fluid">
                <div class="row align-items-center py-3">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            @if ($organization->logo_url)
                                <img src="{{ asset('storage/' . $organization->logo_url) }}"
                                    alt="{{ $organization->name }}" class="img-circle elevation-2 mr-3"
                                    style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-success img-circle elevation-2 mr-3 d-flex align-items-center justify-content-center"
                                    style="width: 60px; height: 60px;">
                                    <i class="fas fa-building fa-2x text-white"></i>
                                </div>
                            @endif

                            <div>
                                <h1 class="m-0 text-dark font-weight-bold">
                                    {{ $organization->name }}
                                    @if ($organization->is_verified)
                                        <i class="fas fa-check-circle text-success ml-1" title="Verified Organization"
                                            data-toggle="tooltip"></i>
                                    @endif
                                </h1>
                                <p class="m-0 text-muted">
                                    <i class="fas fa-industry mr-1"></i>
                                    {{ $organization->industry ?? 'Industry not specified' }}
                                    @if ($organization->location)
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        {{ $organization->location }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="btn-group">
                            <a href="{{ route('employer.opportunities.create') }}" class="btn btn-success">
                                <i class="fas fa-plus mr-1"></i> Post Opportunity
                            </a>
                            <a href="{{ route('employer.organization.edit') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-edit mr-1"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <section class="content">
        <div class="container-fluid">
            <!-- Stats Row -->
            <div class="row">
                <!-- Active Opportunities -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="small-box bg-info stat-card">
                        <div class="inner">
                            <h3>{{ $stats['active_opportunities'] ?? 0 }}</h3>
                            <p>Active Opportunities</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <a href="{{ route('employer.opportunities.index') }}" class="small-box-footer">
                            View Opportunities <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Total Applications -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="small-box bg-warning stat-card">
                        <div class="inner">
                            <h3>{{ $stats['total_applications'] ?? 0 }}</h3>
                            <p>Total Applications</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <a href="{{ route('employer.applications.index') }}" class="small-box-footer">
                            View Applications <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Active Placements -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="small-box bg-success stat-card">
                        <div class="inner">
                            <h3>{{ $stats['active_placements'] ?? 0 }}</h3>
                            <p>Active Placements</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <a href="{{ route('employer.placements.index') }}" class="small-box-footer">
                            View Placements <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Total Placed -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="small-box bg-primary stat-card">
                        <div class="inner">
                            <h3>{{ $stats['total_placed'] ?? 0 }}</h3>
                            <p>Total Students Placed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <a href="{{ route('employer.placements.index') }}" class="small-box-footer">
                            View History <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Applications -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title">
                                <i class="fas fa-clock mr-2 text-warning"></i>
                                Recent Applications
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('employer.applications.index') }}" class="btn btn-tool">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Opportunity</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentApplications as $application)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @php
                                                            $student = $application->user;
                                                            $initials = $student
                                                                ? getInitials($student->full_name)
                                                                : '?';
                                                        @endphp
                                                        <div class="avatar-initials bg-info img-circle mr-2"
                                                            style="width: 30px; height: 30px; line-height: 30px; text-align: center; color: white; font-size: 12px;">
                                                            {{ $initials }}
                                                        </div>
                                                        <span>{{ $student->full_name ?? 'Unknown' }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ Str::limit($application->opportunity->title ?? 'N/A', 25) }}
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'warning',
                                                            'reviewed' => 'info',
                                                            'shortlisted' => 'primary',
                                                            'interviewed' => 'info',
                                                            'offered' => 'success',
                                                            'accepted' => 'success',
                                                            'rejected' => 'danger',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="badge badge-{{ $statusColors[$application->status] ?? 'secondary' }}">
                                                        {{ ucfirst($application->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $application->created_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                    No applications received yet
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Opportunities -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title">
                                <i class="fas fa-briefcase mr-2 text-info"></i>
                                Active Opportunities
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('employer.opportunities.create') }}" class="btn btn-tool"
                                    title="Post New">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @forelse($activeOpportunities as $opportunity)
                                <div class="px-4 py-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="#" class="text-dark">
                                                    {{ $opportunity->title }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $opportunity->location ?? 'Location N/A' }}
                                                <span class="mx-2">|</span>
                                                <i class="fas fa-users mr-1"></i>
                                                {{ $opportunity->applications_count }} applications
                                                <span class="mx-2">|</span>
                                                <i class="fas fa-calendar mr-1"></i>
                                                Closes:
                                                {{ $opportunity->deadline ? $opportunity->deadline->format('M d, Y') : 'N/A' }}
                                            </small>
                                        </div>
                                        <span class="badge badge-success">Active</span>
                                    </div>

                                    <!-- Progress Bar for slots -->
                                    @if ($opportunity->slots)
                                        @php
                                            $fillPercentage =
                                                ($opportunity->placements_count / $opportunity->slots) * 100;
                                        @endphp
                                        <div class="progress progress-xs mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-success"
                                                style="width: {{ $fillPercentage }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $opportunity->placements_count }}/{{ $opportunity->slots }} slots
                                            filled
                                        </small>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="fas fa-briefcase fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">No active opportunities</p>
                                    <a href="{{ route('employer.opportunities.create') }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus mr-1"></i> Post Your First Opportunity
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Placements & Quick Actions -->
            <div class="row">
                <!-- Upcoming Placements -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-alt mr-2 text-success"></i>
                                Upcoming Placements
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            @forelse($upcomingPlacements as $placement)
                                <div class="d-flex align-items-center px-4 py-3 border-bottom">
                                    @php
                                        $student = $placement->student;
                                        $initials = $student ? getInitials($student->full_name) : '?';
                                    @endphp
                                    <div class="avatar-initials bg-success img-circle mr-3"
                                        style="width: 40px; height: 40px; line-height: 40px; text-align: center; color: white;">
                                        {{ $initials }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong>{{ $student->full_name ?? 'Unknown Student' }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $placement->opportunity->title ?? 'Placement' }}
                                            <span class="mx-1">•</span>
                                            Starts {{ $placement->start_date->format('M d, Y') }}
                                        </small>
                                    </div>
                                    <span class="badge badge-info">
                                        {{ $placement->start_date->diffForHumans(now(), ['syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW]) }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-alt fa-2x mb-3"></i>
                                    <p>No upcoming placements</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title">
                                <i class="fas fa-bolt mr-2 text-warning"></i>
                                Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <a href="{{ route('employer.opportunities.create') }}"
                                        class="btn btn-outline-success btn-block py-3">
                                        <i class="fas fa-plus-circle fa-2x mb-2 d-block"></i>
                                        Post New Opportunity
                                    </a>
                                </div>
                                <div class="col-6 mb-3">
                                    <a href="{{ route('employer.applications.index') }}"
                                        class="btn btn-outline-warning btn-block py-3">
                                        <i class="fas fa-file-alt fa-2x mb-2 d-block"></i>
                                        Review Applications
                                    </a>
                                </div>
                                <div class="col-6 mb-3">
                                    <a href="{{ route('employer.matching.suggestions') }}"
                                        class="btn btn-outline-info btn-block py-3">
                                        <i class="fas fa-magic fa-2x mb-2 d-block"></i>
                                        Matching Suggestions
                                    </a>
                                </div>
                                <div class="col-6 mb-3">
                                    <a href="{{ route('employer.students.search') }}"
                                        class="btn btn-outline-primary btn-block py-3">
                                        <i class="fas fa-search fa-2x mb-2 d-block"></i>
                                        Search Students
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Organization Quick Stats -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">Organization Status</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Verification Status:</span>
                                @if ($organization->is_verified)
                                    <span class="badge badge-success">Verified</span>
                                @else
                                    <span class="badge badge-warning">Pending Verification</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Industry:</span>
                                <span>{{ $organization->industry ?? 'Not specified' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Member Since:</span>
                                <span>{{ $organization->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
</div>
