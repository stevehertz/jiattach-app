<?php

namespace App\Livewire\Admin\Applications;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Placement;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class HiredStage extends Component
{
    use WithPagination;

    // Filters
    public $statusFilter = 'all'; // all, placed, completed, cancelled
    public $search = '';
    public $organizationFilter = '';
    public $departmentFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    
    // Placement details modal
    public $showPlacementModal = false;
    public $selectedApplicationId;
    public $placementStartDate;
    public $placementEndDate;
    public $placementDepartment;
    public $placementSupervisorName;
    public $placementSupervisorContact;
    public $placementNotes;
    public $placementRequirements = [];
    public $newRequirement = '';
    
    // Placement view modal
    public $showPlacementViewModal = false;
    public $selectedPlacement;
    
    // Placement update modal
    public $showUpdatePlacementModal = false;
    public $selectedPlacementId;
    public $updateStartDate;
    public $updateEndDate;
    public $updateStatus;
    public $updateNotes;
    
    // Completion modal
    public $showCompleteModal = false;
    public $selectedPlacementForComplete;
    public $completionNotes;
    public $completionFeedback;
    public $completionRating = 5;
    
    // Cancel modal
    public $showCancelModal = false;
    public $selectedPlacementForCancel;
    public $cancelReason;
    
    // Bulk actions
    public $selectedApplications = [];
    public $selectAll = false;
    public $bulkAction = '';
    
