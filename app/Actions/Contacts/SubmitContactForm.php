<?php

namespace App\Actions\Contacts;

use App\Events\ContactFormSubmitted;
use App\Mail\ContactMeMail;
use App\Mail\QueryMail;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use Propaganistas\LaravelPhone\PhoneNumber;
use Tiptap\Editor as TiptapEditor;
use Tiptap\Extensions\StarterKit;

class SubmitContactForm
{
    /**
     * @param  array<string, mixed>  $validated
     */
    public function handle(array $validated): Contact
    {
        $payload = $this->payload($validated);

        $contact = Contact::create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'phone' => $payload['phone'],
            'company' => $payload['company'],
            'message' => $payload['message'],
            'status' => 'new',
        ]);

        ContactFormSubmitted::dispatch($contact);

        Mail::to('sales@ultrashots.net', 'Kingsley')->send(new ContactMeMail(
            $payload['name'],
            $payload['email'],
            $payload['phone'],
            $payload['message'],
            $payload['company']
        ));

        Mail::to($payload['email'], $payload['name'])->send(new QueryMail($payload));

        return $contact;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{name:string,email:string,phone:string,message:string,company:?string}
     */
    private function payload(array $validated): array
    {
        $phone = new PhoneNumber((string) $validated['phone']);

        return [
            'name' => (string) $validated['name'],
            'email' => (string) $validated['email'],
            'phone' => $phone->formatInternational(),
            'message' => $this->sanitizeHtml((string) $validated['message']),
            'company' => isset($validated['company']) ? (string) $validated['company'] : null,
        ];
    }

    private function sanitizeHtml(string $message): string
    {
        return (new TiptapEditor([
            'extensions' => [
                new StarterKit,
            ],
        ]))->setContent($message)->getHTML();
    }
}
