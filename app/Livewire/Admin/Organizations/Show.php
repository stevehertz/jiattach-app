<?php

namespace App\Livewire\Admin\Organizations;

use App\Models\Organization;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Organization $organization;
    public $activeTab = 'overview';
    public $showAddUserModal = false;
    public $showEditModal = false;

    // User management properties
    public $selectedUserId;
    public $userRole = 'member';
    public $userPosition;
    public $isPrimaryContact = false;

    // Activity log
    public $activityLog = [];

    protected $listeners = [
        'refresh' => '$refresh',
        'userAdded' => 'handleUserAdded'
    ];

    protected $rules = [
        'userRole' => 'required|in:owner,admin,member,contact',
        'userPosition' => 'nullable|string|max:255',
        'isPrimaryContact' => 'boolean'
    ];

    public function mount(Organization $organization)
    {
        $this->organization = $organization->load([
            'users' => function ($query) {
                $query->withPivot(['role', 'position', 'is_primary_contact', 'is_active', 'created_at'])
                    ->orderBy('pivot_created_at', 'desc');
            },
            'opportunities' => function ($query) {
                $query->withCount('applications')
                    ->latest();
            },
            'placements' => function ($query) {
                $query->with('user')
                    ->latest();
            }
        ]);

        $this->loadActivityLog();
    }

    protected function loadActivityLog()
    {
        // Load recent activity from various sources
        $this->activityLog = collect()
            ->merge($this->organization->opportunities->map(fn($opp) => [
                'type' => 'opportunity',
                'action' => 'created',
                'title' => $opp->title,
                'user' => 'System',
                'time' => $opp->created_at
            ]))
            ->merge($this->organization->placements->map(fn($placement) => [
                'type' => 'placement',
                'action' => 'placed',
                'title' => "Student: {$placement->user?->full_name}",
                'user' => 'System',
                'time' => $placement->created_at
            ]))
            ->merge($this->organization->users->map(fn($user) => [
                'type' => 'user',
                'action' => 'added',
                'title' => $user->full_name,
                'user' => 'System',
                'time' => $user->pivot->created_at
            ]))
            ->sortByDesc('time')
            ->take(10)
            ->values()
            ->toArray();
    }

     public function addUser()
    {
        $this->reset(['selectedUserId', 'userRole', 'userPosition', 'isPrimaryContact']);
        $this->showAddUserModal = true;
    }

    public function saveUser()
    {
        $this->validate();

        $this->organization->users()->attach($this->selectedUserId, [
            'role' => $this->userRole,
            'position' => $this->userPosition,
            'is_primary_contact' => $this->isPrimaryContact,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // If this is set as primary contact, remove primary contact from others
        if ($this->isPrimaryContact) {
            $this->organization->users()
                ->wherePivot('is_primary_contact', true)
                ->wherePivot('user_id', '!=', $this->selectedUserId)
                ->updateExistingPivot($this->organization->users->pluck('id'), [
                    'is_primary_contact' => false
                ]);
        }

        $this->showAddUserModal = false;
        $this->dispatch('toastr:success', message: 'User added successfully.');
        $this->dispatch('refresh');
    }

    public function removeUser($userId)
    {
        $user = $this->organization->users()->find($userId);
        
        // Don't allow removing the last owner
        if ($user && $user->pivot->role === 'owner') {
            $ownerCount = $this->organization->users()
                ->wherePivot('role', 'owner')
                ->count();
            
            if ($ownerCount <= 1) {
                $this->dispatch('toastr:error', message: 'Cannot remove the last owner.');
                return;
            }
        }

        $this->organization->users()->detach($userId);
        $this->dispatch('toastr:success', message: 'User removed successfully.');
        $this->dispatch('refresh');
    }

    public function updateUserRole($userId, $role)
    {
        $this->organization->users()->updateExistingPivot($userId, [
            'role' => $role,
            'updated_at' => now()
        ]);
        
        $this->dispatch('toastr:success', message: 'User role updated.');
        $this->dispatch('refresh');
    }

    public function toggleUserActive($userId)
    {
        $user = $this->organization->users()->find($userId);
        $this->organization->users()->updateExistingPivot($userId, [
            'is_active' => !$user->pivot->is_active,
            'updated_at' => now()
        ]);
        
        $this->dispatch('toastr:success', message: 'User status updated.');
        $this->dispatch('refresh');
    }

    public function toggleVerification()
    {
        $this->organization->update([
            'is_verified' => !$this->organization->is_verified,
            'verified_at' => !$this->organization->is_verified ? now() : null
        ]);
        
        $this->dispatch('toastr:success', message: 'Verification status updated.');
        $this->dispatch('refresh');
    }

    public function toggleActive()
    {
        $this->organization->update([
            'is_active' => !$this->organization->is_active
        ]);
        
        $this->dispatch('toastr:success', message: 'Organization status updated.');
        $this->dispatch('refresh');
    }

    public function searchUsers($search = '')
    {
        return User::whereDoesntHave('organizations', function($query) {
                $query->where('organization_id', $this->organization->id);
            })
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->limit(10)
            ->get();
    }

    public function render()
    {
        $stats = [
            'total_opportunities' => $this->organization->opportunities->count(),
            'active_opportunities' => $this->organization->opportunities()
                ->where('deadline', '>=', now())
                ->where('status', 'open')
                ->count(),
            'total_placements' => $this->organization->placements->count(),
            'active_placements' => $this->organization->placements()
                ->where('status', 'placed')
                ->whereDate('end_date', '>=', now())
                ->count(),
            'total_users' => $this->organization->users->count(),
            'available_slots' => $this->organization->available_slots,
            'capacity_used' => $this->organization->placements()
                ->where('status', 'placed')
                ->count() . '/' . $this->organization->max_students_per_intake
        ];

        $recentOpportunities = $this->organization->opportunities()
            ->withCount('applications')
            ->latest()
            ->limit(5)
            ->get();

        $recentPlacements = $this->organization->placements()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        $availableUsers = $this->searchUsers();

        return view('livewire.admin.organizations.show', [
            'stats' => $stats,
            'recentOpportunities' => $recentOpportunities,
            'recentPlacements' => $recentPlacements,
            'availableUsers' => $availableUsers
        ]);
    }
}
