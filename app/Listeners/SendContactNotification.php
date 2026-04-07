<?php

namespace App\Listeners;

use App\Actions\Notifications\HandleContactSubmitted;
use App\Events\ContactFormSubmitted;

class SendContactNotification
{
    public function __construct(private HandleContactSubmitted $handleContactSubmitted) {}

    public function handle(ContactFormSubmitted $event): void
    {
        $this->handleContactSubmitted->handle($event->contact);
    }
}
