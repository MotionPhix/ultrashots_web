<?php

namespace App\Events;

use App\Models\Reaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectReacted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Reaction $reaction) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin-notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'project.reacted';
    }

    public function broadcastWith(): array
    {
        return [
            'reaction_id' => $this->reaction->id,
            'project_id' => $this->reaction->project_id,
            'type' => $this->reaction->type,
            'visitor_name' => $this->reaction->visitor_name,
            'visitor_email' => $this->reaction->visitor_email,
        ];
    }
}
