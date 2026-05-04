<?php

namespace App\Livewire\Employer\Organization;

use App\Models\Application;
use App\Models\Placement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Profile extends Component
{
    use WithPagination;

    public $organization;
    public $activeTab = 'overview';

    // Stats
    public $totalOpportunities = 0;
    public $activeOpportunities = 0;
    public $totalApplications = 0;
    public $pendingApplications = 0;
    public $totalPlacements = 0;
    public $activePlacements = 0;
    public $availableSlots = 0;

    protected $queryString = [
        'activeTab' => ['except' => 'overview'],
    ];

    public function mount()
    {
        $user = User::findOrFail(Auth::user()->id);
        $this->organization = $user->primaryOrganization();

        if (!$this->organization) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'No organization found. Please contact an administrator.');
        }

        $this->loadStats();
    }

    /**
     * Load organization statistics
     */
    public function loadStats()
    {
        if (!$this->organization) return;

        // Opportunities stats
        $this->totalOpportunities = $this->organization->opportunities()->count();
        $this->activeOpportunities = $this->organization->opportunities()
            ->where('status', 'active')
            ->count();

        // Applications stats
        $this->totalApplications = Application::where('organization_id', $this->organization->id)->count();
        $this->pendingApplications = Application::where('organization_id', $this->organization->id)
            ->where('status', 'pending')
            ->count();

        // Placements stats
        $this->totalPlacements = Placement::where('organization_id', $this->organization->id)->count();
        $this->activePlacements = Placement::where('organization_id', $this->organization->id)
            ->where('status', 'placed')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->count();

        // Available slots
        $this->availableSlots = $this->organization->available_slots;
    }

    /**
     * Switch active tab
     */
    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * Get department list
     */
    public function getDepartmentsProperty()
    {
        if (!$this->organization || !$this->organization->departments) {
            return collect([]);
        }

        return collect($this->organization->departments);
    }

    /**
     * Get team members
     */
    public function getTeamMembersProperty()
    {
        if (!$this->organization) {
            return collect([]);
        }

        return $this->organization->users()
            ->withPivot(['role', 'position', 'is_primary_contact', 'is_active'])
            ->wherePivot('is_active', true)
            ->get();
    }

    /**
     * Get recent opportunities
     */
    public function getRecentOpportunitiesProperty()
    {
        if (!$this->organization) {
            return collect([]);
        }

        return $this->organization->opportunities()
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * Get active placements
     */
    public function getCurrentPlacementsProperty()
    {
        if (!$this->organization) {
            return collect([]);
        }

        return Placement::where('organization_id', $this->organization->id)
            ->where('status', 'placed')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->with('student')
            ->latest('start_date')
            ->take(10)
            ->get();
    }

    /**
     * Get recent applications
     */
    public function getRecentApplicationsProperty()
    {
        if (!$this->organization) {
            return collect([]);
        }

        return Application::where('organization_id', $this->organization->id)
            ->with(['student', 'opportunity'])
            ->latest()
            ->take(10)
            ->get();
    }


    public function render()
    {
        if (!$this->organization) {
            return view('livewire.employer.organization.no-organization');
        }
        return view('livewire.employer.organization.profile', [
            'organization' => $this->organization,
            'departments' => $this->departments,
            'teamMembers' => $this->teamMembers,
            'recentOpportunities' => $this->recentOpportunities,
            'currentPlacements' => $this->currentPlacements,
            'recentApplications' => $this->recentApplications,
        ]);
    }
}
