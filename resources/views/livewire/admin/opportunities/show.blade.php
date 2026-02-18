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
                                            {{ $opportunity->days_until_deadline }} days left
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
                            <div class="small-box bg-{{ $opportunity->isOpen ? 'success' : 'danger' }}">
                                <div class="inner">
                                    <h3>{{ $opportunity->isOpen ? 'Open' : 'Closed' }}</h3>
                                    <p>Status</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-{{ $opportunity->isOpen ? 'check-circle' : 'times-circle' }}"></i>
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
                            <!-- Description -->
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
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge badge-{{ $application->match_score >= 80 ? 'success' : ($application->match_score >= 60 ? 'warning' : 'secondary') }}">
                                                                {{ round($application->match_score ?? 0) }}%
                                                            </span>
                                                        </div>
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
                                        <a href="{{ route('admin.opportunities.applications', $opportunity) }}" class="btn btn-link">
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

                    <!-- Activity Timeline -->
                    @if($opportunity->placements->count() > 0)
                    <div class="card card-outline card-secondary shadow-sm mt-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-2"></i>Placements
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach($opportunity->placements as $placement)
                                    <div class="time-label">
                                        <span class="bg-{{ $placement->status === 'placed' ? 'success' : 'warning' }}">
                                            {{ $placement->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <div>
                                        <i class="fas fa-user-check bg-{{ $placement->status === 'placed' ? 'success' : 'warning' }}"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> {{ $placement->created_at->diffForHumans() }}</span>
                                            <h3 class="timeline-header">
                                                <a href="{{ route('admin.users.show', $placement->student) }}">
                                                    {{ $placement->student->full_name ?? 'Unknown' }}
                                                </a> 
                                                was {{ $placement->status === 'placed' ? 'placed' : 'assigned' }}
                                            </h3>
                                            <div class="timeline-body">
                                                Department: {{ $placement->department ?? 'N/A' }}<br>
                                                Supervisor: {{ $placement->supervisor_name ?? 'TBD' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
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
                            @if($opportunity->status === 'draft' || $opportunity->status === 'pending_approval')
                                <button wire:click="publishOpportunity" 
                                    wire:confirm="Are you sure you want to publish this opportunity? It will be visible to students."
                                    class="btn btn-success btn-block mb-3" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="publishOpportunity">
                                        <i class="fas fa-rocket mr-2"></i>Publish Now
                                    </span>
                                    <span wire:loading wire:target="publishOpportunity">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Publishing...
                                    </span>
                                </button>
                            @endif

                            @if($opportunity->status === 'open')
                                <button wire:click="closeOpportunity" 
                                    wire:confirm="Close this opportunity? No new applications will be accepted."
                                    class="btn btn-warning btn-block mb-3" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="closeOpportunity">
                                        <i class="fas fa-door-closed mr-2"></i>Close Opportunity
                                    </span>
                                    <span wire:loading wire:target="closeOpportunity">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Closing...
                                    </span>
                                </button>

                                <button wire:click="markAsFilled" 
                                    wire:confirm="Mark all slots as filled?"
                                    class="btn btn-info btn-block mb-3" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="markAsFilled">
                                        <i class="fas fa-check-double mr-2"></i>Mark as Filled
                                    </span>
                                    <span wire:loading wire:target="markAsFilled">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Processing...
                                    </span>
                                </button>
                            @endif

                            @if(in_array($opportunity->status, ['draft', 'pending_approval', 'open']))
                                <button wire:click="cancelOpportunity" 
                                    wire:confirm="Cancel this opportunity? This action cannot be undone."
                                    class="btn btn-danger btn-block mb-3" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="cancelOpportunity">
                                        <i class="fas fa-ban mr-2"></i>Cancel Opportunity
                                    </span>
                                    <span wire:loading wire:target="cancelOpportunity">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Cancelling...
                                    </span>
                                </button>
                            @endif

                            @if(in_array($opportunity->status, ['draft', 'cancelled']) && $opportunity->applications->count() === 0)
                                <hr>
                                <button wire:click="deleteOpportunity" 
                                    wire:confirm="Permanently delete this opportunity? This cannot be undone."
                                    class="btn btn-outline-danger btn-block" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="deleteOpportunity">
                                        <i class="fas fa-trash-alt mr-2"></i>Delete Permanently
                                    </span>
                                    <span wire:loading wire:target="deleteOpportunity">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Deleting...
                                    </span>
                                </button>
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
                                    <span class="float-right {{ $opportunity->daysRemaining > 7 ? 'text-success' : ($opportunity->daysRemaining > 0 ? 'text-warning' : 'text-danger') }}">
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