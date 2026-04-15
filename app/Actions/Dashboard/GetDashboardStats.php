<?php

namespace App\Actions\Dashboard;

use App\Models\Customer;
use App\Models\Download;
use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Cache;

class GetDashboardStats
{
    public function handle(): array
    {
        return Cache::remember('dashboard.stats', now()->addMinutes(15), function () {
            return [
                'stats' => $this->getCounts(),
                'trends' => $this->getTrends(),
            ];
        });
    }

    private function getCounts(): array
    {
        return [
            'customers_count' => Customer::count(),
            'projects_count' => Project::count(),
            'downloads_count' => Download::count(),
            'subscribers_count' => Subscriber::count(),
        ];
    }

    private function getTrends(): array
    {
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();

        $projectCounts = $this->dailyCountMap(Project::query(), $startDate, $endDate);
        $customerCounts = $this->dailyCountMap(Customer::query(), $startDate, $endDate);
        $subscriberCounts = $this->dailyCountMap(
            Subscriber::query()->where('subscribed', true),
            $startDate,
            $endDate,
        );

        $daily = collect(range(29, 0))->map(function ($daysAgo) use ($customerCounts, $projectCounts, $subscriberCounts) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');

            return [
                'date' => $date,
                'label' => now()->subDays($daysAgo)->format('M j'),
                'projects' => $projectCounts[$date] ?? 0,
                'customers' => $customerCounts[$date] ?? 0,
                'subscribers' => $subscriberCounts[$date] ?? 0,
            ];
        });

        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        $previousMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = now()->subMonthNoOverflow()->endOfMonth();

        $current = [
            'projects' => $this->countBetween(Project::query(), $currentMonthStart, $currentMonthEnd),
            'customers' => $this->countBetween(Customer::query(), $currentMonthStart, $currentMonthEnd),
            'subscribers' => $this->countBetween(Subscriber::query()->where('subscribed', true), $currentMonthStart, $currentMonthEnd),
        ];

        $previous = [
            'projects' => $this->countBetween(Project::query(), $previousMonthStart, $previousMonthEnd),
            'customers' => $this->countBetween(Customer::query(), $previousMonthStart, $previousMonthEnd),
            'subscribers' => $this->countBetween(Subscriber::query()->where('subscribed', true), $previousMonthStart, $previousMonthEnd),
        ];

        return [
            'daily' => $daily,
            'monthly_comparison' => [
                'current' => $current,
                'previous' => $previous,
                'changes' => [
                    'projects' => $this->percentageChange($previous['projects'], $current['projects']),
                    'customers' => $this->percentageChange($previous['customers'], $current['customers']),
                    'subscribers' => $this->percentageChange($previous['subscribers'], $current['subscribers']),
                ],
            ],
        ];
    }

    /**
     * @return array<string, int>
     */
    private function dailyCountMap($query, $startDate, $endDate): array
    {
        return $query
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as aggregate_date, COUNT(*) as aggregate_count')
            ->groupByRaw('DATE(created_at)')
            ->pluck('aggregate_count', 'aggregate_date')
            ->map(fn ($count): int => (int) $count)
            ->all();
    }

    private function countBetween($query, $startDate, $endDate): int
    {
        return (int) $query->whereBetween('created_at', [$startDate, $endDate])->count();
    }

    private function percentageChange(int|float $previous, int|float $current): float
    {
        if ($previous == 0 && $current == 0) {
            return 0;
        }

        if ($previous == 0) {
            return 100;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
