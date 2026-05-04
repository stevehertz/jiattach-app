<?php

namespace App\Livewire\Employer\Organization;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Edit extends Component
{
    public $organization;
    public $organization_id;

    // Basic Information
    public $name = '';
    public $type = '';
    public $industry = '';
    public $description = '';

    // Contact Information
    public $email = '';
    public $phone = '';
    public $website = '';

    // Location
    public $address = '';
    public $county = '';
    public $constituency = '';
    public $ward = '';

    // Contact Person
    public $contact_person_name = '';
    public $contact_person_email = '';
    public $contact_person_phone = '';
    public $contact_person_position = '';

    // Settings
    public $max_students_per_intake = 10;
    public $departments = [];

    // New department input
    public $newDepartmentName = '';
    public $newDepartmentDescription = '';
    public $newDepartmentHead = '';

    // Industry options
    public $industryOptions = [];
    public $organizationTypes = [];

    // Success message
    public $successMessage = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'industry' => 'required|string|max:100',
            'description' => 'nullable|string|max:2000',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'county' => 'required|string|max:100',
            'constituency' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',
            'contact_person_name' => 'required|string|max:255',
            'contact_person_email' => 'required|email|max:255',
            'contact_person_phone' => 'required|string|max:20',
            'contact_person_position' => 'required|string|max:255',
            'max_students_per_intake' => 'required|integer|min:1|max:500',
            'departments' => 'nullable|array',
            'departments.*.name' => 'required|string|max:255',
            'departments.*.description' => 'nullable|string|max:500',
            'departments.*.head' => 'nullable|string|max:255',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Organization name is required.',
            'email.required' => 'Organization email is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Phone number is required.',
            'county.required' => 'Please select a county.',
            'contact_person_name.required' => 'Contact person name is required.',
            'max_students_per_intake.required' => 'Please specify the maximum number of students per intake.',
            'max_students_per_intake.min' => 'Must be able to host at least 1 student.',
        ];
    }

    public function mount()
    {
        $user = User::findOrFail(auth()->id());
        $this->organization = $user->primaryOrganization();

        if (!$this->organization) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'No organization found. Please contact an administrator.');
        }

        // Check if user has permission to edit
        if (!$this->organization->isOwner($user) && !$this->organization->isAdmin($user)) {
            return redirect()->route('employer.organization.profile')
                ->with('error', 'You do not have permission to edit this organization.');
        }

        $this->loadOrganizationData();
        $this->loadOptions();
    }

    /**
     * Load existing organization data
     */
    protected function loadOrganizationData()
    {
        $this->organization_id = $this->organization->id;
        $this->name = $this->organization->name;
        $this->type = $this->organization->type;
        $this->industry = $this->organization->industry;
        $this->description = $this->organization->description;
        $this->email = $this->organization->email;
        $this->phone = $this->organization->phone;
        $this->website = $this->organization->website;
        $this->address = $this->organization->address;
        $this->county = $this->organization->county;
        $this->constituency = $this->organization->constituency;
        $this->ward = $this->organization->ward;
        $this->contact_person_name = $this->organization->contact_person_name;
        $this->contact_person_email = $this->organization->contact_person_email;
        $this->contact_person_phone = $this->organization->contact_person_phone;
        $this->contact_person_position = $this->organization->contact_person_position;
        $this->max_students_per_intake = $this->organization->max_students_per_intake;

        // Load departments
        $this->departments = $this->normalizeDepartments($this->organization->departments ?? []);
    }

    /**
     * Normalize departments to consistent array structure
     */
    protected function normalizeDepartments($departments)
    {
        return collect($departments)
            ->map(function ($dept) {

                // Already correct structure
                if (is_array($dept)) {
                    return [
                        'name' => $dept['name'] ?? '',
                        'description' => $dept['description'] ?? null,
                        'head' => $dept['head'] ?? null,
                    ];
                }

                // If it's a string (old data)
                if (is_string($dept)) {
                    return [
                        'name' => $dept,
                        'description' => null,
                        'head' => null,
                    ];
                }

                // Fallback (just in case)
                return [
                    'name' => '',
                    'description' => null,
                    'head' => null,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Load dropdown options
     */
    protected function loadOptions()
    {
        $this->industryOptions = [
            'Technology & IT',
            'Finance & Banking',
            'Healthcare & Medical',
            'Education & Training',
            'Manufacturing',
            'Construction & Engineering',
            'Agriculture & Agribusiness',
            'Transport & Logistics',
            'Hospitality & Tourism',
            'Media & Communications',
            'Legal Services',
            'Consulting',
            'Retail & Wholesale',
            'Energy & Utilities',
            'Real Estate',
            'Insurance',
            'NGO & Non-Profit',
            'Government & Public Sector',
            'Entertainment & Arts',
            'Other',
        ];

        $this->organizationTypes = [
            'Private Company',
            'Public Company',
            'Government Agency',
            'NGO',
            'Startup',
            'SME',
            'Multinational Corporation',
            'Educational Institution',
            'Healthcare Facility',
            'Research Institution',
            'Other',
        ];
    }

    /**
     * Real-time validation on specific fields
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Add a new department
     */
    public function addDepartment()
    {
        $this->validate([
            'newDepartmentName' => 'required|string|max:255',
            'newDepartmentDescription' => 'nullable|string|max:500',
            'newDepartmentHead' => 'nullable|string|max:255',
        ], [
            'newDepartmentName.required' => 'Department name is required.',
        ]);

        $this->departments[] = [
            'name' => $this->newDepartmentName,
            'description' => $this->newDepartmentDescription,
            'head' => $this->newDepartmentHead,
        ];

        // Reset input fields
        $this->newDepartmentName = '';
        $this->newDepartmentDescription = '';
        $this->newDepartmentHead = '';

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Department added successfully. Save changes to apply.',
        ]);
    }

    /**
     * Remove a department
     */
    public function removeDepartment($index)
    {
        unset($this->departments[$index]);
        $this->departments = array_values($this->departments); // Re-index array

        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Department removed. Save changes to apply.',
        ]);
    }

    /**
     * Update organization
     */
    public function update()
    {
        // Validate all fields
        $validatedData = $this->validate();

        try {
            // Begin update
            $this->organization->update([
                'name' => $this->name,
                'type' => $this->type,
                'industry' => $this->industry,
                'description' => $this->description,
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
                'max_students_per_intake' => $this->max_students_per_intake,
                'departments' => $this->departments,
            ]);

            // Log activity
            activity_log(
                "Organization '{$this->organization->name}' updated by " . auth()->user()->full_name,
                'updated',
                [
                    'organization_id' => $this->organization->id,
                    'updated_by' => auth()->id(),
                ],
                'organization'
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Organization profile updated successfully!',
            ]);

            // Optional: Redirect to profile after short delay
            $this->redirect(route('employer.organization.profile'), navigate: true);
        } catch (\Exception $e) {
            Log::error('Error updating organization:', [
                'error' => $e->getMessage(),
                'organization_id' => $this->organization->id,
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'An error occurred while updating: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get Kenyan counties list
     */
    public function getCountiesProperty()
    {
        return [
            'Baringo',
            'Bomet',
            'Bungoma',
            'Busia',
            'Elgeyo Marakwet',
            'Embu',
            'Garissa',
            'Homa Bay',
            'Isiolo',
            'Kajiado',
            'Kakamega',
            'Kericho',
            'Kiambu',
            'Kilifi',
            'Kirinyaga',
            'Kisii',
            'Kisumu',
            'Kitui',
            'Kwale',
            'Laikipia',
            'Lamu',
            'Machakos',
            'Makueni',
            'Mandera',
            'Marsabit',
            'Meru',
            'Migori',
            'Mombasa',
            'Murang\'a',
            'Nairobi',
            'Nakuru',
            'Nandi',
            'Narok',
            'Nyamira',
            'Nyandarua',
            'Nyeri',
            'Samburu',
            'Siaya',
            'Taita Taveta',
            'Tana River',
            'Tharaka Nithi',
            'Trans Nzoia',
            'Turkana',
            'Uasin Gishu',
            'Vihiga',
            'Wajir',
            'West Pokot',
        ];
    }

    public function render()
    {
        return view('livewire.employer.organization.edit', [
            'counties' => $this->counties,
            'industryOptions' => $this->industryOptions,
            'organizationTypes' => $this->organizationTypes,
        ]);
    }
}
