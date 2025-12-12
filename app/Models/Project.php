<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Project extends Model implements HasMedia
{
  use HasFactory, SoftDeletes, InteractsWithMedia;

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
      'is_featured' => 'boolean',
      'is_public' => 'boolean',
      'sort_order' => 'integer',
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
   * Get the project's duration in days.
   */
  public function getDurationAttribute(): ?int
  {
    if (!$this->start_date || !$this->end_date) {
      return null;
    }

    return $this->start_date->diffInDays($this->end_date);
  }

  /**
   * Get the project's progress percentage.
   */
  public function getProgressAttribute(): int
  {
    if ($this->status === 'completed') {
      return 100;
    }

    if ($this->status === 'not_started') {
      return 0;
    }

    if (!$this->start_date || !$this->end_date) {
      return 0;
    }

    $totalDays = $this->start_date->diffInDays($this->end_date);
    $elapsedDays = $this->start_date->diffInDays(now());

    if ($totalDays <= 0) {
      return 0;
    }

    return min(100, max(0, round(($elapsedDays / $totalDays) * 100)));
  }

  /**
   * Get the project's status badge color.
   */
  public function getStatusColorAttribute(): string
  {
    return match($this->status) {
      'not_started' => 'gray',
      'in_progress' => 'blue',
      'on_hold' => 'yellow',
      'completed' => 'green',
      'cancelled' => 'red',
      default => 'gray',
    };
  }

  /**
   * Get the project's priority badge color.
   */
  public function getPriorityColorAttribute(): string
  {
    return match($this->priority) {
      'low' => 'green',
      'medium' => 'yellow',
      'high' => 'orange',
      'urgent' => 'red',
      default => 'gray',
    };
  }

  /**
   * Get the project's main technology stack.
   */
  public function getMainTechnologiesAttribute(): array
  {
    if (!$this->technologies) {
      return [];
    }

    return array_slice($this->technologies, 0, 3);
  }

  /**
   * Get the project's estimated vs actual hours variance.
   */
  public function getHoursVarianceAttribute(): ?float
  {
    if (!$this->estimated_hours || !$this->actual_hours) {
      return null;
    }

    return $this->actual_hours - $this->estimated_hours;
  }

  /**
   * Check if the project is overdue.
   */
  public function getIsOverdueAttribute(): bool
  {
    return $this->end_date &&
      $this->end_date->isPast() &&
      $this->status !== 'completed';
  }

  /**
   * Scope a query to only include active projects.
   */
  public function scopeActive($query)
  {
    return $query->whereIn('status', ['not_started', 'in_progress']);
  }

  /**
   * Scope a query to only include completed projects.
   */
  public function scopeCompleted($query)
  {
    return $query->where('status', 'completed');
  }

  /**
   * Scope a query to only include featured projects.
   */
  public function scopeFeatured($query)
  {
    return $query->where('is_featured', true);
  }

  /**
   * Scope a query to only include public projects.
   */
  public function scopePublic($query)
  {
    return $query->where('is_public', true);
  }

  /**
   * Scope a query to search projects.
   */
  public function scopeSearch($query, $search)
  {
    return $query->where(function ($q) use ($search) {
      $q->where('name', 'like', "%{$search}%")
        ->orWhere('description', 'like', "%{$search}%")
        ->orWhere('short_description', 'like', "%{$search}%")
        ->orWhere('production_type', 'like', "%{$search}%")
        ->orWhere('category', 'like', "%{$search}%");
    });
  }

  /**
   * Scope a query to filter by production type.
   */
  public function scopeByProductionType($query, $type)
  {
    return $query->where('production_type', $type);
  }

  /**
   * Scope a query to filter by category.
   */
  public function scopeByCategory($query, $category)
  {
    return $query->where('category', $category);
  }

  /**
   * Scope a query to filter by status.
   */
  public function scopeByStatus($query, $status)
  {
    return $query->where('status', $status);
  }

  /**
   * Scope a query to filter by priority.
   */
  public function scopeByPriority($query, $priority)
  {
    return $query->where('priority', $priority);
  }

  /**
   * Scope a query to order by sort order.
   */
  public function scopeOrdered($query)
  {
    return $query->orderBy('sort_order');
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
  public function registerMediaConversions(Media $media = null): void
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
   */
  public function getPosterUrlAttribute(): ?string
  {
    $poster = $this->getFirstMedia('poster');
    return $poster ? $poster->getUrl('medium') : null;
  }

  /**
   * Get the gallery images.
   */
  public function getGalleryImagesAttribute(): array
  {
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
}
