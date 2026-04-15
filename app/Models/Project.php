<?php

namespace App\Models;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Project extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'customer_id',
        'name',
        'slug',
        'description',
        'short_description',
        'production_type',
        'category',
        'status',
        'priority',
        'start_date',
        'end_date',
        'estimated_hours',
        'actual_hours',
        'budget',
        'technologies',
        'features',
        'challenges',
        'solutions',
        'process_steps',
        'results',
        'client_feedback',
        'is_featured',
        'is_public',
        'sort_order',
        'meta_title',
        'meta_description',
        'poster_url',
        'live_url',
        'github_url',
        'figma_url',
        'behance_url',
        'dribbble_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'estimated_hours' => 'decimal:2',
            'actual_hours' => 'decimal:2',
            'budget' => 'decimal:2',
            'technologies' => 'array',
            'features' => 'array',
            'process_steps' => 'array',
            'is_featured' => 'boolean',
            'is_public' => 'boolean',
            'sort_order' => 'integer',
            'status' => ProjectStatus::class,
            'priority' => ProjectPriority::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->uuid)) {
                $project->uuid = (string) Str::orderedUuid();
            }

            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name);
            }

            if (is_null($project->sort_order)) {
                $project->sort_order = static::max('sort_order') + 1;
            }
        });

        static::updating(function ($project) {
            if ($project->isDirty('name') && empty($project->slug)) {
                $project->slug = Str::slug($project->name);
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the customer that owns the project.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all images for this project.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class);
    }

    /**
     * Get all reactions for this project.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    /**
     * Get the project's duration in days.
     */
    protected function duration(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): ?int {
                if (! $this->start_date || ! $this->end_date) {
                    return null;
                }

                return $this->start_date->diffInDays($this->end_date);
            }
        );
    }

    /**
     * Get the project's progress percentage.
     */
    protected function progress(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): int {
                if ($this->status === ProjectStatus::Completed) {
                    return 100;
                }

                if ($this->status === ProjectStatus::NotStarted) {
                    return 0;
                }

                if (! $this->start_date || ! $this->end_date) {
                    return 0;
                }

                $totalDays = $this->start_date->diffInDays($this->end_date);
                $elapsedDays = $this->start_date->diffInDays(now());

                if ($totalDays <= 0) {
                    return 0;
                }

                return min(100, max(0, round(($elapsedDays / $totalDays) * 100)));
            }
        );
    }

    /**
     * Get the project's status badge color.
     */
    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): string {
                return match ($this->status) {
                    ProjectStatus::NotStarted => 'gray',
                    ProjectStatus::InProgress => 'blue',
                    ProjectStatus::OnHold => 'yellow',
                    ProjectStatus::Completed => 'green',
                    ProjectStatus::Cancelled => 'red',
                    default => 'gray',
                };
            }
        );
    }

    /**
     * Get the project's priority badge color.
     */
    protected function priorityColor(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): string {
                return match ($this->priority) {
                    ProjectPriority::Low => 'green',
                    ProjectPriority::Medium => 'yellow',
                    ProjectPriority::High => 'orange',
                    ProjectPriority::Critical => 'red',
                    default => 'gray',
                };
            }
        );
    }

    /**
     * Get the project's main technology stack.
     */
    protected function mainTechnologies(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): array {
                if (! $this->technologies) {
                    return [];
                }

                return array_slice($this->technologies, 0, 3);
            }
        );
    }

    /**
     * Get the project's estimated vs actual hours variance.
     */
    protected function hoursVariance(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): ?float {
                if (! $this->estimated_hours || ! $this->actual_hours) {
                    return null;
                }

                return $this->actual_hours - $this->estimated_hours;
            }
        );
    }

    /**
     * Check if the project is overdue.
     */
    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): bool {
                return $this->end_date &&
                    $this->end_date->isPast() &&
                    $this->status !== ProjectStatus::Completed;
            }
        );
    }

    /**
     * Scope a query to only include active projects.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereIn('status', [ProjectStatus::NotStarted, ProjectStatus::InProgress]);
    }

    /**
     * Scope a query to only include completed projects.
     */
    #[Scope]
    protected function completed(Builder $query): void
    {
        $query->where('status', ProjectStatus::Completed);
    }

    /**
     * Scope a query to only include featured projects.
     */
    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include public projects.
     */
    #[Scope]
    protected function public(Builder $query): void
    {
        $query->where('is_public', true);
    }

    /**
     * Scope a query to search projects.
     */
    #[Scope]
    protected function search(Builder $query, string $search): void
    {
        $searchTerm = Str::lower((string) $search);

        $query->where(function ($q) use ($search, $searchTerm) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('short_description', 'like', "%{$search}%")
                ->orWhere('production_type', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%")
                ->orWhereRaw("LOWER(COALESCE(technologies, '')) like ?", ["%{$searchTerm}%"])
                ->orWhereRaw("LOWER(COALESCE(features, '')) like ?", ["%{$searchTerm}%"]);
        });
    }

    /**
     * Scope a query to filter by production type.
     */
    #[Scope]
    protected function byProductionType(Builder $query, string $type): void
    {
        $query->where('production_type', $type);
    }

    /**
     * Scope a query to filter by category.
     */
    #[Scope]
    protected function byCategory(Builder $query, string $category): void
    {
        $query->where('category', $category);
    }

    /**
     * Scope a query to filter by status.
     */
    #[Scope]
    protected function byStatus(Builder $query, ProjectStatus $status): void
    {
        $query->where('status', $status);
    }

    /**
     * Scope a query to filter by year.
     */
    #[Scope]
    protected function byYear(Builder $query, int $year): void
    {
        $query->where(function ($q) use ($year) {
            $q->whereYear('end_date', $year)
                ->orWhere(function ($q2) use ($year) {
                    $q2->whereNull('end_date')->whereYear('created_at', $year);
                });
        });
    }

    /**
     * Scope a query to filter by priority.
     */
    #[Scope]
    protected function byPriority(Builder $query, string $priority): void
    {
        $query->where('priority', $priority);
    }

    /**
     * Scope a query to order by sort order.
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order');
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

        $this->addMediaCollection('poster')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    }

    /**
     * Register media conversions.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('gallery', 'poster');

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(600)
            ->sharpen(10)
            ->performOnCollections('gallery', 'poster');

        $this->addMediaConversion('large')
            ->width(1200)
            ->height(900)
            ->sharpen(10)
            ->performOnCollections('gallery');
    }

    /**
     * Get the poster image URL.
     * Priority: 1) Media library poster, 2) Database poster_url field
     */
    protected function posterUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value) {
                // First, check for media library poster
                $poster = $this->getFirstMedia('poster');
                if ($poster) {
                    return $poster->getUrl('medium');
                }

                // Fall back to database poster_url field (for placeholder images)
                return $value;
            }
        );
    }

    /**
     * Get the gallery images.
     */
    protected function galleryImages(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): array {
                return $this->getMedia('gallery')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'url' => $media->getUrl(),
                        'thumb_url' => $media->getUrl('thumb'),
                        'medium_url' => $media->getUrl('medium'),
                        'large_url' => $media->getUrl('large'),
                    ];
                })->toArray();
            }
        );
    }
}
