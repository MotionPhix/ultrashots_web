<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QueryMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public array $data
    ) {
        // Set queue connection and delay if needed
        $this->onQueue('emails');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address(config('mail.from.address'), config('mail.from.name')),
            ],
            subject: 'Thank you for contacting me!',
            tags: ['feedback', 'confirmation', 'auto-reply'],
            metadata: [
                'recipient_email' => $this->data['email'],
                'recipient_name' => $this->data['name'],
                'has_company' => ! empty($this->data['company']),
                'timestamp' => now()->toISOString(),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Get logo path with fallback
        $logoPath = public_path('ultrashots_logo.png');
        if (! file_exists($logoPath)) {
            $logoPath = public_path('images/logo.png'); // Fallback
        }

        return new Content(
            view: 'emails.query-mail',
            with: [
                'name' => $this->data['name'],
                'phone' => $this->data['phone'],
                'email' => $this->data['email'],
                'company' => $this->data['company'] ?? null,
                'logo' => $logoPath,
                'timestamp' => now(),
                'app_name' => config('app.name'),
                'app_url' => config('app.url'),
                'support_email' => config('mail.from.address'),
            ],
        );
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(5);
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }
}
