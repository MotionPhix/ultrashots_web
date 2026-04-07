<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        private string $name,
        private string $email,
        private string $phone,
        private string $user_query,
        private ?string $company = null,
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
            from: new Address($this->email, $this->name),
            replyTo: [
                new Address($this->email, $this->name),
            ],
            subject: "New Contact Form Submission from {$this->name}",
            tags: ['contact-form', 'inquiry'],
            metadata: [
                'sender_email' => $this->email,
                'sender_name' => $this->name,
                'has_company' => ! empty($this->company),
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
            view: 'emails.contact-me',
            with: [
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'user_query' => $this->user_query,
                'company' => $this->company,
                'logo' => $logoPath,
                'timestamp' => now(),
                'app_name' => config('app.name'),
                'app_url' => config('app.url'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(3);
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }
}
