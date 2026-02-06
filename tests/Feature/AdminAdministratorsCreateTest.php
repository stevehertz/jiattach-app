<?php

namespace Tests\Feature;

use App\Livewire\Admin\Administrators\Create;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminAdministratorsCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure roles table has the expected roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'moderator']);
    }

    protected function validState(array $overrides = []): array
    {
        $password = 'Password123!';
        $base = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+254700000000',
            'password' => $password,
            'password_confirmation' => $password,
            'national_id' => '12345678',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'county' => 'Nairobi',
            'constituency' => 'Westlands',
            'ward' => 'Parklands',
            'bio' => 'Experienced admin',
            'roles' => ['admin'],
            'is_active' => true,
            'send_welcome_email' => false,
            'force_password_change' => true,
            'currentStep' => 3,
        ];

        return array_merge($base, $overrides);
    }

    /** @test */
    public function it_loads_available_roles_on_mount()
    {
        Livewire::test(Create::class)
            ->assertSet('availableRoles', function ($roles) {
                $names = collect($roles)->pluck('name')->all();
                sort($names);
                return $names === ['admin', 'moderator', 'super-admin'];
            });
    }

    /** @test */
    public function it_generates_and_confirms_password_and_sets_force_change_flag()
    {
        Livewire::test(Create::class)
            ->call('generatePassword')
            ->assertSet('force_password_change', true)
            ->assertSet('password', function ($value, $component) {
                return is_string($value) && strlen($value) === 12 && $component->password_confirmation === $value;
            });
    }

    /** @test */
    public function it_validates_each_step_rules_correctly()
    {
        // Step 1 validation errors
        Livewire::test(Create::class)
            ->set('currentStep', 1)
            ->call('validateCurrentStep')
            ->assertHasErrors([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'national_id' => 'required',
                'date_of_birth' => 'required',
                'gender' => 'required',
            ]);

        // Step 2 validation passes with valid data
        Livewire::test(Create::class)
            ->set('currentStep', 2)
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->set('county', 'Nairobi')
            ->call('validateCurrentStep')
            ->assertHasNoErrors();

        // Step 3 validation requires roles
        Livewire::test(Create::class)
            ->set('currentStep', 3)
            ->call('validateCurrentStep')
            ->assertHasErrors(['roles' => 'required']);
    }

    /** @test */
    public function it_creates_user_assigns_roles_and_marks_email_verified_on_save()
    {
        Livewire::test(Create::class)
            ->set('first_name', 'Jane')
            ->set('last_name', 'Doe')
            ->set('email', 'jane.doe@example.com')
            ->set('phone', '+254711111111')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->set('national_id', '87654321')
            ->set('date_of_birth', '1991-02-02')
            ->set('gender', 'female')
            ->set('county', 'Nairobi')
            ->set('constituency', 'Langata')
            ->set('ward', 'Karen')
            ->set('bio', 'Another admin')
            ->set('roles', ['admin'])
            ->set('is_active', true)
            ->call('save')
            ->assertDispatched('notify', function ($event, $params) {
                return $params['type'] === 'success';
            });

        $user = User::where('email', 'jane.doe@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('admin'));
        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals('admin', $user->user_type);
        $this->assertTrue($user->is_active);
    }

    /** @test */
    public function it_prevents_duplicate_email_and_national_id()
    {
        // Existing user
        $existing = User::factory()->create([
            'email' => 'john.doe@example.com',
            'national_id' => '12345678',
        ]);

        // Attempt to create with duplicates
        Livewire::test(Create::class)
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', $existing->email)
            ->set('phone', '+254700000000')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->set('national_id', $existing->national_id)
            ->set('date_of_birth', '1990-01-01')
            ->set('gender', 'male')
            ->set('county', 'Nairobi')
            ->set('roles', ['admin'])
            ->call('save')
            ->assertHasErrors(['email' => 'unique', 'national_id' => 'unique']);
    }
}
