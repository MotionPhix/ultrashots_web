<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'message',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    #[Scope]
    protected function new(Builder $query): void
    {
        $query->where('status', 'new');
    }

    #[Scope]
    protected function read(Builder $query): void
    {
        $query->where('status', 'read');
    }

    public function markAsRead(): void
    {
        $this->update(['status' => 'read']);
    }
}
