<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Customer;
use App\Models\Download;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardService
{
  protected ProjectService $projectService;
  protected CustomerService $customerService;

  public function __construct(ProjectService $projectService, CustomerService $customerService)
  {
    $this->projectService = $projectService;
    $this->customerService = $customerService;
  }

  /**
   * Get cached dashboard data.
   */
  public function getDashboardData(): array
  {
    return Cache::remember('dashboard_data', now()->addMinutes(15), function () {
      return $this->generateDashboardData();
    });
  }

  /**
   * Generate fresh dashboard data.
   */
  private function generateDashboardData(): array
  {
    return [
      'stats' => $this->getBasicStats(),
      'project_stats' => $this->projectService->getProjectStats(),
      'customer_stats' => $this->customerService->getCustomerStats(),
      'recent_activity' => $this->getRecentActivity(),
      'trends' => $this->getTrends(),
      'project_insights' => $this->getProjectInsights(),
      'performance_metrics' => $this->getPerformanceMetrics(),
      'quick_actions' => $this->getQuickActions(),
    ];
  }

  /**
   * Get basic statistics.
   */
  private function getBasicStats(): array
  {
    return [
      'customers_count' => Customer::count(),
      'projects_count' => Project::count(),
      'downloads_count' => Download::count(),
      'subscribers_count' => Subscriber::where('subscribed', true)->count(),
    ];
  }

  /**
   * Get recent activity with optimized queries.
   */
  private function getRecentActivity(): array
  {
    $recentCustomers = Customer::latest()
      ->select(['uuid', 'first_name', 'last_name', 'company_name', 'created_at', 'status'])
      ->take(5)
      ->get()
      ->map(function ($customer) {
        return [
          'uuid' => $customer->uuid,
          'name' => trim($customer->first_name . ' ' . $customer->last_name),
          'company' => $customer->company_name,
          'status' => $customer->status,
          'created_at' => $customer->created_at->diffForHumans(),
          'avatar' => $this->generateAvatar($customer->first_name, $customer->last_name),
        ];
      });

    $recentProjects = Project::with('customer:id,first_name,last_name,company_name')
      ->latest()
      ->take(5)
      ->get()
      ->map(function ($project) {
        return [
          'uuid' => $project->uuid,
          'name' => $project->name,
          'status' => $project->status,
          'priority' => $project->priority,
          'progress' => $project->progress,
          'customer' => [
            'name' => trim($project->customer->first_name . ' ' . $project->customer->last_name),
            'company' => $project->customer->company_name,
          ],
          'created_at' => $project->created_at->diffForHumans(),
          'status_color' => $project->status_color,
          'priority_color' => $project->priority_color,
        ];
      });

    // Combined timeline
    $timeline = collect()
      ->merge($recentCustomers->map(fn($customer) => [
        'type' => 'customer',
        'title' => "New customer: {$customer['name']}",
        'subtitle' => $customer['company'],
        'time' => $customer['created_at'],
        'icon' => 'user-plus',
        'color' => 'blue',
      ]))
      ->merge($recentProjects->map(fn($project) => [
        'type' => 'project',
        'title' => "New project: {$project['name']}",
        'subtitle' => "for {$project['customer']['name']}",
        'time' => $project['created_at'],
        'icon' => 'folder-plus',
        'color' => 'green',
      ]))
      ->sortByDesc('time')
      ->take(10)
      ->values();

    return [
      'customers' => $recentCustomers,
      'projects' => $recentProjects,
      'timeline' => $timeline,
    ];
  }

  /**
   * Get trends data for charts.
   */
  private function getTrends(): array
  {
    $last30Days = collect(range(29, 0))->map(function ($daysAgo) {
      $date = now()->subDays($daysAgo);
      return [
        'date' => $date->format('Y-m-d'),
        'label' => $date->format('M j'),
        'projects' => Project::whereDate('created_at', $date)->count(),
        'customers' => Customer::whereDate('created_at', $date)->count(),
        'subscribers' => Subscriber::whereDate('created_at', $date)->where('subscribed', true)->count(),
      ];
    });

    // Monthly comparison
    $currentMonth = [
      'projects' => Project::whereMonth('created_at', now()->month)->count(),
      'customers' => Customer::whereMonth('created_at', now()->month)->count(),
      'subscribers' => Subscriber::whereMonth('created_at', now()->month)->where('subscribed', true)->count(),
    ];

    $previousMonth = [
      'projects' => Project::whereMonth('created_at', now()->subMonth()->month)->count(),
      'customers' => Customer::whereMonth('created_at', now()->subMonth()->month)->count(),
      'subscribers' => Subscriber::whereMonth('created_at', now()->subMonth()->month)->where('subscribed', true)->count(),
    ];

    return [
      'daily' => $last30Days,
      'monthly_comparison' => [
        'current' => $currentMonth,
        'previous' => $previousMonth,
        'changes' => [
          'projects' => $this->calculatePercentageChange($previousMonth['projects'], $currentMonth['projects']),
          'customers' => $this->calculatePercentageChange($previousMonth['customers'], $currentMonth['customers']),
          'subscribers' => $this->calculatePercentageChange($previousMonth['subscribers'], $currentMonth['subscribers']),
        ],
      ],
    ];
  }

  /**
   * Get project insights.
   */
  private function getProjectInsights(): array
  {
    return [
      'status_distribution' => Project::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status'),
      'priority_distribution' => Project::selectRaw('priority, COUNT(*) as count')
        ->groupBy('priority')
        ->pluck('count', 'priority'),
      //'production_types' => $this->projectService->getProductionTypes(),
      //'categories' => $this->projectService->getCategories(),
      'overdue_projects' => Project::where('end_date', '<', now())
        ->whereNotIn('status', ['completed', 'cancelled'])
        ->count(),
      'upcoming_deadlines' => Project::where('end_date', '>=', now())
        ->where('end_date', '<=', now()->addDays(7))
        ->whereNotIn('status', ['completed', 'cancelled'])
        ->with('customer:id,first_name,last_name')
        ->get()
        ->map(function ($project) {
          return [
            'uuid' => $project->uuid,
            'name' => $project->name,
            'end_date' => $project->end_date->format('M j, Y'),
            'days_left' => $project->end_date->diffInDays(now()),
            'customer' => trim($project->customer->first_name . ' ' . $project->customer->last_name),
            'priority' => $project->priority,
            'priority_color' => $project->priority_color,
          ];
        }),
    ];
  }

  /**
   * Get performance metrics.
   */
  private function getPerformanceMetrics(): array
  {
    $totalEstimatedHours = Project::sum('estimated_hours') ?: 0;
    $totalActualHours = Project::sum('actual_hours') ?: 0;
    $totalBudget = Project::sum('budget') ?: 0;

    return [
      'total_estimated_hours' => $totalEstimatedHours,
      'total_actual_hours' => $totalActualHours,
      'total_budget' => $totalBudget,
      'hours_variance' => $totalActualHours - $totalEstimatedHours,
      'efficiency_rate' => $totalEstimatedHours > 0 ?
        round(($totalEstimatedHours / $totalActualHours) * 100, 1) : 0,
      'average_project_duration' => Project::whereNotNull('start_date')
        ->whereNotNull('end_date')
        ->selectRaw('AVG(DATEDIFF(end_date, start_date)) as avg_duration')
        ->value('avg_duration') ?: 0,
      'completion_rate' => Project::count() > 0 ?
        round((Project::completed()->count() / Project::count()) * 100, 1) : 0,
    ];
  }

  /**
   * Get quick actions.
   */
  private function getQuickActions(): array
  {
    return [
      [
        'title' => 'Add New Project',
        'description' => 'Create a new project for a client',
        'route' => 'admin.projects.create',
        'icon' => 'folder-plus',
        'color' => 'blue',
      ],
      [
        'title' => 'Add New Customer',
        'description' => 'Register a new customer',
        'route' => 'admin.customers.create',
        'icon' => 'user-plus',
        'color' => 'green',
      ],
      [
        'title' => 'View All Projects',
        'description' => 'Manage your project portfolio',
        'route' => 'admin.projects.index',
        'icon' => 'folders',
        'color' => 'purple',
      ],
      [
        'title' => 'Customer Management',
        'description' => 'View and manage customers',
        'route' => 'admin.customers.index',
        'icon' => 'users',
        'color' => 'orange',
      ],
    ];
  }

  /**
   * Generate avatar initials.
   */
  private function generateAvatar(string $firstName, string $lastName): string
  {
    return strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
  }

  /**
   * Calculate percentage change.
   */
  private function calculatePercentageChange($previousValue, $currentValue): float
  {
    if ($previousValue == 0 && $currentValue == 0) {
      return 0;
    }

    if ($previousValue == 0) {
      return 100;
    }

    return round((($currentValue - $previousValue) / $previousValue) * 100, 1);
  }

  /**
   * Clear dashboard cache.
   */
  public function clearCache(): void
  {
    Cache::forget('dashboard_data');
  }

  /**
   * Get real-time notifications.
   */
  public function getNotifications(): array
  {
    $notifications = [];

    // Overdue projects
    $overdueCount = Project::where('end_date', '<', now())
      ->whereNotIn('status', ['completed', 'cancelled'])
      ->count();

    if ($overdueCount > 0) {
      $notifications[] = [
        'type' => 'warning',
        'title' => 'Overdue Projects',
        'message' => "{$overdueCount} project(s) are overdue and need attention",
        'action' => 'admin.projects.index',
        'action_text' => 'View Projects',
      ];
    }

    // Upcoming deadlines
    $upcomingCount = Project::where('end_date', '>=', now())
      ->where('end_date', '<=', now()->addDays(3))
      ->whereNotIn('status', ['completed', 'cancelled'])
      ->count();

    if ($upcomingCount > 0) {
      $notifications[] = [
        'type' => 'info',
        'title' => 'Upcoming Deadlines',
        'message' => "{$upcomingCount} project(s) due within 3 days",
        'action' => 'admin.projects.index',
        'action_text' => 'View Projects',
      ];
    }

    return $notifications;
  }
}
