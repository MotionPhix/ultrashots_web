<?php

use App\Actions\Notifications\HandleContactSubmitted;
use App\Actions\Notifications\HandleProjectReaction;
use App\Actions\Notifications\HandleQuoteRequested;
use App\Mail\QuoteReceivedMail;
use App\Models\Contact;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('admin', 'web');
});

it('creates quote notifications for all admins and queues mail only for opted in admins', function () {
    Mail::fake();

    $emailAdmin = User::factory()->create([
        'preferences' => ['notifications' => ['email' => true]],
    ]);
    $emailAdmin->assignRole('admin');

    $silentAdmin = User::factory()->create([
        'preferences' => ['notifications' => ['email' => false]],
    ]);
    $silentAdmin->assignRole('admin');

    $quote = Quote::create([
        'name' => 'Jordan Smith',
        'email' => 'jordan@laravel.com',
        'phone' => '+265991234567',
        'company' => 'Ultrashots',
        'project_type' => 'web_design',
        'budget_range' => '5k_10k',
        'timeline' => 'standard',
        'description' => 'We need a strong marketing site and portfolio refresh for the next launch.',
        'goals' => 'Increase lead quality',
        'target_audience' => 'Founders',
        'additional_info' => 'Launch is in Q3.',
        'status' => 'pending',
    ]);

    app(HandleQuoteRequested::class)->handle($quote);

    expect(Notification::query()->where('type', 'quote_requested')->count())->toBe(2);

    $notification = Notification::query()
        ->where('user_id', $emailAdmin->id)
        ->where('type', 'quote_requested')
        ->first();

    expect($notification)->not->toBeNull();
    expect($notification->title)->toBe('New Quote Request');
    expect($notification->notifiable_type)->toBe(Quote::class);
    expect($notification->notifiable_id)->toBe($quote->id);
    expect($notification->data)->toMatchArray([
        'quote_id' => $quote->id,
        'name' => 'Jordan Smith',
        'email' => 'jordan@laravel.com',
        'project_type' => 'web_design',
    ]);

    Mail::assertQueued(QuoteReceivedMail::class, function (QuoteReceivedMail $mail) use ($emailAdmin): bool {
        return $mail->hasTo($emailAdmin->email);
    });

    Mail::assertNotQueued(QuoteReceivedMail::class, function (QuoteReceivedMail $mail) use ($silentAdmin): bool {
        return $mail->hasTo($silentAdmin->email);
    });
});

it('creates contact notifications for admins', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $contact = Contact::create([
        'name' => 'John Banda',
        'email' => 'john@laravel.com',
        'phone' => '+265991234567',
        'company' => 'Ultrashots',
        'message' => '<p>Hello</p>',
        'status' => 'new',
    ]);

    app(HandleContactSubmitted::class)->handle($contact);

    $notification = Notification::query()
        ->where('user_id', $admin->id)
        ->where('type', 'contact_submitted')
        ->first();

    expect($notification)->not->toBeNull();
    expect($notification->title)->toBe('New Contact Message');
    expect($notification->notifiable_type)->toBe(Contact::class);
    expect($notification->notifiable_id)->toBe($contact->id);
    expect($notification->data)->toMatchArray([
        'contact_id' => $contact->id,
        'name' => 'John Banda',
        'email' => 'john@laravel.com',
        'company' => 'Ultrashots',
    ]);
});

it('creates reaction notifications for admins', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $project = Project::factory()->create();

    $reaction = Reaction::create([
        'project_id' => $project->id,
        'type' => 'recommended',
        'feedback' => 'Strong craft and clean interaction design.',
        'visitor_name' => 'Taylor',
        'visitor_email' => 'taylor@laravel.com',
    ]);

    app(HandleProjectReaction::class)->handle($reaction);

    $notification = Notification::query()
        ->where('user_id', $admin->id)
        ->where('type', 'project_reaction')
        ->first();

    expect($notification)->not->toBeNull();
    expect($notification->title)->toBe('New recommended on Project');
    expect($notification->notifiable_type)->toBe(Reaction::class);
    expect($notification->notifiable_id)->toBe($reaction->id);
    expect($notification->data)->toMatchArray([
        'reaction_id' => $reaction->id,
        'project_id' => $project->id,
        'type' => 'recommended',
        'visitor_name' => 'Taylor',
        'visitor_email' => 'taylor@laravel.com',
    ]);
});
