<x-layouts.student>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">Placement Journey</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Timeline</li>
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
                        @foreach($timeline as $key => $step)
                            <div class="time-label">
                                @php
                                    $color = match($step['status']) {
                                        'completed' => 'success',
                                        'in_progress' => 'primary',
                                        'action_required' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="bg-{{ $color }}">
                                    {{ $step['date'] ? $step['date']->format('d M. Y') : 'Pending' }}
                                </span>
                            </div>
                            
                            <div>
                                @php
                                    $icon = match($key) {
                                        'registration' => 'fa-user-plus',
                                        'profile' => 'fa-id-card',
                                        'matching' => 'fa-cogs', // Cogs for system/machine
                                        'placement' => 'fa-briefcase',
                                        default => 'fa-circle'
                                    };
                                    $bg = 'bg-' . $color;
                                @endphp

                                <i class="fas {{ $icon }} {{ $bg }}"></i>
                                
                                <div class="timeline-item shadow-sm">
                                    <span class="time"><i class="fas fa-clock"></i> {{ $step['date'] ? $step['date']->diffForHumans() : '' }}</span>
                                    
                                    <h3 class="timeline-header {{ $step['status'] === 'in_progress' ? 'text-primary font-weight-bold' : '' }}">
                                        {{ $step['title'] }}
                                    </h3>

                                    <div class="timeline-body">
                                        {{ $step['description'] }}
                                        
                                        @if($key === 'matching' && $step['status'] === 'in_progress')
                                            <div class="progress progress-xs mt-2 active">
                                                <div class="progress-bar bg-primary progress-bar-striped" style="width: 100%"></div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    @if($key === 'profile' && $step['status'] !== 'completed')
                                        <div class="timeline-footer">
                                            <a href="{{ route('student.profile.edit') }}" class="btn btn-primary btn-sm">Complete Profile</a>
                                        </div>
                                    @endif

                                    @if($key === 'matching' && $step['status'] === 'action_required')
                                        <div class="timeline-footer">
                                            <a href="{{ route('student.placement.status') }}" class="btn btn-warning btn-sm font-weight-bold">
                                                <i class="fas fa-eye mr-1"></i> Review Match
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.student>