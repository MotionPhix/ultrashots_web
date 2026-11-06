<?php

use Inertia\Testing\AssertableInertia as Assert;

/**
 * The pages that exist to be read: about, the product, and the legal pair.
 *
 * They matter beyond the site being complete. These are the pages a business
 * verification reviewer opens, and the details on /about are the ones compared
 * against the registered entity, so a silent regression here is more expensive
 * than a broken layout.
 */
it('serves the read-only pages', function (string $uri, string $component) {
    $this->get($uri)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($component));
})->with([
    ['/about', 'frontend/about/Index'],
    ['/vibora', 'frontend/vibora/Index'],
    ['/terms', 'frontend/legal/Terms'],
    ['/privacy', 'frontend/legal/Privacy'],
]);

it('publishes the registered business details on the about page', function () {
    $this->get('/about')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('business.trading_name', 'Ultrashots')
            ->where('business.email', 'hello@ultrashots.net')
            ->where('business.phone', '+265 888 166 911')
            ->where('business.address_lines', [
                'Infinity Complex, Sir Glyn Jones Road',
                'Area 3',
                'Lilongwe',
                'Malawi',
            ])
        );
});

it('states what the business actually does, and its product', function () {
    $this->get('/about')
        ->assertInertia(fn (Assert $page) => $page
            ->where('business.services', [
                'Graphic design',
                'Printing',
                'Web development',
                'Custom software',
            ])
        );

    $this->get('/vibora')
        ->assertInertia(fn (Assert $page) => $page
            ->where('business.name', 'Ultrashots')
        );
});

it('answers an unknown url with a 404 rather than a page that claims to exist', function () {
    $this->get('/this-page-does-not-exist')
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page->component('NotFound'));
});

it('destroys nothing by being reachable without an account', function (string $uri) {
    // A reviewer is not signed in. If any of these started redirecting to the
    // login screen, the whole point of them would be lost.
    $this->get($uri)->assertOk();
})->with(['/about', '/vibora', '/terms', '/privacy']);
