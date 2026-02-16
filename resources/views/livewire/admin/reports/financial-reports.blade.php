<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <div class="content">
        <div class="container-fluid">
            <!-- Date Range Filter -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form wire:submit.prevent="loadData" class="row">
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
                <!-- Total Revenue -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>KSh {{ number_format($revenueStats['total_revenue'] ?? 0, 2) }}</h3>
                            <p>Total Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Revenue Growth -->
                <div class="col-lg-3 col-6">
                    <div class="small-box {{ ($revenueStats['revenue_growth'] ?? 0) >= 0 ? 'bg-info' : 'bg-danger' }}">
                        <div class="inner">
                            <h3>{{ number_format($revenueStats['revenue_growth'] ?? 0, 1) }}%</h3>
                            <p>Revenue Growth</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Average Transaction -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>KSh {{ number_format($revenueStats['avg_transaction_value'] ?? 0, 2) }}</h3>
                            <p>Avg. Transaction</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Projected Revenue -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>KSh
                                {{ number_format(($revenueStats['projected_revenue']['total_projection'] ?? 0) / 12, 2) }}
                            </h3>
                            <p>Avg. Monthly Projection</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Charts Row -->
            <div class="row">
                <!-- Revenue Trends -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Revenue Trends</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueTrendsChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Revenue by Source -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Revenue by Source</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueSourceChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row -->
            <div class="row">
                <!-- Payment Status Analysis -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Payment Status Analysis</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-3">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Paid</span>
                                            <span
                                                class="info-box-number">{{ $paymentStatusAnalysis['paid']['count'] ?? 0 }}</span>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    style="width: {{ $paymentStatusAnalysis['paid']['percentage'] ?? 0 }}%">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                {{ $paymentStatusAnalysis['paid']['percentage'] ?? 0 }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Pending</span>
                                            <span
                                                class="info-box-number">{{ $paymentStatusAnalysis['pending']['count'] ?? 0 }}</span>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    style="width: {{ $paymentStatusAnalysis['pending']['percentage'] ?? 0 }}%">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                {{ $paymentStatusAnalysis['pending']['percentage'] ?? 0 }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="info-box bg-danger">
                                        <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Overdue</span>
                                            <span
                                                class="info-box-number">{{ $paymentStatusAnalysis['overdue']['count'] ?? 0 }}</span>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    style="width: {{ $paymentStatusAnalysis['overdue']['percentage'] ?? 0 }}%">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                {{ $paymentStatusAnalysis['overdue']['percentage'] ?? 0 }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="info-box bg-secondary">
                                        <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Failed</span>
                                            <span
                                                class="info-box-number">{{ $paymentStatusAnalysis['failed']['count'] ?? 0 }}</span>
                                            <div class="progress">
                                                <div class="progress-bar"
                                                    style="width: {{ $paymentStatusAnalysis['failed']['percentage'] ?? 0 }}%">
                                                </div>
                                            </div>
                                            <span class="progress-description">
                                                {{ $paymentStatusAnalysis['failed']['percentage'] ?? 0 }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expense Analysis -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Expense Analysis</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="expenseAnalysisChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Third Row -->
            <div class="row">
                <!-- Top Revenue Mentors -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top Revenue Generating Mentors</h3>
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
                                            <th>Mentor</th>
                                            <th>Company</th>
                                            <th>Sessions</th>
                                            <th>Revenue</th>
                                            <th>Avg. Session</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topRevenueMentors as $mentor)
                                            <tr>
                                                <td>{{ $mentor['mentor_name'] }}</td>
                                                <td>{{ $mentor['company'] }}</td>
                                                <td>{{ $mentor['session_count'] }}</td>
                                                <td>KSh {{ number_format($mentor['total_revenue'], 2) }}</td>
                                                <td>KSh {{ number_format($mentor['avg_session_value'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Projections -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Revenue Projections (Next 12 Months)</h3>
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
                                            <th>Month</th>
                                            <th>Projected Revenue</th>
                                            <th>Cumulative</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $cumulative = 0;
                                        @endphp
                                        @foreach ($revenueStats['projected_revenue']['next_12_months'] ?? [] as $projection)
                                            @php
                                                $cumulative += $projection['revenue'];
                                            @endphp
                                            <tr>
                                                <td>{{ $projection['month'] }}</td>
                                                <td>KSh {{ number_format($projection['revenue'], 2) }}</td>
                                                <td>KSh {{ number_format($cumulative, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-success">
                                            <td><strong>Total</strong></td>
                                            <td colspan="2">
                                                <strong>KSh
                                                    {{ number_format($revenueStats['projected_revenue']['total_projection'] ?? 0, 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Financial Summary</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Revenue Breakdown</h5>
                                    <table class="table table-sm">
                                        <tbody>
                                            @foreach ($revenueBySource as $source)
                                                <tr>
                                                    <td>{{ $source['source'] }}</td>
                                                    <td class="text-right">KSh
                                                        {{ number_format($source['amount'], 2) }}</td>
                                                    <td class="text-right">{{ $source['percentage'] }}%</td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-success">
                                                <td><strong>Total Revenue</strong></td>
                                                <td class="text-right">
                                                    <strong>KSh
                                                        {{ number_format($revenueStats['total_revenue'] ?? 0, 2) }}</strong>
                                                </td>
                                                <td class="text-right"><strong>100%</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Expense Breakdown</h5>
                                    <table class="table table-sm">
                                        <tbody>
                                            @foreach ($expenseAnalysis as $expense)
                                                <tr>
                                                    <td>{{ $expense['category'] }}</td>
                                                    <td class="text-right">KSh
                                                        {{ number_format($expense['amount'], 2) }}</td>
                                                    <td class="text-right">{{ $expense['percentage'] }}%</td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-info">
                                                <td><strong>Net Profit Margin</strong></td>
                                                <td class="text-right">
                                                    <strong>KSh
                                                        {{ number_format(($revenueStats['total_revenue'] ?? 0) * 0.2, 2) }}</strong>
                                                </td>
                                                <td class="text-right"><strong>20%</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                initializeFinancialCharts();

                Livewire.on('financialChartsUpdated', () => {
                    setTimeout(() => {
                        initializeFinancialCharts();
                    }, 100);
                });
            });

            function initializeFinancialCharts() {
                // Destroy existing charts
                ['revenueTrendsChart', 'revenueSourceChart', 'expenseAnalysisChart'].forEach(chartId => {
                    const chart = window[chartId];
                    if (chart && typeof chart.destroy === 'function') {
                        chart.destroy();
                    }
                });

                // Revenue Trends Chart
                const trendsCtx = document.getElementById('revenueTrendsChart');
                if (trendsCtx && @json($revenueTrends)) {
                    window.revenueTrendsChart = new Chart(trendsCtx.getContext('2d'), {
                        type: 'line',
                        data: @json($revenueTrends),
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Revenue Trends'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'KSh ' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Revenue Source Chart
                const sourceCtx = document.getElementById('revenueSourceChart');
                if (sourceCtx && @json($revenueBySource)) {
                    const sources = @json($revenueBySource);
                    window.revenueSourceChart = new Chart(sourceCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: sources.map(s => s.source),
                            datasets: [{
                                data: sources.map(s => s.amount),
                                backgroundColor: sources.map(s => s.color),
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
                                    text: 'Revenue by Source'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const percentage = sources[context.dataIndex]?.percentage || 0;
                                            return `${label}: KSh ${value.toLocaleString()} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Expense Analysis Chart
                const expenseCtx = document.getElementById('expenseAnalysisChart');
                if (expenseCtx && @json($expenseAnalysis)) {
                    const expenses = @json($expenseAnalysis);
                    window.expenseAnalysisChart = new Chart(expenseCtx.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: expenses.map(e => e.category),
                            datasets: [{
                                data: expenses.map(e => e.percentage),
                                backgroundColor: expenses.map(e => e.color),
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
                                    text: 'Expense Distribution'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const percentage = context.raw || 0;
                                            const amount = expenses[context.dataIndex]?.amount || 0;
                                            return `${label}: ${percentage}% (KSh ${amount.toLocaleString()})`;
                                        }
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
                        if (el.component.name.includes('FinancialReports')) {
                            setTimeout(() => {
                                initializeFinancialCharts();
                            }, 100);
                        }
                    });
                }
            });
        </script>
    @endpush


</div>
