<?php

namespace App\Livewire\Admin\Opportunities;

use App\Models\AttachmentOpportunity;
use Livewire\Component;

class Show extends Component
{
    public AttachmentOpportunity $opportunity;

    public function mount(AttachmentOpportunity $opportunity)
    {
        $this->opportunity = $opportunity->load([
            'organization',
            'applications.student.studentProfile',
            'applications' => function ($query) {
                $query->latest()->take(20);
            }
        ]);
    }

    public function publishOpportunity()
    {
        try {
            $this->opportunity->publish();
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Opportunity published successfully!'
            ]);
            
            $this->opportunity->refresh();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function closeOpportunity()
    {
        try {
            $this->opportunity->close();
            
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => 'Opportunity closed successfully!'
            ]);
            
            $this->opportunity->refresh();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function markAsFilled()
    {
        try {
            $this->opportunity->markAsFilled();
            
            $this->dispatch('show-toast', [
                'type' => 'info',
                'message' => 'Opportunity marked as filled!'
            ]);
            
            $this->opportunity->refresh();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function cancelOpportunity()
    {
        try {
            $this->opportunity->cancel();
            
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => 'Opportunity cancelled!'
            ]);
            
            $this->opportunity->refresh();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteOpportunity()
    {
        if ($this->opportunity->applications()->count() > 0) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot delete opportunity with applications. Close it instead.'
            ]);
            return;
        }
        
        $this->opportunity->delete();
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Opportunity deleted successfully!'
        ]);
        
        return redirect()->route('admin.opportunities.index');
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
