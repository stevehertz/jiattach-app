<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Application;
use App\Models\Mentorship;
use App\Models\MentorshipSession;
use Carbon\Carbon;
use Livewire\Component;

class FinancialReports extends Component
{
    public $startDate;
    public $endDate;
    public $viewType = 'monthly';
    public $revenueStats = [];
    public $revenueTrends = [];
    public $revenueBySource = [];
    public $topRevenueMentors = [];
    public $paymentStatusAnalysis = [];
    public $expenseAnalysis = [];

     public function mount()
    {
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->loadData();
    }

    public function loadData()
    {
        $this->revenueStats = $this->getRevenueStats();
        $this->revenueTrends = $this->getRevenueTrends();
        $this->revenueBySource = $this->getRevenueBySource();
        $this->topRevenueMentors = $this->getTopRevenueMentors();
        $this->paymentStatusAnalysis = $this->getPaymentStatusAnalysis();
        $this->expenseAnalysis = $this->getExpenseAnalysis();
    }

    private function getRevenueStats()
    {
        // Mentorship revenue (from paid mentorships)
        $mentorshipRevenue = Mentorship::where('is_paid', true)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->sum('hourly_rate');

        // Session revenue (from individual paid sessions)
        $sessionRevenue = MentorshipSession::where('is_paid', true)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->sum('session_cost');

        // Platform fees (assume 10% commission on placements)
        $placementCount = Application::where('status', 'hired')
            ->whereBetween('updated_at', [$this->startDate, $this->endDate])
            ->count();
        $placementRevenue = $placementCount * 5000; // Assume KSh 5,000 per placement

        // Subscription revenue (if you have subscription model)
        $subscriptionRevenue = 0; // Implement based on your subscription model

        $totalRevenue = $mentorshipRevenue + $sessionRevenue + $placementRevenue + $subscriptionRevenue;

        // Revenue growth (compared to previous period)
        $previousStartDate = Carbon::parse($this->startDate)->subYear();
        $previousEndDate = Carbon::parse($this->endDate)->subYear();
        
        $previousMentorshipRevenue = Mentorship::where('is_paid', true)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->sum('hourly_rate');
            
        $previousSessionRevenue = MentorshipSession::where('is_paid', true)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->sum('session_cost');
            
        $previousTotalRevenue = $previousMentorshipRevenue + $previousSessionRevenue + $placementRevenue;
        
        $revenueGrowth = $previousTotalRevenue > 0 
            ? round((($totalRevenue - $previousTotalRevenue) / $previousTotalRevenue) * 100, 1)
            : ($totalRevenue > 0 ? 100 : 0);

        return [
            'total_revenue' => $totalRevenue,
            'mentorship_revenue' => $mentorshipRevenue,
            'session_revenue' => $sessionRevenue,
            'placement_revenue' => $placementRevenue,
            'subscription_revenue' => $subscriptionRevenue,
            'revenue_growth' => $revenueGrowth,
            'avg_transaction_value' => $this->getAverageTransactionValue(),
            'projected_revenue' => $this->getProjectedRevenue(),
        ];
    }

