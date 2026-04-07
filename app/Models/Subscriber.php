<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'token',
        'subscribed',
        'confirmed_at',
        'subscribed_at',
        'unsubscribed_at',
        'meta',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subscribed' => 'boolean',
            'confirmed_at' => 'datetime',
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
            'meta' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Subscriber $subscriber): void {
            if ($subscriber->email !== null) {
                $subscriber->email = Str::lower($subscriber->email);
            }

            if (blank($subscriber->token)) {
                $subscriber->token = Str::random(64);
            }
        });
    }

    public function scopeSubscribed($query)
    {
        return $query->where('subscribed', true);
    }

    public function confirm(): void
    {
        $this->forceFill([
            'subscribed' => true,
            'confirmed_at' => $this->confirmed_at ?? now(),
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ])->save();
    }

    public function unsubscribe(): void
    {
        $this->forceFill([
            'subscribed' => false,
            'unsubscribed_at' => now(),
        ])->save();
    }
}
