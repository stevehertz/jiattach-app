<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $disabilityStatuses = ['none', 'mobility', 'visual', 'hearing', 'cognitive', 'other', 'prefer_not_to_say'];
        $disabilityStatus = $this->faker->randomElement($disabilityStatuses);

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => $this->faker->phoneNumber(),
            'national_id' => $this->faker->unique()->numerify('########'),
            'date_of_birth' => $this->faker->dateTimeBetween('-30 years', '-20 years'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'county' => $this->faker->randomElement([
                'Nairobi',
                'Mombasa',
                'Kisumu',
                'Nakuru',
                'Kiambu',
                'Machakos',
                'Uasin Gishu',
                'Kilifi',
                'Meru',
                'Kakamega'
            ]),
            'constituency' => $this->faker->streetName(),
            'ward' => $this->faker->streetName(),
            'bio' => $this->faker->paragraph(),
            'disability_status' => $disabilityStatus,
            'disability_details' => $disabilityStatus !== 'none' && $disabilityStatus !== 'prefer_not_to_say'
                ? $this->faker->sentence()
                : null,
            'is_active' => true,
            'is_verified' => true,
            'verification_token' => null,
            'last_login_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
                'is_verified' => false,
            ];
        });
    }

    public function withDisability($type = null): static
    {
        return $this->state(function (array $attributes) use ($type) {
            $disabilityType = $type ?? $this->faker->randomElement(['mobility', 'visual', 'hearing', 'cognitive', 'other']);
            return [
                'disability_status' => $disabilityType,
                'disability_details' => $this->faker->sentence(),
            ];
        });
    }
}
