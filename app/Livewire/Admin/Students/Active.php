<?php 

namespace App\Livewire\Admin\Students;

class Active extends Index
{
    public function mount($statsOnly = false)
    {
        parent::mount($statsOnly);
        // Active students are those with is_active = true
    }

    public function getStudentsQuery()
    {
        return parent::getStudentsQuery()->where('is_active', true);
    }
}