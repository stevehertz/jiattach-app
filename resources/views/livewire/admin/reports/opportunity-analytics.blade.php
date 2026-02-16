<div>
    {{-- Success is as dangerous as failure. --}}
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
                <!-- Total Opportunities -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($opportunityStats['total_opportunities'] ?? 0) }}</h3>
                            <p>Total Opportunities</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <a href="{{ route('admin.opportunities.index') }}" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Active Opportunities -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($opportunityStats['active_opportunities'] ?? 0) }}</h3>
                            <p>Active Opportunities</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="{{ route('admin.opportunities.active') }}" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Average Applications -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($opportunityStats['avg_applications'] ?? 0, 1) }}</h3>
                            <p>Avg. Applications</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Fill Rate -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($opportunityStats['fill_rate'] ?? 0, 1) }}%</h3>
                            <p>Fill Rate</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="{{ route('admin.opportunities.index') }}" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Charts Row -->
            <div class="row">
                <!-- Opportunity Trends -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Opportunity Trends</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="opportunityTrendsChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Opportunity Type Analysis -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Opportunity Type Analysis</h3>
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
                                            <th>Type</th>
                                            <th>Count</th>
                                            <th>Avg. Apps</th>
                                            <th>Fill Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($opportunityTypeAnalysis as $type)
                                            <tr>
                                                <td>{{ $type['type'] }}</td>
                                                <td>{{ $type['count'] }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $type['avg_applications'] >= 10 ? 'success' : ($type['avg_applications'] >= 5 ? 'warning' : 'danger') }}">
                                                        {{ $type['avg_applications'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-{{ $type['fill_rate'] >= 50 ? 'success' : ($type['fill_rate'] >= 30 ? 'warning' : 'danger') }}"
                                                            style="width: {{ min($type['fill_rate'], 100) }}%">
                                                        </div>
                                                    </div>
                                                    <small>{{ $type['fill_rate'] }}%</small>
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

            <!-- Second Row -->
            <div class="row">
                <!-- Top Performing Opportunities -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top Performing Opportunities</h3>
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
                                            <th>Opportunity</th>
                                            <th>Company</th>
                                            <th>Applications</th>
                                            <th>Fill Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topPerformingOpportunities as $opportunity)
                                            <tr>
                                                <td>{{ Str::limit($opportunity->title, 30) }}</td>
                                                <td>{{ $opportunity->employer->company_name ?? 'N/A' }}</td>
                                                <td>{{ $opportunity->applications_count }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $opportunity->fill_rate >= 80 ? 'success' : ($opportunity->fill_rate >= 50 ? 'warning' : 'danger') }}">
                                                        {{ $opportunity->fill_rate }}%
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

                <!-- Industry Analysis -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Industry Analysis</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="industryAnalysisChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Third Row -->
            <div class="row">
                <!-- Location Analysis -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Location Analysis</h3>
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
                                            <th>Location</th>
                                            <th>Opportunities</th>
                                            <th>Avg. Applications</th>
                                            <th>Total Applications</th>
                                            <th>Fill Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($locationAnalysis as $location)
                                            <tr>
                                                <td>
                                                    {{ $location->location }}
                                                    @if ($location->town)
                                                        <br><small class="text-muted">{{ $location->town }},
                                                            {{ $location->county }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $location->opportunity_count }}</td>
                                                <td>{{ $location->avg_applications }}</td>
                                                <td>{{ $location->total_applications }}</td>
                                                <td>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-{{ $location->fill_rate >= 50 ? 'success' : ($location->fill_rate >= 30 ? 'warning' : 'danger') }}"
                                                            style="width: {{ min($location->fill_rate, 100) }}%">
                                                        </div>
                                                    </div>
                                                    <small>{{ $location->fill_rate }}%</small>
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
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                initializeOpportunityCharts();
                
                Livewire.on('opportunityChartsUpdated', () => {
                    setTimeout(() => {
                        initializeOpportunityCharts();
                    }, 100);
                });
            });

            function initializeOpportunityCharts() {
                // Destroy existing charts
                ['opportunityTrendsChart', 'industryAnalysisChart'].forEach(chartId => {
                    const chart = window[chartId];
                    if (chart && typeof chart.destroy === 'function') {
                        chart.destroy();
                    }
                });

                // Opportunity Trends Chart
                const trendsCtx = document.getElementById('opportunityTrendsChart');
                if (trendsCtx && @json($opportunityTrends)) {
                    window.opportunityTrendsChart = new Chart(trendsCtx.getContext('2d'), {
                        type: 'line',
                        data: @json($opportunityTrends),
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Opportunity Publishing Trends'
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

                // Industry Analysis Chart
                const industryCtx = document.getElementById('industryAnalysisChart');
                if (industryCtx && @json($industryAnalysis)) {
                    const industries = @json($industryAnalysis);
                    window.industryAnalysisChart = new Chart(industryCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: industries.map(ind => ind.industry),
                            datasets: [{
                                label: 'Opportunities',
                                data: industries.map(ind => ind.opportunity_count),
                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                borderColor: 'rgba(54, 162, 235, 1)',
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
                                    text: 'Opportunities by Industry'
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
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Livewire !== 'undefined') {
                    Livewire.hook('morph.updated', (el) => {
                        if (el.component.name.includes('OpportunityAnalytics')) {
                            setTimeout(() => {
                                initializeOpportunityCharts();
                            }, 100);
                        }
                    });
                }
            });
        </script>
    @endpush
</div>
