<?php

namespace App\Livewire\Admin\Applications;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Organization;
use App\Models\Placement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Analytics extends Component
{

      // Date range filters
    public $dateRange = 'this_year'; // this_month, last_month, this_quarter, this_year, last_year, custom
    public $startDate;
    public $endDate;
    public $customStartDate;
    public $customEndDate;
    public $showCustomDatePicker = false;
    
    // Organization filter
    public $organizationFilter = 'all';
    public $organizations;
    
    // Chart data
    public $applicationTrendData = [];
    public $statusDistributionData = [];
    public $placementTrendData = [];
    public $topOrganizationsData = [];
    public $courseDistributionData = [];
    public $institutionTypeData = [];
    public $matchScoreDistributionData = [];
    public $monthlyApplicationsData = [];
    public $monthlyPlacementsData = [];
    
    // Key metrics
    public $totalApplications = 0;
    public $totalPlacements = 0;
    public $averageMatchScore = 0;
    public $conversionRate = 0;
    public $activePlacements = 0;
    public $completedPlacements = 0;
    public $cancelledPlacements = 0;
    public $totalOrganizations = 0;
    public $totalStudents = 0;
    public $averageTimeToPlace = 0;
    public $successRate = 0;
    
    // Growth metrics
    public $applicationsGrowth = 0;
    public $placementsGrowth = 0;
    public $studentsGrowth = 0;
    
    protected $listeners = ['refreshAnalytics' => 'loadData'];
    
    public function mount()
    {
        $this->organizations = Organization::orderBy('name')->get(['id', 'name']);
        $this->setDateRange();
        $this->loadData();
    }
    
    public function setDateRange()
    {
        $now = Carbon::now();
        
        switch ($this->dateRange) {
            case 'this_month':
                $this->startDate = $now->copy()->startOfMonth();
                $this->endDate = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $this->startDate = $now->copy()->subMonth()->startOfMonth();
                $this->endDate = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'this_quarter':
                $this->startDate = $now->copy()->startOfQuarter();
                $this->endDate = $now->copy()->endOfQuarter();
                break;
            case 'this_year':
                $this->startDate = $now->copy()->startOfYear();
                $this->endDate = $now->copy()->endOfYear();
                break;
            case 'last_year':
                $this->startDate = $now->copy()->subYear()->startOfYear();
                $this->endDate = $now->copy()->subYear()->endOfYear();
                break;
            case 'custom':
                if ($this->customStartDate && $this->customEndDate) {
                    $this->startDate = Carbon::parse($this->customStartDate);
                    $this->endDate = Carbon::parse($this->customEndDate);
                }
                break;
        }
    }
    
    public function updatedDateRange()
    {
        $this->showCustomDatePicker = ($this->dateRange === 'custom');
        $this->setDateRange();
        $this->loadData();
    }
    
    public function updatedCustomStartDate()
    {
        if ($this->dateRange === 'custom') {
            $this->setDateRange();
            $this->loadData();
        }
    }
    
    public function updatedCustomEndDate()
    {
        if ($this->dateRange === 'custom') {
            $this->setDateRange();
            $this->loadData();
        }
    }
    
    public function updatedOrganizationFilter()
    {
        $this->loadData();
    }
    
    public function loadData()
    {
        $this->loadKeyMetrics();
        $this->loadApplicationTrend();
        $this->loadStatusDistribution();
        $this->loadPlacementTrend();
        $this->loadTopOrganizations();
        $this->loadCourseDistribution();
        $this->loadInstitutionTypeDistribution();
        $this->loadMatchScoreDistribution();
        $this->loadMonthlyData();
        $this->loadGrowthMetrics();
    }
    
    protected function getBaseApplicationQuery()
    {
        $query = Application::query();
        
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }
        
        if ($this->organizationFilter !== 'all') {
            $query->where('organization_id', $this->organizationFilter);
        }
        
        return $query;
    }
    
    protected function getBasePlacementQuery()
    {
        $query = Placement::query();
        
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }
        
        if ($this->organizationFilter !== 'all') {
            $query->where('organization_id', $this->organizationFilter);
        }
        
        return $query;
    }
    
    protected function loadKeyMetrics()
    {
        $applications = $this->getBaseApplicationQuery();
        $placements = $this->getBasePlacementQuery();
        
        $this->totalApplications = $applications->count();
        $this->totalPlacements = $placements->count();
        $this->averageMatchScore = $applications->avg('match_score') ?? 0;
        
        // Conversion rate: placements / applications
        $this->conversionRate = $this->totalApplications > 0 
            ? round(($this->totalPlacements / $this->totalApplications) * 100, 2) 
            : 0;
        
        $this->activePlacements = Placement::where('status', 'placed')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
        
        $this->completedPlacements = Placement::where('status', 'completed')->count();
        $this->cancelledPlacements = Placement::where('status', 'cancelled')->count();
        $this->totalOrganizations = Organization::count();
        $this->totalStudents = User::role('student')->count();
        
        // Average time to place (days from application to placement)
        $averageTime = DB::table('applications')
            ->join('placements', 'applications.id', '=', 'placements.application_id')
            ->whereNotNull('placements.created_at')
            ->whereNotNull('applications.created_at')
            ->select(DB::raw('AVG(DATEDIFF(placements.created_at, applications.created_at)) as avg_days'))
            ->first();
        
        $this->averageTimeToPlace = round($averageTime->avg_days ?? 0);
        
        // Success rate: placed applications vs total applications
        $placedCount = Application::whereHas('placement')->count();
        $this->successRate = $this->totalApplications > 0 
            ? round(($placedCount / $this->totalApplications) * 100, 2) 
            : 0;
    }
    
    protected function loadApplicationTrend()
    {
        $startDate = $this->startDate ?? Carbon::now()->subMonths(6);
        $endDate = $this->endDate ?? Carbon::now();
        
        $applications = Application::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $placements = Placement::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $this->applicationTrendData = [
            'labels' => $applications->pluck('date')->map(function($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'applications' => $applications->pluck('total')->toArray(),
            'placements' => $placements->pluck('total')->toArray(),
        ];
    }
    
    protected function loadStatusDistribution()
    {
        $statuses = ApplicationStatus::cases();
        $distribution = [];
        
        foreach ($statuses as $status) {
            $count = Application::where('status', $status->value)->count();
            if ($count > 0) {
                $distribution[] = [
                    'label' => $status->label(),
                    'value' => $count,
                    'color' => $status->color(),
                    'icon' => $status->icon(),
                ];
            }
        }
        
        $this->statusDistributionData = $distribution;
    }
    
    protected function loadPlacementTrend()
    {
        $startDate = $this->startDate ?? Carbon::now()->subMonths(12);
        $endDate = $this->endDate ?? Carbon::now();
        
        $monthlyPlacements = Placement::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $this->placementTrendData = [
            'labels' => $monthlyPlacements->pluck('month')->map(function($month) {
                return Carbon::createFromFormat('Y-m', $month)->format('M Y');
            })->toArray(),
            'placements' => $monthlyPlacements->pluck('total')->toArray(),
        ];
    }
    
    protected function loadTopOrganizations()
    {
        $topOrgs = Application::select('organization_id', DB::raw('COUNT(*) as total'))
            ->with('organization')
            ->groupBy('organization_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        $this->topOrganizationsData = $topOrgs->map(function($item) {
            return [
                'name' => $item->organization->name ?? 'Unknown',
                'total' => $item->total,
                'color' => $this->getRandomColor(),
            ];
        })->toArray();
    }
    
    protected function loadCourseDistribution()
    {
        $courses = DB::table('student_profiles')
            ->select('course_name', DB::raw('COUNT(*) as total'))
            ->whereNotNull('course_name')
            ->groupBy('course_name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        $this->courseDistributionData = $courses->map(function($course) {
            return [
                'label' => $course->course_name,
                'value' => $course->total,
            ];
        })->toArray();
    }
    
    protected function loadInstitutionTypeDistribution()
    {
        $institutions = DB::table('student_profiles')
            ->select('institution_type', DB::raw('COUNT(*) as total'))
            ->whereNotNull('institution_type')
            ->groupBy('institution_type')
            ->get();
        
        $typeLabels = [
            'university' => 'University',
            'college' => 'College',
            'polytechnic' => 'Polytechnic',
            'technical' => 'Technical Institute',
        ];
        
        $this->institutionTypeData = $institutions->map(function($inst) use ($typeLabels) {
            return [
                'label' => $typeLabels[$inst->institution_type] ?? $inst->institution_type,
                'value' => $inst->total,
            ];
        })->toArray();
    }
    
    protected function loadMatchScoreDistribution()
    {
        $ranges = [
            '0-20%' => [0, 20],
            '21-40%' => [21, 40],
            '41-60%' => [41, 60],
            '61-80%' => [61, 80],
            '81-100%' => [81, 100],
        ];
        
        $distribution = [];
        foreach ($ranges as $label => $range) {
            $count = Application::whereBetween('match_score', $range)->count();
            $distribution[] = [
                'label' => $label,
                'value' => $count,
            ];
        }
        
        $this->matchScoreDistributionData = $distribution;
    }
    
    protected function loadMonthlyData()
    {
        $startDate = $this->startDate ?? Carbon::now()->subMonths(12);
        $endDate = $this->endDate ?? Carbon::now();
        
        $monthlyApps = Application::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $monthlyPlaced = Placement::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $this->monthlyApplicationsData = $monthlyApps->pluck('total')->toArray();
        $this->monthlyPlacementsData = $monthlyPlaced->pluck('total')->toArray();
        
        if (empty($this->monthlyApplicationsData)) {
            $this->monthlyApplicationsData = array_fill(0, 12, 0);
            $this->monthlyPlacementsData = array_fill(0, 12, 0);
        }
    }
    
    protected function loadGrowthMetrics()
    {
        $previousPeriod = Carbon::now()->subMonths(1);
        $currentPeriod = Carbon::now();
        
        $currentApplications = Application::whereBetween('created_at', [
            $previousPeriod->copy()->startOfMonth(),
            $currentPeriod->endOfMonth()
        ])->count();
        
        $previousApplications = Application::whereBetween('created_at', [
            $previousPeriod->copy()->subMonth()->startOfMonth(),
            $previousPeriod->copy()->endOfMonth()
        ])->count();
        
        $this->applicationsGrowth = $previousApplications > 0 
            ? round((($currentApplications - $previousApplications) / $previousApplications) * 100, 1)
            : 0;
        
        $currentPlacements = Placement::whereBetween('created_at', [
            $previousPeriod->copy()->startOfMonth(),
            $currentPeriod->endOfMonth()
        ])->count();
        
        $previousPlacements = Placement::whereBetween('created_at', [
            $previousPeriod->copy()->subMonth()->startOfMonth(),
            $previousPeriod->copy()->endOfMonth()
        ])->count();
        
        $this->placementsGrowth = $previousPlacements > 0 
            ? round((($currentPlacements - $previousPlacements) / $previousPlacements) * 100, 1)
            : 0;
        
        $currentStudents = User::role('student')->whereBetween('created_at', [
            $previousPeriod->copy()->startOfMonth(),
            $currentPeriod->endOfMonth()
        ])->count();
        
        $previousStudents = User::role('student')->whereBetween('created_at', [
            $previousPeriod->copy()->subMonth()->startOfMonth(),
            $previousPeriod->copy()->endOfMonth()
        ])->count();
        
        $this->studentsGrowth = $previousStudents > 0 
            ? round((($currentStudents - $previousStudents) / $previousStudents) * 100, 1)
            : 0;
    }
    
    protected function getRandomColor()
    {
        $colors = ['#667eea', '#764ba2', '#f6ad55', '#fc8181', '#48bb78', '#4299e1', '#ed64a6', '#9f7aea'];
        return $colors[array_rand($colors)];
    }

    public function render()
    {
        return view('livewire.admin.applications.analytics', [
            'applicationTrendData' => json_encode($this->applicationTrendData),
            'statusDistributionData' => $this->statusDistributionData,
            'placementTrendData' => json_encode($this->placementTrendData),
            'topOrganizationsData' => json_encode($this->topOrganizationsData),
            'courseDistributionData' => json_encode($this->courseDistributionData),
            'institutionTypeData' => json_encode($this->institutionTypeData),
            'matchScoreDistributionData' => json_encode($this->matchScoreDistributionData),
            'monthlyApplicationsData' => json_encode($this->monthlyApplicationsData),
            'monthlyPlacementsData' => json_encode($this->monthlyPlacementsData),
        ]);
    }
}
