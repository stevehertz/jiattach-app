<div>
    <!-- Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create New Opportunity</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.opportunities.index') }}">Opportunities</a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <form wire:submit.prevent="saveOpportunity">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Basic Information Card -->
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Basic Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="title">Opportunity Title *</label>
                                            <input type="text" wire:model="title" id="title"
                                                class="form-control @error('title') is-invalid @enderror"
                                                placeholder="e.g., Software Development Intern">
                                            @error('title')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Line 48: Change $employers to $this->employersList -->
                                        <div class="form-group">
                                            <label for="organization_id">Organization *</label>
                                            <select wire:model="organization_id" id="organization_id"
                                                class="form-control @error('organization_id') is-invalid @enderror">
                                                <option value="">Select Organization</option>
                                                @foreach ($organizations as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>

                                            @error('organization_id')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="opportunity_type">Opportunity Type *</label>
                                            <select wire:model="opportunity_type" id="opportunity_type"
                                                class="form-control @error('opportunity_type') is-invalid @enderror">
                                                @foreach ($opportunityTypes as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('opportunity_type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="employment_type">Employment Type *</label>
                                            <select wire:model="employment_type" id="employment_type"
                                                class="form-control @error('employment_type') is-invalid @enderror">
                                                @foreach ($employmentTypes as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('employment_type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description *</label>
                                    <textarea wire:model="description" id="description" rows="5"
                                        class="form-control @error('description') is-invalid @enderror" placeholder="Describe the opportunity in detail..."></textarea>
                                    <small class="text-muted">Minimum 100 characters. Describe what the student will
                                        learn and do.</small>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="responsibilities">Responsibilities (Optional)</label>
                                    <textarea wire:model="responsibilities" id="responsibilities" rows="3" class="form-control"
                                        placeholder="List specific responsibilities..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="requirements">Requirements (Optional)</label>
                                    <textarea wire:model="requirements" id="requirements" rows="3" class="form-control"
                                        placeholder="List specific requirements..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="benefits">Benefits (Optional)</label>
                                    <textarea wire:model="benefits" id="benefits" rows="3" class="form-control"
                                        placeholder="List benefits students will receive..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Location & Duration Card -->
                        <div class="card card-success mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Location & Duration</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="duration_months">Duration (Months) *</label>
                                            <input type="number" wire:model="duration_months" id="duration_months"
                                                class="form-control @error('duration_months') is-invalid @enderror"
                                                min="1" max="24">
                                            @error('duration_months')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Work Mode</label>
                                            <div class="form-check">
                                                <input type="checkbox" wire:model="is_remote" id="is_remote"
                                                    class="form-check-input">
                                                <label class="form-check-label" for="is_remote">Remote</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" wire:model="is_hybrid" id="is_hybrid"
                                                    class="form-check-input">
                                                <label class="form-check-label" for="is_hybrid">Hybrid</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="start_date">Start Date *</label>
                                            <input type="date" wire:model="start_date" id="start_date"
                                                class="form-control @error('start_date') is-invalid @enderror">
                                            @error('start_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="end_date">End Date *</label>
                                            <input type="date" wire:model="end_date" id="end_date"
                                                class="form-control @error('end_date') is-invalid @enderror">
                                            @error('end_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="application_deadline">Application Deadline *</label>
                                            <input type="date" wire:model="application_deadline"
                                                id="application_deadline"
                                                class="form-control @error('application_deadline') is-invalid @enderror">
                                            @error('application_deadline')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                @if (!$is_remote || $is_hybrid)
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="county">County</label>
                                                <select wire:model="county" id="county" class="form-control">
                                                    <option value="">Select County</option>
                                                    @foreach ($counties as $county)
                                                        <option value="{{ $county }}">{{ $county }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="town">Town/City</label>
                                                <input type="text" wire:model="town" id="town"
                                                    class="form-control" placeholder="e.g., Nairobi">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="location">Specific Location</label>
                                                <input type="text" wire:model="location" id="location"
                                                    class="form-control" placeholder="e.g., Westlands, ABC Building">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Requirements Card -->
                        <div class="card card-warning mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Requirements & Skills</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="min_cgpa">Minimum CGPA (Optional)</label>
                                            <input type="number" wire:model="min_cgpa" id="min_cgpa"
                                                class="form-control" step="0.1" min="0" max="4"
                                                placeholder="e.g., 3.0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="min_year_of_study">Minimum Year of Study (Optional)</label>
                                            <select wire:model="min_year_of_study" id="min_year_of_study"
                                                class="form-control">
                                                <option value="">Any Year</option>
                                                <option value="1">First Year</option>
                                                <option value="2">Second Year</option>
                                                <option value="3">Third Year</option>
                                                <option value="4">Fourth Year</option>
                                                <option value="5">Fifth Year</option>
                                                <option value="6">Sixth Year</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Required Skills -->
                                <div class="form-group">
                                    <label>Required Skills</label>
                                    <div class="input-group mb-2">
                                        <input type="text" wire:model="newSkill" class="form-control"
                                            placeholder="Add a required skill">
                                        <div class="input-group-append">
                                            <button type="button" wire:click="addRequiredSkill"
                                                class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>

                                    @if (count($required_skills) > 0)
                                        <div class="mb-3">
                                            @foreach ($required_skills as $index => $skill)
                                                <span class="badge badge-primary mr-2 mb-2">
                                                    {{ $skill }}
                                                    <button type="button"
                                                        wire:click="removeRequiredSkill({{ $index }})"
                                                        class="badge badge-light ml-1" style="cursor: pointer;">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Quick add common skills -->
                                    <div class="mt-2">
                                        <small class="text-muted">Quick add:</small>
                                        @foreach (array_slice($commonSkills, 0, 10) as $skill)
                                            <button type="button"
                                                wire:click="addRequiredSkill('{{ $skill }}')"
                                                class="btn btn-xs btn-outline-secondary mr-1 mb-1">
                                                {{ $skill }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Preferred Skills -->
                                <div class="form-group">
                                    <label>Preferred Skills (Optional)</label>
                                    <div class="input-group mb-2">
                                        <input type="text" wire:model="newPreferredSkill" class="form-control"
                                            placeholder="Add a preferred skill">
                                        <div class="input-group-append">
                                            <button type="button" wire:click="addPreferredSkill"
                                                class="btn btn-secondary">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>

                                    @if (count($preferred_skills) > 0)
                                        <div class="mb-3">
                                            @foreach ($preferred_skills as $index => $skill)
                                                <span class="badge badge-secondary mr-2 mb-2">
                                                    {{ $skill }}
                                                    <button type="button"
                                                        wire:click="removePreferredSkill({{ $index }})"
                                                        class="badge badge-light ml-1" style="cursor: pointer;">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Preferred Courses -->
                                <div class="form-group">
                                    <label>Preferred Courses (Optional)</label>
                                    <div class="input-group mb-2">
                                        <input type="text" wire:model="newPreferredCourse" class="form-control"
                                            placeholder="e.g., Computer Science, Business">
                                        <div class="input-group-append">
                                            <button type="button" wire:click="addPreferredCourse"
                                                class="btn btn-info">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>

                                    @if (count($preferred_courses) > 0)
                                        <div class="mb-3">
                                            @foreach ($preferred_courses as $index => $course)
                                                <span class="badge badge-info mr-2 mb-2">
                                                    {{ $course }}
                                                    <button type="button"
                                                        wire:click="removePreferredCourse({{ $index }})"
                                                        class="badge badge-light ml-1" style="cursor: pointer;">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" wire:model="requires_portfolio" id="requires_portfolio"
                                        class="form-check-input">
                                    <label class="form-check-label" for="requires_portfolio">Requires
                                        Portfolio</label>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" wire:model="requires_cover_letter"
                                        id="requires_cover_letter" class="form-check-input">
                                    <label class="form-check-label" for="requires_cover_letter">Requires Cover
                                        Letter</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Application Details Card -->
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Application Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="slots_available">Available Slots *</label>
                                    <input type="number" wire:model="slots_available" id="slots_available"
                                        class="form-control @error('slots_available') is-invalid @enderror"
                                        min="1" value="5">
                                    @error('slots_available')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="stipend">Stipend Amount (Optional)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">KSh</span>
                                        </div>
                                        <input type="number" wire:model="stipend" id="stipend"
                                            class="form-control" placeholder="e.g., 15000" step="1000">
                                    </div>
                                </div>

                                @if ($stipend)
                                    <div class="form-group">
                                        <label for="stipend_frequency">Stipend Frequency</label>
                                        <select wire:model="stipend_frequency" id="stipend_frequency"
                                            class="form-control">
                                            <option value="monthly">Monthly</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="one-time">One-time</option>
                                            <option value="daily">Daily</option>
                                        </select>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label>Other Benefits (Optional)</label>
                                    <div class="input-group mb-2">
                                        <input type="text" id="newBenefit" class="form-control"
                                            placeholder="e.g., Lunch, Transport">
                                        <div class="input-group-append">
                                            <button type="button" onclick="addBenefit()" class="btn btn-success">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        </div>
                                    </div>

                                    @if (count($other_benefits) > 0)
                                        <div class="mb-3">
                                            @foreach ($other_benefits as $index => $benefit)
                                                <span class="badge badge-success mr-2 mb-2">
                                                    {{ $benefit }}
                                                    <button type="button"
                                                        wire:click="removeOtherBenefit({{ $index }})"
                                                        class="badge badge-light ml-1" style="cursor: pointer;">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Quick benefit suggestions -->
                                    <div class="mt-2">
                                        <small class="text-muted">Suggestions:</small>
                                        @foreach (['Lunch Provided', 'Transport Allowance', 'Flexible Hours', 'Certificate', 'Networking Events'] as $benefit)
                                            <button type="button" onclick="addBenefit('{{ $benefit }}')"
                                                class="btn btn-xs btn-outline-success mr-1 mb-1">
                                                {{ $benefit }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Save Actions Card -->
                        <div class="card card-primary mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Save Actions</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <button type="button" wire:click="saveDraft"
                                        class="btn btn-secondary btn-block mb-2">
                                        <i class="fas fa-save mr-1"></i> Save as Draft
                                    </button>

                                    <button type="button" wire:click="submitForApproval"
                                        class="btn btn-warning btn-block mb-2">
                                        <i class="fas fa-paper-plane mr-1"></i> Submit for Approval
                                    </button>

                                    <button type="button" wire:click="publishDirectly"
                                        class="btn btn-success btn-block mb-2">
                                        <i class="fas fa-check-circle mr-1"></i> Publish Directly
                                    </button>

                                    <a href="{{ route('admin.opportunities.index') }}"
                                        class="btn btn-default btn-block">
                                        <i class="fas fa-times mr-1"></i> Cancel
                                    </a>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <h6><i class="fas fa-info-circle mr-1"></i> Note:</h6>
                                    <small>
                                        • <strong>Draft:</strong> Only you can see it<br>
                                        • <strong>Pending Approval:</strong> Needs admin approval<br>
                                        • <strong>Publish:</strong> Immediately visible to students
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Card -->
                        <div class="card card-secondary mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Quick Preview</h3>
                            </div>
                            <div class="card-body">
                                <h6>{{ $title ?: 'Opportunity Title' }}</h6>
                                <p class="text-muted small mb-2">
                                    {{-- Updated to use organization_id and the organizations collection --}}
                                    @if ($organization_id && isset($organizations[$organization_id]))
                                        {{ explode(' (', $organizations[$organization_id])[0] }}
                                    @else
                                        Organization Name
                                    @endif
                                </p>

                                <div class="mb-2">
                                    @if ($opportunity_type)
                                        <span class="badge badge-info mr-1">
                                            {{ $opportunityTypes[$opportunity_type] ?? $opportunity_type }}
                                        </span>
                                    @endif

                                    @if ($duration_months)
                                        <span class="badge badge-secondary mr-1">
                                            {{ $duration_months }} month{{ $duration_months > 1 ? 's' : '' }}
                                        </span>
                                    @endif

                                    @if ($stipend)
                                        <span class="badge badge-success">
                                            KSh {{ number_format($stipend, 0) }}
                                        </span>
                                    @endif
                                </div>

                                @if ($location || $town || $county)
                                    <p class="small mb-1">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        {{ $location ?: ($town ?: ($county ?: 'Location')) }}
                                    </p>
                                @endif

                                @if ($application_deadline)
                                    <p class="small mb-1">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        Apply by: {{ \Carbon\Carbon::parse($application_deadline)->format('M d, Y') }}
                                    </p>
                                @endif

                                <p class="small text-muted mt-2 mb-0">
                                    {{ strlen($description) > 100 ? substr($description, 0, 100) . '...' : ($description ?: 'Description will appear here') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function addBenefit(benefit = null) {
                if (benefit) {
                    Livewire.dispatch('add-other-benefit', {
                        benefit: benefit
                    });
                } else {
                    const input = document.getElementById('newBenefit');
                    if (input.value.trim()) {
                        Livewire.dispatch('add-other-benefit', {
                            benefit: input.value.trim()
                        });
                        input.value = '';
                    }
                }
            }

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
            });
        </script>
    @endpush
</div>
