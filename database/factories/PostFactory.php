<?php

namespace Database\Factories;

use App\Models\Post;
use App\PostStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = rtrim(fake()->sentence(6), '.');

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(4)),
            'description' => fake()->sentence(20),
            'category' => fake()->randomElement(['Planning', 'Pricing', 'SEO']),
            'tags' => fake()->words(3),
            'body' => "## Introduction\n\n".fake()->paragraphs(3, true),
            'faq' => [['question' => fake()->sentence().'?', 'answer' => fake()->sentence()]],
            'status' => PostStatus::Draft,
            'published_at' => null,
        ];
    }

    /**
     * A post that is live on the website.
     */
    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }
}
