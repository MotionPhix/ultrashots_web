<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case OnHold = 'on_hold';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            ProjectStatus::NotStarted => 'Not Started',
            ProjectStatus::InProgress => 'In Progress',
            ProjectStatus::Completed => 'Completed',
            ProjectStatus::OnHold => 'On Hold',
            ProjectStatus::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            ProjectStatus::NotStarted => 'gray',
            ProjectStatus::InProgress => 'blue',
            ProjectStatus::OnHold => 'yellow',
            ProjectStatus::Completed => 'green',
            ProjectStatus::Cancelled => 'red',
        };
    }
}
