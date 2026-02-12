<?php

namespace App\Livewire\Admin\Organizations;

use Livewire\Component;
use App\Models\Organization;

class Show extends Component
{
    public Organization $organization;

    public function mount(Organization $organization)
    {
        $this->organization = $organization->load('user', 'opportunities', 'placements');
    }

    public function render()
    {
        return view('livewire.admin.organizations.show');
    }
}
