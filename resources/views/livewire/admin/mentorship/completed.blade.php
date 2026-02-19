<div>
    {{-- The Master doesn't talk, he acts. --}}

    <section class="content">
        <div class="container-fluid">
            <!-- Statistics -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $totalCompleted }}</h3>
                            <p>Total Completed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $thisMonthCompleted }}</h3>
                            <p>This Month</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View This Month <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $averageDuration }}</h3>
                            <p>Avg. Duration (weeks)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $successRate }}<sup style="font-size: 20px">%</sup></h3>
                            <p>Success Rate</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Analytics <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Completed Mentorships</h3>
                    <div class="card-tools">
                        @if (count($selectedMentorships) > 0)
                            <div class="btn-group">
                                <button type="button" class="btn btn-warning btn-sm"
                                    onclick="confirm('Archive selected mentorships?') || event.stopImmediatePropagation()"
                                    wire:click="archiveSelected">
                                    <i class="fas fa-archive"></i> Archive Selected ({{ count($selectedMentorships) }})
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                    placeholder="Search...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="mentorFilter" class="form-control">
                                    <option value="">All Mentors</option>
                                    @foreach ($mentors as $mentor)
                                        <option value="{{ $mentor['id'] }}">{{ $mentor['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="menteeFilter" class="form-control">
                                    <option value="">All Mentees</option>
                                    @foreach ($mentees as $mentee)
                                        <option value="{{ $mentee['id'] }}">
                                            {{ $mentee['name'] }} ({{ $mentee['institution'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="dateRange" class="form-control">
                                    <option value="">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="this_week">This Week</option>
                                    <option value="this_month">This Month</option>
                                    <option value="last_month">Last Month</option>
                                    <option value="last_3_months">Last 3 Months</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select wire:model.live="ratingFilter" class="form-control">
                                    <option value="">All Ratings</option>
                                    <option value="5">5 Stars</option>
                                    <option value="4">4+ Stars</option>
                                    <option value="3">3+ Stars</option>
                                    <option value="2">2+ Stars</option>
                                    <option value="1">1+ Stars</option>
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
                    </div>
                </div>
            </div>

            <!-- Completed Mentorships Table -->
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
                                        Mentorship
                                        @if ($sortBy === 'title')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th>Mentor → Mentee</th>
                                    <th wire:click="sortBy('completed_at')" style="cursor: pointer;">
                                        Completed
                                        @if ($sortBy === 'completed_at')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @else
                                            <i class="fas fa-sort"></i>
                                        @endif
                                    </th>
                                    <th>Duration</th>
                                    <th>Sessions</th>
                                    <th>Rating</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedMentorships as $mentorship)
                                    @php
                                        $mentorRating = $mentorship->average_mentor_rating;
                                        $menteeRating = $mentorship->average_mentee_rating;
                                        $avgRating =
                                            $mentorRating && $menteeRating
                                                ? ($mentorRating + $menteeRating) / 2
                                                : $mentorRating ?? $menteeRating;

                                        $durationWeeks =
                                            $mentorship->start_date && $mentorship->completed_at
                                                ? $mentorship->start_date->diffInWeeks($mentorship->completed_at)
                                                : $mentorship->duration_weeks;

                                        $totalSessions = $mentorship->sessions->count();
                                        $completedSessions = $mentorship->sessions
                                            ->where('status', 'completed')
                                            ->count();
                                    @endphp
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
                                            <small class="text-muted">
                                                {{ $mentorship->description ? Str::limit($mentorship->description, 40) : 'No description' }}
                                            </small>
                                            @if ($mentorship->completion_notes)
                                                <br>
                                                <small class="text-info">
                                                    <i class="fas fa-sticky-note"></i> Has completion notes
                                                </small>
                                            @endif
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
                                            @if ($mentorship->completed_at)
                                                {{ formatDate($mentorship->completed_at) }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ $mentorship->completed_at->diffForHumans() }}
                                                </small>
                                            @else
                                                <span class="text-muted">Not recorded</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $durationWeeks }} weeks</span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $mentorship->start_date ? formatDate($mentorship->start_date) : 'No start date' }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <span
                                                    class="badge bg-success">{{ $completedSessions }}/{{ $totalSessions }}</span>
                                                <br>
                                                <small class="text-muted">sessions completed</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($avgRating)
                                                <div class="text-warning">
                                                    {!! getRatingStars($avgRating) !!}
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ number_format($avgRating, 1) }}/5.0</small>
                                                </div>
                                                @if ($mentorship->reviews_count > 0)
                                                    <small class="text-muted">
                                                        {{ $mentorship->reviews_count }} review(s)
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">No ratings yet</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.mentorships.show', $mentorship) }}"
                                                    class="btn btn-info btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-warning btn-sm"
                                                    wire:click="reopenMentorship({{ $mentorship->id }})"
                                                    title="Reopen Mentorship">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                                <a href="{{ route('admin.mentorships.reviews') }}?mentorship={{ $mentorship->id }}"
                                                    class="btn btn-primary btn-sm" title="View Reviews">
                                                    <i class="fas fa-star"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                                <h4>No Completed Mentorships</h4>
                                                <p>There are no completed mentorship programs yet.</p>
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
                            Showing {{ $completedMentorships->firstItem() ?? 0 }} to
                            {{ $completedMentorships->lastItem() ?? 0 }}
                            of {{ $completedMentorships->total() }} entries
                        </span>
                    </div>
                    <div class="float-right">
                        {{ $completedMentorships->links() }}
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Completion Trends</h3>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="border-right">
                                        <div class="text-muted">This Month</div>
                                        <div class="h3">{{ $thisMonthCompleted }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border-right">
                                        <div class="text-muted">Last Month</div>
                                        <div class="h3">{{ $lastMonthCompleted }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="">
                                        <div class="text-muted">Avg. Duration</div>
                                        <div class="h3">{{ $averageDuration }} <small>weeks</small></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Success Metrics</h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <div class="h1">{{ $successRate }}%</div>
                                <div class="text-muted">Success Rate</div>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-success" style="width: {{ $successRate }}%"></div>
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    Percentage of completed mentorships with reviews
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
