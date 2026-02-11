<div>
    <!-- Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        @if ($viewType === 'active')
                            Active Opportunities
                        @elseif($viewType === 'pending')
                            Pending Approval Opportunities
                        @else
                            All Opportunities
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.opportunities.index') }}">Opportunities</a>
                        </li>
                        <li class="breadcrumb-item active">
                            @if ($viewType === 'active')
                                Active
                            @elseif($viewType === 'pending')
                                Pending
                            @else
                                All
                            @endif
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1">
                            <i class="fas fa-briefcase"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total</span>
                            <span class="info-box-number">{{ $stats['total'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active</span>
                            <span class="info-box-number">{{ $stats['active'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning elevation-1">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending</span>
                            <span class="info-box-number">{{ $stats['pending'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary elevation-1">
                            <i class="fas fa-ban"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Closed</span>
                            <span class="info-box-number">{{ $stats['closed'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary elevation-1">
                            <i class="fas fa-money-bill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">With Stipend</span>
                            <span class="info-box-number">{{ $stats['with_stipend'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-indigo elevation-1">
                            <i class="fas fa-plus-circle"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Today</span>
                            <span class="info-box-number">{{ $stats['today'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        @if($viewType === 'active')
                            Active Opportunities List
                        @elseif($viewType === 'pending')
                            Pending Approval Opportunities
                        @else
                            All Opportunities List
                        @endif
                    </h3>

                    <div class="card-tools">
                        <div class="btn-group mr-2">
                            <a href="{{ route('admin.opportunities.index') }}" 
                               class="btn btn-sm btn-outline-secondary {{ $viewType === 'all' ? 'active' : '' }}">
                                All
                            </a>
                            <a href="{{ route('admin.opportunities.active') }}" 
                               class="btn btn-sm btn-outline-success {{ $viewType === 'active' ? 'active' : '' }}">
                                Active
                            </a>
                            <a href="{{ route('admin.opportunities.pending') }}" 
                               class="btn btn-sm btn-outline-warning {{ $viewType === 'pending' ? 'active' : '' }}">
                                Pending
                            </a>
                            <a href="{{ route('admin.opportunities.create') }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus mr-1"></i> New
                            </a>
                        </div>

                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" wire:model.live.debounce.300ms="search"
                                   class="form-control float-right" placeholder="Search opportunities...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" wire:click="$set('search', '')" title="Clear search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body border-bottom @if(!$showFilters) d-none @endif" id="filterSection">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Opportunity Type</label>
                                <select wire:model.live="typeFilter" class="form-control">
                                    <option value="">All Types</option>
                                    @foreach($opportunityTypes as $typeValue => $typeLabel)
                                        <option value="{{ $typeValue }}">{{ $typeLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select wire:model.live="statusFilter" class="form-control">
                                    <option value="">All Status</option>
                                    @foreach($statusOptions as $statusValue => $statusLabel)
                                        <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Location</label>
                                <select wire:model.live="locationFilter" class="form-control">
                                    <option value="">All Locations</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location }}">{{ $location }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- <div class="col-md-3">
                            <div class="form-group">
                                <label>Employer</label>
                                <select wire:model.live="employerFilter" class="form-control">
                                    <option value="">All Employers</option>
                                    @foreach($employers as $employerId => $employerName)
                                        <option value="{{ $employerId }}">{{ $employerName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Per Page</label>
                                <select wire:model.live="perPage" class="form-control">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th wire:click="sortBy('title')" style="cursor: pointer;">
                                        Opportunity
                                        @if($sortField === 'title')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </th>
                                    <th>Company</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Slots</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                        Created
                                        @if($sortField === 'created_at')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort text-muted"></i>
                                        @endif
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($opportunities as $opportunity)
                                    <tr wire:key="opportunity-{{ $opportunity->id }}">
                                        <td>
                                            <div>
                                                <strong>{{ $opportunity->title }}</strong>
                                                <div class="text-muted small">
                                                    {{ truncateText($opportunity->description, 60) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    @php
                                                        $initials = getInitials($opportunity->employer->company_name ?? 'Company');
                                                        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                                                        $color = $colors[crc32($opportunity->employer->company_name ?? '') % count($colors)];
                                                    @endphp
                                                    <div class="avatar-initials bg-{{ $color }} img-circle"
                                                         style="width: 30px; height: 30px; line-height: 30px; text-align: center; color: white; font-weight: bold; font-size: 12px;">
                                                        {{ $initials }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>{{ $opportunity->employer->company_name ?? 'N/A' }}</strong>
                                                    <div class="text-muted small">
                                                        {{ $opportunity->employer->company_email ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $opportunity->opportunity_type_label }}
                                            </span>
                                            <div class="text-muted small">
                                                {{ $opportunity->employment_type_label ?? '' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $opportunity->location_type }}</strong>
                                                <div class="text-muted small">
                                                    {{ $opportunity->full_location }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress-group">
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-{{ $opportunity->slots_filled >= $opportunity->slots_available ? 'danger' : 'success' }}" 
                                                         style="width: {{ ($opportunity->slots_filled / max(1, $opportunity->slots_available)) * 100 }}%">
                                                    </div>
                                                </div>
                                                <span class="progress-number">
                                                    <b>{{ $opportunity->slots_filled }}</b>/{{ $opportunity->slots_available }}
                                                </span>
                                            </div>
                                            <div class="text-muted small">
                                                {{ $opportunity->duration_label }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($opportunity->application_deadline)
                                                <div class="{{ $opportunity->application_deadline_passed ? 'text-danger' : 'text-success' }}">
                                                    {{ formatDate($opportunity->application_deadline) }}
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $opportunity->days_until_deadline }} days
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            {!! getOpportunityStatusBadge($opportunity->status) !!}
                                            @if($opportunity->stipend)
                                                <div class="text-muted small">
                                                    {{ $opportunity->stipend_formatted }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                {{ formatDate($opportunity->created_at) }}
                                            </div>
                                            <small class="text-muted">{{ timeAgo($opportunity->created_at) }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info"
                                                        wire:click="viewOpportunity({{ $opportunity->id }})"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        @if($opportunity->status === 'pending_approval')
                                                            <button class="dropdown-item text-success"
                                                                    wire:click="publishOpportunity({{ $opportunity->id }})"
                                                                    wire:confirm="Publish this opportunity?">
                                                                <i class="fas fa-check mr-2"></i> Publish
                                                            </button>
                                                        @endif
                                                        
                                                        @if($opportunity->status === 'published')
                                                            <button class="dropdown-item text-warning"
                                                                    wire:click="closeOpportunity({{ $opportunity->id }})"
                                                                    wire:confirm="Close this opportunity?">
                                                                <i class="fas fa-times mr-2"></i> Close
                                                            </button>
                                                            <button class="dropdown-item text-info"
                                                                    wire:click="markAsFilled({{ $opportunity->id }})"
                                                                    wire:confirm="Mark as filled?">
                                                                <i class="fas fa-check-circle mr-2"></i> Mark as Filled
                                                            </button>
                                                        @endif
                                                        
                                                        @if(in_array($opportunity->status, ['published', 'pending_approval', 'draft']))
                                                            <div class="dropdown-divider"></div>
                                                            <a href="{{ route('admin.opportunities.edit', $opportunity->id) }}" 
                                                               class="dropdown-item">
                                                                <i class="fas fa-edit mr-2"></i> Edit
                                                            </a>
                                                        @endif
                                                        
                                                        <div class="dropdown-divider"></div>
                                                        <button class="dropdown-item text-danger"
                                                                wire:click="deleteOpportunity({{ $opportunity->id }})"
                                                                wire:confirm="Delete this opportunity?">
                                                            <i class="fas fa-trash mr-2"></i> Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No opportunities found</h5>
                                            @if($search || $typeFilter || $statusFilter || $locationFilter || $employerFilter)
                                                <p class="text-muted">Try adjusting your search or filters</p>
                                                <button wire:click="$set(['search' => '', 'typeFilter' => '', 'statusFilter' => '', 'locationFilter' => '', 'employerFilter' => ''])"
                                                        class="btn btn-sm btn-primary">
                                                    <i class="fas fa-times mr-1"></i> Clear Filters
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="card-footer clearfix">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">
                                Showing {{ $opportunities->firstItem() ?? 0 }} to {{ $opportunities->lastItem() ?? 0 }}
                                of {{ $opportunities->total() }} entries
                            </span>
                        </div>
                        <div>
                            @if($opportunities->hasPages())
                                {{ $opportunities->links() }}
                            @endif
                        </div>
                        <div>
                            <button type="button" class="btn btn-default" wire:click="$toggle('showFilters')">
                                <i class="fas fa-filter mr-1"></i>
                                {{ $showFilters ? 'Hide Filters' : 'Show Filters' }}
                            </button>
                            <a href="{{ route('admin.opportunities.create') }}" class="btn btn-primary ml-2">
                                <i class="fas fa-plus mr-1"></i> New Opportunity
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
            });
        </script>
    @endpush

</div>
