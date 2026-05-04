<x-layouts.app>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Employer Profile</h1>
                    <ol class="breadcrumb text-sm">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.employers.index') }}">Employers</a></li>
                        <li class="breadcrumb-item active">{{ $employer->full_name }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a href="{{ route('admin.employers.edit', $employer) }}" class="btn btn-warning">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.employers.toggle-active', $employer) }}" method="POST" class="d-inline ml-2">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-{{ $employer->is_active ? 'secondary' : 'primary' }}">
                                <i class="fas fa-{{ $employer->is_active ? 'ban' : 'check-circle' }} mr-1"></i>
                                {{ $employer->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        @if(!$employer->is_verified)
                            <form action="{{ route('admin.employers.verify', $employer) }}" method="POST" class="d-inline ml-2">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check mr-1"></i> Verify
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.employers.index') }}" class="btn btn-default ml-2">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <!-- Profile Card -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                @if($employer->profile_photo_path)
                                    <img class="profile-user-img img-fluid img-circle"
                                         src="{{ $employer->profile_photo_url }}"
                                         alt="{{ $employer->full_name }}">
                                @else
                                    <div class="avatar-initials bg-info img-circle d-flex align-items-center justify-content-center mx-auto"
                                         style="width: 100px; height: 100px; color: white; font-size: 36px;">
                                        {{ $employer->initials() }}
                                    </div>
                                @endif
                                
                                <h3 class="profile-username text-center mt-2">{{ $employer->full_name }}</h3>
                                
                                <p class="text-muted text-center">
                                    @if($employer->organizations->isNotEmpty())
                                        {{ $employer->organizations->first()->pivot->position ?? 'Employer' }}
                                    @else
                                        Employer
                                    @endif
                                </p>

                                <div class="mb-3">
                                    @if($employer->is_verified)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle mr-1"></i> Verified
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock mr-1"></i> Pending
                                        </span>
                                    @endif
                                    
                                    <span class="badge badge-{{ $employer->is_active ? 'success' : 'danger' }} ml-1">
                                        {{ $employer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b><i class="fas fa-envelope mr-2"></i>Email</b>
                                    <a class="float-right" href="mailto:{{ $employer->email }}">
                                        {{ $employer->email }}
                                    </a>
                                </li>
                                @if($employer->phone)
                                    <li class="list-group-item">
                                        <b><i class="fas fa-phone mr-2"></i>Phone</b>
                                        <a class="float-right" href="tel:{{ $employer->phone }}">
                                            {{ $employer->phone }}
                                        </a>
                                    </li>
                                @endif
                                @if($employer->gender)
                                    <li class="list-group-item">
                                        <b><i class="fas fa-{{ $employer->gender === 'male' ? 'mars' : ($employer->gender === 'female' ? 'venus' : 'genderless') }} mr-2"></i>Gender</b>
                                        <span class="float-right">{{ ucfirst($employer->gender) }}</span>
                                    </li>
                                @endif
                                @if($employer->date_of_birth)
                                    <li class="list-group-item">
                                        <b><i class="fas fa-birthday-cake mr-2"></i>Date of Birth</b>
                                        <span class="float-right">{{ $employer->date_of_birth->format('M d, Y') }}</span>
                                    </li>
                                @endif
                                <li class="list-group-item">
                                    <b><i class="fas fa-calendar mr-2"></i>Member Since</b>
                                    <span class="float-right">{{ $employer->created_at->format('M d, Y') }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-clock mr-2"></i>Last Login</b>
                                    <span class="float-right">
                                        {{ $employer->last_login_at ? $employer->last_login_at->diffForHumans() : 'Never' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Location Card -->
                    @if($employer->county || $employer->constituency || $employer->ward)
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Location
                                </h3>
                            </div>
                            <div class="card-body">
                                @if($employer->county)
                                    <div class="mb-2">
                                        <strong>County:</strong>
                                        <span class="float-right">{{ $employer->county }}</span>
                                    </div>
                                @endif
                                @if($employer->constituency)
                                    <div class="mb-2">
                                        <strong>Constituency:</strong>
                                        <span class="float-right">{{ $employer->constituency }}</span>
                                    </div>
                                @endif
                                @if($employer->ward)
                                    <div class="mb-0">
                                        <strong>Ward:</strong>
                                        <span class="float-right">{{ $employer->ward }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-8">
                    <!-- Organizations Card -->
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#organizations" data-toggle="tab">
                                        <i class="fas fa-building mr-1"></i> Organizations
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#opportunities" data-toggle="tab">
                                        <i class="fas fa-briefcase mr-1"></i> Opportunities
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#placements" data-toggle="tab">
                                        <i class="fas fa-user-check mr-1"></i> Placements
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#activity" data-toggle="tab">
                                        <i class="fas fa-history mr-1"></i> Activity
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Organizations Tab -->
                                <div class="tab-pane active" id="organizations">
                                    @if($employer->organizations->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Organization</th>
                                                        <th>Role</th>
                                                        <th>Position</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($employer->organizations as $organization)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $organization->name }}</strong>
                                                                <br>
                                                                <small class="text-muted">{{ $organization->industry ?? 'N/A' }}</small>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-{{ $organization->pivot->role === 'owner' ? 'primary' : ($organization->pivot->role === 'admin' ? 'info' : 'secondary') }}">
                                                                    {{ ucfirst($organization->pivot->role) }}
                                                                </span>
                                                                @if($organization->pivot->is_primary_contact)
                                                                    <span class="badge badge-warning ml-1">Primary</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $organization->pivot->position ?? '-' }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $organization->is_active ? 'success' : 'danger' }}">
                                                                    {{ $organization->is_active ? 'Active' : 'Inactive' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No organizations assigned</p>
                                            <a href="{{ route('admin.organizations.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus mr-1"></i> Create Organization
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <!-- Opportunities Tab -->
                                <div class="tab-pane" id="opportunities">
                                    @if($employer->organizations->isNotEmpty() && $employer->organizations->flatMap->opportunities->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Opportunity</th>
                                                        <th>Organization</th>
                                                        <th>Slots</th>
                                                        <th>Deadline</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($employer->organizations as $organization)
                                                        @foreach($organization->opportunities->take(5) as $opportunity)
                                                            <tr>
                                                                <td>{{ $opportunity->title ?? 'Untitled' }}</td>
                                                                <td>{{ $organization->name }}</td>
                                                                <td>{{ $opportunity->slots ?? 'N/A' }}</td>
                                                                <td>{{ $opportunity->deadline ? $opportunity->deadline->format('M d, Y') : 'N/A' }}</td>
                                                                <td>
                                                                    <span class="badge badge-info">Active</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No opportunities posted</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Placements Tab -->
                                <div class="tab-pane" id="placements">
                                    @php
                                        $placements = $employer->organizations->flatMap->placements;
                                    @endphp
                                    @if($placements->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Student</th>
                                                        <th>Organization</th>
                                                        <th>Start Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($placements->take(10) as $placement)
                                                        <tr>
                                                            <td>{{ $placement->student->full_name ?? 'N/A' }}</td>
                                                            <td>{{ $placement->organization->name ?? 'N/A' }}</td>
                                                            <td>{{ $placement->start_date ? $placement->start_date->format('M d, Y') : 'N/A' }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $placement->status === 'placed' ? 'success' : 'secondary' }}">
                                                                    {{ ucfirst($placement->status) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-user-check fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No placements yet</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Activity Tab -->
                                <div class="tab-pane" id="activity">
                                    @php
                                        $activities = \App\Models\ActivityLog::where('causer_id', $employer->id)
                                            ->latest()
                                            ->take(20)
                                            ->get();
                                    @endphp
                                    @if($activities->isNotEmpty())
                                        <div class="timeline">
                                            @foreach($activities as $activity)
                                                <div class="time-label">
                                                    <span class="bg-{{ $loop->first ? 'primary' : 'secondary' }}">
                                                        {{ $activity->created_at->format('M d, Y') }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <i class="fas {{ $activity->icon }} bg-{{ $loop->first ? 'info' : 'secondary' }}"></i>
                                                    <div class="timeline-item">
                                                        <span class="time">
                                                            <i class="fas fa-clock"></i> {{ $activity->created_at->format('H:i') }}
                                                        </span>
                                                        <h3 class="timeline-header">
                                                            {{ ucfirst($activity->event) }}
                                                        </h3>
                                                        <div class="timeline-body">
                                                            {{ $activity->description }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No activity recorded yet</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>