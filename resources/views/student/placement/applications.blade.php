<x-layouts.student>
    @push('styles')
        <style>
            .application-card {
                transition: all 0.3s ease;
                border: none;
                border-radius: 12px;
            }

            .application-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            }

            .status-badge {
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .match-score-circle {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 18px;
            }

            .stats-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                border-radius: 15px;
            }

            .stats-card-success {
                background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            }

            .stats-card-warning {
                background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            }

            .stats-card-danger {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            }

            .stats-card-info {
                background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
            }
        </style>
    @endpush

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">My Applications</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('student.placement.status') }}">Placement
                                Status</a></li>
                        <li class="breadcrumb-item active">Applications</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="stats-card text-white rounded-lg p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Total Applications</h6>
                                <h2 class="text-white mb-0 font-weight-bold">{{ $stats['total'] }}</h2>
                            </div>
                            <div>
                                <i class="fas fa-file-alt fa-3x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="stats-card-success text-white rounded-lg p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Active</h6>
                                <h2 class="text-white mb-0 font-weight-bold">{{ $stats['active'] }}</h2>
                            </div>
                            <div>
                                <i class="fas fa-spinner fa-3x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="stats-card-info text-white rounded-lg p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Accepted/Placed</h6>
                                <h2 class="text-white mb-0 font-weight-bold">{{ $stats['accepted'] + $stats['hired'] }}
                                </h2>
                            </div>
                            <div>
                                <i class="fas fa-check-circle fa-3x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="stats-card-danger text-white rounded-lg p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Rejected</h6>
                                <h2 class="text-white mb-0 font-weight-bold">{{ $stats['rejected'] }}</h2>
                            </div>
                            <div>
                                <i class="fas fa-times-circle fa-3x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Active Placement Alert -->
            @if ($currentPlacement)
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                    <i class="fas fa-briefcase mr-2"></i>
                    <strong>You are currently on attachment!</strong>
                    You're placed at <strong>{{ $currentPlacement->organization->name }}</strong> until
                    <strong>{{ $currentPlacement->end_date->format('d M, Y') }}</strong>.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Applications List -->
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold text-dark">
                            <i class="fas fa-list-alt text-primary mr-2"></i>
                            All Applications
                        </h5>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('student.placement.applications') }}">All</a>
                                <a class="dropdown-item"
                                    href="{{ route('student.placement.applications', ['status' => 'active']) }}">Active</a>
                                <a class="dropdown-item"
                                    href="{{ route('student.placement.applications', ['status' => 'accepted']) }}">Accepted/Placed</a>
                                <a class="dropdown-item"
                                    href="{{ route('student.placement.applications', ['status' => 'rejected']) }}">Rejected</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if ($applications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($applications as $application)
                                <div class="list-group-item p-4 border-bottom">
                                    <div class="row align-items-center">
                                        <!-- Organization Logo/Icon -->
                                        <div class="col-md-1 text-center mb-3 mb-md-0">
                                            <div class="avatar-circle bg-primary-soft mx-auto"
                                                style="width: 50px; height: 50px; line-height: 50px; font-size: 20px;">
                                                {{ substr($application->opportunity->organization->name, 0, 2) }}
                                            </div>
                                        </div>

                                        <!-- Application Details -->
                                        <div class="col-md-7 mb-3 mb-md-0">
                                            <h6 class="mb-1 font-weight-bold">
                                                <a href="{{ route('student.placement.applications.show', $application->id) }}"
                                                    class="text-dark text-decoration-none hover-primary">
                                                    {{ $application->opportunity->title }}
                                                </a>
                                            </h6>
                                            <p class="text-muted small mb-1">
                                                <i class="fas fa-building mr-1"></i>
                                                {{ $application->opportunity->organization->name }}
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $application->opportunity->location }}
                                            </p>
                                            <div class="d-flex flex-wrap gap-2 mt-2">
                                                <span class="text-muted small">
                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                    Applied: {{ $application->created_at->format('d M, Y') }}
                                                </span>
                                                @if ($application->match_score)
                                                    <span class="text-muted small ml-3">
                                                        <i class="fas fa-chart-line mr-1"></i>
                                                        Match: {{ $application->match_score }}%
                                                    </span>
                                                @endif
                                                @if ($application->interviews->count() > 0)
                                                    <span class="text-muted small ml-3">
                                                        <i class="fas fa-calendar-check mr-1"></i>
                                                        {{ $application->interviews->count() }} Interview(s)
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="col-md-2 mb-3 mb-md-0">
                                            <span class="status-badge"
                                                style="background: {{ $application->status->color() }}20; color: {{ $application->status->color() }};">
                                                <i class="fas {{ $application->status->icon() }} mr-1"></i>
                                                {{ $application->status->label() }}
                                            </span>
                                        </div>

                                        <!-- Action Button -->
                                        <div class="col-md-2 text-md-right">
                                            <a href="{{ route('student.placement.applications.show', $application->id) }}"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="fas fa-eye mr-1"></i> View Details
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Progress Bar for Active Applications -->
                                    @if (in_array($application->status->value, [
                                            'pending',
                                            'under_review',
                                            'shortlisted',
                                            'interview_scheduled',                                            'interview_completed',
                                            'offer_sent',
                                        ]))
                                        <div class="mt-3 pt-2">
                                            <div class="d-flex justify-content-between small text-muted mb-1">
                                                <span>Application Progress</span>
                                                <span>{{ $application->getProgressPercentage() ?? 0 }}%</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                    style="width: {{ $application->getProgressPercentage() ?? 0 }}%; background-color: {{ $application->status->color() }};">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer bg-white border-0 py-3">
                            {{ $applications->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No Applications Yet</h5>
                            <p class="text-muted mb-4">Your applications will appear here once the system matches you
                                with opportunities.</p>
                            <a href="{{ route('student.placement.status') }}" class="btn btn-primary">
                                <i class="fas fa-chart-line mr-2"></i> Check Placement Status
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @push('styles')
        <style>
            .bg-primary-soft {
                background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
                color: #667eea;
            }

            .hover-primary:hover {
                color: #667eea !important;
            }

            .gap-2 {
                gap: 0.5rem;
            }
        </style>
    @endpush
</x-layouts.student>
