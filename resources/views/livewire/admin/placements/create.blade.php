<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    
    <!-- Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create New Placement</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.placements.index') }}">Placements</a>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Progress Steps -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="steps-progress">
                                <div
                                    class="step {{ $currentStep >= 1 ? 'active' : '' }} {{ $currentStep > 1 ? 'completed' : '' }}">
                                    <div class="step-icon">1</div>
                                    <div class="step-label">Select Student</div>
                                </div>
                                <div class="step-line {{ $currentStep >= 2 ? 'active' : '' }}"></div>
                                <div
                                    class="step {{ $currentStep >= 2 ? 'active' : '' }} {{ $currentStep > 2 ? 'completed' : '' }}">
                                    <div class="step-icon">2</div>
                                    <div class="step-label">Select Match</div>
                                </div>
                                <div class="step-line {{ $currentStep >= 3 ? 'active' : '' }}"></div>
                                <div
                                    class="step {{ $currentStep >= 3 ? 'active' : '' }} {{ $currentStep > 3 ? 'completed' : '' }}">
                                    <div class="step-icon">3</div>
                                    <div class="step-label">Placement Details</div>
                                </div>
                                <div class="step-line {{ $currentStep >= 4 ? 'active' : '' }}"></div>
                                <div class="step {{ $currentStep >= 4 ? 'active' : '' }}">
                                    <div class="step-icon">4</div>
                                    <div class="step-label">Review & Confirm</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 1: Select Student -->
            @if ($currentStep === 1)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Step 1: Select Student</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Search Student</label>
                            <input type="text" wire:model.live.debounce.300ms="studentSearch" class="form-control"
                                placeholder="Search by name, email, reg number, or institution...">
                        </div>

                        @if ($students->isNotEmpty())
                            <div class="table-responsive mt-4">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Registration No.</th>
                                            <th>Institution</th>
                                            <th>Course</th>
                                            <th>Current Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($students as $student)
                                            <tr>
                                                <td>
                                                    <strong>{{ $student->full_name }}</strong>
                                                    <div class="text-muted small">{{ $student->email }}</div>
                                                </td>
                                                <td>{{ $student->studentProfile->student_reg_number ?? 'N/A' }}
                                                </td>
                                                <td>{{ $student->studentProfile->institution_name ?? 'N/A' }}</td>
                                                <td>{{ $student->studentProfile->course_name ?? 'N/A' }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $student->studentProfile->attachment_status === 'seeking' ? 'warning' : 'info' }}">
                                                        {{ $student->studentProfile->attachment_status_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm"
                                                        wire:click="selectStudent({{ $student->id }})">
                                                        <i class="fas fa-check mr-1"></i> Select
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($studentSearch)
                            <div class="alert alert-info mt-3">No students found matching your search.</div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Step 2: Select Application/Match -->
            @if ($currentStep === 2 && $selectedStudent)
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Step 2: Select Match for {{ $selectedStudent->full_name }}</h3>
                        <button class="btn btn-sm btn-outline-secondary" wire:click="previousStep">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </button>
                    </div>
                    <div class="card-body">
                        @if ($studentApplications->isNotEmpty())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-1"></i>
                                Select one of the matched opportunities to create a placement.
                            </div>

                            <div class="row">
                                @foreach ($studentApplications as $application)
                                    <div class="col-md-6 mb-3">
                                        <div
                                            class="card h-100 {{ $selectedApplicationId == $application->id ? 'border-primary' : '' }}">
                                            <div class="card-header bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span
                                                        class="badge badge-{{ $application->match_score >= 80 ? 'success' : ($application->match_score >= 60 ? 'info' : 'warning') }}">
                                                        {{ $application->match_score }}% Match
                                                    </span>
                                                    <span
                                                        class="badge badge-secondary">{{ $application->status }}</span>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $application->opportunity->title }}</h5>
                                                <h6 class="card-subtitle mb-2 text-muted">
                                                    <i class="fas fa-building mr-1"></i>
                                                    {{ $application->opportunity->organization->name }}
                                                </h6>
                                                <p class="card-text text-muted small">
                                                    {{ Str::limit($application->opportunity->description, 150) }}
                                                </p>
                                                <ul class="list-unstyled small mb-0">
                                                    <li><i class="fas fa-map-marker-alt mr-1"></i>
                                                        {{ $application->opportunity->location ?? ($application->opportunity->county ?? 'N/A') }}
                                                    </li>
                                                    <li><i class="fas fa-calendar mr-1"></i> Deadline:
                                                        {{ $application->opportunity->deadline?->format('d M Y') ?? 'No deadline' }}
                                                    </li>
                                                    <li><i class="fas fa-users mr-1"></i> Slots:
                                                        {{ $application->opportunity->slots_available }}</li>
                                                </ul>
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <button class="btn btn-primary btn-block"
                                                    wire:click="selectApplication({{ $application->id }})">
                                                    <i class="fas fa-check-circle mr-1"></i> Select This Match
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                No pending matches found for this student.
                                <a href="{{ route('admin.students.index') }}" class="alert-link">Go back and run
                                    matching first.</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Step 3: Placement Details -->
            @if ($currentStep === 3 && $selectedApplication)
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Step 3: Placement Details</h3>
                        <button class="btn btn-sm btn-outline-secondary" wire:click="previousStep">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <strong>Selected:</strong> {{ $selectedApplication->opportunity->title }} at
                            {{ $selectedApplication->opportunity->organization->name }}
                        </div>

                        <form wire:submit.prevent="nextStep">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Start Date <span class="text-danger">*</span></label>
                                        <input type="date" wire:model="placementData.start_date"
                                            class="form-control @error('placementData.start_date') is-invalid @enderror">
                                        @error('placementData.start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>End Date <span class="text-danger">*</span></label>
                                        <input type="date" wire:model="placementData.end_date"
                                            class="form-control @error('placementData.end_date') is-invalid @enderror">
                                        @error('placementData.end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Department <span class="text-danger">*</span></label>
                                <input type="text" wire:model="placementData.department"
                                    class="form-control @error('placementData.department') is-invalid @enderror"
                                    placeholder="e.g., IT Department, Human Resources">
                                @error('placementData.department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Supervisor Name <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="placementData.supervisor_name"
                                            class="form-control @error('placementData.supervisor_name') is-invalid @enderror">
                                        @error('placementData.supervisor_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Supervisor Contact</label>
                                        <input type="text" wire:model="placementData.supervisor_contact"
                                            class="form-control" placeholder="Phone or email">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Monthly Stipend (KES)</label>
                                <input type="number" wire:model="placementData.stipend" class="form-control"
                                    placeholder="e.g., 10000">
                            </div>

                            <div class="form-group">
                                <label>Notes</label>
                                <textarea wire:model="placementData.notes" class="form-control" rows="3"
                                    placeholder="Any additional information about this placement..."></textarea>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">
                                    Next: Review <i class="fas fa-arrow-right ml-1"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Step 4: Review & Confirm -->
            @if ($currentStep === 4 && $placementPreview)
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Step 4: Review & Confirm Placement</h3>
                        <button class="btn btn-sm btn-outline-secondary" wire:click="previousStep">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2">Student Information</h5>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $placementPreview['student'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Reg No:</strong></td>
                                        <td>{{ $placementPreview['student_reg'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Institution:</strong></td>
                                        <td>{{ $placementPreview['institution'] }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="border-bottom pb-2">Opportunity Information</h5>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td><strong>Position:</strong></td>
                                        <td>{{ $placementPreview['opportunity'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Organization:</strong></td>
                                        <td>{{ $placementPreview['organization'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Match Score:</strong></td>
                                        <td><span
                                                class="badge badge-success">{{ $placementPreview['match_score'] }}%</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <h5 class="border-bottom pb-2">Placement Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td><strong>Start Date:</strong></td>
                                        <td>{{ $placementPreview['start_date'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>End Date:</strong></td>
                                        <td>{{ $placementPreview['end_date'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Duration:</strong></td>
                                        <td>{{ $placementPreview['duration'] }} months</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td><strong>Department:</strong></td>
                                        <td>{{ $placementPreview['department'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Supervisor:</strong></td>
                                        <td>{{ $placementPreview['supervisor'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Contact:</strong></td>
                                        <td>{{ $placementPreview['supervisor_contact'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Stipend:</strong></td>
                                        <td>{{ $placementPreview['stipend'] ? 'KES ' . number_format($placementPreview['stipend']) : 'Unpaid' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if ($placementPreview['notes'])
                            <div class="alert alert-light border mt-3">
                                <strong>Notes:</strong><br>
                                {{ $placementPreview['notes'] }}
                            </div>
                        @endif

                        <div class="alert alert-warning mt-4">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <strong>Important:</strong> Once confirmed, this will:
                            <ul class="mb-0 mt-1">
                                <li>Create an active placement for the student</li>
                                <li>Update student's status to "Placed"</li>
                                <li>Accept this application and reject all other pending applications</li>
                                <li>Notify the student and organization</li>
                            </ul>
                        </div>

                        <div class="text-right mt-4">
                            <button type="button" class="btn btn-success btn-lg" wire:click="createPlacement"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-check-circle mr-1"></i> Confirm & Create Placement
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin mr-1"></i> Creating...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .steps-progress {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #6c757d;
            border: 2px solid #dee2e6;
        }

        .step.active .step-icon {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .step.completed .step-icon {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }

        .step-label {
            margin-top: 8px;
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #007bff;
            font-weight: 600;
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: #dee2e6;
            margin: 0 10px;
            margin-bottom: 25px;
        }

        .step-line.active {
            background: #007bff;
        }
    </style>

</div>
