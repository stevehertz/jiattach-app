<?php

namespace App\Livewire\Admin\Reports;

use App\Models\AttachmentOpportunity;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OpportunityAnalytics extends Component
{
    public $startDate;
    public $endDate;
    public $opportunityStats = [];
    public $opportunityTrends = [];
    public $opportunityTypeAnalysis = [];
    public $topPerformingOpportunities = [];
    public $industryAnalysis = [];
    public $locationAnalysis = [];

    public function mount()
    {
        $this->startDate = now()->subYear()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->loadData();
    }

    public function loadData()
    {
        $this->opportunityStats = $this->getOpportunityStats();
        $this->opportunityTrends = $this->getOpportunityTrends();
        $this->opportunityTypeAnalysis = $this->getOpportunityTypeAnalysis();
        $this->topPerformingOpportunities = $this->getTopPerformingOpportunities();
        $this->industryAnalysis = $this->getIndustryAnalysis();
        $this->locationAnalysis = $this->getLocationAnalysis();
    }

    private function getOpportunityStats()
    {
        $totalOpportunities = AttachmentOpportunity::whereBetween('created_at', [$this->startDate, $this->endDate])->count();
        $publishedOpportunities = AttachmentOpportunity::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'published')
            ->count();
        $activeOpportunities = AttachmentOpportunity::where('status', 'published')
            ->where('deadline', '>=', now())
            ->whereRaw('slots_available > COALESCE((SELECT COUNT(*) FROM placements WHERE attachment_opportunities.id = placements.attachment_opportunity_id AND placements.deleted_at IS NULL), 0)')
            ->count();
        $filledOpportunities = AttachmentOpportunity::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'filled')
            ->count();

        // Average applications per opportunity
        $avgApplications = AttachmentOpportunity::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->withCount('applications')
            ->get()
            ->avg('applications_count');

        return [
            'total_opportunities' => $totalOpportunities,
            'published_opportunities' => $publishedOpportunities,
            'active_opportunities' => $activeOpportunities,
            'filled_opportunities' => $filledOpportunities,
            'avg_applications' => round($avgApplications, 1),
            'fill_rate' => $totalOpportunities > 0 ? round(($filledOpportunities / $totalOpportunities) * 100, 1) : 0,
        ];
    }

    private function getOpportunityTrends()
    {
        $data = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $published = AttachmentOpportunity::where('status', 'published')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $filled = AttachmentOpportunity::where('status', 'filled')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $data['published'][] = $published;
            $data['filled'][] = $filled;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Published',
                    'data' => $data['published'],
                    'borderColor' => 'rgb(54, 162, 235)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'tension' => 0.4
                ],
                [
                    'label' => 'Filled',
                    'data' => $data['filled'],
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'tension' => 0.4
                ]
            ]
        ];
    }

    private function getOpportunityTypeAnalysis()
    {
        $types = [
            'internship',
            'attachment',
            'volunteer',
            'research',
            'part_time',
            'full_time'
        ];

        $analysis = [];

        foreach ($types as $type) {
            $count = AttachmentOpportunity::where('type', $type)
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->count();

            if ($count > 0) {
                // Get average applications for this type
                $avgApplications = AttachmentOpportunity::where('type', $type)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->withCount('applications')
                    ->get()
                    ->avg('applications_count');

                // Get fill rate for this type
                $filled = AttachmentOpportunity::where('type', $type)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->where('status', 'filled')
                    ->count();

                $analysis[] = [
                    'type' => ucfirst(str_replace('_', ' ', $type)),
                    'count' => $count,
                    'avg_applications' => round($avgApplications, 1),
                    'fill_rate' => $count > 0 ? round(($filled / $count) * 100, 1) : 0
                ];
            }
        }

        return $analysis;
    }

    private function getTopPerformingOpportunities()
    {
        return AttachmentOpportunity::with(['organization', 'applications'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->withCount('applications')
            ->orderByDesc('applications_count')
            ->limit(10)
            ->get()
            ->map(function ($opportunity) {
                $opportunity->fill_rate = $opportunity->slots_available > 0
                    ? round(($opportunity->slots_filled / $opportunity->slots_available) * 100, 1)
                    : 0;
                return $opportunity;
            });
    }

    private function getIndustryAnalysis()
    {
        // Get employers with opportunities in selected period
        $organizations = Organization::with(['opportunities' => function ($query) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }])->get();

        $industryData = [];

        foreach ($organizations as $organization) {
            if (!empty($organization->industry) && $organization->opportunities->count() > 0) {
                $industry = $organization->industry;

                if (!isset($industryData[$industry])) {
                    $industryData[$industry] = [
                        'industry' => $industry,
                        'opportunity_count' => 0,
                        'application_count' => 0,
                        'employer_count' => 0
                    ];
                }

                $industryData[$industry]['opportunity_count'] += $organization->opportunities->count();
                $industryData[$industry]['employer_count']++;

                // Count applications for these opportunities
                foreach ($organization->opportunities as $opportunity) {
                    $industryData[$industry]['application_count'] += $opportunity->applications_count ?? 0;
                }
            }
        }

        // Calculate averages and convert to array
        $analysis = collect($industryData)->map(function ($item) {
            $item['avg_applications'] = $item['opportunity_count'] > 0
                ? round($item['application_count'] / $item['opportunity_count'], 1)
                : 0;
            return $item;
        })->sortByDesc('opportunity_count')->values()->take(10);

        return $analysis;
    }

    private function getLocationAnalysis()
    {
        $locations = AttachmentOpportunity::select('location', 'county')
            ->selectRaw('COUNT(DISTINCT attachment_opportunities.id) as opportunity_count')
            ->selectRaw('COUNT(DISTINCT applications.id) as total_applications')
            ->selectRaw('COALESCE(SUM(DISTINCT placement_counts.total), 0) as total_filled')
            ->leftJoin('applications', function ($join) {
                $join->on('attachment_opportunities.id', '=', 'applications.attachment_opportunity_id');
                // Uncomment the next line if your Application model uses SoftDeletes
                // ->whereNull('applications.deleted_at');
            })
            ->leftJoinSub(
                DB::table('placements')
                    ->select('attachment_opportunity_id', DB::raw('COUNT(*) as total'))
                    ->whereNull('deleted_at')
                    ->groupBy('attachment_opportunity_id'),
                'placement_counts',
                'attachment_opportunities.id',
                '=',
                'placement_counts.attachment_opportunity_id'
            )
            ->whereBetween('attachment_opportunities.created_at', [$this->startDate, $this->endDate])
            ->whereNotNull('location')
            ->groupBy('location', 'county')
            ->orderByDesc('opportunity_count')
            ->limit(15)
            ->get()
            ->map(function ($location) {
                $location->fill_rate = $location->opportunity_count > 0
                    ? round(($location->total_filled / ($location->opportunity_count * 10)) * 100, 1)
                    : 0;
                $location->avg_applications = $location->opportunity_count > 0
                    ? round($location->total_applications / $location->opportunity_count, 1)
                    : 0;
                return $location;
            });

        return $locations;
    }

    public function render()
    {
        return view('livewire.admin.reports.opportunity-analytics');
    }
}
