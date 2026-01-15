<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\StudentProfile;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Register extends Component
{
    public $currentStep = 1;

     // Personal Information
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $date_of_birth = '';
    public $gender = '';
    public $national_id = '';

    // Academic Information
    public $student_reg_number = '';
    public $institution_name = '';
    public $institution_type = '';
    public $course_name = '';
    public $course_level = '';
    public $year_of_study = '';
    public $expected_graduation_year = '';
    public $cgpa = '';

    // Skills & Preferences
    public $county = '';
    public $preferred_location = '';
    public $bio = '';

    // Account Security
    public $password = '';
    public $password_confirmation = '';
    public $terms = false;
    public $marketing_consent = false;

    // Skills and interests as arrays
    public $skills = [];
    public $interests = [];
    public $skillInput = '';
    public $interestInput = '';

    // Add this property to track initialization
    protected $hasInitialized = false;

    public function mount()
    {
        // Only initialize once
        if (!$this->hasInitialized) {
            // Initialize with current year for graduation
            $this->expected_graduation_year = (string) (date('Y') + 2);

            // Set default date of birth (18 years ago)
            $this->date_of_birth = date('Y-m-d', strtotime('-18 years'));

            // Set default gender
            $this->gender = 'male';

            $this->hasInitialized = true;
        }
    }

    // Add this method to reset component state
    public function resetComponent()
    {
        $this->reset();
        $this->mount(); // Re-initialize with defaults
    }

    public function addSkill()
    {
        if (!empty($this->skillInput) && !in_array($this->skillInput, $this->skills)) {
            $this->skills[] = trim($this->skillInput);
            $this->skillInput = '';
        }
    }


    public function removeSkill($index)
    {
        unset($this->skills[$index]);
        $this->skills = array_values($this->skills);
    }

    public function addInterest()
    {
        if (!empty($this->interestInput) && !in_array($this->interestInput, $this->interests)) {
            $this->interests[] = trim($this->interestInput);
            $this->interestInput = '';
        }
    }

    public function removeInterest($index)
    {
        unset($this->interests[$index]);
        $this->interests = array_values($this->interests);
    }

    public function nextStep()
    {
        Log::info('=== NEXT STEP CALLED ===');
        Log::info('Current Step: ' . $this->currentStep);
        Log::info('Email: ' . $this->email);
        Log::info('First Name: ' . $this->first_name);
        Log::info('Last Name: ' . $this->last_name);
        // Validate current step before proceeding
        if ($this->currentStep == 1) {
            $this->validateStep1();
        } elseif ($this->currentStep == 2) {
            $this->validateStep2();
        } elseif ($this->currentStep == 3) {
            $this->validateStep3();
        }

        if ($this->currentStep < 4) {
            $this->currentStep++;

            // Dispatch event for scrolling
            $this->dispatch('scroll-to-top');
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->dispatch('scroll-to-top');
        }
    }

    protected function validateStep1()
    {
        $this->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|unique:users',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|string|in:male,female,other,prefer_not_to_say',
            'national_id' => 'required|string|max:20|unique:users',
        ], [
            'email.unique' => 'This email is already registered. Please use a different email or login.',
            'phone.unique' => 'This phone number is already registered.',
            'national_id.unique' => 'This national ID is already registered.',
        ]);
    }

    protected function validateStep2()
    {
        $currentYear = date('Y');
        $maxYear = $currentYear + 10;

        $this->validate([
            'student_reg_number' => 'required|string|max:50|unique:student_profiles',
            'institution_name' => 'required|string|max:255',
            'institution_type' => 'required|string|in:university,college,polytechnic,technical',
            'course_name' => 'required|string|max:255',
            'course_level' => 'required|string|in:certificate,diploma,bachelor,masters,phd',
            'year_of_study' => 'required|integer|min:1|max:6',
            'expected_graduation_year' => 'required|integer|min:' . $currentYear . '|max:' . $maxYear,
            'cgpa' => 'nullable|numeric|min:0|max:4.0',
        ], [
            'student_reg_number.unique' => 'This student registration number is already registered.',
        ]);
    }

     protected function validateStep3()
    {
        $this->validate([
            'county' => 'required|string|max:100',
            'preferred_location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);
    }

    public function register()
    {
        // Debug: Log current values before validation
        Log::info('Register attempt with email: ' . $this->email);
        Log::info('Full data:', [
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
        ]);

        // Validate step 4
        $this->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => 'required|accepted',
        ]);

        // Final validation of all data
        $this->validateStep1();
        $this->validateStep2();
        $this->validateStep3();

        try {
            // Double-check email uniqueness
            if (User::where('email', $this->email)->exists()) {
                throw new \Exception('Email already exists: ' . $this->email);
            }

            // Create user
            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'national_id' => $this->national_id,
                'county' => $this->county,
                'user_type' => 'student',
                'password' => Hash::make($this->password),
                'is_active' => true,
                'verification_token' => Str::random(60),
                'bio' => $this->bio,
            ]);

            // Assign student role
            $user->assignRole('student');

            // Create student profile
            StudentProfile::create([
                'user_id' => $user->id,
                'student_reg_number' => $this->student_reg_number,
                'institution_name' => $this->institution_name,
                'institution_type' => $this->institution_type,
                'course_name' => $this->course_name,
                'course_level' => $this->course_level,
                'year_of_study' => $this->year_of_study,
                'expected_graduation_year' => $this->expected_graduation_year,
                'cgpa' => $this->cgpa ?: null,
                'skills' => $this->skills,
                'interests' => $this->interests,
                'preferred_location' => $this->preferred_location,
                'attachment_status' => 'seeking',
            ]);

            // Log the user in
            Auth::login($user);

            // Clear component state
            $this->resetComponent();

            // Redirect to dashboard
            return redirect()->route('student.dashboard')
                ->with('success', 'Account created successfully! Please complete your profile.');
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            Log::error('Registration data:', [
                'email' => $this->email,
                'attempted_email' => $this->email,
            ]);

            $this->addError('email', 'Registration failed: ' . $e->getMessage());
        }
    }

    // Add this method to help debug
    public function updated($property)
    {
        // Log when email is updated
        if ($property === 'email') {
            Log::info('Email updated to: ' . $this->email);
        }
    }
    
    public function render()
    {
        return view('livewire.auth.register');
    }
}
