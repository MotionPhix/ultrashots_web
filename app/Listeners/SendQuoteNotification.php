<?php

namespace App\Listeners;

use App\Actions\Notifications\HandleQuoteRequested;
use App\Events\QuoteRequested;

class SendQuoteNotification
{
    public function __construct(private HandleQuoteRequested $handleQuoteRequested) {}

    public function handle(QuoteRequested $event): void
    {
        $this->handleQuoteRequested->handle($event->quote);
    }
}
