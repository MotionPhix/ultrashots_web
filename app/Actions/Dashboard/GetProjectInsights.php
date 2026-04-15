<?php

namespace App\Actions\Dashboard;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;

class GetProjectInsights
{
    public function handle(): array
    {
        return Cache::remember('dashboard.project_insights', now()->addMinutes(15), function () {
            $upcomingDeadlines = Project::where('end_date', '>=', now())
                ->where('end_date', '<=', now()->addDays(7))
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->with('customer:id,first_name,last_name')
                ->get()
                ->map(fn ($project) => [
                    'uuid' => $project->uuid,
                    'name' => $project->name,
                    'end_date' => $project->end_date->format('M j, Y'),
                    'days_left' => (int) $project->end_date->diffInDays(now()),
                    'customer' => trim(($project->customer?->first_name ?? '').' '.($project->customer?->last_name ?? '')),
                    'priority' => $project->priority,
                    'priority_color' => $project->priority_color,
                ]);

            return [
                'status_distribution' => Project::selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
                'priority_distribution' => Project::selectRaw('priority, COUNT(*) as count')
                    ->groupBy('priority')
                    ->pluck('count', 'priority'),
                'overdue_projects' => Project::where('end_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->count(),
                'upcoming_deadlines' => $upcomingDeadlines,
            ];
        });
    }
}
