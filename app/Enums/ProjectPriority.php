<?php

namespace App\Enums;

enum ProjectPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            ProjectPriority::Low => 'Low',
            ProjectPriority::Medium => 'Medium',
            ProjectPriority::High => 'High',
            ProjectPriority::Critical => 'Critical',
        };
    }

    public function color(): string
    {
        return match ($this) {
            ProjectPriority::Low => 'green',
            ProjectPriority::Medium => 'yellow',
            ProjectPriority::High => 'orange',
            ProjectPriority::Critical => 'red',
        };
    }
}
