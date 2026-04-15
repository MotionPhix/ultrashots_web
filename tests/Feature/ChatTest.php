<?php

use App\Ai\Agents\BrandConsultant;
use App\Ai\Tools\ScheduleBooking;
use App\Events\QuoteRequested;
use Illuminate\Support\Facades\Event;
use Laravel\Ai\Tools\Request;

it('sends a chat message and gets a response', function () {
    BrandConsultant::fake(['Hi there, tell me about your brand challenge.']);

    $response = $this->postJson(route('chat.send'), [
        'message' => 'Hello',
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure(['message', 'conversation_id']);
    expect($response->json('conversation_id'))->not->toBeNull();
});

it('continues an existing conversation', function () {
    BrandConsultant::fake([
        'Tell me about your brand.',
        'Great, a rebrand is a big opportunity.',
    ]);

    $first = $this->postJson(route('chat.send'), [
        'message' => 'Hello',
    ]);

    $first->assertSuccessful();
    $conversationId = $first->json('conversation_id');

    $second = $this->postJson(route('chat.send'), [
        'message' => 'We need a rebrand',
        'conversation_id' => $conversationId,
    ]);

    $second->assertSuccessful();
    expect($second->json('conversation_id'))->toBe($conversationId);
});

it('validates the message field is required', function () {
    $response = $this->postJson(route('chat.send'), []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['message']);
});

it('validates message does not exceed 2000 characters', function () {
    $response = $this->postJson(route('chat.send'), [
        'message' => str_repeat('a', 2001),
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['message']);
});

it('schedule booking tool persists a quote and dispatches event', function () {
    Event::fake([QuoteRequested::class]);

    $tool = new ScheduleBooking;
    $request = new Request([
        'client_name' => 'John Doe',
        'client_email' => 'john@example.com',
        'project_type' => 'Branding',
        'preferred_date' => 'April 20, 2026',
        'preferred_time' => '10:00 AM',
    ]);

    $result = $tool->handle($request);

    expect((string) $result)->toContain('john@example.com');

    $this->assertDatabaseHas('quotes', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'project_type' => 'Branding',
        'timeline' => 'April 20, 2026 at 10:00 AM',
        'status' => 'pending',
    ]);

    Event::assertDispatched(QuoteRequested::class);
});

it('schedule booking tool handles missing preferred date gracefully', function () {
    Event::fake([QuoteRequested::class]);

    $tool = new ScheduleBooking;
    $request = new Request([
        'client_name' => 'Jane Smith',
        'client_email' => 'jane@example.com',
        'project_type' => 'Web Design',
    ]);

    $tool->handle($request);

    $this->assertDatabaseHas('quotes', [
        'email' => 'jane@example.com',
        'timeline' => 'Flexible',
    ]);
});
