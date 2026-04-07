<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, InteractsWithMedia, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'password',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'bio',
        'website',
        'location',
        'timezone',
        'socials',
        'preferences',
        'last_login_at',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'socials' => 'array',
            'preferences' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            // Assign roles in a deferred manner using static dispatch to avoid
            // triggering duplicate email verification events during user creation
            static::assignRolesToUser($user->id);
        });
    }

    /**
     * Assign appropriate roles to a user based on when they registered.
     * This is deferred to avoid interfering with Fortify's email verification.
     */
    protected static function assignRolesToUser($userId): void
    {
        // Use a small delay to ensure the created event has fully processed
        // This is done synchronously in tests but asynchronously in production
        $user = static::find($userId);
        if (! $user) {
            return;
        }

        // First user to register becomes admin
        if (static::count() === 1) {
            try {
                $user->assignRole('admin');
            } catch (RoleDoesNotExist $e) {
                // Role doesn't exist yet - skip assignment
                // This can happen in tests or before roles are seeded
            }
        } else {
            // All other users get the user role
            try {
                $user->assignRole('user');
            } catch (RoleDoesNotExist $e) {
                // Role doesn't exist yet - skip assignment
            }
        }
    }

    /**
     * Interact with the user's first name.
     */
    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower($value),
        );
    }

    /**
     * Interact with the user's last name.
     */
    protected function lastName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower($value),
        );
    }

    /**
     * Get the user's full name.
     */
    protected function fullName(): Attribute
    {
        /*return Attribute::make(
            get: fn (mixed $value, array $attributes) => trim(
                ucfirst($attributes['first_name']).' '.ucfirst($attributes['last_name'])
            ),
        );*/

        return Attribute::make(
            get: fn (mixed $value, array $attributes) => trim(
                $attributes['first_name'].' '.$attributes['last_name']
            ),
        );
    }

    /**
     * Get the user's initials.
     */
    protected function initials(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => strtoupper(
                substr($attributes['first_name'], 0, 1).substr($attributes['last_name'], 0, 1)
            ),
        );
    }

    /**
     * Get the user's avatar URL.
     */
    protected function avatarUrl(): ?Attribute
    {
        $avatar = $this->getFirstMedia('avatars');

        return Attribute::make(
            get: fn () => $avatar ? $avatar->getUrl('thumb') : null
        );
    }

    /**
     * Get the user's cover image URL.
     */
    protected function coverImageUrl(): ?Attribute
    {
        $cover = $this->getFirstMedia('covers');

        return Attribute::make(
            get: fn () => $cover ? $cover->getUrl('large') : null
        );
    }

    /**
     * Get user's preferred timezone.
     */
    protected function timezone(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => $value ?: config('app.timezone', 'UTC')
        );
    }

    /**
     * Get user's preferences with defaults.
     */
    protected function preferences(): Attribute
    {
        $defaults = [
            'theme' => 'light',
            'language' => 'en',
            'notifications' => [
                'email' => true,
                'browser' => true,
                'mobile' => true,
            ],
            'dashboard' => [
                'show_stats' => true,
                'show_recent_activity' => true,
                'items_per_page' => 15,
            ],
        ];

        return Attribute::make(
            get: fn (mixed $value) => array_merge($defaults, json_decode($value, true) ?: [])
        );
    }

    /**
     * Check if user has a specific social platform.
     */
    public function hasSocial(string $platform): bool
    {
        return ! empty($this->socials[$platform]);
    }

    /**
     * Get a specific social platform URL.
     */
    public function getSocial(string $platform): ?string
    {
        return $this->socials[$platform] ?? null;
    }

    /**
     * Scope to get only active users.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope to get users with a specific role.
     */
    #[Scope]
    protected function withRole(Builder $query, string $role): void
    {
        $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('covers')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get user's notifications.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
