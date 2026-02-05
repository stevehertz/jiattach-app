<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    public User $user;

    public $showDeleteModal = false;
    public $deleteConfirmation = '';
    public $hasRelatedRecords = false;

    public function mount(User $user)
    {
        $this->user = $user->load(['roles', 'studentProfile']);
        $this->hasRelatedRecords = $this->checkRelatedRecords();
    }

    public function toggleActive()
    {
        // Prevent modifying super-admin or yourself
        if ($this->user->hasRole('super-admin')) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot modify super-admin user!'
            ]);
            return;
        }

        if ($this->user->id === Auth::id()) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'You cannot modify your own account!'
            ]);
            return;
        }

        $this->user->update(['is_active' => !$this->user->is_active]);

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => $this->user->is_active ? 'User activated successfully!' : 'User deactivated successfully!'
        ]);
    }

     public function verifyUser()
    {
        // Prevent modifying super-admin
        if ($this->user->hasRole('super-admin')) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot modify super-admin user!'
            ]);
            return;
        }

        $this->user->update(['is_verified' => true]);

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'User verified successfully!'
        ]);
    }

    public function confirmDelete()
    {
        // Check if user can be deleted
        if ($this->user->hasRole('super-admin')) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot delete super-admin user!'
            ]);
            return;
        }

        if ($this->user->id === Auth::id()) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'You cannot delete your own account!'
            ]);
            return;
        }

        if ($this->hasRelatedRecords) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot delete user with related records!'
            ]);
            return;
        }

        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        $confirmation = trim($this->deleteConfirmation);
        
        // Final validation
        if (strtoupper($confirmation) !== 'DELETE') {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Please type "DELETE" to confirm deletion.'
            ]);
            return;
        }
        
        // Double-check protections
        if ($this->user->hasRole('super-admin')) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot delete super-admin user!'
            ]);
            $this->resetDeleteModal();
            return;
        }
        
        if ($this->user->id === Auth::id()) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'You cannot delete your own account!'
            ]);
            $this->resetDeleteModal();
            return;
        }
        
        if ($this->hasRelatedRecords) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Cannot delete user with related records!'
            ]);
            $this->resetDeleteModal();
            return;
        }
        
        // Delete the user
        $userName = $this->user->full_name;
        $this->user->delete();
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => "User '{$userName}' deleted successfully!"
        ]);
        
        $this->resetDeleteModal();
        
        // Redirect back to users list
        return redirect()->route('admin.users.index');
    }

     public function resetDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteConfirmation = '';
    }
    
    private function checkRelatedRecords(): bool
    {
        $user = $this->user;
        
        // Check employer opportunities
        if ($user->employer && $user->employer->opportunities()->count() > 0) {
            return true;
        }
        
        // Check applications
        if ($user->applications()->count() > 0) {
            return true;
        }
        
        // Check mentor relationships
        if ($user->mentor && $user->mentor->mentorships()->count() > 0) {
            return true;
        }
        
        // Check as mentee
        // if ($user->mentorshipsAsMentee()->count() > 0) {
        //     return true;
        // }
        
        // Check exchange program applications
        // if ($user->exchangeProgramApplications()->count() > 0) {
        //     return true;
        // }
        
        return false;
    }
    
    public function getCanDeleteProperty()
    {
        return strtoupper(trim($this->deleteConfirmation)) === 'DELETE';
    }
    
    public function render()
    {
        return view('livewire.admin.users.show');
    }
}
