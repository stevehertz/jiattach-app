<?php

namespace App\Livewire\Student\Profile;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EditProfile extends Component
{
    use WithFileUploads;

     // User Fields (Personal)
    public $first_name, $last_name, $phone, $bio, $gender, $county, $profile_photo;

    // Disability Fields
    public $disability_status;
    public $disability_details;

    // Student Profile Fields (Academic)
    public $student_reg_number, $institution_name, $institution_type, $course_name;
    public $course_level, $year_of_study, $expected_graduation_year, $cgpa;
    public $preferred_location, $preferred_attachment_duration;

    // Arrays for Skills and Interests
    public $skills = [];
    public $interests = [];
    public $newSkill = '';
    public $newInterest = '';

    // Documents
    public $cv, $transcript, $school_letter;

    public $activeTab = 'academic';

    public function mount()
    {
        $user = Auth::user();
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->phone = $user->phone;
        $this->bio = $user->bio;
        $this->gender = $user->gender;
        $this->county = $user->county;

        // Initialize Disability fields
        $this->disability_status = $user->disability_status ?? 'none';
        $this->disability_details = $user->disability_details;

        $profile = $user->studentProfile;
        if ($profile) {
            $this->student_reg_number = $profile->student_reg_number;
            $this->institution_name = $profile->institution_name;
            $this->institution_type = $profile->institution_type;
            $this->course_name = $profile->course_name;
            $this->course_level = $profile->course_level;
            $this->year_of_study = $profile->year_of_study;
            $this->expected_graduation_year = $profile->expected_graduation_year;
            $this->cgpa = $profile->cgpa;
            $this->preferred_location = $profile->preferred_location;
            $this->preferred_attachment_duration = $profile->preferred_attachment_duration;
            $this->skills = $profile->skills ?? [];
            $this->interests = $profile->interests ?? [];
        }
    }

     public function addSkill()
    {
        $this->newSkill = trim($this->newSkill);
        if ($this->newSkill && !in_array($this->newSkill, $this->skills)) {
            $this->skills[] = $this->newSkill;
        }
        $this->newSkill = '';
    }

    public function removeSkill($skill)
    {
        $this->skills = array_diff($this->skills, [$skill]);
    }

    public function addInterest()
    {
        $this->newInterest = trim($this->newInterest);
        if ($this->newInterest && !in_array($this->newInterest, $this->interests)) {
            $this->interests[] = $this->newInterest;
        }
        $this->newInterest = '';
    }

    public function removeInterest($interest)
    {
        $this->interests = array_diff($this->interests, [$interest]);
    }

    public function save()
    {
        $user = User::findOrFail(Auth::user()->id);

        $this->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|max:1024',
            'student_reg_number' => 'required|string',
            'institution_name' => 'required|string',
            'course_name' => 'required|string',
            'cv' => 'nullable|mimes:pdf|max:2048',
            'transcript' => 'nullable|mimes:pdf|max:2048',
            // Disability Validation
            'disability_status' => 'required|in:none,mobility,visual,hearing,cognitive,other,prefer_not_to_say',
            'disability_details' => 'nullable|required_unless:disability_status,none,prefer_not_to_say|string|max:500',
            'cv' => 'nullable|mimes:pdf|max:2048',
            'transcript' => 'nullable|mimes:pdf|max:2048',
            // 2. Add validation for letter
            'school_letter' => 'nullable|mimes:pdf|max:2048',
        ]);

        

        // 1. Update User
        $user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'bio' => $this->bio,
            'gender' => $this->gender,
            'county' => $this->county,
            'disability_status' => $this->disability_status,
            'disability_details' => in_array($this->disability_status, ['none', 'prefer_not_to_say']) ? null : $this->disability_details,
        ]);

        if ($this->profile_photo) {
            // Using Jetstream's profile photo logic if available, otherwise manual
            $path = $this->profile_photo->store('profile-photos', 'public');
            $user->forceFill(['profile_photo_path' => $path])->save();
        }

        // 2. Update/Create Student Profile
        $profile = StudentProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'student_reg_number' => $this->student_reg_number,
                'institution_name' => $this->institution_name,
                'institution_type' => $this->institution_type,
                'course_name' => $this->course_name,
                'course_level' => $this->course_level,
                'year_of_study' => $this->year_of_study,
                'expected_graduation_year' => $this->expected_graduation_year,
                'cgpa' => $this->cgpa,
                'preferred_location' => $this->preferred_location,
                'preferred_attachment_duration' => $this->preferred_attachment_duration,
                'skills' => $this->skills,
                'interests' => $this->interests,
            ]
        );

        if ($this->cv) {
            $profile->cv_url = Storage::url($this->cv->store('docs/cvs', 'public'));
        }

        if ($this->transcript) {
            $profile->transcript_url = Storage::url($this->transcript->store('docs/transcripts', 'public'));
        }

        // Add School Letter Upload
        if ($this->school_letter) {
            $profile->school_letter_url = Storage::url($this->school_letter->store('docs/letters', 'public'));
        }

        $profile->save();

        session()->flash('success', 'Profile updated successfully!');

        return redirect()->route('student.profile.show');
    }

    public function render()
    {
        return view('livewire.student.profile.edit-profile');
    }
}
