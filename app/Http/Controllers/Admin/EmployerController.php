<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class EmployerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        // Add stats calculation
        $employerIds = User::role('employer')->pluck('id');

        $stats = [
            'total' => User::role('employer')->count(),
            'verified' => User::role('employer')->where('is_verified', true)->count(),
            'pending' => User::role('employer')->where('is_verified', false)->where('is_active', true)->count(),
            'inactive' => User::role('employer')->where('is_active', false)->count(),
        ];

        $query = User::role('employer')
            ->with(['organizations' => function ($q) {
                $q->withPivot(['role', 'position', 'is_primary_contact', 'is_active'])
                    ->where('organization_user.is_active', true);
            }])
            ->withCount(['organizations as active_organizations_count' => function ($q) {
                $q->where('organization_user.is_active', true);
            }]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by verification status
        if ($request->filled('verified')) {
            $query->where('users.is_verified', $request->verified === 'yes');
        }

        // Filter by active status
        if ($request->filled('active')) {
            $query->where('users.is_active', $request->active === 'yes');
        }

        // Filter by county
        if ($request->filled('county')) {
            $query->where('users.county', $request->county);
        }

        $employers = $query->latest()->paginate(15);


        // Get unique counties for filter
        $counties = User::role('employer')
            ->whereNotNull('county')
            ->distinct()
            ->pluck('county');



        return view('admin.employers.index', compact('employers', 'counties', 'stats'));
    }

    /**
     * Show verified employers only.
     */
    public function verified(Request $request)
    {
        $request->merge(['verified' => 'yes']);
        return $this->index($request);
    }

    /**
     * Show pending verification employers.
     */
    public function pending(Request $request)
    {
        $request->merge(['verified' => 'no', 'active' => 'yes']);
        return $this->index($request);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $organizations = Organization::where('is_active', true)->get();
        $counties = $this->getKenyanCounties();

        return view('admin.employers.create', compact('organizations', 'counties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'county' => 'nullable|string|max:100',
            'password' => 'required|string|min:8',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'organization_id' => 'nullable|exists:organizations,id',
            'organization_role' => 'nullable|string|in:owner,admin,member',
            'position' => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_verified'] = $request->boolean('is_verified', false);

        $employer = User::create($validated);
        $employer->assignRole(Role::findByName('employer', 'web'));

        // Attach to organization if selected
        if ($request->filled('organization_id')) {
            $employer->organizations()->attach($request->organization_id, [
                'role' => $request->organization_role ?? 'member',
                'position' => $request->position,
                'is_primary_contact' => $request->organization_role === 'owner',
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.employers.index')
            ->with('success', 'Employer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $employer)
    {
        //
        $employer->load([
            'organizations' => function ($q) {
                $q->withPivot(['role', 'position', 'is_primary_contact', 'is_active'])
                    ->withCount(['placements', 'opportunities']);
            },
            'organizations.opportunities' => function ($q) {
                $q->latest()->take(5);
            },
        ]);

        return view('admin.employers.show', compact('employer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $employer)
    {
        //
        $organizations = Organization::where('is_active', true)->get();
        $counties = $this->getKenyanCounties();

        $employer->load(['organizations' => function ($q) {
            $q->withPivot(['role', 'position', 'is_primary_contact', 'is_active']);
        }]);

        // dd($employer);

        return view('admin.employers.edit', compact('employer', 'organizations', 'counties'));
    }

    /**
     * Update the specified employer.
     */
    public function update(Request $request, User $employer)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $employer->id,
                'phone' => 'nullable|string|max:20',
                'gender' => 'nullable|in:male,female,other',
                'county' => 'nullable|string|max:100',
                'constituency' => 'nullable|string|max:100',
                'ward' => 'nullable|string|max:100',
                'date_of_birth' => 'nullable|date',
                'bio' => 'nullable|string',
                'is_active' => 'sometimes|boolean',
                'is_verified' => 'sometimes|boolean',
            ]);

            // Handle password separately - only update if provided
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'required|string|min:8',
                ]);
                $validated['password'] = Hash::make($request->password);
            }

            // Handle boolean checkboxes - they won't be present if unchecked
            $validated['is_active'] = $request->has('is_active') ? true : false;
            $validated['is_verified'] = $request->has('is_verified') ? true : false;

            // Update the employer
            $employer->update($validated);

            // Ensure the employer role is maintained (CRITICAL FIX)
            if (!$employer->hasRole('employer')) {
                $employer->assignRole(Role::findByName('employer', 'web'));
            }

            // Log the activity
            activity_log(
                "Employer {$employer->full_name} updated by " . auth()->user()->full_name,
                'updated',
                [
                    'employer_id' => $employer->id,
                    'updated_fields' => array_keys($validated),
                ],
                'employer'
            );

            return redirect()
                ->route('admin.employers.show', $employer)
                ->with('success', 'Employer updated successfully.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error updating employer: ' . $e->getMessage(), [
                'employer_id' => $employer->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update employer. Please try again.');
        }
    }

    /**
     * Toggle employer active status.
     */
    public function toggleActive(User $employer)
    {
        $employer->update(['is_active' => !$employer->is_active]);

        $status = $employer->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Employer {$status} successfully.");
    }

    /**
     * Verify employer.
     */
    public function verify(User $employer)
    {
        $employer->update([
            'is_verified' => true,
            'email_verified_at' => $employer->email_verified_at ?? now(),
        ]);

        return back()->with('success', 'Employer verified successfully.');
    }


    /**
     * Remove the specified employer.
     */
    public function destroy(User $employer)
    {
        try {
            // Store info for logging before deletion
            $employerName = $employer->full_name;
            $employerId = $employer->id;

            // Remove organization associations softly
            $employer->organizations()->detach();

            // Remove employer role
            $employer->removeRole('employer');

            // Delete the user
            $employer->delete();

            // Log the activity
            activity_log(
                "Employer {$employerName} deleted by " . auth()->user()->full_name,
                'deleted',
                ['employer_id' => $employerId],
                'employer'
            );

            return redirect()
                ->route('admin.employers.index')
                ->with('success', 'Employer deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting employer: ' . $e->getMessage(), [
                'employer_id' => $employer->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to delete employer. Please try again.');
        }
    }

    /**
     * Get list of Kenyan counties.
     */
    private function getKenyanCounties(): array
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
}
