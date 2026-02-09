<?php

namespace App\Livewire\Admin\Students;

class Seeking extends Index
{
    public function mount($statsOnly = false)
    {
        parent::mount($statsOnly);
        $this->attachmentStatusFilter = 'seeking';
    }

    public function getStudentsQuery()
    {
        return parent::getStudentsQuery()->whereHas('studentProfile', function ($q) {
            $q->where('attachment_status', 'seeking');
        });
    }
}