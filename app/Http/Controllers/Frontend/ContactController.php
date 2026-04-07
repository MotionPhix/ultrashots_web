<?php

namespace App\Http\Controllers\Frontend;

use App\Actions\Contacts\SubmitContactForm;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactQueryRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ContactController extends Controller
{
    public function __construct(private SubmitContactForm $submitContactForm) {}

    /**
     * Display the contact form.
     */
    public function load(): Response
    {
        return Inertia::render('frontend/contact/Index');
    }

    public function send(StoreContactQueryRequest $request): RedirectResponse
    {
        try {
            $this->submitContactForm->handle($request->validated());
        } catch (Throwable $exception) {
            report($exception);

            return back()->with(
                'error',
                'Your message was received, but we could not send email notifications right now.'
            );
        }

        return back()->with(
            'success',
            'Your message was successfully delivered.'
        );
    }
}
