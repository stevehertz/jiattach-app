<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
      <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- Progress Steps -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Create Administrator (Step {{ $currentStep }} of
                                {{ $totalSteps }})</h3>
                        </div>
                        <div class="card-body">
                            <!-- Progress Bar -->
                            <div class="progress mb-4" style="height: 20px;">
                                <div class="progress-bar bg-success progress-bar-striped" role="progressbar"
                                    style="width: {{ ($currentStep / $totalSteps) * 100 }}%">
                                    Step {{ $currentStep }} of {{ $totalSteps }}
                                </div>
                            </div>

                            <!-- Step Indicators -->
                            <div class="d-flex justify-content-between mb-4">
                                <div class="text-center">
                                    <span class="badge badge-{{ $currentStep >= 1 ? 'success' : 'secondary' }} p-2">
                                        1. Basic Info
                                    </span>
                                </div>
                                <div class="text-center">
                                    <span class="badge badge-{{ $currentStep >= 2 ? 'success' : 'secondary' }} p-2">
                                        2. Account Details
                                    </span>
                                </div>
                                <div class="text-center">
                                    <span class="badge badge-{{ $currentStep >= 3 ? 'success' : 'secondary' }} p-2">
                                        3. Role Assignment
                                    </span>
                                </div>
                            </div>

                            <!-- Step 1: Basic Information -->
                            @if ($currentStep == 1)
                                <div class="step-content">
                                    <h4 class="mb-3">Basic Information</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="first_name">First Name *</label>
                                                <input type="text" id="first_name"
                                                    class="form-control @error('first_name') is-invalid @enderror"
                                                    wire:model="first_name" placeholder="Enter first name" required>
                                                @error('first_name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="last_name">Last Name *</label>
                                                <input type="text" id="last_name"
                                                    class="form-control @error('last_name') is-invalid @enderror"
                                                    wire:model="last_name" placeholder="Enter last name" required>
                                                @error('last_name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email Address *</label>
                                                <input type="email" id="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    wire:model="email" placeholder="Enter email address" required>
                                                @error('email')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Phone Number *</label>
                                                <input type="text" id="phone"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    wire:model="phone" placeholder="e.g. 0712 345 678" required>
                                                @error('phone')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="national_id">National ID *</label>
                                                <input type="text" id="national_id"
                                                    class="form-control @error('national_id') is-invalid @enderror"
                                                    wire:model="national_id" placeholder="Enter national ID" required>
                                                @error('national_id')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date_of_birth">Date of Birth *</label>
                                                <input type="date" id="date_of_birth"
                                                    class="form-control @error('date_of_birth') is-invalid @enderror"
                                                    wire:model="date_of_birth" max="{{ date('Y-m-d') }}" required>
                                                @error('date_of_birth')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="gender">Gender *</label>
                                                <select id="gender"
                                                    class="form-control @error('gender') is-invalid @enderror"
                                                    wire:model="gender" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                                @error('gender')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Step 2: Account Details -->
                            @if ($currentStep == 2)
                                <div class="step-content">
                                    <h4 class="mb-3">Account Details</h4>

                                    <!-- Password Section -->
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h5 class="card-title">Password Settings</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="password">Password *</label>
                                                        <div class="input-group">
                                                            <input type="password" id="password"
                                                                class="form-control @error('password') is-invalid @enderror"
                                                                wire:model="password" placeholder="Enter password"
                                                                required>
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary"
                                                                    type="button"
                                                                    onclick="togglePassword('password', this)">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        @error('password')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>&nbsp;</label>
                                                    <button type="button" class="btn btn-outline-info btn-block"
                                                        wire:click="generatePassword">
                                                        <i class="fas fa-key"></i> Generate Strong Password
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="password_confirmation">Confirm Password *</label>
                                                <div class="input-group">
                                                    <input type="password" id="password_confirmation"
                                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                                        wire:model="password_confirmation"
                                                        placeholder="Confirm password" required>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            onclick="togglePassword('password_confirmation', this)">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    id="force_password_change" wire:model="force_password_change"
                                                    checked>
                                                <label class="form-check-label" for="force_password_change">
                                                    Force password change on first login
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location Information -->
                                    <div class="card card-secondary mt-3">
                                        <div class="card-header">
                                            <h5 class="card-title">Location Information</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="county">County *</label>
                                                        <select id="county"
                                                            class="form-control @error('county') is-invalid @enderror"
                                                            wire:model="county" required>
                                                            <option value="">Select County</option>
                                                            @foreach ($counties as $county)
                                                                <option value="{{ $county }}">
                                                                    {{ $county }}</option>
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
                                                        <input type="text" id="constituency"
                                                            class="form-control @error('constituency') is-invalid @enderror"
                                                            wire:model="constituency"
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
                                                        <input type="text" id="ward"
                                                            class="form-control @error('ward') is-invalid @enderror"
                                                            wire:model="ward" placeholder="Enter ward">
                                                        @error('ward')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="bio">Bio / Description</label>
                                                <textarea id="bio" class="form-control @error('bio') is-invalid @enderror" wire:model="bio" rows="3"
                                                    placeholder="Brief description about this administrator"></textarea>
                                                @error('bio')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Step 3: Role Assignment -->
                            @if ($currentStep == 3)
                                <div class="step-content">
                                    <h4 class="mb-3">Role Assignment & Finalization</h4>

                                    <!-- Role Selection -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h5 class="card-title">Assign Roles *</h5>
                                        </div>
                                        <div class="card-body">
                                            @error('roles')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror

                                            @forelse ($availableRoles as $role)
                                                @php
                                                    // Handle both array and object
                                                    $roleName = is_array($role) ? $role['name'] : $role->name;
                                                    $roleId = is_array($role) ? $role['id'] : $role->id;
                                                    
                                                    $roleDescription = match ($roleName) {
                                                        'super-admin'
                                                            => 'Full system access, can manage all administrators and system settings.',
                                                        'admin'
                                                            => 'Full access to manage users, opportunities, and mentorship programs.',
                                                        'moderator'
                                                            => 'Limited access for content moderation and user management.',
                                                        default => 'Administrator role'
                                                    };
                                                    $badgeColor = match ($roleName) {
                                                        'super-admin' => 'danger',
                                                        'admin' => 'success',
                                                        'moderator' => 'info',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <div class="custom-control custom-checkbox mb-3">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="role_{{ $roleId }}" wire:model.live="roles"
                                                        value="{{ $roleName }}">
                                                    <label class="custom-control-label d-flex align-items-start"
                                                        for="role_{{ $roleId }}">
                                                        <div class="mr-2">
                                                            <span class="badge badge-{{ $badgeColor }}">
                                                                {{ ucfirst(str_replace('-', ' ', $roleName)) }}
                                                            </span>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <small
                                                                class="text-muted d-block">{{ $roleDescription }}</small>
                                                        </div>
                                                    </label>
                                                </div>
                                            @empty
                                                <div class="alert alert-warning">
                                                    No roles available. Please contact your system administrator.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <!-- Final Settings -->
                                    <div class="card card-success mt-3">
                                        <div class="card-header">
                                            <h5 class="card-title">Final Settings</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="is_active" wire:model="is_active" checked>
                                                        <label class="form-check-label" for="is_active">
                                                            <strong>Active Account</strong><br>
                                                            <small class="text-muted">User can login
                                                                immediately</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="send_welcome_email" wire:model="send_welcome_email">
                                                        <label class="form-check-label" for="send_welcome_email">
                                                            <strong>Send Welcome Email</strong><br>
                                                            <small class="text-muted">Send login credentials via
                                                                email</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Review Summary -->
                                    <div class="card card-primary mt-3">
                                        <div class="card-header">
                                            <h5 class="card-title">Review Summary</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Name:</strong> {{ $first_name }} {{ $last_name }}
                                                    </p>
                                                    <p><strong>Email:</strong> {{ $email }}</p>
                                                    <p><strong>Phone:</strong> {{ $phone }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>National ID:</strong> {{ $national_id }}</p>
                                                    <p><strong>County:</strong> {{ $county }}</p>
                                                    <p><strong>Status:</strong>
                                                        {{ $is_active ? 'Active' : 'Inactive' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Navigation Buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                @if ($currentStep > 1)
                                    <button type="button" class="btn btn-secondary" wire:click="previousStep">
                                        <i class="fas fa-arrow-left"></i> Previous
                                    </button>
                                @else
                                    <a href="{{ route('admin.administrators.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                @endif

                                @if ($currentStep < $totalSteps)
                                    <button type="button" class="btn btn-primary" wire:click="nextStep">
                                        Next <i class="fas fa-arrow-right"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-success" wire:click="save">
                                        <i class="fas fa-save"></i> Create Administrator
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                All fields marked with * are required. Administrator accounts are automatically
                                verified.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            function togglePassword(id, btn) {
                const input = document.getElementById(id);
                if (!input) return;
                const icon = btn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    if (icon) {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }
                } else {
                    input.type = 'password';
                    if (icon) {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                }
            }

            document.addEventListener('livewire:load', function () {
                Livewire.hook('message.processed', (message, component) => {
                    // Re-bind any dynamic UI if necessary (icons remain toggled by user action)
                });
            });
        </script>
    @endpush

</div>
