<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
  /**
   * Handle the incoming request.
   */
  public function index(Request $request)
  {
    return Inertia::render('Welcome', [
      'user' => fn() => User::where('email', 'hello@ultrashots.net')->first(),
      'projects' => fn() => $this->getFeaturedProjects(),
    ]);
  }

  /**
   * Get featured projects for the homepage.
   */
  private function getFeaturedProjects()
  {
    return Project::query()
      ->with(['media', 'customer:id,first_name,last_name,company_name'])
      ->select([
        'id', 'uuid', 'name', 'slug', 'short_description',
        'production_type', 'category', 'status', 'technologies',
        'is_featured', 'is_public', 'customer_id', 'created_at'
      ])
      ->public()
      ->completed()
      ->featured()
      ->orderBy('sort_order')
      ->take(6)
      ->get()
      ->map(function ($project) {
        return [
          'id' => $project->id,
          'uuid' => $project->uuid,
          'name' => $project->name,
          'slug' => $project->slug,
          'short_description' => $project->short_description,
          'production_type' => $project->production_type,
          'category' => $project->category,
          'status' => $project->status,
          'technologies' => $project->main_technologies,
          'customer' => $project->customer ? [
            'name' => $project->customer->company_name ?:
              trim($project->customer->first_name . ' ' . $project->customer->last_name),
          ] : null,
          'poster_url' => $project->poster_url,
          'gallery_images' => $project->gallery_images,
          'created_at' => $project->created_at?->format('Y'),
        ];
      });
  }
}
