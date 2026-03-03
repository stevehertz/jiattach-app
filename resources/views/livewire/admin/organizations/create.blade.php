<div>
    <section class="content">
        <div class="container-fluid">
            <form wire:submit.prevent="save">
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
                    <!-- Organization Information (Left Column) -->
                    <div class="col-md-6">
                        <!-- Basic Information Card -->
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-building mr-2"></i>
                                    Organization Details
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="required">Organization Name <span class="text-danger">*</span></label>
                                    <input wire:model="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Enter organization name">
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="row">
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
                    </div>

                    <!-- Owner & Contact Information (Right Column) -->
                    <div class="col-md-6">
                        <!-- Owner Information Card -->
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-user-tie mr-2"></i>
                                    Organization Owner
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="btn-group btn-group-toggle w-100 mb-3" data-toggle="buttons">
                                        <label class="btn btn-outline-primary {{ $create_new_user ? 'active' : '' }}"
                                            wire:click="$set('create_new_user', true)">
                                            <input type="radio" name="owner_option" autocomplete="off"
                                                {{ $create_new_user ? 'checked' : '' }}> Create New User
                                        </label>
                                        <label class="btn btn-outline-primary {{ !$create_new_user ? 'active' : '' }}"
                                            wire:click="$set('create_new_user', false)">
                                            <input type="radio" name="owner_option" autocomplete="off"
                                                {{ !$create_new_user ? 'checked' : '' }}> Select Existing User
                                        </label>
                                    </div>
                                </div>

                                @if ($create_new_user)
                                    <!-- Create New User Form -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="required">First Name <span
                                                        class="text-danger">*</span></label>
                                                <input wire:model="first_name" type="text"
                                                    class="form-control @error('first_name') is-invalid @enderror"
                                                    placeholder="John">
                                                @error('first_name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="required">Last Name <span
                                                        class="text-danger">*</span></label>
                                                <input wire:model="last_name" type="text"
                                                    class="form-control @error('last_name') is-invalid @enderror"
                                                    placeholder="Doe">
                                                @error('last_name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="required">Email Address <span
                                                class="text-danger">*</span></label>
                                        <input wire:model="owner_email" type="email"
                                            class="form-control @error('owner_email') is-invalid @enderror"
                                            placeholder="owner@organization.com">
                                        @error('owner_email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="required">Password <span
                                                        class="text-danger">*</span></label>
                                                <input wire:model="password" type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="********">
                                                @error('password')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="required">Confirm Password <span
                                                        class="text-danger">*</span></label>
                                                <input wire:model="password_confirmation" type="password"
                                                    class="form-control" placeholder="********">
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Select Existing User -->
                                    <div class="form-group">
                                        <label class="required">Search and Select User <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input wire:model.live.debounce.300ms="search_user" type="text"
                                                class="form-control @error('selected_user_id') is-invalid @enderror"
                                                placeholder="Search by name or email..."
                                                wire:focus="$set('show_user_search', true)">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button"
                                                    wire:click="$set('show_user_search', true)">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @error('selected_user_id')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror

                                        @if ($show_user_search && !empty($search_results))
                                            <div class="list-group mt-2" style="max-height: 200px; overflow-y: auto;">
                                                @foreach ($search_results as $user)
                                                    <a href="#"
                                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                                        wire:click.prevent="selectUser({{ $user['id'] }})">
                                                        <div>
                                                            <strong>{{ $user['first_name'] }}
                                                                {{ $user['last_name'] }}</strong><br>
                                                            <small>{{ $user['email'] }}</small>
                                                        </div>
                                                        <i class="fas fa-plus-circle text-success"></i>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($selected_user_id)
                                            @php
                                                $selectedUser = \App\Models\User::find($selected_user_id);
                                            @endphp
                                            @if ($selectedUser)
                                                <div class="alert alert-success mt-2 mb-0">
                                                    <i class="fas fa-check-circle"></i>
                                                    Selected: <strong>{{ $selectedUser->full_name }}</strong>
                                                    ({{ $selectedUser->email }})
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Contact Person Card -->
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-address-card mr-2"></i>
                                    Contact Person (Optional)
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

                        <!-- Description & Capacity Card -->
                        <div class="card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Additional Information
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                        placeholder="Provide a brief description of the organization..."></textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

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
                                    <a href="{{ route('admin.organizations.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times mr-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i> Create Organization
                                    </button>
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
