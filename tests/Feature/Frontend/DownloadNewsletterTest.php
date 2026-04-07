<?php

use App\Models\Download;
use App\Models\Subscriber;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the public downloads page with only public assets', function () {
    Download::factory()->public()->create(['title' => 'Starter Brand Kit']);
    Download::factory()->private()->create(['title' => 'Internal Export']);

    $response = $this->get(route('public.downloads.index'));

    $response->assertSuccessful();
    $response->assertDontSee('Internal Export');
    $response->assertInertia(fn (Assert $page) => $page
        ->component('frontend/downloads/Index')
        ->has('downloads', 1)
        ->where('downloads.0.title', 'Starter Brand Kit')
    );
});

it('downloads a public file and increments the download count', function () {
    Storage::fake('public');

    $download = Download::factory()->public()->create([
        'title' => 'Starter Brand Kit',
        'download_count' => 0,
    ]);

    $file = UploadedFile::fake()->create('brand-kit.zip', 128, 'application/zip');
    $download->addMedia($file)->toMediaCollection('file');

    $response = $this->get(route('public.get-download', $download));

    $response->assertOk();
    $response->assertDownload('brand-kit.zip');

    expect($download->fresh()->download_count)->toBe(1);
});

it('stores a fix request upload', function () {
    Storage::fake('local');

    $file = UploadedFile::fake()->create('broken-export.zip', 512, 'application/zip');

    $response = $this->from(route('public.downloads.index'))->post(route('public.upload-file-to-fix'), [
        'name' => 'Alex Creative',
        'email' => 'alex@laravel.com',
        'notes' => 'Please clean the export, add transparent backgrounds, and include an SVG version.',
        'file' => $file,
    ]);

    $response->assertRedirect(route('public.downloads.index').'#fix-request');
    Storage::disk('local')->assertExists('fix-requests/'.$file->hashName());
});

it('subscribes a visitor to the newsletter', function () {
    $response = $this->from('/')->post(route('subscriber.enroll'), [
        'email' => 'hello@laravel.com',
    ]);

    $response->assertRedirect('/');

    $subscriber = Subscriber::query()->where('email', 'hello@laravel.com')->first();

    expect($subscriber)->not->toBeNull();
    expect($subscriber?->subscribed)->toBeTrue();
    expect($subscriber?->subscribed_at)->not->toBeNull();
});

it('confirms a pending newsletter subscription', function () {
    $subscriber = Subscriber::factory()->unconfirmed()->create([
        'email' => 'confirm@laravel.com',
    ]);

    $response = $this->get(route('subscriber.confirm', [
        'token' => $subscriber->token,
        'email' => $subscriber->email,
    ]));

    $response->assertRedirect(route('home'));

    expect($subscriber->fresh()->subscribed)->toBeTrue();
    expect($subscriber->fresh()->confirmed_at)->not->toBeNull();
});

it('unsubscribes an existing subscriber', function () {
    Subscriber::factory()->create([
        'email' => 'unsubscribe@laravel.com',
    ]);

    $response = $this->from('/')->post(route('subscriber.unsubscribe'), [
        'email' => 'unsubscribe@laravel.com',
    ]);

    $response->assertRedirect('/');

    $subscriber = Subscriber::query()->where('email', 'unsubscribe@laravel.com')->first();

    expect($subscriber)->not->toBeNull();
    expect($subscriber?->subscribed)->toBeFalse();
    expect($subscriber?->unsubscribed_at)->not->toBeNull();
});
