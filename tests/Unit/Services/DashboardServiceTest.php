<?php

use App\Models\Customer;
use App\Models\Download;
use App\Models\Project;
use App\Models\Subscriber;
use App\Services\DashboardService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::forget('dashboard_data');
});

afterEach(function () {
    Cache::forget('dashboard_data');
    Carbon::setTestNow();
});

it('builds year scoped dashboard aggregates and basic stats correctly', function () {
    Carbon::setTestNow('2026-01-15 12:00:00');

    Download::factory()->create();

    Project::factory()->create([
        'created_at' => Carbon::parse('2026-01-10 09:00:00'),
        'estimated_hours' => 8,
        'actual_hours' => 0,
        'status' => 'in_progress',
    ]);

    Project::factory()->create([
        'created_at' => Carbon::parse('2025-12-20 09:00:00'),
        'estimated_hours' => 6,
        'actual_hours' => 0,
        'status' => 'completed',
    ]);

    Project::factory()->create([
        'created_at' => Carbon::parse('2025-01-05 09:00:00'),
        'estimated_hours' => 4,
        'actual_hours' => 0,
    ]);

    Customer::factory()->create([
        'created_at' => Carbon::parse('2026-01-08 09:00:00'),
    ]);

    Customer::factory()->create([
        'created_at' => Carbon::parse('2025-12-18 09:00:00'),
    ]);

    Customer::factory()->create([
        'created_at' => Carbon::parse('2024-12-18 09:00:00'),
    ]);

    Subscriber::factory()->create([
        'created_at' => Carbon::parse('2026-01-09 09:00:00'),
        'subscribed' => true,
    ]);

    Subscriber::factory()->create([
        'created_at' => Carbon::parse('2025-12-10 09:00:00'),
        'subscribed' => true,
    ]);

    Subscriber::factory()->create([
        'created_at' => Carbon::parse('2024-12-10 09:00:00'),
        'subscribed' => true,
    ]);

    $data = app(DashboardService::class)->getDashboardData();

    expect($data['stats'])->toMatchArray([
        'customers_count' => 3,
        'projects_count' => 3,
        'downloads_count' => 1,
        'subscribers_count' => 3,
    ]);

    expect($data['trends']['monthly_comparison']['current'])->toMatchArray([
        'projects' => 1,
        'customers' => 1,
        'subscribers' => 1,
    ]);

    expect($data['trends']['monthly_comparison']['previous'])->toMatchArray([
        'projects' => 1,
        'customers' => 1,
        'subscribers' => 1,
    ]);

    expect($data['performance_metrics']['efficiency_rate'])->toBe(0);
});

it('orders recent activity timeline by timestamps instead of human readable labels', function () {
    Carbon::setTestNow('2026-03-15 12:00:00');

    Customer::factory()->create([
        'first_name' => 'Older',
        'last_name' => 'Customer',
        'created_at' => Carbon::parse('2026-03-10 09:00:00'),
    ]);

    Project::factory()->create([
        'name' => 'Newest Project',
        'created_at' => Carbon::parse('2026-03-14 09:00:00'),
    ]);

    $timeline = app(DashboardService::class)->getDashboardData()['recent_activity']['timeline'];

    expect($timeline)->toHaveCount(2);
    expect($timeline[0]['type'])->toBe('project');
    expect($timeline[0]['title'])->toContain('Newest Project');
    expect($timeline[1]['type'])->toBe('customer');
    expect($timeline[0]['occurred_at'] >= $timeline[1]['occurred_at'])->toBeTrue();
});
