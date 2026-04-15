<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Dashboard\ClearDashboardCache;
use App\Actions\Dashboard\GetDashboardAlerts;
use App\Actions\Dashboard\GetDashboardStats;
use App\Actions\Dashboard\GetPerformanceMetrics;
use App\Actions\Dashboard\GetProjectInsights;
use App\Actions\Dashboard\GetRecentActivity;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(
        GetDashboardStats $getStats,
        GetRecentActivity $getRecentActivity,
        GetProjectInsights $getProjectInsights,
        GetPerformanceMetrics $getPerformanceMetrics,
        GetDashboardAlerts $getAlerts,
    ): Response {
        $statsData = $getStats->handle();

        return Inertia::render('admin/Dashboard', [
            'stats' => $statsData['stats'],
            'trends' => $statsData['trends'],
            'recent_activity' => $getRecentActivity->handle(),
            'project_insights' => $getProjectInsights->handle(),
            'performance_metrics' => $getPerformanceMetrics->handle(),
            'alerts' => $getAlerts->handle(),
        ]);
    }

    public function refresh(ClearDashboardCache $clearCache)
    {
        $clearCache->handle();

        return redirect()->route('admin.dashboard')->with('success', 'Dashboard data refreshed.');
    }
}
