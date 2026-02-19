<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <section class="content">
        <div class="container-fluid">
            <!-- Statistics -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ \App\Models\Mentorship::where('status', 'active')->count() }}</h3>
                            <p>Active Sessions</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ \App\Models\MentorshipSession::whereIn('status', ['scheduled', 'confirmed'])->where('scheduled_start_time', '>', now())->count() }}
                            </h3>
                            <p>Upcoming Sessions</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <a href="{{ route('admin.mentorships.upcoming.sessions') }}" class="small-box-footer">
                            View Upcoming <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ \App\Models\Mentorship::where('status', 'active')->whereHas('sessions', function ($q) {
                                    $q->where('status', 'scheduled')->where('scheduled_start_time', '<=', now()->addDays(7));
                                })->count() }}
                            </h3>
                            <p>Sessions This Week</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Schedule <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ \App\Models\Mentorship::where('status', 'active')->whereHas('sessions', function ($q) {
                                    $q->where('status', 'completed');
                                })->count() }}
                            </h3>
                            <p>Sessions Completed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Completed <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Active Mentorship Sessions</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#scheduleSessionModal">
                            <i class="fas fa-plus"></i> Schedule Session
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                    placeholder="Search active sessions...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select wire:model.live="perPage" class="form-control">
                                    <option value="10">10 per page</option>
                                    <option value="15">15 per page</option>
                                    <option value="25">25 per page</option>
                                    <option value="50">50 per page</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Mentorships Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th wire:click="sortBy('title')" style="cursor: pointer;">
                                        Title
                                        @if ($sortBy === 'title')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th>Mentor → Mentee</th>
                                    <th wire:click="sortBy('start_date')" style="cursor: pointer;">
                                        Started
                                        @if ($sortBy === 'start_date')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th>Progress</th>
                                    <th>Upcoming Session</th>
                                    <th>Sessions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeMentorships as $mentorship)
                                    @php
                                        $nextSession = $mentorship->sessions
                                            ->whereIn('status', ['scheduled', 'confirmed'])
                                            ->where('scheduled_start_time', '>', now())
                                            ->sortBy('scheduled_start_time')
                                            ->first();

                                        $completedSessions = $mentorship->sessions
                                            ->where('status', 'completed')
                                            ->count();
                                        $totalSessions = $mentorship->sessions->count();
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $mentorship->title }}</strong>
                                            <br>
                                            <small
                                                class="text-muted">{{ $mentorship->description ? Str::limit($mentorship->description, 40) : 'No description' }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    {!! getUserAvatar($mentorship->mentor->user, 25) !!}
                                                </div>
                                                <div class="mr-3">
                                                    <small class="text-muted">Mentor</small><br>
                                                    <strong>{{ $mentorship->mentor->user->first_name }}</strong>
                                                </div>
                                                <div class="mr-2">
                                                    <i class="fas fa-arrow-right text-muted"></i>
                                                </div>
                                                <div class="mr-2">
                                                    {!! getUserAvatar($mentorship->mentee->user, 25) !!}
                                                </div>
                                                <div>
                                                    <small class="text-muted">Mentee</small><br>
                                                    <strong>{{ $mentorship->mentee->user->first_name }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($mentorship->start_date)
                                                {{ formatDate($mentorship->start_date) }}
                                                <br>
                                                <small class="text-muted">{{ $mentorship->duration_weeks }} weeks
                                                    program</small>
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
                                                {{ $completedSessions }}/{{ $totalSessions }} sessions
                                            </small>
                                        </td>
                                        <td>
                                            @if ($nextSession)
                                                <div class="text-success">
                                                    <i class="fas fa-calendar-check"></i>
                                                    {{ formatDateTime($nextSession->scheduled_start_time) }}
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $nextSession->meeting_type_label }}</small>
                                                </div>
                                            @else
                                                <span class="text-warning">
                                                    <i class="fas fa-calendar-times"></i> No upcoming session
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $totalSessions }} total</span>
                                            <span class="badge bg-success">{{ $completedSessions }} completed</span>
                                            <span class="badge bg-warning">{{ $totalSessions - $completedSessions }}
                                                remaining</span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.mentorships.show', $mentorship) }}"
                                                    class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-warning btn-sm"
                                                    wire:click="pauseMentorship({{ $mentorship->id }})"
                                                    title="Pause Mentorship">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm"
                                                    wire:click="completeMentorship({{ $mentorship->id }})"
                                                    title="Mark as Complete">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-play-circle fa-3x mb-3"></i>
                                                <h4>No Active Mentorship Sessions</h4>
                                                <p>There are currently no active mentorship sessions.</p>
                                                <a href="{{ route('admin.mentorships.index') }}"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-arrow-left"></i> View All Mentorships
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
                            Showing {{ $activeMentorships->firstItem() ?? 0 }} to
                            {{ $activeMentorships->lastItem() ?? 0 }}
                            of {{ $activeMentorships->total() }} entries
                        </span>
                    </div>
                    <div class="float-right">
                        {{ $activeMentorships->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

     <!-- Schedule Session Modal -->
    <div class="modal fade" id="scheduleSessionModal" tabindex="-1" role="dialog"
        aria-labelledby="scheduleSessionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleSessionModalLabel">Schedule New Session</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Select an active mentorship to schedule a new session.</p>
                    <!-- You would add a form here to schedule sessions -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Session scheduling functionality will be implemented soon.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Schedule Session</button>
                </div>
            </div>
        </div>
    </div>

</div>
