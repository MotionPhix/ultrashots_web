<?php

namespace App\Actions\Notifications;

use App\Models\Reaction;

class HandleProjectReaction
{
    public function __construct(private DispatchAdminNotifications $dispatchAdminNotifications) {}

    public function handle(Reaction $reaction): void
    {
        $this->dispatchAdminNotifications->handle(
            'project_reaction',
            "New {$reaction->type} on Project",
            "Your project received a {$reaction->type} reaction from {$reaction->visitor_name}",
            $reaction,
            [
                'reaction_id' => $reaction->id,
                'project_id' => $reaction->project_id,
                'type' => $reaction->type,
                'visitor_name' => $reaction->visitor_name,
                'visitor_email' => $reaction->visitor_email,
            ],
        );
    }
}
