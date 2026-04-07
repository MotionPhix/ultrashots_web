<?php

namespace App\Providers;

use App\Events\ContactFormSubmitted;
use App\Events\ProjectReacted;
use App\Events\QuoteRequested;
use App\Listeners\SendContactNotification;
use App\Listeners\SendProjectReactionNotification;
use App\Listeners\SendQuoteNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        QuoteRequested::class => [
            SendQuoteNotification::class,
        ],
        ContactFormSubmitted::class => [
            SendContactNotification::class,
        ],
        ProjectReacted::class => [
            SendProjectReactionNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
