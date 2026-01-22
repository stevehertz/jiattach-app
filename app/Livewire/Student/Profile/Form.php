<?php

namespace App\Livewire\Student\Profile;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Form extends Component
{

    use WithFileUploads;

    public $profile;
    public $student_reg_number;
    public $institution_name;
    public $institution_type = 'university';
    public $course_name;
    public $course_level = 'bachelor';
    public $year_of_study = 1;
    public $expected_graduation_year;
    public $cgpa;
    public $skills = [];
    public $interests = [];
    public $preferred_location;
    public $preferred_attachment_duration = 3;
    public $cv_file;
    public $transcript_file;
    public $cv_url;
    public $transcript_url;
    public $skillInput = '';
    public $interestInput = '';

    public $institutionTypes = [
        'university' => 'University',
        'college' => 'College',
        'polytechnic' => 'Polytechnic',
        'technical' => 'Technical Institute',
    ];

    public $courseLevels = [
        'certificate' => 'Certificate',
        'diploma' => 'Diploma',
        'bachelor' => 'Bachelor\'s Degree',
        'masters' => 'Master\'s Degree',
        'phd' => 'PhD',
    ];

    protected $listeners = ['documentUploaded' => 'refreshProfile'];

    public function mount()
    {
        $user = User::findOrFail(Auth::user()->id);

        $this->profile = $user->studentProfile;

        if ($this->profile) {
            $this->student_reg_number = $this->profile->student_reg_number;
            $this->institution_name = $this->profile->institution_name;
            $this->institution_type = $this->profile->institution_type;
            $this->course_name = $this->profile->course_name;
            $this->course_level = $this->profile->course_level;
            $this->year_of_study = $this->profile->year_of_study;
            $this->expected_graduation_year = $this->profile->expected_graduation_year;
            $this->cgpa = $this->profile->cgpa;
            $this->skills = $this->profile->skills ?? [];
            $this->interests = $this->profile->interests ?? [];
            $this->preferred_location = $this->profile->preferred_location;
            $this->preferred_attachment_duration = $this->profile->preferred_attachment_duration;
            $this->cv_url = $this->profile->cv_url;
            $this->transcript_url = $this->profile->transcript_url;
        }

        // Set default graduation year if not set
        if (!$this->expected_graduation_year) {
            $this->expected_graduation_year = date('Y') + 4;
        }
    }

    protected function rules()
    {
        return [
            'student_reg_number' => 'required|string|max:50',
            'institution_name' => 'required|string|max:255',
            'institution_type' => 'required|in:university,college,polytechnic,technical',
            'course_name' => 'required|string|max:255',
            'course_level' => 'required|in:certificate,diploma,bachelor,masters,phd',
            'year_of_study' => 'required|integer|min:1|max:6',
            'expected_graduation_year' => 'required|integer|min:' . date('Y'),
            'cgpa' => 'nullable|numeric|min:0|max:4.0',
            'skills' => 'required|array|min:1',
            'preferred_location' => 'required|string|max:255',
            'preferred_attachment_duration' => 'required|integer|min:1|max:12',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'transcript_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        // Auto-save on field change (optional)
        if ($this->profile && !in_array($propertyName, ['cv_file', 'transcript_file', 'skillInput', 'interestInput'])) {
            $this->save();
        }
    }

    public function addSkill()
    {
        if (!empty(trim($this->skillInput))) {
            $skill = trim($this->skillInput);
            if (!in_array($skill, $this->skills)) {
                $this->skills[] = $skill;
                $this->skillInput = '';
                $this->dispatchBrowserEvent('skill-added');
            }
        }
    }

    public function removeSkill($index)
    {
        unset($this->skills[$index]);
        $this->skills = array_values($this->skills);
    }

    public function addInterest()
    {
        if (!empty(trim($this->interestInput))) {
            $interest = trim($this->interestInput);
            if (!in_array($interest, $this->interests)) {
                $this->interests[] = $interest;
                $this->interestInput = '';
                $this->dispatchBrowserEvent('interest-added');
            }
        }
    }

    public function removeInterest($index)
    {
        unset($this->interests[$index]);
        $this->interests = array_values($this->interests);
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        // Handle file uploads
        if ($this->cv_file) {
            $cvPath = $this->cv_file->store('documents/student/' . $user->id, 'public');
            $cvUrl = Storage::url($cvPath);
        }

        if ($this->transcript_file) {
            $transcriptPath = $this->transcript_file->store('documents/student/' . $user->id, 'public');
            $transcriptUrl = Storage::url($transcriptPath);
        }

        // Update or create profile
        $profileData = [
            'student_reg_number' => $this->student_reg_number,
            'institution_name' => $this->institution_name,
            'institution_type' => $this->institution_type,
            'course_name' => $this->course_name,
            'course_level' => $this->course_level,
            'year_of_study' => $this->year_of_study,
            'expected_graduation_year' => $this->expected_graduation_year,
            'cgpa' => $this->cgpa,
            'skills' => $this->skills,
            'interests' => $this->interests,
            'preferred_location' => $this->preferred_location,
            'preferred_attachment_duration' => $this->preferred_attachment_duration,
        ];

        if (isset($cvUrl)) {
            $profileData['cv_url'] = $cvUrl;
        }

        if (isset($transcriptUrl)) {
            $profileData['transcript_url'] = $transcriptUrl;
        }

        if ($this->profile) {
            $this->profile->update($profileData);
            $message = 'Profile updated successfully!';
        } else {
            $profileData['user_id'] = $user->id;
            $this->profile = StudentProfile::create($profileData);
            $message = 'Profile created successfully!';
        }

        // Clear file inputs
        $this->cv_file = null;
        $this->transcript_file = null;

        // Refresh profile data
        $this->profile->refresh();
        $this->cv_url = $this->profile->cv_url;
        $this->transcript_url = $this->profile->transcript_url;

        // Dispatch event to parent
        $this->dispatchBrowserEvent('profile-saved', [
            'message' => $message,
            'completeness' => $this->profile->profile_completeness
        ]);

        // Emit event to parent component/controller
        $this->emit('profileUpdated', $this->profile);
    }

    public function deleteDocument($type)
    {
        if (!$this->profile) return;

        $field = $type == 'cv' ? 'cv_url' : 'transcript_url';
        $currentFile = $this->profile->$field;

        if ($currentFile) {
            // Delete file from storage
            $filePath = str_replace('/storage/', '', $currentFile);
            Storage::disk('public')->delete($filePath);

            // Clear the field
            $this->profile->$field = null;
            $this->profile->save();

            // Update local property
            $this->{$type . '_url'} = null;

            // Refresh
            $this->profile->refresh();

            $this->dispatchBrowserEvent('document-deleted', [
                'type' => $type,
                'message' => ucfirst($type) . ' deleted successfully.'
            ]);

            $this->emit('documentDeleted', $type);
        }
    }

    public function refreshProfile()
    {
        $this->profile->refresh();
        $this->cv_url = $this->profile->cv_url;
        $this->transcript_url = $this->profile->transcript_url;
    }


    public function render()
    {
        return view('livewire.student.profile.form', [
            'profileCompleteness' => $this->profile ? $this->profile->profile_completeness : 0,
            'progressBreakdown' => $this->profile ? $this->profile->getProfileProgressBreakdown() : [],
            'missingFields' => $this->profile ? $this->profile->getMissingFields() : [],
            'isPlacementReady' => $this->profile ? $this->profile->isPlacementReady() : false,
        ]);
    }
}
