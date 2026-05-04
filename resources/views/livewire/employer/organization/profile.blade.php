<div>
    {{-- In work, do what you enjoy. --}}
    @if ($organization)
        <!-- Organization Header -->
        <div class="card card-success card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 text-center">
                        <div class="mb-3">
                            @if ($organization->logo_path)
                                <img src="{{ asset('storage/' . $organization->logo_path) }}"
                                    alt="{{ $organization->name }}" class="img-fluid img-thumbnail"
                                    style="max-height: 120px;">
                            @else
                                <div class="bg-success d-flex align-items-center justify-content-center mx-auto"
                                    style="width: 120px; height: 120px; border-radius: 10px;">
                                    <span style="font-size: 48px; color: white;">
                                        {{ strtoupper(substr($organization->name, 0, 2)) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="mb-2">
                            @if ($organization->is_verified)
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock mr-1"></i> Pending Verification
                                </span>
                            @endif
                        </div>

                        <div>
                            <span class="badge badge-{{ $organization->is_active ? 'success' : 'danger' }}">
                                {{ $organization->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-10">
                        <h2 class="mb-2">{{ $organization->name }}</h2>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="text-muted mb-1">
                                    <i class="fas fa-industry mr-2"></i>
                                    <strong>Industry:</strong> {{ $organization->industry ?? 'Not specified' }}
                                </p>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-building mr-2"></i>
                                    <strong>Type:</strong> {{ $organization->type ?? 'Not specified' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <strong>Location:</strong>
                                    {{ $organization->county ?? 'N/A' }}
                                    @if ($organization->constituency)
                                        , {{ $organization->constituency }}
                                    @endif
                                </p>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-users mr-2"></i>
                                    <strong>Max Students:</strong> {{ $organization->max_students_per_intake }}
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                @if ($organization->email)
                                    <p class="text-muted mb-1">
                                        <i class="fas fa-envelope mr-2"></i>
                                        <a href="mailto:{{ $organization->email }}">{{ $organization->email }}</a>
                                    </p>
                                @endif
                                @if ($organization->phone)
                                    <p class="text-muted mb-1">
                                        <i class="fas fa-phone mr-2"></i>
                                        <a href="tel:{{ $organization->phone }}">{{ $organization->phone }}</a>
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if ($organization->website)
                                    <p class="text-muted mb-1">
                                        <i class="fas fa-globe mr-2"></i>
                                        <a href="{{ $organization->website }}" target="_blank">
                                            {{ $organization->website }}
                                        </a>
                                    </p>
                                @endif
                                @if ($organization->contact_person_name)
                                    <p class="text-muted mb-1">
                                        <i class="fas fa-user mr-2"></i>
                                        <strong>Contact:</strong> {{ $organization->contact_person_name }}
                                        @if ($organization->contact_person_position)
                                            ({{ $organization->contact_person_position }})
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-lg-2 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalOpportunities }}</h3>
                        <p>Total Opportunities</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $activeOpportunities }}</h3>
                        <p>Active</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $pendingApplications }}</h3>
                        <p>Pending Apps</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $activePlacements }}</h3>
                        <p>Active Placements</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $availableSlots }}</h3>
                        <p>Available Slots</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-door-open"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-dark">
                    <div class="inner">
                        <h3>{{ $totalPlacements }}</h3>
                        <p>Total Placements</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-history"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'overview' ? 'active' : '' }}"
                            wire:click="switchTab('overview')" href="#">
                            <i class="fas fa-info-circle mr-1"></i> Overview
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'departments' ? 'active' : '' }}"
                            wire:click="switchTab('departments')" href="#">
                            <i class="fas fa-sitemap mr-1"></i> Departments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'team' ? 'active' : '' }}" wire:click="switchTab('team')"
                            href="#">
                            <i class="fas fa-users mr-1"></i> Team Members
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'opportunities' ? 'active' : '' }}"
                            wire:click="switchTab('opportunities')" href="#">
                            <i class="fas fa-briefcase mr-1"></i> Opportunities
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'placements' ? 'active' : '' }}"
                            wire:click="switchTab('placements')" href="#">
                            <i class="fas fa-user-graduate mr-1"></i> Placements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'applications' ? 'active' : '' }}"
                            wire:click="switchTab('applications')" href="#">
                            <i class="fas fa-file-alt mr-1"></i> Applications
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <!-- Overview Tab -->
                @if ($activeTab === 'overview')
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Description</h5>
                            <p class="text-muted">
                                {{ $organization->description ?? 'No description available.' }}
                            </p>

                            <h5 class="mt-4">Contact Information</h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Contact Person:</th>
                                    <td>{{ $organization->contact_person_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Position:</th>
                                    <td>{{ $organization->contact_person_position ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>
                                        @if ($organization->contact_person_email)
                                            <a href="mailto:{{ $organization->contact_person_email }}">
                                                {{ $organization->contact_person_email }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $organization->contact_person_phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Organization Details</h5>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Established:</th>
                                    <td>{{ $organization->created_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Industry:</th>
                                    <td>{{ $organization->industry ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td>{{ $organization->type ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Location:</th>
                                    <td>
                                        {{ $organization->address ?? 'N/A' }}
                                        @if ($organization->county)
                                            <br>{{ $organization->county }}
                                        @endif
                                        @if ($organization->ward)
                                            , {{ $organization->ward }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Student Capacity:</th>
                                    <td>
                                        {{ $organization->max_students_per_intake }} students per intake
                                        <br>
                                        <small class="text-muted">
                                            {{ $availableSlots }} slots available
                                        </small>
                                    </td>
                                </tr>
                            </table>

                            <h5 class="mt-4">Quick Links</h5>
                            <div class="btn-group-vertical w-100">
                                <a href="{{ route('employer.organization.edit') }}"
                                    class="btn btn-outline-primary text-left">
                                    <i class="fas fa-edit mr-2"></i> Edit Organization Profile
                                </a>
                                <a href="{{ route('employer.opportunities.create') }}"
                                    class="btn btn-outline-success text-left">
                                    <i class="fas fa-plus mr-2"></i> Post New Opportunity
                                </a>
                                <a href="{{ route('employer.applications.index') }}"
                                    class="btn btn-outline-warning text-left">
                                    <i class="fas fa-file-alt mr-2"></i> View Applications
                                    ({{ $pendingApplications }} pending)
                                </a>
                                <a href="{{ route('employer.organization.members') }}"
                                    class="btn btn-outline-info text-left">
                                    <i class="fas fa-users mr-2"></i> Manage Team Members
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Departments Tab -->
                @if ($activeTab === 'departments')
                    @if ($departments->isNotEmpty())
                        <div class="row">
                            @foreach ($departments as $department)
                                <div class="col-md-4 mb-3">
                                    <div class="card border-info">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <i class="fas fa-sitemap text-info mr-2"></i>
                                                {{ $department['name'] ?? 'Unnamed Department' }}
                                            </h5>
                                            @if (isset($department['description']))
                                                <p class="card-text text-muted small">
                                                    {{ $department['description'] }}
                                                </p>
                                            @endif
                                            @if (isset($department['head']))
                                                <small class="text-muted">
                                                    <i class="fas fa-user mr-1"></i>
                                                    Head: {{ $department['head'] }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-sitemap fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No departments configured yet</p>
                        </div>
                    @endif
                @endif

                <!-- Team Members Tab -->
                @if ($activeTab === 'team')
                    @if ($teamMembers->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Position</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($teamMembers as $member)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3">
                                                        <div class="avatar-initials bg-info img-circle d-flex align-items-center justify-content-center"
                                                            style="width: 35px; height: 35px; color: white; font-size: 12px;">
                                                            {{ $member->initials() }}
                                                        </div>
                                                    </div>
                                                    <strong>{{ $member->full_name }}</strong>
                                                </div>
                                            </td>
                                            <td>{{ $member->email }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $member->pivot->role === 'owner' ? 'primary' : ($member->pivot->role === 'admin' ? 'info' : 'secondary') }}">
                                                    {{ ucfirst($member->pivot->role) }}
                                                </span>
                                                @if ($member->pivot->is_primary_contact)
                                                    <span class="badge badge-warning ml-1">Primary</span>
                                                @endif
                                            </td>
                                            <td>{{ $member->pivot->position ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $member->pivot->is_active ? 'success' : 'danger' }}">
                                                    {{ $member->pivot->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $member->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No team members found</p>
                        </div>
                    @endif
                @endif

                <!-- Opportunities Tab -->
                @if ($activeTab === 'opportunities')
                    @if ($recentOpportunities->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Slots</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentOpportunities as $opportunity)
                                        <tr>
                                            <td>
                                                <strong>{{ $opportunity->title ?? 'Untitled' }}</strong>
                                            </td>
                                            <td>{{ $opportunity->type ?? 'N/A' }}</td>
                                            <td>{{ $opportunity->slots ?? 0 }}</td>
                                            <td>
                                                @if ($opportunity->deadline)
                                                    <span
                                                        class="{{ $opportunity->deadline->isPast() ? 'text-danger' : 'text-success' }}">
                                                        {{ $opportunity->deadline->format('M d, Y') }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $opportunity->status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($opportunity->status ?? 'unknown') }}
                                                </span>
                                            </td>
                                            <td>{{ $opportunity->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('employer.opportunities.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list mr-1"></i> View All Opportunities
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No opportunities posted yet</p>
                            <a href="{{ route('employer.opportunities.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus mr-1"></i> Post Your First Opportunity
                            </a>
                        </div>
                    @endif
                @endif

                <!-- Placements Tab -->
                @if ($activeTab === 'placements')
                    @if ($currentPlacements->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Position</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Supervisor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($currentPlacements as $placement)
                                        <tr>
                                            <td>
                                                <strong>{{ $placement->student->full_name ?? 'N/A' }}</strong>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $placement->student->email ?? '' }}</small>
                                            </td>
                                            <td>{{ $placement->position ?? 'N/A' }}</td>
                                            <td>{{ $placement->start_date?->format('M d, Y') }}</td>
                                            <td>{{ $placement->end_date?->format('M d, Y') }}</td>
                                            <td>{{ $placement->supervisor_name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-success">Active</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No active placements</p>
                        </div>
                    @endif
                @endif

                <!-- Applications Tab -->
                @if ($activeTab === 'applications')
                    @if ($recentApplications->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Opportunity</th>
                                        <th>Match Score</th>
                                        <th>Status</th>
                                        <th>Applied Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentApplications as $application)
                                        <tr>
                                            <td>
                                                <strong>{{ $application->student->full_name ?? 'N/A' }}</strong>
                                            </td>
                                            <td>{{ $application->opportunity->title ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ ($application->match_score ?? 0) >= 70 ? 'success' : (($application->match_score ?? 0) >= 50 ? 'warning' : 'danger') }}">
                                                    {{ $application->match_score ?? 0 }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'offered' ? 'info' : 'success') }}">
                                                    {{ ucfirst($application->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $application->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('employer.applications.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list mr-1"></i> View All Applications
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No applications received yet</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endif
</div>
