<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeNewsletterRequest;
use App\Http\Requests\UnsubscribeNewsletterRequest;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(SubscribeNewsletterRequest $request): RedirectResponse
    {
        $email = Str::lower($request->string('email')->toString());

        $subscriber = Subscriber::firstOrNew(['email' => $email]);

        if ($subscriber->subscribed) {
            return back()->with('success', 'You are already subscribed to the newsletter.');
        }

        $subscriber->fill([
            'token' => Str::random(64),
            'subscribed' => true,
            'confirmed_at' => now(),
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
            'meta' => array_filter([
                'source' => 'website',
            ]),
        ])->save();

        return back()->with('success', 'You are now subscribed to the newsletter.');
    }

    public function confirm(string $token, string $email): RedirectResponse
    {
        $subscriber = Subscriber::query()
            ->where('email', Str::lower($email))
            ->where('token', $token)
            ->firstOrFail();

        $subscriber->confirm();

        return redirect()->route('home')
            ->with('success', 'Your subscription has been confirmed.');
    }

    public function unsubscribe(UnsubscribeNewsletterRequest $request): RedirectResponse
    {
        $subscriber = Subscriber::query()
            ->where('email', Str::lower($request->string('email')->toString()))
            ->first();

        if ($subscriber === null || ! $subscriber->subscribed) {
            return back()->with('info', 'That email address is not currently subscribed.');
        }

        $subscriber->unsubscribe();

        return back()->with('success', 'You have been unsubscribed from the newsletter.');
    }
}
