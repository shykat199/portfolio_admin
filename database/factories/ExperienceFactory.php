<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role' => 'Full-Stack Developer',
            'company' => fake()->company(),
            'type' => 'Full-time',
            'start' => '2024',
            'end' => '2025',
            'summary' => fake()->sentence(),
            'highlights' => [fake()->sentence()],
            'tech' => ['Laravel', 'React'],
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 50),
        ];
    }

    /**
     * A job that is hidden from the website.
     */
    public function hidden(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
