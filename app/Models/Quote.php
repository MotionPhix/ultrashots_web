<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Quote extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'project_type',
        'budget_range',
        'timeline',
        'description',
        'goals',
        'target_audience',
        'additional_info',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('reference_materials')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'application/pdf',
            ]);
    }

    /**
     * Get reference materials
     */
    protected function referenceFiles(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->getMedia('reference_materials')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'url' => $media->getUrl(),
                    ];
                })->toArray();
            }
        );
    }

    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('status', 'pending');
    }

    #[Scope]
    protected function approved(Builder $query): void
    {
        $query->where('status', 'approved');
    }

    #[Scope]
    protected function rejected(Builder $query): void
    {
        $query->where('status', 'rejected');
    }
}
