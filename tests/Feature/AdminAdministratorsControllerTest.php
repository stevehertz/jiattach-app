<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AdministratorsController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAdministratorsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles used by controller filters
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'moderator']);
    }

    /** @test */
    public function index_route_renders_index_view()
    {
        $response = $this->get(route('admin.administrators.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.administrators.index');
    }

    /** @test */
    public function create_route_renders_create_view()
    {
        $response = $this->get(route('admin.administrators.create'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.administrators.create');
    }

    /** @test */
    public function show_returns_404_for_non_admin_roles()
    {
        $user = User::factory()->create();
        // assign a role that is not allowed
        $user->assignRole(Role::create(['name' => 'student']));

        $response = $this->get(route('admin.administrators.show', $user->id));
        $response->assertStatus(404);
    }

    /** @test */
    public function show_renders_view_for_admin_role_and_loads_relations()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->get(route('admin.administrators.show', $user->id));
        $response->assertStatus(200);
        $response->assertViewIs('admin.administrators.show');
        $response->assertViewHas('administrator', function ($admin) use ($user) {
            $this->assertTrue($admin->relationLoaded('roles'));
            $this->assertTrue($admin->relationLoaded('permissions'));
            return $admin->id === $user->id;
        });
    }

    /** @test */
    public function edit_renders_view_for_super_admin_and_moderator_roles()
    {
        $super = User::factory()->create();
        $super->assignRole('super-admin');
        $mod = User::factory()->create();
        $mod->assignRole('moderator');

        $this->get(route('admin.administrators.edit', $super->id))
            ->assertStatus(200)
            ->assertViewIs('admin.administrators.edit')
            ->assertViewHas('administrator', function ($admin) use ($super) {
                return $admin->id === $super->id;
            });

        $this->get(route('admin.administrators.edit', $mod->id))
            ->assertStatus(200)
            ->assertViewIs('admin.administrators.edit')
            ->assertViewHas('administrator', function ($admin) use ($mod) {
                return $admin->id === $mod->id;
            });
    }

    /** @test */
    public function edit_returns_404_for_users_without_required_roles()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'student']));

        $this->get(route('admin.administrators.edit', $user->id))
            ->assertStatus(404);
    }
}
