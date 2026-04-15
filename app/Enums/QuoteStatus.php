<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case Pending = 'pending';
    case Reviewed = 'reviewed';
    case Sent = 'sent';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            QuoteStatus::Pending => 'Pending',
            QuoteStatus::Reviewed => 'Reviewed',
            QuoteStatus::Sent => 'Sent',
            QuoteStatus::Accepted => 'Accepted',
            QuoteStatus::Rejected => 'Rejected',
        };
    }
}
