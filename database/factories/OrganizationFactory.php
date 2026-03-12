<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Organization::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            //
            'name' => $name,
            'type' => fake()->randomElement(['company', 'non-profit', 'government', 'institution']),
            'industry' => fake()->randomElement([
                'Technology',
                'Finance',
                'Healthcare',
                'Education',
                'Agriculture',
                'Manufacturing',
                'Telecommunications',
                'Energy'
            ]),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'address' => fake()->streetAddress(),
            'county' => fake()->randomElement([
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
            'constituency' => fake()->streetName(),
            'ward' => fake()->streetName(),
            'contact_person_name' => fake()->name(),
            'contact_person_email' => fake()->email(),
            'contact_person_phone' => fake()->phoneNumber(),
            'contact_person_position' => fake()->jobTitle(),
            'description' => fake()->paragraph(3),
            'departments' => json_encode(fake()->randomElements([
                'IT',
                'Human Resources',
                'Finance',
                'Marketing',
                'Operations',
                'Research & Development',
                'Customer Support'
            ], rand(2, 4))),
            'max_students_per_intake' => rand(3, 10),
            'is_active' => true,
            'is_verified' => fake()->boolean(80), // 80% verified
            'verified_at' => fake()->boolean(80) ? now() : null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
            'verified_at' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
