<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectImage extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'project_id',
    'src',
    'alt',
    'title',
    'description',
    'sort_order',
    'is_featured',
  ];

  /**
   * The attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'sort_order' => 'integer',
      'is_featured' => 'boolean',
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

    static::creating(function ($image) {
      if (is_null($image->sort_order)) {
        $maxOrder = static::where('project_id', $image->project_id)->max('sort_order');
        $image->sort_order = $maxOrder ? $maxOrder + 1 : 1;
      }
    });
  }

  /**
   * Get the project that owns the image.
   */
  public function project(): BelongsTo
  {
    return $this->belongsTo(Project::class);
  }

  /**
   * Scope a query to only include featured images.
   */
  public function scopeFeatured($query)
  {
    return $query->where('is_featured', true);
  }

  /**
   * Scope a query to order by sort order.
   */
  public function scopeOrdered($query)
  {
    return $query->orderBy('sort_order');
  }
}
