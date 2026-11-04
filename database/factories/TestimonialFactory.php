<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quote' => fake()->paragraph(),
            'name' => fake()->name(),
            'role' => 'Owner, '.fake()->company(),
            'rating' => null,
            'photo_path' => null,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 50),
        ];
    }

    /**
     * A testimonial that is hidden from the website.
     */
    public function hidden(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
