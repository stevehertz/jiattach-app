<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Application;
use App\Models\Organization;
use App\Models\StudentProfile;
use Carbon\Carbon;
use Livewire\Component;

class PlacementReports extends Component
{
    public $startDate;
    public $endDate;
    public $viewType = 'monthly';
    public $placementStats = [];
    public $placementTrends = [];
    public $topPlacementCompanies = [];
    public $placementByCourse = [];
    public $placementByInstitution = [];
    public $placementDurationAnalysis = [];

    public function mount()
    {
        $this->startDate = now()->subYear()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->loadData();
    }

    public function loadData()
    {
        $this->placementStats = $this->getPlacementStats();
        $this->placementTrends = $this->getPlacementTrends();
        $this->topPlacementCompanies = $this->getTopPlacementCompanies();
        $this->placementByCourse = $this->getPlacementByCourse();
        $this->placementByInstitution = $this->getPlacementByInstitution();
        $this->placementDurationAnalysis = $this->getPlacementDurationAnalysis();
    }

    public function filter()
    {
        $this->loadData();
        $this->dispatch('placementChartsUpdated');
    }

    public function updated($property)
    {
        if (in_array($property, ['startDate', 'endDate', 'viewType'])) {
            $this->filter();
        }
    }

    private function getPlacementStats()
    {
        $totalApplications = Application::whereBetween('submitted_at', [$this->startDate, $this->endDate])->count();
        $hiredApplications = Application::whereBetween('submitted_at', [$this->startDate, $this->endDate])
            ->where('status', 'hired')
            ->count();

        $conversionRate = $totalApplications > 0 ? round(($hiredApplications / $totalApplications) * 100, 1) : 0;

        // Calculate average time from application to hire
        $avgPlacementTime = Application::where('status', 'hired')
            ->whereBetween('submitted_at', [$this->startDate, $this->endDate])
            ->whereNotNull('submitted_at')
            ->get()
            ->avg(function ($app) {
                if ($app->submitted_at && $app->updated_at) {
                    return $app->submitted_at->diffInDays($app->updated_at);
                }
                return 0;
            });

        return [
            'total_placements' => $hiredApplications,
            'total_applications' => $totalApplications,
            'conversion_rate' => $conversionRate,
            'avg_placement_time' => round($avgPlacementTime, 1),
            'top_performing_month' => $this->getTopPerformingMonth(),
            'placement_by_gender' => $this->getPlacementByGender(),
        ];
    }

