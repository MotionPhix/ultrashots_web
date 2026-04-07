<?php

namespace Database\Factories;

use App\Models\Download;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Download>
 */
class DownloadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::orderedUuid(),
            'title' => fake()->unique()->words(3, true),
            'brand' => fake()->company(),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['Branding', 'UI Kit', 'Mockup', 'Template']),
            'file_type' => fake()->randomElement(['zip', 'pdf', 'fig']),
            'file_size' => fake()->numberBetween(120_000, 15_000_000),
            'download_count' => fake()->numberBetween(0, 250),
            'is_featured' => false,
            'is_public' => true,
            'sort_order' => fake()->numberBetween(1, 50),
            'published_at' => now(),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => [
            'is_featured' => true,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn () => [
            'is_public' => false,
        ]);
    }

    public function public(): static
    {
        return $this->state(fn () => [
            'is_public' => true,
        ]);
    }
}
