<?php

namespace App\Livewire\Admin\Opportunities;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AttachmentOpportunity;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $statusFilter = '';
    public $locationFilter = '';
    public $employerFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 20;
    public $showFilters = false;
    public $viewType = 'all'; // 'all', 'active', 'pending'

    protected $listeners = ['refreshOpportunities' => '$refresh'];

    public function mount($viewType = 'all')
    {
        $this->viewType = $viewType;
        $this->applyViewFilters();
    }

    protected function applyViewFilters()
    {
        switch ($this->viewType) {
            case 'active':
                $this->statusFilter = 'published';
                break;
            case 'pending':
                $this->statusFilter = 'pending_approval';
                break;
        }
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

    public function getOpportunitiesQuery()
    {
        $query = AttachmentOpportunity::with(['organization', 'applications'])
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('location', 'like', '%' . $search . '%')
                        ->orWhere('county', 'like', '%' . $search . '%')
                        ->orWhereHas('organization', function ($q) use ($search) {
                            $q->where('company_name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($this->typeFilter, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($this->statusFilter, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($this->locationFilter, function ($query, $location) {
                $query->where('location', 'like', '%' . $location . '%')
                    ->orWhere('town', 'like', '%' . $location . '%')
                    ->orWhere('county', 'like', '%' . $location . '%');
            })
            ->when($this->employerFilter, function ($query, $employerId) {
                $query->where('employer_id', $employerId);
            });

        // Apply view-specific filters before withCount and ordering
        if ($this->viewType === 'active') {
            $query->where('status', 'published')
                ->where('deadline', '>=', now())
                ->whereRaw('slots_available > COALESCE((SELECT COUNT(*) FROM placements WHERE attachment_opportunities.id = placements.attachment_opportunity_id AND placements.deleted_at IS NULL), 0)');
        }

        $query->withCount('placements as slots_filled')
            ->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

     public function getOpportunitiesProperty()
    {
        return $this->getOpportunitiesQuery()->paginate($this->perPage);
    }

    public function getStatsProperty()
    {
        $total = AttachmentOpportunity::count();
        $active = AttachmentOpportunity::where('status', 'published')
            ->where('deadline', '>=', now())
            ->whereRaw('slots_available > COALESCE((SELECT COUNT(*) FROM placements WHERE attachment_opportunities.id = placements.attachment_opportunity_id AND placements.deleted_at IS NULL), 0)')
            ->count();
        $pending = AttachmentOpportunity::where('status', 'pending_approval')->count();
        $closed = AttachmentOpportunity::whereIn('status', ['closed', 'filled', 'cancelled'])->count();
        $withStipend = AttachmentOpportunity::whereNotNull('stipend')->where('stipend', '>', 0)->count();
        $today = AttachmentOpportunity::whereDate('created_at', today())->count();

        return [
            'total' => $total,
            'active' => $active,
            'pending' => $pending,
            'closed' => $closed,
            'with_stipend' => $withStipend,
            'today' => $today,
        ];
    }

    // public function getEmployersProperty()
    // {
    //     return Employer::with('user')
    //         ->whereHas('opportunities')
    //         ->orderBy('company_name')
    //         ->get()
    //         ->mapWithKeys(function ($employer) {
    //             return [$employer->id => $employer->company_name];
    //         });
    // }

    public function getOpportunityTypesProperty()
    {
        return [
            'internship' => 'Internship',
            'attachment' => 'Attachment',
            'volunteer' => 'Volunteer',
            'research' => 'Research',
            'part_time' => 'Part-time',
            'full_time' => 'Full-time',
        ];
    }

    public function getStatusOptionsProperty()
    {
        return [
            'draft' => 'Draft',
            'pending_approval' => 'Pending Approval',
            'published' => 'Published',
            'closed' => 'Closed',
            'filled' => 'Filled',
            'cancelled' => 'Cancelled',
        ];
    }

    public function getLocationsProperty()
    {
        return AttachmentOpportunity::distinct('location')
            ->whereNotNull('location')
            ->orderBy('location')
            ->pluck('location', 'location');
    }

    public function publishOpportunity($opportunityId)
    {
        $opportunity = AttachmentOpportunity::findOrFail($opportunityId);

        try {
            $opportunity->publish();

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Opportunity published successfully!'
            ]);

            $this->dispatch('refreshOpportunities');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function closeOpportunity($opportunityId)
    {
        $opportunity = AttachmentOpportunity::findOrFail($opportunityId);

        try {
            $opportunity->close();

            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => 'Opportunity closed successfully!'
            ]);

            $this->dispatch('refreshOpportunities');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function markAsFilled($opportunityId)
    {
        $opportunity = AttachmentOpportunity::findOrFail($opportunityId);

        try {
            $opportunity->markAsFilled();

            $this->dispatch('show-toast', [
                'type' => 'info',
                'message' => 'Opportunity marked as filled!'
            ]);

            $this->dispatch('refreshOpportunities');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteOpportunity($opportunityId)
    {
        $opportunity = AttachmentOpportunity::findOrFail($opportunityId);

        if ($opportunity->applications()->count() > 0) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot delete opportunity with applications. Close it instead.'
            ]);
            return;
        }

        $opportunity->delete();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Opportunity deleted successfully!'
        ]);

        $this->dispatch('refreshOpportunities');
    }

    public function viewOpportunity($opportunityId)
    {
        return redirect()->route('admin.opportunities.show', $opportunityId);
    }

    public function render()
    {
        return view('livewire.admin.opportunities.index', [
            'opportunities' => $this->opportunities,
            'stats' => $this->stats,
            // 'employers' => $this->employers,
            'opportunityTypes' => $this->opportunityTypes,
            'statusOptions' => $this->statusOptions,
            'locations' => $this->locations,
        ]);
    }
}
