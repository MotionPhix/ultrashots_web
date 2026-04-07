<?php

namespace App\Actions\Notifications;

use App\Models\Contact;

class HandleContactSubmitted
{
    public function __construct(private DispatchAdminNotifications $dispatchAdminNotifications) {}

    public function handle(Contact $contact): void
    {
        $this->dispatchAdminNotifications->handle(
            'contact_submitted',
            'New Contact Message',
            "New contact message from {$contact->name}",
            $contact,
            [
                'contact_id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'company' => $contact->company,
            ],
        );
    }
}
