<div>
    {{-- Do your work, then step back. --}}
    <div class="container-fluid">
        <!-- Filters Card -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>Filter Applications
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-toggle="collapse" data-target="#filters">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body collapse show" id="filters">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" wire:model.live="startDate" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" wire:model.live="endDate" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>View Type</label>
                            <select wire:model.live="viewType" class="form-control">
                                <option value="monthly">Monthly</option>
                                <option value="weekly">Weekly</option>
                                <option value="daily">Daily</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select wire:model.live="selectedStatus" class="form-control">
                                <option value="">All Statuses</option>
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Organization</label>
                            <select wire:model.live="selectedOrganization" class="form-control">
                                <option value="">All Organizations</option>
                                @foreach ($organizations as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Opportunity</label>
                            <select wire:model.live="selectedOpportunity" class="form-control">
                                <option value="">All Opportunities</option>
                                @foreach ($opportunities as $id => $title)
                                    <option value="{{ $id }}">{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Course</label>
                            <select wire:model.live="selectedCourse" class="form-control">
                                <option value="">All Courses</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course }}">{{ $course }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Match Score Range: {{ $minMatchScore }}% - {{ $maxMatchScore }}%</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="range" wire:model.live="minMatchScore" min="0" max="100"
                                        step="1" class="form-control-range">
                                </div>
                                <div class="col-6">
                                    <input type="range" wire:model.live="maxMatchScore" min="0" max="100"
                                        step="1" class="form-control-range">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-secondary" wire:click="resetFilters">
                            <i class="fas fa-undo mr-1"></i>Reset Filters
                        </button>
                        <button class="btn btn-success" wire:click="exportToCsv">
                            <i class="fas fa-download mr-1"></i>Export CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($applicationStats['total_applications'] ?? 0) }}</h3>
                        <p>Total Applications</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="small-box-footer">
                        <small>{{ $applicationStats['unique_students'] ?? 0 }} unique students</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $applicationStats['acceptance_rate'] ?? 0 }}%</h3>
                        <p>Acceptance Rate</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="small-box-footer">
                        <small>{{ $applicationStats['accepted_applications'] ?? 0 }} accepted</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $applicationStats['avg_match_score'] ?? 0 }}%</h3>
                        <p>Avg Match Score</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="small-box-footer">
                        <small>{{ $applicationStats['applications_per_opportunity'] ?? 0 }} per opportunity</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $applicationStats['avg_review_time_days'] ?? 0 }}d</h3>
                        <p>Avg Review Time</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="small-box-footer">
                        <small>{{ $applicationStats['avg_review_time_hours'] ?? 0 }} hours</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Application Trends</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="applicationTrendsChart" style="min-height: 300px; height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Status Distribution</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="statusDistributionChart" style="min-height: 300px; height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Match Score Distribution</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="matchScoreChart" style="min-height: 250px; height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Top Opportunities</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Opportunity</th>
                                        <th>Applications</th>
                                        <th>Acceptance</th>
                                        <th>Avg Match</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topOpportunities as $opp)
                                        <tr>
                                            <td>
                                                <strong>{{ Str::limit($opp->title, 25) }}</strong>
                                                <br><small>{{ $opp->organization }}</small>
                                            </td>
                                            <td>{{ $opp->total_applications }}</td>
                                            <td>
                                                <span class="badge badge-success">{{ $opp->acceptance_rate }}%</span>
                                            </td>
                                            <td>{{ $opp->avg_match_score }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Tables -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">By Course</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Applications</th>
                                        <th>Accepted</th>
                                        <th>Rate</th>
                                        <th>Avg Match</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($applicationByCourse as $course)
                                        <tr>
                                            <td>{{ Str::limit($course['course'] ?? 'Unknown', 30) }}</td>
                                            <td>{{ $course['total_applications'] ?? 0 }}</td>
                                            <td>{{ $course['accepted'] ?? 0 }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ ($course['acceptance_rate'] ?? 0) >= 50 ? 'success' : 'warning' }}">
                                                    {{ $course['acceptance_rate'] ?? 0 }}%
                                                </span>
                                            </td>
                                            <td>{{ $course['avg_match_score'] ?? 0 }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">By Organization</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Organization</th>
                                        <th>Applications</th>
                                        <th>Accepted</th>
                                        <th>Rate</th>
                                        <th>Apps/Opp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($applicationByOrganization as $org)
                                        <tr>
                                            <td>{{ Str::limit($org['name'] ?? 'Unknown', 30) }}</td>
                                            <td>{{ $org['total_applications'] ?? 0 }}</td>
                                            <td>{{ $org['accepted'] ?? 0 }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ ($org['acceptance_rate'] ?? 0) >= 50 ? 'success' : 'warning' }}">
                                                    {{ $org['acceptance_rate'] ?? 0 }}%
                                                </span>
                                            </td>
                                            <td>{{ $org['avg_applications_per_opportunity'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Recent Applications</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Opportunity</th>
                                <th>Organization</th>
                                <th>Match</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Reviewed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentApplications as $app)
                                <tr>
                                    <td>
                                        <strong>{{ $app->student_name }}</strong>
                                        <br><small>{{ $app->student_email }}</small>
                                    </td>
                                    <td>{{ Str::limit($app->opportunity_title, 30) }}</td>
                                    <td>{{ $app->organization }}</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $app->match_score >= 80 ? 'success' : ($app->match_score >= 60 ? 'info' : 'warning') }}">
                                            {{ $app->match_score }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $app->status_badge }}">
                                            {{ ucfirst($app->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $app->submitted_at }}</td>
                                    <td>{{ $app->reviewed_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No recent applications found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Chart.js Scripts -->
        @push('scripts')
            <script>
                document.addEventListener('livewire:initialized', function() {
                    let trendsChart, statusChart, matchScoreChart;

                    function initCharts() {
                        const trendsCtx = document.getElementById('applicationTrendsChart')?.getContext('2d');
                        const statusCtx = document.getElementById('statusDistributionChart')?.getContext('2d');
                        const matchCtx = document.getElementById('matchScoreChart')?.getContext('2d');

                        // Trends Chart
                        if (trendsCtx) {
                            if (trendsChart) trendsChart.destroy();
                            trendsChart = new Chart(trendsCtx, {
                                type: 'line',
                                data: @json($applicationTrends),
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'top'
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        }

                        // Status Distribution Chart
                        if (statusCtx) {
                            if (statusChart) statusChart.destroy();

                            const statusData = @json($applicationStatusDistribution);

                            statusChart = new Chart(statusCtx, {
                                type: 'doughnut',
                                data: {
                                    labels: statusData.map(item => item.status),
                                    datasets: [{
                                        data: statusData.map(item => item.count),
                                        backgroundColor: statusData.map(item => item.color),
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const item = statusData[context.dataIndex];
                                                    return `${item.status}: ${item.count} (${item.percentage}%)`;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        // Match Score Distribution Chart
                        if (matchCtx) {
                            if (matchScoreChart) matchScoreChart.destroy();

                            const matchData = @json($matchScoreDistribution);

                            matchScoreChart = new Chart(matchCtx, {
                                type: 'bar',
                                data: {
                                    labels: matchData.map(item => item.range),
                                    datasets: [{
                                        label: 'Applications',
                                        data: matchData.map(item => item.count),
                                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                        borderColor: 'rgb(59, 130, 246)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    const item = matchData[context.dataIndex];
                                                    return `${context.raw} applications (${item.percentage}%)`;
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        }
                    }

                    // Initialize charts on page load
                    initCharts();

                    // Refresh charts when data is updated
                    Livewire.on('applicationChartsUpdated', function() {
                        setTimeout(() => {
                            initCharts();
                        }, 100);
                    });
                });
            </script>
        @endpush
    </div>
</div>
