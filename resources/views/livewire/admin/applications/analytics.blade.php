<div>
    {{-- In work, do what you enjoy. --}}
    @push('styles')
        <style>
            .stat-card {
                background: white;
                border-radius: 15px;
                transition: transform 0.3s, box-shadow 0.3s;
                overflow: hidden;
            }

            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
            }

            .growth-positive {
                color: #28a745;
                background: #d4edda;
                padding: 2px 8px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }

            .growth-negative {
                color: #dc3545;
                background: #f8d7da;
                padding: 2px 8px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }

            .chart-container {
                position: relative;
                height: 300px;
                width: 100%;
            }

            .metric-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-radius: 15px;
                padding: 20px;
            }

            .filter-bar {
                background: white;
                border-radius: 12px;
                padding: 15px;
                margin-bottom: 20px;
            }
        </style>
    @endpush

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-line text-primary mr-2"></i>Analytics Dashboard
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.applications.index') }}">Applications</a>
                        </li>
                        <li class="breadcrumb-item active">Analytics</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Filter Bar -->
            <div class="filter-bar shadow-sm">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="font-weight-bold mb-1">Date Range</label>
                        <select wire:model="dateRange" class="form-control">
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="this_quarter">This Quarter</option>
                            <option value="this_year">This Year</option>
                            <option value="last_year">Last Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    @if ($showCustomDatePicker)
                        <div class="col-md-3">
                            <label class="font-weight-bold mb-1">From</label>
                            <input type="date" wire:model="customStartDate" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="font-weight-bold mb-1">To</label>
                            <input type="date" wire:model="customEndDate" class="form-control">
                        </div>
                    @endif
                    <div class="col-md-3">
                        <label class="font-weight-bold mb-1">Organization</label>
                        <select wire:model="organizationFilter" class="form-control">
                            <option value="all">All Organizations</option>
                            @foreach ($organizations as $org)
                                <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Key Metrics Row -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted text-uppercase small font-weight-bold">Total
                                    Applications</span>
                                <h2 class="mb-0 font-weight-bold mt-1">{{ number_format($totalApplications) }}</h2>
                                <small class="text-muted">
                                    <i
                                        class="fas {{ $applicationsGrowth >= 0 ? 'fa-arrow-up text-success' : 'fa-arrow-down text-danger' }} mr-1"></i>
                                    {{ abs($applicationsGrowth) }}% from last month
                                </small>
                            </div>
                            <div class="stat-icon bg-primary-soft">
                                <i class="fas fa-file-alt text-primary fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted text-uppercase small font-weight-bold">Total
                                    Placements</span>
                                <h2 class="mb-0 font-weight-bold mt-1">{{ number_format($totalPlacements) }}</h2>
                                <small class="text-muted">
                                    <i
                                        class="fas {{ $placementsGrowth >= 0 ? 'fa-arrow-up text-success' : 'fa-arrow-down text-danger' }} mr-1"></i>
                                    {{ abs($placementsGrowth) }}% from last month
                                </small>
                            </div>
                            <div class="stat-icon bg-success-soft">
                                <i class="fas fa-briefcase text-success fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted text-uppercase small font-weight-bold">Conversion
                                    Rate</span>
                                <h2 class="mb-0 font-weight-bold mt-1">{{ $conversionRate }}%</h2>
                                <small class="text-muted">Applications → Placements</small>
                            </div>
                            <div class="stat-icon bg-info-soft">
                                <i class="fas fa-chart-line text-info fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted text-uppercase small font-weight-bold">Avg. Match
                                    Score</span>
                                <h2 class="mb-0 font-weight-bold mt-1">{{ number_format($averageMatchScore, 1) }}%
                                </h2>
                                <small class="text-muted">Across all applications</small>
                            </div>
                            <div class="stat-icon bg-warning-soft">
                                <i class="fas fa-star text-warning fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row Metrics -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted text-uppercase small font-weight-bold">Active
                                    Placements</span>
                                <h2 class="mb-0 font-weight-bold mt-1">{{ number_format($activePlacements) }}</h2>
                                <small class="text-muted">Currently ongoing</small>
                            </div>
                            <div class="stat-icon bg-success-soft">
                                <i class="fas fa-play-circle text-success fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted text-uppercase small font-weight-bold">Completed</span>
                                <h2 class="mb-0 font-weight-bold mt-1">{{ number_format($completedPlacements) }}
                                </h2>
                                <small class="text-muted">Successfully completed</small>
                            </div>
                            <div class="stat-icon bg-primary-soft">
                                <i class="fas fa-flag-checkered text-primary fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted text-uppercase small font-weight-bold">Avg. Time to
                                    Place</span>
                                <h2 class="mb-0 font-weight-bold mt-1">{{ $averageTimeToPlace }} days</h2>
                                <small class="text-muted">From application to placement</small>
                            </div>
                            <div class="stat-icon bg-info-soft">
                                <i class="fas fa-clock text-info fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted text-uppercase small font-weight-bold">Success Rate</span>
                                <h2 class="mb-0 font-weight-bold mt-1">{{ $successRate }}%</h2>
                                <small class="text-muted">Applications that led to placement</small>
                            </div>
                            <div class="stat-icon bg-success-soft">
                                <i class="fas fa-trophy text-success fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fas fa-chart-line text-primary mr-2"></i>
                                Application & Placement Trend
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="applicationTrendChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fas fa-chart-pie text-primary mr-2"></i>
                                Application Status
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="statusChart" style="height: 250px;"></canvas>
                            </div>
                            <div class="mt-3">
                                @foreach ($statusDistributionData as $status)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <i class="fas {{ $status['icon'] }} mr-2"
                                                style="color: {{ $status['color'] }}"></i>
                                            <span>{{ $status['label'] }}</span>
                                        </div>
                                        <span class="font-weight-bold">{{ number_format($status['value']) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fas fa-chart-bar text-primary mr-2"></i>
                                Top 10 Organizations by Applications
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="topOrganizationsChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fas fa-graduation-cap text-primary mr-2"></i>
                                Top 10 Courses
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="courseChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 3 -->
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fas fa-chart-pie text-primary mr-2"></i>
                                Institution Type Distribution
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="institutionChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fas fa-chart-line text-primary mr-2"></i>
                                Match Score Distribution
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="matchScoreChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Comparison Chart -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0">
                            <h5 class="mb-0 font-weight-bold">
                                <i class="fas fa-chart-line text-primary mr-2"></i>
                                Monthly Comparison: Applications vs Placements
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="monthlyComparisonChart" style="height: 350px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', function() {
                // Initialize all charts
                let charts = {};

                function initCharts() {
                    // Application Trend Chart
                    const trendData = @json($applicationTrendData);
                    if (trendData && trendData.labels && trendData.labels.length > 0) {
                        const ctx1 = document.getElementById('applicationTrendChart')?.getContext('2d');
                        if (ctx1 && charts.applicationTrend) charts.applicationTrend.destroy();
                        if (ctx1) {
                            charts.applicationTrend = new Chart(ctx1, {
                                type: 'line',
                                data: {
                                    labels: trendData.labels,
                                    datasets: [{
                                            label: 'Applications',
                                            data: trendData.applications,
                                            borderColor: '#667eea',
                                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                            fill: true,
                                            tension: 0.4
                                        },
                                        {
                                            label: 'Placements',
                                            data: trendData.placements,
                                            borderColor: '#48bb78',
                                            backgroundColor: 'rgba(72, 187, 120, 0.1)',
                                            fill: true,
                                            tension: 0.4
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'top'
                                        }
                                    }
                                }
                            });
                        }
                    }

                    // Status Distribution Chart
                    const statusData = @json($statusDistributionData);
                    if (statusData && statusData.length > 0) {
                        const ctx2 = document.getElementById('statusChart')?.getContext('2d');
                        if (ctx2 && charts.status) charts.status.destroy();
                        if (ctx2) {
                            charts.status = new Chart(ctx2, {
                                type: 'doughnut',
                                data: {
                                    labels: statusData.map(s => s.label),
                                    datasets: [{
                                        data: statusData.map(s => s.value),
                                        backgroundColor: statusData.map(s => s.color),
                                        borderWidth: 0
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        }
                                    }
                                }
                            });
                        }
                    }

                    // Top Organizations Chart
                    const orgData = @json($topOrganizationsData);
                    if (orgData && orgData.length > 0) {
                        const ctx3 = document.getElementById('topOrganizationsChart')?.getContext('2d');
                        if (ctx3 && charts.topOrgs) charts.topOrgs.destroy();
                        if (ctx3) {
                            charts.topOrgs = new Chart(ctx3, {
                                type: 'bar',
                                data: {
                                    labels: orgData.map(o => o.name.length > 20 ? o.name.substring(0, 20) +
                                        '...' : o.name),
                                    datasets: [{
                                        label: 'Applications',
                                        data: orgData.map(o => o.total),
                                        backgroundColor: '#667eea',
                                        borderRadius: 8
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
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

                    // Course Distribution Chart
                    const courseData = @json($courseDistributionData);
                    if (courseData && courseData.length > 0) {
                        const ctx4 = document.getElementById('courseChart')?.getContext('2d');
                        if (ctx4 && charts.course) charts.course.destroy();
                        if (ctx4) {
                            charts.course = new Chart(ctx4, {
                                type: 'bar',
                                data: {
                                    labels: courseData.map(c => c.label.length > 25 ? c.label.substring(0, 25) +
                                        '...' : c.label),
                                    datasets: [{
                                        label: 'Students',
                                        data: courseData.map(c => c.value),
                                        backgroundColor: '#48bb78',
                                        borderRadius: 8
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
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

                    // Institution Type Chart
                    const instData = @json($institutionTypeData);
                    if (instData && instData.length > 0) {
                        const ctx5 = document.getElementById('institutionChart')?.getContext('2d');
                        if (ctx5 && charts.institution) charts.institution.destroy();
                        if (ctx5) {
                            charts.institution = new Chart(ctx5, {
                                type: 'pie',
                                data: {
                                    labels: instData.map(i => i.label),
                                    datasets: [{
                                        data: instData.map(i => i.value),
                                        backgroundColor: ['#667eea', '#48bb78', '#f6ad55', '#fc8181',
                                            '#9f7aea'
                                        ],
                                        borderWidth: 0
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        }
                                    }
                                }
                            });
                        }
                    }

                    // Match Score Distribution Chart
                    const matchData = @json($matchScoreDistributionData);
                    if (matchData && matchData.length > 0) {
                        const ctx6 = document.getElementById('matchScoreChart')?.getContext('2d');
                        if (ctx6 && charts.matchScore) charts.matchScore.destroy();
                        if (ctx6) {
                            charts.matchScore = new Chart(ctx6, {
                                type: 'bar',
                                data: {
                                    labels: matchData.map(m => m.label),
                                    datasets: [{
                                        label: 'Applications',
                                        data: matchData.map(m => m.value),
                                        backgroundColor: '#f6ad55',
                                        borderRadius: 8
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Number of Applications'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Match Score Range'
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }

                    // Monthly Comparison Chart
                    const monthlyApps = @json($monthlyApplicationsData);
                    const monthlyPlacements = @json($monthlyPlacementsData);
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                    if (monthlyApps && monthlyApps.length > 0) {
                        const ctx7 = document.getElementById('monthlyComparisonChart')?.getContext('2d');
                        if (ctx7 && charts.monthly) charts.monthly.destroy();
                        if (ctx7) {
                            charts.monthly = new Chart(ctx7, {
                                type: 'line',
                                data: {
                                    labels: months,
                                    datasets: [{
                                            label: 'Applications',
                                            data: monthlyApps,
                                            borderColor: '#667eea',
                                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                            fill: true,
                                            tension: 0.4
                                        },
                                        {
                                            label: 'Placements',
                                            data: monthlyPlacements,
                                            borderColor: '#48bb78',
                                            backgroundColor: 'rgba(72, 187, 120, 0.1)',
                                            fill: true,
                                            tension: 0.4
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'top'
                                        }
                                    }
                                }
                            });
                        }
                    }
                }

                // Initial load
                initCharts();

                // Listen for Livewire updates
                Livewire.on('refreshAnalytics', () => {
                    setTimeout(initCharts, 100);
                });

                // Handle window resize
                window.addEventListener('resize', () => {
                    Object.values(charts).forEach(chart => {
                        if (chart) chart.resize();
                    });
                });
            });
        </script>
    @endpush


    @push('styles')
        <style>
            .bg-primary-soft {
                background: rgba(102, 126, 234, 0.1);
            }

            .bg-success-soft {
                background: rgba(72, 187, 120, 0.1);
            }

            .bg-info-soft {
                background: rgba(23, 162, 184, 0.1);
            }

            .bg-warning-soft {
                background: rgba(246, 173, 85, 0.1);
            }

            .stat-card {
                transition: all 0.3s ease;
            }

            .stat-card:hover {
                transform: translateY(-3px);
            }
        </style>
    @endpush

</div>
