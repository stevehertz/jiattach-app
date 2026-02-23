<?php

namespace App\Livewire\Admin\Students;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StudentProfile;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $institutionFilter = '';
    public $courseFilter = '';
    public $yearFilter = '';
    public $attachmentStatusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 20;
    public $selectedStudents = [];
    public $selectAll = false;
    public $showFilters = false;
    public $showBulkActions = false;
    public $statsOnly = false; // For use by child components

    // Add these properties
    public $showMatchModal = false;
    public $selectedStudentForMatch = null;
    public $studentMatches = [];
    public $matchLoading = false;
    public $selectedMatches = [];

    protected $listeners = ['refreshStudents' => '$refresh'];

    public function mount($statsOnly = false)
    {
        $this->statsOnly = $statsOnly;
    }

    /**
     * Open match modal for a student
     */
    public function matchStudent($studentId)
    {
        $this->selectedStudentForMatch = User::with('studentProfile')
            ->role('student')
            ->findOrFail($studentId);

        // Check if student is eligible for matching
        if ($this->selectedStudentForMatch->studentProfile->attachment_status !== 'seeking') {
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => 'This student is not seeking attachment. Only students with "Seeking" status can be matched.'
            ]);
            return;
        }

        $this->showMatchModal = true;
        $this->findMatches();
    }

    /**
     * Find matches for selected student
     */
    public function findMatches()
    {
        $this->matchLoading = true;
        $this->studentMatches = [];
        $this->selectedMatches = [];

        try {
            $matchingService = app(\App\Services\StudentMatchingService::class);
            $matches = $matchingService->findMatchesForStudent($this->selectedStudentForMatch, 10);

            $this->studentMatches = $matches;
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Error finding matches: ' . $e->getMessage()
            ]);
        }

        $this->matchLoading = false;
    }

    /**
     * Save selected matches as applications
     */
    public function saveMatches()
    {
        if (empty($this->selectedMatches)) {
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => 'Please select at least one match to save.'
            ]);
            return;
        }

        try {
            $matchingService = app(\App\Services\StudentMatchingService::class);

            // Filter selected matches
            $selectedMatchesData = array_filter($this->studentMatches, function ($match, $key) {
                return in_array($key, $this->selectedMatches);
            }, ARRAY_FILTER_USE_BOTH);

            $saved = $matchingService->saveMatchesForStudent(
                $this->selectedStudentForMatch,
                $selectedMatchesData
            );

            $this->showMatchModal = false;
            $this->selectedStudentForMatch = null;
            $this->studentMatches = [];
            $this->selectedMatches = [];

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => count($saved) . ' matches saved successfully!'
            ]);

            $this->dispatch('refreshStudents');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Error saving matches: ' . $e->getMessage()
            ]);
        }
    }


    /**
     * Run matching for all seeking students (bulk action)
     */
    public function matchAllSeekingStudents()
    {
        $this->dispatch('confirm-action', [
            'title' => 'Match All Seeking Students',
            'message' => 'This will run the matching algorithm for all students with "Seeking" status. This may take a while. Continue?',
            'method' => 'processMatchAllSeekingStudents'
        ]);
    }

    public function processMatchAllSeekingStudents()
    {
        try {
            $matchingService = app(\App\Services\StudentMatchingService::class);
            $results = $matchingService->runMatchingForAllSeekingStudents();

            $message = "Processed {$results['total_processed']} students. Created {$results['matches_created']} matches.";

            if (!empty($results['errors'])) {
                $message .= " Errors: " . implode(', ', $results['errors']);
            }

            $this->dispatch('show-toast', [
                'type' => $results['errors'] ? 'warning' : 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Error in bulk matching: ' . $e->getMessage()
            ]);
        }
    }


    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedStudents = $this->getStudentsQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedStudents = [];
        }
    }

    public function updatedSelectedStudents()
    {
        $this->selectAll = false;
        $this->showBulkActions = count($this->selectedStudents) > 0;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function getStudentsQuery()
    {
        return User::role('student')
            ->with(['studentProfile'])
            ->whereHas('studentProfile') // Only users with student profiles
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhereHas('studentProfile', function ($q) use ($search) {
                            $q->where('student_reg_number', 'like', '%' . $search . '%')
                                ->orWhere('institution_name', 'like', '%' . $search . '%')
                                ->orWhere('course_name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($this->institutionFilter, function ($query, $institution) {
                $query->whereHas('studentProfile', function ($q) use ($institution) {
                    $q->where('institution_name', 'like', '%' . $institution . '%');
                });
            })
            ->when($this->courseFilter, function ($query, $course) {
                $query->whereHas('studentProfile', function ($q) use ($course) {
                    $q->where('course_name', 'like', '%' . $course . '%');
                });
            })
            ->when($this->yearFilter, function ($query, $year) {
                $query->whereHas('studentProfile', function ($q) use ($year) {
                    $q->where('year_of_study', $year);
                });
            })
            ->when($this->attachmentStatusFilter, function ($query, $status) {
                $query->whereHas('studentProfile', function ($q) use ($status) {
                    $q->where('attachment_status', $status);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function getStudentsProperty()
    {
        return $this->getStudentsQuery()->paginate($this->perPage);
    }

    public function getStatsProperty()
    {
        $totalStudents = User::role('student')->count();
        $activeStudents = User::role('student')->where('is_active', true)->count();

        $seekingAttachment = StudentProfile::where('attachment_status', 'seeking')->count();
        $onAttachment = StudentProfile::where('attachment_status', 'placed')
            ->where('attachment_start_date', '<=', now())
            ->where('attachment_end_date', '>=', now())
            ->count();

        $verifiedStudents = User::role('student')->where('is_verified', true)->count();
        $todayRegistrations = User::role('student')->whereDate('created_at', today())->count();

        return [
            'total' => $totalStudents,
            'active' => $activeStudents,
            'seeking' => $seekingAttachment,
            'on_attachment' => $onAttachment,
            'verified' => $verifiedStudents,
            'today' => $todayRegistrations,
        ];
    }

    public function getInstitutionsProperty()
    {
        return StudentProfile::distinct('institution_name')
            ->whereNotNull('institution_name')
            ->orderBy('institution_name')
            ->pluck('institution_name', 'institution_name');
    }

    public function getCoursesProperty()
    {
        return StudentProfile::distinct('course_name')
            ->whereNotNull('course_name')
            ->orderBy('course_name')
            ->pluck('course_name', 'course_name');
    }

    public function getYearOptionsProperty()
    {
        return [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
            6 => 'Sixth Year',
        ];
    }

    public function getAttachmentStatusOptionsProperty()
    {
        return [
            'seeking' => 'Seeking Attachment',
            'applied' => 'Applied',
            'interviewing' => 'Interviewing',
            'placed' => 'Placed',
            'completed' => 'Completed',
        ];
    }

    public function toggleStudentActive($studentId)
    {
        $user = User::findOrFail($studentId);
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => "Student {$status} successfully!"
        ]);
    }

    public function updateAttachmentStatus($studentId, $status)
    {
        $student = User::findOrFail($studentId);

        if ($student->studentProfile) {
            $student->studentProfile->update(['attachment_status' => $status]);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => "Attachment status updated to " . ucfirst($status) . "!"
            ]);

            $this->dispatch('refreshStudents');
        }
    }

    public function viewStudent($studentId)
    {
        return redirect()->route('admin.students.show', $studentId);
    }

    public function exportStudents($format = 'csv')
    {
        $this->dispatch('show-toast', [
            'type' => 'info',
            'message' => 'Export feature coming soon!'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.students.index', [
            'students' => $this->students,
            'stats' => $this->stats,
            'institutions' => $this->institutions,
            'courses' => $this->courses,
            'yearOptions' => $this->yearOptions,
            'attachmentStatusOptions' => $this->attachmentStatusOptions,
        ]);
    }
}
