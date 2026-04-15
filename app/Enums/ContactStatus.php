<?php

namespace App\Enums;

enum ContactStatus: string
{
    case New = 'new';
    case Read = 'read';
    case Responded = 'responded';

    public function label(): string
    {
        return match ($this) {
            ContactStatus::New => 'New',
            ContactStatus::Read => 'Read',
            ContactStatus::Responded => 'Responded',
        };
    }
}
