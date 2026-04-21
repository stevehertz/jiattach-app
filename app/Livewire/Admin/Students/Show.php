<?php

namespace App\Livewire\Admin\Students;

use Livewire\Component;
use App\Models\User;
use App\Models\Application;
use App\Models\Placement;
use App\Services\StudentMatchingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Show extends Component
{
    public User $student;

    // Match modal properties
    public $showMatchModal = false;
    public $selectedStudentForMatch = null;
    public $studentMatches = [];
    public $matchLoading = false;
    public $selectedMatches = [];

    // Event listeners
    protected $listeners = [
        'refreshStudent' => '$refresh',
        'confirmDeactivation',
        'confirmActivation',
        'confirmVerification',
        'confirmDelete',
        'confirmStatusChange'
    ];

    /**
     * Mount the component
     */
    public function mount(User $student)
    {
        $this->student = $student->load([
            'studentProfile',
            'studentApplications.opportunity.organization',
            'studentApplications.organization',
            'activeMentorships.mentor',
            'mentorshipsAsMentee.mentor',
            'mentor',
            'activityLogs.causer'
        ]);

        // Initialize selectedMatches as an empty array
        $this->selectedMatches = [];
    }


    /**
     * Clear all selected matches
     */
    public function clearSelectedMatches()
    {
        $this->selectedMatches = [];
        $this->dispatch('selection-updated');
    }

    /**
     * Select all matches
     */
    public function selectAllMatches()
    {
        $this->selectedMatches = collect($this->studentMatches)
            ->keys()
            ->map(fn($key) => (string) $key)
            ->values()
            ->toArray();

        $this->dispatch('selection-updated');
    }

    /**
     * Update selected matches when checkbox changes
     */
    public function updatedSelectedMatches($value)
    {
        // Ensure all values are strings
        $this->selectedMatches = array_map('strval', $this->selectedMatches);
        $this->dispatch('selection-updated');
    }

    /**
     * Confirm deactivation with SweetAlert
     */
    public function confirmDeactivation()
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Deactivate Student?',
            'text' => "Are you sure you want to deactivate {$this->student->full_name}? They will not be able to access the platform.",
            'icon' => 'warning',
            'confirmButtonText' => 'Yes, deactivate',
            'cancelButtonText' => 'Cancel',
            'method' => 'toggleActive'
        ]);
    }

    /**
     * Confirm activation with SweetAlert
     */
    public function confirmActivation()
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Activate Student?',
            'text' => "Are you sure you want to activate {$this->student->full_name}? They will regain access to the platform.",
            'icon' => 'info',
            'confirmButtonText' => 'Yes, activate',
            'cancelButtonText' => 'Cancel',
            'method' => 'toggleActive'
        ]);
    }

    /**
     * Confirm verification with SweetAlert
     */
    public function confirmVerification()
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Verify Student?',
            'text' => "Are you sure you want to verify {$this->student->full_name}'s account?",
            'icon' => 'question',
            'confirmButtonText' => 'Yes, verify',
            'cancelButtonText' => 'Cancel',
            'method' => 'verifyUser'
        ]);
    }

    /**
     * Confirm status change with SweetAlert
     */
    public function confirmStatusChange($status)
    {
        $statusLabels = [
            'seeking' => 'Seeking Attachment',
            'applied' => 'Applied',
            'interviewing' => 'Interviewing',
            'placed' => 'Placed',
            'completed' => 'Completed'
        ];

        $this->dispatch('swal:confirm', [
            'title' => 'Change Status?',
            'text' => "Are you sure you want to change status to " . $statusLabels[$status] . "?",
            'icon' => 'question',
            'confirmButtonText' => 'Yes, change',
            'cancelButtonText' => 'Cancel',
            'method' => 'updateAttachmentStatus',
            'params' => [$status]
        ]);
    }

    /**
     * Confirm delete with SweetAlert
     */
    public function confirmDelete()
    {
        // Check if student has active placements
        if ($this->hasActivePlacement) {
            $this->dispatch('swal:error', [
                'title' => 'Cannot Delete',
                'text' => 'This student has an active placement and cannot be deleted.',
            ]);
            return;
        }

        $this->dispatch('swal:confirm', [
            'title' => 'Delete Student?',
            'text' => "Are you sure you want to delete {$this->student->full_name}? This action cannot be undone and will permanently remove all student data.",
            'icon' => 'error',
            'confirmButtonText' => 'Yes, delete permanently',
            'cancelButtonText' => 'Cancel',
            'method' => 'deleteStudent',
            'danger' => true
        ]);
    }


    /**
     * Toggle student active status
     */
    public function toggleActive()
    {
        $this->student->update([
            'is_active' => !$this->student->is_active
        ]);

        $status = $this->student->is_active ? 'activated' : 'deactivated';

        // Log activity
        activity_log(
            "Student {$status} by admin",
            'status_changed',
            [
                'student_id' => $this->student->id,
                'student_name' => $this->student->full_name,
                'new_status' => $this->student->is_active ? 'active' : 'inactive'
            ],
            'student'
        );

        $this->dispatch('toastr:success', message: "Student {$status} successfully!");

        $this->dispatch('refreshStudent');
    }

    /**
     * Verify student account
     */
    public function verifyUser()
    {
        if ($this->student->is_verified) {
            $this->dispatch('swal:warning', [
                'title' => 'Already Verified',
                'text' => 'Student is already verified!'
            ]);
            return;
        }

        $this->student->update([
            'is_verified' => true,
            'verified_at' => now()
        ]);

        // Log activity
        activity_log(
            "Student verified by admin",
            'verified',
            [
                'student_id' => $this->student->id,
                'student_name' => $this->student->full_name
            ],
            'student'
        );

        $this->dispatch('swal:success', [
            'title' => 'Success!',
            'text' => 'Student verified successfully!'
        ]);

        $this->dispatch('refreshStudent');
    }

    /**
     * Update attachment status
     */
    public function updateAttachmentStatus(string $status)
    {
        if (!$this->student->studentProfile) {
            $this->dispatch('toastr:error', message: 'Student profile not found!');
            return;
        }

        $oldStatus = $this->student->studentProfile->attachment_status;

        $this->student->studentProfile->update([
            'attachment_status' => $status
        ]);

        // Log activity
        activity_log(
            "Attachment status changed from {$oldStatus} to {$status}",
            'status_changed',
            [
                'student_id' => $this->student->id,
                'student_name' => $this->student->full_name,
                'old_status' => $oldStatus,
                'new_status' => $status
            ],
            'student_profile'
        );

        $this->dispatch('toastr:success', message: "Attachment status updated to " . ucfirst($status) . "!");

        $this->dispatch('refreshStudent');
    }

    /**
     * Delete student
     */
    public function deleteStudent()
    {
        // Check if student has active placements
        if ($this->student->hasActivePlacement()) {
            $this->dispatch('toastr:error', message: 'Cannot delete student with active placement!');
            return;
        }

        $studentName = $this->student->full_name;

        DB::beginTransaction();

        try {
            // Log before deletion
            activity_log(
                "Student deleted: {$studentName}",
                'deleted',
                [
                    'student_id' => $this->student->id,
                    'student_email' => $this->student->email,
                    'deleted_by' => auth()->id()
                ],
                'student'
            );

            // Delete the student
            $this->student->delete();

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Deleted!',
                'text' => 'Student deleted successfully!',
                'redirect' => route('admin.students.index')
            ]);

            // Redirect after a short delay
            $this->dispatch('redirect-after-delete', [
                'url' => route('admin.students.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete student: ' . $e->getMessage());

            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Failed to delete student: ' . $e->getMessage()
            ]);
        }
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
        if (!$this->selectedStudentForMatch->studentProfile) {
            $this->dispatch('toastr:warning', message: 'This student has no profile. Please complete profile first.');
            return;
        }

        if ($this->selectedStudentForMatch->studentProfile->attachment_status !== 'seeking') {
            $this->dispatch('toastr:warning', message: 'This student is not seeking attachment. Only students with "Seeking" status can be matched.');
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
            $matchingService = app(StudentMatchingService::class);
            $matches = $matchingService->findMatchesForStudent($this->selectedStudentForMatch, 10);

            $this->studentMatches = $matches;

            // Auto-select high-quality matches (optional)
            foreach ($matches as $index => $match) {
                if ($match['score'] >= 85) {
                    $this->selectedMatches[] = (string) $index;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error finding matches: ' . $e->getMessage());

            $this->dispatch('toastr:error', message: 'Error finding matches: ' . $e->getMessage());
        }

        $this->matchLoading = false;
    }

    /**
     * Refresh matches
     */
    public function refreshMatches()
    {
        $this->findMatches();

        $this->dispatch('toastr:success', message: 'Matches refreshed successfully!');
    }

    /**
     * Save selected matches as applications
     */
    public function saveMatches()
    {
        if (empty($this->selectedMatches)) {
            $this->dispatch('toastr:warning', message: 'Please select at least one match to save.');
            return;
        }

        try {
            $matchingService = app(StudentMatchingService::class);

            // Filter selected matches
            $selectedMatchesData = array_filter($this->studentMatches, function ($match, $key) {
                return in_array((string) $key, $this->selectedMatches);
            }, ARRAY_FILTER_USE_BOTH);

            $saved = $matchingService->saveMatchesForStudent(
                $this->selectedStudentForMatch,
                $selectedMatchesData
            );

            // Close modal
            $this->showMatchModal = false;
            $this->selectedStudentForMatch = null;
            $this->studentMatches = [];
            $this->selectedMatches = [];

            // Refresh the student data to show new applications
            $this->student->load('studentApplications.opportunity.organization');

            $this->dispatch('toastr:success', message: count($saved) . ' matches saved successfully!');

            $this->dispatch('refreshStudent');
        } catch (\Exception $e) {
            Log::error('Error saving matches: ' . $e->getMessage());

            $this->dispatch('toastr:error', message: 'Error saving matches: ' . $e->getMessage());
        }
    }

    /**
     * Toggle a single match
     */
    public function toggleMatch($index)
    {
        if (in_array((string) $index, $this->selectedMatches)) {
            $this->selectedMatches = array_values(array_diff($this->selectedMatches, [(string) $index]));
        } else {
            $this->selectedMatches[] = (string) $index;
        }

        $this->dispatch('selection-updated');
    }






    /**
     * Create placement from an application
     */
    public function createPlacement($applicationId)
    {
        $application = Application::with(['student', 'opportunity', 'organization'])
            ->findOrFail($applicationId);

        // Check if application is offered
        if ($application->status !== 'offered') {
            $this->dispatch('toastr:warning', message: 'Only offered applications can be converted to placements.');
            return;
        }

        // Check if placement already exists
        if ($application->placement) {
            $this->dispatch('toastr:warning', message: 'Placement already exists for this application.');
            return;
        }

        DB::beginTransaction();

        try {
            // Create placement
            $placement = Placement::create([
                'application_id' => $application->id,
                'student_id' => $application->student_id,
                'admin_id' => auth()->id(),
                'organization_id' => $application->organization_id,
                'attachment_opportunity_id' => $application->attachment_opportunity_id,
                'status' => 'pending',
                'start_date' => $application->opportunity->start_date ?? now()->addWeeks(2),
                'end_date' => $application->opportunity->end_date ?? now()->addMonths(3),
                'supervisor_name' => $application->opportunity->supervisor_name ?? null,
                'supervisor_contact' => $application->opportunity->supervisor_contact ?? null,
                'stipend' => $application->opportunity->stipend ?? null,
                'admin_notified_at' => now(),
            ]);

            // Update application status
            $application->update(['status' => 'accepted']);

            // Update student profile status
            if ($application->student->studentProfile) {
                $application->student->studentProfile->update([
                    'attachment_status' => 'placed',
                    'attachment_start_date' => $placement->start_date,
                    'attachment_end_date' => $placement->end_date,
                ]);
            }

            // Log activity
            activity_log(
                "Placement created for student from application #{$applicationId}",
                'placement_created',
                [
                    'placement_id' => $placement->id,
                    'application_id' => $applicationId,
                    'student_id' => $application->student_id,
                    'opportunity_id' => $application->attachment_opportunity_id,
                    'organization_id' => $application->organization_id,
                ],
                'placement'
            );

            DB::commit();

            // Refresh student data
            $this->student->load('studentApplications', 'placements');

            $this->dispatch('toastr:success', message: 'Placement created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create placement: ' . $e->getMessage());

            $this->dispatch('toastr:error', message: 'Failed to create placement: ' . $e->getMessage());
        }
    }

    /**
     * Get activity logs for the student
     */
    public function getActivityLogsProperty()
    {
        return $this->student->activityLogs()
            ->with('causer')
            ->latest()
            ->take(20)
            ->get();
    }

    /**
     * Check if student has active placement
     */
    public function getHasActivePlacementProperty()
    {
        return $this->student->placements()
            ->where('status', 'placed')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->exists();
    }

    /**
     * Get latest placement
     */
    public function getLatestPlacementProperty()
    {
        return $this->student->placements()
            ->with(['organization', 'opportunity'])
            ->latest()
            ->first();
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.admin.students.show', [
            'activityLogs' => $this->activityLogs,
            'hasActivePlacement' => $this->hasActivePlacement,
            'latestPlacement' => $this->latestPlacement,
        ]);
    }
}
