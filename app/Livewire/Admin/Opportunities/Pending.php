<?php

namespace App\Livewire\Admin\Opportunities;

class Pending extends Index
{
    public function mount(...$arguments)
    {
        parent::mount('pending');
    }

    public function getOpportunitiesQuery()
    {
        return parent::getOpportunitiesQuery()
            ->where('status', 'pending_approval');
    }
}
