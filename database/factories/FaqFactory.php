<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Faq>
 */
class FaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => rtrim(fake()->sentence(), '.').'?',
            'answer' => fake()->paragraph(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 50),
        ];
    }

    /**
     * A question that is hidden from the website.
     */
    public function hidden(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
