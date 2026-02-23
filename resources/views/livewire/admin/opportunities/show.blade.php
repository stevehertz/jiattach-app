<div>
    <div class="content">
        <div class="container-fluid">
            
            <!-- Flash Messages -->
            @if(session('message'))
                <div class="alert alert-{{ session('alert-type', 'success') }} alert-dismissible fade show mb-4">
                    <i class="fas fa-{{ session('alert-type') === 'success' ? 'check-circle' : 'info-circle' }} mr-2"></i>
                    {{ session('message') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            <!-- Header Stats Row -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-gradient-primary text-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <h2 class="mb-2">{{ $opportunity->title }}</h2>
                                    <div class="d-flex align-items-center flex-wrap gap-3">
                                        <span class="badge badge-light text-primary px-3 py-2">
                                            <i class="fas fa-building mr-1"></i>
                                            {{ $opportunity->organization->name ?? 'Unknown Organization' }}
                                        </span>
                                        <span class="badge badge-{{ $this->getStatusColor($opportunity->status) }} px-3 py-2">
                                            {{ $this->getStatusLabel($opportunity->status) }}
                                        </span>
                                        <span class="badge badge-light text-dark px-3 py-2">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $opportunity->daysRemaining ?? $opportunity->deadline->diffInDays(now()) }} days left
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.opportunities.edit', $opportunity) }}" class="btn btn-light">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <a href="{{ route('admin.opportunities.index') }}" class="btn btn-outline-light">
                                            <i class="fas fa-arrow-left mr-1"></i> Back
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Main Content Column -->
                <div class="col-lg-8">
                    
                    <!-- Overview Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $opportunity->applications_count ?? $opportunity->applications->count() }}</h3>
                                    <p>Applications</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $opportunity->slots_filled ?? 0 }}/{{ $opportunity->slots_available }}</h3>
                                    <p>Slots Filled</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $opportunity->views ?? 0 }}</h3>
                                    <p>Views</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="small-box bg-{{ $opportunity->status === 'open' ? 'success' : 'danger' }}">
                                <div class="inner">
                                    <h3>{{ $opportunity->status === 'open' ? 'Open' : 'Closed' }}</h3>
                                    <p>Status</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-{{ $opportunity->status === 'open' ? 'check-circle' : 'times-circle' }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Opportunity Details -->
                    <div class="card card-outline card-primary shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>Opportunity Details
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h5 class="text-primary border-bottom pb-2">Description</h5>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($opportunity->description)) !!}
                                </div>
                            </div>

                            @if($opportunity->responsibilities)
                            <div class="mb-4">
                                <h5 class="text-primary border-bottom pb-2">Responsibilities</h5>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($opportunity->responsibilities)) !!}
                                </div>
                            </div>
                            @endif

                            @if($opportunity->requirements)
                            <div class="mb-4">
                                <h5 class="text-primary border-bottom pb-2">Requirements</h5>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($opportunity->requirements)) !!}
                                </div>
                            </div>
                            @endif

                            @if($opportunity->benefits)
                            <div class="mb-4">
                                <h5 class="text-primary border-bottom pb-2">Benefits</h5>
                                <div class="bg-light p-3 rounded">
                                    {!! nl2br(e($opportunity->benefits)) !!}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Applications Section -->
                    <div class="card card-outline card-success shadow-sm mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                <i class="fas fa-users mr-2"></i>Recent Applications
                            </h3>
                            <span class="badge badge-success">{{ $opportunity->applications->count() }} total</span>
                        </div>
                        <div class="card-body p-0">
                            @if($opportunity->applications->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Student</th>
                                                <th>Match Score</th>
                                                <th>Status</th>
                                                <th>Applied</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($opportunity->applications->take(10) as $application)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="user-block mr-2">
                                                                <span class="username">{{ $application->student->full_name ?? 'Unknown' }}</span>
                                                                <span class="description text-muted small">
                                                                    {{ $application->student->studentProfile->course ?? 'N/A' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-{{ ($application->match_score ?? 0) >= 80 ? 'success' : (($application->match_score ?? 0) >= 60 ? 'warning' : 'secondary') }}">
                                                            {{ round($application->match_score ?? 0) }}%
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-{{ match($application->status) {
                                                            'accepted' => 'success',
                                                            'offered' => 'primary',
                                                            'shortlisted' => 'info',
                                                            'reviewing' => 'warning',
                                                            'rejected' => 'danger',
                                                            default => 'secondary'
                                                        } }}">
                                                            {{ ucfirst($application->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-muted small">
                                                        {{ $application->created_at->diffForHumans() }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.users.show', $application->student) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="View Student">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($application->placement)
                                                            <a href="{{ route('admin.placements.show', $application->placement) }}" 
                                                               class="btn btn-sm btn-outline-success" title="View Placement">
                                                                <i class="fas fa-briefcase"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($opportunity->applications->count() > 10)
                                    <div class="card-footer text-center">
                                        <a href="#" class="btn btn-link">
                                            View All Applications <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Applications Yet</h5>
                                    <p class="text-muted">Students haven't applied for this opportunity.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar Column -->
                <div class="col-lg-4">
                    
                    <!-- Action Panel -->
                    <div class="card card-outline card-warning shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs mr-2"></i>Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            
                            {{-- DRAFT / PENDING APPROVAL ACTIONS --}}
                            @if(in_array($opportunity->status, ['draft', 'pending_approval']))
                                <button type="button" 
                                    wire:click="confirmAction('publish', 'Publish Opportunity?', 'This will make the opportunity visible to all students.')"
                                    class="btn btn-success btn-block mb-3">
                                    <i class="fas fa-rocket mr-2"></i>Publish Now
                                </button>

                                <button type="button"
                                    wire:click="confirmAction('cancel', 'Cancel Opportunity?', 'This will cancel the opportunity. No students will be able to apply.')"
                                    class="btn btn-danger btn-block mb-3">
                                    <i class="fas fa-ban mr-2"></i>Cancel Opportunity
                                </button>

                                @if($opportunity->applications->count() === 0)
                                    <button type="button"
                                        wire:click="confirmAction('delete', 'Delete Permanently?', 'This action cannot be undone. The opportunity will be permanently removed.')"
                                        class="btn btn-outline-danger btn-block">
                                        <i class="fas fa-trash-alt mr-2"></i>Delete Permanently
                                    </button>
                                @endif
                            @endif

                            {{-- ACTIVE (OPEN) ACTIONS --}}
                            @if($opportunity->status === 'open')
                                <button type="button"
                                    wire:click="confirmAction('close', 'Close Opportunity?', 'No new applications will be accepted. Existing applications will remain active.')"
                                    class="btn btn-warning btn-block mb-3">
                                    <i class="fas fa-door-closed mr-2"></i>Close Opportunity
                                </button>

                                <button type="button"
                                    wire:click="confirmAction('markAsFilled', 'Mark as Filled?', 'All available slots will be marked as filled.')"
                                    class="btn btn-info btn-block mb-3">
                                    <i class="fas fa-check-double mr-2"></i>Mark as Filled
                                </button>

                                <button type="button"
                                    wire:click="confirmAction('cancel', 'Cancel Opportunity?', 'This will cancel the opportunity immediately.')"
                                    class="btn btn-danger btn-block">
                                    <i class="fas fa-ban mr-2"></i>Cancel Opportunity
                                </button>
                            @endif

                            {{-- CLOSED ACTIONS --}}
                            @if($opportunity->status === 'closed')
                                <button type="button"
                                    wire:click="confirmAction('markAsFilled', 'Mark as Filled?', 'Confirm all slots are now filled?')"
                                    class="btn btn-info btn-block mb-3">
                                    <i class="fas fa-check-double mr-2"></i>Mark as Filled
                                </button>

                                <button type="button"
                                    wire:click="confirmAction('cancel', 'Cancel Opportunity?', 'Cancel this closed opportunity?')"
                                    class="btn btn-danger btn-block">
                                    <i class="fas fa-ban mr-2"></i>Cancel Opportunity
                                </button>
                            @endif

                            {{-- FILLED / CANCELLED --}}
                            @if(in_array($opportunity->status, ['filled', 'cancelled']))
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    This opportunity is {{ $opportunity->status }}. No further actions available.
                                </div>
                            @endif

                        </div>
                    </div>

                    <!-- Opportunity Info Card -->
                    <div class="card card-outline card-primary shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clipboard-list mr-2"></i>Opportunity Info
                            </h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <b>Type</b> 
                                    <span class="float-right badge badge-primary">{{ ucfirst($opportunity->type) }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Work Type</b> 
                                    <span class="float-right badge badge-info">{{ ucfirst($opportunity->work_type) }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Duration</b> 
                                    <span class="float-right">{{ $opportunity->duration_months }} months</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Start Date</b> 
                                    <span class="float-right">{{ $opportunity->start_date?->format('M d, Y') }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>End Date</b> 
                                    <span class="float-right">{{ $opportunity->end_date?->format('M d, Y') }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Deadline</b> 
                                    <span class="float-right {{ ($opportunity->daysRemaining ?? 0) > 7 ? 'text-success' : (($opportunity->daysRemaining ?? 0) > 0 ? 'text-warning' : 'text-danger') }}">
                                        {{ $opportunity->deadline?->format('M d, Y') }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Location & Logistics -->
                    <div class="card card-outline card-success shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-map-marker-alt mr-2"></i>Location & Logistics
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong class="d-block text-muted mb-1">Work Mode</strong>
                                <span class="badge badge-{{ $opportunity->work_type === 'remote' ? 'primary' : ($opportunity->work_type === 'hybrid' ? 'purple' : 'secondary') }}">
                                    {{ ucfirst($opportunity->work_type) }}
                                </span>
                            </div>
                            
                            @if($opportunity->location)
                                <div class="mb-3">
                                    <strong class="d-block text-muted mb-1">Location</strong>
                                    {{ $opportunity->location }}
                                </div>
                            @endif
                            
                            @if($opportunity->county)
                                <div class="mb-3">
                                    <strong class="d-block text-muted mb-1">County</strong>
                                    {{ $opportunity->county }}
                                </div>
                            @endif

                            <div class="mb-3">
                                <strong class="d-block text-muted mb-1">Stipend</strong>
                                @if($opportunity->stipend)
                                    <span class="text-success font-weight-bold">KSh {{ number_format($opportunity->stipend) }}</span>
                                @else
                                    <span class="text-muted">Unpaid / Not specified</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Requirements Summary -->
                    <div class="card card-outline card-info shadow-sm mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-graduation-cap mr-2"></i>Requirements
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($opportunity->min_gpa)
                                <div class="mb-3">
                                    <strong class="d-block text-muted mb-1">Minimum GPA</strong>
                                    <span class="badge badge-warning">{{ $opportunity->min_gpa }}</span>
                                </div>
                            @endif

                            @if($opportunity->skills_required && is_array($opportunity->skills_required))
                                <div class="mb-3">
                                    <strong class="d-block text-muted mb-1">Required Skills</strong>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($opportunity->skills_required as $skill)
                                            <span class="badge badge-primary">{{ $skill }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($opportunity->courses_required && is_array($opportunity->courses_required))
                                <div class="mb-3">
                                    <strong class="d-block text-muted mb-1">Required Courses</strong>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($opportunity->courses_required as $course)
                                            <span class="badge badge-info">{{ $course }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Organization Card -->
                    <div class="card card-outline card-secondary shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-building mr-2"></i>Organization
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                     style="width: 80px; height: 80px;">
                                    <i class="fas fa-building fa-2x text-muted"></i>
                                </div>
                                <h5 class="mb-0">{{ $opportunity->organization->name ?? 'Unknown' }}</h5>
                                <small class="text-muted">{{ $opportunity->organization->industry ?? '' }}</small>
                            </div>
                            
                            <ul class="list-unstyled mb-0">
                                @if($opportunity->organization->email)
                                    <li class="mb-2">
                                        <i class="fas fa-envelope text-muted mr-2 w-20"></i>
                                        {{ $opportunity->organization->email }}
                                    </li>
                                @endif
                                @if($opportunity->organization->phone)
                                    <li class="mb-2">
                                        <i class="fas fa-phone text-muted mr-2 w-20"></i>
                                        {{ $opportunity->organization->phone }}
                                    </li>
                                @endif
                                @if($opportunity->organization->address)
                                    <li class="mb-2">
                                        <i class="fas fa-map-marker-alt text-muted mr-2 w-20"></i>
                                        {{ $opportunity->organization->address }}
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                // SweetAlert confirmation handler
                Livewire.on('show-swal-confirm', (event) => {
                    Swal.fire({
                        title: event.title,
                        text: event.text,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, proceed!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            @this.executeConfirmedAction();
                        }
                    });
                });

                // Toast notifications
                Livewire.on('notify', (event) => {
                    if (typeof toastr !== 'undefined') {
                        toastr[event.type](event.message, '', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: 'toast-top-right',
                            timeOut: 5000
                        });
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: event.type,
                            title: event.type === 'success' ? 'Success!' : 'Error!',
                            text: event.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                });
            });
        </script>
    @endpush
</div>