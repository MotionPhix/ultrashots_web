<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertProjectRequest;
use App\Models\Customer;
use App\Models\Project;
use App\Services\CustomerService;
use App\Services\Filters\ProjectFilters;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService,
        private CustomerService $customerService,
    ) {}

    public function listing(Request $request): Response
    {
        $projects = $this->projectService->getAdminProjects(ProjectFilters::fromRequest($request))
            ->through(fn (Project $project): array => $this->projectPayload($project));

        return Inertia::render('admin/projects/Index', [
            'projects' => $projects,
            'filters' => $request->only(['search', 'status', 'production_type', 'category', 'priority', 'sort_by', 'sort_direction']),
            'stats' => $this->projectService->getProjectStats(),
            'statuses' => $this->statuses(),
            'priorities' => $this->priorities(),
            'production_types' => $this->projectService->getProductionTypes()->values(),
            'categories' => $this->projectService->getCategories()->values(),
        ]);
    }

    public function create(?Customer $customer = null): Response
    {
        return Inertia::render('admin/projects/Form', [
            'project' => null,
            'customers' => $this->customerOptions(),
            'selected_customer_uuid' => $customer?->uuid,
            'statuses' => $this->statuses(),
            'priorities' => $this->priorities(),
        ]);
    }

    public function store(UpsertProjectRequest $request): RedirectResponse
    {
        $project = $this->projectService->createProject($this->projectData($request));

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function detail(Project $project): Response
    {
        $project = $this->projectService->getProject($project, ['customer', 'media']);

        return Inertia::render('admin/projects/Show', [
            'project' => $this->projectPayload($project, includeMedia: true),
        ]);
    }

    public function edit(Project $project): Response
    {
        $project = $this->projectService->getProject($project, ['customer']);

        return Inertia::render('admin/projects/Form', [
            'project' => $this->projectPayload($project, includeMedia: true),
            'customers' => $this->customerOptions(),
            'selected_customer_uuid' => $project->customer?->uuid,
            'statuses' => $this->statuses(),
            'priorities' => $this->priorities(),
        ]);
    }

    public function update(UpsertProjectRequest $request, Project $project): RedirectResponse
    {
        $this->projectService->updateProject($project, $this->projectData($request));

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->projectService->deleteProject($project);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    private function statuses(): array
    {
        return ['not_started', 'in_progress', 'completed', 'on_hold', 'cancelled'];
    }

    private function priorities(): array
    {
        return ['low', 'medium', 'high', 'critical'];
    }

    private function customerOptions(): Collection
    {
        return $this->customerService->getActiveCustomers()->values();
    }

    private function projectData(UpsertProjectRequest $request): array
    {
        $validated = $request->validated();

        $validated['technologies'] = $this->parseList($request->string('technologies')->toString());
        $validated['features'] = $this->parseList($request->string('features')->toString());
        $validated['customer_id'] = $validated['customer_id'] ?? null;

        return $validated;
    }

    private function parseList(?string $value): array
    {
        return collect(explode(',', (string) $value))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    private function projectPayload(Project $project, bool $includeMedia = false): array
    {
        $payload = [
            'id' => $project->id,
            'uuid' => $project->uuid,
            'name' => $project->name,
            'slug' => $project->slug,
            'description' => $project->description,
            'short_description' => $project->short_description,
            'production_type' => $project->production_type,
            'category' => $project->category,
            'status' => $project->status,
            'priority' => $project->priority,
            'start_date' => $project->start_date?->format('Y-m-d\\TH:i'),
            'end_date' => $project->end_date?->format('Y-m-d\\TH:i'),
            'estimated_hours' => $project->estimated_hours,
            'actual_hours' => $project->actual_hours,
            'budget' => $project->budget,
            'technologies' => $project->technologies ?? [],
            'features' => $project->features ?? [],
            'challenges' => $project->challenges,
            'solutions' => $project->solutions,
            'results' => $project->results,
            'client_feedback' => $project->client_feedback,
            'is_featured' => $project->is_featured,
            'is_public' => $project->is_public,
            'sort_order' => $project->sort_order,
            'meta_title' => $project->meta_title,
            'meta_description' => $project->meta_description,
            'poster_url' => $project->poster_url,
            'live_url' => $project->live_url,
            'github_url' => $project->github_url,
            'figma_url' => $project->figma_url,
            'behance_url' => $project->behance_url,
            'dribbble_url' => $project->dribbble_url,
            'customer' => $project->customer ? [
                'id' => $project->customer->id,
                'uuid' => $project->customer->uuid,
                'full_name' => $project->customer->full_name,
                'company_name' => $project->customer->company_name,
            ] : null,
            'created_at' => $project->created_at?->toDateTimeString(),
            'updated_at' => $project->updated_at?->toDateTimeString(),
        ];

        if ($includeMedia) {
            $payload['gallery_images'] = $this->projectService->getGalleryImages($project);
        }

        return $payload;
    }
}
