<?php

namespace App\Models;

use App\Enums\ContactStatus;
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
            'status' => ContactStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    #[Scope]
    protected function new(Builder $query): void
    {
        $query->where('status', ContactStatus::New);
    }

    #[Scope]
    protected function read(Builder $query): void
    {
        $query->where('status', ContactStatus::Read);
    }

    public function markAsRead(): void
    {
        $this->update(['status' => ContactStatus::Read]);
    }
}
