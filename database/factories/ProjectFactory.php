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
        $name = fake()->unique()->sentence(3);

        return [
            'uuid' => (string) Str::orderedUuid(),
            'customer_id' => null,
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->paragraphs(2, true),
            'short_description' => fake()->sentence(),
            'production_type' => fake()->randomElement(['Web App', 'Website', 'Brand System', 'Dashboard']),
            'category' => fake()->randomElement(['Web Design', 'Mobile App', 'Branding', 'Development']),
            'status' => fake()->randomElement(['not_started', 'in_progress', 'completed', 'on_hold', 'cancelled']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'start_date' => fake()->dateTimeBetween('-6 months', '-2 weeks'),
            'end_date' => fake()->dateTimeBetween('-2 weeks', '+2 months'),
            'estimated_hours' => fake()->numberBetween(20, 180),
            'actual_hours' => fake()->numberBetween(10, 220),
            'budget' => fake()->randomFloat(2, 1500, 25000),
            'technologies' => fake()->randomElements(['Laravel', 'Vue', 'TypeScript', 'Tailwind', 'MySQL'], fake()->numberBetween(2, 4)),
            'features' => fake()->randomElements(['Responsive UI', 'CMS', 'Animations', 'Analytics', 'Authentication'], fake()->numberBetween(2, 4)),
            'challenges' => fake()->paragraph(),
            'solutions' => fake()->paragraph(),
            'results' => fake()->paragraph(),
            'client_feedback' => fake()->sentence(),
            'is_featured' => false,
            'is_public' => true,
            'sort_order' => fake()->numberBetween(0, 20),
            'meta_title' => $name,
            'meta_description' => fake()->sentence(),
            'poster_url' => fake()->imageUrl(1200, 900, 'business'),
            'live_url' => fake()->optional()->url(),
            'github_url' => fake()->optional()->url(),
            'figma_url' => fake()->optional()->url(),
            'behance_url' => fake()->optional()->url(),
            'dribbble_url' => fake()->optional()->url(),
        ];
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_public' => true,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_public' => false,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_featured' => true,
        ]);
    }
}
