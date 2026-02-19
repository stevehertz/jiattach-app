<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Mentorships</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Mentorships</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ \App\Models\Mentorship::count() }}</h3>
                            <p>Total Mentorships</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $activeMentorships }}</h3>
                            <p>Active Sessions</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <a href="{{ route('admin.mentorships.active') }}" class="small-box-footer">
                            View Active <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $completedMentorships }}</h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="{{ route('admin.mentorships.completed') }}" class="small-box-footer">
                            View Completed <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $pendingMentorships }}</h3>
                            <p>Pending Approval</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Pending <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Mentorships</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.mentorships.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Mentorship
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                    placeholder="Search mentorships...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="statusFilter" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="requested">Requested</option>
                                    <option value="pending_approval">Pending Approval</option>
                                    <option value="active">Active</option>
                                    <option value="paused">Paused</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="typeFilter" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="paid">Paid</option>
                                    <option value="free">Free</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="perPage" class="form-control">
                                    <option value="10">10 per page</option>
                                    <option value="15">15 per page</option>
                                    <option value="25">25 per page</option>
                                    <option value="50">50 per page</option>
                                    <option value="100">100 per page</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            @if (count($selectedMentorships) > 0)
                                <div class="btn-group">
                                    <button type="button" class="btn btn-danger"
                                        onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                        wire:click="deleteSelected">
                                        <i class="fas fa-trash"></i> Delete Selected ({{ count($selectedMentorships) }})
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mentorships Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <div class="icheck-primary">
                                            <input type="checkbox" wire:model="selectAll" id="selectAll">
                                            <label for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('title')" style="cursor: pointer;">
                                        Title
                                        @if ($sortBy === 'title')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th>Mentor</th>
                                    <th>Mentee</th>
                                    <th wire:click="sortBy('status')" style="cursor: pointer;">
                                        Status
                                        @if ($sortBy === 'status')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('start_date')" style="cursor: pointer;">
                                        Start Date
                                        @if ($sortBy === 'start_date')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('progress_percentage')" style="cursor: pointer;">
                                        Progress
                                        @if ($sortBy === 'progress_percentage')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mentorships as $mentorship)
                                    <tr>
                                        <td>
                                            <div class="icheck-primary">
                                                <input type="checkbox" wire:model="selectedMentorships"
                                                    value="{{ $mentorship->id }}"
                                                    id="mentorship_{{ $mentorship->id }}">
                                                <label for="mentorship_{{ $mentorship->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $mentorship->title }}</strong>
                                            <br>
                                            <small
                                                class="text-muted">{{ $mentorship->description ? Str::limit($mentorship->description, 50) : 'No description' }}</small>
                                        </td>
                                        <td>
                                            @if ($mentorship->mentor)
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2">
                                                        {!! getUserAvatar($mentorship->mentor->user, 30) !!}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $mentorship->mentor->user->full_name }}</strong>
                                                        <br>
                                                        <small
                                                            class="text-muted">{{ $mentorship->mentor->job_title }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-danger">Mentor not found</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($mentorship->mentee)
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2">
                                                        {!! getUserAvatar($mentorship->mentee->user, 30) !!}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $mentorship->mentee->user->full_name }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            @if ($mentorship->mentee->studentProfile)
                                                                {{ $mentorship->mentee->studentProfile->institution_name }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-danger">Mentee not found</span>
                                            @endif
                                        </td>
                                        <td>
                                            {!! getMentorshipStatusBadge($mentorship->status) !!}
                                            @if ($mentorship->is_paid)
                                                <br>
                                                <small class="badge badge-warning">Paid</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($mentorship->start_date)
                                                {{ formatDate($mentorship->start_date) }}
                                                <br>
                                                <small class="text-muted">{{ $mentorship->duration_weeks }}
                                                    weeks</small>
                                            @else
                                                <span class="text-muted">Not started</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-success"
                                                    style="width: {{ $mentorship->progress_percentage }}%"></div>
                                            </div>
                                            <small>{{ number_format($mentorship->progress_percentage, 1) }}%</small>
                                            <br>
                                            <small class="text-muted">
                                                {{ $mentorship->meetings_completed }}/{{ $mentorship->total_meetings }}
                                                sessions
                                            </small>
                                        </td>
                                        <td>
                                            @if ($mentorship->is_paid)
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-money-bill"></i> Paid
                                                </span>
                                            @else
                                                <span class="badge badge-success">
                                                    <i class="fas fa-heart"></i> Free
                                                </span>
                                            @endif
                                            <br>
                                            <small
                                                class="text-muted">{{ $mentorship->meeting_preference_label }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.mentorships.show', $mentorship) }}"
                                                    class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.mentorships.edit', $mentorship) }}"
                                                    class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-secondary btn-sm dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        @if ($mentorship->status === 'active')
                                                            <a class="dropdown-item" href="#"
                                                                wire:click="updateStatus({{ $mentorship->id }}, 'paused')">
                                                                <i class="fas fa-pause"></i> Pause
                                                            </a>
                                                        @elseif($mentorship->status === 'paused')
                                                            <a class="dropdown-item" href="#"
                                                                wire:click="updateStatus({{ $mentorship->id }}, 'active')">
                                                                <i class="fas fa-play"></i> Resume
                                                            </a>
                                                        @endif
                                                        @if (in_array($mentorship->status, ['active', 'paused']))
                                                            <a class="dropdown-item" href="#"
                                                                wire:click="updateStatus({{ $mentorship->id }}, 'completed')">
                                                                <i class="fas fa-check"></i> Mark Complete
                                                            </a>
                                                        @endif
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="#"
                                                            onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                                            wire:click="updateStatus({{ $mentorship->id }}, 'cancelled')">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-hands-helping fa-3x mb-3"></i>
                                                <h4>No mentorships found</h4>
                                                <p>Create your first mentorship or adjust your filters.</p>
                                                <a href="{{ route('admin.mentorships.create') }}"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create New Mentorship
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <div class="float-left">
                        <span class="text-muted">
                            Showing {{ $mentorships->firstItem() ?? 0 }} to {{ $mentorships->lastItem() ?? 0 }}
                            of {{ $mentorships->total() }} entries
                        </span>
                    </div>
                    <div class="float-right">
                        {{ $mentorships->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
