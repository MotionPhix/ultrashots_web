<?php

use App\Models\User;

test('returns a successful response', function () {
    User::withoutEvents(function () {
        User::factory()->create([
            'email' => 'hello@ultrashots.net',
            'job_title' => 'Brand strategist',
            'location' => 'Lilongwe, Malawi',
            'bio' => 'Helping ambitious teams turn visual identity into commercial lift.',
            'socials' => [
                'linkedin' => 'https://www.linkedin.com/in/ultrashots',
            ],
        ]);
    });

    $response = $this->get(route('home'));

    $response->assertSuccessful();
});
