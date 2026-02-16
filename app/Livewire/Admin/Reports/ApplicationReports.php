<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Application;
use App\Models\AttachmentOpportunity;
use App\Models\Organization;
use Livewire\Component;

class ApplicationReports extends Component
{
     // Date filters
    public $startDate;
    public $endDate;
    public $viewType = 'monthly'; // monthly, weekly, daily
    
    // Filter options
    public $selectedStatus = '';
    public $selectedOrganization = '';
    public $selectedOpportunity = '';
    public $selectedCourse = '';
    public $minMatchScore = 0;
    public $maxMatchScore = 100;
    
    // Data containers
    public $applicationStats = [];
    public $applicationTrends = [];
    public $applicationStatusDistribution = [];
    public $matchScoreDistribution = [];
    public $topOpportunities = [];
    public $applicationByCourse = [];
    public $applicationByOrganization = [];
    public $applicationTimelineAnalysis = [];
    public $recentApplications = [];
    
    // Filter options data
    public $organizations = [];
    public $opportunities = [];
    public $courses = [];
    public $statuses = [
        'pending' => 'Pending',
        'reviewing' => 'Reviewing',
        'shortlisted' => 'Shortlisted',
        'offered' => 'Offered',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected'
    ];

    public function mount()
    {
        $this->startDate = now()->subMonths(6)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->loadFilterOptions();
        $this->loadData();
    }