    private function getPlacementTrends()
    {
        $data = [];
        $labels = [];

        if ($this->viewType === 'monthly') {
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->format('M Y');

                $count = Application::where('status', 'hired')
                    ->whereYear('updated_at', $date->year)
                    ->whereMonth('updated_at', $date->month)
                    ->count();

                $data[] = $count;
            }
        } else {
            for ($i = 52; $i >= 0; $i -= 4) {
                $date = now()->subWeeks($i);
                $labels[] = 'Week ' . $date->weekOfYear;

                $count = Application::where('status', 'hired')
                    ->whereBetween('updated_at', [$date->startOfWeek(), $date->endOfWeek()])
                    ->count();

                $data[] = $count;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Placements',
                'data' => $data,
                'borderColor' => 'rgb(54, 162, 235)',
                'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                'tension' => 0.4,
                'fill' => true
            ]]
        ];
    }

    private function getTopPlacementCompanies()
    {
        // Get employers with their opportunities and hired applications
        return Organization::withCount([
            'opportunities',
            'opportunities as placements_count' => function ($query) {
                $query->whereHas('applications', function ($q) {
                    $q->where('status', 'hired')
                        ->whereBetween('updated_at', [$this->startDate, $this->endDate]);
                });
            }
        ])
            ->having('placements_count', '>', 0)
            ->orderByDesc('placements_count')
            ->limit(10)
            ->get()
            ->map(function ($organization) {
                return (object)[
                    'name' => $organization->name,
                    'placements_count' => $organization->placements_count,
                    'opportunities_count' => $organization->opportunities_count,
                    'success_rate' => $organization->opportunities_count > 0
                        ? round(($organization->placements_count / $organization->opportunities_count) * 100, 1)
                        : 0
                ];
            });
    }

    private function getPlacementByCourse()
    {
        // Get students with hired applications
        $studentIds = Application::where('status', 'hired')
            ->whereBetween('updated_at', [$this->startDate, $this->endDate])
            ->pluck('student_id');

        return StudentProfile::whereIn('user_id', $studentIds)
            ->select('course_name')
            ->selectRaw('COUNT(*) as total_students')
            ->groupBy('course_name')
            ->orderByDesc('total_students')
            ->limit(15)
            ->get()
            ->map(function ($course) use ($studentIds) {
                // Count total students in this course (for placement rate calculation)
                $totalInCourse = StudentProfile::where('course_name', $course->course_name)
                    ->whereIn('user_id', $studentIds)
                    ->count();

                $course->placed_students = $course->total_students;
                $course->placement_rate = $totalInCourse > 0
                    ? round(($course->total_students / $totalInCourse) * 100, 1)
                    : 0;
                return $course;
            });
    }

    private function getPlacementByInstitution()
    {
        // Get students with hired applications
        $studentIds = Application::where('status', 'hired')
            ->whereBetween('updated_at', [$this->startDate, $this->endDate])
            ->pluck('student_id');

        return StudentProfile::whereIn('user_id', $studentIds)
            ->select('institution_name')
            ->selectRaw('COUNT(*) as placed_students')
            ->groupBy('institution_name')
            ->orderByDesc('placed_students')
            ->limit(10)
            ->get()
            ->map(function ($institution) use ($studentIds) {
                // Count total students from this institution (for placement rate calculation)
                $totalFromInstitution = StudentProfile::where('institution_name', $institution->institution_name)
                    ->whereIn('user_id', $studentIds)
                    ->count();

                $institution->total_students = $totalFromInstitution;
                $institution->placement_rate = $totalFromInstitution > 0
                    ? round(($institution->placed_students / $totalFromInstitution) * 100, 1)
                    : 0;
                return $institution;
            });
    }

    private function getPlacementDurationAnalysis()
    {
        $durations = [
            '0-7 days' => 0,
            '8-14 days' => 0,
            '15-30 days' => 0,
            '31-60 days' => 0,
            '61-90 days' => 0,
            '91+ days' => 0,
        ];

        $placements = Application::where('status', 'hired')
            ->whereBetween('submitted_at', [$this->startDate, $this->endDate])
            ->whereNotNull('submitted_at')
            ->whereNotNull('updated_at')
            ->get();

        foreach ($placements as $placement) {
            $days = $placement->submitted_at->diffInDays($placement->updated_at);

            if ($days <= 7) {
                $durations['0-7 days']++;
            } elseif ($days <= 14) {
                $durations['8-14 days']++;
            } elseif ($days <= 30) {
                $durations['15-30 days']++;
            } elseif ($days <= 60) {
                $durations['31-60 days']++;
            } elseif ($days <= 90) {
                $durations['61-90 days']++;
            } else {
                $durations['91+ days']++;
            }
        }

        return [
            'labels' => array_keys($durations),
            'data' => array_values($durations),
            'total' => array_sum(array_values($durations)),
        ];
    }

    private function getTopPerformingMonth()
    {
        $month = Application::where('status', 'hired')
            ->whereBetween('updated_at', [$this->startDate, $this->endDate])
            ->selectRaw('MONTH(updated_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderByDesc('count')
            ->first();

        return $month ? [
            'month' => Carbon::create()->month($month->month)->format('F'),
            'count' => $month->count
        ] : null;
    }

    private function getPlacementByGender()
    {
        $male = Application::where('status', 'hired')
            ->whereBetween('updated_at', [$this->startDate, $this->endDate])
            ->whereHas('student', function ($query) {
                $query->where('gender', 'male');
            })
            ->count();

        $female = Application::where('status', 'hired')
            ->whereBetween('updated_at', [$this->startDate, $this->endDate])
            ->whereHas('student', function ($query) {
                $query->where('gender', 'female');
            })
            ->count();

        $other = Application::where('status', 'hired')
            ->whereBetween('updated_at', [$this->startDate, $this->endDate])
            ->whereHas('student', function ($query) {
                $query->whereNotIn('gender', ['male', 'female'])->orWhereNull('gender');
            })
            ->count();

        $total = $male + $female + $other;

        return [
            'male' => [
                'count' => $male,
                'percentage' => $total > 0 ? round(($male / $total) * 100, 1) : 0
            ],
            'female' => [
                'count' => $female,
                'percentage' => $total > 0 ? round(($female / $total) * 100, 1) : 0
            ],
            'other' => [
                'count' => $other,
                'percentage' => $total > 0 ? round(($other / $total) * 100, 1) : 0
            ],
            'total' => $total
        ];
    }

    public function render()
    {
        return view('livewire.admin.reports.placement-reports');
    }
}
