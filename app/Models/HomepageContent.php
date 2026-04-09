<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HomepageContent extends Model
{
    use HasFactory;

    protected $table = 'homepage_content'; // Explicitly set table name

    protected $fillable = [
        'section',
        'content',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'content' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active content
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Get content by section name
     */
    public static function getBySection(string $section): ?array
    {
        $record = static::where('section', $section)
            ->where('is_active', true)
            ->first();

        return $record ? $record->content : null;
    }

    /**
     * Update or create content for a section
     */
    public static function updateOrCreateForSection(string $section, array $content): self
    {
        return static::updateOrCreate(
            ['section' => $section],
            [
                'content' => $content,
                'is_active' => true,
            ]
        );
    }
}
