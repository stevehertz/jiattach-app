<?php

namespace App\Livewire\Admin\Placements;

use App\Models\Application;
use App\Models\Placement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Create extends Component
{
    use WithPagination;

    // Step management
    public $currentStep = 1;
    public $totalSteps = 4;

    // Step 1: Select Student
    public $selectedStudentId = null;
    public $selectedStudent = null;
    public $studentSearch = '';
    public $studentApplications = [];

    // Step 2: Select Application/Match
    public $selectedApplicationId = null;
    public $selectedApplication = null;

      // Step 3: Placement Details
    public $placementData = [
        'start_date' => '',
        'end_date' => '',
        'department' => '',
        'supervisor_name' => '',
        'supervisor_contact' => '',
        'notes' => '',
        'stipend' => '',
    ];

    // Step 4: Review & Confirm
    public $placementPreview = [];

    // Validation rules for each step
    protected $validationRules = [
        1 => ['selectedStudentId' => 'required|exists:users,id'],
        2 => ['selectedApplicationId' => 'required|exists:applications,id'],
        3 => [
            'placementData.start_date' => 'required|date|after_or_equal:today',
            'placementData.end_date' => 'required|date|after:placementData.start_date',
            'placementData.department' => 'required|string|max:255',
            'placementData.supervisor_name' => 'required|string|max:255',
            'placementData.supervisor_contact' => 'nullable|string|max:255',
            'placementData.notes' => 'nullable|string',
            'placementData.stipend' => 'nullable|numeric|min:0',
        ],
    ];

     public function mount($studentId = null)
    {
        if ($studentId) {
            $this->selectStudent($studentId);
        }
    }

    public function getStudentsProperty()
    {
        return User::role('student')
            ->whereHas('studentProfile', function ($query) {
                $query->whereIn('attachment_status', ['applied', 'interviewing', 'seeking']);
            })
            ->with('studentProfile')
            ->when($this->studentSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->studentSearch . '%')
                      ->orWhere('last_name', 'like', '%' . $this->studentSearch . '%')
                      ->orWhere('email', 'like', '%' . $this->studentSearch . '%')
                      ->orWhereHas('studentProfile', function ($sq) {
                          $sq->where('student_reg_number', 'like', '%' . $this->studentSearch . '%')
                             ->orWhere('institution_name', 'like', '%' . $this->studentSearch . '%');
                      });
                });
            })
            ->limit(20)
            ->get();
    }


      public function selectStudent($studentId)
    {
        $this->selectedStudentId = $studentId;
        $this->selectedStudent = User::with('studentProfile')
            ->role('student')
            ->findOrFail($studentId);

        // Load student's pending applications (matches)
        $this->studentApplications = Application::with(['opportunity.organization'])
            ->where('user_id', $studentId)
            ->whereIn('status', ['pending', 'reviewing', 'shortlisted'])
            ->orderByDesc('match_score')
            ->get();

        $this->nextStep();
    }

    public function selectApplication($applicationId)
    {
        $this->selectedApplicationId = $applicationId;
        $this->selectedApplication = Application::with(['opportunity.organization', 'student'])
            ->findOrFail($applicationId);

        // Pre-fill dates if opportunity has them
        if ($this->selectedApplication->opportunity) {
            $opp = $this->selectedApplication->opportunity;
            $this->placementData['start_date'] = $opp->start_date?->format('Y-m-d') ?? now()->format('Y-m-d');
            $this->placementData['end_date'] = $opp->end_date?->format('Y-m-d') ?? now()->addMonths(3)->format('Y-m-d');
        }

        $this->nextStep();
    }

    public function nextStep()
    {
        $this->validate($this->validationRules[$this->currentStep] ?? []);
        
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }

        if ($this->currentStep === 4) {
            $this->preparePreview();
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function preparePreview()
    {
        $this->placementPreview = [
            'student' => $this->selectedStudent->full_name,
            'student_reg' => $this->selectedStudent->studentProfile->student_reg_number,
            'institution' => $this->selectedStudent->studentProfile->institution_name,
            'opportunity' => $this->selectedApplication->opportunity->title,
            'organization' => $this->selectedApplication->opportunity->organization->name,
            'match_score' => $this->selectedApplication->match_score,
            'start_date' => $this->placementData['start_date'],
            'end_date' => $this->placementData['end_date'],
            'duration' => \Carbon\Carbon::parse($this->placementData['start_date'])
                ->diffInMonths(\Carbon\Carbon::parse($this->placementData['end_date'])),
            'department' => $this->placementData['department'],
            'supervisor' => $this->placementData['supervisor_name'],
            'supervisor_contact' => $this->placementData['supervisor_contact'],
            'stipend' => $this->placementData['stipend'],
            'notes' => $this->placementData['notes'],
        ];
    }

     public function savePlacement()
    {
        $this->validate($this->validationRules[3]);

        DB::beginTransaction();

        try {
            // Create the placement
            $placement = Placement::create([
                'student_id' => $this->selectedStudentId,
                'admin_id' => auth()->id(),
                'organization_id' => $this->selectedApplication->opportunity->organization_id,
                'attachment_opportunity_id' => $this->selectedApplication->attachment_opportunity_id,
                'application_id' => $this->selectedApplication->id,
                'status' => 'placed',
                'start_date' => $this->placementData['start_date'],
                'end_date' => $this->placementData['end_date'],
                'department' => $this->placementData['department'],
                'supervisor_name' => $this->placementData['supervisor_name'],
                'supervisor_contact' => $this->placementData['supervisor_contact'],
                'notes' => $this->placementData['notes'],
                'stipend' => $this->placementData['stipend'],
                'placement_confirmed_at' => now(),
            ]);

            // Update application status to accepted
            $this->selectedApplication->update([
                'status' => 'accepted',
                'reviewed_at' => now(),
            ]);

            // Update student profile status to placed
            $this->selectedStudent->studentProfile->update([
                'attachment_status' => 'placed',
                'attachment_start_date' => $this->placementData['start_date'],
                'attachment_end_date' => $this->placementData['end_date'],
            ]);

            // Reject other pending applications for this student
            Application::where('user_id', $this->selectedStudentId)
                ->where('id', '!=', $this->selectedApplication->id)
                ->whereIn('status', ['pending', 'reviewing', 'shortlisted'])
                ->update([
                    'status' => 'rejected',
                    'reviewed_at' => now(),
                    'employer_notes' => 'Another placement was accepted',
                ]);

            DB::commit();

            // Log the placement creation
            activity_log(
                'Placement created for student: ' . $this->selectedStudent->full_name,
                'placement_created',
                [
                    'placement_id' => $placement->id,
                    'student_id' => $this->selectedStudentId,
                    'organization_id' => $placement->organization_id,
                    'opportunity_id' => $placement->attachment_opportunity_id,
                    'application_id' => $this->selectedApplication->id,
                ],
                'placement'
            );

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Placement created successfully! Student has been notified.'
            ]);

            // Reset and redirect to placements list
            return redirect()->route('admin.placements.index');

        } catch (\Exception $e) {
            DB::rollBack();
            
            activity_log(
                'Failed to create placement for student: ' . $this->selectedStudent->full_name,
                'placement_failed',
                [
                    'student_id' => $this->selectedStudentId,
                    'error' => $e->getMessage(),
                ],
                'placement'
            );

            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Failed to create placement: ' . $e->getMessage()
            ]);
        }
    }



    public function render()
    {
        return view('livewire.admin.placements.create', [
            'students' => $this->currentStep === 1 ? $this->students : collect(),
        ]);
    }
}
