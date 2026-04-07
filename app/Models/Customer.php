<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone_number',
        'contact_person',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'status',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Customer $customer): void {
            if (blank($customer->uuid)) {
                $customer->uuid = (string) Str::orderedUuid();
            }
        });

        static::saving(function (Customer $customer): void {
            if ($customer->email !== null) {
                $customer->email = Str::lower($customer->email);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the full name of the customer.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->company_name ?: $this->full_name ?: 'Untitled Customer';
    }

    /**
     * Get the projects for this customer.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Scope to get active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get inactive customers.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope to get pending customers.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
