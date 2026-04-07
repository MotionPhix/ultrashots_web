<?php

namespace App\Http\Controllers\Frontend;

use App\Events\QuoteRequested;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuoteRequest;
use App\Services\QuoteService;
use Inertia\Inertia;
use Inertia\Response;

class QuoteController extends Controller
{
    public function __construct(private QuoteService $quoteService) {}

    /**
     * Display the quote request form
     */
    public function index(): Response
    {
        return Inertia::render('frontend/quote/Index', $this->quoteFormOptions());
    }

    /**
     * Store quote request and generate quotation
     */
    public function store(StoreQuoteRequest $request): Response
    {
        $validated = $request->validated();

        $files = $request->referenceFiles();

        $quote = $this->quoteService->createQuote($validated, $files);

        QuoteRequested::dispatch($quote);

        $quotation = $this->quoteService->generateQuote($validated);

        return Inertia::render('frontend/quote/Index', [
            'quotation' => $quotation,
            'quote_id' => $quote->id,
            ...$this->quoteFormOptions(),
        ]);
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function quoteFormOptions(): array
    {
        return [
            'projectTypes' => [
                'web_design' => 'Web Design',
                'web_development' => 'Web Development',
                'mobile_app' => 'Mobile App',
                'branding' => 'Branding',
                'ui_ux' => 'UI/UX Design',
                'e_commerce' => 'E-Commerce',
                'api_development' => 'API Development',
            ],
            'budgetRanges' => [
                'under_2k' => 'Under $2,000',
                '2k_5k' => '$2,000 - $5,000',
                '5k_10k' => '$5,000 - $10,000',
                '10k_plus' => '$10,000+',
            ],
            'timelines' => [
                'urgent' => 'Urgent (1-2 weeks)',
                'tight' => 'Tight (2-4 weeks)',
                'standard' => 'Standard (1-3 months)',
                'flexible' => 'Flexible (3+ months)',
            ],
        ];
    }
}
