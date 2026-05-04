<?php

namespace App\Livewire\Employer\Organization;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TeamMembers extends Component
{
     use WithPagination;

    public $organization;
    public $search = '';
    public $filterRole = '';
    public $filterStatus = '';
    public $perPage = 10;

    // Add member form
    public $showAddForm = false;
    public $isEditing = false;
    public $editingMemberId = null;

    // Form fields
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $phone = '';
    public $gender = '';
    public $role = 'member';
    public $position = '';
    public $is_primary_contact = false;
    public $is_active = true;

     // Remove member
    public $removingMemberId = null;
    public $removingMemberName = '';

    protected $paginationTheme = 'bootstrap';

     protected $queryString = [
        'search' => ['except' => ''],
        'filterRole' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    protected function rules()
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255'],
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'role' => 'required|in:owner,admin,member',
            'position' => 'nullable|string|max:255',
            'is_primary_contact' => 'boolean',
            'is_active' => 'boolean',
        ];

        // Email unique validation
        if ($this->isEditing) {
            $rules['email'][] = Rule::unique('users', 'email')->ignore($this->editingMemberId);
        } else {
            $rules['email'][] = 'unique:users,email';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        // Password only required for new members
        if (!$this->isEditing && $this->password) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } elseif ($this->isEditing && $this->password) {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

     protected function messages()
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email is already in use.',
            'password.required' => 'Password is required for new members.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role.required' => 'Please select a role.',
        ];
    }

    public function mount()
    {
        $user = User::findOrFail(auth()->id());
        $this->organization = $user->primaryOrganization();

        if (!$this->organization) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'No organization found.');
        }

        // Only owners and admins can manage team
        if (!$this->organization->isOwner($user) && !$this->organization->isAdmin($user)) {
            return redirect()->route('employer.organization.profile')
                ->with('error', 'You do not have permission to manage team members.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    /**
     * Reset form fields
     */
    public function resetForm()
    {
        $this->reset([
            'showAddForm',
            'isEditing',
            'editingMemberId',
            'first_name',
            'last_name',
            'email',
            'password',
            'password_confirmation',
            'phone',
            'gender',
            'role',
            'position',
            'is_primary_contact',
            'is_active',
        ]);

        $this->role = 'member';
        $this->is_active = true;
        $this->is_primary_contact = false;
        $this->resetValidation();
    }

    /**
     * Show add member form
     */
    public function showAddMemberForm()
    {
        $this->resetForm();
        $this->showAddForm = true;
        $this->isEditing = false;
    }

    /**
     * Edit existing member
     */
    public function editMember($userId)
    {
        $member = $this->getTeamMember($userId);
        
        if (!$member) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Team member not found.',
            ]);
            return;
        }

        $this->resetForm();
        $this->isEditing = true;
        $this->editingMemberId = $member->id;
        $this->showAddForm = true;

        $this->first_name = $member->first_name;
        $this->last_name = $member->last_name;
        $this->email = $member->email;
        $this->phone = $member->phone;
        $this->gender = $member->gender;
        $this->role = $member->pivot->role ?? 'member';
        $this->position = $member->pivot->position ?? '';
        $this->is_primary_contact = (bool) ($member->pivot->is_primary_contact ?? false);
        $this->is_active = (bool) ($member->pivot->is_active ?? true);
    }

    /**
     * Save team member (create or update)
     */
    public function saveMember()
    {
        $validatedData = $this->validate();

        try {
            if ($this->isEditing) {
                // Update existing member
                $member = User::findOrFail($this->editingMemberId);

                // Update user details
                $member->update([
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'gender' => $this->gender,
                ]);

                // Update password if provided
                if ($this->password) {
                    $member->update([
                        'password' => Hash::make($this->password),
                    ]);
                }

                // Update organization pivot
                $member->organizations()->updateExistingPivot($this->organization->id, [
                    'role' => $this->role,
                    'position' => $this->position,
                    'is_primary_contact' => $this->is_primary_contact,
                    'is_active' => $this->is_active,
                ]);

                // If this member is set as primary contact, remove from others
                if ($this->is_primary_contact) {
                    $this->organization->users()
                        ->wherePivot('is_primary_contact', true)
                        ->where('user_id', '!=', $member->id)
                        ->updateExistingPivot(
                            $this->organization->users()
                                ->wherePivot('is_primary_contact', true)
                                ->where('user_id', '!=', $member->id)
                                ->pluck('user_id')
                                ->toArray(),
                            ['is_primary_contact' => false]
                        );
                }

                // Ensure employer role
                if (!$member->hasRole('employer')) {
                    $member->assignRole('employer');
                }

                $message = 'Team member updated successfully!';

            } else {
                // Create new member
                $member = User::create([
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'phone' => $this->phone,
                    'gender' => $this->gender,
                    'is_active' => true,
                ]);

                // Assign employer role
                $member->assignRole('employer');

                // Attach to organization
                $member->organizations()->attach($this->organization->id, [
                    'role' => $this->role,
                    'position' => $this->position,
                    'is_primary_contact' => $this->is_primary_contact,
                    'is_active' => $this->is_active,
                ]);

                // If this member is set as primary contact, remove from others
                if ($this->is_primary_contact) {
                    $this->organization->primaryContacts()
                        ->where('user_id', '!=', $member->id)
                        ->each(function ($existingPrimary) {
                            $existingPrimary->organizations()->updateExistingPivot(
                                $this->organization->id,
                                ['is_primary_contact' => false]
                            );
                        });
                }

                $message = 'Team member added successfully!';
            }

            // Log activity
            activity_log(
                "Team member {$member->full_name} " . ($this->isEditing ? 'updated' : 'added') . " to {$this->organization->name}",
                $this->isEditing ? 'updated' : 'created',
                [
                    'organization_id' => $this->organization->id,
                    'member_id' => $member->id,
                    'role' => $this->role,
                ],
                'organization'
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message,
            ]);

            $this->resetForm();

        } catch (\Exception $e) {
            Log::error('Error saving team member:', [
                'error' => $e->getMessage(),
                'organization_id' => $this->organization->id,
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Confirm remove member
     */
    public function confirmRemove($userId)
    {
        $member = $this->getTeamMember($userId);
        
        if (!$member) {
            return;
        }

        // Prevent removing yourself
        if ($member->id === auth()->id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You cannot remove yourself from the organization.',
            ]);
            return;
        }

        // Prevent removing the last owner
        if ($member->pivot->role === 'owner') {
            $ownerCount = $this->organization->owners()->count();
            if ($ownerCount <= 1) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Cannot remove the last owner. Assign another owner first.',
                ]);
                return;
            }
        }

        $this->removingMemberId = $userId;
        $this->removingMemberName = $member->full_name;

        $this->dispatch('confirm-remove', [
            'memberId' => $userId,
            'memberName' => $member->full_name,
        ]);
    }

    /**
     * Remove team member
     */
    public function removeMember($userId)
    {
        try {
            $member = $this->getTeamMember($userId);

            if (!$member) {
                return;
            }

            // Remove from organization (detach)
            $member->organizations()->detach($this->organization->id);

            // Log activity
            activity_log(
                "Team member {$member->full_name} removed from {$this->organization->name}",
                'deleted',
                [
                    'organization_id' => $this->organization->id,
                    'member_id' => $member->id,
                ],
                'organization'
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Team member removed successfully.',
            ]);

            $this->removingMemberId = null;
            $this->removingMemberName = '';

        } catch (\Exception $e) {
            Log::error('Error removing team member:', [
                'error' => $e->getMessage(),
                'organization_id' => $this->organization->id,
                'member_id' => $userId,
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Toggle member active status
     */
    public function toggleActive($userId)
    {
        try {
            $member = $this->getTeamMember($userId);

            if (!$member) {
                return;
            }

            // Prevent deactivating yourself
            if ($member->id === auth()->id()) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'You cannot deactivate yourself.',
                ]);
                return;
            }

            $newStatus = !($member->pivot->is_active ?? true);

            $member->organizations()->updateExistingPivot($this->organization->id, [
                'is_active' => $newStatus,
            ]);

            $status = $newStatus ? 'activated' : 'deactivated';
            
            // Log activity
            activity_log(
                "Team member {$member->full_name} {$status} in {$this->organization->name}",
                'updated',
                [
                    'organization_id' => $this->organization->id,
                    'member_id' => $member->id,
                    'status' => $status,
                ],
                'organization'
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Team member {$status} successfully.",
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling member status:', [
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get team member with pivot data
     */
    protected function getTeamMember($userId)
    {
        return $this->organization->users()
            ->withPivot(['role', 'position', 'is_primary_contact', 'is_active'])
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Get team members with filters
     */
    public function getTeamMembersProperty()
    {
        $query = $this->organization->users()
            ->withPivot(['role', 'position', 'is_primary_contact', 'is_active']);

        // Apply search
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', $searchTerm)
                  ->orWhere('last_name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm);
            });
        }

        // Filter by role
        if ($this->filterRole) {
            $query->wherePivot('role', $this->filterRole);
        }

        // Filter by status
        if ($this->filterStatus !== '') {
            $query->wherePivot('is_active', $this->filterStatus === 'active');
        }

        return $query->orderBy('first_name')
            ->paginate($this->perPage);
    }


    
    public function render()
    {
        return view('livewire.employer.organization.team-members', [
            'teamMembers' => $this->teamMembers,
        ]);
    }
}
