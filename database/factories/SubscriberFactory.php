<?php

namespace Database\Factories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Subscriber>
 */
class SubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'token' => Str::random(64),
            'subscribed' => true,
            'confirmed_at' => now(),
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
            'meta' => [
                'source' => 'factory',
            ],
        ];
    }

    public function unconfirmed(): static
    {
        return $this->state(fn () => [
            'subscribed' => false,
            'confirmed_at' => null,
            'subscribed_at' => null,
            'unsubscribed_at' => null,
        ]);
    }

    public function unsubscribed(): static
    {
        return $this->state(fn () => [
            'subscribed' => false,
            'unsubscribed_at' => now(),
        ]);
    }
}
