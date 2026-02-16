<?php

namespace App\Livewire\Admin\Reports;

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class UserStatistics extends Component
{
    public $startDate;
    public $endDate;
    public array $userStats = [];
    public array $userGrowthData = [];
    public array $userTypeDistribution = [];
    public array $userDemographics = [];
    public array $activeUsersAnalysis = [];
    public Collection $topInstitutions;

    public function mount()
    {
        // Initialize all properties with default values
        $this->topInstitutions = collect();
        $this->userStats = $this->getDefaultUserStats();
        $this->userGrowthData = $this->getDefaultUserGrowthData();
        $this->userTypeDistribution = $this->getDefaultUserTypeDistribution();
        $this->userDemographics = $this->getDefaultUserDemographics();
        $this->activeUsersAnalysis = $this->getDefaultActiveUsersAnalysis();

        $this->startDate = now()->subYear()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        // Now load the actual data
        $this->loadData();
    }

    public function loadData()
    {
        try {
            $this->userStats = $this->getUserStats();
            $this->userGrowthData = $this->getUserGrowthData();
            $this->userTypeDistribution = $this->getUserTypeDistribution();
            $this->userDemographics = $this->getUserDemographics();
            $this->activeUsersAnalysis = $this->getActiveUsersAnalysis();
            $this->topInstitutions = $this->getTopInstitutions();
        } catch (\Exception $e) {
            // Log error but don't break the page
            Log::error('Error loading user statistics: ' . $e->getMessage());

            // Use default values if there's an error
            $this->userStats = $this->getDefaultUserStats();
            $this->activeUsersAnalysis = $this->getDefaultActiveUsersAnalysis();
        }
    }

    private function getDefaultUserStats(): array
    {
        return [
            'total_users' => 0,
            'active_users' => 0,
            'verified_users' => 0,
            'new_users' => 0,
            'engaged_users' => 0,
            'engagement_rate' => 0,
            'verification_rate' => 0,
        ];
    }

    private function getDefaultUserGrowthData(): array
    {
        return [
            'labels' => [],
            'datasets' => []
        ];
    }

    private function getDefaultUserTypeDistribution(): array
    {
        return [
            'labels' => ['Students', 'Employers', 'Mentors', 'Administrators'],
            'data' => [0, 0, 0, 0],
            'percentages' => [0, 0, 0, 0],
            'colors' => ['#3498db', '#2ecc71', '#f39c12', '#e74c3c']
        ];
    }

    private function getDefaultUserDemographics(): array
    {
        return [
            'gender' => [
                'male' => ['count' => 0, 'percentage' => 0],
                'female' => ['count' => 0, 'percentage' => 0],
                'other' => ['count' => 0, 'percentage' => 0]
            ],
            'age_groups' => [
                '18-24' => 0,
                '25-34' => 0,
                '35-44' => 0,
                '45+' => 0
            ],
            'county_distribution' => collect()
        ];
    }

    private function getDefaultActiveUsersAnalysis(): array
    {
        return [
            'recent_activity' => 0,
            'login_frequency' => [
                'frequent' => 0,
                'occasional' => 0,
                'inactive' => 0
            ],
            'total_users' => 0
        ];
    }

    private function getUserStats(): array
    {
        try {
            $totalUsers = User::count();
            $activeUsers = User::where('is_active', true)->count();
            $verifiedUsers = User::where('is_verified', true)->count();
            $newUsers = User::whereBetween('created_at', [$this->startDate, $this->endDate])->count();

            // User engagement
            $studentsWithApplications = User::role('student')
                ->whereHas('applications', function ($query) {
                    $query->where('submitted_at', '>=', now()->subDays(30));
                })
                ->count();

            $employersWithOpportunities = User::role('employer')
                ->whereHas('employer', function ($query) {
                    $query->whereHas('opportunities', function ($q) {
                        $q->where('created_at', '>=', now()->subDays(30));
                    });
                })
                ->count();

            $mentorsWithMentorships = User::role('mentor')
                ->whereHas('mentor', function ($query) {
                    $query->whereHas('mentorships', function ($q) {
                        $q->where('created_at', '>=', now()->subDays(30));
                    });
                })
                ->count();

            $engagedUsers = $studentsWithApplications + $employersWithOpportunities + $mentorsWithMentorships;

            return [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'verified_users' => $verifiedUsers,
                'new_users' => $newUsers,
                'engaged_users' => $engagedUsers,
                'engagement_rate' => $totalUsers > 0 ? round(($engagedUsers / $totalUsers) * 100, 1) : 0,
                'verification_rate' => $totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100, 1) : 0,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getUserStats: ' . $e->getMessage());
            return $this->getDefaultUserStats();
        }
    }


    private function getUserGrowthData(): array
    {
        try {
            $data = [];
            $labels = [];

            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->format('M Y');

                $students = User::role('student')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $employers = User::role('employer')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $mentors = User::role('mentor')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $data['students'][] = $students;
                $data['employers'][] = $employers;
                $data['mentors'][] = $mentors;
                $data['total'][] = $students + $employers + $mentors;
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Students',
                        'data' => $data['students'] ?? [],
                        'borderColor' => 'rgb(54, 162, 235)',
                        'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'Employers',
                        'data' => $data['employers'] ?? [],
                        'borderColor' => 'rgb(75, 192, 192)',
                        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'Mentors',
                        'data' => $data['mentors'] ?? [],
                        'borderColor' => 'rgb(255, 159, 64)',
                        'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                        'tension' => 0.4
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error in getUserGrowthData: ' . $e->getMessage());
            return $this->getDefaultUserGrowthData();
        }
    }

    private function getUserTypeDistribution(): array
    {
        try {
            $students = User::role('student')->count();
            $employers = User::role('employer')->count();
            $mentors = User::role('mentor')->count();
            $admins = User::role(['admin', 'super_admin'])->count();

            $total = $students + $employers + $mentors + $admins;

            return [
                'labels' => ['Students', 'Employers', 'Mentors', 'Administrators'],
                'data' => [$students, $employers, $mentors, $admins],
                'percentages' => $total > 0 ? [
                    round(($students / $total) * 100, 1),
                    round(($employers / $total) * 100, 1),
                    round(($mentors / $total) * 100, 1),
                    round(($admins / $total) * 100, 1)
                ] : [0, 0, 0, 0],
                'colors' => ['#3498db', '#2ecc71', '#f39c12', '#e74c3c']
            ];
        } catch (\Exception $e) {
            Log::error('Error in getUserTypeDistribution: ' . $e->getMessage());
            return $this->getDefaultUserTypeDistribution();
        }
    }

    private function getUserDemographics(): array
    {
        try {
            $male = User::where('gender', 'male')->count();
            $female = User::where('gender', 'female')->count();
            $other = User::whereNotIn('gender', ['male', 'female'])->orWhereNull('gender')->count();

            $total = $male + $female + $other;

            // Age distribution
            $ageGroups = [
                '18-24' => 0,
                '25-34' => 0,
                '35-44' => 0,
                '45+' => 0
            ];

            $users = User::whereNotNull('date_of_birth')->get();
            foreach ($users as $user) {
                $age = $user->date_of_birth->age;
                if ($age >= 18 && $age <= 24) {
                    $ageGroups['18-24']++;
                } elseif ($age <= 34) {
                    $ageGroups['25-34']++;
                } elseif ($age <= 44) {
                    $ageGroups['35-44']++;
                } else {
                    $ageGroups['45+']++;
                }
            }

            // County distribution (top 10)
            $countyDistribution = User::select('county')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('county')
                ->groupBy('county')
                ->orderByDesc('count')
                ->limit(10)
                ->get();

            return [
                'gender' => [
                    'male' => ['count' => $male, 'percentage' => $total > 0 ? round(($male / $total) * 100, 1) : 0],
                    'female' => ['count' => $female, 'percentage' => $total > 0 ? round(($female / $total) * 100, 1) : 0],
                    'other' => ['count' => $other, 'percentage' => $total > 0 ? round(($other / $total) * 100, 1) : 0]
                ],
                'age_groups' => $ageGroups,
                'county_distribution' => $countyDistribution
            ];
        } catch (\Exception $e) {
            Log::error('Error in getUserDemographics: ' . $e->getMessage());
            return $this->getDefaultUserDemographics();
        }
    }

    private function getActiveUsersAnalysis(): array
    {
        try {
            // Users with activity in last 30 days
            $studentsRecentActivity = User::role('student')
                ->whereHas('applications', function ($query) {
                    $query->where('submitted_at', '>=', now()->subDays(30));
                })
                ->count();

            $employersRecentActivity = User::role('employer')
                ->whereHas('employer', function ($query) {
                    $query->whereHas('opportunities', function ($q) {
                        $q->where('created_at', '>=', now()->subDays(30));
                    });
                })
                ->count();

            $mentorsRecentActivity = User::role('mentor')
                ->whereHas('mentor', function ($query) {
                    $query->whereHas('mentorships', function ($q) {
                        $q->where('created_at', '>=', now()->subDays(30));
                    });
                })
                ->count();

            $recentActivity = $studentsRecentActivity + $employersRecentActivity + $mentorsRecentActivity;

            // User login frequency
            $frequentUsers = User::where('last_login_at', '>=', now()->subDays(7))->count();
            $occasionalUsers = User::where('last_login_at', '>=', now()->subDays(30))
                ->where('last_login_at', '<', now()->subDays(7))
                ->count();
            $inactiveUsers = User::where('last_login_at', '<', now()->subDays(30))
                ->orWhereNull('last_login_at')
                ->count();

            return [
                'recent_activity' => $recentActivity,
                'login_frequency' => [
                    'frequent' => $frequentUsers,
                    'occasional' => $occasionalUsers,
                    'inactive' => $inactiveUsers
                ],
                'total_users' => $frequentUsers + $occasionalUsers + $inactiveUsers
            ];
        } catch (\Exception $e) {
            Log::error('Error in getActiveUsersAnalysis: ' . $e->getMessage());
            return $this->getDefaultActiveUsersAnalysis();
        }
    }

    private function getTopInstitutions(): Collection
    {
        try {
            $institutions = StudentProfile::select('institution_name')
                ->selectRaw('COUNT(*) as student_count')
                ->groupBy('institution_name')
                ->orderByDesc('student_count')
                ->limit(10)
                ->get();

            return $institutions->map(function ($item) {
                return (object) [
                    'institution_name' => $item->institution_name,
                    'student_count' => (int) $item->student_count
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error in getTopInstitutions: ' . $e->getMessage());
            return collect();
        }
    }


    public function render()
    {
        return view('livewire.admin.reports.user-statistics');
    }
}
