<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Download extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'title',
        'brand',
        'description',
        'category',
        'file_type',
        'file_size',
        'download_count',
        'is_featured',
        'is_public',
        'sort_order',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'download_count' => 'integer',
            'is_featured' => 'boolean',
            'is_public' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Download $download): void {
            if (blank($download->uuid)) {
                $download->uuid = (string) Str::orderedUuid();
            }

            if ($download->sort_order === null) {
                $download->sort_order = (static::max('sort_order') ?? 0) + 1;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('download_count');
    }

    public function scopeRecent($query)
    {
        return $query->latest('published_at')->latest();
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($downloadQuery) use ($search) {
            $downloadQuery->where('title', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%")
                ->orWhere('file_type', 'like', "%{$search}%");
        });
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByFileType($query, string $fileType)
    {
        return $query->where('file_type', $fileType);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->latest('published_at');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('poster')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']);

        $this->addMediaCollection('file')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(640)
            ->height(480)
            ->performOnCollections('poster')
            ->nonQueued();
    }

    public function getPosterUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('poster', 'thumb')
            ?: $this->getFirstMediaUrl('poster')
            ?: null;
    }

    public function getFormattedFileSizeAttribute(): ?string
    {
        $bytes = $this->file_size ?? $this->getFirstMedia('file')?->size;

        if ($bytes === null || $bytes <= 0) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        return number_format($bytes / (1024 ** $power), $power === 0 ? 0 : 1).' '.$units[$power];
    }

    public function getHasFileAttribute(): bool
    {
        return $this->getFirstMedia('file') !== null;
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * @return array<string, int>
     */
    public static function getStats(): array
    {
        return [
            'total_downloads' => static::count(),
            'public_downloads' => static::public()->count(),
            'featured_downloads' => static::featured()->count(),
            'download_events' => (int) static::sum('download_count'),
        ];
    }
}
