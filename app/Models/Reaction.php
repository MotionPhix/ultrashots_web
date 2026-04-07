<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaction extends Model
{
    protected $fillable = [
        'project_id',
        'type',
        'feedback',
        'visitor_name',
        'visitor_email',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeLikes($query)
    {
        return $query->where('type', 'like');
    }

    public function scopeRecommendations($query)
    {
        return $query->where('type', 'recommended');
    }

    public function scopeFeedback($query)
    {
        return $query->where('type', 'feedback');
    }
}
