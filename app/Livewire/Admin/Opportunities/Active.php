<?php

namespace App\Livewire\Admin\Opportunities;

class Active extends Index
{
    public function mount(...$params)
    {
        parent::mount('active');
    }

    public function getOpportunitiesQuery()
    {
        return parent::getOpportunitiesQuery()
            ->where('status', 'published')
            ->where('deadline', '>=', now())
            ->whereRaw('slots_available > COALESCE((SELECT COUNT(*) FROM placements WHERE attachment_opportunities.id = placements.attachment_opportunity_id AND placements.deleted_at IS NULL), 0)');
    }
}
