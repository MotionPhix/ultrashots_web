<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\CustomerService;
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
    $projects = $this->projectService->getPublicProjects($request);

    return Inertia::render('frontend/projects/Index', [
      'projects' => ProjectResource::collection($projects),
      'filters' => [
        'search' => $request->search,
        'production_type' => $request->production_type,
        'category' => $request->category,
        'featured' => $request->featured,
      ],
      'productionTypes' => $this->projectService->getProductionTypes(true),
      'categories' => $this->projectService->getCategories(true),
    ]);
  }

  /**
   * Display the specified project for public portfolio.
   */
  public function show(Project $project): Response
  {
    if (!$project->is_public) {
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
