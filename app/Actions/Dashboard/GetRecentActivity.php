<?php

namespace App\Actions\Dashboard;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;

class GetRecentActivity
{
    public function handle(): array
    {
        return Cache::remember('dashboard.recent_activity', now()->addMinutes(5), function () {
            $recentCustomers = Customer::latest()
                ->select(['uuid', 'first_name', 'last_name', 'company_name', 'created_at', 'status'])
                ->take(5)
                ->get()
                ->map(fn ($customer) => [
                    'uuid' => $customer->uuid,
                    'name' => trim("{$customer->first_name} {$customer->last_name}"),
                    'company' => $customer->company_name,
                    'status' => $customer->status,
                    'occurred_at' => $customer->created_at->toIso8601String(),
                    'time' => $customer->created_at->diffForHumans(),
                    'avatar' => strtoupper(substr($customer->first_name, 0, 1).substr($customer->last_name, 0, 1)),
                ]);

            $recentProjects = Project::with('customer:id,first_name,last_name,company_name')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($project) => [
                    'uuid' => $project->uuid,
                    'name' => $project->name,
                    'status' => $project->status,
                    'priority' => $project->priority,
                    'progress' => $project->progress,
                    'customer_name' => trim(($project->customer?->first_name ?? '').' '.($project->customer?->last_name ?? '')),
                    'customer_company' => $project->customer?->company_name,
                    'occurred_at' => $project->created_at->toIso8601String(),
                    'time' => $project->created_at->diffForHumans(),
                    'status_color' => $project->status_color,
                    'priority_color' => $project->priority_color,
                ]);

            $timeline = collect()
                ->merge($recentCustomers->map(fn ($c) => [
                    'type' => 'customer',
                    'title' => "New customer: {$c['name']}",
                    'subtitle' => $c['company'],
                    'occurred_at' => $c['occurred_at'],
                    'time' => $c['time'],
                    'icon' => 'user-plus',
                    'color' => 'blue',
                ]))
                ->merge($recentProjects->map(fn ($p) => [
                    'type' => 'project',
                    'title' => "New project: {$p['name']}",
                    'subtitle' => $p['customer_name'],
                    'occurred_at' => $p['occurred_at'],
                    'time' => $p['time'],
                    'icon' => 'folder-plus',
                    'color' => 'green',
                ]))
                ->sortByDesc('occurred_at')
                ->take(10)
                ->values();

            return [
                'customers' => $recentCustomers,
                'projects' => $recentProjects,
                'timeline' => $timeline,
            ];
        });
    }
}
