<?php

namespace App\Events;

use App\Models\Quote;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuoteRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Quote $quote) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin-notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'quote.requested';
    }

    public function broadcastWith(): array
    {
        return [
            'quote_id' => $this->quote->id,
            'name' => $this->quote->name,
            'email' => $this->quote->email,
            'project_type' => $this->quote->project_type,
        ];
    }
}
