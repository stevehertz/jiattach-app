<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    <form wire:submit="update">
        <div class="row">
            <div class="col-md-8">
                <!-- Basic Information Card -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building mr-2"></i>Basic Information
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">
                                        Organization Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" wire:model.live="name" placeholder="Enter organization name">
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">
                                        Organization Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('type') is-invalid @enderror" id="type"
                                        wire:model.live="type">
                                        <option value="">Select Type</option>
                                        @foreach ($organizationTypes as $type)
                                            <option value="{{ $type }}">{{ $type }}</option>
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
                                    <label for="industry">
                                        Industry <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('industry') is-invalid @enderror" id="industry"
                                        wire:model.live="industry">
                                        <option value="">Select Industry</option>
                                        @foreach ($industryOptions as $industry)
                                            <option value="{{ $industry }}">{{ $industry }}</option>
                                        @endforeach
                                    </select>
                                    @error('industry')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_students_per_intake">
                                        Max Students Per Intake <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                        class="form-control @error('max_students_per_intake') is-invalid @enderror"
                                        id="max_students_per_intake" wire:model.live="max_students_per_intake"
                                        min="1" max="500" placeholder="e.g., 10">
                                    @error('max_students_per_intake')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">
                                        How many students can your organization host per intake?
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" wire:model.live="description"
                                rows="4" placeholder="Describe your organization, its mission, and what students can expect..."></textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">
                                Maximum 2000 characters. Provide a brief overview of your organization.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Card -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-address-book mr-2"></i>Contact Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">
                                        Organization Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" wire:model.live="email" placeholder="info@organization.com">
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">
                                        Phone Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" wire:model.live="phone" placeholder="+254 700 000 000">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="website">Website</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror"
                                id="website" wire:model.live="website" placeholder="https://www.organization.com">
                            @error('website')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address">Physical Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" wire:model.live="address"
                                rows="2" placeholder="P.O. Box / Street Address / Building"></textarea>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Location Card -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-map-marker-alt mr-2"></i>Location
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="county">
                                        County <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('county') is-invalid @enderror" id="county"
                                        wire:model.live="county">
                                        <option value="">Select County</option>
                                        @foreach ($counties as $county)
                                            <option value="{{ $county }}">{{ $county }}</option>
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
                                        id="constituency" wire:model.live="constituency"
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
                                        id="ward" wire:model.live="ward" placeholder="Enter ward">
                                    @error('ward')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Person Card -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-tie mr-2"></i>Primary Contact Person
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person_name">
                                        Full Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control @error('contact_person_name') is-invalid @enderror"
                                        id="contact_person_name" wire:model.live="contact_person_name"
                                        placeholder="Enter contact person's full name">
                                    @error('contact_person_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person_position">
                                        Position/Title <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control @error('contact_person_position') is-invalid @enderror"
                                        id="contact_person_position" wire:model.live="contact_person_position"
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
                                    <label for="contact_person_email">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                        class="form-control @error('contact_person_email') is-invalid @enderror"
                                        id="contact_person_email" wire:model.live="contact_person_email"
                                        placeholder="contact.person@organization.com">
                                    @error('contact_person_email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person_phone">
                                        Phone <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control @error('contact_person_phone') is-invalid @enderror"
                                        id="contact_person_phone" wire:model.live="contact_person_phone"
                                        placeholder="+254 700 000 000">
                                    @error('contact_person_phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Departments Card -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-sitemap mr-2"></i>Departments
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Existing Departments List -->
                        @if (count($departments) > 0)
                            <div class="mb-4">
                                <h5>Current Departments</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Department Name</th>
                                                <th>Description</th>
                                                <th>Head</th>
                                                <th width="100px">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($departments as $index => $department)
                                                <tr>
                                                    <td>{{ $department['name'] }}</td>
                                                    <td>{{ $department['description'] ?? 'N/A' }}</td>
                                                    <td>{{ $department['head'] ?? 'N/A' }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            wire:click="removeDepartment({{ $index }})">
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

                        <!-- Add New Department -->
                        <div class="border-top pt-3">
                            <h5>
                                <i class="fas fa-plus-circle mr-2 text-success"></i>
                                Add New Department
                            </h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="newDepartmentName">Department Name</label>
                                        <input type="text" class="form-control" id="newDepartmentName"
                                            wire:model="newDepartmentName" placeholder="e.g., Finance">
                                        @error('newDepartmentName')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="newDepartmentHead">Department Head</label>
                                        <input type="text" class="form-control" id="newDepartmentHead"
                                            wire:model="newDepartmentHead" placeholder="e.g., Jane Doe">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="newDepartmentDescription">Description</label>
                                        <input type="text" class="form-control" id="newDepartmentDescription"
                                            wire:model="newDepartmentDescription" placeholder="Brief description">
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success" wire:click="addDepartment">
                                <i class="fas fa-plus mr-1"></i> Add Department
                            </button>
                            <small class="text-muted ml-2">
                                Departments will be saved when you update the organization.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="update">
                                <i class="fas fa-save mr-1"></i> Update Organization
                            </span>
                            <span wire:loading wire:target="update">
                                <i class="fas fa-spinner fa-spin mr-1"></i> Saving Changes...
                            </span>
                        </button>

                        <a href="{{ route('employer.organization.profile') }}" class="btn btn-default btn-lg ml-2">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </a>

                        <button type="reset" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-undo mr-1"></i> Reset Form
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Organization Preview Card -->
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-eye mr-2"></i>Preview
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="bg-success d-flex align-items-center justify-content-center mx-auto mb-3"
                                style="width: 80px; height: 80px; border-radius: 10px;">
                                <span style="font-size: 32px; color: white;">
                                    {{ strtoupper(substr($name ?: 'ORG', 0, 2)) }}
                                </span>
                            </div>
                            <h5>{{ $name ?: 'Organization Name' }}</h5>
                            @if ($industry)
                                <span class="badge badge-info">{{ $industry }}</span>
                            @endif
                        </div>

                        <ul class="list-group list-group-flush mb-3">
                            @if ($email)
                                <li class="list-group-item px-0">
                                    <i class="fas fa-envelope mr-2 text-muted"></i>
                                    {{ $email }}
                                </li>
                            @endif
                            @if ($phone)
                                <li class="list-group-item px-0">
                                    <i class="fas fa-phone mr-2 text-muted"></i>
                                    {{ $phone }}
                                </li>
                            @endif
                            @if ($county)
                                <li class="list-group-item px-0">
                                    <i class="fas fa-map-marker-alt mr-2 text-muted"></i>
                                    {{ $county }}
                                </li>
                            @endif
                            @if ($contact_person_name)
                                <li class="list-group-item px-0">
                                    <i class="fas fa-user mr-2 text-muted"></i>
                                    {{ $contact_person_name }}
                                </li>
                            @endif
                        </ul>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i>
                            This is a live preview of your organization details.
                        </div>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-lightbulb mr-2"></i>Tips
                        </h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-success mr-2"></i>
                                Complete all required fields marked with <span class="text-danger">*</span>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-success mr-2"></i>
                                Provide accurate contact information for students
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-success mr-2"></i>
                                Add all departments that can host students
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-success mr-2"></i>
                                Keep your organization description up-to-date
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-success mr-2"></i>
                                Set realistic student capacity numbers
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Important Note
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">
                            Your organization profile will be visible to students seeking attachments.
                            Ensure all information is accurate and professional.
                        </p>
                        <p class="text-muted small mb-0">
                            Changes to your organization details may require re-verification by administrators.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('notify', (data) => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });

                    Toast.fire({
                        icon: data.type || 'success',
                        title: data.message || 'Action completed'
                    });
                });
            });
        </script>
    @endpush

</div>
