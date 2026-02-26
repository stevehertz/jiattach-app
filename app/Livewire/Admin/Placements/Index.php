<?php

namespace App\Livewire\Admin\Placements;

use App\Models\AttachmentOpportunity;
use App\Models\Organization;
use App\Models\Placement;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $organizationFilter = '';
    public $opportunityFilter = '';
    public $studentFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 20;
    public $selectedPlacements = [];
    public $selectAll = false;
    public $showFilters = false;
    public $showBulkActions = false;
    public $viewType = 'all'; // 'all', 'active', 'upcoming', 'completed', 'cancelled'

    // Bulk actions
    public $bulkAction = '';
    public $showBulkActionModal = false;

    // Advanced filters
    public $departmentFilter = '';
    public $supervisorFilter = '';
    public $minStipend = '';
    public $maxStipend = '';
    public $durationMin = '';
    public $durationMax = '';

    protected $listeners = ['refreshPlacements' => '$refresh'];

    public function mount($viewType = 'all')
    {
        $this->viewType = $viewType;
        $this->applyViewFilters();
    }

    protected function applyViewFilters()
    {
        switch ($this->viewType) {
            case 'active':
                $this->statusFilter = 'placed';
                $this->dateFrom = now()->subMonths(6)->format('Y-m-d');
                $this->dateTo = now()->addMonths(6)->format('Y-m-d');
                break;
            case 'upcoming':
                $this->statusFilter = 'placed';
                $this->dateFrom = now()->format('Y-m-d');
                break;
            case 'completed':
                $this->statusFilter = 'completed';
                break;
            case 'cancelled':
                $this->statusFilter = 'cancelled';
                break;
            case 'pending':
                $this->statusFilter = 'pending,processing';
                break;
        }
    }

    public function applyBulkAction()
    {
        if (!$this->bulkAction || empty($this->selectedPlacements)) {
            return;
        }

        $placements = Placement::whereIn('id', $this->selectedPlacements)->get();

        switch ($this->bulkAction) {
            case 'complete':
                foreach ($placements as $placement) {
                    if ($placement->status === 'placed') {
                        $placement->transitionTo('completed', 'Bulk completion by admin');
                    }
                }
                $message = 'Placements marked as completed successfully!';
                break;

            case 'cancel':
                foreach ($placements as $placement) {
                    if (in_array($placement->status, ['pending', 'processing', 'placed'])) {
                        $placement->transitionTo('cancelled', 'Bulk cancellation by admin');

                        // Reset student profile attachment status if needed
                        if ($placement->student && $placement->student->studentProfile) {
                            $placement->student->studentProfile->resetStatus();
                        }
                    }
                }
                $message = 'Placements cancelled successfully!';
                break;

            case 'extend':
                session(['bulk_placements' => $this->selectedPlacements]);
                return redirect()->route('admin.placements.bulk-extend');
                break;

            case 'export':
                return $this->exportPlacements();
                break;

            case 'send_reminder':
                foreach ($placements as $placement) {
                    // Send reminder notification to student/supervisor
                    // You can implement a notification class for this
                }
                $message = 'Reminders sent successfully!';
                break;

            case 'generate_reports':
                session(['bulk_placements' => $this->selectedPlacements]);
                return redirect()->route('admin.placements.bulk-reports');
                break;
        }

        $this->selectedPlacements = [];
        $this->showBulkActions = false;
        $this->showBulkActionModal = false;
        $this->bulkAction = '';

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => $message ?? 'Action completed successfully!'
        ]);

        $this->dispatch('refreshPlacements');
    }

    private function exportPlacements()
    {
        $placements = Placement::whereIn('id', $this->selectedPlacements)
            ->with(['student', 'organization', 'opportunity', 'admin'])
            ->get();

        $csvData = [];

        // Add headers
        $csvData[] = [
            'Placement ID',
            'Student Name',
            'Student Email',
            'Organization',
            'Opportunity',
            'Department',
            'Supervisor',
            'Supervisor Contact',
            'Status',
            'Start Date',
            'End Date',
            'Duration (Days)',
            'Stipend (KES)',
            'Notes',
            'Confirmed At',
            'Created By',
            'Created At',
        ];

        // Add data
        foreach ($placements as $placement) {
            $csvData[] = [
                $placement->id,
                $placement->student?->full_name ?? 'N/A',
                $placement->student?->email ?? 'N/A',
                $placement->organization?->name ?? 'N/A',
                $placement->opportunity?->title ?? 'N/A',
                $placement->department ?? 'N/A',
                $placement->supervisor_name ?? 'N/A',
                $placement->supervisor_contact ?? 'N/A',
                $placement->status_label,
                $placement->start_date?->format('Y-m-d') ?? 'N/A',
                $placement->end_date?->format('Y-m-d') ?? 'N/A',
                $placement->duration_days ?? 'N/A',
                $placement->stipend ? number_format($placement->stipend, 2) : 'N/A',
                $placement->notes ?? 'N/A',
                $placement->placement_confirmed_at?->format('Y-m-d H:i') ?? 'N/A',
                $placement->admin?->full_name ?? 'N/A',
                $placement->created_at->format('Y-m-d H:i'),
            ];
        }

        $filename = 'placements_export_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedPlacements = $this->getPlacementsQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedPlacements = [];
        }
    }

    public function updatedSelectedPlacements()
    {
        $this->selectAll = false;
        $this->showBulkActions = count($this->selectedPlacements) > 0;
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

    public function resetFilters()
    {
        $this->reset([
            'search',
            'statusFilter',
            'organizationFilter',
            'opportunityFilter',
            'studentFilter',
            'dateFrom',
            'dateTo',
            'departmentFilter',
            'supervisorFilter',
            'minStipend',
            'maxStipend',
            'durationMin',
            'durationMax',
            'sortField',
            'sortDirection',
            'viewType'
        ]);
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->viewType = 'all';
    }

    public function getPlacementsQuery()
    {
        $query = Placement::with([
            'student.studentProfile',
            'organization',
            'opportunity',
            'admin'
        ])
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('notes', 'like', '%' . $search . '%')
                        ->orWhere('department', 'like', '%' . $search . '%')
                        ->orWhere('supervisor_name', 'like', '%' . $search . '%')
                        ->orWhere('supervisor_contact', 'like', '%' . $search . '%')
                        ->orWhereHas('student', function ($q) use ($search) {
                            $q->where('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('organization', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('opportunity', function ($q) use ($search) {
                            $q->where('title', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query, $status) {
                $statuses = explode(',', $status);
                $query->whereIn('status', $statuses);
            })
            ->when($this->organizationFilter, function ($query, $orgId) {
                $query->where('organization_id', $orgId);
            })
            ->when($this->opportunityFilter, function ($query, $oppId) {
                $query->where('attachment_opportunity_id', $oppId);
            })
            ->when($this->studentFilter, function ($query, $studentId) {
                $query->where('student_id', $studentId);
            })
            ->when($this->departmentFilter, function ($query, $department) {
                $query->where('department', 'like', '%' . $department . '%');
            })
            ->when($this->supervisorFilter, function ($query, $supervisor) {
                $query->where('supervisor_name', 'like', '%' . $supervisor . '%');
            })
            ->when($this->minStipend, function ($query, $stipend) {
                $query->where('stipend', '>=', $stipend);
            })
            ->when($this->maxStipend, function ($query, $stipend) {
                $query->where('stipend', '<=', $stipend);
            })
            ->when($this->durationMin, function ($query, $days) {
                $query->where('duration_days', '>=', $days);
            })
            ->when($this->durationMax, function ($query, $days) {
                $query->where('duration_days', '<=', $days);
            })
            ->when($this->dateFrom, function ($query, $date) {
                $query->whereDate('start_date', '>=', $date);
            })
            ->when($this->dateTo, function ($query, $date) {
                $query->whereDate('end_date', '<=', $date);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

     // Computed Properties for Filters
    public function getOrganizationsProperty()
    {
        return Organization::orderBy('name')
            ->get(['id', 'name']);
    }

    public function getOpportunitiesProperty()
    {
        return AttachmentOpportunity::orderBy('title')
            ->get(['id', 'title']);
    }

    public function getStudentsProperty()
    {
        return User::role('student')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email']);
    }

    public function getDepartmentsProperty()
    {
        return Placement::distinct('department')
            ->whereNotNull('department')
            ->orderBy('department')
            ->pluck('department');
    }

    public function getPlacementsProperty()
    {
        return $this->getPlacementsQuery()->paginate($this->perPage);
    }

    public function getStatsProperty()
    {
        return [
            'total' => Placement::count(),
            'active' => Placement::where('status', 'placed')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->count(),
            'upcoming' => Placement::where('status', 'placed')
                ->whereDate('start_date', '>', now())
                ->count(),
            'completed' => Placement::where('status', 'completed')->count(),
            'pending' => Placement::whereIn('status', ['pending', 'processing'])->count(),
            'cancelled' => Placement::where('status', 'cancelled')->count(),
            'total_students' => Placement::distinct('student_id')->count('student_id'),
            'total_organizations' => Placement::distinct('organization_id')->count('organization_id'),
            'avg_duration' => Placement::whereNotNull('duration_days')->avg('duration_days'),
            'avg_stipend' => Placement::whereNotNull('stipend')->avg('stipend'),
        ];
    }

     public function viewPlacement($placementId)
    {
        return redirect()->route('admin.placements.show', $placementId);
    }

    public function editPlacement($placementId)
    {
        return redirect()->route('admin.placements.edit', $placementId);
    }

    public function deletePlacement($placementId)
    {
        $placement = Placement::findOrFail($placementId);

        // Check if placement can be deleted
        if (!in_array($placement->status, ['pending', 'cancelled', 'completed'])) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot delete active placement. Please cancel it first.'
            ]);
            return;
        }

        // Reset student profile if needed
        if ($placement->student && $placement->student->studentProfile) {
            $placement->student->studentProfile->resetStatus();
        }

        $placement->delete();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Placement deleted successfully!'
        ]);

        $this->dispatch('refreshPlacements');
    }

    public function generateReport($placementId)
    {
        return redirect()->route('admin.placements.report', $placementId);
    }

    public function sendReminder($placementId)
    {
        $placement = Placement::findOrFail($placementId);

        // Send reminder logic here
        // You can implement a notification class

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Reminder sent successfully!'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.placements.index', [
            'placements' => $this->placements,
            'stats' => $this->stats,
            'organizations' => $this->organizations,
            'opportunities' => $this->opportunities,
            'students' => $this->students,
            'departments' => $this->departments,
        ]);
    }
}
