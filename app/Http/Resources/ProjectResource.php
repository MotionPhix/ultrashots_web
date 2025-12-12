<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'uuid' => $this->uuid,
      'name' => $this->name,
      'slug' => $this->slug,
      'description' => $this->description,
      'short_description' => $this->short_description,
      'production_type' => $this->production_type,
      'category' => $this->category,
      'status' => $this->status,
      'status_color' => $this->status_color,
      'priority' => $this->priority,
      'priority_color' => $this->priority_color,
      'start_date' => $this->start_date?->toDateString(),
      'end_date' => $this->end_date?->toDateString(),
      'duration' => $this->duration,
      'progress' => $this->progress,
      'estimated_hours' => $this->estimated_hours,
      'actual_hours' => $this->actual_hours,
      'hours_variance' => $this->hours_variance,
      'budget' => $this->budget,
      'technologies' => $this->technologies,
      'main_technologies' => $this->main_technologies,
      'features' => $this->features,
      'challenges' => $this->challenges,
      'solutions' => $this->solutions,
      'results' => $this->results,
      'client_feedback' => $this->client_feedback,
      'is_featured' => $this->is_featured,
      'is_public' => $this->is_public,
      'is_overdue' => $this->is_overdue,
      'sort_order' => $this->sort_order,
      'meta_title' => $this->meta_title,
      'meta_description' => $this->meta_description,
      'poster_url' => $this->poster_url,
      'live_url' => $this->live_url,
      'github_url' => $this->github_url,
      'figma_url' => $this->figma_url,
      'behance_url' => $this->behance_url,
      'dribbble_url' => $this->dribbble_url,
      'gallery_images' => $this->gallery_images,
      'created_at' => $this->created_at?->toISOString(),
      'updated_at' => $this->updated_at?->toISOString(),

      // Conditionally include relationships
      'customer' => new CustomerResource($this->whenLoaded('customer')),
      'images' => ProjectImageResource::collection($this->whenLoaded('images')),

      // Include counts when available
      'images_count' => $this->when(
        $this->relationLoaded('images'),
        fn() => $this->images->count()
      ),
    ];
  }

  /**
   * Get additional data that should be returned with the resource array.
   *
   * @return array<string, mixed>
   */
  public function with(Request $request): array
  {
    return [
      'meta' => [
        'resource_type' => 'project',
        'version' => '1.0',
      ],
    ];
  }
}
