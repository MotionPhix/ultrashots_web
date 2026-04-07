<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Project;
use App\Services\Filters\ProjectFilters;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProjectService
{
    /**
     * Get filtered and paginated projects for public portfolio.
     */
    public function getPublicProjects(ProjectFilters $filters): LengthAwarePaginator
    {
        $query = Project::query()
            ->public()
            ->with(['customer', 'media']);

        $this->applyFilters($query, $filters);
        $this->applySortingPublic($query, $filters);

        return $query->paginate($filters->perPage);
    }

    /**
     * Get filtered and paginated projects for admin dashboard.
     */
    public function getAdminProjects(ProjectFilters $filters): LengthAwarePaginator
    {
        $query = Project::query()->with(['customer', 'media']);

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters);

        return $query->paginate($filters->perPage);
    }

    /**
     * Get a single project with relationships.
     */
    public function getProject(Project $project, array $with = []): Project
    {
        if (! empty($with)) {
            $project->load($with);
        }

        return $project;
    }

    /**
     * Create a new project.
     */
    public function createProject(array $data): Project
    {
        // Generate slug if not provided
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }

        // Generate UUID if not provided
        if (empty($data['uuid'])) {
            $data['uuid'] = (string) Str::orderedUuid();
        }

        // Resolve customer_id from UUID if needed
        if (! empty($data['customer_id']) && ! is_numeric($data['customer_id'])) {
            $customer = Customer::where('uuid', $data['customer_id'])->first();
            $data['customer_id'] = $customer?->id;
        }

        return Project::create($data);
    }

    /**
     * Update an existing project.
     */
    public function updateProject(Project $project, array $data): Project
    {
        // Resolve customer_id from UUID if needed
        if (! empty($data['customer_id']) && ! is_numeric($data['customer_id'])) {
            $customer = Customer::where('uuid', $data['customer_id'])->first();
            $data['customer_id'] = $customer?->id;
        }

        // Update slug if name changed and slug not provided
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $project->id);
        }

        $project->update($data);

        return $project->fresh();
    }

    /**
     * Delete a project.
     */
    public function deleteProject(Project $project): bool
    {
        return $project->delete();
    }

    /**
     * Handle all media updates for a project (gallery and poster).
     */
    public function updateProjectMedia(
        Project $project,
        array $newGalleryFiles = [],
        ?UploadedFile $newPosterFile = null,
        array $existingGalleryUrls = [],
        ?string $existingPosterUrl = null
    ): void {
        DB::transaction(function () use ($project, $newGalleryFiles, $newPosterFile, $existingGalleryUrls, $existingPosterUrl) {
            // Update gallery media
            $this->updateGalleryMedia($project, $existingGalleryUrls, $newGalleryFiles);

            // Update poster media
            $this->updatePosterMedia($project, $existingPosterUrl, $newPosterFile);
        });
    }

    /**
     * Update gallery media for a project.
     * Removes media not in existing list and adds new uploads.
     */
    public function updateGalleryMedia(Project $project, array $existingImageUrls = [], array $newFiles = []): void
    {
        try {
            // Get current gallery media
            $currentMedia = $project->getMedia('gallery');

            Log::info('Updating gallery media', [
                'project_id' => $project->id,
                'current_media_count' => $currentMedia->count(),
                'existing_urls_count' => count($existingImageUrls),
                'new_files_count' => count($newFiles),
            ]);

            // Remove media that's no longer in the existing list
            foreach ($currentMedia as $media) {
                $mediaUrl = $media->getUrl();
                if (! in_array($mediaUrl, $existingImageUrls)) {
                    Log::info('Removing gallery media', ['media_id' => $media->id, 'url' => $mediaUrl]);
                    $media->delete();
                }
            }

            // Add new files if any
            if (! empty($newFiles)) {
                $this->uploadMedia($project, $newFiles, 'gallery');
            }
        } catch (\Exception $e) {
            Log::error('Error updating gallery media', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update poster media for a project.
     * Removes current poster if not in existing and adds new upload.
     */
    public function updatePosterMedia(Project $project, ?string $existingPosterUrl = null, ?UploadedFile $newFile = null): void
    {
        try {
            // Get current poster media
            $currentPosterMedia = $project->getFirstMedia('poster');

            Log::info('Updating poster media', [
                'project_id' => $project->id,
                'has_current_poster' => (bool) $currentPosterMedia,
                'existing_poster_url' => $existingPosterUrl,
                'has_new_file' => (bool) $newFile,
            ]);

            // If there's a current poster and it's not in the existing URL, remove it
            if ($currentPosterMedia && (! $existingPosterUrl || $currentPosterMedia->getUrl() !== $existingPosterUrl)) {
                Log::info('Removing current poster', ['media_id' => $currentPosterMedia->id]);
                $currentPosterMedia->delete();
            }

            // Add new poster file if provided
            if ($newFile) {
                // Clear any remaining poster media to ensure single file
                $project->clearMediaCollection('poster');
                $this->uploadMedia($project, [$newFile], 'poster');
            }
        } catch (\Exception $e) {
            Log::error('Error updating poster media', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Upload media for a project.
     */
    public function uploadMedia(Project $project, array|UploadedFile $files, string $collection = 'gallery'): void
    {
        if (! $files) {
            return;
        }

        // Normalize to array if single file is passed
        $files = is_array($files) ? $files : [$files];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                try {
                    $mediaAdder = $project->addMedia($file)
                        ->usingFileName($this->generateUniqueFileName($file))
                        ->usingName(Str::slug($project->name));

                    // Add to specific collection
                    $media = $mediaAdder->toMediaCollection($collection);

                    Log::info('Media uploaded successfully', [
                        'project_id' => $project->id,
                        'media_id' => $media->id,
                        'collection' => $collection,
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error uploading media', [
                        'project_id' => $project->id,
                        'collection' => $collection,
                        'file_name' => $file->getClientOriginalName(),
                        'error' => $e->getMessage(),
                    ]);
                    throw $e;
                }
            }
        }
    }

    /**
     * Generate a unique filename to prevent conflicts.
     */
    private function generateUniqueFileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(6);

        return Str::slug($name)."_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get related projects based on production type and category.
     */
    public function getRelatedProjects(Project $project, int $limit = 3): Collection
    {
        return Project::public()
            ->where('id', '!=', $project->id)
            ->where(function ($query) use ($project) {
                $query->where('production_type', $project->production_type)
                    ->orWhere('category', $project->category);
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Get project statistics.
     */
    public function getProjectStats(): array
    {
        return [
            'total_projects' => Project::count(),
            'active_projects' => Project::active()->count(),
            'completed_projects' => Project::completed()->count(),
            'featured_projects' => Project::featured()->count(),
            'public_projects' => Project::public()->count(),
            'overdue_projects' => Project::where('end_date', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'projects_by_status' => Project::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'projects_by_production_type' => Project::selectRaw('production_type, COUNT(*) as count')
                ->whereNotNull('production_type')
                ->groupBy('production_type')
                ->pluck('count', 'production_type'),
            'average_project_duration' => $this->getAverageProjectDuration(),
            'total_estimated_hours' => Project::sum('estimated_hours'),
            'total_actual_hours' => Project::sum('actual_hours'),
            'total_budget' => Project::sum('budget'),
        ];
    }

    private function getAverageProjectDuration(): float
    {
        $averageDuration = Project::query()
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get(['start_date', 'end_date'])
            ->avg(fn (Project $project): int => $project->start_date->diffInDays($project->end_date));

        return round((float) ($averageDuration ?? 0), 2);
    }

    /**
     * Get distinct production types.
     */
    public function getProductionTypes(bool $publicOnly = false): \Illuminate\Support\Collection
    {
        $query = Project::distinct()->whereNotNull('production_type');

        if ($publicOnly) {
            $query->where('is_public', true);
        }

        return $query->pluck('production_type')->sort()->values();
    }

    /**
     * Get distinct categories.
     */
    public function getCategories(bool $publicOnly = false): \Illuminate\Support\Collection
    {
        $query = Project::distinct()->whereNotNull('category');

        if ($publicOnly) {
            $query->where('is_public', true);
        }

        return $query->pluck('category')->sort()->values();
    }

    /**
     * Get all technologies used across projects.
     */
    public function getTechnologies(): Collection
    {
        return Project::whereNotNull('technologies')
            ->pluck('technologies')
            ->flatten()
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Reorder projects.
     */
    public function reorderProjects(array $projects): bool
    {
        foreach ($projects as $projectData) {
            Project::where('id', $projectData['id'])
                ->update(['sort_order' => $projectData['sort_order']]);
        }

        return true;
    }

    /**
     * Get project media by collection.
     */
    public function getProjectMedia(Project $project, string $collection = 'default')
    {
        return $project->getMedia($collection);
    }

    /**
     * Delete all media for a project.
     */
    public function deleteProjectMedia(Project $project): void
    {
        $project->clearMediaCollections();
    }

    /**
     * Get project gallery images with URLs.
     */
    public function getGalleryImages(Project $project): array
    {
        return $project->getMedia('gallery')->map(function (Media $media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->name,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'thumb_url' => $media->getUrl('thumb'),
                'medium_url' => $media->getUrl('medium'),
                'large_url' => $media->getUrl('large'),
            ];
        })->toArray();
    }

    /**
     * Get project poster image URL.
     */
    public function getPosterImageUrl(Project $project): ?string
    {
        $posterMedia = $project->getFirstMedia('poster');

        return $posterMedia ? $posterMedia->getUrl() : null;
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters($query, ProjectFilters $filters): void
    {
        if ($filters->search !== null) {
            $query->search($filters->search);
        }

        if ($filters->status !== null) {
            $query->byStatus($filters->status);
        }

        if ($filters->productionType !== null) {
            $query->byProductionType($filters->productionType);
        }

        if ($filters->category !== null) {
            $query->byCategory($filters->category);
        }

        if ($filters->year !== null) {
            $query->byYear($filters->year);
        }

        if ($filters->priority !== null) {
            $query->byPriority($filters->priority);
        }

        if ($filters->customerId !== null) {
            $query->where('customer_id', $filters->customerId);
        }

        if ($filters->featured) {
            $query->featured();
        }

        if ($filters->public) {
            $query->public();
        }

        if ($filters->startDate !== null) {
            $query->where('start_date', '>=', $filters->startDate);
        }

        if ($filters->endDate !== null) {
            $query->where('end_date', '<=', $filters->endDate);
        }
    }

    /**
     * Apply sorting to the query for public projects.
     */
    private function applySortingPublic($query, ProjectFilters $filters): void
    {
        $sort = $filters->sort;

        switch ($sort) {
            case 'oldest':
                $query->orderByRaw('COALESCE(end_date, created_at) ASC');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'featured':
                $query->orderByRaw('is_featured DESC')
                    ->orderByRaw('COALESCE(end_date, created_at) DESC');
                break;
            case 'newest':
            default:
                $query->orderByRaw('COALESCE(end_date, created_at) DESC');
                break;
        }
    }

    /**
     * Apply sorting to the query.
     */
    private function applySorting($query, ProjectFilters $filters): void
    {
        $sortField = $filters->sortBy;
        $sortDirection = $filters->sortDirection;

        $allowedSortFields = [
            'name', 'status', 'priority', 'start_date', 'end_date',
            'estimated_hours', 'actual_hours', 'budget', 'sort_order',
            'created_at', 'updated_at',
        ];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->ordered();
        }
    }

    /**
     * Generate a unique slug for a project.
     */
    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if a slug already exists.
     */
    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Project::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
