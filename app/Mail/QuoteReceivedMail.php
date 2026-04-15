<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Quote $quote) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Quote Request from {$this->quote->name}",
        );
    }

    public function content(): Content
    {
        $logoPath = public_path('ultrashots_logo.png');
        if (! file_exists($logoPath)) {
            $logoPath = public_path('images/logo.png');
        }

        return new Content(
            view: 'emails.quote-received',
            with: [
                'quote' => $this->quote,
                'logo' => $logoPath,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
