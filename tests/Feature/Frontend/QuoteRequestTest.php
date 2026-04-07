<?php

use App\Events\QuoteRequested;
use App\Models\Quote;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

it('stores a quote request, uploads reference files, and returns a quotation payload', function () {
    Event::fake([QuoteRequested::class]);
    Storage::fake('public');

    config()->set('media-library.disk_name', 'public');

    $response = $this->post(route('quote.store'), [
        'name' => 'Alex Creative',
        'email' => 'alex@laravel.com',
        'phone' => '+265991234567',
        'company' => 'Ultrashots',
        'project_type' => 'web_development',
        'budget_range' => '5k_10k',
        'timeline' => 'standard',
        'description' => 'Build a client portal with reporting, authentication, dashboards, and a polished admin workflow.',
        'goals' => 'Improve internal operations and reporting.',
        'target_audience' => 'Operations and account teams.',
        'additional_info' => 'Needs a production rollout plan.',
        'reference_files' => [
            UploadedFile::fake()->image('wireframe.png'),
        ],
    ]);

    $quote = Quote::query()->where('email', 'alex@laravel.com')->first();

    expect($quote)->not->toBeNull();
    expect($quote?->status)->toBe('pending');
    expect($quote?->getMedia('reference_materials'))->toHaveCount(1);

    $response->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('frontend/quote/Index')
            ->where('quote_id', $quote?->id)
            ->has('quotation')
        );

    Event::assertDispatched(QuoteRequested::class, function (QuoteRequested $event) use ($quote) {
        return $event->quote->is($quote);
    });
});

it('rejects invalid quote request payloads', function () {
    Storage::fake('public');

    config()->set('media-library.disk_name', 'public');

    $response = $this->from(route('quote.index'))->post(route('quote.store'), [
        'name' => '',
        'email' => 'not-an-email',
        'project_type' => 'invalid-type',
        'timeline' => 'not-real',
        'description' => 'too short',
        'reference_files' => [
            UploadedFile::fake()->create('payload.exe', 16, 'application/octet-stream'),
        ],
    ]);

    $response->assertRedirect(route('quote.index'))
        ->assertSessionHasErrors(['name', 'email', 'project_type', 'timeline', 'description', 'reference_files.0']);

    expect(Quote::query()->count())->toBe(0);
});
