<?php

namespace App\Livewire\Admin\Mentors;

use App\Models\Mentor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Create extends Component
{
     // User Information
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';

    // Mentor Information
    public $job_title = '';
    public $company = '';
    public $years_of_experience = '';
    public $areas_of_expertise = [];
    public $industries = [];
    public $bio = '';
    public $mentoring_philosophy = '';

    // Contact & Social
    public $linkedin_profile = '';
    public $twitter_profile = '';
    public $website = '';

    // Mentoring Details
    public $max_mentees = 5;
    public $availability = 'available';
    public $mentoring_focus = 'general';
    public $meeting_preference = 'video';
    public $session_duration_minutes = 60;

    // Financial
    public $hourly_rate = '';
    public $offers_free_sessions = false;
    public $free_sessions_per_month = 2;

    // Languages & Education
    public $languages = ['English'];
    public $education_background = [];

    // Verification
    public $is_verified = false;
    public $is_featured = false;

    // Arrays for dropdowns
    public $expertiseOptions = [];
    public $industryOptions = [];
    public $languageOptions = ['English', 'Swahili', 'French', 'Spanish', 'Arabic', 'Chinese', 'German', 'Other'];
    public $availabilityOptions = [
        'available' => 'Available',
        'limited' => 'Limited Availability',
        'fully_booked' => 'Fully Booked',
        'unavailable' => 'Currently Unavailable',
    ];
    public $focusOptions = [
        'career_development' => 'Career Development',
        'technical_skills' => 'Technical Skills',
        'leadership' => 'Leadership',
        'entrepreneurship' => 'Entrepreneurship',
        'industry_specific' => 'Industry Specific',
        'general' => 'General Mentoring',
    ];
    public $meetingOptions = [
        'video' => 'Video Call',
        'phone' => 'Phone Call',
        'in_person' => 'In Person',
        'hybrid' => 'Hybrid',
    ];
    public $durationOptions = [
        30 => '30 minutes',
        45 => '45 minutes',
        60 => '1 hour',
        90 => '1.5 hours',
        120 => '2 hours',
    ];

    public function mount()
    {
        $this->expertiseOptions = getCommonSkills();
        $this->industryOptions = getIndustries();
    }

    public function addEducation()
    {
        $this->education_background[] = [
            'degree' => '',
            'field' => '',
            'institution' => '',
            'year' => '',
        ];
    }

    public function removeEducation($index)
    {
        unset($this->education_background[$index]);
        $this->education_background = array_values($this->education_background);
    }

    public function addExpertise($expertise)
    {
        if (!in_array($expertise, $this->areas_of_expertise)) {
            $this->areas_of_expertise[] = $expertise;
        }
    }

    public function removeExpertise($expertise)
    {
        $this->areas_of_expertise = array_diff($this->areas_of_expertise, [$expertise]);
    }

    public function addIndustry($industry)
    {
        if (!in_array($industry, $this->industries)) {
            $this->industries[] = $industry;
        }
    }

    public function removeIndustry($industry)
    {
        $this->industries = array_diff($this->industries, [$industry]);
    }

    public function addLanguage($language)
    {
        if (!in_array($language, $this->languages)) {
            $this->languages[] = $language;
        }
    }

    public function removeLanguage($language)
    {
        $this->languages = array_diff($this->languages, [$language]);
    }

    public function saveMentor()
    {
        // Validate user data
        $userValidated = $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Validate mentor data
        $mentorValidated = $this->validate([
            'job_title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'years_of_experience' => 'required|integer|min:0',
            'areas_of_expertise' => 'required|array|min:1',
            'industries' => 'required|array|min:1',
            'bio' => 'required|string|min:100|max:2000',
            'mentoring_philosophy' => 'nullable|string|max:1000',

            'linkedin_profile' => 'nullable|url',
            'twitter_profile' => 'nullable|string',
            'website' => 'nullable|url',

            'max_mentees' => 'required|integer|min:1|max:20',
            'availability' => 'required|string',
            'mentoring_focus' => 'required|string',
            'meeting_preference' => 'required|string',
            'session_duration_minutes' => 'required|integer|min:15|max:240',

            'hourly_rate' => 'nullable|numeric|min:0',
            'offers_free_sessions' => 'boolean',
            'free_sessions_per_month' => 'required_if:offers_free_sessions,true|integer|min:0',

            'languages' => 'nullable|array',
            'education_background' => 'nullable|array',

            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
        ], [
            'areas_of_expertise.required' => 'Please select at least one area of expertise.',
            'industries.required' => 'Please select at least one industry.',
            'bio.min' => 'Bio should be at least 100 characters.',
        ]);

        // Create user
        $user = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'user_type' => 'mentor',
            'is_active' => true,
            'is_verified' => $this->is_verified,
        ]);

        // Assign mentor role
        $user->assignRole('mentor');

        // Create mentor profile
        $mentor = Mentor::create([
            'user_id' => $user->id,
            'job_title' => $this->job_title,
            'company' => $this->company,
            'years_of_experience' => $this->years_of_experience,
            'areas_of_expertise' => $this->areas_of_expertise,
            'industries' => $this->industries,
            'bio' => $this->bio,
            'mentoring_philosophy' => $this->mentoring_philosophy,

            'linkedin_profile' => $this->linkedin_profile,
            'twitter_profile' => $this->twitter_profile,
            'website' => $this->website,

            'max_mentees' => $this->max_mentees,
            'availability' => $this->availability,
            'mentoring_focus' => $this->mentoring_focus,
            'meeting_preference' => $this->meeting_preference,
            'session_duration_minutes' => $this->session_duration_minutes,

            'hourly_rate' => $this->hourly_rate ?: null,
            'offers_free_sessions' => $this->offers_free_sessions,
            'free_sessions_per_month' => $this->offers_free_sessions ? $this->free_sessions_per_month : null,

            'languages' => $this->languages,
            'education_background' => $this->education_background,

            'is_verified' => $this->is_verified,
            'is_featured' => $this->is_featured,
            'verified_at' => $this->is_verified ? now() : null,
            'featured_at' => $this->is_featured ? now() : null,
        ]);

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Mentor created successfully!'
        ]);

        return redirect()->route('admin.mentors.show', $mentor->id);
    }

    public function render()
    {
        return view('livewire.admin.mentors.create');
    }
}
