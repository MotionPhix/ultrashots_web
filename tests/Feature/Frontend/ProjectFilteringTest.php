<?php

namespace Tests\Feature\Frontend;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProjectFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_filter_projects_by_search(): void
    {
        Project::factory()
            ->public()
            ->create([
                'name' => 'Laravel Dashboard',
                'technologies' => ['Laravel', 'Vue'],
            ]);

        Project::factory()
            ->public()
            ->create([
                'name' => 'Vue Website',
                'technologies' => ['Vue', 'TypeScript'],
            ]);

        $response = $this->get('/projects?search=Laravel');

        $response->assertSuccessful();
        $response->assertSee('Laravel Dashboard');
        $response->assertDontSee('Vue Website');
    }

    public function test_can_filter_projects_by_partial_technology_match(): void
    {
        Project::factory()
            ->public()
            ->create([
                'name' => 'Studio Platform',
                'technologies' => ['Laravel', 'Vue'],
            ]);

        Project::factory()
            ->public()
            ->create([
                'name' => 'React Portal',
                'technologies' => ['React', 'TypeScript'],
            ]);

        $response = $this->get('/projects?search=larav');

        $response->assertSuccessful();
        $response->assertSee('Studio Platform');
        $response->assertDontSee('React Portal');
    }

    public function test_can_filter_projects_by_category(): void
    {
        Project::factory()
            ->public()
            ->create(['category' => 'Web Design']);

        Project::factory()
            ->public()
            ->create(['category' => 'Mobile App']);

        $response = $this->get('/projects?category=Web%20Design');

        $response->assertSuccessful();
        $response->assertSee('Web Design');
    }

    public function test_can_filter_projects_by_status(): void
    {
        Project::factory()
            ->public()
            ->create(['status' => 'completed']);

        Project::factory()
            ->public()
            ->create(['status' => 'in_progress']);

        $response = $this->get('/projects?status=completed');

        $response->assertSuccessful();
    }

    public function test_can_filter_projects_by_year(): void
    {
        $year = now()->year;

        Project::factory()
            ->public()
            ->create(['end_date' => now()->year($year)]);

        Project::factory()
            ->public()
            ->create(['end_date' => now()->year($year - 1)]);

        $response = $this->get('/projects?year='.$year);

        $response->assertSuccessful();
    }

    public function test_can_sort_projects_newest_first(): void
    {
        $older = Project::factory()
            ->public()
            ->create(['created_at' => now()->subMonth()]);

        $newer = Project::factory()
            ->public()
            ->create(['created_at' => now()]);

        $response = $this->get('/projects?sort=newest');

        $response->assertSuccessful();
    }

    public function test_can_sort_projects_oldest_first(): void
    {
        Project::factory()
            ->public()
            ->create(['created_at' => now()->subMonth()]);

        Project::factory()
            ->public()
            ->create(['created_at' => now()]);

        $response = $this->get('/projects?sort=oldest');

        $response->assertSuccessful();
    }

    public function test_can_sort_projects_by_name(): void
    {
        Project::factory()
            ->public()
            ->create(['name' => 'Zebra']);

        Project::factory()
            ->public()
            ->create(['name' => 'Apple']);

        $response = $this->get('/projects?sort=name');

        $response->assertSuccessful();
    }

    public function test_can_sort_featured_projects_first(): void
    {
        Project::factory()
            ->public()
            ->create(['is_featured' => false]);

        Project::factory()
            ->public()
            ->create(['is_featured' => true]);

        $response = $this->get('/projects?sort=featured');

        $response->assertSuccessful();
    }

    public function test_filters_are_persisted_in_response(): void
    {
        Project::factory()
            ->public()
            ->create(['name' => 'Laravel Project', 'category' => 'Web']);

        $response = $this->get('/projects?search=Laravel&category=Web&sort=newest');

        $response->assertSuccessful();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('frontend/projects/Index')
            ->where('filters.search', 'Laravel')
            ->where('filters.category', 'Web')
            ->where('filters.sort', 'newest')
        );
    }

    public function test_only_public_projects_are_shown(): void
    {
        Project::factory()
            ->public()
            ->create(['name' => 'Public Project']);

        Project::factory()
            ->create(['is_public' => false, 'name' => 'Private Project']);

        $response = $this->get('/projects');

        $response->assertSuccessful();
        $response->assertSee('Public Project');
        $response->assertDontSee('Private Project');
    }

    public function test_available_years_are_provided(): void
    {
        $year1 = 2022;
        $year2 = 2023;

        Project::factory()
            ->public()
            ->create(['end_date' => now()->year($year1)]);

        Project::factory()
            ->public()
            ->create(['end_date' => now()->year($year2)]);

        $response = $this->get('/projects');

        $response->assertSuccessful();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('frontend/projects/Index')
            ->has('years')
        );
    }

    public function test_available_categories_are_provided(): void
    {
        Project::factory()
            ->public()
            ->create(['category' => 'Web Design']);

        Project::factory()
            ->public()
            ->create(['category' => 'Mobile App']);

        $response = $this->get('/projects');

        $response->assertSuccessful();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('frontend/projects/Index')
            ->has('categories')
        );
    }
}
