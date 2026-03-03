<?php

namespace App\Livewire\Admin\Organizations;

use Livewire\Component;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class Create extends Component
{
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
    public $is_active = true;
    public $is_verified = false;
    
    // Owner/Creator fields (for the user who will be the owner)
    public $create_new_user = true;
    public $first_name;
    public $last_name;
    public $owner_email;
    public $password;
    public $password_confirmation;
    
    // For existing user selection
    public $selected_user_id;
    public $search_user = '';
    public $search_results = [];
    public $show_user_search = false;
    
    protected function rules()
    {
        $rules = [
            // Organization rules
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'industry' => 'required|string|max:255',
            'email' => 'required|email|unique:organizations,email',
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
        
        // Add user creation/selection rules
        if ($this->create_new_user) {
            $rules['first_name'] = 'required|string|max:255';
            $rules['last_name'] = 'required|string|max:255';
            $rules['owner_email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['selected_user_id'] = 'required|exists:users,id';
        }
        
        return $rules;
    }

    protected $messages = [
        'first_name.required' => 'The owner\'s first name is required.',
        'last_name.required' => 'The owner\'s last name is required.',
        'owner_email.required' => 'The owner\'s email is required.',
        'owner_email.unique' => 'This email is already registered.',
        'password.required' => 'A password is required for the new user.',
        'password.confirmed' => 'The password confirmation does not match.',
        'selected_user_id.required' => 'Please select a user to associate with this organization.',
        'selected_user_id.exists' => 'The selected user is invalid.',
    ];

    public function updatedSearchUser()
    {
        if (strlen($this->search_user) >= 2) {
            $this->search_results = User::where(function($query) {
                $query->where('first_name', 'like', '%' . $this->search_user . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search_user . '%')
                      ->orWhere('email', 'like', '%' . $this->search_user . '%');
            })
            ->limit(10)
            ->get()
            ->toArray();
        } else {
            $this->search_results = [];
        }
    }

    public function selectUser($userId)
    {
        $this->selected_user_id = $userId;
        $this->show_user_search = false;
        $this->search_user = '';
        $this->search_results = [];
    }

    public function save()
    {
        $validatedData = $this->validate();

        DB::transaction(function () use ($validatedData) {
            // Handle user creation or selection
            if ($this->create_new_user) {
                // Create new user
                $user = User::create([
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->owner_email,
                    'password' => Hash::make($this->password),
                    'is_active' => true,
                    'is_verified' => true,
                ]);
                
                // Assign company role
                $user->assignRole('employer');
                
                $ownerId = $user->id;
            } else {
                $ownerId = $this->selected_user_id;
            }

            // Create organization
            $organization = Organization::create([
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

            // Attach the owner to the organization
            $organization->users()->attach($ownerId, [
                'role' => 'owner',
                'is_primary_contact' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // If contact person is different from owner and provided, attach as contact
            if ($this->contact_person_email && $this->contact_person_email !== ($this->owner_email ?? $this->selected_user_id)) {
                $contactUser = User::where('email', $this->contact_person_email)->first();
                if ($contactUser && !$organization->users()->where('user_id', $contactUser->id)->exists()) {
                    $organization->users()->attach($contactUser->id, [
                        'role' => 'contact',
                        'position' => $this->contact_person_position,
                        'is_primary_contact' => false,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        });

        session()->flash('success', 'Organization created successfully.');
        return redirect()->route('admin.organizations.index');
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

        return view('livewire.admin.organizations.create', [
            'industries' => $industries,
            'organizationTypes' => $organizationTypes,
            'counties' => $counties,
        ]);
    }
}