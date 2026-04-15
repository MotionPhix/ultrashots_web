<?php

namespace App\Actions\Dashboard;

use Illuminate\Support\Facades\Cache;

class ClearDashboardCache
{
    /** @var string[] */
    private array $cacheKeys = [
        'dashboard.stats',
        'dashboard.recent_activity',
        'dashboard.project_insights',
        'dashboard.performance_metrics',
    ];

    public function handle(): void
    {
        foreach ($this->cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}
