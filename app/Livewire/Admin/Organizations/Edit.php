<?php

namespace App\Livewire\Admin\Organizations;

use Livewire\Component;
use App\Models\Organization;

class Edit extends Component
{
     public Organization $organization;
    public $name, $email, $phone, $industry, $county, $website, $is_verified;

    public function mount(Organization $organization)
    {
        $this->organization = $organization;
        $this->name = $organization->name;
        $this->email = $organization->email;
        $this->phone = $organization->phone;
        $this->industry = $organization->industry;
        $this->county = $organization->county;
        $this->website = $organization->website;
        $this->is_verified = $organization->is_verified;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:organizations,email,' . $this->organization->id,
            'industry' => 'required',
            'phone' => 'required',
        ]);

        $this->organization->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'industry' => $this->industry,
            'county' => $this->county,
            'website' => $this->website,
            'is_verified' => $this->is_verified,
            'verified_at' => $this->is_verified ? now() : null,
        ]);

        session()->flash('success', 'Organization updated successfully.');
        return redirect()->route('admin.organizations.index');
    }

    public function render()
    {
        return view('livewire.admin.organizations.edit');
    }
}
