<?php

namespace Tests\Unit\Models;

use App\Models\Organization;
use App\Models\Placement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function casts_and_fillable_are_configured_as_expected()
    {
        $org = new Organization([
            'name' => 'Acme Inc',
            'type' => 'Private',
            'industry' => 'Tech',
            'email' => 'info@acme.test',
            'phone' => '123456789',
            'website' => 'https://acme.test',
            'address' => '123 Street',
            'county' => 'Nairobi',
            'constituency' => 'West',
            'ward' => 'Ward 1',
            'contact_person_name' => 'Jane Doe',
            'contact_person_email' => 'jane@acme.test',
            'contact_person_phone' => '987654321',
            'contact_person_position' => 'HR',
            'description' => 'Great place',
            'departments' => ['IT', 'HR'],
            'max_students_per_intake' => 10,
            'is_active' => true,
            'is_verified' => false,
            'verified_at' => now(),
        ]);

        $this->assertIsArray($org->departments);
        $this->assertIsBool($org->is_active);
        $this->assertIsBool($org->is_verified);
        $this->assertInstanceOf(\Carbon\Carbon::class, $org->verified_at);
    }

    /** @test */
    public function relationships_scopes_and_helpers_exist()
    {
        $org = new Organization();

        // relationships
        $this->assertTrue(method_exists($org, 'users'));
        $this->assertTrue(method_exists($org, 'owners'));
        $this->assertTrue(method_exists($org, 'primaryContacts'));
        $this->assertTrue(method_exists($org, 'admins'));
        $this->assertTrue(method_exists($org, 'placements'));
        $this->assertTrue(method_exists($org, 'opportunities'));
        $this->assertTrue(method_exists($org, 'applications'));

        // scopes
        $this->assertTrue(method_exists($org, 'scopeVerified'));
        $this->assertTrue(method_exists($org, 'scopeActive'));
        $this->assertTrue(method_exists($org, 'scopeByIndustry'));
        $this->assertTrue(method_exists($org, 'scopeByCounty'));

        // helpers/accessors
        $this->assertTrue(method_exists($org, 'getAvailableSlotsAttribute'));
        $this->assertTrue(method_exists($org, 'hasCapacity'));
        $this->assertTrue(method_exists($org, 'hasUser'));
        $this->assertTrue(method_exists($org, 'isOwner'));
        $this->assertTrue(method_exists($org, 'isAdmin'));
    }

    /**
     * Behavior 1: available_slots subtracts active placed placements and never goes below zero.
     * @test
     */
    public function available_slots_computation_and_flooring()
    {
        $org = Organization::create([
            'name' => 'Acme Inc',
            'max_students_per_intake' => 2,
            'is_active' => true,
            'is_verified' => true,
        ]);

        // No placements => 2 available
        $this->assertSame(2, $org->available_slots);

        // Create a non-placed placement (should not reduce slots)
        Placement::create([
            'organization_id' => $org->id,
            'status' => 'pending',
        ]);
        $org->refresh();
        $this->assertSame(2, $org->available_slots);

        // Create placed placements that count against capacity
        Placement::create([
            'organization_id' => $org->id,
            'status' => 'placed',
        ]);
        Placement::create([
            'organization_id' => $org->id,
            'status' => 'placed',
        ]);
        $org->refresh();
        $this->assertSame(0, $org->available_slots);

        // Add another placed beyond capacity -> still 0
        Placement::create([
            'organization_id' => $org->id,
            'status' => 'placed',
        ]);
        $org->refresh();
        $this->assertSame(0, $org->available_slots);
    }

    /**
     * Behavior 2: hasCapacity reflects available_slots > 0
     * @test
     */
    public function has_capacity_uses_available_slots()
    {
        $org = Organization::create([
            'name' => 'Acme Inc',
            'max_students_per_intake' => 1,
            'is_active' => true,
            'is_verified' => true,
        ]);

        $this->assertTrue($org->hasCapacity());

        Placement::create([
            'organization_id' => $org->id,
            'status' => 'placed',
        ]);

        $org->refresh();
        $this->assertFalse($org->hasCapacity());
    }

    /**
     * Behavior 3: scopes filter as expected.
     * @test
     */
    public function scopes_verified_active_and_filters()
    {
        Organization::create(['name' => 'A', 'industry' => 'Tech', 'county' => 'Nairobi', 'is_active' => true, 'is_verified' => true]);
        Organization::create(['name' => 'B', 'industry' => 'Finance', 'county' => 'Mombasa', 'is_active' => false, 'is_verified' => true]);
        Organization::create(['name' => 'C', 'industry' => 'Tech', 'county' => 'Nairobi', 'is_active' => true, 'is_verified' => false]);

        $this->assertCount(1, Organization::query()->verified()->get());
        $this->assertCount(2, Organization::query()->active()->get());
        $this->assertCount(2, Organization::query()->byIndustry('Tech')->get());
        $this->assertCount(2, Organization::query()->byCounty('Nairobi')->get());
    }

    /**
     * Behavior 4: users relationship filters to active pivot rows and supports role-based subsets.
     * @test
     */
    public function users_relationship_filters_active_and_role_helpers()
    {
        $org = Organization::create(['name' => 'Acme', 'is_active' => true, 'is_verified' => true]);
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $inactive = User::factory()->create();

        $org->users()->attach($owner->id, ['role' => 'owner', 'position' => 'CEO', 'is_primary_contact' => true, 'is_active' => true]);
        $org->users()->attach($admin->id, ['role' => 'admin', 'position' => 'Ops', 'is_primary_contact' => false, 'is_active' => true]);
        $org->users()->attach($inactive->id, ['role' => 'admin', 'position' => 'Ops', 'is_primary_contact' => false, 'is_active' => false]);

        // Base users only returns active pivot entries
        $this->assertCount(2, $org->users()->get());

        // Helpers
        $this->assertTrue($org->hasUser($owner));
        $this->assertTrue($org->isOwner($owner));
        $this->assertFalse($org->isOwner($admin));

        $this->assertTrue($org->isAdmin($admin));
        $this->assertFalse($org->hasUser($inactive)); // filtered due to inactive pivot

        $this->assertCount(1, $org->owners()->get());
        $this->assertCount(1, $org->admins()->get());
        $this->assertCount(1, $org->primaryContacts()->get());
    }

    /**
     * Behavior 5: applications/opportunities/placements relationships exist and are usable.
     * Minimal smoke by creating records with only required keys used by relationships.
     * @test
     */
    public function relationships_smoke_usable()
    {
        $org = Organization::create(['name' => 'Acme', 'is_active' => true, 'is_verified' => true]);

        // Ensure calling relations does not error and returns relations builder/collection
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $org->placements());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $org->opportunities());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $org->applications());
    }
}
