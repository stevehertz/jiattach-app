<x-layouts.app>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Employer</h1>
                    <ol class="breadcrumb text-sm">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.employers.index') }}">Employers</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.employers.show', $employer) }}">{{ $employer->full_name }}</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-right">
                        <a href="{{ route('admin.employers.show', $employer) }}" class="btn btn-info">
                            <i class="fas fa-eye mr-1"></i> View Profile
                        </a>
                        <a href="{{ route('admin.employers.index') }}" class="btn btn-default ml-2">
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
                    <form action="{{ route('admin.employers.update', $employer) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Personal Information Card -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-user-tie mr-2"></i>Personal Information
                                </h3>
                                <div class="card-tools">
                                    <span class="badge badge-{{ $employer->is_active ? 'success' : 'danger' }} ml-2">
                                        {{ $employer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span class="badge badge-{{ $employer->is_verified ? 'success' : 'warning' }} ml-2">
                                        {{ $employer->is_verified ? 'Verified' : 'Unverified' }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                id="first_name" name="first_name"
                                                value="{{ old('first_name', $employer->first_name) }}" required>
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
                                                id="last_name" name="last_name"
                                                value="{{ old('last_name', $employer->last_name) }}" required>
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
                                                name="email" value="{{ old('email', $employer->email) }}" required>
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
                                                name="phone"
                                                value="{{ old('phone', preg_replace('/(\d{4})(\d{3})(\d{3})/', '$1 $2 $3', $employer->phone)) }}">
                                            @error('phone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">New Password</label>
                                            <div class="input-group">
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password" name="password"
                                                    placeholder="Leave blank to keep current">
                                                <div class="input-group-append">
                                                    <button type="button" class="input-group-text password-toggle"
                                                        data-target="#password" aria-label="Toggle password visibility">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Leave blank to keep current password</small>
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
                                                <option value="male"
                                                    {{ old('gender', $employer->gender) == 'male' ? 'selected' : '' }}>
                                                    Male</option>
                                                <option value="female"
                                                    {{ old('gender', $employer->gender) == 'female' ? 'selected' : '' }}>
                                                    Female</option>
                                                <option value="other"
                                                    {{ old('gender', $employer->gender) == 'other' ? 'selected' : '' }}>
                                                    Other</option>
                                            </select>
                                            @error('gender')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="bio">Bio</label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3"
                                        placeholder="Brief description about the employer">{{ old('bio', $employer->bio) }}</textarea>
                                    @error('bio')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
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
                                                        {{ old('county', $employer->county) == $county ? 'selected' : '' }}>
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
                                                id="constituency" name="constituency"
                                                value="{{ old('constituency', $employer->constituency) }}">
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
                                            <input type="text"
                                                class="form-control @error('ward') is-invalid @enderror"
                                                id="ward" name="ward"
                                                value="{{ old('ward', $employer->ward) }}">
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
                                                value="{{ old('date_of_birth', optional($employer->date_of_birth)->format('Y-m-d')) }}">
                                            @error('date_of_birth')
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
                                                    {{ old('is_active', $employer->is_active) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_active">
                                                    Account Active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="is_verified"
                                                    name="is_verified" value="1"
                                                    {{ old('is_verified', $employer->is_verified) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_verified">
                                                    Verified Account
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Update Employer
                                </button>
                                <a href="{{ route('admin.employers.show', $employer) }}"
                                    class="btn btn-default ml-2">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </a>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Profile Card -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                @if ($employer->profile_photo_path)
                                    <img class="profile-user-img img-fluid img-circle"
                                        src="{{ $employer->profile_photo_url }}" alt="{{ $employer->full_name }}">
                                @else
                                    <div class="avatar-initials bg-info img-circle d-flex align-items-center justify-content-center mx-auto"
                                        style="width: 100px; height: 100px; color: white; font-size: 36px;">
                                        {{ $employer->initials() }}
                                    </div>
                                @endif
                                <h3 class="profile-username text-center mt-2">{{ $employer->full_name }}</h3>
                                <p class="text-muted text-center">{{ $employer->email }}</p>
                            </div>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Organizations</b>
                                    <span class="float-right">{{ $employer->organizations->count() }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Member Since</b>
                                    <span class="float-right">{{ $employer->created_at->format('M d, Y') }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Last Login</b>
                                    <span class="float-right">
                                        {{ $employer->last_login_at ? $employer->last_login_at->diffForHumans() : 'Never' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Danger Zone
                            </h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">
                                Deleting this employer will remove all their associations and cannot be undone.
                            </p>
                            <form action="{{ route('admin.employers.destroy', $employer) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this employer? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash mr-1"></i> Delete Employer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.password-toggle').forEach(function(button) {

                button.addEventListener('click', function() {

                    const inputGroup = this.closest('.input-group');
                    const passwordInput = inputGroup.querySelector('input');
                    const icon = this.querySelector('i');

                    if (!passwordInput || !icon) return;

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
        });
    </script>
@endpush
