<?php

namespace App\Livewire\Admin\Mentorship;

use App\Models\Mentor;
use App\Models\Mentorship;
use App\Models\StudentProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
     // Form fields
    public $title = '';
    public $description = '';
    public $mentor_id = '';
    public $mentee_id = '';
    public $goals = [];
    public $topics = [];
    public $meeting_preference = 'video';
    public $duration_weeks = 12;
    public $meetings_per_month = 4;
    public $meeting_duration_minutes = 60;
    public $start_date = '';
    public $end_date = '';
    public $hourly_rate = 0;
    public $is_paid = false;
    public $expectations = '';
    public $mentor_expectations = '';
    public $mentee_expectations = '';
    public $is_confidential = false;
    public $experience_level = 'mid';
    public $notes = '';
    
    // Filter options
    public $mentorSearch = '';
    public $menteeSearch = '';
    
    // Available options
    public $meetingPreferences = [
        'video' => 'Video Call',
        'phone' => 'Phone Call',
        'in_person' => 'In Person',
        'hybrid' => 'Hybrid'
    ];
    
    public $experienceLevels = [
        'entry' => 'Entry Level',
        'junior' => 'Junior',
        'mid' => 'Mid Level',
        'senior' => 'Senior',
        'executive' => 'Executive'
    ];
    
    public $durationOptions = [
        4 => '1 Month (4 weeks)',
        8 => '2 Months (8 weeks)',
        12 => '3 Months (12 weeks)',
        16 => '4 Months (16 weeks)',
        20 => '5 Months (20 weeks)',
        24 => '6 Months (24 weeks)',
        36 => '9 Months (36 weeks)',
        48 => '1 Year (48 weeks)'
    ];
    
    public $meetingsPerMonthOptions = [
        1 => 'Once per month',
        2 => 'Twice per month',
        3 => 'Three times per month',
        4 => 'Weekly',
        8 => 'Twice weekly'
    ];
    
    public $durationMinutesOptions = [
        30 => '30 minutes',
        45 => '45 minutes',
        60 => '1 hour',
        90 => '1.5 hours',
        120 => '2 hours'
    ];
    
    // Available topics (you can customize this)
    public $availableTopics = [
        'Career Development',
        'Technical Skills',
        'Leadership',
        'Communication',
        'Networking',
        'Interview Preparation',
        'Resume Writing',
        'Job Search Strategy',
        'Industry Insights',
        'Project Management',
        'Entrepreneurship',
        'Work-Life Balance',
        'Professional Branding',
        'Public Speaking',
        'Team Collaboration'
    ];
    
    public function mount()
    {
        // Set default dates
        $this->start_date = Carbon::now()->addWeek()->format('Y-m-d');
        $this->calculateEndDate();
    }
    
    public function calculateEndDate()
    {
        if ($this->start_date && $this->duration_weeks) {
            $startDate = Carbon::parse($this->start_date);
            $this->end_date = $startDate->addWeeks($this->duration_weeks)->format('Y-m-d');
        }
    }
    
    public function updatedDurationWeeks()
    {
        $this->calculateEndDate();
    }
    
    public function updatedStartDate()
    {
        $this->calculateEndDate();
    }
    
    public function updatedIsPaid($value)
    {
        if (!$value) {
            $this->hourly_rate = 0;
        }
    }
    
    public function addGoal()
    {
        $this->goals[] = '';
    }
    
    public function removeGoal($index)
    {
        unset($this->goals[$index]);
        $this->goals = array_values($this->goals);
    }
    
    public function toggleTopic($topic)
    {
        if (in_array($topic, $this->topics)) {
            $this->topics = array_diff($this->topics, [$topic]);
        } else {
            $this->topics[] = $topic;
        }
    }
    
    public function getAvailableMentorsProperty()
    {
        $query = Mentor::query()
            ->with(['user'])
            ->where('is_verified', true)
            ->where(function($q) {
                $q->where('availability', 'available')
                  ->orWhere('availability', 'limited');
            })
            ->whereRaw('current_mentees < max_mentees');
            
        if ($this->mentorSearch) {
            $query->whereHas('user', function($q) {
                $q->where('first_name', 'like', '%' . $this->mentorSearch . '%')
                  ->orWhere('last_name', 'like', '%' . $this->mentorSearch . '%')
                  ->orWhere('email', 'like', '%' . $this->mentorSearch . '%');
            });
        }
        
        return $query->orderBy('years_of_experience', 'desc')
                    ->limit(20)
                    ->get();
    }
    
    public function getAvailableMenteesProperty()
    {
        $query = User::role('student')
            ->with(['studentProfile'])
            ->whereHas('studentProfile', function($q) {
                $q->whereIn('attachment_status', ['seeking', 'applied', 'completed']);
            });
            
        if ($this->menteeSearch) {
            $query->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->menteeSearch . '%')
                  ->orWhere('last_name', 'like', '%' . $this->menteeSearch . '%')
                  ->orWhere('email', 'like', '%' . $this->menteeSearch . '%')
                  ->orWhereHas('studentProfile', function($q2) {
                      $q2->where('institution_name', 'like', '%' . $this->menteeSearch . '%')
                         ->orWhere('course_name', 'like', '%' . $this->menteeSearch . '%');
                  });
            });
        }
        
        return $query->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get();
    }
    
    public function getSelectedMentorProperty()
    {
        if ($this->mentor_id) {
            return Mentor::with('user')->find($this->mentor_id);
        }
        return null;
    }
    
    public function getSelectedMenteeProperty()
    {
        if ($this->mentee_id) {
            return User::with('studentProfile')->find($this->mentee_id);
        }
        return null;
    }
    
    public function getEstimatedCostProperty()
    {
        if (!$this->hourly_rate || $this->hourly_rate <= 0) {
            return 0;
        }
        
        $totalHours = ($this->meeting_duration_minutes / 60) * $this->meetings_per_month * ($this->duration_weeks / 4);
        return $totalHours * $this->hourly_rate;
    }
    
    public function getTotalMeetingsProperty()
    {
        return $this->meetings_per_month * ($this->duration_weeks / 4);
    }
    
    public function save()
    {
        // Validate the form
        $this->validate([
            'title' => 'required|string|min:5|max:200',
            'description' => 'required|string|min:20|max:2000',
            'mentor_id' => 'required|exists:mentors,id',
            'mentee_id' => 'required|exists:users,id',
            'goals' => 'array',
            'goals.*' => 'string|min:5|max:500',
            'topics' => 'array',
            'meeting_preference' => 'required|in:video,phone,in_person,hybrid',
            'duration_weeks' => 'required|integer|min:1|max:104',
            'meetings_per_month' => 'required|integer|min:1|max:12',
            'meeting_duration_minutes' => 'required|integer|min:15|max:240',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'hourly_rate' => 'nullable|numeric|min:0|max:10000',
            'is_paid' => 'boolean',
            'expectations' => 'nullable|string|max:1000',
            'mentor_expectations' => 'nullable|string|max:1000',
            'mentee_expectations' => 'nullable|string|max:1000',
            'is_confidential' => 'boolean',
            'experience_level' => 'required|in:entry,junior,mid,senior,executive',
            'notes' => 'nullable|string|max:2000',
        ], [
            'mentor_id.required' => 'Please select a mentor.',
            'mentee_id.required' => 'Please select a mentee.',
            'start_date.after_or_equal' => 'Start date must be today or in the future.',
            'end_date.after' => 'End date must be after the start date.',
        ]);
        
        // Check if mentor is available
        $mentor = Mentor::find($this->mentor_id);
        if ($mentor->current_mentees >= $mentor->max_mentees) {
            $this->addError('mentor_id', 'This mentor has reached their maximum mentee capacity.');
            return;
        }
        
        // Check if mentee is already in an active mentorship
        $activeMentorship = Mentorship::where('mentee_id', $this->mentee_id)
            ->whereIn('status', ['active', 'pending_approval', 'requested'])
            ->exists();
            
        if ($activeMentorship) {
            $this->addError('mentee_id', 'This mentee is already in an active mentorship.');
            return;
        }
        
        try {
            DB::beginTransaction();
            
            // Create the mentorship
            $mentorship = Mentorship::create([
                'title' => $this->title,
                'description' => $this->description,
                'mentor_id' => $this->mentor_id,
                'mentee_id' => $this->mentee_id,
                'goals' => array_filter($this->goals),
                'topics' => $this->topics,
                'status' => 'pending_approval',
                'meeting_preference' => $this->meeting_preference,
                'duration_weeks' => $this->duration_weeks,
                'meetings_per_month' => $this->meetings_per_month,
                'meeting_duration_minutes' => $this->meeting_duration_minutes,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'hourly_rate' => $this->is_paid ? $this->hourly_rate : null,
                'is_paid' => $this->is_paid,
                'payment_status' => $this->is_paid ? 'pending' : null,
                'expectations' => $this->expectations,
                'mentor_expectations' => $this->mentor_expectations,
                'mentee_expectations' => $this->mentee_expectations,
                'is_confidential' => $this->is_confidential,
                'experience_level' => $this->experience_level,
                'notes' => $this->notes,
                'requested_at' => now(),
                'approved_at' => now(), // Auto-approve since admin is creating it
            ]);
            
            // Update mentor's current mentees count
            $mentor->increment('current_mentees');
            
            // Update mentor availability if needed
            if ($mentor->current_mentees >= $mentor->max_mentees) {
                $mentor->availability = 'fully_booked';
            } elseif ($mentor->current_mentees >= $mentor->max_mentees * 0.8) {
                $mentor->availability = 'limited';
            }
            $mentor->save();
            
            // Update mentee's attachment status
            $menteeProfile = StudentProfile::where('user_id', $this->mentee_id)->first();
            if ($menteeProfile && $menteeProfile->attachment_status === 'seeking') {
                $menteeProfile->update(['attachment_status' => 'placed']);
            }
            
            DB::commit();
            
            // Send notifications (you can implement this later)
            // $this->sendNotifications($mentorship);
            
            // Show success message
            session()->flash('success', 'Mentorship created successfully!');
            
            // Redirect to the mentorship details page
            return redirect()->route('admin.mentorships.show', $mentorship);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('form', 'An error occurred while creating the mentorship: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.admin.mentorship.create', [
            'availableMentors' => $this->availableMentors,
            'availableMentees' => $this->availableMentees,
            'selectedMentor' => $this->selectedMentor,
            'selectedMentee' => $this->selectedMentee,
            'estimatedCost' => $this->estimatedCost,
            'totalMeetings' => $this->totalMeetings,
        ]);
    }
}
