<x-layouts.student>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0 text-dark font-weight-bold">Upcoming Sessions</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Sessions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="timeline">
                        @forelse($sessions as $session)
                            <div class="time-label">
                                <span class="bg-primary">{{ $session->scheduled_start_time->format('d M, Y') }}</span>
                            </div>
                            <div>
                                <i class="fas fa-video bg-blue"></i>
                                <div class="timeline-item shadow-sm">
                                    <span class="time"><i class="fas fa-clock"></i> {{ $session->scheduled_start_time->format('H:i A') }}</span>
                                    <h3 class="timeline-header"><a href="#">{{ $session->title }}</a> with {{ $session->mentorship->mentor->user->full_name }}</h3>

                                    <div class="timeline-body">
                                        {{ $session->description ?? 'Regular check-in session.' }}
                                        <div class="mt-2">
                                            <span class="badge badge-light border">
                                                <i class="fas fa-video mr-1"></i> {{ $session->meeting_platform }}
                                            </span>
                                            <span class="badge badge-light border">
                                                <i class="fas fa-hourglass-half mr-1"></i> {{ $session->scheduled_start_time->diffInMinutes($session->scheduled_end_time) }} mins
                                            </span>
                                        </div>
                                    </div>

                                    <div class="timeline-footer">
                                        <a href="{{ $session->meeting_link }}" target="_blank" class="btn btn-primary btn-sm">
                                            <i class="fas fa-external-link-alt mr-1"></i> Join Meeting
                                        </a>
                                        <a href="#" class="btn btn-danger btn-sm">Reschedule</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="time-label">
                                <span class="bg-gray">Today</span>
                            </div>
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header no-border">No upcoming sessions scheduled.</h3>
                                </div>
                            </div>
                        @endforelse
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.student>
