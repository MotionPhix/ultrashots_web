<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LandingPageContentService;
use Illuminate\Http\JsonResponse;

class LandingContentController extends Controller
{
    public function __construct(private LandingPageContentService $landingPageContentService) {}

    public function caseStudies(): JsonResponse
    {
        return response()->json([
            'data' => $this->landingPageContentService->caseStudies(),
        ]);
    }

    public function testimonials(): JsonResponse
    {
        return response()->json([
            'data' => $this->landingPageContentService->testimonials(),
        ]);
    }
}
