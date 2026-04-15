<?php

namespace App\Enums;

enum CustomerStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            CustomerStatus::Active => 'Active',
            CustomerStatus::Inactive => 'Inactive',
            CustomerStatus::Pending => 'Pending',
        };
    }
}
