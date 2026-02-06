<?php

namespace App\Livewire\Admin\Administrators;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class Create extends Component
{
    // Form fields
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';
    public $national_id = '';
    public $date_of_birth = '';
    public $gender = '';
    public $county = '';
    public $constituency = '';
    public $ward = '';
    public $bio = '';

    // Role assignment
    public $roles = [];
    public $availableRoles = [];

    // Status flags
    public $is_active = true;
    public $send_welcome_email = false;
    public $force_password_change = true;

    // Step tracking
    public $currentStep = 1;
    public $totalSteps = 3;

    // Validation messages
    protected $validationAttributes = [
        'first_name' => 'first name',
        'last_name' => 'last name',
        'email' => 'email address',
        'password' => 'password',
        'national_id' => 'national ID',
        'date_of_birth' => 'date of birth',
        'roles' => 'roles',
    ];

    public function mount()
    {
        $this->loadAvailableRoles();
    }

    public function loadAvailableRoles()
    {
        $this->availableRoles = Role::whereIn('name', ['admin', 'super-admin', 'moderator'])
            ->orderBy('name')
            ->get()
            ->toArray();
            
        // If no roles found, try loading all roles as fallback
        if (empty($this->availableRoles)) {
            $this->availableRoles = Role::all()->toArray();
        }
    }

    public function generatePassword()
    {
        $this->password = Str::random(12);
        $this->password_confirmation = $this->password;
        $this->force_password_change = true;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Strong password generated. User will be prompted to change it on first login.'
        ]);
    }

    public function nextStep()
    {
        $this->validateCurrentStep();
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1:
                $this->validateStepOne();
                break;
            case 2:
                $this->validateStepTwo();
                break;
            case 3:
                $this->validateStepThree();
                break;
        }
    }

     public function validateStepOne()
    {
        $this->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'national_id' => ['required', 'string', 'max:20', 'unique:users'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
        ], [
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'email.unique' => 'This email is already registered.',
            'national_id.unique' => 'This national ID is already registered.',
        ]);
    }

    public function validateStepTwo()
    {
        $this->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'county' => ['required', 'string', 'max:50'],
            'constituency' => ['nullable', 'string', 'max:100'],
            'ward' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
        ]);
    }

    public function validateStepThree()
    {
        $this->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['in:admin,super-admin,moderator'],
            'is_active' => ['boolean'],
        ], [
            'roles.required' => 'Please assign at least one role to the administrator.',
            'roles.*.in' => 'Invalid role selected.',
        ]);
    }

    public function save()
    {
        // Validate all steps
        $this->validateStepOne();
        $this->validateStepTwo();
        $this->validateStepThree();

        try {
            DB::beginTransaction();

            // Create the user
            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'national_id' => $this->national_id,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'county' => $this->county,
                'constituency' => $this->constituency,
                'ward' => $this->ward,
                'bio' => $this->bio,
                'is_active' => $this->is_active,
                'is_verified' => true, // Automatically verify administrators
                'user_type' => 'admin', // Set user type
            ]);

            // Assign roles
            $user->assignRole($this->roles);

            // Set email verification (already verified for admins)
            $user->markEmailAsVerified();

            DB::commit();

            // Send welcome email if requested
            if ($this->send_welcome_email) {
                // You can implement welcome email sending here
                // dispatch(new SendWelcomeEmail($user, $this->password));
            }

            $this->resetForm();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Administrator created successfully!'
            ]);

            // Redirect to the new administrator's profile
            return redirect()->route('admin.administrators.show', $user);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to create administrator: ' . $e->getMessage()
            ]);
        }
    }

    public function resetForm()
    {
        $this->reset([
            'first_name',
            'last_name',
            'email',
            'phone',
            'password',
            'password_confirmation',
            'national_id',
            'date_of_birth',
            'gender',
            'county',
            'constituency',
            'ward',
            'bio',
            'roles',
            'is_active',
            'send_welcome_email',
            'force_password_change'
        ]);
        $this->currentStep = 1;
    }




    public function render()
    {
        return view('livewire.admin.administrators.create', [
            'availableRoles' => $this->availableRoles,
            'counties' => getKenyanCounties(),
        ]);
    }
}
