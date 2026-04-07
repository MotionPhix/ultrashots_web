<?php

use App\Actions\Contacts\SubmitContactForm;
use App\Events\ContactFormSubmitted;
use App\Mail\ContactMeMail;
use App\Mail\QueryMail;
use App\Models\Contact;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

it('persists a contact submission and sends both notification emails', function () {
    Event::fake([ContactFormSubmitted::class]);
    Mail::fake();

    $contact = app(SubmitContactForm::class)->handle([
        'name' => 'John Banda',
        'email' => 'john@laravel.com',
        'phone' => '+265991234567',
        'company' => 'Ultrashots',
        'message' => '<p>I need help planning a portfolio redesign.</p>',
    ]);

    expect($contact->exists)->toBeTrue();
    expect($contact->status)->toBe('new');
    expect($contact->phone)->toContain('+265');
    expect($contact->message)->toContain('<p>')
        ->toContain('portfolio redesign');

    expect(Contact::query()->whereKey($contact->id)->exists())->toBeTrue();

    Event::assertDispatched(ContactFormSubmitted::class, function (ContactFormSubmitted $event) use ($contact) {
        return $event->contact->is($contact);
    });

    Mail::assertSent(ContactMeMail::class, function (ContactMeMail $mail): bool {
        return $mail->hasTo('sales@ultrashots.net');
    });

    Mail::assertSent(QueryMail::class, function (QueryMail $mail) use ($contact): bool {
        return $mail->hasTo($contact->email);
    });
});
