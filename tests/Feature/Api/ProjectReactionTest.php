<?php

use App\Events\ProjectReacted;
use App\Models\Project;
use App\Models\Reaction;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('admin', 'web');
});

it('stores a project reaction and dispatches the event', function () {
    Event::fake([ProjectReacted::class]);

    $project = Project::factory()->create();

    $this->postJson("/api/projects/{$project->uuid}/reactions", [
        'type' => 'recommended',
        'feedback' => 'Strong craft and clean interaction design.',
        'visitor_name' => 'Taylor',
        'visitor_email' => 'taylor@laravel.com',
    ])->assertCreated()
        ->assertJsonPath('reaction.type', 'recommended')
        ->assertJsonPath('reaction.visitor_name', 'Taylor')
        ->assertJsonPath('reaction.visitor_email', 'taylor@laravel.com');

    $reaction = Reaction::query()->where('project_id', $project->id)->first();

    expect($reaction)->not->toBeNull();

    Event::assertDispatched(ProjectReacted::class, function (ProjectReacted $event) use ($reaction) {
        return $event->reaction->is($reaction);
    });
});

it('rejects duplicate reactions from the same visitor email and type', function () {
    $project = Project::factory()->create();

    Reaction::create([
        'project_id' => $project->id,
        'type' => 'like',
        'visitor_name' => 'Alex',
        'visitor_email' => 'alex@laravel.com',
    ]);

    $this->postJson("/api/projects/{$project->uuid}/reactions", [
        'type' => 'like',
        'visitor_name' => 'Alex',
        'visitor_email' => 'alex@laravel.com',
    ])->assertStatus(409)
        ->assertJson([
            'message' => 'You have already reacted to this project',
        ]);

    expect(Reaction::query()->where('project_id', $project->id)->count())->toBe(1);
});

it('allows anonymous reactions of the same type on the same project', function () {
    $project = Project::factory()->create();

    Reaction::create([
        'project_id' => $project->id,
        'type' => 'nice',
        'visitor_name' => 'Anonymous',
        'visitor_email' => null,
    ]);

    $this->postJson("/api/projects/{$project->uuid}/reactions", [
        'type' => 'nice',
    ])->assertCreated()
        ->assertJsonPath('reaction.visitor_name', 'Anonymous')
        ->assertJsonPath('reaction.visitor_email', null);

    expect(Reaction::query()->where('project_id', $project->id)->where('type', 'nice')->count())->toBe(2);
});

it('validates reaction payloads', function () {
    $project = Project::factory()->create();

    $this->postJson("/api/projects/{$project->uuid}/reactions", [
        'type' => 'unsupported',
        'visitor_email' => 'bad-email',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['type', 'visitor_email']);

    expect(Reaction::query()->count())->toBe(0);
});
