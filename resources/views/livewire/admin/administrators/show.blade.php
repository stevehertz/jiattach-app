<div>
    {{-- In work, do what you enjoy. --}}
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        {!! getUserAvatar($administrator, 100) !!}
                    </div>
                    
                    <h3 class="profile-username text-center">{{ $administrator->full_name }}</h3>
                    
                    <p class="text-muted text-center">
                        @php
                            $primaryRole = $administrator->getRoleNames()->first();
                            $roleBadgeColor = match($primaryRole) {
                                'super-admin' => 'danger',
                                'admin' => 'success',
                                'moderator' => 'info',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge badge-{{ $roleBadgeColor }}">
                            {{ ucfirst(str_replace('-', ' ', $primaryRole)) }}
                        </span>
                        @if($administrator->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                        @if($administrator->is_verified)
                            <span class="badge badge-info">Verified</span>
                        @endif
                    </p>
                    
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Email</b>
                            <a class="float-right" href="mailto:{{ $administrator->email }}">
                                {{ $administrator->email }}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>Phone</b>
                            <span class="float-right">{{ formatPhoneNumber($administrator->phone) }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>National ID</b>
                            <span class="float-right">{{ $administrator->national_id }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Member Since</b>
                            <span class="float-right">{{ formatDate($administrator->created_at) }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Last Login</b>
                            <span class="float-right">
                                {{ $stats['last_login'] }}
                            </span>
                        </li>
                    </ul>
                    
                    <div class="btn-group w-100">
                        {{-- <a href="{{ route('admin.administrators.edit', $administrator) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a> --}}
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown">
                            <i class="fas fa-cog"></i> Actions
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <button class="dropdown-item" wire:click="openPasswordModal">
                                <i class="fas fa-key text-warning"></i> Change Password
                            </button>
                            <button class="dropdown-item" wire:click="toggleStatus">
                                @if($administrator->is_active)
                                    <i class="fas fa-user-times text-danger"></i> Deactivate
                                @else
                                    <i class="fas fa-user-check text-success"></i> Activate
                                @endif
                            </button>
                            @if(!$isCurrentUser)
                                <div class="dropdown-divider"></div>
                                <button class="dropdown-item text-danger" wire:click="openDeleteModal">
                                    <i class="fas fa-trash"></i> Delete Administrator
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stats Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center">
                            <div class="info-box bg-gradient-info">
                                <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active For</span>
                                    <span class="info-box-number">{{ $stats['days_active'] }} days</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="info-box bg-gradient-success">
                                <span class="info-box-icon"><i class="fas fa-sign-in-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Logins</span>
                                    <span class="info-box-number">{{ $stats['total_logins'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 text-center">
                            <div class="info-box bg-gradient-warning">
                                <span class="info-box-icon"><i class="fas fa-user-tag"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Assigned Roles</span>
                                    <span class="info-box-number">{{ $stats['assigned_roles'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="info-box bg-gradient-primary">
                                <span class="info-box-icon"><i class="fas fa-shield-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Permissions</span>
                                    <span class="info-box-number">{{ $stats['permissions_count'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Tabs Navigation -->
            <div class="card">
                <div class="card-header p-0">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'overview' ? 'active' : '' }}" 
                               wire:click="switchTab('overview')" href="#">
                                <i class="fas fa-user-circle mr-1"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'roles' ? 'active' : '' }}" 
                               wire:click="switchTab('roles')" href="#">
                                <i class="fas fa-user-tag mr-1"></i> Roles & Permissions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'activity' ? 'active' : '' }}" 
                               wire:click="switchTab('activity')" href="#">
                                <i class="fas fa-history mr-1"></i> Recent Activity
                                @if(count($recentActivity) > 0)
                                    <span class="badge badge-info">{{ count($recentActivity) }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTab === 'security' ? 'active' : '' }}" 
                               wire:click="switchTab('security')" href="#">
                                <i class="fas fa-shield-alt mr-1"></i> Security
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    
                    <!-- Overview Tab -->
                    @if($activeTab === 'overview')
                        <div class="tab-content">
                            @if($isEditing && $editSection === 'personal')
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h4 class="card-title">Edit Personal Information</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>First Name *</label>
                                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                                           wire:model="first_name">
                                                    @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Last Name *</label>
                                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                                           wire:model="last_name">
                                                    @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Email *</label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                           wire:model="email">
                                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Phone *</label>
                                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                           wire:model="phone">
                                                    @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>National ID *</label>
                                                    <input type="text" class="form-control @error('national_id') is-invalid @enderror"
                                                           wire:model="national_id">
                                                    @error('national_id') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Date of Birth *</label>
                                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                                           wire:model="date_of_birth" max="{{ date('Y-m-d') }}">
                                                    @error('date_of_birth') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Gender *</label>
                                            <select class="form-control @error('gender') is-invalid @enderror" wire:model="gender">
                                                <option value="">Select Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                            @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-secondary" wire:click="cancelEditing">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                            <button class="btn btn-primary" wire:click="saveProfile">
                                                <i class="fas fa-save"></i> Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h4 class="card-title">Personal Information</h4>
                                        <div class="card-tools">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    wire:click="startEditing('personal')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Full Name:</strong> {{ $administrator->full_name }}</p>
                                                <p><strong>Email:</strong> {{ $administrator->email }}</p>
                                                <p><strong>Phone:</strong> {{ formatPhoneNumber($administrator->phone) }}</p>
                                                <p><strong>National ID:</strong> {{ $administrator->national_id }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Date of Birth:</strong> {{ formatDate($administrator->date_of_birth) }}</p>
                                                <p><strong>Gender:</strong> {{ ucfirst($administrator->gender) }}</p>
                                                <p><strong>Age:</strong> {{ \Carbon\Carbon::parse($administrator->date_of_birth)->age }} years</p>
                                                <p><strong>Account Status:</strong> 
                                                    @if($administrator->is_active)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if($isEditing && $editSection === 'location')
                                <div class="card card-primary mt-3">
                                    <div class="card-header">
                                        <h4 class="card-title">Edit Location Information</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>County *</label>
                                            <select class="form-control @error('county') is-invalid @enderror" wire:model="county">
                                                <option value="">Select County</option>
                                                @foreach($counties as $county)
                                                    <option value="{{ $county }}">{{ $county }}</option>
                                                @endforeach
                                            </select>
                                            @error('county') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Constituency</label>
                                                    <input type="text" class="form-control @error('constituency') is-invalid @enderror"
                                                           wire:model="constituency">
                                                    @error('constituency') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Ward</label>
                                                    <input type="text" class="form-control @error('ward') is-invalid @enderror"
                                                           wire:model="ward">
                                                    @error('ward') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Bio</label>
                                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                                      wire:model="bio" rows="3"></textarea>
                                            @error('bio') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-secondary" wire:click="cancelEditing">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                            <button class="btn btn-primary" wire:click="saveProfile">
                                                <i class="fas fa-save"></i> Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card card-primary mt-3">
                                    <div class="card-header">
                                        <h4 class="card-title">Location Information</h4>
                                        <div class="card-tools">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    wire:click="startEditing('location')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>County:</strong> {{ $administrator->county }}</p>
                                                <p><strong>Constituency:</strong> {{ $administrator->constituency ?? 'Not specified' }}</p>
                                                <p><strong>Ward:</strong> {{ $administrator->ward ?? 'Not specified' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                @if($administrator->bio)
                                                    <p><strong>Bio:</strong></p>
                                                    <p class="text-muted">{{ $administrator->bio }}</p>
                                                @else
                                                    <p class="text-muted">No bio provided.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Account Information -->
                            <div class="card card-info mt-3">
                                <div class="card-header">
                                    <h4 class="card-title">Account Information</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Account Created:</strong> {{ formatDateTime($administrator->created_at) }}</p>
                                            <p><strong>Email Verified:</strong> 
                                                @if($administrator->email_verified_at)
                                                    <span class="badge badge-success">Yes</span> 
                                                    ({{ formatDate($administrator->email_verified_at) }})
                                                @else
                                                    <span class="badge badge-warning">No</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Last Login:</strong> 
                                                @if($administrator->last_login_at)
                                                    {{ formatDateTime($administrator->last_login_at) }}
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </p>
                                            <p><strong>Last Updated:</strong> {{ formatDateTime($administrator->updated_at) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Roles & Permissions Tab -->
                    @if($activeTab === 'roles')
                        <div class="tab-content">
                            @if($isEditing && $editSection === 'roles')
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h4 class="card-title">Edit Roles</h4>
                                    </div>
                                    <div class="card-body">
                                        @foreach(['super-admin', 'admin', 'moderator'] as $role)
                                            @php
                                                $roleDescription = match($role) {
                                                    'super-admin' => 'Full system access, can manage all administrators and system settings.',
                                                    'admin' => 'Full access to manage users, opportunities, and mentorship programs.',
                                                    'moderator' => 'Limited access for content moderation and user management.',
                                                };
                                                $badgeColor = match($role) {
                                                    'super-admin' => 'danger',
                                                    'admin' => 'success',
                                                    'moderator' => 'info',
                                                };
                                            @endphp
                                            <div class="custom-control custom-checkbox mb-3">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="edit_role_{{ $role }}"
                                                       wire:model="roles" 
                                                       value="{{ $role }}">
                                                <label class="custom-control-label d-flex align-items-start" for="edit_role_{{ $role }}">
                                                    <div class="mr-2">
                                                        <span class="badge badge-{{ $badgeColor }}">
                                                            {{ ucfirst(str_replace('-', ' ', $role)) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <small class="text-muted d-block">{{ $roleDescription }}</small>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                        
                                        @error('roles') 
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                        
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Note:</strong> Changing roles will affect the administrator's permissions.
                                            At least one role must be assigned.
                                        </div>
                                        
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-secondary" wire:click="cancelEditing">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                            <button class="btn btn-primary" wire:click="saveRoles">
                                                <i class="fas fa-save"></i> Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h4 class="card-title">Current Roles</h4>
                                        <div class="card-tools">
                                            <button class="btn btn-sm btn-outline-warning" 
                                                    wire:click="startEditing('roles')">
                                                <i class="fas fa-edit"></i> Edit Roles
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            @foreach($administrator->getRoleNames() as $role)
                                                @php
                                                    $badgeColor = match($role) {
                                                        'super-admin' => 'danger',
                                                        'admin' => 'success',
                                                        'moderator' => 'info',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge badge-{{ $badgeColor }} p-2 mb-2 mr-2" style="font-size: 1em;">
                                                    <i class="fas fa-user-tag mr-1"></i>
                                                    {{ ucfirst(str_replace('-', ' ', $role)) }}
                                                </span>
                                            @endforeach
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Roles:</strong>
                                            <ul class="mb-0 mt-2">
                                                @foreach($administrator->getRoleNames() as $role)
                                                    <li>
                                                        <strong>{{ ucfirst(str_replace('-', ' ', $role)) }}:</strong> 
                                                        {{ match($role) {
                                                            'super-admin' => 'Full system access, can manage all administrators and system settings.',
                                                            'admin' => 'Full access to manage users, opportunities, and mentorship programs.',
                                                            'moderator' => 'Limited access for content moderation and user management.',
                                                        } }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Permissions Overview -->
                                <div class="card card-primary mt-3">
                                    <div class="card-header">
                                        <h4 class="card-title">Permissions Overview</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($permissions as $category => $categoryPermissions)
                                                <div class="col-md-6 mb-3">
                                                    <div class="card card-outline card-secondary">
                                                        <div class="card-header">
                                                            <h5 class="card-title mb-0 text-capitalize">{{ $category }}</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            @foreach($categoryPermissions as $permission => $hasPermission)
                                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                                    <span>{{ ucfirst(str_replace('_', ' ', $permission)) }}</span>
                                                                    @if($hasPermission)
                                                                        <span class="badge badge-success">Allowed</span>
                                                                    @else
                                                                        <span class="badge badge-danger">Denied</span>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Recent Activity Tab -->
                    @if($activeTab === 'activity')
                        <div class="tab-content">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h4 class="card-title">Recent Activity</h4>
                                    <div class="card-tools">
                                        <button class="btn btn-sm btn-outline-info" onclick="window.print()">
                                            <i class="fas fa-print"></i> Print
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Action</th>
                                                    <th>Description</th>
                                                    <th>IP Address</th>
                                                    <th>Device</th>
                                                    <th>Timestamp</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recentActivity as $activity)
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-{{ $activity['color'] }}">
                                                                <i class="fas fa-{{ $activity['icon'] }}"></i> 
                                                                {{ $activity['action'] }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $activity['description'] }}</td>
                                                        <td>
                                                            <code>{{ $activity['ip_address'] }}</code>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">{{ $activity['user_agent'] }}</small>
                                                        </td>
                                                        <td>
                                                            {{ formatDateTime($activity['timestamp']) }}
                                                            <br>
                                                            <small class="text-muted">{{ $activity['timestamp']->diffForHumans() }}</small>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="fas fa-history fa-3x mb-3"></i>
                                                                <h4>No Recent Activity</h4>
                                                                <p>This administrator hasn't performed any actions yet.</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Activity log shows the last 50 actions. Full history is available in the system logs.
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Security Tab -->
                    @if($activeTab === 'security')
                        <div class="tab-content">
                            <div class="card card-danger">
                                <div class="card-header">
                                    <h4 class="card-title">Security Settings</h4>
                                </div>
                                <div class="card-body">
                                    <!-- Password Management -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h5 class="card-title">Password Management</h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-3">
                                                <strong>Last Password Change:</strong> 
                                                @php
                                                    $lastPasswordChange = $administrator->updated_at;
                                                @endphp
                                                {{ $lastPasswordChange ? formatDateTime($lastPasswordChange) : 'Unknown' }}
                                            </p>
                                            
                                            <button class="btn btn-warning" wire:click="openPasswordModal">
                                                <i class="fas fa-key"></i> Change Password
                                            </button>
                                            
                                            <div class="alert alert-info mt-3">
                                                <i class="fas fa-shield-alt"></i>
                                                <strong>Security Recommendations:</strong>
                                                <ul class="mb-0 mt-2">
                                                    <li>Use strong passwords with at least 12 characters</li>
                                                    <li>Enable two-factor authentication if available</li>
                                                    <li>Regularly update passwords (every 90 days)</li>
                                                    <li>Never share passwords with anyone</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Session Management -->
                                    <div class="card card-info mt-3">
                                        <div class="card-header">
                                            <h5 class="card-title">Session Management</h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-3">
                                                <strong>Current Session:</strong> Active
                                                <br>
                                                <strong>IP Address:</strong> {{ request()->ip() }}
                                                <br>
                                                <strong>Browser:</strong> {{ request()->userAgent() }}
                                            </p>
                                            
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Note:</strong> Session management features will be available in future updates.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Account Security -->
                                    <div class="card card-danger mt-3">
                                        <div class="card-header">
                                            <h5 class="card-title">Account Security</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <p><strong>Account Status:</strong> 
                                                    @if($administrator->is_active)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </p>
                                                
                                                <p><strong>Email Verification:</strong> 
                                                    @if($administrator->email_verified_at)
                                                        <span class="badge badge-success">Verified</span>
                                                    @else
                                                        <span class="badge badge-warning">Not Verified</span>
                                                    @endif
                                                </p>
                                            </div>
                                            
                                            @if(!$isCurrentUser)
                                                <button class="btn btn-outline-danger" wire:click="toggleStatus">
                                                    @if($administrator->is_active)
                                                        <i class="fas fa-user-times"></i> Deactivate Account
                                                    @else
                                                        <i class="fas fa-user-check"></i> Activate Account
                                                    @endif
                                                </button>
                                            @endif
                                            
                                            <div class="alert alert-danger mt-3">
                                                <i class="fas fa-exclamation-circle"></i>
                                                <strong>Warning:</strong> Deactivating an account will prevent the user from logging in.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

      <!-- Change Password Modal -->
    @if($showPasswordModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Change Password</h5>
                        <button type="button" class="close" wire:click="closePasswordModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Changing password for: <strong>{{ $administrator->full_name }}</strong>
                        </div>
                        
                        <div class="form-group">
                            <label for="newPassword">New Password *</label>
                            <input type="password" id="newPassword" 
                                   class="form-control @error('newPassword') is-invalid @enderror"
                                   wire:model="newPassword" placeholder="Enter new password">
                            @error('newPassword') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="newPasswordConfirmation">Confirm New Password *</label>
                            <input type="password" id="newPasswordConfirmation" 
                                   class="form-control @error('newPasswordConfirmation') is-invalid @enderror"
                                   wire:model="newPasswordConfirmation" placeholder="Confirm new password">
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Note:</strong> The user will need to use this new password on their next login.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closePasswordModal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-warning" wire:click="updatePassword">
                            <i class="fas fa-key"></i> Update Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title">Delete Administrator</h5>
                        <button type="button" class="close" wire:click="closeDeleteModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <h5>Warning: This action cannot be undone!</h5>
                        </div>
                        
                        <div class="text-center mb-4">
                            {!! getUserAvatar($administrator, 80) !!}
                            <h4 class="mt-3">{{ $administrator->full_name }}</h4>
                            <p class="text-muted">{{ $administrator->email }}</p>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i>
                            <strong>What will be deleted:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Administrator account</li>
                                <li>All associated roles and permissions</li>
                                <li>Account history and logs</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-shield-alt"></i>
                            <strong>Security Check:</strong>
                            <p class="mb-0">You are about to delete administrator <strong>{{ $administrator->full_name }}</strong>. 
                            Please type the administrator's email to confirm:</p>
                            <div class="mt-2">
                                <input type="text" class="form-control" id="confirmEmailShow" 
                                       placeholder="Type: {{ $administrator->email }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" id="confirmDeleteBtnShow" class="btn btn-danger" disabled>
                            <i class="fas fa-trash"></i> Delete Administrator
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

@push('scripts')
    <script>
        function setupDeleteConfirmShow() {
            const input = document.getElementById('confirmEmailShow');
            const btn = document.getElementById('confirmDeleteBtnShow');
            if (!input || !btn) return;

            // Extract expected email from placeholder - more robust
            let placeholder = input.getAttribute('placeholder') || '';
            let expected = placeholder.replace('Type: ', '').trim();
            
            console.log('Expected email:', expected);
            console.log('Input value:', input.value);
            
            // Update button state - normalize comparison
            const updateButtonState = () => {
                const isMatch = input.value.trim().toLowerCase() === expected.toLowerCase();
                console.log('Is match:', isMatch);
                btn.disabled = !isMatch;
            };
            
            updateButtonState();
            
            // Add direct input listener (no cloning issues)
            input.addEventListener('input', updateButtonState);
            
            // Add click listener to delete button
            btn.onclick = function(e) {
                e.preventDefault();
                const isMatch = input.value.trim().toLowerCase() === expected.toLowerCase();
                if (isMatch) {
                    @this.call('deleteAdministrator', input.value);
                } else {
                    alert('Please type the email address correctly to confirm deletion.');
                }
            };
        }

        document.addEventListener('livewire:load', function () {
            setTimeout(() => setupDeleteConfirmShow(), 100);
        });
        
        // Also run on modal open - watch for when modal appears
        const observer = new MutationObserver(() => {
            const modal = document.querySelector('.modal');
            if (modal && modal.style.display === 'block') {
                setupDeleteConfirmShow();
            }
        });
        
        observer.observe(document.body, { attributes: true, subtree: true });
    </script>
@endpush
