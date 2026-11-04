<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'business' => fake()->company(),
            'website' => fake()->domainName(),
            'message' => fake()->paragraph(),
            'read_at' => null,
        ];
    }

    /**
     * A message that has been opened.
     */
    public function read(): static
    {
        return $this->state(fn (): array => ['read_at' => now()]);
    }
}
