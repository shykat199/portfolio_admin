<?php

namespace Database\Factories;

use App\Models\TechItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TechItem>
 */
class TechItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word().' '.fake()->unique()->numberBetween(1, 9999),
            'logo_path' => null,
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 50),
        ];
    }

    /**
     * An item that is hidden from the website.
     */
    public function hidden(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
