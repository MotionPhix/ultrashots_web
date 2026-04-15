<?php

namespace App\Actions\Dashboard;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;

class GetPerformanceMetrics
{
    public function handle(): array
    {
        return Cache::remember('dashboard.performance_metrics', now()->addMinutes(15), function () {
            $totalEstimatedHours = (float) (Project::sum('estimated_hours') ?: 0);
            $totalActualHours = (float) (Project::sum('actual_hours') ?: 0);
            $totalBudget = (float) (Project::sum('budget') ?: 0);
            $projectCount = Project::count();
            $completedCount = Project::completed()->count();

            return [
                'total_estimated_hours' => $totalEstimatedHours,
                'total_actual_hours' => $totalActualHours,
                'total_budget' => $totalBudget,
                'hours_variance' => $totalActualHours - $totalEstimatedHours,
                'efficiency_rate' => $totalEstimatedHours > 0 && $totalActualHours > 0
                    ? round(($totalEstimatedHours / $totalActualHours) * 100, 1)
                    : 0,
                'average_project_duration' => $this->getAverageProjectDuration(),
                'completion_rate' => $projectCount > 0
                    ? round(($completedCount / $projectCount) * 100, 1)
                    : 0,
            ];
        });
    }

    private function getAverageProjectDuration(): float
    {
        $average = Project::query()
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get(['start_date', 'end_date'])
            ->avg(fn (Project $project): int => $project->start_date->diffInDays($project->end_date));

        return round((float) ($average ?? 0), 2);
    }
}
