<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <!-- Profile Card -->
                    <div class="card card-primary">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                @php
                                    $initials = getInitials($user->full_name);
                                    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                                    $color = $colors[crc32($user->email) % count($colors)];
                                @endphp
                                <div class="avatar-initials bg-{{ $color }} img-circle elevation-2"
                                    style="width: 100px; height: 100px; line-height: 100px; text-align: center; color: white; font-weight: bold; font-size: 36px; margin: 0 auto 15px;">
                                    {{ $initials }}
                                </div>
                            </div>

                            <h3 class="profile-username text-center">{{ $user->full_name }}</h3>

                            <p class="text-muted text-center">
                                @foreach ($user->roles as $role)
                                    @php
                                        $roleColors = [
                                            'admin' => 'danger',
                                            'student' => 'success',
                                            'employer' => 'primary',
                                            'mentor' => 'warning',
                                        ];
                                        $color = $roleColors[$role->name] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $color }}">{{ ucfirst($role->name) }}</span>
                                @endforeach
                            </p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Email</b>
                                    <a class="float-right">{{ $user->email }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Phone</b>
                                    <span class="float-right">
                                        {{ $user->phone ? formatPhoneNumber($user->phone) : 'N/A' }}
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>User Type</b>
                                    <span class="float-right text-capitalize">{{ $user->user_type ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Status</b>
                                    <span class="float-right">
                                        @if ($user->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif

                                        @if ($user->is_verified)
                                            <span class="badge badge-info ml-1">Verified</span>
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Joined</b>
                                    <span class="float-right">{{ formatDateTime($user->created_at) }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Last Login</b>
                                    <span class="float-right">
                                        {{ $user->last_login_at ? formatDateTime($user->last_login_at) : 'Never' }}
                                    </span>
                                </li>
                            </ul>

                            <div class="row">
                                <div class="col-12">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-default btn-block">
                                        <i class="fas fa-arrow-left mr-1"></i> Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            @if (!$user->hasRole('super-admin') && $user->id !== Auth::id())
                                @if ($user->is_active)
                                    <button wire:click="toggleActive" class="btn btn-warning btn-block mb-2">
                                        <i class="fas fa-ban mr-1"></i> Deactivate User
                                    </button>
                                @else
                                    <button wire:click="toggleActive" class="btn btn-success btn-block mb-2">
                                        <i class="fas fa-check mr-1"></i> Activate User
                                    </button>
                                @endif

                                @if (!$user->is_verified)
                                    <button wire:click="verifyUser" class="btn btn-info btn-block mb-2">
                                        <i class="fas fa-check-circle mr-1"></i> Verify Account
                                    </button>
                                @endif

                                @if (!$hasRelatedRecords)
                                    <button wire:click="confirmDelete" class="btn btn-danger btn-block">
                                        <i class="fas fa-trash mr-1"></i> Delete User
                                    </button>
                                @else
                                    <button class="btn btn-danger btn-block" disabled title="User has related records">
                                        <i class="fas fa-trash mr-1"></i> Delete User
                                    </button>
                                    <small class="text-danger mt-1 d-block">
                                        <i class="fas fa-info-circle mr-1"></i> Cannot delete - user has related records
                                    </small>
                                @endif
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    <strong>Protected Account</strong>
                                    <p class="mb-0 mt-2 small">
                                        @if ($user->hasRole('super-admin'))
                                            Super-admin accounts cannot be modified
                                        @else
                                            You cannot modify your own account here
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Additional Info Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Additional Information</h3>
                        </div>
                        <div class="card-body">
                            @if ($user->studentProfile)
                                <div class="callout callout-success">
                                    <h5><i class="fas fa-graduation-cap mr-1"></i> Student Profile</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Registration Number:</strong><br>
                                                {{ $user->studentProfile->student_reg_number ?? 'N/A' }}</p>

                                            <p><strong>Institution:</strong><br>
                                                {{ $user->studentProfile->institution_name ?? 'N/A' }}</p>

                                            <p><strong>Institution Type:</strong><br>
                                                {{ $user->studentProfile->institution_type_label ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Course:</strong><br>
                                                {{ $user->studentProfile->full_course_name ?? 'N/A' }}</p>

                                            <p><strong>Year of Study:</strong><br>
                                                {{ $user->studentProfile->year_of_study ?? 'N/A' }}</p>

                                            <p><strong>Expected Graduation:</strong><br>
                                                {{ $user->studentProfile->expected_graduation_year ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <p><strong>CGPA:</strong> {{ $user->studentProfile->cgpa ?? 'N/A' }}</p>
                                    <p><strong>Attachment Status:</strong>
                                        <span
                                            class="badge badge-{{ $user->studentProfile->attachment_status === 'placed' ? 'success' : 'info' }}">
                                            {{ $user->studentProfile->attachment_status_label ?? 'N/A' }}
                                        </span>
                                    </p>

                                    @if ($user->studentProfile->skills && is_array($user->studentProfile->skills))
                                        <p><strong>Skills:</strong><br>
                                            @foreach ($user->studentProfile->skills as $skill)
                                                <span
                                                    class="badge badge-secondary mr-1 mb-1">{{ $skill }}</span>
                                            @endforeach
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if ($user->employer)
                                <div class="callout callout-primary">
                                    <h5><i class="fas fa-building mr-1"></i> Employer Profile</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Company Name:</strong><br>
                                                {{ $user->employer->company_name ?? 'N/A' }}</p>

                                            <p><strong>Email:</strong><br>
                                                {{ $user->employer->company_email ?? 'N/A' }}</p>

                                            <p><strong>Phone:</strong><br>
                                                {{ $user->employer->company_phone ? formatPhoneNumber($user->employer->company_phone) : 'N/A' }}
                                            </p>

                                            <p><strong>Registration Number:</strong><br>
                                                {{ $user->employer->company_registration_number ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Industry:</strong><br>
                                                {{ $user->employer->industry ?? 'N/A' }}</p>

                                            <p><strong>Company Type:</strong><br>
                                                {{ $user->employer->company_type ?? 'N/A' }}</p>

                                            <p><strong>Company Size:</strong><br>
                                                {{ $user->employer->company_size ?? 'N/A' }}</p>

                                            <p><strong>KRA PIN:</strong><br>
                                                {{ $user->employer->kra_pin ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <p><strong>Location:</strong> {{ $user->employer->town ?? 'N/A' }},
                                        {{ $user->employer->county ?? 'N/A' }}</p>
                                    <p><strong>Physical Address:</strong>
                                        {{ $user->employer->physical_address ?? 'N/A' }}</p>
                                    <p><strong>Status:</strong>
                                        @if ($user->employer->is_verified)
                                            <span class="badge badge-success">Verified</span>
                                        @else
                                            <span class="badge badge-warning">Unverified</span>
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if ($user->mentor)
                                <div class="callout callout-warning">
                                    <h5><i class="fas fa-user-tie mr-1"></i> Mentor Profile</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Job Title:</strong><br>
                                                {{ $user->mentor->job_title ?? 'N/A' }}</p>

                                            <p><strong>Company:</strong><br>
                                                {{ $user->mentor->company ?? 'N/A' }}</p>

                                            <p><strong>Experience:</strong><br>
                                                {{ $user->mentor->years_of_experience ?? 'N/A' }} years</p>

                                            <p><strong>Mentoring Focus:</strong><br>
                                                {{ $user->mentor->mentoring_focus_label ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Availability:</strong><br>
                                                {{ $user->mentor->availability_label ?? 'N/A' }}</p>

                                            <p><strong>Current Mentees:</strong><br>
                                                {{ $user->mentor->current_mentees ?? '0' }} of
                                                {{ $user->mentor->max_mentees ?? '0' }}</p>

                                            <p><strong>Hourly Rate:</strong><br>
                                                {{ $user->mentor->hourly_rate_formatted ?? 'Free' }}</p>

                                            <p><strong>Rating:</strong><br>
                                                {{ $user->mentor->average_rating ? getRatingStars($user->mentor->average_rating) : 'No ratings yet' }}
                                            </p>
                                        </div>
                                    </div>

                                    @if ($user->mentor->areas_of_expertise && is_array($user->mentor->areas_of_expertise))
                                        <p><strong>Expertise:</strong><br>
                                            @foreach ($user->mentor->areas_of_expertise as $expertise)
                                                <span class="badge badge-info mr-1 mb-1">{{ $expertise }}</span>
                                            @endforeach
                                        </p>
                                    @endif

                                    @if ($user->mentor->industries && is_array($user->mentor->industries))
                                        <p><strong>Industries:</strong><br>
                                            @foreach ($user->mentor->industries as $industry)
                                                <span
                                                    class="badge badge-secondary mr-1 mb-1">{{ $industry }}</span>
                                            @endforeach
                                        </p>
                                    @endif

                                    <p><strong>Status:</strong>
                                        @if ($user->mentor->is_verified)
                                            <span class="badge badge-success">Verified</span>
                                        @else
                                            <span class="badge badge-warning">Unverified</span>
                                        @endif

                                        @if ($user->mentor->is_featured)
                                            <span class="badge badge-primary ml-1">Featured</span>
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if (!$user->studentProfile && !$user->employer && !$user->mentor)
                                <div class="callout callout-info">
                                    <h5><i class="fas fa-info-circle mr-1"></i> No Additional Profile</h5>
                                    <p>This user doesn't have a specialized profile (student, employer, or mentor).</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- System Info Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">System Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>User ID:</strong> {{ $user->id }}</p>
                                    <p><strong>Email Verified:</strong>
                                        @if ($user->email_verified_at)
                                            <span class="badge badge-success">Yes -
                                                {{ formatDate($user->email_verified_at) }}</span>
                                        @else
                                            <span class="badge badge-warning">No</span>
                                        @endif
                                    </p>
                                    <p><strong>Two-Factor Enabled:</strong>
                                        @if ($user->two_factor_secret)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Account Created:</strong> {{ formatDateTime($user->created_at) }}</p>
                                    <p><strong>Last Updated:</strong> {{ formatDateTime($user->updated_at) }}</p>
                                    <p><strong>Profile Picture:</strong>
                                        @if ($user->profile)
                                            <span class="badge badge-success">Uploaded</span>
                                        @else
                                            <span class="badge badge-secondary">Not uploaded</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Delete User: {{ $user->full_name }}
                        </h5>
                        <button type="button" class="close text-white" wire:click="resetDeleteModal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <strong>Warning:</strong> You are about to delete this user permanently.
                        </div>

                        <!-- User details -->
                        <div class="user-info mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3">
                                    @php
                                        $initials = getInitials($user->full_name);
                                        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                                        $color = $colors[crc32($user->email) % count($colors)];
                                    @endphp
                                    <div class="avatar-initials bg-{{ $color }} img-circle"
                                        style="width: 50px; height: 50px; line-height: 50px; text-align: center; color: white; font-weight: bold; font-size: 18px;">
                                        {{ $initials }}
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $user->full_name }}</h6>
                                    <small class="text-muted">{{ $user->email }}</small>
                                    <div class="mt-1">
                                        @foreach ($user->roles as $role)
                                            <span class="badge badge-primary">{{ ucfirst($role->name) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p>This action will:</p>
                        <ul>
                            <li>Permanently delete the user account</li>
                            <li>Remove all associated data</li>
                            <li>Cannot be undone</li>
                        </ul>

                        <!-- Confirmation input -->
                        <div class="form-group">
                            <label for="deleteConfirmation">
                                Type <strong>DELETE</strong> to confirm:
                            </label>
                            <input type="text" wire:model.live="deleteConfirmation" id="deleteConfirmation"
                                class="form-control {{ strtoupper(trim($deleteConfirmation)) === 'DELETE' ? 'is-valid' : ($deleteConfirmation ? 'is-invalid' : '') }}"
                                placeholder="Type DELETE here">

                            @if ($deleteConfirmation && strtoupper(trim($deleteConfirmation)) !== 'DELETE')
                                <div class="invalid-feedback">
                                    Please type exactly "DELETE" (case-insensitive)
                                </div>
                            @elseif(strtoupper(trim($deleteConfirmation)) === 'DELETE')
                                <div class="valid-feedback">
                                    ✓ Confirmed! Click Delete User to proceed.
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="resetDeleteModal">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </button>

                        <button type="button" class="btn btn-danger" wire:click="deleteUser"
                            {{ !$canDelete ? 'disabled' : '' }}>
                            <i class="fas fa-trash mr-1"></i> Delete User
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif


    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                // Toast notification handler
                Livewire.on('show-toast', (event) => {
                    toastr[event.type](event.message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000
                    });
                });

                // Focus on confirmation input when modal opens
                Livewire.on('showDeleteModal', () => {
                    setTimeout(() => {
                        const input = document.getElementById('deleteConfirmation');
                        if (input) {
                            input.focus();
                        }
                    }, 100);
                });
            });
        </script>
    @endpush

</div>