    public function loadFilterOptions()
    {
        // Load organizations for filter dropdown
        $this->organizations = Organization::where('is_verified', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        // Load opportunities for filter dropdown
        $this->opportunities = AttachmentOpportunity::where('status', 'open')
            ->orderBy('title')
            ->pluck('title', 'id')
            ->toArray();

        // Load unique courses from applications via student profiles
        $this->courses = Application::whereHas('student.studentProfile')
            ->with('student.studentProfile')
            ->get()
            ->pluck('student.studentProfile.course_name')
            ->unique()
            ->filter()
            ->values()
            ->toArray();
    }

      public function loadData()
    {
        $this->applicationStats = $this->getApplicationStats();
        $this->applicationTrends = $this->getApplicationTrends();
        $this->applicationStatusDistribution = $this->getApplicationStatusDistribution();
        $this->matchScoreDistribution = $this->getMatchScoreDistribution();
        $this->topOpportunities = $this->getTopOpportunities();
        $this->applicationByCourse = $this->getApplicationByCourse();
        $this->applicationByOrganization = $this->getApplicationByOrganization();
        $this->applicationTimelineAnalysis = $this->getApplicationTimelineAnalysis();
        $this->recentApplications = $this->getRecentApplications();
    }

    public function filter()
    {
        $this->loadData();
        $this->dispatch('applicationChartsUpdated');
        $this->dispatch('close-filters');
    }

    public function resetFilters()
    {
        $this->startDate = now()->subMonths(6)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->selectedStatus = '';
        $this->selectedOrganization = '';
        $this->selectedOpportunity = '';
        $this->selectedCourse = '';
        $this->minMatchScore = 0;
        $this->maxMatchScore = 100;
        $this->viewType = 'monthly';
        
        $this->filter();
    }

    public function updated($property)
    {
        if (in_array($property, ['startDate', 'endDate', 'viewType', 'selectedStatus', 
                                  'selectedOrganization', 'selectedOpportunity', 
                                  'selectedCourse', 'minMatchScore', 'maxMatchScore'])) {
            $this->filter();
        }
    }

    /**
     * Build the base query with all filters applied
     */
    private function baseQuery()
    {
        $query = Application::query()
            ->with(['student', 'opportunity.organization'])
            ->whereBetween('submitted_at', [$this->startDate, $this->endDate]);

        // Apply status filter
        if (!empty($this->selectedStatus)) {
            $query->where('status', $this->selectedStatus);
        }

        // Apply organization filter
        if (!empty($this->selectedOrganization)) {
            $query->whereHas('opportunity', function ($q) {
                $q->where('organization_id', $this->selectedOrganization);
            });
        }

        // Apply opportunity filter
        if (!empty($this->selectedOpportunity)) {
            $query->where('attachment_opportunity_id', $this->selectedOpportunity);
        }

        // Apply course filter
        if (!empty($this->selectedCourse)) {
            $query->whereHas('student.studentProfile', function ($q) {
                $q->where('course_name', 'LIKE', '%' . $this->selectedCourse . '%');
            });
        }

        // Apply match score range filter
        if ($this->minMatchScore > 0) {
            $query->where('match_score', '>=', $this->minMatchScore);
        }
        if ($this->maxMatchScore < 100) {
            $query->where('match_score', '<=', $this->maxMatchScore);
        }

        return $query;
    }

    private function getApplicationStats()
    {
        $query = $this->baseQuery();
        
        $totalApplications = $query->count();
        $uniqueStudents = $query->distinct('user_id')->count('user_id');
        $uniqueOpportunities = $query->distinct('attachment_opportunity_id')->count('attachment_opportunity_id');
        
        // Average match score
        $avgMatchScore = $query->avg('match_score') ?? 0;
        
        // Conversion rates
        $acceptedApplications = (clone $query)->where('status', 'accepted')->count();
        $rejectedApplications = (clone $query)->where('status', 'rejected')->count();
        $pendingApplications = (clone $query)->whereIn('status', ['pending', 'reviewing', 'shortlisted', 'offered'])->count();
        
        $acceptanceRate = $totalApplications > 0 
            ? round(($acceptedApplications / $totalApplications) * 100, 1) 
            : 0;
            
        $rejectionRate = $totalApplications > 0 
            ? round(($rejectedApplications / $totalApplications) * 100, 1) 
            : 0;
        
        // Average review time (from submission to review)
        $avgReviewTime = Application::whereNotNull('reviewed_at')
            ->whereNotNull('submitted_at')
            ->whereBetween('submitted_at', [$this->startDate, $this->endDate])
            ->get()
            ->avg(function ($app) {
                return $app->submitted_at->diffInHours($app->reviewed_at);
            });

        // Applications per opportunity (average)
        $appsPerOpportunity = $uniqueOpportunities > 0 
            ? round($totalApplications / $uniqueOpportunities, 1) 
            : 0;

        return [
            'total_applications' => $totalApplications,
            'unique_students' => $uniqueStudents,
            'unique_opportunities' => $uniqueOpportunities,
            'avg_match_score' => round($avgMatchScore, 1),
            'accepted_applications' => $acceptedApplications,
            'rejected_applications' => $rejectedApplications,
            'pending_applications' => $pendingApplications,
            'acceptance_rate' => $acceptanceRate,
            'rejection_rate' => $rejectionRate,
            'avg_review_time_hours' => round($avgReviewTime, 1),
            'avg_review_time_days' => round($avgReviewTime / 24, 1),
            'applications_per_opportunity' => $appsPerOpportunity,
        ];
    }

    private function getApplicationTrends()
    {
        $data = [];
        $labels = [];
        $applicationsData = [];
        $acceptedData = [];

        if ($this->viewType === 'monthly') {
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->format('M Y');

                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                $applicationsData[] = Application::whereBetween('submitted_at', [$startOfMonth, $endOfMonth])->count();
                $acceptedData[] = Application::whereBetween('submitted_at', [$startOfMonth, $endOfMonth])
                    ->where('status', 'accepted')
                    ->count();
            }
        } elseif ($this->viewType === 'weekly') {
            for ($i = 7; $i >= 0; $i--) {
                $date = now()->subWeeks($i);
                $labels[] = 'Week ' . $date->weekOfYear . ' (' . $date->format('M d') . ')';

                $startOfWeek = $date->copy()->startOfWeek();
                $endOfWeek = $date->copy()->endOfWeek();

                $applicationsData[] = Application::whereBetween('submitted_at', [$startOfWeek, $endOfWeek])->count();
                $acceptedData[] = Application::whereBetween('submitted_at', [$startOfWeek, $endOfWeek])
                    ->where('status', 'accepted')
                    ->count();
            }
        } else {
            for ($i = 13; $i >= 0; $i--) {
                $date = now()->subDays($i * 2);
                $labels[] = $date->format('M d');

                $startOfDay = $date->copy()->startOfDay();
                $endOfDay = $date->copy()->endOfDay();

                $applicationsData[] = Application::whereBetween('submitted_at', [$startOfDay, $endOfDay])->count();
                $acceptedData[] = Application::whereBetween('submitted_at', [$startOfDay, $endOfDay])
                    ->where('status', 'accepted')
                    ->count();
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Applications',
                    'data' => $applicationsData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ],
                [
                    'label' => 'Accepted Applications',
                    'data' => $acceptedData,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];
    }

    private function getApplicationStatusDistribution()
    {
        $statuses = ['pending', 'reviewing', 'shortlisted', 'offered', 'accepted', 'rejected'];
        $data = [];
        $colors = [
            'pending' => '#9CA3AF',    // gray
            'reviewing' => '#F59E0B',   // amber
            'shortlisted' => '#3B82F6',  // blue
            'offered' => '#8B5CF6',      // purple
            'accepted' => '#10B981',      // green
            'rejected' => '#EF4444'       // red
        ];

        $query = $this->baseQuery();
        $total = $query->count();

        foreach ($statuses as $status) {
            $count = (clone $query)->where('status', $status)->count();
            $data[] = [
                'status' => ucfirst($status),
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
                'color' => $colors[$status]
            ];
        }

        return $data;
    }

    private function getMatchScoreDistribution()
    {
        $ranges = [
            '0-20' => [0, 20],
            '21-40' => [21, 40],
            '41-60' => [41, 60],
            '61-80' => [61, 80],
            '81-100' => [81, 100]
        ];

        $query = $this->baseQuery();
        $total = $query->count();
        $data = [];

        foreach ($ranges as $label => [$min, $max]) {
            $count = (clone $query)
                ->where('match_score', '>=', $min)
                ->where('match_score', '<=', $max)
                ->count();

            $data[] = [
                'range' => $label,
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0
            ];
        }

        return $data;
    }

    private function getTopOpportunities()
    {
        return AttachmentOpportunity::withCount(['applications' => function ($query) {
                $query->whereBetween('submitted_at', [$this->startDate, $this->endDate]);
            }])
            ->withCount(['applications as accepted_count' => function ($query) {
                $query->whereBetween('submitted_at', [$this->startDate, $this->endDate])
                    ->where('status', 'accepted');
            }])
            ->withCount(['applications as rejected_count' => function ($query) {
                $query->whereBetween('submitted_at', [$this->startDate, $this->endDate])
                    ->where('status', 'rejected');
            }])
            ->having('applications_count', '>', 0)
            ->orderByDesc('applications_count')
            ->limit(10)
            ->get()
            ->map(function ($opportunity) {
                return (object)[
                    'title' => $opportunity->title,
                    'organization' => $opportunity->organization->name ?? 'N/A',
                    'total_applications' => $opportunity->applications_count,
                    'accepted' => $opportunity->accepted_count,
                    'rejected' => $opportunity->rejected_count,
                    'pending' => $opportunity->applications_count - $opportunity->accepted_count - $opportunity->rejected_count,
                    'acceptance_rate' => $opportunity->applications_count > 0 
                        ? round(($opportunity->accepted_count / $opportunity->applications_count) * 100, 1)
                        : 0,
                    'avg_match_score' => round(Application::where('attachment_opportunity_id', $opportunity->id)
                        ->whereBetween('submitted_at', [$this->startDate, $this->endDate])
                        ->avg('match_score') ?? 0, 1)
                ];
            });
    }

    private function getApplicationByCourse()
    {
        return Application::whereBetween('submitted_at', [$this->startDate, $this->endDate])
            ->whereHas('student.studentProfile')
            ->with('student.studentProfile')
            ->get()
            ->groupBy(function ($application) {
                return $application->student->studentProfile->course_name ?? 'Unknown';
            })
            ->map(function ($applications, $course) {
                $total = $applications->count();
                $accepted = $applications->where('status', 'accepted')->count();
                $rejected = $applications->where('status', 'rejected')->count();
                
                return (object)[
                    'course' => $course ?: 'Unknown',
                    'total_applications' => $total,
                    'accepted' => $accepted,
                    'rejected' => $rejected,
                    'pending' => $total - $accepted - $rejected,
                    'acceptance_rate' => $total > 0 ? round(($accepted / $total) * 100, 1) : 0,
                    'avg_match_score' => round($applications->avg('match_score') ?? 0, 1)
                ];
            })
            ->sortByDesc('total_applications')
            ->take(15)
            ->values()
            ->toArray();
    }

    private function getApplicationByOrganization()
    {
        return Organization::whereHas('opportunities.applications', function ($query) {
                $query->whereBetween('submitted_at', [$this->startDate, $this->endDate]);
            })
            ->withCount(['opportunities'])
            ->get()
            ->map(function ($organization) {
                $applications = Application::whereBetween('submitted_at', [$this->startDate, $this->endDate])
                    ->whereHas('opportunity', function ($query) use ($organization) {
                        $query->where('organization_id', $organization->id);
                    });
                
                $total = $applications->count();
                $accepted = (clone $applications)->where('status', 'accepted')->count();
                
                return (object)[
                    'name' => $organization->name,
                    'total_applications' => $total,
                    'accepted' => $accepted,
                    'opportunities_count' => $organization->opportunities_count,
                    'acceptance_rate' => $total > 0 ? round(($accepted / $total) * 100, 1) : 0,
                    'avg_applications_per_opportunity' => $organization->opportunities_count > 0 
                        ? round($total / $organization->opportunities_count, 1) 
                        : 0
                ];
            })
            ->filter(function ($org) {
                return $org->total_applications > 0;
            })
            ->sortByDesc('total_applications')
            ->take(10)
            ->values()
            ->toArray();
    }

    private function getApplicationTimelineAnalysis()
    {
        $query = $this->baseQuery();
        
        // Average time in each status
        $statusTransitions = [];
        $applications = (clone $query)->whereNotNull('reviewed_at')->get();
        
        $avgTimeToReview = $applications->avg(function ($app) {
            return $app->submitted_at->diffInHours($app->reviewed_at);
        });
        
        // Applications by day of week
        $applicationsByDay = Application::whereBetween('submitted_at', [$this->startDate, $this->endDate])
            ->get()
            ->groupBy(function ($app) {
                return $app->submitted_at->format('l'); // Day name
            })
            ->map(function ($apps, $day) {
                return [
                    'day' => $day,
                    'count' => $apps->count()
                ];
            })
            ->values()
            ->toArray();
            
        // Applications by hour of day
        $applicationsByHour = Application::whereBetween('submitted_at', [$this->startDate, $this->endDate])
            ->get()
            ->groupBy(function ($app) {
                return $app->submitted_at->format('H'); // Hour (00-23)
            })
            ->map(function ($apps, $hour) {
                return [
                    'hour' => $hour . ':00',
                    'count' => $apps->count()
                ];
            })
            ->sortBy('hour')
            ->values()
            ->toArray();

        return [
            'avg_time_to_review_hours' => round($avgTimeToReview, 1),
            'avg_time_to_review_days' => round($avgTimeToReview / 24, 1),
            'applications_by_day' => $applicationsByDay,
            'applications_by_hour' => $applicationsByHour,
            'peak_submission_day' => $this->getPeakSubmissionTime($applicationsByDay, 'day'),
            'peak_submission_hour' => $this->getPeakSubmissionTime($applicationsByHour, 'hour')
        ];
    }

    private function getPeakSubmissionTime($data, $key)
    {
        if (empty($data)) return 'N/A';
        
        $peak = collect($data)->sortByDesc('count')->first();
        return $peak[$key] ?? 'N/A';
    }

    private function getRecentApplications()
    {
        return $this->baseQuery()
            ->with(['student', 'opportunity.organization'])
            ->latest('submitted_at')
            ->limit(10)
            ->get()
            ->map(function ($application) {
                return (object)[
                    'id' => $application->id,
                    'student_name' => $application->student->name ?? 'N/A',
                    'student_email' => $application->student->email ?? 'N/A',
                    'opportunity_title' => $application->opportunity->title ?? 'N/A',
                    'organization' => $application->opportunity->organization->name ?? 'N/A',
                    'match_score' => $application->match_score,
                    'status' => $application->status,
                    'status_badge' => $application->status_badge,
                    'submitted_at' => $application->submitted_at ? $application->submitted_at->format('M d, Y H:i') : 'N/A',
                    'reviewed_at' => $application->reviewed_at ? $application->reviewed_at->format('M d, Y H:i') : 'Not reviewed'
                ];
            });
    }

    public function exportToCsv()
    {
        $applications = $this->baseQuery()
            ->with(['student', 'opportunity.organization'])
            ->get();
        
        $csvData = [];
        
        // Headers
        $csvData[] = [
            'Student Name',
            'Student Email',
            'Opportunity',
            'Organization',
            'Match Score',
            'Status',
            'Submitted Date',
            'Reviewed Date',
            'Cover Letter'
        ];
        
        // Data rows
        foreach ($applications as $app) {
            $csvData[] = [
                $app->student->name ?? 'N/A',
                $app->student->email ?? 'N/A',
                $app->opportunity->title ?? 'N/A',
                $app->opportunity->organization->name ?? 'N/A',
                $app->match_score ?? 0,
                ucfirst($app->status),
                $app->submitted_at ? $app->submitted_at->format('Y-m-d H:i:s') : 'N/A',
                $app->reviewed_at ? $app->reviewed_at->format('Y-m-d H:i:s') : 'N/A',
                strip_tags($app->cover_letter ?? 'N/A')
            ];
        }
        
        // Generate CSV file
        $fileName = 'applications_report_' . now()->format('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ]);
    }



    public function render()
    {
        return view('livewire.admin.reports.application-reports');
    }
}
