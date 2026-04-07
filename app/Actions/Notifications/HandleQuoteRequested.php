<?php

namespace App\Actions\Notifications;

use App\Mail\QuoteReceivedMail;
use App\Models\Quote;
use Illuminate\Support\Facades\Mail;

class HandleQuoteRequested
{
    public function __construct(private DispatchAdminNotifications $dispatchAdminNotifications) {}

    public function handle(Quote $quote): void
    {
        $admins = $this->dispatchAdminNotifications->handle(
            'quote_requested',
            'New Quote Request',
            "New quote request from {$quote->name} for {$quote->project_type}",
            $quote,
            [
                'quote_id' => $quote->id,
                'name' => $quote->name,
                'email' => $quote->email,
                'project_type' => $quote->project_type,
            ],
        );

        foreach ($admins as $admin) {
            if ($admin->preferences['notifications']['email'] ?? true) {
                Mail::to($admin->email)->queue(new QuoteReceivedMail($quote));
            }
        }
    }
}
