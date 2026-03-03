<?php

namespace App\Livewire\Admin\Organizations;

use Livewire\Component;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class Edit extends Component
{
    public Organization $organization;
    
    // Organization fields (based on your Organization model)
    public $name;
    public $type;
    public $industry;
    public $email;
    public $phone;
    public $website;
    public $address;
    public $county;
    public $constituency;
    public $ward;
    public $contact_person_name;
    public $contact_person_email;
    public $contact_person_phone;
    public $contact_person_position;
    public $description;
    public $max_students_per_intake;
    public $is_active;
    public $is_verified;
    
    // User management
    public $selectedUsers = [];
    public $availableUsers = [];
    public $showUserSearch = false;
    public $userSearch = '';

    protected function rules()
    {
        return [
            // Organization rules based on your fillable fields
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'industry' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('organizations')->ignore($this->organization->id)],
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'county' => 'nullable|string|max:255',
            'constituency' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_email' => 'nullable|email|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'contact_person_position' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'max_students_per_intake' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function mount(Organization $organization)
    {
        $this->organization = $organization->load('users');
        
        // Load all organization fields
        $this->name = $organization->name;
        $this->type = $organization->type;
        $this->industry = $organization->industry;
        $this->email = $organization->email;
        $this->phone = $organization->phone;
        $this->website = $organization->website;
        $this->address = $organization->address;
        $this->county = $organization->county;
        $this->constituency = $organization->constituency;
        $this->ward = $organization->ward;
        $this->contact_person_name = $organization->contact_person_name;
        $this->contact_person_email = $organization->contact_person_email;
        $this->contact_person_phone = $organization->contact_person_phone;
        $this->contact_person_position = $organization->contact_person_position;
        $this->description = $organization->description;
        $this->max_students_per_intake = $organization->max_students_per_intake;
        $this->is_active = $organization->is_active;
        $this->is_verified = $organization->is_verified;
        
        // Load existing users
        $this->selectedUsers = $organization->users->pluck('id')->toArray();
    }

    public function updatedUserSearch()
    {
        if (strlen($this->userSearch) >= 2) {
            $this->availableUsers = User::where(function($query) {
                $query->where('first_name', 'like', '%' . $this->userSearch . '%')
                      ->orWhere('last_name', 'like', '%' . $this->userSearch . '%')
                      ->orWhere('email', 'like', '%' . $this->userSearch . '%');
            })
            ->whereNotIn('id', $this->selectedUsers)
            ->limit(10)
            ->get()
            ->toArray();
        } else {
            $this->availableUsers = [];
        }
    }

    public function addUser($userId)
    {
        $user = User::find($userId);
        if ($user && !in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers[] = $userId;
            
            // Attach user with default role
            $this->organization->users()->attach($userId, [
                'role' => 'member',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $this->userSearch = '';
            $this->availableUsers = [];
            $this->showUserSearch = false;
            
            $this->dispatch('toastr:success', message: 'User added successfully.');
        }
    }

    public function removeUser($userId)
    {
        // Check if this is the last owner
        $userRole = $this->organization->users()
            ->wherePivot('user_id', $userId)
            ->first()?->pivot->role;
            
        if ($userRole === 'owner') {
            $ownerCount = $this->organization->users()
                ->wherePivot('role', 'owner')
                ->count();
                
            if ($ownerCount <= 1) {
                $this->dispatch('toastr:error', message: 'Cannot remove the last owner.');
                return;
            }
        }
        
        $this->selectedUsers = array_diff($this->selectedUsers, [$userId]);
        $this->organization->users()->detach($userId);
        
        $this->dispatch('toastr:success', message: 'User removed successfully.');
    }

    public function updateUserRole($userId, $role)
    {
        $this->organization->users()->updateExistingPivot($userId, [
            'role' => $role,
            'updated_at' => now()
        ]);
        
        $this->dispatch('toastr:success', message: 'User role updated.');
    }

    public function update()
    {
        $validatedData = $this->validate();

        DB::transaction(function () use ($validatedData) {
            // Update organization with all fields
            $this->organization->update([
                'name' => $this->name,
                'type' => $this->type,
                'industry' => $this->industry,
                'email' => $this->email,
                'phone' => $this->phone,
                'website' => $this->website,
                'address' => $this->address,
                'county' => $this->county,
                'constituency' => $this->constituency,
                'ward' => $this->ward,
                'contact_person_name' => $this->contact_person_name,
                'contact_person_email' => $this->contact_person_email,
                'contact_person_phone' => $this->contact_person_phone,
                'contact_person_position' => $this->contact_person_position,
                'description' => $this->description,
                'max_students_per_intake' => $this->max_students_per_intake,
                'is_active' => $this->is_active,
                'is_verified' => $this->is_verified,
                'verified_at' => $this->is_verified ? now() : null,
            ]);
        });

        session()->flash('success', 'Organization updated successfully.');
        return redirect()->route('admin.organizations.show', $this->organization->id);
    }

    public function render()
    {
        $industries = [
            'Technology' => 'Technology',
            'Finance' => 'Finance',
            'Banking' => 'Banking',
            'Insurance' => 'Insurance',
            'Health' => 'Health',
            'Healthcare' => 'Healthcare',
            'Education' => 'Education',
            'Agriculture' => 'Agriculture',
            'Manufacturing' => 'Manufacturing',
            'Construction' => 'Construction',
            'Real Estate' => 'Real Estate',
            'Hospitality' => 'Hospitality',
            'Tourism' => 'Tourism',
            'Transport' => 'Transport',
            'Logistics' => 'Logistics',
            'Retail' => 'Retail',
            'Wholesale' => 'Wholesale',
            'Energy' => 'Energy',
            'Mining' => 'Mining',
            'Telecommunications' => 'Telecommunications',
            'Media' => 'Media',
            'Entertainment' => 'Entertainment',
            'Non-profit' => 'Non-profit',
            'Government' => 'Government',
            'Other' => 'Other'
        ];

        $organizationTypes = [
            'private' => 'Private Company',
            'public' => 'Public Company',
            'non_profit' => 'Non-Profit Organization',
            'government' => 'Government Agency',
            'educational' => 'Educational Institution',
            'healthcare' => 'Healthcare Facility',
            'startup' => 'Startup',
            'sme' => 'Small/Medium Enterprise',
            'corporation' => 'Corporation',
            'partnership' => 'Partnership',
            'sole_proprietorship' => 'Sole Proprietorship'
        ];

        $counties = [
            'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Kiambu', 'Machakos',
            'Uasin Gishu', 'Kericho', 'Kakamega', 'Kilifi', 'Kwale', 'Lamu',
            'Taita Taveta', 'Garissa', 'Wajir', 'Mandera', 'Marsabit', 'Isiolo',
            'Meru', 'Tharaka Nithi', 'Embu', 'Kitui', 'Makueni', 'Nyandarua',
            'Nyeri', 'Kirinyaga', 'Muranga', 'Nyamira', 'Kisii', 'Homa Bay',
            'Migori', 'Siaya', 'Vihiga', 'Bungoma', 'Busia', 'Trans Nzoia',
            'Elgeyo Marakwet', 'Nandi', 'Baringo', 'Laikipia', 'Samburu',
            'Turkana', 'West Pokot', 'Kajiado', 'Narok', 'Bomet'
        ];

        $existingUsers = $this->organization->users()
            ->withPivot(['role', 'position', 'is_primary_contact', 'is_active'])
            ->get();

        return view('livewire.admin.organizations.edit', [
            'industries' => $industries,
            'organizationTypes' => $organizationTypes,
            'counties' => $counties,
            'existingUsers' => $existingUsers
        ]);
    }
}