    private function getRevenueTrends()
    {
        $data = [];
        $labels = [];

        if ($this->viewType === 'monthly') {
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->format('M Y');
                
                $mentorship = Mentorship::where('is_paid', true)
                    ->where('payment_status', 'paid')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('hourly_rate');
                
                $session = MentorshipSession::where('is_paid', true)
                    ->where('payment_status', 'paid')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('session_cost');
                
                $data['mentorship'][] = $mentorship;
                $data['session'][] = $session;
                $data['total'][] = $mentorship + $session;
            }
        } else {
            for ($i = 52; $i >= 0; $i -= 4) {
                $date = now()->subWeeks($i);
                $labels[] = 'Week ' . $date->weekOfYear;
                
                $mentorship = Mentorship::where('is_paid', true)
                    ->where('payment_status', 'paid')
                    ->whereBetween('created_at', [$date->startOfWeek(), $date->endOfWeek()])
                    ->sum('hourly_rate');
                
                $session = MentorshipSession::where('is_paid', true)
                    ->where('payment_status', 'paid')
                    ->whereBetween('created_at', [$date->startOfWeek(), $date->endOfWeek()])
                    ->sum('session_cost');
                
                $data['mentorship'][] = $mentorship;
                $data['session'][] = $session;
                $data['total'][] = $mentorship + $session;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Mentorship Revenue',
                    'data' => $data['mentorship'],
                    'borderColor' => 'rgb(54, 162, 235)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'tension' => 0.4
                ],
                [
                    'label' => 'Session Revenue',
                    'data' => $data['session'],
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'tension' => 0.4
                ],
                [
                    'label' => 'Total Revenue',
                    'data' => $data['total'],
                    'borderColor' => 'rgb(255, 99, 132)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'tension' => 0.4
                ]
            ]
        ];
    }

     private function getRevenueBySource()
    {
        $total = $this->revenueStats['total_revenue'] ?? 0;
        
        if ($total == 0) {
            return [
                ['source' => 'Mentorships', 'amount' => 0, 'percentage' => 0, 'color' => '#3498db'],
                ['source' => 'Sessions', 'amount' => 0, 'percentage' => 0, 'color' => '#2ecc71'],
                ['source' => 'Placements', 'amount' => 0, 'percentage' => 0, 'color' => '#9b59b6'],
                ['source' => 'Subscriptions', 'amount' => 0, 'percentage' => 0, 'color' => '#f39c12']
            ];
        }

        return [
            [
                'source' => 'Mentorships',
                'amount' => $this->revenueStats['mentorship_revenue'] ?? 0,
                'percentage' => round(($this->revenueStats['mentorship_revenue'] / $total) * 100, 1),
                'color' => '#3498db'
            ],
            [
                'source' => 'Sessions',
                'amount' => $this->revenueStats['session_revenue'] ?? 0,
                'percentage' => round(($this->revenueStats['session_revenue'] / $total) * 100, 1),
                'color' => '#2ecc71'
            ],
            [
                'source' => 'Placements',
                'amount' => $this->revenueStats['placement_revenue'] ?? 0,
                'percentage' => round(($this->revenueStats['placement_revenue'] / $total) * 100, 1),
                'color' => '#9b59b6'
            ],
            [
                'source' => 'Subscriptions',
                'amount' => $this->revenueStats['subscription_revenue'] ?? 0,
                'percentage' => round(($this->revenueStats['subscription_revenue'] / $total) * 100, 1),
                'color' => '#f39c12'
            ]
        ];
    }

    private function getTopRevenueMentors()
    {
        return \App\Models\Mentor::with('user')
            ->whereHas('mentorships', function ($query) {
                $query->where('is_paid', true)
                    ->where('payment_status', 'paid')
                    ->whereBetween('created_at', [$this->startDate, $this->endDate]);
            })
            ->with(['mentorships' => function ($query) {
                $query->where('is_paid', true)
                    ->where('payment_status', 'paid')
                    ->whereBetween('created_at', [$this->startDate, $this->endDate]);
            }])
            ->get()
            ->map(function ($mentor) {
                $totalRevenue = $mentor->mentorships->sum('hourly_rate');
                $sessionCount = $mentor->mentorships->count();
                
                return [
                    'mentor_name' => $mentor->user->full_name ?? 'Unknown',
                    'company' => $mentor->company,
                    'total_revenue' => $totalRevenue,
                    'session_count' => $sessionCount,
                    'avg_session_value' => $sessionCount > 0 ? round($totalRevenue / $sessionCount, 2) : 0
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(10)
            ->values();
    }

    private function getPaymentStatusAnalysis()
    {
        $paid = Mentorship::where('is_paid', true)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->count();

        $pending = Mentorship::where('is_paid', true)
            ->where('payment_status', 'pending')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->count();

        $overdue = Mentorship::where('is_paid', true)
            ->where('payment_status', 'overdue')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->count();

        $failed = Mentorship::where('is_paid', true)
            ->where('payment_status', 'failed')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->count();

        $total = $paid + $pending + $overdue + $failed;

        return [
            'paid' => [
                'count' => $paid,
                'percentage' => $total > 0 ? round(($paid / $total) * 100, 1) : 0
            ],
            'pending' => [
                'count' => $pending,
                'percentage' => $total > 0 ? round(($pending / $total) * 100, 1) : 0
            ],
            'overdue' => [
                'count' => $overdue,
                'percentage' => $total > 0 ? round(($overdue / $total) * 100, 1) : 0
            ],
            'failed' => [
                'count' => $failed,
                'percentage' => $total > 0 ? round(($failed / $total) * 100, 1) : 0
            ],
            'total' => $total
        ];
    }

    private function getExpenseAnalysis()
    {
        // These would come from your actual expense tracking system
        // For now, using estimated percentages
        
        $totalRevenue = $this->revenueStats['total_revenue'] ?? 0;
        
        return [
            [
                'category' => 'Platform Maintenance',
                'amount' => $totalRevenue * 0.20, // 20% of revenue
                'percentage' => 20,
                'color' => '#e74c3c'
            ],
            [
                'category' => 'Marketing & Advertising',
                'amount' => $totalRevenue * 0.15, // 15% of revenue
                'percentage' => 15,
                'color' => '#3498db'
            ],
            [
                'category' => 'Staff Salaries',
                'amount' => $totalRevenue * 0.30, // 30% of revenue
                'percentage' => 30,
                'color' => '#2ecc71'
            ],
            [
                'category' => 'Payment Processing Fees',
                'amount' => $totalRevenue * 0.05, // 5% of revenue
                'percentage' => 5,
                'color' => '#f39c12'
            ],
            [
                'category' => 'Other Expenses',
                'amount' => $totalRevenue * 0.10, // 10% of revenue
                'percentage' => 10,
                'color' => '#9b59b6'
            ],
            [
                'category' => 'Net Profit',
                'amount' => $totalRevenue * 0.20, // 20% of revenue
                'percentage' => 20,
                'color' => '#27ae60'
            ]
        ];
    }

    private function getAverageTransactionValue()
    {
        $paidMentorships = Mentorship::where('is_paid', true)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();

        if ($paidMentorships->count() == 0) {
            return 0;
        }

        return round($paidMentorships->avg('hourly_rate'), 2);
    }

    private function getProjectedRevenue()
    {
        // Simple projection based on current month's revenue
        $currentMonthRevenue = Mentorship::where('is_paid', true)
            ->where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('hourly_rate');

        $currentMonthRevenue += MentorshipSession::where('is_paid', true)
            ->where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('session_cost');

        // Project for next 12 months with 5% monthly growth
        $projections = [];
        $monthlyRevenue = $currentMonthRevenue;
        
        for ($i = 1; $i <= 12; $i++) {
            $projections[] = [
                'month' => now()->addMonths($i)->format('M Y'),
                'revenue' => round($monthlyRevenue * (1 + (0.05 * $i)), 2)
            ];
        }

        return [
            'current_month' => $currentMonthRevenue,
            'next_12_months' => $projections,
            'total_projection' => array_sum(array_column($projections, 'revenue'))
        ];
    }

    public function render()
    {
        return view('livewire.admin.reports.financial-reports');
    }
}
