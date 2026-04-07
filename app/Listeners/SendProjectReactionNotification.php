<?php

namespace App\Listeners;

use App\Actions\Notifications\HandleProjectReaction;
use App\Events\ProjectReacted;

class SendProjectReactionNotification
{
    public function __construct(private HandleProjectReaction $handleProjectReaction) {}

    public function handle(ProjectReacted $event): void
    {
        $this->handleProjectReaction->handle($event->reaction);
    }
}
