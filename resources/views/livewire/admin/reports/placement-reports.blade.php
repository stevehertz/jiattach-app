<div>
    {{-- In work, do what you enjoy. --}}
    <div class="content">
        <div class="container-fluid">
            <!-- Date Range Filter -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form wire:submit.prevent="filter" class="row">
                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" wire:model="startDate">
                                </div>
                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" wire:model="endDate">
                                </div>
                                <div class="col-md-3">
                                    <label>View Type</label>
                                    <select class="form-control" wire:model="viewType">
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="quarterly">Quarterly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
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
                <!-- Total Placements -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($placementStats['total_placements'] ?? 0) }}</h3>
                            <p>Total Placements</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Conversion Rate -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($placementStats['conversion_rate'] ?? 0, 1) }}%</h3>
                            <p>Conversion Rate</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Average Placement Time -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($placementStats['avg_placement_time'] ?? 0) }}</h3>
                            <p>Avg. Placement Time (Days)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            More Info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Top Performing Month -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $placementStats['top_performing_month']['month'] ?? 'N/A' }}</h3>
                            <p>Top Performing Month</p>
                            @if (isset($placementStats['top_performing_month']))
                                <small>{{ $placementStats['top_performing_month']['count'] }} placements</small>
                            @endif
                        </div>
                        <div class="icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Charts Row -->
            <div class="row">
                <!-- Placement Trends -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Placement Trends Over Time</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="placementTrendsChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Placement Duration Analysis -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Placement Duration Analysis</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="placementDurationChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Charts Row -->
            <div class="row">
                <!-- Placement by Course -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Placements by Course</h3>
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
                                            <th>Course</th>
                                            <th>Total Students</th>
                                            <th>Placements</th>
                                            <th>Placement Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($placementByCourse as $course)
                                            <tr>
                                                <td>{{ $course->course_name }}</td>
                                                <td>{{ $course->total_students }}</td>
                                                <td>{{ $course->placed_students }}</td>
                                                <td>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-{{ $course->placement_rate >= 50 ? 'success' : ($course->placement_rate >= 30 ? 'warning' : 'danger') }}"
                                                            style="width: {{ min($course->placement_rate, 100) }}%">
                                                        </div>
                                                    </div>
                                                    <small>{{ $course->placement_rate }}%</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Placement Companies -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top Placement Companies</h3>
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
                                            <th>Company</th>
                                            <th>Placements</th>
                                            <th>Opportunities</th>
                                            <th>Success Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topPlacementCompanies as $company)
                                            <tr>
                                                <td>{{ $company->name }}</td>
                                                <td>{{ $company->placements_count }}</td>
                                                <td>{{ $company->opportunities_count }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $company->success_rate >= 50 ? 'success' : ($company->success_rate >= 30 ? 'warning' : 'danger') }}">
                                                        {{ $company->success_rate }}%
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Third Row -->
            <div class="row">
                <!-- Placement by Institution -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Placements by Institution</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="placementByInstitutionChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Placement by Gender -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Placements by Gender</h3>
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
                                                class="info-box-number">{{ $placementStats['placement_by_gender']['male']['count'] ?? 0 }}</span>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    style="width: {{ $placementStats['placement_by_gender']['male']['percentage'] ?? 0 }}%">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                {{ $placementStats['placement_by_gender']['male']['percentage'] ?? 0 }}%
                                                of placements
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
                                                class="info-box-number">{{ $placementStats['placement_by_gender']['female']['count'] ?? 0 }}</span>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    style="width: {{ $placementStats['placement_by_gender']['female']['percentage'] ?? 0 }}%">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                {{ $placementStats['placement_by_gender']['female']['percentage'] ?? 0 }}%
                                                of placements
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
                                                class="info-box-number">{{ $placementStats['placement_by_gender']['other']['count'] ?? 0 }}</span>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    style="width: {{ $placementStats['placement_by_gender']['other']['percentage'] ?? 0 }}%">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                {{ $placementStats['placement_by_gender']['other']['percentage'] ?? 0 }}%
                                                of placements
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Export Reports</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <button class="btn btn-outline-primary btn-block mb-2">
                                        <i class="fas fa-file-excel mr-1"></i> Export to Excel
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-outline-danger btn-block mb-2">
                                        <i class="fas fa-file-pdf mr-1"></i> Export to PDF
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-outline-success btn-block mb-2">
                                        <i class="fas fa-file-csv mr-1"></i> Export to CSV
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-outline-info btn-block mb-2">
                                        <i class="fas fa-print mr-1"></i> Print Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                initializePlacementCharts();

                Livewire.on('placementChartsUpdated', () => {
                    setTimeout(() => {
                        initializePlacementCharts();
                    }, 100);
                });
            });

            function initializePlacementCharts() {
                // Destroy existing charts
                ['placementTrendsChart', 'placementDurationChart', 'placementByInstitutionChart'].forEach(chartId => {
                    const chart = window[chartId];
                    if (chart && typeof chart.destroy === 'function') {
                        chart.destroy();
                    }
                });

                // Placement Trends Chart
                const trendsCtx = document.getElementById('placementTrendsChart');
                if (trendsCtx) {
                    window.placementTrendsChart = new Chart(trendsCtx.getContext('2d'), {
                        type: 'line',
                        data: @json($placementTrends),
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Placement Trends'
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

                // Placement Duration Chart
                const durationCtx = document.getElementById('placementDurationChart');
                if (durationCtx && @json($placementDurationAnalysis)) {
                    window.placementDurationChart = new Chart(durationCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: @json($placementDurationAnalysis['labels']),
                            datasets: [{
                                label: 'Number of Placements',
                                data: @json($placementDurationAnalysis['data']),
                                backgroundColor: [
                                    '#3498db', '#2ecc71', '#9b59b6',
                                    '#f39c12', '#e74c3c', '#1abc9c'
                                ],
                                borderColor: [
                                    '#2980b9', '#27ae60', '#8e44ad',
                                    '#d35400', '#c0392b', '#16a085'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                title: {
                                    display: true,
                                    text: 'Days from Application to Placement'
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

                // Placement by Institution Chart
                const institutionCtx = document.getElementById('placementByInstitutionChart');
                if (institutionCtx && @json($placementByInstitution)) {
                    const institutions = @json($placementByInstitution);
                    window.placementByInstitutionChart = new Chart(institutionCtx.getContext('2d'), {
                        type: 'horizontalBar',
                        data: {
                            labels: institutions.map(inst => inst.institution_name),
                            datasets: [{
                                label: 'Placements',
                                data: institutions.map(inst => inst.placed_students),
                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                title: {
                                    display: true,
                                    text: 'Placements by Institution'
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Livewire !== 'undefined') {
                    Livewire.hook('morph.updated', (el) => {
                        if (el.component.name.includes('PlacementReports')) {
                            setTimeout(() => {
                                initializePlacementCharts();
                            }, 100);
                        }
                    });
                }
            });
        </script>
    @endpush
</div>
