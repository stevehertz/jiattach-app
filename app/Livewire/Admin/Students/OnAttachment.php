<?php

namespace App\Livewire\Admin\Students;

class OnAttachment extends Index
{
    public function mount($statsOnly = false)
    {
        parent::mount($statsOnly);
        $this->attachmentStatusFilter = 'placed';
    }

    public function getStudentsQuery()
    {
        return parent::getStudentsQuery()->whereHas('studentProfile', function ($q) {
            $q->where('attachment_status', 'placed')
              ->where('attachment_start_date', '<=', now())
              ->where('attachment_end_date', '>=', now());
        });
    }
}