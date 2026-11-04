<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = rtrim(fake()->sentence(4), '.');

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(4)),
            'is_demo' => false,
            'category' => fake()->randomElement(['Clinic', 'Salon', 'Trades']),
            'year' => '2026',
            'problem' => fake()->sentence(),
            'solution' => fake()->sentence(),
            'result' => fake()->sentence(),
            'overview' => fake()->paragraph(),
            'client_problem' => fake()->paragraph(),
            'built' => fake()->sentences(3),
            'tech' => ['Laravel', 'React'],
            'results' => fake()->sentences(2),
            'cover_path' => 'work/cover.webp',
            'cover_alt' => 'Project cover',
            'screenshots' => [],
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 50),
        ];
    }

    /**
     * A project that is hidden from the website.
     */
    public function hidden(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
