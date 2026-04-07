<?php

namespace App\Services;

use App\Models\Customer;
use App\Services\Filters\CustomerFilters;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CustomerService
{
    /**
     * Get filtered and paginated customers.
     */
    public function getCustomers(CustomerFilters $filters): LengthAwarePaginator
    {
        $query = Customer::query()->withCount('projects');

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters);

        return $query->paginate($filters->perPage);
    }

    /**
     * Get all active customers for dropdowns.
     */
    public function getActiveCustomers(): Collection
    {
        return Customer::active()
            ->select(['uuid', 'first_name', 'last_name', 'company_name'])
            ->get()
            ->transform(function ($customer) {
                return [
                    'label' => trim($customer->first_name.' '.$customer->last_name),
                    'company' => $customer->company_name,
                    'value' => $customer->uuid,
                ];
            });
    }

    /**
     * Get a single customer with relationships.
     */
    public function getCustomer(Customer $customer, array $with = []): Customer
    {
        if (! empty($with)) {
            $customer->load($with);
        }

        $customer->loadCount('projects');

        return $customer;
    }

    /**
     * Create a new customer.
     */
    public function createCustomer(array $data): Customer
    {
        // Generate CID if not provided
        if (empty($data['uuid'])) {
            $data['uuid'] = $this->generateUniqueCid();
        }

        return Customer::create($data);
    }

    /**
     * Update an existing customer.
     */
    public function updateCustomer(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        return $customer->fresh();
    }

    /**
     * Delete a customer.
     */
    public function deleteCustomer(Customer $customer): bool
    {
        return $customer->delete();
    }

    /**
     * Get customer statistics.
     */
    public function getCustomerStats(): array
    {
        return [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::active()->count(),
            'customers_with_projects' => Customer::has('projects')->count(),
            'customers_by_status' => Customer::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'top_customers_by_projects' => Customer::withCount('projects')
                ->orderBy('projects_count', 'desc')
                ->limit(5)
                ->get(['first_name', 'last_name', 'company_name', 'projects_count']),
            'growth_rate' => $this->calculateGrowthRate(),
            'growth_data' => $this->getCustomerGrowthData(),
        ];
    }

    /**
     * Calculate customer growth rate by comparing current month with previous month.
     */
    private function calculateGrowthRate(): string
    {
        $now = now();
        $currentMonthStart = $now->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        $currentMonthCount = Customer::where('created_at', '>=', $currentMonthStart)->count();
        $lastMonthCount = Customer::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        if ($lastMonthCount === 0) {
            return $currentMonthCount > 0 ? '+100%' : '0%';
        }

        $growthRate = (($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100;
        $sign = $growthRate >= 0 ? '+' : '';

        return $sign.number_format($growthRate, 1).'%';
    }

    /**
     * Get customer growth data for the last 12 months
     */
    private function getCustomerGrowthData(): array
    {
        $months = collect(range(11, 0))->map(function ($month) {
            $date = now()->subMonths($month);

            return [
                'month' => $date->format('M'),
                'year' => $date->format('Y'),
                'start' => $date->startOfMonth(),
                'end' => $date->endOfMonth(),
            ];
        });

        $customerCounts = $months->map(function ($period) {
            $count = Customer::whereBetween('created_at', [$period['start'], $period['end']])->count();

            return [
                'label' => "{$period['month']} {$period['year']}",
                'value' => $count,
            ];
        });

        return [
            'labels' => $customerCounts->pluck('label')->toArray(),
            'data' => $customerCounts->pluck('value')->toArray(),
        ];
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters($query, CustomerFilters $filters): void
    {
        if ($filters->search !== null) {
            $search = $filters->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($filters->status !== null) {
            $query->where('status', $filters->status);
        }

        if ($filters->companyName !== null) {
            $query->where('company_name', 'like', "%{$filters->companyName}%");
        }
    }

    /**
     * Apply sorting to the query.
     */
    private function applySorting($query, CustomerFilters $filters): void
    {
        $sortField = $filters->sortBy;
        $sortDirection = $filters->sortDirection;

        $allowedSortFields = [
            'first_name', 'last_name', 'company_name', 'email',
            'status', 'created_at', 'updated_at',
        ];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * Generate a unique customer ID.
     */
    private function generateUniqueCid(): string
    {
        return Str::orderedUuid();
    }
}
