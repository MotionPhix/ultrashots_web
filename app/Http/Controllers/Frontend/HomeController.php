<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LandingPageContentService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(private LandingPageContentService $landingPageContentService) {}

    public function index(): Response
    {
        $owner = User::query()
            ->select([
                'id',
                'first_name',
                'last_name',
                'job_title',
                'location',
                'bio',
                'socials',
                'email',
                'created_at',
                'updated_at',
                'email_verified_at',
            ])
            ->where('email', 'hello@ultrashots.net')
            ->first();

        return Inertia::render('Welcome', $this->landingPageContentService->pagePayload($owner));
    }
}
