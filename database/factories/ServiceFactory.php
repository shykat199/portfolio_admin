<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::title(fake()->words(2, true));

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'for_who' => fake()->sentence(),
            'price' => fake()->numberBetween(100, 3000),
            'time_label' => 'Delivery',
            'time' => '2 to 3 weeks',
            'includes' => fake()->sentences(3),
            'heading' => fake()->sentence(5),
            'intro' => fake()->sentence(18),
            'story' => fake()->paragraph()."\n\n".fake()->paragraph(),
            'ideal_for' => fake()->sentences(2),
            'faq' => [['question' => fake()->sentence().'?', 'answer' => fake()->sentence()]],
            'related_projects' => [],
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 50),
        ];
    }

    /**
     * A service that is hidden from the website.
     */
    public function hidden(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
