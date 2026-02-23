<?php

namespace App\Livewire\Admin\Opportunities;

use App\Models\AttachmentOpportunity;
use Livewire\Component;

class Show extends Component
{
    public AttachmentOpportunity $opportunity;
    public $confirmingAction = null;
    public $actionToConfirm = null;

    public function mount(AttachmentOpportunity $opportunity)
    {
        $this->opportunity = $opportunity->load([
            'organization',
            'applications.student.studentProfile',
            'placements',
            'applications' => function ($query) {
                $query->with(['student', 'placement'])->latest()->take(20);
            }
        ]);
    }

    // Trigger confirmation modal
    public function confirmAction($action, $title = '', $text = '')
    {
        $this->actionToConfirm = $action;
        $this->confirmingAction = [
            'action' => $action,
            'title' => $title,
            'text' => $text
        ];

        $this->dispatch('show-swal-confirm', [
            'action' => $action,
            'title' => $title,
            'text' => $text
        ]);
    }

    // Execute confirmed action
    public function executeConfirmedAction()
    {
        if (!$this->actionToConfirm) {
            return;
        }

        $action = $this->actionToConfirm;
        $this->actionToConfirm = null;
        $this->confirmingAction = null;

        match ($action) {
            'publish' => $this->publishOpportunity(),
            'close' => $this->closeOpportunity(),
            'markAsFilled' => $this->markAsFilled(),
            'cancel' => $this->cancelOpportunity(),
            'delete' => $this->deleteOpportunity(),
            default => null
        };
    }


    public function publishOpportunity()
    {
        try {
            if (!in_array($this->opportunity->status, ['draft', 'pending_approval'])) {
                throw new \Exception('Only draft or pending approval opportunities can be published.');
            }

            $this->opportunity->update([
                'status' => 'open',
                'published_at' => now()
            ]);

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Opportunity published successfully!'
            );

            $this->opportunity->refresh();
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: $e->getMessage()
            );
        }
    }

    public function closeOpportunity()
    {
        try {
            if ($this->opportunity->status !== 'open') {
                throw new \Exception('Only open opportunities can be closed.');
            }

            $this->opportunity->update([
                'status' => 'closed',
                'closed_at' => now()
            ]);

            $this->dispatch(
                'notify',
                type: 'warning',
                message: 'Opportunity closed successfully!'
            );

            $this->opportunity->refresh();
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: $e->getMessage()
            );
        }
    }

    public function markAsFilled()
    {
        try {
            if (!in_array($this->opportunity->status, ['open', 'closed'])) {
                throw new \Exception('Cannot mark as filled from current status.');
            }

            $this->opportunity->update([
                'status' => 'filled',
                'filled_at' => now()
            ]);

            $this->dispatch(
                'notify',
                type: 'info',
                message: 'Opportunity marked as filled!'
            );

            $this->opportunity->refresh();
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: $e->getMessage()
            );
        }
    }

    public function cancelOpportunity()
    {
        try {
            if (!in_array($this->opportunity->status, ['open', 'pending_approval', 'draft', 'closed'])) {
                throw new \Exception('Cannot cancel opportunity in current status.');
            }

            $this->opportunity->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            $this->dispatch(
                'notify',
                type: 'warning',
                message: 'Opportunity cancelled!'
            );

            $this->opportunity->refresh();
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: $e->getMessage()
            );
        }
    }

    public function deleteOpportunity()
    {
         try {
            if ($this->opportunity->applications()->count() > 0) {
                throw new \Exception('Cannot delete opportunity with applications. Close it instead.');
            }

            if (!in_array($this->opportunity->status, ['draft', 'cancelled'])) {
                throw new \Exception('Only draft or cancelled opportunities can be deleted.');
            }

            $this->opportunity->delete();

            $this->dispatch('notify',
                type: 'success',
                message: 'Opportunity deleted successfully!'
            );

            return redirect()->route('admin.opportunities.index');
        } catch (\Exception $e) {
            $this->dispatch('notify',
                type: 'error',
                message: $e->getMessage()
            );
        }
    }

    // Helper methods for status styling
    public function getStatusColor($status): string
    {
       return match($status) {
            'open', 'published' => 'success',
            'draft' => 'secondary',
            'pending_approval' => 'warning',
            'closed', 'cancelled' => 'danger',
            'filled' => 'info',
            default => 'secondary'
        };
    }

    public function getStatusLabel($status): string
    {
        return match($status) {
            'open' => 'Active',
            'draft' => 'Draft',
            'pending_approval' => 'Pending Approval',
            'closed' => 'Closed',
            'filled' => 'Filled',
            'cancelled' => 'Cancelled',
            default => ucfirst($status)
        };
    }

    public function render()
    {
        return view('livewire.admin.opportunities.show');
    }
}
