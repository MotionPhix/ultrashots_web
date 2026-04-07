<?php

namespace App\Services;

use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteService
{
    /**
     * Base pricing structure (in USD)
     */
    private const BASE_RATES = [
        'web_design' => 2500,
        'web_development' => 4000,
        'mobile_app' => 6000,
        'branding' => 1500,
        'ui_ux' => 2000,
        'e_commerce' => 5000,
        'api_development' => 3500,
    ];

    /**
     * Complexity multipliers based on timeline
     */
    private const TIMELINE_MULTIPLIERS = [
        'urgent' => 1.5,        // 1-2 weeks
        'tight' => 1.25,        // 2-4 weeks
        'standard' => 1.0,      // 1-3 months
        'flexible' => 0.85,     // 3+ months
    ];

    /**
     * Budget range thresholds
     */
    private const BUDGET_RANGES = [
        'under_2k' => [500, 2000],
        '2k_5k' => [2000, 5000],
        '5k_10k' => [5000, 10000],
        '10k_plus' => [10000, null],
    ];

    /**
     * Feature complexity scoring
     */
    private const FEATURE_SCORES = [
        'payment_gateway' => 800,
        'user_authentication' => 500,
        'api_integration' => 600,
        'database_design' => 400,
        'real_time_features' => 1000,
        'mobile_responsive' => 300,
        'seo_optimization' => 400,
        'analytics' => 300,
        'custom_cms' => 1200,
        'third_party_integration' => 600,
    ];

    /**
     * Generate a quotation based on request data
     */
    public function generateQuote(array $data): array
    {
        $projectType = $data['project_type'] ?? 'web_design';
        $timeline = $data['timeline'] ?? 'standard';
        $description = $data['description'] ?? '';

        // Calculate base price
        $basePrice = $this->getBasePrice($projectType);

        // Apply timeline multiplier (affects labor cost)
        $timelineMultiplier = self::TIMELINE_MULTIPLIERS[$timeline] ?? 1.0;
        $laborCost = $basePrice * $timelineMultiplier;

        // Analyze description for complexity factors
        $complexityScore = $this->analyzeComplexity($description);
        $additionalCost = $this->calculateAdditionalCost($complexityScore);

        // Calculate subtotal
        $subtotal = $laborCost + $additionalCost;

        // Add contingency (15% buffer for unknowns)
        $contingency = $subtotal * 0.15;

        // Final total
        $total = $subtotal + $contingency;

        // Round to nearest 500
        $total = round($total / 500) * 500;

        return [
            'base_price' => round($basePrice),
            'timeline_multiplier' => $timelineMultiplier,
            'labor_cost' => round($laborCost),
            'complexity_score' => $complexityScore,
            'additional_cost' => round($additionalCost),
            'subtotal' => round($subtotal),
            'contingency' => round($contingency),
            'total_price' => round($total),
            'timeline_estimate' => $this->estimateTimeline($projectType, $timeline, $complexityScore),
            'breakdown' => [
                'labor' => round($laborCost),
                'features' => round($additionalCost),
                'contingency' => round($contingency),
            ],
        ];
    }

    /**
     * Get base price for project type
     */
    private function getBasePrice(string $projectType): float
    {
        return self::BASE_RATES[$projectType] ?? self::BASE_RATES['web_design'];
    }

    /**
     * Analyze complexity from description
     */
    private function analyzeComplexity(string $description): int
    {
        $score = 0;
        $description = strtolower($description);

        foreach (self::FEATURE_SCORES as $feature => $points) {
            // Convert feature name to keywords (e.g., 'payment_gateway' -> 'payment', 'gateway')
            $keywords = explode('_', $feature);

            foreach ($keywords as $keyword) {
                if (str_contains($description, $keyword)) {
                    $score += $points;
                    break; // Count feature once
                }
            }
        }

        // Boost score if description is detailed (long descriptions usually mean complex projects)
        $wordCount = str_word_count($description);
        if ($wordCount > 100) {
            $score += 300;
        } elseif ($wordCount > 50) {
            $score += 150;
        }

        return $score;
    }

    /**
     * Calculate additional cost based on complexity
     */
    private function calculateAdditionalCost(int $complexityScore): float
    {
        // $100 per complexity point
        return $complexityScore * 100;
    }

    /**
     * Estimate timeline in weeks
     */
    private function estimateTimeline(string $projectType, string $timeline, int $complexityScore): array
    {
        $baseWeeks = [
            'web_design' => 3,
            'web_development' => 8,
            'mobile_app' => 12,
            'branding' => 2,
            'ui_ux' => 4,
            'e_commerce' => 10,
            'api_development' => 5,
        ];

        $weeks = $baseWeeks[$projectType] ?? 6;

        // Adjust for timeline preference
        $timelineAdjustments = [
            'urgent' => 0.5,
            'tight' => 0.75,
            'standard' => 1.0,
            'flexible' => 1.5,
        ];

        $weeks *= $timelineAdjustments[$timeline] ?? 1.0;

        // Add weeks for complexity (1 week per 1000 complexity points)
        $weeks += ceil($complexityScore / 1000);

        $minWeeks = max(1, ceil($weeks * 0.8));
        $maxWeeks = ceil($weeks * 1.2);

        return [
            'estimate_weeks' => ceil($weeks),
            'min_weeks' => $minWeeks,
            'max_weeks' => $maxWeeks,
        ];
    }

    /**
     * Create a quote from request
     */
    public function createQuote(array $data, array $files = []): Quote
    {
        $quote = Quote::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'project_type' => $data['project_type'],
            'budget_range' => $data['budget_range'] ?? null,
            'timeline' => $data['timeline'],
            'description' => $data['description'],
            'goals' => $data['goals'] ?? null,
            'target_audience' => $data['target_audience'] ?? null,
            'additional_info' => $data['additional_info'] ?? null,
            'status' => 'pending',
        ]);

        // Upload reference materials if provided
        if (! empty($files)) {
            $this->uploadReferenceFiles($quote, $files);
        }

        return $quote;
    }

    /**
     * Upload reference materials for a quote
     */
    public function uploadReferenceFiles(Quote $quote, array $files): void
    {
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $quote->addMedia($file)
                    ->toMediaCollection('reference_materials');
            }
        }
    }

    /**
     * Get quote with calculated price
     */
    public function getQuoteWithCalculation(Quote $quote): array
    {
        $quote = $quote->toArray();
        $calculation = $this->generateQuote($quote);

        return array_merge($quote, $calculation);
    }
}
