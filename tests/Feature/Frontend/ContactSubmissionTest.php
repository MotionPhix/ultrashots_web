<?php

use App\Events\ContactFormSubmitted;
use App\Mail\ContactMeMail;
use App\Mail\QueryMail;
use App\Models\Contact;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

it('stores the contact message, dispatches the event, and sends both emails', function () {
    Event::fake([ContactFormSubmitted::class]);
    Mail::fake();

    $payload = [
        'name' => 'John Banda',
        'email' => 'john@laravel.com',
        'phone' => '+265991234567',
        'company' => 'Ultrashots',
        'message' => '<p>I need help planning a portfolio redesign and a fresh brand system for my business.</p>',
    ];

    $response = $this->from(route('contact.load'))->post(route('contact.send'), $payload);

    $response->assertRedirect(route('contact.load'));
    $response->assertSessionHas('success', 'Your message was successfully delivered.');

    $contact = Contact::query()->where('email', $payload['email'])->first();

    expect($contact)->not->toBeNull();
    expect($contact->status)->toBe('new');
    expect($contact->message)->toContain('<p>')
        ->toContain('portfolio redesign');

    Event::assertDispatched(ContactFormSubmitted::class, function (ContactFormSubmitted $event) use ($payload) {
        return $event->contact->email === $payload['email'];
    });

    Mail::assertSent(ContactMeMail::class, function (ContactMeMail $mail) {
        return $mail->hasTo('sales@ultrashots.net');
    });

    Mail::assertSent(QueryMail::class, function (QueryMail $mail) use ($payload) {
        return $mail->hasTo($payload['email']);
    });
});

it('persists the message and surfaces a clear error when mail delivery fails', function () {
    Event::fake([ContactFormSubmitted::class]);

    Mail::partialMock()
        ->shouldReceive('to')
        ->once()
        ->with('sales@ultrashots.net', 'Kingsley')
        ->andReturn(new class
        {
            public function send(mixed $mailable): void
            {
                throw new RuntimeException('SMTP unavailable');
            }
        });

    $payload = [
        'name' => 'John Banda',
        'email' => 'john@laravel.com',
        'phone' => '+265991234567',
        'company' => 'Ultrashots',
        'message' => '<p>I need help planning a portfolio redesign and a fresh brand system for my business.</p>',
    ];

    $response = $this->from(route('contact.load'))->post(route('contact.send'), $payload);

    $response->assertRedirect(route('contact.load'));
    $response->assertSessionHas('error', 'Your message was received, but we could not send email notifications right now.');

    $contact = Contact::query()->where('email', $payload['email'])->first();

    expect($contact)->not->toBeNull();
    expect($contact->status)->toBe('new');

    Event::assertDispatched(ContactFormSubmitted::class, function (ContactFormSubmitted $event) use ($payload) {
        return $event->contact->email === $payload['email'];
    });
});
