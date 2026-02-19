<div>
    {{-- Stop trying to control. --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        @if ($viewType === 'verified')
                            Verified Mentors
                        @elseif($viewType === 'featured')
                            Featured Mentors
                        @elseif($viewType === 'available')
                            Available Mentors
                        @else
                            All Mentors
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.mentors.index') }}">Mentors</a></li>
                        <li class="breadcrumb-item active">
                            @if ($viewType === 'verified')
                                Verified
                            @elseif($viewType === 'featured')
                                Featured
                            @elseif($viewType === 'available')
                                Available
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
                            <i class="fas fa-user-tie"></i>
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
                            <span class="info-box-text">Verified</span>
                            <span class="info-box-number">{{ $stats['verified'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning elevation-1">
                            <i class="fas fa-star"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Featured</span>
                            <span class="info-box-number">{{ $stats['featured'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary elevation-1">
                            <i class="fas fa-user-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Available</span>
                            <span class="info-box-number">{{ $stats['available'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-indigo elevation-1">
                            <i class="fas fa-hands-helping"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active</span>
                            <span class="info-box-number">{{ $stats['active_mentorships'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger elevation-1">
                            <i class="fas fa-calendar-day"></i>
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
                        @if($viewType === 'verified')
                            Verified Mentors List ({{ $stats['verified'] }})
                        @elseif($viewType === 'featured')
                            Featured Mentors List ({{ $stats['featured'] }})
                        @elseif($viewType === 'available')
                            Available Mentors List ({{ $stats['available'] }})
                        @else
                            All Mentors List ({{ $stats['total'] }})
                        @endif
                    </h3>

                    <div class="card-tools">
                        <div class="btn-group mr-2">
                            <a href="{{ route('admin.mentors.index') }}"
                               class="btn btn-sm btn-outline-secondary {{ $viewType === 'all' ? 'active' : '' }}">
                                All
                            </a>
                            <a href="{{ route('admin.mentors.verified') }}"
                               class="btn btn-sm btn-outline-success {{ $viewType === 'verified' ? 'active' : '' }}">
                                <i class="fas fa-check-circle mr-1"></i> Verified
                                @if($stats['verified'] > 0)
                                    <span class="badge badge-success ml-1">{{ $stats['verified'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.mentors.featured') }}"
                               class="btn btn-sm btn-outline-warning {{ $viewType === 'featured' ? 'active' : '' }}">
                                <i class="fas fa-star mr-1"></i> Featured
                                @if($stats['featured'] > 0)
                                    <span class="badge badge-warning ml-1">{{ $stats['featured'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.mentors.available') }}"
                               class="btn btn-sm btn-outline-primary {{ $viewType === 'available' ? 'active' : '' }}">
                                <i class="fas fa-user-check mr-1"></i> Available
                                @if($stats['available'] > 0)
                                    <span class="badge badge-primary ml-1">{{ $stats['available'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.mentors.create') }}"
                               class="btn btn-sm btn-outline-info">
                                <i class="fas fa-plus mr-1"></i> Add Mentor
                            </a>
                        </div>

                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" wire:model.live.debounce.300ms="search"
                                   class="form-control float-right" placeholder="Search mentors...">
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
                                <label>Expertise</label>
                                <select wire:model.live="expertiseFilter" class="form-control">
                                    <option value="">All Expertise</option>
                                    @foreach($expertises as $expertise)
                                        <option value="{{ $expertise }}">{{ $expertise }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Industry</label>
                                <select wire:model.live="industryFilter" class="form-control">
                                    <option value="">All Industries</option>
                                    @foreach($industries as $industry)
                                        <option value="{{ $industry }}">{{ $industry }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Experience</label>
                                <select wire:model.live="experienceFilter" class="form-control">
                                    <option value="">All Levels</option>
                                    <option value="entry">Entry Level (0-2 yrs)</option>
                                    <option value="junior">Junior (2-4 yrs)</option>
                                    <option value="mid">Mid Level (5-9 yrs)</option>
                                    <option value="senior">Senior (10+ yrs)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Min Rating</label>
                                <select wire:model.live="ratingFilter" class="form-control">
                                    <option value="">Any Rating</option>
                                    <option value="3.0">3.0+ Stars</option>
                                    <option value="4.0">4.0+ Stars</option>
                                    <option value="4.5">4.5+ Stars</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Results Per Page</label>
                                <select wire:model.live="perPage" class="form-control">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
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
                                    <th>Mentor</th>
                                    <th>Expertise</th>
                                    <th>Experience</th>
                                    <th>Rating</th>
                                    <th>Availability</th>
                                    <th>Mentees</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mentors as $mentor)
                                    <tr wire:key="mentor-{{ $mentor->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    @php
                                                        $initials = getInitials($mentor->user->full_name);
                                                        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                                                        $color = $colors[crc32($mentor->user->email) % count($colors)];
                                                    @endphp
                                                    <div class="avatar-initials bg-{{ $color }} img-circle"
                                                         style="width: 40px; height: 40px; line-height: 40px; text-align: center; color: white; font-weight: bold; font-size: 14px;">
                                                        {{ $initials }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>{{ $mentor->user->full_name }}</strong>
                                                    <div class="text-muted small">{{ $mentor->job_title }} at {{ $mentor->company }}</div>
                                                    <div class="text-muted small">{{ $mentor->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($mentor->areas_of_expertise && count($mentor->areas_of_expertise) > 0)
                                                <div class="mb-1">
                                                    @foreach(array_slice($mentor->areas_of_expertise, 0, 2) as $expertise)
                                                        <span class="badge badge-info mr-1 mb-1">{{ $expertise }}</span>
                                                    @endforeach
                                                    @if(count($mentor->areas_of_expertise) > 2)
                                                        <small class="text-muted">+{{ count($mentor->areas_of_expertise) - 2 }} more</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                {{ $mentor->years_of_experience }} years
                                            </div>
                                            <small class="text-muted">{{ $mentor->experience_level }}</small>
                                        </td>
                                        <td>
                                            @if($mentor->average_rating)
                                                <div class="d-flex align-items-center">
                                                    {!! getRatingStars($mentor->average_rating) !!}
                                                    <small class="text-muted ml-2">{{ $mentor->average_rating }}/5</small>
                                                </div>
                                                <small class="text-muted">({{ $mentor->total_reviews }} reviews)</small>
                                            @else
                                                <span class="text-muted">No ratings yet</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $mentor->availability === 'available' ? 'success' : ($mentor->availability === 'limited' ? 'warning' : 'secondary') }}">
                                                {{ $mentor->availability_label }}
                                            </span>
                                            <div class="text-muted small">
                                                {{ $mentor->meeting_preference_label }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-{{ $mentor->current_mentees >= $mentor->max_mentees ? 'danger' : ($mentor->current_mentees >= $mentor->max_mentees * 0.7 ? 'warning' : 'success') }}"
                                                     role="progressbar"
                                                     style="width: {{ min(100, ($mentor->current_mentees / $mentor->max_mentees) * 100) }}%">
                                                    {{ $mentor->current_mentees }}/{{ $mentor->max_mentees }}
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $mentor->slots_available }} slots available</small>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                @if($mentor->is_verified)
                                                    <span class="badge badge-success mb-1">Verified</span>
                                                @else
                                                    <span class="badge badge-warning mb-1">Pending</span>
                                                @endif

                                                @if($mentor->is_featured)
                                                    <span class="badge badge-warning">Featured</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info"
                                                        wire:click="viewMentor({{ $mentor->id }})"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <!-- Verification Actions -->
                                                        @if($mentor->is_verified)
                                                            <button class="dropdown-item text-warning"
                                                                    wire:click="unverifyMentor({{ $mentor->id }})"
                                                                    wire:confirm="Unverify this mentor?">
                                                                <i class="fas fa-times-circle mr-2"></i> Unverify
                                                            </button>
                                                        @else
                                                            <button class="dropdown-item text-success"
                                                                    wire:click="verifyMentor({{ $mentor->id }})"
                                                                    wire:confirm="Verify this mentor?">
                                                                <i class="fas fa-check-circle mr-2"></i> Verify
                                                            </button>
                                                        @endif

                                                        <!-- Feature Actions -->
                                                        @if($mentor->is_featured)
                                                            <button class="dropdown-item text-warning"
                                                                    wire:click="unfeatureMentor({{ $mentor->id }})"
                                                                    wire:confirm="Remove featured status?">
                                                                <i class="fas fa-star-half-alt mr-2"></i> Unfeature
                                                            </button>
                                                        @else
                                                            <button class="dropdown-item text-warning"
                                                                    wire:click="featureMentor({{ $mentor->id }})"
                                                                    wire:confirm="Feature this mentor?">
                                                                <i class="fas fa-star mr-2"></i> Feature
                                                            </button>
                                                        @endif

                                                        <div class="dropdown-divider"></div>

                                                        <a href="{{ route('admin.mentors.edit', $mentor->id) }}"
                                                           class="dropdown-item">
                                                            <i class="fas fa-edit mr-2"></i> Edit
                                                        </a>

                                                        <div class="dropdown-divider"></div>

                                                        <button class="dropdown-item text-danger"
                                                                wire:click="deleteMentor({{ $mentor->id }})"
                                                                wire:confirm="Delete this mentor? This will also delete their user account.">
                                                            <i class="fas fa-trash mr-2"></i> Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No mentors found</h5>
                                            @if($search || $expertiseFilter || $industryFilter || $experienceFilter || $ratingFilter)
                                                <p class="text-muted">Try adjusting your search or filters</p>
                                                <button wire:click="$set(['search' => '', 'expertiseFilter' => '', 'industryFilter' => '', 'experienceFilter' => '', 'ratingFilter' => ''])"
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
                                Showing {{ $mentors->firstItem() ?? 0 }} to {{ $mentors->lastItem() ?? 0 }}
                                of {{ $mentors->total() }} entries
                            </span>
                        </div>
                        <div>
                            @if($mentors->hasPages())
                                {{ $mentors->links() }}
                            @endif
                        </div>
                        <div>
                            <button type="button" class="btn btn-default" wire:click="$toggle('showFilters')">
                                <i class="fas fa-filter mr-1"></i>
                                {{ $showFilters ? 'Hide Filters' : 'Show Filters' }}
                            </button>
                            <a href="{{ route('admin.mentors.create') }}" class="btn btn-primary ml-2">
                                <i class="fas fa-plus mr-1"></i> Add Mentor
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
