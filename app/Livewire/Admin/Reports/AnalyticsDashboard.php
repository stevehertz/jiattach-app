<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Application;
use App\Models\AttachmentOpportunity;
use App\Models\StudentProfile;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AnalyticsDashboard extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $viewType = 'monthly';
    public $stats = [];
    public $userGrowthData = [];
    public $applicationStatusData = [];
    public $opportunityTypeData = [];
    public $topInstitutions = [];
    public $recentActivity = [];

    protected $listeners = ['refreshCharts' => 'refresh'];

    public function mount()
    {
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->loadData();
    }

    public function loadData()
    {
        $this->stats = $this->getStats();
        $this->userGrowthData = $this->getUserGrowthData();
        $this->applicationStatusData = $this->getApplicationStatusData();
        $this->opportunityTypeData = $this->getOpportunityTypeData();
        $this->topInstitutions = $this->getTopInstitutions();
        $this->recentActivity = $this->getRecentActivity();
    }

     public function filter()
    {
        $this->loadData();
        $this->dispatch('chartsUpdated');
    }

    public function updated($property)
    {
        if (in_array($property, ['startDate', 'endDate', 'viewType'])) {
            $this->filter();
        }
    }

    public function refresh()
    {
        $this->loadData();
    }

    private function getStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_students' => User::role('student')->count(),
            'total_employers' => User::role('employer')->count(),
            'total_mentors' => User::role('mentor')->count(),
            'total_opportunities' => AttachmentOpportunity::count(),
            'total_applications' => Application::count(),
            'successful_placements' => Application::where('status', 'hired')->count(),
            'placement_rate' => Application::count() > 0
                ? round((Application::where('status', 'hired')->count() / Application::count()) * 100, 1)
                : 0,
        ];
    }

    private function getUserGrowthData(): array
    {
        $data = [];
        $labels = [];

        if ($this->viewType === 'monthly') {
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->format('M Y');

                $count = User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $data[] = $count;
            }
        } else {
            for ($i = 30; $i >= 0; $i -= 5) {
                $date = now()->subDays($i);
                $labels[] = $date->format('M d');

                $count = User::whereDate('created_at', $date)
                    ->count();

                $data[] = $count;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'User Registrations',
                'data' => $data,
                'borderColor' => 'rgb(75, 192, 192)',
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'tension' => 0.4
            ]]
        ];
    }

    private function getApplicationStatusData(): array
    {
        $statuses = [
            'submitted',
            'under_review',
            'shortlisted',
            'interview_scheduled',
            'offer_sent',
            'hired',
            'rejected'
        ];

        $data = [];
        $backgroundColors = [
            '#3498db', // submitted - blue
            '#9b59b6', // under_review - purple
            '#f39c12', // shortlisted - orange
            '#e74c3c', // interview_scheduled - red
            '#2ecc71', // offer_sent - green
            '#27ae60', // hired - dark green
            '#95a5a6'  // rejected - gray
        ];

        $filteredLabels = [];
        $filteredColors = [];

        foreach ($statuses as $index => $status) {
            $count = Application::where('status', $status)->count();
            if ($count > 0) {
                $data[] = $count;
                $filteredLabels[] = ucfirst(str_replace('_', ' ', $status));
                $filteredColors[] = $backgroundColors[$index];
            }
        }

        return [
            'labels' => $filteredLabels,
            'datasets' => [[
                'data' => $data,
                'backgroundColor' => $filteredColors,
                'hoverOffset' => 4
            ]]
        ];
    }

    private function getOpportunityTypeData(): array
    {
        $types = [
            'internship',
            'attachment',
            'volunteer',
            'research',
            'part_time',
            'full_time'
        ];

        $data = [];
        $labels = [];
        $backgroundColors = [
            '#3498db',
            '#2ecc71',
            '#9b59b6',
            '#f39c12',
            '#e74c3c',
            '#1abc9c'
        ];

        $filteredData = [];
        $filteredLabels = [];
        $filteredColors = [];

        foreach ($types as $index => $type) {
            $count = AttachmentOpportunity::where('type', $type)->count();
            if ($count > 0) {
                $filteredData[] = $count;
                $filteredLabels[] = ucfirst(str_replace('_', ' ', $type));
                $filteredColors[] = $backgroundColors[$index];
            }
        }

        return [
            'labels' => $filteredLabels,
            'datasets' => [[
                'label' => 'Opportunities',
                'data' => $filteredData,
                'backgroundColor' => $filteredColors,
                'borderColor' => $filteredColors,
                'borderWidth' => 1
            ]]
        ];
    }

    private function getTopInstitutions()
    {
        return StudentProfile::select('institution_name')
            ->selectRaw('COUNT(*) as student_count')
            ->selectRaw('SUM(CASE WHEN attachment_status = "placed" THEN 1 ELSE 0 END) as placement_count')
            ->groupBy('institution_name')
            ->having('student_count', '>', 0)
            ->orderBy('placement_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($institution) {
                $institution->success_rate = $institution->student_count > 0
                    ? round(($institution->placement_count / $institution->student_count) * 100, 1)
                    : 0;
                return $institution;
            });
    }

    private function getRecentActivity()
    {
        // Get recent applications
        $applications = Application::with(['student.user', 'opportunity'])
            ->latest('submitted_at')
            ->limit(5)
            ->get()
            ->map(function ($app) {
                return (object)[
                    'description' => 'Application submitted for ' . ($app->opportunity->title ?? 'Opportunity'),
                    'type' => 'application',
                    'type_color' => 'info',
                    'user' => $app->student->user ?? null,
                    'created_at' => $app->submitted_at
                ];
            });

        // Get recent user registrations
        $registrations = User::latest()
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return (object)[
                    'description' => 'New ' . ucfirst($user->user_type ?? 'user') . ' registered: ' . $user->full_name,
                    'type' => 'registration',
                    'type_color' => 'success',
                    'user' => $user,
                    'created_at' => $user->created_at
                ];
            });

        // Get recent opportunity postings
        $opportunities = AttachmentOpportunity::latest('published_at')
            ->whereNotNull('published_at')
            ->limit(5)
            ->get()
            ->map(function ($opp) {
                return (object)[
                    'description' => 'New opportunity posted: ' . $opp->title,
                    'type' => 'opportunity',
                    'type_color' => 'warning',
                    'user' => $opp->employer->user ?? null,
                    'created_at' => $opp->published_at
                ];
            });

        // Get recent mentorship sessions
        $mentorshipSessions = \App\Models\MentorshipSession::with(['mentor.user', 'mentee'])
            ->whereIn('status', ['completed', 'scheduled'])
            ->latest('scheduled_start_time')
            ->limit(5)
            ->get()
            ->map(function ($session) {
                return (object)[
                    'description' => 'Mentorship session: ' . $session->title,
                    'type' => 'mentorship',
                    'type_color' => 'primary',
                    'user' => $session->mentor->user ?? null,
                    'created_at' => $session->scheduled_start_time
                ];
            });

        // Combine and sort
        $activity = collect()
            ->merge($applications)
            ->merge($registrations)
            ->merge($opportunities)
            ->merge($mentorshipSessions)
            ->sortByDesc('created_at')
            ->take(10);

        return $activity;
    }

    public function render()
    {
        return view('livewire.admin.reports.analytics-dashboard');
    }
}
