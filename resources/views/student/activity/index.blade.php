<x-layouts.student>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">Activity Log</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Activity</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    
                    @if($groupedActivities->isEmpty())
                        <div class="col-md-12 text-center py-5">
                            <i class="fas fa-history fa-4x text-muted opacity-50 mb-3"></i>
                            <h5 class="text-muted">No activity recorded yet.</h5>
                            <p class="small text-muted">Actions you take on the platform will appear here.</p>
                        </div>
                    @else
                        <!-- The Timeline -->
                        <div class="timeline">
                            @foreach($groupedActivities as $date => $logs)
                                <!-- Timeline Time Label -->
                                <div class="time-label">
                                    <span class="bg-secondary">
                                        @if($date == date('Y-m-d'))
                                            Today
                                        @elseif($date == date('Y-m-d', strtotime('-1 day')))
                                            Yesterday
                                        @else
                                            {{ \Carbon\Carbon::parse($date)->format('d M. Y') }}
                                        @endif
                                    </span>
                                </div>

                                @foreach($logs as $log)
                                    <!-- Timeline Item -->
                                    <div>
                                        <!-- Dynamic Icon based on Event -->
                                        @php
                                            $iconClass = match($log->event) {
                                                'created' => 'fas fa-plus bg-success',
                                                'updated' => 'fas fa-edit bg-warning',
                                                'deleted' => 'fas fa-trash bg-danger',
                                                'login'   => 'fas fa-sign-in-alt bg-info',
                                                'logout'  => 'fas fa-sign-out-alt bg-secondary',
                                                default   => 'fas fa-circle bg-gray'
                                            };
                                        @endphp
                                        <i class="{{ $iconClass }}"></i>

                                        <div class="timeline-item shadow-sm">
                                            <span class="time"><i class="fas fa-clock"></i> {{ $log->created_at->format('H:i') }}</span>
                                            
                                            <h3 class="timeline-header no-border">
                                                <span class="font-weight-bold text-primary">{{ ucfirst($log->log_name) }}</span>
                                                {{ $log->description }}
                                            </h3>

                                            <!-- Optional: Show changes if it's an update -->
                                            @if($log->event === 'updated' && !empty($log->properties['attributes']))
                                                <div class="timeline-body small text-muted">
                                                    changed 
                                                    @foreach(array_keys($log->properties['attributes'] ?? []) as $key)
                                                        <span class="badge badge-light border">{{ str_replace('_', ' ', $key) }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach

                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $activities->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>
</x-layouts.student>