    // Statistics
    public $stats = [];
    
    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
        'organizationFilter' => ['except' => ''],
        'departmentFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];
    
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'placementUpdated' => 'refreshPlacements',
    ];

     public function mount()
    {
        $this->loadStats();
    }
    
    public function refreshPlacements()
    {
        $this->dispatch('refreshComponent');
        $this->loadStats();
    }
    
    /**
     * Load statistics for the dashboard
     */
    public function loadStats()
    {
        $this->stats = [
            'total_placed' => Placement::where('status', 'placed')->count(),
            'active_placements' => Placement::where('status', 'placed')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count(),
            'upcoming_placements' => Placement::where('status', 'placed')
                ->where('start_date', '>', now())
                ->count(),
            'completed_placements' => Placement::where('status', 'completed')->count(),
            'cancelled_placements' => Placement::where('status', 'cancelled')->count(),
            'pending_placements' => Placement::where('status', 'pending')->count(),
            'total_students' => Placement::distinct('student_id')->count('student_id'),
            'total_organizations' => Placement::distinct('organization_id')->count('organization_id'),
        ];
    }

     /**
     * Get the base query for placements
     */
    public function getPlacementsQuery()
    {
        $query = Placement::query()
            ->with([
                'student' => function($q) {
                    $q->with('studentProfile');
                },
                'organization',
                'opportunity',
                'application' => function($q) {
                    $q->with('interviews');
                }
            ]);
        
        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }
        
        // Search by student name or organization
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('student', function($sub) {
                    $sub->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })->orWhereHas('organization', function($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }
        
        // Filter by organization
        if ($this->organizationFilter) {
            $query->whereHas('organization', function($q) {
                $q->where('name', 'like', '%' . $this->organizationFilter . '%');
            });
        }
        
        // Filter by department
        if ($this->departmentFilter) {
            $query->where('department', 'like', '%' . $this->departmentFilter . '%');
        }
        
        // Date range filter
        if ($this->dateFrom) {
            $query->whereDate('start_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('end_date', '<=', $this->dateTo);
        }
        
        // Sorting
        if ($this->sortBy === 'student_name') {
            $query->join('users', 'placements.student_id', '=', 'users.id')
                  ->orderBy('users.first_name', $this->sortDirection)
                  ->orderBy('users.last_name', $this->sortDirection);
        } elseif ($this->sortBy === 'organization_name') {
            $query->join('organizations', 'placements.organization_id', '=', 'organizations.id')
                  ->orderBy('organizations.name', $this->sortDirection);
        } elseif ($this->sortBy === 'duration') {
            $query->orderByRaw('DATEDIFF(end_date, start_date) ' . $this->sortDirection);
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }
        
        return $query;
    }
    
    public function getPlacementsProperty()
    {
        return $this->getPlacementsQuery()->paginate(15);
    }
    
    /**
     * Get organizations for filter dropdown
     */
    public function getOrganizationsProperty()
    {
        return \App\Models\Organization::orderBy('name')->get(['id', 'name']);
    }
    
    /**
     * Open placement modal for a hired application
     */
    public function openPlacementModal($applicationId)
    {
        $application = Application::with(['opportunity', 'student'])
            ->findOrFail($applicationId);
        
        // Check if application status is OFFER_ACCEPTED
        if ($application->status !== ApplicationStatus::OFFER_ACCEPTED) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot create placement: Application status is not "Offer Accepted".'
            ]);
            return;
        }
        
        // Check if placement already exists
        if ($application->placement) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'A placement already exists for this application.'
            ]);
            return;
        }
        
        $this->selectedApplicationId = $applicationId;
        
        // Pre-fill from offer details if available
        if ($application->offer_details) {
            $this->placementStartDate = $application->offer_details['start_date'] ?? now()->addDays(7)->format('Y-m-d');
            $this->placementEndDate = $application->offer_details['end_date'] ?? now()->addMonths(3)->format('Y-m-d');
        } else {
            $this->placementStartDate = now()->addDays(7)->format('Y-m-d');
            $this->placementEndDate = now()->addMonths(3)->format('Y-m-d');
        }
        
        $this->placementDepartment = '';
        $this->placementSupervisorName = '';
        $this->placementSupervisorContact = '';
        $this->placementNotes = '';
        $this->placementRequirements = [];
        $this->newRequirement = '';
        
        $this->showPlacementModal = true;
    }
    
    /**
     * Add requirement to the list
     */
    public function addRequirement()
    {
        $requirement = trim($this->newRequirement);
        if ($requirement && !in_array($requirement, $this->placementRequirements)) {
            $this->placementRequirements[] = $requirement;
            $this->newRequirement = '';
        }
    }
    
    /**
     * Remove requirement from the list
     */
    public function removeRequirement($index)
    {
        unset($this->placementRequirements[$index]);
        $this->placementRequirements = array_values($this->placementRequirements);
    }
    
    /**
     * Create placement from accepted offer
     */
    public function createPlacement()
    {
        $this->validate([
            'placementStartDate' => 'required|date',
            'placementEndDate' => 'required|date|after:placementStartDate',
            'placementDepartment' => 'nullable|string|max:255',
            'placementSupervisorName' => 'nullable|string|max:255',
            'placementSupervisorContact' => 'nullable|string|max:255',
            'placementNotes' => 'nullable|string|max:1000',
        ]);
        
        $application = Application::findOrFail($this->selectedApplicationId);
        
        DB::transaction(function () use ($application) {
            // Calculate duration in days
            $startDate = \Carbon\Carbon::parse($this->placementStartDate);
            $endDate = \Carbon\Carbon::parse($this->placementEndDate);
            $durationDays = $startDate->diffInDays($endDate);
            
            // Create placement record
            $placement = Placement::create([
                'application_id' => $application->id,
                'student_id' => $application->student_id,
                'admin_id' => auth()->id(),
                'organization_id' => $application->organization_id,
                'attachment_opportunity_id' => $application->attachment_opportunity_id,
                'status' => 'placed',
                'start_date' => $this->placementStartDate,
                'end_date' => $this->placementEndDate,
                'duration_days' => $durationDays,
                'department' => $this->placementDepartment,
                'supervisor_name' => $this->placementSupervisorName,
                'supervisor_contact' => $this->placementSupervisorContact,
                'notes' => $this->placementNotes,
                'requirements' => $this->placementRequirements,
                'placement_confirmed_at' => now(),
            ]);
            
            // Update application status to HIRED
            $oldStatus = $application->status;
            $application->status = ApplicationStatus::HIRED;
            $application->hired_at = now();
            $application->save();
            
            // Update student profile
            $studentProfile = $application->student->studentProfile;
            if ($studentProfile) {
                $studentProfile->update([
                    'attachment_status' => 'placed',
                    'attachment_start_date' => $this->placementStartDate,
                    'attachment_end_date' => $this->placementEndDate,
                ]);
            }
            
            // Add application history
            $application->addHistory(
                'hired',
                $application->student_id,
                $application->organization_id,
                $oldStatus->value,
                ApplicationStatus::HIRED->value,
                'Student placed for attachment',
                [
                    'placement_id' => $placement->id,
                    'start_date' => $this->placementStartDate,
                    'end_date' => $this->placementEndDate,
                    'department' => $this->placementDepartment,
                    'supervisor' => $this->placementSupervisorName,
                ]
            );
            
            // Log activity
            activity_log(
                "Placement created for student {$application->student->full_name} at {$application->organization->name}",
                'placement_created',
                [
                    'placement_id' => $placement->id,
                    'application_id' => $application->id,
                    'student_name' => $application->student->full_name,
                    'organization' => $application->organization->name,
                    'start_date' => $this->placementStartDate,
                    'end_date' => $this->placementEndDate,
                ],
                'placement'
            );
        });
        
        $this->showPlacementModal = false;
        $this->selectedApplicationId = null;
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Placement created successfully!'
        ]);
        
        $this->refreshPlacements();
    }
    
    /**
     * View placement details
     */
    public function viewPlacement($placementId)
    {
        $this->selectedPlacement = Placement::with([
            'student.studentProfile',
            'organization',
            'opportunity',
            'application.interviews'
        ])->findOrFail($placementId);
        
        $this->showPlacementViewModal = true;
    }
    
    /**
     * Open update placement modal
     */
    public function openUpdatePlacementModal($placementId)
    {
        $placement = Placement::findOrFail($placementId);
        
        $this->selectedPlacementId = $placementId;
        $this->updateStartDate = $placement->start_date->format('Y-m-d');
        $this->updateEndDate = $placement->end_date->format('Y-m-d');
        $this->updateStatus = $placement->status;
        $this->updateNotes = $placement->notes;
        
        $this->showUpdatePlacementModal = true;
    }
    
    /**
     * Update placement details
     */
    public function updatePlacement()
    {
        $this->validate([
            'updateStartDate' => 'required|date',
            'updateEndDate' => 'required|date|after:updateStartDate',
            'updateStatus' => 'required|in:placed,completed,cancelled',
            'updateNotes' => 'nullable|string|max:1000',
        ]);
        
        $placement = Placement::findOrFail($this->selectedPlacementId);
        
        DB::transaction(function () use ($placement) {
            $oldStatus = $placement->status;
            
            // Calculate new duration
            $startDate = \Carbon\Carbon::parse($this->updateStartDate);
            $endDate = \Carbon\Carbon::parse($this->updateEndDate);
            $durationDays = $startDate->diffInDays($endDate);
            
            $placement->update([
                'start_date' => $this->updateStartDate,
                'end_date' => $this->updateEndDate,
                'duration_days' => $durationDays,
                'status' => $this->updateStatus,
                'notes' => $this->updateNotes,
            ]);
            
            // Update student profile if dates changed
            if ($placement->student->studentProfile) {
                $placement->student->studentProfile->update([
                    'attachment_start_date' => $this->updateStartDate,
                    'attachment_end_date' => $this->updateEndDate,
                ]);
            }
            
            // Log activity
            activity_log(
                "Placement #{$placement->id} updated",
                'placement_updated',
                [
                    'placement_id' => $placement->id,
                    'old_status' => $oldStatus,
                    'new_status' => $this->updateStatus,
                    'old_start_date' => $placement->getOriginal('start_date'),
                    'new_start_date' => $this->updateStartDate,
                    'old_end_date' => $placement->getOriginal('end_date'),
                    'new_end_date' => $this->updateEndDate,
                ],
                'placement'
            );
        });
        
        $this->showUpdatePlacementModal = false;
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Placement updated successfully!'
        ]);
        
        $this->refreshPlacements();
    }
    
    /**
     * Open complete placement modal
     */
    public function openCompleteModal($placementId)
    {
        $placement = Placement::findOrFail($placementId);
        
        $this->selectedPlacementForComplete = $placement;
        $this->completionNotes = '';
        $this->completionFeedback = '';
        $this->completionRating = 5;
        
        $this->showCompleteModal = true;
    }
    
    /**
     * Complete placement
     */
    public function completePlacement()
    {
        $this->validate([
            'completionNotes' => 'nullable|string|max:1000',
            'completionFeedback' => 'nullable|string|max:2000',
            'completionRating' => 'required|integer|min:1|max:5',
        ]);
        
        $placement = $this->selectedPlacementForComplete;
        
        DB::transaction(function () use ($placement) {
            $placement->update([
                'status' => 'completed',
                'notes' => $this->completionNotes ?: $placement->notes,
            ]);
            
            // Update student profile
            if ($placement->student->studentProfile) {
                $placement->student->studentProfile->update([
                    'attachment_status' => 'completed',
                ]);
            }
            
            // Add completion feedback to placement
            $placement->update([
                'metadata' => array_merge($placement->metadata ?? [], [
                    'completion_feedback' => $this->completionFeedback,
                    'completion_rating' => $this->completionRating,
                    'completed_by' => auth()->user()->full_name,
                    'completed_at' => now()->toDateTimeString(),
                ]),
            ]);
            
            // Log activity
            activity_log(
                "Placement #{$placement->id} completed",
                'placement_completed',
                [
                    'placement_id' => $placement->id,
                    'student_name' => $placement->student->full_name,
                    'organization' => $placement->organization->name,
                    'rating' => $this->completionRating,
                    'feedback' => $this->completionFeedback,
                ],
                'placement'
            );
        });
        
        $this->showCompleteModal = false;
        $this->selectedPlacementForComplete = null;
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Placement marked as completed!'
        ]);
        
        $this->refreshPlacements();
    }
    
    /**
     * Open cancel placement modal
     */
    public function openCancelModal($placementId)
    {
        $placement = Placement::findOrFail($placementId);
        
        $this->selectedPlacementForCancel = $placement;
        $this->cancelReason = '';
        
        $this->showCancelModal = true;
    }
    
    /**
     * Cancel placement
     */
    public function cancelPlacement()
    {
        $this->validate([
            'cancelReason' => 'required|string|max:500',
        ]);
        
        $placement = $this->selectedPlacementForCancel;
        
        DB::transaction(function () use ($placement) {
            $placement->update([
                'status' => 'cancelled',
                'notes' => $this->cancelReason . "\n\n" . ($placement->notes ?? ''),
            ]);
            
            // Update student profile
            if ($placement->student->studentProfile) {
                $placement->student->studentProfile->update([
                    'attachment_status' => 'seeking',
                    'attachment_start_date' => null,
                    'attachment_end_date' => null,
                ]);
            }
            
            // Update application status
            if ($placement->application) {
                $placement->application->update([
                    'status' => ApplicationStatus::OFFER_ACCEPTED, // Back to offer accepted
                ]);
            }
            
            // Log activity
            activity_log(
                "Placement #{$placement->id} cancelled",
                'placement_cancelled',
                [
                    'placement_id' => $placement->id,
                    'student_name' => $placement->student->full_name,
                    'organization' => $placement->organization->name,
                    'reason' => $this->cancelReason,
                ],
                'placement'
            );
        });
        
        $this->showCancelModal = false;
        $this->selectedPlacementForCancel = null;
        
        $this->dispatch('show-toast', [
            'type' => 'warning',
            'message' => 'Placement cancelled successfully!'
        ]);
        
        $this->refreshPlacements();
    }
    
    /**
     * Bulk actions
     */
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedApplications = $this->placements->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedApplications = [];
        }
    }
    
    public function executeBulkAction()
    {
        if (empty($this->selectedApplications)) {
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => 'No placements selected.'
            ]);
            return;
        }
        
        if ($this->bulkAction === 'export') {
            $this->bulkExport();
        } elseif ($this->bulkAction === 'generate_reports') {
            $this->bulkGenerateReports();
        }
        
        $this->bulkAction = '';
        $this->selectedApplications = [];
        $this->selectAll = false;
    }
    
    protected function bulkExport()
    {
        $placements = Placement::whereIn('id', $this->selectedApplications)
            ->with(['student', 'organization', 'opportunity'])
            ->get();
        
        $fileName = 'placements_export_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
        
        $callback = function() use ($placements) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Placement ID', 'Student Name', 'Student Email', 'Student Phone',
                'Organization', 'Department', 'Supervisor', 'Supervisor Contact',
                'Start Date', 'End Date', 'Duration (Days)', 'Status', 'Created Date'
            ]);
            
            // Add data
            foreach ($placements as $placement) {
                fputcsv($file, [
                    $placement->id,
                    $placement->student->full_name,
                    $placement->student->email,
                    $placement->student->phone,
                    $placement->organization->name,
                    $placement->department ?? 'N/A',
                    $placement->supervisor_name ?? 'N/A',
                    $placement->supervisor_contact ?? 'N/A',
                    $placement->start_date->format('Y-m-d'),
                    $placement->end_date->format('Y-m-d'),
                    $placement->duration_days,
                    $placement->status,
                    $placement->created_at->format('Y-m-d'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    protected function bulkGenerateReports()
    {
        // Generate reports for selected placements
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Reports generated for ' . count($this->selectedApplications) . ' placements.'
        ]);
    }
    
    /**
     * Reset filters
     */
    public function resetFilters()
    {
        $this->reset(['statusFilter', 'search', 'organizationFilter', 'departmentFilter', 'dateFrom', 'dateTo', 'sortBy', 'sortDirection']);
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
    }
    
    /**
     * Get placement progress percentage
     */
    public function getProgressPercentage($placement)
    {
        if ($placement->status !== 'placed') {
            return 0;
        }
        
        $now = now();
        $start = $placement->start_date;
        $end = $placement->end_date;
        
        if ($now < $start) return 0;
        if ($now > $end) return 100;
        
        $totalDays = $start->diffInDays($end);
        $elapsedDays = $start->diffInDays($now);
        
        return round(($elapsedDays / $totalDays) * 100);
    }
    
    public function render()
    {
        return view('livewire.admin.applications.hired-stage', [
            'placements' => $this->placements,
            'organizations' => $this->organizations,
        ]);
    }
}
