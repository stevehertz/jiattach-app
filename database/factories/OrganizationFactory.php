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

        $industries = [
            'Technology',
            'Construction',
            'Finance',
            'Healthcare',
            'Education',
            'Agriculture',
            'Manufacturing',
            'Telecommunications',
            'Hospitality',
            'Energy',
            'Transport',
            'Real Estate'
        ];

        return [
            'name' => $name,
            'type' => $this->faker->randomElement(['company', 'non-profit', 'government', 'institution']),
            'industry' => $this->faker->randomElement($industries),
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'website' => $this->faker->url(),
            'address' => $this->faker->streetAddress(),
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
            'contact_person_name' => $this->faker->name(),
            'contact_person_email' => $this->faker->email(),
            'contact_person_phone' => $this->faker->phoneNumber(),
            'contact_person_position' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraphs(3, true),
            'departments' => $this->faker->randomElements([
                'IT',
                'Human Resources',
                'Finance',
                'Marketing',
                'Operations',
                'Research & Development',
                'Customer Support',
                'Sales',
                'Engineering',
                'Construction',
                'Project Management'
            ], rand(3, 6)),
            'max_students_per_intake' => rand(5, 20),
            'is_active' => true,
            'is_verified' => $this->faker->boolean(90),
            'verified_at' => $this->faker->boolean(90) ? now() : null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_verified' => false,
            'verified_at' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function byIndustry(string $industry): static
    {
        return $this->state(fn(array $attributes) => [
            'industry' => $industry,
        ]);
    }
}
