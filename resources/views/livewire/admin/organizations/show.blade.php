<div>
    <!-- Page Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-building mr-2"></i>
                        {{ $organization->name }}
                        <small class="text-muted">Details</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.organizations.index') }}">Organizations</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $organization->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Status Bar -->
            <div class="row mb-3">
                <div class="col-12">
                    <div
                        class="card card-outline 
                        {{ $organization->is_verified && $organization->is_active ? 'card-success' : 'card-warning' }}">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>
                                Organization Status
                            </h3>
                            <div class="card-tools">
                                <button wire:click="toggleActive" class="btn btn-tool">
                                    <span
                                        class="badge {{ $organization->is_active ? 'badge-success' : 'badge-secondary' }} mr-2">
                                        {{ $organization->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </button>
                                <button wire:click="toggleVerification" class="btn btn-tool">
                                    <span
                                        class="badge {{ $organization->is_verified ? 'badge-success' : 'badge-warning' }}">
                                        {{ $organization->is_verified ? 'Verified' : 'Pending Verification' }}
                                    </span>
                                </button>
                                @if ($organization->verified_at)
                                    <small class="text-muted ml-2">
                                        Verified: {{ $organization->verified_at->format('d M Y') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total_opportunities'] }}</h3>
                            <p>Total Opportunities</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <a href="#" wire:click="$set('activeTab', 'opportunities')" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['active_opportunities'] }}</h3>
                            <p>Active Opportunities</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="#" wire:click="$set('activeTab', 'opportunities')" class="small-box-footer">
                            View Active <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $stats['total_placements'] }}</h3>
                            <p>Total Placements</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <a href="#" wire:click="$set('activeTab', 'placements')" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['capacity_used'] }}</h3>
                            <p>Capacity Used</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="#" wire:click="$set('activeTab', 'users')" class="small-box-footer">
                            View Users <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="row">
                <!-- Left Column - Profile Card -->
                <div class="col-md-3">
                    <!-- Profile Card -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center mb-3">
                                <div class="profile-user-img img-fluid img-circle"
                                    style="width: 100px; height: 100px; background: #f4f6f9; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <i class="fas fa-building fa-3x text-muted"></i>
                                </div>
                            </div>

                            <h3 class="profile-username text-center">{{ $organization->name }}</h3>
                            <p class="text-muted text-center">{{ $organization->industry ?? 'Industry not specified' }}
                            </p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b><i class="fas fa-envelope mr-1"></i> Email</b>
                                    <a href="mailto:{{ $organization->email }}">{{ $organization->email }}</a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b><i class="fas fa-phone mr-1"></i> Phone</b>
                                    <a href="tel:{{ $organization->phone }}">{{ $organization->phone ?? 'N/A' }}</a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b><i class="fas fa-globe mr-1"></i> Website</b>
                                    @if ($organization->website)
                                        <a href="{{ $organization->website }}" target="_blank">Visit</a>
                                    @else
                                        <span>N/A</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b><i class="fas fa-map-marker-alt mr-1"></i> County</b>
                                    <span>{{ $organization->county ?? 'N/A' }}</span>
                                </li>
                            </ul>

                            <div class="btn-group w-100">
                                <a href="{{ route('admin.organizations.edit', $organization->id) }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-edit mr-1"></i> Edit Profile
                                </a>
                                <button type="button" class="btn btn-info dropdown-toggle dropdown-icon"
                                    data-toggle="dropdown">
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#" wire:click.prevent="toggleActive">
                                        <i
                                            class="fas {{ $organization->is_active ? 'fa-pause' : 'fa-play' }} mr-2"></i>
                                        {{ $organization->is_active ? 'Deactivate' : 'Activate' }}
                                    </a>
                                    <a class="dropdown-item" href="#" wire:click.prevent="toggleVerification">
                                        <i
                                            class="fas {{ $organization->is_verified ? 'fa-times-circle' : 'fa-check-circle' }} mr-2"></i>
                                        {{ $organization->is_verified ? 'Unverify' : 'Verify' }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#">
                                        <i class="fas fa-trash mr-2"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-address-card mr-2"></i>
                                Contact Information
                            </h3>
                        </div>
                        <div class="card-body">
                            <strong><i class="fas fa-user mr-1"></i> Contact Person</strong>
                            <p class="text-muted">
                                {{ $organization->contact_person_name ?? 'Not specified' }}<br>
                                @if ($organization->contact_person_position)
                                    <small>{{ $organization->contact_person_position }}</small>
                                @endif
                            </p>
                            <hr>

                            <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                            <p class="text-muted">
                                <a href="mailto:{{ $organization->contact_person_email }}">
                                    {{ $organization->contact_person_email ?? 'Not specified' }}
                                </a>
                            </p>
                            <hr>

                            <strong><i class="fas fa-phone mr-1"></i> Phone</strong>
                            <p class="text-muted">
                                <a href="tel:{{ $organization->contact_person_phone }}">
                                    {{ $organization->contact_person_phone ?? 'Not specified' }}
                                </a>
                            </p>
                            <hr>

                            <strong><i class="fas fa-map-pin mr-1"></i> Full Address</strong>
                            <p class="text-muted">
                                {{ $organization->address ?? 'Not specified' }}<br>
                                <small>
                                    {{ $organization->ward ?? '' }}
                                    {{ $organization->constituency ? ', ' . $organization->constituency : '' }}
                                    {{ $organization->county ? ', ' . $organization->county : '' }}
                                </small>
                            </p>
                        </div>
                    </div>

                    <!-- Departments -->
                    @if ($organization->departments)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-sitemap mr-2"></i>
                                    Departments
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($organization->departments as $dept)
                                        <span class="badge badge-info p-2 m-1">{{ $dept }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Tabs Content -->
                <div class="col-md-9">
                    <div class="card card-primary card-outline">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link {{ $activeTab == 'overview' ? 'active' : '' }}" href="#"
                                        wire:click.prevent="$set('activeTab', 'overview')">
                                        <i class="fas fa-chart-pie mr-1"></i> Overview
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $activeTab == 'users' ? 'active' : '' }}" href="#"
                                        wire:click.prevent="$set('activeTab', 'users')">
                                        <i class="fas fa-users mr-1"></i> Users
                                        <span class="badge badge-info ml-1">{{ $stats['total_users'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $activeTab == 'opportunities' ? 'active' : '' }}"
                                        href="#" wire:click.prevent="$set('activeTab', 'opportunities')">
                                        <i class="fas fa-briefcase mr-1"></i> Opportunities
                                        <span class="badge badge-info ml-1">{{ $stats['total_opportunities'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $activeTab == 'placements' ? 'active' : '' }}"
                                        href="#" wire:click.prevent="$set('activeTab', 'placements')">
                                        <i class="fas fa-user-graduate mr-1"></i> Placements
                                        <span class="badge badge-info ml-1">{{ $stats['total_placements'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $activeTab == 'activity' ? 'active' : '' }}" href="#"
                                        wire:click.prevent="$set('activeTab', 'activity')">
                                        <i class="fas fa-history mr-1"></i> Activity
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <!-- Overview Tab -->
                            @if ($activeTab == 'overview')
                                <div class="tab-pane active">
                                    <!-- Description -->
                                    <div class="mb-4">
                                        <h5 class="mb-3">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            About {{ $organization->name }}
                                        </h5>
                                        <div class="p-3 bg-light rounded">
                                            {{ $organization->description ?? 'No description provided.' }}
                                        </div>
                                    </div>

                                    <!-- Quick Stats -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info"><i
                                                        class="fas fa-calendar-alt"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Member Since</span>
                                                    <span class="info-box-number">
                                                        {{ $organization->created_at->format('d M Y') }}
                                                        <small>({{ $organization->created_at->diffForHumans() }})</small>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i
                                                        class="fas fa-check-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Last Updated</span>
                                                    <span class="info-box-number">
                                                        {{ $organization->updated_at->format('d M Y') }}
                                                        <small>({{ $organization->updated_at->diffForHumans() }})</small>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Recent Activity Preview -->
                                    <div class="mt-4">
                                        <h5 class="mb-3">
                                            <i class="fas fa-clock mr-2"></i>
                                            Recent Activity
                                        </h5>
                                        <div class="timeline">
                                            @forelse($activityLog as $activity)
                                                <div>
                                                    <i
                                                        class="fas fa-{{ $activity['type'] == 'opportunity' ? 'briefcase' : ($activity['type'] == 'placement' ? 'user-graduate' : 'user') }} 
                                                      bg-{{ $activity['type'] == 'opportunity' ? 'info' : ($activity['type'] == 'placement' ? 'success' : 'primary') }}"></i>
                                                    <div class="timeline-item">
                                                        <span class="time">
                                                            <i class="fas fa-clock"></i>
                                                            {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                                                        </span>
                                                        <h3 class="timeline-header">
                                                            <span
                                                                class="text-capitalize">{{ $activity['type'] }}</span>
                                                            {{ $activity['action'] }}
                                                        </h3>
                                                        <div class="timeline-body">
                                                            {{ $activity['title'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted">No recent activity</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Users Tab -->
                            @if ($activeTab == 'users')
                                <div class="tab-pane active">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">
                                            <i class="fas fa-users mr-2"></i>
                                            Organization Users
                                        </h5>
                                        <button class="btn btn-primary btn-sm" wire:click="addUser">
                                            <i class="fas fa-plus"></i> Add User
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>User</th>
                                                    <th>Role</th>
                                                    <th>Position</th>
                                                    <th>Status</th>
                                                    <th>Added</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($organization->users as $user)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="user-panel">
                                                                    <div class="image">
                                                                        <img src="{{ $user->profile_photo_url }}"
                                                                            class="img-circle elevation-2"
                                                                            alt="{{ $user->full_name }}"
                                                                            style="width: 30px; height: 30px;">
                                                                    </div>
                                                                </div>
                                                                <div class="ml-2">
                                                                    <strong>{{ $user->full_name }}</strong><br>
                                                                    <small
                                                                        class="text-muted">{{ $user->email }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm"
                                                                wire:change="updateUserRole({{ $user->id }}, $event.target.value)">
                                                                <option value="owner"
                                                                    {{ $user->pivot->role == 'owner' ? 'selected' : '' }}>
                                                                    Owner</option>
                                                                <option value="admin"
                                                                    {{ $user->pivot->role == 'admin' ? 'selected' : '' }}>
                                                                    Admin</option>
                                                                <option value="member"
                                                                    {{ $user->pivot->role == 'member' ? 'selected' : '' }}>
                                                                    Member</option>
                                                                <option value="contact"
                                                                    {{ $user->pivot->role == 'contact' ? 'selected' : '' }}>
                                                                    Contact</option>
                                                            </select>
                                                        </td>
                                                        <td>{{ $user->pivot->position ?? 'N/A' }}</td>
                                                        <td>
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="userActive{{ $user->id }}"
                                                                    wire:change="toggleUserActive({{ $user->id }})"
                                                                    {{ $user->pivot->is_active ? 'checked' : '' }}>
                                                                <label class="custom-control-label"
                                                                    for="userActive{{ $user->id }}">
                                                                    <span
                                                                        class="badge {{ $user->pivot->is_active ? 'badge-success' : 'badge-secondary' }}">
                                                                        {{ $user->pivot->is_active ? 'Active' : 'Inactive' }}
                                                                    </span>
                                                                </label>
                                                            </div>
                                                            @if ($user->pivot->is_primary_contact)
                                                                <span class="badge badge-info mt-1">Primary
                                                                    Contact</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $user->pivot->created_at->format('d M Y') }}</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-danger"
                                                                onclick="confirm('Remove this user?') || event.stopImmediatePropagation()"
                                                                wire:click="removeUser({{ $user->id }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Opportunities Tab -->
                            @if ($activeTab == 'opportunities')
                                <div class="tab-pane active">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">
                                            <i class="fas fa-briefcase mr-2"></i>
                                            Attachment Opportunities
                                        </h5>
                                        <a href="{{ route('admin.opportunities.create', ['organization_id' => $organization->id]) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> New Opportunity
                                        </a>
                                    </div>

                                    @forelse($organization->opportunities as $opportunity)
                                        <div class="post">
                                            <div class="user-block">
                                                <img class="img-circle img-bordered-sm"
                                                    src="{{ $organization->logo ?? asset('img/default-org.png') }}"
                                                    alt="Organization">
                                                <span class="username">
                                                    <a
                                                        href="{{ route('admin.opportunities.show', $opportunity->id) }}">
                                                        {{ $opportunity->title }}
                                                    </a>
                                                    <span class="float-right">
                                                        <span
                                                            class="badge {{ $opportunity->is_active ? 'badge-success' : 'badge-secondary' }}">
                                                            {{ $opportunity->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </span>
                                                </span>
                                                <span class="description">
                                                    <i class="fas fa-clock"></i> Deadline:
                                                    {{ $opportunity->deadline->format('d M Y') }}
                                                    @if ($opportunity->deadline->isPast())
                                                        <span class="badge badge-danger ml-2">Expired</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <p>{{ Str::limit($opportunity->description, 200) }}</p>
                                            <p>
                                                <span class="badge badge-info">{{ $opportunity->type }}</span>
                                                <span class="badge badge-secondary">{{ $opportunity->slots }}
                                                    slots</span>
                                                <span
                                                    class="badge badge-primary">{{ $opportunity->applications_count }}
                                                    applications</span>
                                            </p>
                                        </div>
                                    @empty
                                        <p class="text-muted text-center py-4">No opportunities posted yet.</p>
                                    @endforelse
                                </div>
                            @endif

                            <!-- Placements Tab -->
                            @if ($activeTab == 'placements')
                                <div class="tab-pane active">
                                    <h5 class="mb-3">
                                        <i class="fas fa-user-graduate mr-2"></i>
                                        Placement History
                                    </h5>

                                    @forelse($organization->placements as $placement)
                                        <div class="post">
                                            <div class="user-block">
                                                <img class="img-circle img-bordered-sm"
                                                    src="{{ $placement->user->profile_photo_url ?? asset('img/default-user.png') }}"
                                                    alt="Student">
                                                <span class="username">
                                                    <a href="{{ route('admin.students.show', $placement->user_id) }}">
                                                        {{ $placement->user->full_name ?? 'Unknown' }}
                                                    </a>
                                                    <span class="float-right">
                                                        <span
                                                            class="badge 
                                                        @if ($placement->status == 'placed') badge-success
                                                        @elseif($placement->status == 'completed') badge-info
                                                        @elseif($placement->status == 'cancelled') badge-danger
                                                        @else badge-secondary @endif">
                                                            {{ ucfirst($placement->status) }}
                                                        </span>
                                                    </span>
                                                </span>
                                                <span class="description">
                                                    <i class="fas fa-calendar"></i>
                                                    {{ $placement->start_date->format('d M Y') }} -
                                                    {{ $placement->end_date->format('d M Y') }}
                                                </span>
                                            </div>
                                            <p>
                                                <strong>Supervisor:</strong>
                                                {{ $placement->supervisor_name ?? 'Not assigned' }}<br>
                                                @if ($placement->supervisor_email)
                                                    <small>{{ $placement->supervisor_email }}</small>
                                                @endif
                                            </p>
                                        </div>
                                    @empty
                                        <p class="text-muted text-center py-4">No placements recorded yet.</p>
                                    @endforelse
                                </div>
                            @endif

                            <!-- Activity Tab -->
                            @if ($activeTab == 'activity')
                                <div class="tab-pane active">
                                    <h5 class="mb-3">
                                        <i class="fas fa-history mr-2"></i>
                                        Activity Timeline
                                    </h5>

                                    <div class="timeline">
                                        @forelse($activityLog as $activity)
                                            <div>
                                                <i
                                                    class="fas fa-{{ $activity['type'] == 'opportunity' ? 'briefcase' : ($activity['type'] == 'placement' ? 'user-graduate' : 'user') }} 
                                                  bg-{{ $activity['type'] == 'opportunity' ? 'info' : ($activity['type'] == 'placement' ? 'success' : 'primary') }}"></i>
                                                <div class="timeline-item">
                                                    <span class="time">
                                                        <i class="fas fa-clock"></i>
                                                        {{ \Carbon\Carbon::parse($activity['time'])->format('d M Y H:i') }}
                                                    </span>
                                                    <h3 class="timeline-header">
                                                        <span class="text-capitalize">{{ $activity['type'] }}</span>
                                                        {{ $activity['action'] }}
                                                        <small>{{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}</small>
                                                    </h3>
                                                    <div class="timeline-body">
                                                        {{ $activity['title'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-muted text-center py-4">No activity recorded yet.</p>
                                        @endforelse
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add User to Organization</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveUser">
                        <div class="form-group">
                            <label>Select User</label>
                            <select wire:model="selectedUserId" class="form-control" required>
                                <option value="">Choose a user...</option>
                                @foreach ($availableUsers as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->full_name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <select wire:model="userRole" class="form-control">
                                <option value="member">Member</option>
                                <option value="admin">Admin</option>
                                <option value="owner">Owner</option>
                                <option value="contact">Contact Person</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Position/Title</label>
                            <input type="text" wire:model="userPosition" class="form-control"
                                placeholder="e.g., HR Manager">
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="isPrimaryContact"
                                    wire:model="isPrimaryContact">
                                <label class="custom-control-label" for="isPrimaryContact">
                                    Set as Primary Contact
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Add User</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    @push('scripts')
        <script>
            // Handle modal
            Livewire.on('showAddUserModal', () => {
                $('#addUserModal').modal('show');
            });

            Livewire.on('refresh', () => {
                $('#addUserModal').modal('hide');
            });

            // Toastr notifications
            Livewire.on('toastr:success', ({
                message
            }) => {
                toastr.success(message);
            });

            Livewire.on('toastr:error', ({
                message
            }) => {
                toastr.error(message);
            });
        </script>
    @endpush
</div>
