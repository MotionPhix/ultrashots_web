<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\Filters\ProjectFilters;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService
    ) {}

    /**
     * Display public portfolio projects.
     */
    public function index(Request $request): Response
    {
        $projects = $this->projectService->getPublicProjects(ProjectFilters::fromRequest($request, 10));

        return Inertia::render('frontend/projects/Index', [
            'projects' => ProjectResource::collection($projects),
            'filters' => [
                'search' => $request->get('search'),
                'category' => $request->get('category'),
                'status' => $request->get('status'),
                'year' => $request->get('year'),
                'sort' => $request->get('sort', 'newest'),
            ],
            'categories' => $this->projectService->getCategories(true),
            'statuses' => ['not_started', 'in_progress', 'on_hold', 'completed', 'cancelled'],
            'years' => $this->getAvailableYears($projects),
        ]);
    }

    /**
     * Get available years from projects.
     */
    private function getAvailableYears($projects): array
    {
        $years = [];
        foreach ($projects as $project) {
            $year = $project->end_date
              ? $project->end_date->year
              : $project->created_at->year;
            $years[$year] = $year;
        }
        arsort($years);

        return array_values(array_unique($years));
    }

    /**
     * Display the specified project for public portfolio.
     */
    public function show(Project $project): Response
    {
        if (! $project->is_public) {
            abort(404);
        }

        $project = $this->projectService->getProject($project, ['customer', 'media']);
        $relatedProjects = $this->projectService->getRelatedProjects($project);

        return Inertia::render('frontend/projects/Show', [
            'project' => new ProjectResource($project),
            'relatedProjects' => ProjectResource::collection($relatedProjects),
        ]);
    }
}
