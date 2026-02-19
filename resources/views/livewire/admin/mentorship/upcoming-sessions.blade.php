<div>
    {{-- The Master doesn't talk, he acts. --}}
    <section class="content">
        <div class="container-fluid">
            <!-- Statistics -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $totalUpcoming }}</h3>
                            <p>Total Upcoming</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $todaySessions }}</h3>
                            <p>Today's Sessions</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Today <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $tomorrowSessions }}</h3>
                            <p>Tomorrow's Sessions</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Tomorrow <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $thisWeekSessions }}</h3>
                            <p>This Week</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View This Week <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Upcoming Sessions</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#scheduleSessionModal">
                            <i class="fas fa-plus"></i> Schedule Session
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" wire:model.live.debounce.300ms="search" 
                                       class="form-control" placeholder="Search sessions...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="mentorshipFilter" class="form-control">
                                    <option value="">All Mentorships</option>
                                    @foreach($mentorships as $mentorship)
                                        <option value="{{ $mentorship->id }}">
                                            {{ $mentorship->title }} ({{ $mentorship->mentor->user->first_name }} → {{ $mentorship->mentee->user->first_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="statusFilter" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="confirmed">Confirmed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="dateRange" class="form-control">
                                    <option value="">All Dates</option>
                                    <option value="today">Today</option>
                                    <option value="tomorrow">Tomorrow</option>
                                    <option value="this_week">This Week</option>
                                    <option value="next_week">Next Week</option>
                                    <option value="this_month">This Month</option>
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
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            @if(count($selectedSessions) > 0)
                                <div class="form-group">
                                    <button type="button" class="btn btn-danger btn-block" 
                                            onclick="confirm('Cancel selected sessions?') || event.stopImmediatePropagation()"
                                            wire:click="cancelSelected">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Sessions Table -->
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
                                    <th wire:click="sortBy('scheduled_start_time')" style="cursor: pointer;">
                                        Date & Time
                                        @if($sortBy === 'scheduled_start_time')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th>Session Details</th>
                                    <th>Mentorship</th>
                                    <th wire:click="sortBy('status')" style="cursor: pointer;">
                                        Status
                                        @if($sortBy === 'status')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th>Meeting Details</th>
                                    <th>Time Until</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingSessions as $session)
                                    @php
                                        $timeUntil = $session->minutes_until_start;
                                        $timeClass = 'text-success';
                                        if ($timeUntil <= 60) $timeClass = 'text-warning';
                                        if ($timeUntil <= 30) $timeClass = 'text-danger';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="icheck-primary">
                                                <input type="checkbox" 
                                                       wire:model="selectedSessions" 
                                                       value="{{ $session->id }}" 
                                                       id="session_{{ $session->id }}">
                                                <label for="session_{{ $session->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ formatDateTime($session->scheduled_start_time) }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                Duration: {{ $session->duration_minutes }} minutes
                                            </small>
                                        </td>
                                        <td>
                                            <strong>{{ $session->title }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $session->description ? Str::limit($session->description, 50) : 'No description' }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($session->mentorship)
                                                <div>
                                                    <strong>{{ $session->mentorship->title }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $session->mentorship->mentor->user->first_name }} → 
                                                        {{ $session->mentorship->mentee->user->first_name }}
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-danger">Mentorship not found</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($session->status === 'scheduled')
                                                <span class="badge badge-warning">Scheduled</span>
                                            @elseif($session->status === 'confirmed')
                                                <span class="badge badge-success">Confirmed</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">{{ $session->session_type_label }}</small>
                                        </td>
                                        <td>
                                            @if($session->meeting_type === 'video' && $session->meeting_link)
                                                <span class="badge badge-info">
                                                    <i class="fas fa-video"></i> Video Call
                                                </span>
                                                <br>
                                                <small class="text-muted">Link available</small>
                                            @elseif($session->meeting_type === 'in_person' && $session->meeting_location)
                                                <span class="badge badge-primary">
                                                    <i class="fas fa-map-marker-alt"></i> In Person
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ $session->meeting_location }}</small>
                                            @elseif($session->meeting_type === 'phone')
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-phone"></i> Phone Call
                                                </span>
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </td>
                                        <td class="{{ $timeClass }}">
                                            @if($timeUntil > 0)
                                                @if($timeUntil > 1440)
                                                    {{ floor($timeUntil / 1440) }} days
                                                @elseif($timeUntil > 60)
                                                    {{ floor($timeUntil / 60) }} hours
                                                @else
                                                    {{ $timeUntil }} minutes
                                                @endif
                                            @else
                                                <span class="text-danger">Starting now!</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                {{ $session->time_status }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @if($session->status === 'scheduled')
                                                    <button type="button" class="btn btn-success btn-sm"
                                                            wire:click="confirmSession({{ $session->id }})"
                                                            title="Confirm Session">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-warning btn-sm"
                                                        wire:click="markAsMissed({{ $session->id }})"
                                                        title="Mark as Missed">
                                                    <i class="fas fa-clock"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="confirm('Are you sure you want to cancel this session?') || event.stopImmediatePropagation()"
                                                        wire:click="cancelSession({{ $session->id }})"
                                                        title="Cancel Session">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                                                <h4>No Upcoming Sessions</h4>
                                                <p>There are no upcoming mentorship sessions scheduled.</p>
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#scheduleSessionModal">
                                                    <i class="fas fa-plus"></i> Schedule New Session
                                                </button>
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
                            Showing {{ $upcomingSessions->firstItem() ?? 0 }} to {{ $upcomingSessions->lastItem() ?? 0 }} 
                            of {{ $upcomingSessions->total() }} entries
                        </span>
                    </div>
                    <div class="float-right">
                        {{ $upcomingSessions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

     <!-- Schedule Session Modal -->
    <div class="modal fade" id="scheduleSessionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule New Session</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Session scheduling functionality will be implemented in the next update.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
