<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    @php
        // Ensure ALL variables are initialized with defaults at the top
        $userStats = $userStats ?? [
            'total_users' => 0,
            'active_users' => 0,
            'verified_users' => 0,
            'new_users' => 0,
            'engaged_users' => 0,
            'engagement_rate' => 0,
            'verification_rate' => 0,
        ];

        $userGrowthData = $userGrowthData ?? [
            'labels' => [],
            'datasets' => [],
        ];

        $userTypeDistribution = $userTypeDistribution ?? [
            'labels' => ['Students', 'Employers', 'Mentors', 'Administrators'],
            'data' => [0, 0, 0, 0],
            'percentages' => [0, 0, 0, 0],
            'colors' => ['#3498db', '#2ecc71', '#f39c12', '#e74c3c'],
        ];

        $userDemographics = $userDemographics ?? [
            'gender' => [
                'male' => ['count' => 0, 'percentage' => 0],
                'female' => ['count' => 0, 'percentage' => 0],
                'other' => ['count' => 0, 'percentage' => 0],
            ],
            'age_groups' => [
                '18-24' => 0,
                '25-34' => 0,
                '35-44' => 0,
                '45+' => 0,
            ],
            'county_distribution' => collect(),
        ];

        $activeUsersAnalysis = $activeUsersAnalysis ?? [
            'recent_activity' => 0,
            'login_frequency' => [
                'frequent' => 0,
                'occasional' => 0,
                'inactive' => 0,
            ],
            'total_users' => 0,
        ];

        $topInstitutions = $topInstitutions ?? collect();
    @endphp

    <div class="content">
        <div class="container-fluid">
            <!-- Date Range Filter -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form wire:submit.prevent="loadData" class="row">
                                <div class="col-md-4">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" wire:model="startDate">
                                </div>
                                <div class="col-md-4">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" wire:model="endDate">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter mr-1"></i> Apply Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row">
                <!-- Total Users -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($userStats['total_users']) }}</h3>
                            <p>Total Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($userStats['active_users']) }}</h3>
                            <p>Active Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Verified Users -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($userStats['verified_users']) }}</h3>
                            <p>Verified Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- New Users -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($userStats['new_users']) }}</h3>
                            <p>New Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Charts Row -->
            <div class="row">
                <!-- User Growth Chart -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">User Growth Over Time</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="userGrowthChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- User Type Distribution -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">User Type Distribution</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="userTypeChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row -->
            <div class="row">
                <!-- Gender Distribution -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Gender Distribution</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-male"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Male</span>
                                            <span
                                                class="info-box-number">{{ $userDemographics['gender']['male']['count'] }}</span>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    style="width: {{ $userDemographics['gender']['male']['percentage'] }}%">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                {{ $userDemographics['gender']['male']['percentage'] }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-female"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Female</span>
                                            <span
                                                class="info-box-number">{{ $userDemographics['gender']['female']['count'] }}</span>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    style="width: {{ $userDemographics['gender']['female']['percentage'] }}%">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                {{ $userDemographics['gender']['female']['percentage'] }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-user"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Other</span>
                                            <span
                                                class="info-box-number">{{ $userDemographics['gender']['other']['count'] }}</span>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    style="width: {{ $userDemographics['gender']['other']['percentage'] }}%">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                {{ $userDemographics['gender']['other']['percentage'] }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Age Distribution -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Age Distribution</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="ageDistributionChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Third Row -->
            <div class="row">
                <!-- Top Institutions -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top Institutions by Student Count</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Institution</th>
                                            <th>Students</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $topInstitutionsCollection = collect($topInstitutions);
                                            $totalStudents = $topInstitutionsCollection->sum('student_count');
                                        @endphp

                                        @if ($topInstitutionsCollection->count() > 0)
                                            @foreach ($topInstitutionsCollection as $institution)
                                                <tr>
                                                    <td>{{ $institution->institution_name ?? 'N/A' }}</td>
                                                    <td>{{ $institution->student_count ?? 0 }}</td>
                                                    <td>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-primary"
                                                                style="width: {{ $totalStudents > 0 ? (($institution->student_count ?? 0) / $totalStudents) * 100 : 0 }}%">
                                                            </div>
                                                        </div>
                                                        <small>{{ $totalStudents > 0 ? round((($institution->student_count ?? 0) / $totalStudents) * 100, 1) : 0 }}%</small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">
                                                    <i class="fas fa-university mr-1"></i> No institution data
                                                    available
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Activity -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">User Activity Analysis</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-bolt"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Frequent</span>
                                            <span
                                                class="info-box-number">{{ $activeUsersAnalysis['login_frequency']['frequent'] }}</span>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    style="width: {{ $activeUsersAnalysis['total_users'] > 0 ? ($activeUsersAnalysis['login_frequency']['frequent'] / $activeUsersAnalysis['total_users']) * 100 : 0 }}%">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                Last 7 days
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-calendar-week"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Occasional</span>
                                            <span
                                                class="info-box-number">{{ $activeUsersAnalysis['login_frequency']['occasional'] }}</span>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    style="width: {{ $activeUsersAnalysis['total_users'] > 0 ? ($activeUsersAnalysis['login_frequency']['occasional'] / $activeUsersAnalysis['total_users']) * 100 : 0 }}%">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                8-30 days
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="info-box bg-danger">
                                        <span class="info-box-icon"><i class="fas fa-moon"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Inactive</span>
                                            <span
                                                class="info-box-number">{{ $activeUsersAnalysis['login_frequency']['inactive'] }}</span>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    style="width: {{ $activeUsersAnalysis['total_users'] > 0 ? ($activeUsersAnalysis['login_frequency']['inactive'] / $activeUsersAnalysis['total_users']) * 100 : 0 }}%">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                30+ days
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h5>Recent Activity</h5>
                                <p class="text-muted">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $activeUsersAnalysis['recent_activity'] }} users with activity in last 30 days
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- County Distribution -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top Counties by User Count</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>County</th>
                                            <th>Users</th>
                                            <th>Distribution</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $countyDistribution = collect(
                                                $userDemographics['county_distribution'] ?? [],
                                            );
                                            $totalUsers = $userStats['total_users'] ?? 0;
                                        @endphp

                                        @if ($countyDistribution->count() > 0)
                                            @foreach ($countyDistribution as $county)
                                                <tr>
                                                    <td>{{ $county->county ?? 'Not Specified' }}</td>
                                                    <td>{{ $county->count ?? 0 }}</td>
                                                    <td>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-info"
                                                                style="width: {{ $totalUsers > 0 ? (($county->count ?? 0) / $totalUsers) * 100 : 0 }}%">
                                                            </div>
                                                        </div>
                                                        <small>{{ $totalUsers > 0 ? round((($county->count ?? 0) / $totalUsers) * 100, 1) : 0 }}%</small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">
                                                    <i class="fas fa-map-marker-alt mr-1"></i> No county data available
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $ageGroups = $userDemographics['age_groups'] ?? [];
    @endphp

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                initializeUserCharts();

                Livewire.on('userChartsUpdated', () => {
                    setTimeout(() => {
                        initializeUserCharts();
                    }, 100);
                });
            });

            function initializeUserCharts() {
                // Destroy existing charts
                ['userGrowthChart', 'userTypeChart', 'ageDistributionChart'].forEach(chartId => {
                    const chart = window[chartId];
                    if (chart && typeof chart.destroy === 'function') {
                        chart.destroy();
                    }
                });

                // User Growth Chart
                const growthCtx = document.getElementById('userGrowthChart');
                if (growthCtx) {
                    // Get the data from Livewire or use empty data
                    const growthData = @json($userGrowthData) || { labels: [], datasets: [] };
                    
                    window.userGrowthChart = new Chart(growthCtx.getContext('2d'), {
                        type: 'line',
                        data: growthData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'User Registration Trends'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }

                // User Type Distribution Chart
                const typeCtx = document.getElementById('userTypeChart');
                if (typeCtx) {
                    const data = @json($userTypeDistribution) || { 
                        labels: [], 
                        data: [], 
                        percentages: [], 
                        colors: [] 
                    };
                    
                    window.userTypeChart = new Chart(typeCtx.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: data.labels || [],
                            datasets: [{
                                data: data.data || [],
                                backgroundColor: data.colors || [],
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                },
                                title: {
                                    display: true,
                                    text: 'User Type Distribution'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const percentages = data.percentages || [];
                                            const percentage = percentages[context.dataIndex] || 0;
                                            return `${label}: ${value} users (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Age Distribution Chart
                const ageCtx = document.getElementById('ageDistributionChart');
                if (ageCtx) {
                    const ageGroups = @json($ageGroups) || {};
                    
                    window.ageDistributionChart = new Chart(ageCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: Object.keys(ageGroups),
                            datasets: [{
                                label: 'Number of Users',
                                data: Object.values(ageGroups),
                                backgroundColor: [
                                    '#3498db', '#2ecc71', '#9b59b6', '#f39c12'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    initializeUserCharts();
                }, 300); // Small delay to ensure Livewire is loaded
            });
        </script>
    @endpush

</div>
