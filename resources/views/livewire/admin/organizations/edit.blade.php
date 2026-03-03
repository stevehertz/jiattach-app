<div>
    <!-- Page Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-edit mr-2"></i>
                        Edit Organization
                        <small class="text-muted">{{ $organization->name }}</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.organizations.index') }}">Organizations</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.organizations.show', $organization->id) }}">{{ $organization->name }}</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form wire:submit.prevent="update">
                <!-- Status Bar -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-toggle-on mr-2"></i>
                                    Organization Status
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="is_active"
                                                wire:model="is_active">
                                            <label class="custom-control-label" for="is_active">
                                                <strong>Active Status</strong>
                                                <span
                                                    class="badge {{ $is_active ? 'badge-success' : 'badge-secondary' }} ml-2">
                                                    {{ $is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </label>
                                            <small class="form-text text-muted">
                                                Inactive organizations won't appear in listings or receive new matches.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="is_verified"
                                                wire:model="is_verified">
                                            <label class="custom-control-label" for="is_verified">
                                                <strong>Verification Status</strong>
                                                <span
                                                    class="badge {{ $is_verified ? 'badge-success' : 'badge-warning' }} ml-2">
                                                    {{ $is_verified ? 'Verified' : 'Pending' }}
                                                </span>
                                            </label>
                                            <small class="form-text text-muted">
                                                Verified organizations have been vetted and approved.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Main Information -->
                    <div class="col-md-8">
                        <!-- Basic Information Card -->
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-building mr-2"></i>
                                    Basic Information
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required">Organization Name <span
                                                    class="text-danger">*</span></label>
                                            <input wire:model="name" type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Enter organization name">
                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Organization Type</label>
                                            <select wire:model="type"
                                                class="form-control @error('type') is-invalid @enderror">
                                                <option value="">Select Type</option>
                                                @foreach ($organizationTypes as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('type')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required">Industry <span class="text-danger">*</span></label>
                                            <select wire:model="industry"
                                                class="form-control @error('industry') is-invalid @enderror">
                                                <option value="">Select Industry</option>
                                                @foreach ($industries as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('industry')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Website</label>
                                            <input wire:model="website" type="url"
                                                class="form-control @error('website') is-invalid @enderror"
                                                placeholder="https://example.com">
                                            @error('website')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required">Official Email <span
                                                    class="text-danger">*</span></label>
                                            <input wire:model="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder="info@organization.com">
                                            @error('email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required">Phone Number <span
                                                    class="text-danger">*</span></label>
                                            <input wire:model="phone" type="text"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                placeholder="+254 XXX XXX XXX">
                                            @error('phone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                                        placeholder="Provide a brief description of the organization..."></textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Location Information Card -->
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    Location Information
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>County</label>
                                            <select wire:model="county"
                                                class="form-control @error('county') is-invalid @enderror">
                                                <option value="">Select County</option>
                                                @foreach ($counties as $countyOption)
                                                    <option value="{{ $countyOption }}">{{ $countyOption }}</option>
                                                @endforeach
                                            </select>
                                            @error('county')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Constituency</label>
                                            <input wire:model="constituency" type="text"
                                                class="form-control @error('constituency') is-invalid @enderror"
                                                placeholder="e.g., Westlands">
                                            @error('constituency')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Ward</label>
                                            <input wire:model="ward" type="text"
                                                class="form-control @error('ward') is-invalid @enderror"
                                                placeholder="e.g., Kilimani">
                                            @error('ward')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Street Address / Building</label>
                                    <input wire:model="address" type="text"
                                        class="form-control @error('address') is-invalid @enderror"
                                        placeholder="e.g., 123 Kenyatta Avenue, 4th Floor">
                                    @error('address')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Person Card -->
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-user-tie mr-2"></i>
                                    Contact Person
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Contact Person Name</label>
                                            <input wire:model="contact_person_name" type="text"
                                                class="form-control @error('contact_person_name') is-invalid @enderror"
                                                placeholder="Full name">
                                            @error('contact_person_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Position/Title</label>
                                            <input wire:model="contact_person_position" type="text"
                                                class="form-control @error('contact_person_position') is-invalid @enderror"
                                                placeholder="e.g., HR Manager">
                                            @error('contact_person_position')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Contact Email</label>
                                            <input wire:model="contact_person_email" type="email"
                                                class="form-control @error('contact_person_email') is-invalid @enderror"
                                                placeholder="contact@organization.com">
                                            @error('contact_person_email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Contact Phone</label>
                                            <input wire:model="contact_person_phone" type="text"
                                                class="form-control @error('contact_person_phone') is-invalid @enderror"
                                                placeholder="+254 XXX XXX XXX">
                                            @error('contact_person_phone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Information -->
                    <div class="col-md-4">
                        <!-- Capacity Card -->
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-users mr-2"></i>
                                    Placement Capacity
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Maximum Students Per Intake</label>
                                    <input wire:model="max_students_per_intake" type="number"
                                        class="form-control @error('max_students_per_intake') is-invalid @enderror"
                                        min="1" step="1" placeholder="e.g., 50">
                                    @error('max_students_per_intake')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">Leave empty for unlimited</small>
                                </div>

                                @if ($max_students_per_intake)
                                    <div class="info-box bg-light mt-3">
                                        <span class="info-box-icon bg-info"><i class="fas fa-chart-line"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Current Usage</span>
                                            <span class="info-box-number">
                                                {{ $organization->placements()->where('status', 'placed')->count() }} /
                                                {{ $max_students_per_intake }}
                                            </span>
                                            <div class="progress">
                                                <div class="progress-bar bg-info"
                                                    style="width: {{ min(100, ($organization->placements()->where('status', 'placed')->count() / $max_students_per_intake) * 100) }}%">
                                                </div>
                                            </div>
                                            <span class="info-box-text mt-2">
                                                Available: {{ $organization->available_slots }} slots
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Associated Users Card -->
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-users mr-2"></i>
                                    Associated Users
                                    <span class="badge badge-info ml-2">{{ count($selectedUsers) }}</span>
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool"
                                        wire:click="$set('showUserSearch', true)">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                @if ($showUserSearch)
                                    <div class="p-3 bg-light">
                                        <div class="input-group mb-2">
                                            <input wire:model.live.debounce.300ms="userSearch" type="text"
                                                class="form-control" placeholder="Search users by name or email...">
                                        </div>

                                        @if ($availableUsers)
                                            <div class="list-group">
                                                @foreach ($availableUsers as $user)
                                                    <a href="#"
                                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                                        wire:click.prevent="addUser({{ $user['id'] }})">
                                                        <div>
                                                            <strong>{{ $user['first_name'] }}
                                                                {{ $user['last_name'] }}</strong><br>
                                                            <small>{{ $user['email'] }}</small>
                                                        </div>
                                                        <i class="fas fa-plus-circle text-success"></i>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @elseif(strlen($userSearch) >= 2)
                                            <p class="text-muted">No users found</p>
                                        @endif

                                        <button type="button" class="btn btn-sm btn-link mt-2"
                                            wire:click="$set('showUserSearch', false)">
                                            <i class="fas fa-times"></i> Close
                                        </button>
                                    </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <tbody>
                                            @forelse($existingUsers as $user)
                                                <tr>
                                                    <td>
                                                        <div class="user-panel d-flex align-items-center">
                                                            <div class="image">
                                                                <img src="{{ $user->profile_photo_url }}"
                                                                    class="img-circle elevation-1"
                                                                    alt="{{ $user->full_name }}"
                                                                    style="width: 30px; height: 30px;">
                                                            </div>
                                                            <div class="info ml-2">
                                                                <strong>{{ $user->full_name }}</strong><br>
                                                                <small class="text-muted">{{ $user->email }}</small>
                                                                @if ($user->pivot->is_primary_contact)
                                                                    <span class="badge badge-info">Primary
                                                                        Contact</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td width="120">
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
                                                    <td width="40">
                                                        <a href="#"
                                                            wire:click.prevent="removeUser({{ $user->id }})"
                                                            class="text-danger"
                                                            onclick="return confirm('Remove this user from organization?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-3">
                                                        <p class="text-muted mb-0">No users associated</p>
                                                        <small>Click the <i class="fas fa-user-plus"></i> icon to add
                                                            users</small>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats Card -->
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-pie mr-2"></i>
                                    Quick Stats
                                </h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-briefcase mr-2 text-info"></i> Opportunities</span>
                                        <span
                                            class="badge badge-info">{{ $organization->opportunities()->count() }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-user-graduate mr-2 text-success"></i> Placements</span>
                                        <span
                                            class="badge badge-success">{{ $organization->placements()->count() }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-users mr-2 text-primary"></i> Associated Users</span>
                                        <span class="badge badge-primary">{{ count($selectedUsers) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-calendar-alt mr-2 text-warning"></i> Member Since</span>
                                        <span>{{ $organization->created_at->format('d M Y') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-clock mr-2 text-secondary"></i> Last Updated</span>
                                        <span>{{ $organization->updated_at->diffForHumans() }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-footer">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.organizations.show', $organization->id) }}"
                                        class="btn btn-secondary">
                                        <i class="fas fa-times mr-1"></i> Cancel
                                    </a>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i> Update Organization
                                        </button>
                                        <a href="{{ route('admin.organizations.index') }}"
                                            class="btn btn-outline-secondary ml-2">
                                            <i class="fas fa-arrow-left mr-1"></i> Back to List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Toastr Notifications -->
    @push('scripts')
        <script>
            Livewire.on('toastr:success', ({
                message
            }) => {
                toastr.success(message, 'Success', {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 3000
                });
            });

            Livewire.on('toastr:error', ({
                message
            }) => {
                toastr.error(message, 'Error', {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 5000
                });
            });
        </script>
    @endpush
</div>
