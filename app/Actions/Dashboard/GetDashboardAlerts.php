<?php

namespace App\Actions\Dashboard;

use App\Models\Project;

class GetDashboardAlerts
{
    public function handle(): array
    {
        $alerts = [];

        $overdueCount = Project::where('end_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        if ($overdueCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Overdue Projects',
                'message' => "{$overdueCount} project(s) are overdue and need attention",
                'route' => 'admin.projects.index',
                'action_text' => 'View Projects',
            ];
        }

        $upcomingCount = Project::where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(3))
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        if ($upcomingCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Upcoming Deadlines',
                'message' => "{$upcomingCount} project(s) due within 3 days",
                'route' => 'admin.projects.index',
                'action_text' => 'View Projects',
            ];
        }

        return $alerts;
    }
}
