<div>
    {{-- Because she competes with no one, no one can compete with her. --}}

    <div class="content">
        <div class="container-fluid">

            <!-- Edit Mode Indicator -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="callout callout-info">
                        <h5><i class="fas fa-edit mr-2"></i>Editing Mode</h5>
                        <p class="mb-0">You are editing <strong>{{ $opportunity->title }}</strong>.
                            Created {{ $opportunity->created_at->diffForHumans() }} •
                            Last updated {{ $opportunity->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body pt-3 pb-2">
                            <div class="d-flex justify-content-between align-items-center position-relative">
                                <div class="position-absolute"
                                    style="top: 20px; left: 10%; right: 10%; height: 2px; background: #e9ecef; z-index: 0;">
                                </div>
                                <div class="position-absolute"
                                    style="top: 20px; left: 10%; height: 2px; background: #28a745; z-index: 0; transition: width 0.3s; width: {{ match ($activeTab) {'basic' => '0%','details' => '33%','requirements' => '66%','review' => '100%'} }};">
                                </div>

                                @foreach ([
                                    'basic' => ['icon' => 'fa-info-circle', 'label' => 'Basic Info'],
                                    'details' => ['icon' => 'fa-calendar-alt', 'label' => 'Details'],
                                    'requirements' => ['icon' => 'fa-graduation-cap', 'label' => 'Requirements'],
                                    'review' => ['icon' => 'fa-check-circle', 'label' => 'Review'],
                                ] as $tab => $info)
                                    <div class="text-center position-relative" style="z-index: 1; cursor: pointer;"
                                        wire:click="setTab('{{ $tab }}')">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 {{ $activeTab === $tab ? 'bg-success text-white' : ($this->isTabComplete($tab) ? 'bg-success text-white' : 'bg-light text-muted border') }}"
                                            style="width: 40px; height: 40px; transition: all 0.3s;">
                                            <i class="fas {{ $info['icon'] }}"></i>
                                        </div>
                                        <small
                                            class="font-weight-bold {{ $activeTab === $tab ? 'text-success' : 'text-muted' }}">{{ $info['label'] }}</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="updateOpportunity">
                <div class="row">
                    <!-- Main Content Area -->
                    <div class="col-lg-8">

                        <!-- Tab 1: Basic Information -->
                        <div class="{{ $activeTab !== 'basic' ? 'd-none' : '' }}">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-info-circle mr-2"></i>Basic Information
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Opportunity Title <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" wire:model.live="title"
                                                    class="form-control form-control-lg @error('title') is-invalid @enderror"
                                                    placeholder="e.g., Software Development Internship">
                                                @error('title')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Organization <span
                                                        class="text-danger">*</span></label>
                                                <select wire:model="organization_id"
                                                    class="form-control @error('organization_id') is-invalid @enderror">
                                                    <option value="">Select Organization</option>
                                                    @foreach ($this->organizations as $id => $name)
                                                        <option value="{{ $id }}">
                                                            {{ explode(' (', $name)[0] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('organization_id')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Type <span
                                                        class="text-danger">*</span></label>
                                                <select wire:model="type" class="form-control">
                                                    @foreach ($this->opportunityTypes as $value => $label)
                                                        <option value="{{ $value }}">{{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Work Type <span
                                                        class="text-danger">*</span></label>
                                                <select wire:model="work_type" class="form-control">
                                                    @foreach ($this->workTypes as $value => $label)
                                                        <option value="{{ $value }}">{{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Description <span
                                                class="text-danger">*</span></label>
                                        <textarea wire:model.live="description" rows="6" class="form-control @error('description') is-invalid @enderror"
                                            placeholder="Describe the opportunity, what the student will learn, and the impact of their work..."></textarea>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Minimum 100 characters. Current: {{ strlen($description) }} chars
                                        </small>
                                        @error('description')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Responsibilities</label>
                                                <textarea wire:model="responsibilities" rows="3" class="form-control" placeholder="Key responsibilities..."></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Benefits</label>
                                                <textarea wire:model="benefits" rows="3" class="form-control" placeholder="What students will gain..."></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Requirements</label>
                                        <textarea wire:model="requirements" rows="3" class="form-control" placeholder="Specific requirements..."></textarea>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="button" wire:click="nextTab" class="btn btn-primary btn-lg">
                                        Next: Details <i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Details -->
                        <div class="{{ $activeTab !== 'details' ? 'd-none' : '' }}">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-calendar-alt mr-2"></i>Duration & Location
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Duration (Months) <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" wire:model="duration_months"
                                                        class="form-control @error('duration_months') is-invalid @enderror"
                                                        min="1" max="24">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">months</span>
                                                    </div>
                                                </div>
                                                @error('duration_months')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Start Date <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" wire:model="start_date"
                                                    class="form-control @error('start_date') is-invalid @enderror">
                                                @error('start_date')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold">End Date <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" wire:model="end_date"
                                                    class="form-control @error('end_date') is-invalid @enderror">
                                                @error('end_date')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Work Mode</label>
                                        <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                            <label
                                                class="btn btn-outline-secondary flex-fill {{ $work_type_location === 'onsite' ? 'active' : '' }}">
                                                <input type="radio" wire:model="work_type_location" value="onsite"
                                                    autocomplete="off">
                                                <i class="fas fa-building mr-2"></i>On-site
                                            </label>
                                            <label
                                                class="btn btn-outline-info flex-fill {{ $work_type_location === 'hybrid' ? 'active' : '' }}">
                                                <input type="radio" wire:model="work_type_location" value="hybrid"
                                                    autocomplete="off">
                                                <i class="fas fa-exchange-alt mr-2"></i>Hybrid
                                            </label>
                                            <label
                                                class="btn btn-outline-primary flex-fill {{ $work_type_location === 'remote' ? 'active' : '' }}">
                                                <input type="radio" wire:model="work_type_location" value="remote"
                                                    autocomplete="off">
                                                <i class="fas fa-home mr-2"></i>Remote
                                            </label>
                                        </div>
                                    </div>

                                    @if ($work_type_location !== 'remote')
                                        <div class="row bg-light p-3 rounded border">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>County</label>
                                                    <select wire:model="county" class="form-control">
                                                        <option value="">Select County</option>
                                                        @foreach ($this->counties as $c)
                                                            <option value="{{ $c }}">{{ $c }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Town/City</label>
                                                    <input type="text" wire:model="town" class="form-control"
                                                        placeholder="e.g., Nairobi CBD">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Specific Location</label>
                                                    <input type="text" wire:model="location" class="form-control"
                                                        placeholder="e.g., Westlands, ABC Building">
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <hr class="my-4">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Application Deadline <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" wire:model="deadline"
                                                    class="form-control @error('deadline') is-invalid @enderror">
                                                @error('deadline')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Available Slots <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" wire:model="slots_available"
                                                    class="form-control @error('slots_available') is-invalid @enderror"
                                                    min="1">
                                                @error('slots_available')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Stipend (Optional)</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">KSh</span>
                                                    </div>
                                                    <input type="number" wire:model.live="stipend"
                                                        class="form-control" placeholder="0" step="1000">
                                                </div>
                                            </div>
                                        </div>
                                        @if ($stipend)
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Frequency</label>
                                                    <select wire:model="stipend_frequency" class="form-control">
                                                        <option value="monthly">Monthly</option>
                                                        <option value="weekly">Weekly</option>
                                                        <option value="daily">Daily</option>
                                                        <option value="one-time">One-time</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between">
                                    <button type="button" wire:click="previousTab"
                                        class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i> Back
                                    </button>
                                    <button type="button" wire:click="nextTab" class="btn btn-primary btn-lg">
                                        Next: Requirements <i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Requirements -->
                        <div class="{{ $activeTab !== 'requirements' ? 'd-none' : '' }}">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-graduation-cap mr-2"></i>Requirements & Skills
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Minimum GPA</label>
                                                <input type="number" wire:model="min_gpa" class="form-control"
                                                    step="0.1" min="0" max="4"
                                                    placeholder="e.g., 3.0">
                                                <small class="text-muted">Leave empty if no minimum required</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Minimum Year of Study</label>
                                                <select wire:model="min_year_of_study" class="form-control">
                                                    <option value="">Any Year</option>
                                                    @for ($i = 1; $i <= 6; $i++)
                                                        <option value="{{ $i }}">
                                                            {{ $i }}{{ $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')) }}
                                                            Year</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Required Skills -->
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-primary">Required Skills</label>
                                        <div class="input-group mb-2">
                                            <input type="text" wire:model="newSkill"
                                                wire:keydown.enter="addRequiredSkill" class="form-control"
                                                placeholder="Type skill and press Enter">
                                            <div class="input-group-append">
                                                <button type="button" wire:click="addRequiredSkill"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Add
                                                </button>
                                            </div>
                                        </div>

                                        @if (!empty($skills_required))
                                            <div class="mb-2">
                                                @foreach ($skills_required as $index => $skill)
                                                    <span class="badge badge-primary badge-lg mr-2 mb-2 p-2"
                                                        style="font-size: 0.9em;">
                                                        {{ $skill }}
                                                        <button type="button"
                                                            wire:click="removeRequiredSkill({{ $index }})"
                                                            class="btn btn-xs btn-link text-white ml-1">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="mt-2">
                                            <small class="text-muted d-block mb-2">Quick add common skills:</small>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach (array_slice($this->commonSkills, 0, 8) as $skill)
                                                    <button type="button"
                                                        wire:click="addRequiredSkill('{{ $skill }}')"
                                                        class="btn btn-sm btn-outline-primary">
                                                        + {{ $skill }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Preferred Skills -->
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-secondary">Preferred Skills
                                            (Optional)</label>
                                        <div class="input-group mb-2">
                                            <input type="text" wire:model="newPreferredSkill"
                                                wire:keydown.enter="addPreferredSkill" class="form-control"
                                                placeholder="Type skill and press Enter">
                                            <div class="input-group-append">
                                                <button type="button" wire:click="addPreferredSkill"
                                                    class="btn btn-secondary">
                                                    <i class="fas fa-plus"></i> Add
                                                </button>
                                            </div>
                                        </div>

                                        @if (!empty($preferred_skills))
                                            <div class="mb-2">
                                                @foreach ($preferred_skills as $index => $skill)
                                                    <span class="badge badge-secondary badge-lg mr-2 mb-2 p-2"
                                                        style="font-size: 0.9em;">
                                                        {{ $skill }}
                                                        <button type="button"
                                                            wire:click="removePreferredSkill({{ $index }})"
                                                            class="btn btn-xs btn-link text-white ml-1">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Required Courses -->
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-info">Required Courses (Optional)</label>
                                        <div class="input-group mb-2">
                                            <input type="text" wire:model="newCourse"
                                                wire:keydown.enter="addCourse" class="form-control"
                                                placeholder="e.g., Computer Science">
                                            <div class="input-group-append">
                                                <button type="button" wire:click="addCourse" class="btn btn-info">
                                                    <i class="fas fa-plus"></i> Add
                                                </button>
                                            </div>
                                        </div>

                                        @if (!empty($courses_required))
                                            <div class="mb-2">
                                                @foreach ($courses_required as $index => $course)
                                                    <span class="badge badge-info badge-lg mr-2 mb-2 p-2"
                                                        style="font-size: 0.9em;">
                                                        {{ $course }}
                                                        <button type="button"
                                                            wire:click="removeCourse({{ $index }})"
                                                            class="btn btn-xs btn-link text-white ml-1">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" wire:model="requires_portfolio"
                                                    id="requires_portfolio" class="custom-control-input">
                                                <label class="custom-control-label" for="requires_portfolio">
                                                    <i class="fas fa-folder-open mr-1"></i> Requires Portfolio
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" wire:model="requires_cover_letter"
                                                    id="requires_cover_letter" class="custom-control-input">
                                                <label class="custom-control-label" for="requires_cover_letter">
                                                    <i class="fas fa-envelope mr-1"></i> Requires Cover Letter
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between">
                                    <button type="button" wire:click="previousTab"
                                        class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i> Back
                                    </button>
                                    <button type="button" wire:click="nextTab" class="btn btn-primary btn-lg">
                                        Review <i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 4: Review -->
                        <div class="{{ $activeTab !== 'review' ? 'd-none' : '' }}">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-check-circle mr-2"></i>Review Changes
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <!-- Changes Summary -->
                                    <div class="alert alert-warning mb-4">
                                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Review Before Saving</h6>
                                        <p class="mb-0">You are editing an existing opportunity. Changes will be
                                            visible immediately if published.</p>
                                    </div>

                                    <!-- Preview Card -->
                                    <div class="callout callout-info mb-4">
                                        <h5 class="mb-3">{{ $title ?: 'Untitled Opportunity' }}</h5>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong><i class="fas fa-building mr-2"></i>Organization:</strong><br>
                                                {{ $organization_id ? explode(' (', $this->organizations[$organization_id] ?? '')[0] : 'Not selected' }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong><i class="fas fa-tag mr-2"></i>Type:</strong><br>
                                                {{ $this->opportunityTypes[$type] ?? $type }}
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <strong><i class="fas fa-calendar mr-2"></i>Duration:</strong><br>
                                                {{ $duration_months }} months
                                            </div>
                                            <div class="col-md-4">
                                                <strong><i class="fas fa-users mr-2"></i>Slots:</strong><br>
                                                {{ $slots_available }} available
                                            </div>
                                            <div class="col-md-4">
                                                <strong><i class="fas fa-money-bill mr-2"></i>Stipend:</strong><br>
                                                {{ $stipend ? 'KSh ' . number_format($stipend) . ' ' . $stipend_frequency : 'Unpaid' }}
                                            </div>
                                        </div>

                                        @if (!empty($skills_required))
                                            <div class="mb-3">
                                                <strong><i class="fas fa-tools mr-2"></i>Required Skills:</strong><br>
                                                @foreach ($skills_required as $skill)
                                                    <span class="badge badge-primary mr-1">{{ $skill }}</span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div
                                            class="alert alert-{{ $this->getCompletionPercentage() >= 80 ? 'success' : 'warning' }} mt-3">
                                            <strong>Profile Completion:
                                                {{ $this->getCompletionPercentage() }}%</strong><br>
                                            <small>{{ $this->getCompletionPercentage() >= 80 ? 'Great! This opportunity has detailed information.' : 'Consider adding more details for better matching.' }}</small>
                                        </div>
                                    </div>

                                    <!-- Action Cards -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card bg-light h-100 border-0">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-save fa-3x text-secondary mb-3"></i>
                                                    <h5>Save as Draft</h5>
                                                    <p class="small text-muted">Save changes without publishing.
                                                        Opportunity will remain in current state.</p>
                                                    <button type="button" wire:click="saveAsDraft"
                                                        wire:loading.attr="disabled"
                                                        class="btn btn-secondary btn-block">
                                                        <span wire:loading.remove wire:target="saveAsDraft">Save
                                                            Draft</span>
                                                        <span wire:loading wire:target="saveAsDraft"><i
                                                                class="fas fa-spinner fa-spin"></i> Saving...</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light h-100 border-0">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-paper-plane fa-3x text-warning mb-3"></i>
                                                    <h5>Submit for Approval</h5>
                                                    <p class="small text-muted">Send updated opportunity for admin
                                                        review.</p>
                                                    <button type="button" wire:click="submitForApproval"
                                                        wire:loading.attr="disabled"
                                                        class="btn btn-warning btn-block">
                                                        <span wire:loading.remove
                                                            wire:target="submitForApproval">Submit for Approval</span>
                                                        <span wire:loading wire:target="submitForApproval"><i
                                                                class="fas fa-spinner fa-spin"></i>
                                                            Submitting...</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-success h-100 border-0 text-white">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-rocket fa-3x mb-3"></i>
                                                    <h5>Update & Publish</h5>
                                                    <p class="small opacity-75">Save changes and publish immediately.
                                                    </p>
                                                    <button type="button" wire:click="publishDirectly"
                                                        wire:loading.attr="disabled"
                                                        class="btn btn-light btn-block text-success font-weight-bold">
                                                        <span wire:loading.remove wire:target="publishDirectly">Update
                                                            & Publish</span>
                                                        <span wire:loading wire:target="publishDirectly"><i
                                                                class="fas fa-spinner fa-spin"></i>
                                                            Publishing...</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between">
                                    <button type="button" wire:click="previousTab"
                                        class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i> Back to Edit
                                    </button>
                                    <a href="{{ route('admin.opportunities.show', $opportunity) }}"
                                        class="btn btn-default">
                                        Cancel Changes
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Sidebar: Live Preview -->
                    <div class="col-lg-4">
                        <div class="card card-primary card-outline sticky-top" style="top: 20px; z-index: 100;">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-eye mr-2"></i>Live Preview
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="bg-light rounded p-3 mb-2">
                                        <h5 class="mb-1">{{ $title ?: 'Opportunity Title' }}</h5>
                                        <small class="text-muted">
                                            {{ $organization_id ? explode(' (', $this->organizations[$organization_id] ?? '')[0] : 'Organization Name' }}
                                        </small>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center flex-wrap mb-3 gap-2">
                                    @if ($type)
                                        <span
                                            class="badge badge-info">{{ $this->opportunityTypes[$type] ?? $type }}</span>
                                    @endif
                                    @if ($duration_months)
                                        <span class="badge badge-secondary">{{ $duration_months }} months</span>
                                    @endif
                                    <span
                                        class="badge badge-{{ $work_type_location === 'remote' ? 'primary' : ($work_type_location === 'hybrid' ? 'purple' : 'default') }}">
                                        {{ ucfirst($work_type_location) }}
                                    </span>
                                </div>

                                @if ($stipend)
                                    <div class="alert alert-success py-2 text-center mb-3">
                                        <strong>KSh {{ number_format($stipend, 0) }}</strong> /
                                        {{ $stipend_frequency }}
                                    </div>
                                @else
                                    <div class="alert alert-secondary py-2 text-center mb-3">
                                        <small>Unpaid Position</small>
                                    </div>
                                @endif

                                <hr>

                                <div class="small mb-2">
                                    <i class="fas fa-map-marker-alt mr-2 text-muted"></i>
                                    {{ $work_type_location === 'remote' ? 'Remote Work' : ($county ?: 'Location TBD') }}
                                    @if ($town && $work_type_location !== 'remote')
                                        <br><span class="ml-4 text-muted">{{ $town }}</span>
                                    @endif
                                </div>

                                <div class="small mb-2">
                                    <i class="fas fa-calendar-alt mr-2 text-muted"></i>
                                    @if ($deadline)
                                        Apply by {{ \Carbon\Carbon::parse($deadline)->format('M d, Y') }}
                                    @else
                                        Deadline not set
                                    @endif
                                </div>

                                <div class="small mb-2">
                                    <i class="fas fa-users mr-2 text-muted"></i>
                                    {{ $slots_available }} slot{{ $slots_available != 1 ? 's' : '' }} available
                                </div>

                                @if (!empty($skills_required))
                                    <hr>
                                    <small class="text-muted d-block mb-2">Required Skills:</small>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach (array_slice($skills_required, 0, 5) as $skill)
                                            <span class="badge badge-light border">{{ $skill }}</span>
                                        @endforeach
                                        @if (count($skills_required) > 5)
                                            <span class="badge badge-light border">+{{ count($skills_required) - 5 }}
                                                more</span>
                                        @endif
                                    </div>
                                @endif

                                <hr>
                                <div class="text-muted small" style="max-height: 100px; overflow: hidden;">
                                    {{ \Illuminate\Support\Str::limit($description, 150) ?: 'Description will appear here...' }}
                                </div>
                            </div>
                        </div>

                        <!-- Current Status Card -->
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle mr-2"></i>Current Status
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <span
                                        class="badge badge-{{ match ($opportunity->status) {
                                            'open' => 'success',
                                            'draft' => 'secondary',
                                            'pending_approval' => 'warning',
                                            'closed' => 'danger',
                                            'filled' => 'info',
                                            'cancelled' => 'dark',
                                            default => 'secondary',
                                        } }} badge-lg p-2"
                                        style="font-size: 1rem;">
                                        {{ ucfirst(str_replace('_', ' ', $opportunity->status)) }}
                                    </span>
                                </div>
                                <hr>
                                <div class="small text-muted">
                                    <p class="mb-1"><strong>Created:</strong>
                                        {{ $opportunity->created_at->format('M d, Y') }}</p>
                                    <p class="mb-1"><strong>Last Updated:</strong>
                                        {{ $opportunity->updated_at->diffForHumans() }}</p>
                                    @if ($opportunity->published_at)
                                        <p class="mb-0"><strong>Published:</strong>
                                            {{ $opportunity->published_at->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('notify', (event) => {
                    if (typeof toastr !== 'undefined') {
                        toastr[event.type](event.message, '', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: 'toast-top-right',
                            timeOut: 5000
                        });
                    }
                });
            });
        </script>
    @endpush

</div>
