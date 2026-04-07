<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
    ) {}

    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        $dashboardData = $this->dashboardService->getDashboardData();
        $notifications = $this->dashboardService->getNotifications();

        return Inertia::render('admin/Dashboard', $dashboardData + [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Refresh dashboard data (clear cache).
     */
    public function refresh()
    {
        $this->dashboardService->clearCache();

        return redirect()->route('admin.dashboard')->with('success', 'Dashboard data refreshed successfully.');
    }
}
