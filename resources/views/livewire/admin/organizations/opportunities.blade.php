<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <!-- Page Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-briefcase mr-2"></i>
                        {{ $organization->name }} - Opportunities
                        <small class="text-muted">Manage attachment and internship opportunities</small>
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
                        <li class="breadcrumb-item active">Opportunities</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $stats['total'] }}</h3>
                            <p>Total</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $stats['published'] }}</h3>
                            <p>Published</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="#" wire:click.prevent="$set('status', 'published')" class="small-box-footer">
                            <i class="fas fa-arrow-circle-right"></i> View
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $stats['pending'] }}</h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="#" wire:click.prevent="$set('status', 'pending_approval')"
                            class="small-box-footer">
                            <i class="fas fa-arrow-circle-right"></i> View
                        </a>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $stats['draft'] }}</h3>
                            <p>Drafts</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-pencil-alt"></i>
                        </div>
                        <a href="#" wire:click.prevent="$set('status', 'draft')" class="small-box-footer">
                            <i class="fas fa-arrow-circle-right"></i> View
                        </a>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $stats['total_applications'] }}</h3>
                            <p>Applications</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-2"></i>
                        Opportunities List
                        <span class="badge badge-info ml-2">{{ $opportunities->total() }}</span>
                    </h3>

                    <div class="card-tools">
                        <!-- Filter Toggle -->
                        <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#filters">
                            <i class="fas fa-filter"></i>
                        </button>

                        <!-- Add New Button -->
                        <button type="button" class="btn btn-primary btn-sm" wire:click="openCreateModal">
                            <i class="fas fa-plus"></i> New Opportunity
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="collapse" id="filters">
                    <div class="card-body border-bottom">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select wire:model.live="status" class="form-control form-control-sm">
                                        <option value="">All Status</option>
                                        <option value="active">Active</option>
                                        <option value="expired">Expired</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select wire:model.live="type" class="form-control form-control-sm">
                                        <option value="">All Types</option>
                                        <option value="attachment">Attachment</option>
                                        <option value="internship">Internship</option>
                                        <option value="industrial_attachment">Industrial Attachment</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" wire:model.live="dateFrom"
                                        class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" wire:model.live="dateTo" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button wire:click="$set('status', '')" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-undo"></i> Reset Filters
                                </button>

                                <span class="ml-2 text-muted">
                                    Showing {{ $opportunities->firstItem() }} - {{ $opportunities->lastItem() }}
                                    of {{ $opportunities->total() }} results
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input wire:model.live.debounce.300ms="search" type="text" class="form-control"
                                    placeholder="Search by title, description, or location...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                        data-toggle="dropdown">
                                        {{ $perPage }} per page
                                    </button>
                                    <div class="dropdown-menu">
                                        @foreach ([10, 25, 50, 100] as $size)
                                            <a class="dropdown-item {{ $perPage == $size ? 'active' : '' }}"
                                                href="#"
                                                wire:click.prevent="$set('perPage', {{ $size }})">
                                                {{ $size }} per page
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table class="table table-hover table-striped table-head-fixed">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Work Type</th>
                                    <th>Location</th>
                                    <th>Slots</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th>Applications</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($opportunities as $opportunity)
                                    <tr wire:key="opp-{{ $opportunity->id }}">
                                        <td>
                                            <strong>{{ $opportunity->title }}</strong><br>
                                            <small class="text-muted">
                                                {{ Str::limit($opportunity->description, 50) }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $opportunityTypes[$opportunity->type] ?? $opportunity->type }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                {{ $workTypes[$opportunity->work_type] ?? $opportunity->work_type }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $opportunity->location }}
                                            @if ($opportunity->county)
                                                <br><small>{{ $opportunity->county }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-secondary">{{ $opportunity->slots_available }}</span>
                                        </td>
                                        <td>
                                            {{ $opportunity->deadline->format('d M Y') }}
                                            @if ($opportunity->deadline->isPast())
                                                <br><span class="badge badge-danger">Expired</span>
                                            @endif
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm"
                                                wire:change="updateStatus({{ $opportunity->id }}, $event.target.value)">
                                                @foreach ($statuses as $value => $label)
                                                    <option value="{{ $value }}"
                                                        {{ $opportunity->status == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($opportunity->published_at)
                                                <small class="text-muted d-block mt-1">
                                                    Published: {{ $opportunity->published_at->format('d M Y') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-primary">
                                                {{ $opportunity->applications_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('admin.opportunities.show', $opportunity->id) }}"
                                                    class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    wire:click="openEditModal({{ $opportunity->id }})"
                                                    title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    wire:click="openDeleteModal({{ $opportunity->id }})"
                                                    title="Delete"
                                                    {{ $opportunity->applications_count > 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                            <h5>No Opportunities Found</h5>
                                            <p class="text-muted">
                                                @if ($search || $status || $type)
                                                    Try adjusting your filters
                                                @else
                                                    Get started by creating your first opportunity for this organization
                                                @endif
                                            </p>
                                            @if (!$search && !$status && !$type)
                                                <button type="button" class="btn btn-primary"
                                                    wire:click="openCreateModal">
                                                    <i class="fas fa-plus"></i> Create Opportunity
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>

                <!-- Pagination -->
                <div class="card-footer clearfix">
                    <div class="row">
                        <div class="col-sm-6">
                            <span class="text-muted">
                                Showing {{ $opportunities->firstItem() }} to {{ $opportunities->lastItem() }}
                                of {{ $opportunities->total() }} entries
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-right">
                                {{ $opportunities->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="opportunityModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-xl"> <!-- Changed to modal-xl for more space -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if ($showCreateModal)
                            <i class="fas fa-plus-circle mr-2"></i>Create New Opportunity
                        @elseif($showEditModal)
                            <i class="fas fa-edit mr-2"></i>Edit Opportunity
                        @endif
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $showCreateModal ? 'createOpportunity' : 'updateOpportunity' }}">
                        <!-- Basic Information -->
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Basic Information
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="required">Title <span class="text-danger">*</span></label>
                                            <input wire:model="title" type="text"
                                                class="form-control @error('title') is-invalid @enderror"
                                                placeholder="e.g., Software Development Intern">
                                            @error('title')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="required">Status</label>
                                            <select wire:model="status"
                                                class="form-control @error('status') is-invalid @enderror">
                                                <option value="draft">Draft</option>
                                                <option value="pending_approval">Pending Approval</option>
                                                <option value="published">Published</option>
                                                <option value="closed">Closed</option>
                                                <option value="filled">Filled</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                            @error('status')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required">Opportunity Type <span
                                                    class="text-danger">*</span></label>
                                            <select wire:model="type"
                                                class="form-control @error('type') is-invalid @enderror">
                                                <option value="">Select Type</option>
                                                <option value="attachment">Attachment</option>
                                                <option value="internship">Internship</option>
                                                <option value="industrial_attachment">Industrial Attachment</option>
                                                <option value="volunteer">Volunteer</option>
                                                <option value="graduate_trainee">Graduate Trainee</option>
                                            </select>
                                            @error('type')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required">Work Type <span
                                                    class="text-danger">*</span></label>
                                            <select wire:model="work_type"
                                                class="form-control @error('work_type') is-invalid @enderror">
                                                <option value="">Select Work Type</option>
                                                <option value="onsite">On-site</option>
                                                <option value="remote">Remote</option>
                                                <option value="hybrid">Hybrid</option>
                                            </select>
                                            @error('work_type')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="required">Description <span class="text-danger">*</span></label>
                                    <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                                        placeholder="Provide a detailed description of the opportunity..."></textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Location & Dates -->
                        <div class="card card-info card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    Location & Dates
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required">Location <span
                                                    class="text-danger">*</span></label>
                                            <input wire:model="location" type="text"
                                                class="form-control @error('location') is-invalid @enderror"
                                                placeholder="e.g., Nairobi">
                                            @error('location')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>County</label>
                                            <input wire:model="county" type="text"
                                                class="form-control @error('county') is-invalid @enderror"
                                                placeholder="e.g., Nairobi">
                                            @error('county')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input wire:model="start_date" type="date"
                                                class="form-control @error('start_date') is-invalid @enderror">
                                            @error('start_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input wire:model="end_date" type="date"
                                                class="form-control @error('end_date') is-invalid @enderror">
                                            @error('end_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Duration (Months)</label>
                                            <input wire:model="duration_months" type="number"
                                                class="form-control @error('duration_months') is-invalid @enderror"
                                                min="1" step="1" placeholder="e.g., 3">
                                            @error('duration_months')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required">Application Deadline <span
                                                    class="text-danger">*</span></label>
                                            <input wire:model="deadline" type="date"
                                                class="form-control @error('deadline') is-invalid @enderror"
                                                min="{{ now()->format('Y-m-d') }}">
                                            @error('deadline')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required">Slots Available <span
                                                    class="text-danger">*</span></label>
                                            <input wire:model="slots_available" type="number"
                                                class="form-control @error('slots_available') is-invalid @enderror"
                                                min="1" step="1" placeholder="e.g., 5">
                                            @error('slots_available')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Requirements & Qualifications -->
                        <div class="card card-warning card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-tasks mr-2"></i>
                                    Requirements & Qualifications
                                </h3>
                            </div>
                            <div class="card-body">
                                <!-- GPA Requirement -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Minimum GPA (4.0 scale)</label>
                                            <input wire:model="min_gpa" type="number" step="0.1" min="0"
                                                max="4.0"
                                                class="form-control @error('min_gpa') is-invalid @enderror"
                                                placeholder="e.g., 3.0">
                                            @error('min_gpa')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Stipend (KSh)</label>
                                            <input wire:model="stipend" type="number"
                                                class="form-control @error('stipend') is-invalid @enderror"
                                                min="0" step="100" placeholder="e.g., 15000">
                                            @error('stipend')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Responsibilities Section -->
                                <div class="card card-secondary card-outline mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-briefcase mr-2"></i>
                                            Responsibilities
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            @foreach ($responsibilities as $index => $responsibility)
                                                <span class="badge badge-info p-2 m-1" style="font-size: 14px;">
                                                    {{ $responsibility }}
                                                    <a href="#"
                                                        wire:click.prevent="removeResponsibility({{ $index }})"
                                                        class="text-white ml-2">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </span>
                                            @endforeach
                                        </div>

                                        @if ($showResponsibilityInput)
                                            <div class="input-group">
                                                <input wire:model="newResponsibility" type="text"
                                                    class="form-control"
                                                    placeholder="Enter responsibility (e.g., Develop and maintain software applications)"
                                                    wire:keydown.enter="addResponsibility">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-success"
                                                        wire:click="addResponsibility">
                                                        <i class="fas fa-plus"></i> Add
                                                    </button>
                                                    <button type="button" class="btn btn-secondary"
                                                        wire:click="$set('showResponsibilityInput', false)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                wire:click="$set('showResponsibilityInput', true)">
                                                <i class="fas fa-plus"></i> Add Responsibility
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Skills Required Section -->
                                <div class="card card-info card-outline mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-code mr-2"></i>
                                            Skills Required
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            @foreach ($skills_required as $index => $skill)
                                                <span class="badge badge-primary p-2 m-1" style="font-size: 14px;">
                                                    {{ $skill }}
                                                    <a href="#"
                                                        wire:click.prevent="removeSkill({{ $index }})"
                                                        class="text-white ml-2">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </span>
                                            @endforeach
                                        </div>

                                        @if ($showSkillInput)
                                            <div class="input-group">
                                                <input wire:model="newSkill" type="text" class="form-control"
                                                    placeholder="Enter skill (e.g., PHP, Laravel, JavaScript)"
                                                    wire:keydown.enter="addSkill">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-success"
                                                        wire:click="addSkill">
                                                        <i class="fas fa-plus"></i> Add
                                                    </button>
                                                    <button type="button" class="btn btn-secondary"
                                                        wire:click="$set('showSkillInput', false)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                wire:click="$set('showSkillInput', true)">
                                                <i class="fas fa-plus"></i> Add Skill
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Courses Required Section -->
                                <div class="card card-primary card-outline mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-book mr-2"></i>
                                            Required Courses
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            @foreach ($courses_required as $courseId)
                                                <span class="badge badge-success p-2 m-1" style="font-size: 14px;">
                                                    {{ $this->getCourseName($courseId) }}
                                                    <a href="#"
                                                        wire:click.prevent="removeCourse({{ $courseId }})"
                                                        class="text-white ml-2">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </span>
                                            @endforeach
                                        </div>

                                        @if ($showCourseSearch)
                                            <div class="mb-2">
                                                <input wire:model.live.debounce.300ms="courseSearch" type="text"
                                                    class="form-control"
                                                    placeholder="Search courses by name or code...">

                                                @if (!empty($availableCourses))
                                                    <div class="list-group mt-2"
                                                        style="max-height: 200px; overflow-y: auto;">
                                                        @foreach ($availableCourses as $course)
                                                            <a href="#"
                                                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                                                wire:click.prevent="addCourse({{ $course['id'] }})">
                                                                <div>
                                                                    <strong>{{ $course['name'] }}</strong><br>
                                                                    <small>{{ $course['code'] }}</small>
                                                                </div>
                                                                <i class="fas fa-plus-circle text-success"></i>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <button type="button" class="btn btn-sm btn-link mt-2"
                                                    wire:click="$set('showCourseSearch', false)">
                                                    <i class="fas fa-times"></i> Close
                                                </button>
                                            </div>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                wire:click="$set('showCourseSearch', true)">
                                                <i class="fas fa-plus"></i> Add Required Course
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary (Optional) -->
                        @if ($showEditModal && $editingOpportunity)
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-line mr-2"></i>
                                        Summary
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Slug:</strong>
                                            <p class="text-muted">{{ $editingOpportunity->slug }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Published:</strong>
                                            <p class="text-muted">
                                                {{ $editingOpportunity->published_at ? $editingOpportunity->published_at->format('d M Y') : 'Not published' }}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Applications:</strong>
                                            <p class="text-muted">{{ $editingOpportunity->applications()->count() }}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Created:</strong>
                                            <p class="text-muted">
                                                {{ $editingOpportunity->created_at->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary"
                        wire:click="{{ $showCreateModal ? 'createOpportunity' : 'updateOpportunity' }}">
                        <i class="fas fa-save mr-1"></i>
                        {{ $showCreateModal ? 'Create Opportunity' : 'Update Opportunity' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Delete Opportunity
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this opportunity?</p>
                    <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                    @if ($editingOpportunity && $editingOpportunity->applications()->count() > 0)
                        <div class="alert alert-warning mt-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            This opportunity has {{ $editingOpportunity->applications()->count() }} application(s).
                            Deleting it will also remove all associated applications.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="deleteOpportunity">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    @push('scripts')
        <script>
            // Modal handling
            Livewire.on('showCreateModal', () => {
                console.log('Opening create modal'); // For debugging
                $('#opportunityModal').modal('show');
            });

            Livewire.on('showEditModal', () => {
                console.log('Opening edit modal'); // For debugging
                $('#opportunityModal').modal('show');
            });

            Livewire.on('showDeleteModal', () => {
                console.log('Opening delete modal'); // For debugging
                $('#deleteModal').modal('show');
            });

            Livewire.on('refresh', () => {
                $('#opportunityModal').modal('hide');
                $('#deleteModal').modal('hide');
            });

            // Toastr notifications
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
