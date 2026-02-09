<?php

namespace App\Livewire\Admin\Administrators;

use App\Models\User;
use Livewire\Component;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Show extends Component
{
    public $administrator;
    public $userId;

    // Tabs
    public $activeTab = 'overview';

    // Activity data
    public $recentActivity = [];
    public $permissions = [];

    // Edit mode
    public $isEditing = false;
    public $editSection = null;

    // Edit form fields
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $national_id = '';
    public $date_of_birth = '';
    public $gender = '';
    public $county = '';
    public $constituency = '';
    public $ward = '';
    public $bio = '';
    public $is_active = true;
    public $roles = [];
    public $newPassword = '';
    public $newPasswordConfirmation = '';

    // Modal states
    public $showPasswordModal = false;
    public $showDeleteModal = false;
    public $showActivityModal = false;
    public $showPermissionsModal = false;

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'passwordUpdated' => 'handlePasswordUpdated',
        'statusUpdated' => 'handleStatusUpdated',
        'confirmDelete' => 'deleteAdministrator',
    ];

    public function mount($administrator)
    {
        // If $administrator is an ID, load the user
        if (is_numeric($administrator)) {
            $this->userId = $administrator;
            $this->loadAdministrator();
        }
        // If it's already a User object
        else if ($administrator instanceof User) {
            $this->administrator = $administrator;
            $this->userId = $administrator->id;
        }
        // If it's an array with user data
        else if (is_array($administrator) && isset($administrator['id'])) {
            $this->userId = $administrator['id'];
            $this->loadAdministrator();
        }

        $this->loadRecentActivity();
        $this->loadPermissions();
    }

    public function loadAdministrator()
    {
        $this->administrator = User::with(['roles', 'permissions'])
            ->where('id', $this->userId)
            ->firstOrFail();

        $this->first_name = $this->administrator->first_name;
        $this->last_name = $this->administrator->last_name;
        $this->email = $this->administrator->email;
        $this->phone = $this->administrator->phone;
        $this->national_id = $this->administrator->national_id;
        $this->date_of_birth = $this->administrator->date_of_birth;
        $this->gender = $this->administrator->gender;
        $this->county = $this->administrator->county;
        $this->constituency = $this->administrator->constituency;
        $this->ward = $this->administrator->ward;
        $this->bio = $this->administrator->bio;
        $this->is_active = $this->administrator->is_active;
        $this->roles = $this->administrator->getRoleNames()->toArray();
    }

    public function loadRecentActivity()
    {
        // This would typically come from an activity log table
        // For now, we'll create sample data
        $this->recentActivity = [
            [
                'action' => 'Login',
                'description' => 'Logged into the system',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Chrome on Windows',
                'timestamp' => now()->subMinutes(30),
                'icon' => 'sign-in-alt',
                'color' => 'success',
            ],
            [
                'action' => 'Update',
                'description' => 'Updated mentor profile',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Chrome on Windows',
                'timestamp' => now()->subHours(2),
                'icon' => 'edit',
                'color' => 'info',
            ],
            [
                'action' => 'Create',
                'description' => 'Created new opportunity',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Chrome on Windows',
                'timestamp' => now()->subDays(1),
                'icon' => 'plus',
                'color' => 'primary',
            ],
            [
                'action' => 'Delete',
                'description' => 'Deleted inactive user',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Chrome on Windows',
                'timestamp' => now()->subDays(2),
                'icon' => 'trash',
                'color' => 'danger',
            ],
            [
                'action' => 'System',
                'description' => 'Password changed by system',
                'ip_address' => 'System',
                'user_agent' => 'System',
                'timestamp' => now()->subDays(7),
                'icon' => 'key',
                'color' => 'warning',
            ],
        ];
    }

    public function loadPermissions()
    {
        $this->permissions = [
            'users' => [
                'view_users' => true,
                'create_users' => true,
                'edit_users' => true,
                'delete_users' => false,
                'manage_roles' => true,
            ],
            'students' => [
                'view_students' => true,
                'manage_students' => true,
                'export_students' => true,
            ],
            'employers' => [
                'view_employers' => true,
                'verify_employers' => true,
                'manage_opportunities' => true,
            ],
            'mentors' => [
                'view_mentors' => true,
                'verify_mentors' => true,
                'manage_mentorships' => true,
            ],
            'opportunities' => [
                'view_opportunities' => true,
                'create_opportunities' => true,
                'approve_opportunities' => true,
                'close_opportunities' => true,
            ],
            'applications' => [
                'view_applications' => true,
                'process_applications' => true,
                'manage_interviews' => true,
                'send_offers' => true,
            ],
            'system' => [
                'access_settings' => $this->administrator->hasRole('super-admin'),
                'manage_backups' => $this->administrator->hasRole('super-admin'),
                'view_logs' => true,
                'manage_database' => $this->administrator->hasRole('super-admin'),
            ],
        ];
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->isEditing = false;
        $this->editSection = null;
    }

    public function startEditing($section)
    {
        $this->isEditing = true;
        $this->editSection = $section;
    }

    public function cancelEditing()
    {
        $this->isEditing = false;
        $this->editSection = null;
        $this->loadAdministrator(); // Reload original data
    }

    public function saveProfile()
    {
        $this->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email,' . $this->userId],
            'phone' => ['required', 'string', 'max:20'],
            'national_id' => ['required', 'string', 'max:20', 'unique:users,national_id,' . $this->userId],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'county' => ['required', 'string', 'max:50'],
            'constituency' => ['nullable', 'string', 'max:100'],
            'ward' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        try {
            DB::beginTransaction();

            $this->administrator->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'national_id' => $this->national_id,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'county' => $this->county,
                'constituency' => $this->constituency,
                'ward' => $this->ward,
                'bio' => $this->bio,
                'is_active' => $this->is_active,
            ]);

            DB::commit();

            $this->isEditing = false;
            $this->editSection = null;

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Profile updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ]);
        }
    }

    public function saveRoles()
    {
        $this->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['in:super-admin,admin,moderator'],
        ]);

        try {
            // Check if we're removing the last admin role
            if (
                !$this->administrator->hasAnyRole(['super-admin', 'admin']) &&
                !in_array('super-admin', $this->roles) &&
                !in_array('admin', $this->roles)
            ) {

                $adminCount = User::role(['super-admin', 'admin'])->count();
                if ($adminCount <= 1) {
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'message' => 'Cannot remove the last administrator role from the system.'
                    ]);
                    return;
                }
            }

            $this->administrator->syncRoles($this->roles);

            $this->isEditing = false;
            $this->editSection = null;
            $this->loadAdministrator();
            $this->loadPermissions();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Roles updated successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update roles: ' . $e->getMessage()
            ]);
        }
    }

    public function openPasswordModal()
    {
        $this->newPassword = '';
        $this->newPasswordConfirmation = '';
        $this->showPasswordModal = true;
    }

    public function closePasswordModal()
    {
        $this->showPasswordModal = false;
    }

    public function updatePassword()
    {
        $this->validate([
            'newPassword' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $this->administrator->update([
                'password' => Hash::make($this->newPassword),
            ]);

            $this->closePasswordModal();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Password updated successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update password: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleStatus()
    {
        // Prevent deactivating the last active admin
        if (!$this->administrator->is_active === false) {
            $activeAdmins = User::role(['super-admin', 'admin'])
                ->where('is_active', true)
                ->count();

            if ($activeAdmins <= 1) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Cannot deactivate the last active administrator.'
                ]);
                return;
            }
        }

        try {
            $this->administrator->update([
                'is_active' => !$this->administrator->is_active,
            ]);

            $this->loadAdministrator();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Status updated successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update status: ' . $e->getMessage()
            ]);
        }
    }

    public function openDeleteModal()
    {
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
    }

    public function deleteAdministrator($confirmedEmail = '')
    {
        // Server-side confirmation: verify the email matches
        if ($confirmedEmail !== $this->administrator->email) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Email does not match. Deletion cancelled.']);
            return;
        }

        // Prevent deleting the last admin
        $adminCount = User::role(['super-admin', 'admin'])->count();
        if ($adminCount <= 1) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot delete the last administrator.'
            ]);
            $this->closeDeleteModal();
            return;
        }

        try {
            $adminName = $this->administrator->full_name;
            $this->administrator->delete();

            $this->closeDeleteModal();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Administrator ' . $adminName . ' deleted successfully!'
            ]);

            return redirect()->route('admin.administrators.index');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to delete administrator: ' . $e->getMessage()
            ]);
        }
    }

    public function getAdministratorStats()
    {
        return [
            'days_active' => $this->administrator->created_at->diffInDays(),
            'last_login' => $this->administrator->last_login_at ?
                $this->administrator->last_login_at->diffForHumans() : 'Never',
            'total_logins' => 0, // You would track this in your login system
            'assigned_roles' => $this->administrator->roles->count(),
            'permissions_count' => $this->administrator->getAllPermissions()->count(),
        ];
    }


    public function render()
    {
        return view('livewire.admin.administrators.show', [
            'administrator' => $this->administrator,
            'stats' => $this->getAdministratorStats(),
            'permissions' => $this->permissions,
            'recentActivity' => $this->recentActivity,
            'counties' => getKenyanCounties(),
            'isSuperAdmin' => $this->administrator->hasRole('super-admin'),
            'isCurrentUser' => $this->administrator->id === Auth::id(),
        ]);
    }
}
