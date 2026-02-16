<div>
    {{-- Be like water. --}}
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
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
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
                <!-- Total Users -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($stats['total_users'] ?? 0) }}</h3>
                            <p>Total Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Total Opportunities -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($stats['total_opportunities'] ?? 0) }}</h3>
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

                <!-- Total Applications -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($stats['total_applications'] ?? 0) }}</h3>
                            <p>Total Applications</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Placement Rate -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($stats['placement_rate'] ?? 0, 1) }}%</h3>
                            <p>Placement Rate</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="#" class="small-box-footer">
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

                <!-- Application Status Distribution -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Application Status Distribution</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="applicationStatusChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Charts Row -->
            <div class="row">
                <!-- Opportunities by Type -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Opportunities by Type</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="opportunityTypeChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top Performing Institutions -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top Performing Institutions</h3>
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
                                            <th>Placements</th>
                                            <th>Success Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topInstitutions as $institution)
                                            <tr>
                                                <td>{{ $institution->institution_name }}</td>
                                                <td>{{ $institution->student_count }}</td>
                                                <td>{{ $institution->placement_count }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $institution->success_rate >= 50 ? 'success' : ($institution->success_rate >= 30 ? 'warning' : 'danger') }}">
                                                        {{ $institution->success_rate }}%
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

            <!-- Recent Activity -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Platform Activity</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="products-list product-list-in-card pl-2 pr-2">
                                @forelse($recentActivity as $activity)
                                    <li class="item">
                                        <div class="product-img">
                                            @if ($activity->user)
                                                {!! getUserAvatar($activity->user, 50) !!}
                                            @else
                                                <div class="avatar-initials bg-secondary img-circle elevation-2"
                                                    style="width: 50px; height: 50px; line-height: 50px; text-align: center; color: white; font-weight: bold; font-size: 18px;">
                                                    SYS
                                                </div>
                                            @endif
                                        </div>
                                        <div class="product-info">
                                            <a href="#" class="product-title">
                                                {{ $activity->description }}
                                                <span class="badge badge-{{ $activity->type_color }} float-right">
                                                    {{ ucfirst($activity->type) }}
                                                </span>
                                            </a>
                                            <span class="product-description">
                                                @if ($activity->user)
                                                    By {{ $activity->user->full_name }}
                                                @else
                                                    System Generated
                                                @endif
                                                | {{ timeAgo($activity->created_at) }}
                                            </span>
                                        </div>
                                    </li>
                                @empty
                                    <li class="item">
                                        <div class="product-info">
                                            <span class="product-description text-muted">No recent activity</span>
                                        </div>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                initializeCharts();

                Livewire.on('chartsUpdated', () => {
                    setTimeout(() => {
                        initializeCharts();
                    }, 100);
                });
            });

            function initializeCharts() {
                // Destroy existing charts if they exist
                [userGrowthChart, applicationStatusChart, opportunityTypeChart].forEach(chart => {
                    if (chart && typeof chart.destroy === 'function') {
                        chart.destroy();
                    }
                });

                // User Growth Chart
                const userGrowthCtx = document.getElementById('userGrowthChart');
                if (userGrowthCtx) {
                    const userGrowthChart = new Chart(userGrowthCtx.getContext('2d'), {
                        type: 'line',
                        data: @json($userGrowthData),
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'User Registration Trend'
                                }
                            }
                        }
                    });
                }

                // Application Status Chart
                const applicationStatusCtx = document.getElementById('applicationStatusChart');
                if (applicationStatusCtx) {
                    const applicationStatusChart = new Chart(applicationStatusCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: @json($applicationStatusData),
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                },
                                title: {
                                    display: true,
                                    text: 'Application Status Distribution'
                                }
                            }
                        }
                    });
                }

                // Opportunity Type Chart
                const opportunityTypeCtx = document.getElementById('opportunityTypeChart');
                if (opportunityTypeCtx) {
                    const opportunityTypeChart = new Chart(opportunityTypeCtx.getContext('2d'), {
                        type: 'bar',
                        data: @json($opportunityTypeData),
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                title: {
                                    display: true,
                                    text: 'Opportunities by Type'
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
                        if (el.component.name.includes('AnalyticsDashboard')) {
                            setTimeout(() => {
                                initializeCharts();
                            }, 100);
                        }
                    });
                }
            });
        </script>
    @endpush
</div>
