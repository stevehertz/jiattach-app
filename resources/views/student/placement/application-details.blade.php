<x-layouts.student>
    @push('styles')
        <style>
            .timeline-modern {
                position: relative;
                padding-left: 20px;
            }

            .timeline-modern:before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 2px;
                background: #e9ecef;
            }

            .timeline-item-modern {
                position: relative;
                padding-left: 30px;
                margin-bottom: 30px;
            }

            .timeline-dot {
                position: absolute;
                left: -6px;
                top: 0;
                width: 14px;
                height: 14px;
                border-radius: 50%;
                border: 2px solid white;
                z-index: 1;
            }

            .timeline-content {
                background: #f8f9fa;
                border-radius: 10px;
                padding: 15px;
            }

            .status-step {
                position: relative;
                margin-bottom: 20px;
                display: flex;
                align-items: flex-start;
            }

            .step-indicator {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #e9ecef;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 15px;
                z-index: 1;
            }

            .status-step.completed .step-indicator {
                background: #28a745;
                color: white;
            }

            .status-step.current .step-indicator {
                background: #667eea;
                color: white;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
            }

            .step-content {
                flex: 1;
            }
        </style>
    @endpush

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">
                        Application Details
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('student.dashboard') }}">
                                Home
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('student.placement.applications') }}">
                                My Applications
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            #{{ $application->id }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Application Header -->
                    <div class="card shadow-sm border-0 rounded-lg mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h3 class="mb-1 font-weight-bold">{{ $application->opportunity->title }}</h3>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-building mr-1"></i>
                                        {{ $application->opportunity->organization->name }}
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        {{ $application->opportunity->location }}
                                    </p>
                                </div>
                                <span class="status-badge p-2"
                                    style="background: {{ $application->status->color() }}20; color: {{ $application->status->color() }};">
                                    <i class="fas {{ $application->status->icon() }} mr-1"></i>
                                    {{ $application->status->label() }}
                                </span>
                            </div>

                            @if ($application->match_score)
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">Match Score</span>
                                                <span class="font-weight-bold">{{ $application->match_score }}%</span>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success"
                                                    style="width: {{ $application->match_score }}%"></div>
                                            </div>
                                            <small class="text-muted mt-2 d-block">
                                                Based on your skills, course, and location preferences
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">Applied On</span>
                                                <span
                                                    class="font-weight-bold">{{ $application->created_at->format('d M, Y h:i A') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <span class="text-muted">Last Updated</span>
                                                <span>{{ $application->updated_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Application Timeline -->
                    <div class="card shadow-sm border-0 rounded-lg mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fas fa-history text-primary mr-2"></i>
                                Application Timeline
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($application->history->count() > 0)
                                <div class="timeline-modern">
                                    @foreach ($application->history as $history)
                                        <div class="timeline-item-modern">
                                            <div class="timeline-dot"
                                                style="background: {{ $history->new_status_enum?->color() ?? '#6c757d' }};">
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <div>
                                                        <strong>{{ $history->user?->full_name ?? 'System' }}</strong>
                                                        <span class="text-muted mx-2">•</span>
                                                        <span
                                                            class="text-muted">{{ $history->created_at->format('h:i A') }}</span>
                                                    </div>
                                                </div>
                                                <p class="mb-2">{{ $history->notes ?? $history->action }}</p>
                                                @if ($history->old_status_label && $history->new_status_label)
                                                    <div class="small">
                                                        <span
                                                            class="badge badge-secondary">{{ $history->old_status_label }}</span>
                                                        <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                        <span class="badge"
                                                            style="background: {{ $history->new_status_enum?->color() }}; color: white;">
                                                            {{ $history->new_status_label }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No timeline events yet</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Interviews Section -->
                    @if ($application->interviews->count() > 0)
                        <div class="card shadow-sm border-0 rounded-lg">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="mb-0 font-weight-bold">
                                    <i class="fas fa-calendar-alt text-warning mr-2"></i>
                                    Interviews ({{ $application->interviews->count() }})
                                </h5>
                            </div>
                            <div class="card-body">
                                @foreach ($application->interviews as $interview)
                                    <div class="border rounded p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 font-weight-bold">
                                                    <i class="fas {{ $interview->type_icon }} mr-1"></i>
                                                    {{ ucfirst($interview->type) }} Interview
                                                </h6>
                                                <p class="mb-1">
                                                    <i class="fas fa-calendar mr-1 text-muted"></i>
                                                    {{ $interview->scheduled_at->format('l, d M Y') }}
                                                    <span class="mx-2">•</span>
                                                    <i class="fas fa-clock mr-1 text-muted"></i>
                                                    {{ $interview->scheduled_at->format('h:i A') }}
                                                    <span class="mx-2">•</span>
                                                    <i class="fas fa-hourglass-half mr-1 text-muted"></i>
                                                    {{ $interview->duration_formatted }}
                                                </p>
                                                @if ($interview->meeting_details)
                                                    <p class="mb-1">
                                                        <i class="fas fa-link mr-1 text-muted"></i>
                                                        <a href="{{ $interview->meeting_details }}" target="_blank"
                                                            class="text-primary">
                                                            {{ $interview->meeting_details }}
                                                        </a>
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="badge"
                                                style="background: {{ $interview->status->color() }}20; color: {{ $interview->status->color() }};">
                                                {{ $interview->status->label() }}
                                            </span>
                                        </div>

                                        @if ($interview->outcome)
                                            <hr class="my-3">
                                            <div class="bg-light p-2 rounded">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="font-weight-bold">Outcome:</span>
                                                        <span class="badge ml-2"
                                                            style="background: {{ $interview->outcome->outcome_enum?->color() }}; color: white;">
                                                            {{ $interview->outcome->outcome_enum?->label() }}
                                                        </span>
                                                        @if ($interview->outcome->rating)
                                                            <span class="ml-3">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    <i
                                                                        class="fas fa-star {{ $i <= $interview->outcome->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                                @endfor
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if ($interview->outcome->feedback)
                                                    <p class="mb-0 mt-2 small text-muted">
                                                        <i class="fas fa-comment mr-1"></i>
                                                        {{ $interview->outcome->feedback }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Opportunity Details Card -->
                    <div class="card shadow-sm border-0 rounded-lg mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fas fa-briefcase text-primary mr-2"></i>
                                Opportunity Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><i class="fas fa-calendar-alt text-muted mr-2"></i>Start Date
                                    </td>
                                    <td>{{ $application->opportunity->start_date?->format('d M, Y') ?? 'Flexible' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-times text-muted mr-2"></i>End Date</td>
                                    <td>{{ $application->opportunity->end_date?->format('d M, Y') ?? 'Flexible' }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-clock text-muted mr-2"></i>Duration</td>
                                    <td>{{ $application->opportunity->duration_months ?? 'N/A' }} months</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-money-bill text-muted mr-2"></i>Stipend</td>
                                    <td>{{ $application->opportunity->stipend ? 'KSh ' . number_format($application->opportunity->stipend, 2) : 'Not specified' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-tag text-muted mr-2"></i>Work Type</td>
                                    <td>{{ ucfirst($application->opportunity->work_type ?? 'Not specified') }}</td>
                                </tr>
                            </table>

                            @if ($application->opportunity->description)
                                <hr>
                                <h6 class="font-weight-bold mb-2">Description</h6>
                                <p class="small text-muted">
                                    {{ Str::limit($application->opportunity->description, 200) }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Required Skills Card -->
                    @if ($application->opportunity->skills_required && count($application->opportunity->skills_required) > 0)
                        <div class="card shadow-sm border-0 rounded-lg mb-4">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="mb-0 font-weight-bold">
                                    <i class="fas fa-code text-primary mr-2"></i>
                                    Required Skills
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($application->opportunity->skills_required as $skill)
                                        <span class="badge badge-info p-2">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Placement Card if exists -->
                    @if ($application->placement)
                        <div class="card shadow-sm border-0 rounded-lg mb-4 bg-success-soft">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                                <h6 class="font-weight-bold">Placement Confirmed!</h6>
                                <p class="small mb-2">
                                    You have been placed at {{ $application->placement->organization->name }}
                                </p>
                                <a href="{{ route('student.placement.status') }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-eye mr-1"></i> View Placement
                                </a>
                            </div>
                        </div>
                    @endif
                    
                    @if ($application->hasPaymentRequired())
                        <div class="card shadow-sm border-0 rounded-lg mb-4 bg-warning-soft">
                            <div class="card-body text-center">
                                <i class="fas fa-credit-card fa-3x text-success mb-2"></i>
                                <h6 class="font-weight-bold">Payment Required</h6>
                                <p class="small mb-3">
                                    Complete payment to receive your offer letter.
                                    <br>Fee: <strong>KSh
                                        {{ number_format(config('payments.attachment_fee', 1500), 2) }}</strong>
                                </p>
                                <a href="#"
                                    class="btn btn-success btn-block">
                                    <i class="fas fa-credit-card mr-1"></i> Make Payment
                                </a>
                            </div>
                        </div>
                    @endif

                    @if ($application->status === \App\Enums\ApplicationStatus::OFFER_SENT)
                        <div class="card shadow-sm border-0 rounded-lg mb-4 bg-success-soft">
                            <div class="card-body text-center">
                                <i class="fas fa-file-alt fa-3x text-success mb-2"></i>
                                <h6 class="font-weight-bold">Offer Letter Ready!</h6>
                                <p class="small mb-3">
                                    Your offer letter has been generated. Click below to download.
                                </p>
                                <button class="btn btn-success btn-block"
                                    onclick="downloadOfferLetter({{ $application->id }})">
                                    <i class="fas fa-download mr-1"></i> Download Offer Letter
                                </button>
                            </div>
                        </div>
                    @endif



                    <!-- Similar Applications -->
                    @if ($similarApplications->count() > 0)
                        <div class="card shadow-sm border-0 rounded-lg">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="mb-0 font-weight-bold">
                                    <i class="fas fa-clock text-primary mr-2"></i>
                                    Other Active Applications
                                </h5>
                            </div>
                            <div class="list-group list-group-flush">
                                @foreach ($similarApplications as $app)
                                    <a href="{{ route('student.placement.applications.show', $app->id) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $app->opportunity->title }}</strong>
                                                <div class="small text-muted">
                                                    {{ $app->opportunity->organization->name }}</div>
                                            </div>
                                            <span class="badge"
                                                style="background: {{ $app->status->color() }}20; color: {{ $app->status->color() }};">
                                                {{ $app->status->label() }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-layouts.student>
