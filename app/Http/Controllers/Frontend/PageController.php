<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The pages that exist to be read rather than to convert: about, the product,
 * and the legal pair.
 *
 * The business details live here, once, because they are the same facts that
 * get entered into every platform that asks who we are. When they are typed in
 * three places they drift, and a drift between a website and a business
 * registration is the kind of thing a verification reviewer notices.
 */
class PageController extends Controller
{
    public function about(): Response
    {
        return Inertia::render('frontend/about/Index', [
            'business' => $this->business(),
        ]);
    }

    public function vibora(): Response
    {
        return Inertia::render('frontend/vibora/Index', [
            'business' => $this->business(),
        ]);
    }

    public function terms(): Response
    {
        return Inertia::render('frontend/legal/Terms', [
            'business' => $this->business(),
            'updated' => '6 November 2026',
        ]);
    }

    public function privacy(): Response
    {
        return Inertia::render('frontend/legal/Privacy', [
            'business' => $this->business(),
            'updated' => '6 November 2026',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function business(): array
    {
        return [
            'name' => 'Ultrashots',
            'trading_name' => 'Ultrashots',
            'address_lines' => [
                'Infinity Complex, Sir Glyn Jones Road',
                'Area 3',
                'Lilongwe',
                'Malawi',
            ],
            'phone' => '+265 888 166 911',
            'phone_local' => '0888 166 911',
            'email' => 'hello@ultrashots.net',
            'website' => 'https://ultrashots.net',
            'services' => [
                'Graphic design',
                'Printing',
                'Web development',
                'Custom software',
            ],
        ];
    }
}
