<?php

namespace App\Livewire\Employer;

use App\Models\Application;
use App\Models\Placement;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public $organization;
    public $stats = [];
    public $recentApplications = [];
    public $upcomingPlacements = [];
    public $activeOpportunities = [];

    public function mount()
    {
        $user = User::findOrFail(auth()->id());
        $this->organization = $user->primaryOrganization();

        if ($this->organization) {
            $this->loadStats();
            $this->loadRecentData();
        }
    }

    protected function loadStats()
    {
        $org = $this->organization;

        $this->stats = [
            'total_opportunities' => $org->opportunities()->count(),
            'active_opportunities' => $org->opportunities()->where('status', 'active')->count(),
            'total_applications' => Application::whereHas('opportunity', function ($q) use ($org) {
                $q->where('organization_id', $org->id);
            })->count(),
            'pending_applications' => Application::whereHas('opportunity', function ($q) use ($org) {
                $q->where('organization_id', $org->id);
            })->where('status', 'pending')->count(),
            'active_placements' => Placement::where('organization_id', $org->id)
                ->where('status', 'placed')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count(),
            'completed_placements' => Placement::where('organization_id', $org->id)
                ->where('status', 'completed')
                ->count(),
            'total_placed' => Placement::where('organization_id', $org->id)->count(),
        ];
    }

    protected function loadRecentData()
    {
        $org = $this->organization;

        $this->recentApplications = Application::whereHas('opportunity', function ($q) use ($org) {
            $q->where('organization_id', $org->id);
        })
            ->with(['user', 'opportunity'])
            ->latest()
            ->take(5)
            ->get();

        $this->upcomingPlacements = Placement::where('organization_id', $org->id)
            ->where('status', 'placed')
            ->with(['student', 'opportunity'])
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(5)
            ->get();

        $this->activeOpportunities = $org->opportunities()
            ->where('status', 'active')
            ->withCount(['applications', 'placements'])
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.employer.dashboard', [
            'stats' => $this->stats,
            'recentApplications' => $this->recentApplications,
            'upcomingPlacements' => $this->upcomingPlacements,
            'activeOpportunities' => $this->activeOpportunities,
        ]);
    }
}
