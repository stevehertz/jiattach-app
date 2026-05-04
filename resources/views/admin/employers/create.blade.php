<x-layouts.app>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add New Employer</h1>
                    <ol class="breadcrumb text-sm">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.employers.index') }}">Employers</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a href="{{ route('admin.employers.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left mr-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <!-- Personal Information Card -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-tie mr-2"></i>Personal Information
                            </h3>
                        </div>
                        <form action="{{ route('admin.employers.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                id="first_name" name="first_name" value="{{ old('first_name') }}"
                                                placeholder="Enter first name" required>
                                            @error('first_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('last_name') is-invalid @enderror"
                                                id="last_name" name="last_name" value="{{ old('last_name') }}"
                                                placeholder="Enter last name" required>
                                            @error('last_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email Address <span
                                                    class="text-danger">*</span></label>
                                            <input type="email"
                                                class="form-control @error('email') is-invalid @enderror" id="email"
                                                name="email" value="{{ old('email') }}"
                                                placeholder="Enter email address" required>
                                            @error('email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone Number</label>
                                            <input type="text"
                                                class="form-control @error('phone') is-invalid @enderror" id="phone"
                                                name="phone" value="{{ old('phone') }}" placeholder="07XX XXX XXX"
                                                data-inputmask='"mask": "9999 999 999"' data-mask>
                                            @error('phone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password" name="password" placeholder="Enter password"
                                                    required>
                                                <div class="input-group-append">
                                                    <button type="button" class="input-group-text password-toggle"
                                                        data-target="#password" aria-label="Toggle password visibility">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Minimum 8 characters</small>
                                            @error('password')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="gender">Gender</label>
                                            <select class="form-control @error('gender') is-invalid @enderror"
                                                id="gender" name="gender">
                                                <option value="">Select Gender</option>
                                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>
                                                    Male</option>
                                                <option value="female"
                                                    {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                                <option value="other"
                                                    {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('gender')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <!-- Location Information Card -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-map-marker-alt mr-2"></i>Location Information
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="county">County</label>
                                        <select class="form-control @error('county') is-invalid @enderror"
                                            id="county" name="county">
                                            <option value="">Select County</option>
                                            @foreach ($counties as $county)
                                                <option value="{{ $county }}"
                                                    {{ old('county') == $county ? 'selected' : '' }}>
                                                    {{ $county }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('county')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="constituency">Constituency</label>
                                        <input type="text"
                                            class="form-control @error('constituency') is-invalid @enderror"
                                            id="constituency" name="constituency" value="{{ old('constituency') }}"
                                            placeholder="Enter constituency">
                                        @error('constituency')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ward">Ward</label>
                                        <input type="text" class="form-control @error('ward') is-invalid @enderror"
                                            id="ward" name="ward" value="{{ old('ward') }}"
                                            placeholder="Enter ward">
                                        @error('ward')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_of_birth">Date of Birth</label>
                                        <input type="date"
                                            class="form-control @error('date_of_birth') is-invalid @enderror"
                                            id="date_of_birth" name="date_of_birth"
                                            value="{{ old('date_of_birth') }}"
                                            max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                                        @error('date_of_birth')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Organization Assignment Card -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-building mr-2"></i>Organization Assignment
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="organization_id">Organization</label>
                                        <select class="form-control @error('organization_id') is-invalid @enderror"
                                            id="organization_id" name="organization_id">
                                            <option value="">Select Organization</option>
                                            @foreach ($organizations as $organization)
                                                <option value="{{ $organization->id }}"
                                                    {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
                                                    {{ $organization->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('organization_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="organization_role">Role in Organization</label>
                                        <select class="form-control @error('organization_role') is-invalid @enderror"
                                            id="organization_role" name="organization_role">
                                            <option value="">Select Role</option>
                                            <option value="owner"
                                                {{ old('organization_role') == 'owner' ? 'selected' : '' }}>Owner
                                            </option>
                                            <option value="admin"
                                                {{ old('organization_role') == 'admin' ? 'selected' : '' }}>Admin
                                            </option>
                                            <option value="member"
                                                {{ old('organization_role') == 'member' ? 'selected' : '' }}>Member
                                            </option>
                                        </select>
                                        @error('organization_role')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="position">Position/Job Title</label>
                                        <input type="text"
                                            class="form-control @error('position') is-invalid @enderror"
                                            id="position" name="position" value="{{ old('position') }}"
                                            placeholder="e.g., HR Manager, CEO">
                                        @error('position')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Settings Card -->
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cog mr-2"></i>Account Settings
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="is_active"
                                                name="is_active" value="1"
                                                {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_active">
                                                Account Active
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Enable or disable employer account
                                            access</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="is_verified"
                                                name="is_verified" value="1"
                                                {{ old('is_verified', false) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="is_verified">
                                                Verified Account
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Mark employer account as
                                            verified</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Create Employer
                            </button>
                            <button type="reset" class="btn btn-default ml-2">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </button>
                            <a href="{{ route('admin.employers.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </a>
                        </div>
                    </div>
                    </form>
                </div>

                <!-- Sidebar Info -->
                <div class="col-md-4">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>Quick Info
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-lightbulb"></i> Tips:</h5>
                                <ul class="mb-0">
                                    <li>All fields marked with <span class="text-danger">*</span> are required</li>
                                    <li>Password must be at least 8 characters</li>
                                    <li>Assigning an organization is optional</li>
                                    <li>Employers can manage vacancies and placements</li>
                                </ul>
                            </div>

                            <div class="alert alert-warning">
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Note:</h5>
                                <p class="mb-0">
                                    The employer will receive login credentials via email if they don't have one set.
                                    Make sure to provide a valid email address.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Employers Card -->
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-2"></i>Recent Employers
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @php
                                    $recentEmployers = \App\Models\User::role('employer')->latest()->take(5)->get();
                                @endphp
                                @forelse($recentEmployers as $recent)
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <div class="avatar-initials bg-secondary img-circle d-flex align-items-center justify-content-center"
                                                    style="width: 35px; height: 35px; color: white; font-size: 12px;">
                                                    {{ $recent->initials() }}
                                                </div>
                                            </div>
                                            <div>
                                                <strong>{{ $recent->full_name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    Added {{ $recent->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center text-muted py-3">
                                        <i class="fas fa-user-tie d-block mb-2" style="font-size: 24px;"></i>
                                        No employers added yet
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.password-toggle').forEach(function (button) {
                button.addEventListener('click', function () {
                    var targetSelector = this.dataset.target;
                    var passwordInput = document.querySelector(targetSelector);
                    var icon = this.querySelector('i');

                    if (!passwordInput || !icon) {
                        return;
                    }

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            $('#phone').inputmask('9999 999 999');
        });
    </script>
@endpush
