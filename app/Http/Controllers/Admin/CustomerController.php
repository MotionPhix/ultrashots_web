<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CustomerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\Filters\CustomerFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService,
    ) {}

    public function index(Request $request): Response
    {
        $customers = $this->customerService->getCustomers(CustomerFilters::fromRequest($request))
            ->through(fn (Customer $customer): array => $this->customerPayload($customer));

        return Inertia::render('admin/customers/Index', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'status', 'company_name', 'sort_by', 'sort_direction']),
            'stats' => $this->customerService->getCustomerStats(),
            'statuses' => $this->statuses(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/customers/Form', [
            'customer' => null,
            'statuses' => $this->statuses(),
        ]);
    }

    public function store(UpsertCustomerRequest $request): RedirectResponse
    {
        $customer = $this->customerService->createCustomer($request->validated());

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): Response
    {
        $customer = $this->customerService->getCustomer($customer, ['projects.customer']);

        return Inertia::render('admin/customers/Show', [
            'customer' => $this->customerPayload($customer, includeProjects: true),
        ]);
    }

    public function edit(Customer $customer): Response
    {
        return Inertia::render('admin/customers/Form', [
            'customer' => $this->customerPayload($this->customerService->getCustomer($customer)),
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(UpsertCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->customerService->updateCustomer($customer, $request->validated());

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->customerService->deleteCustomer($customer);

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    private function statuses(): array
    {
        return array_column(CustomerStatus::cases(), 'value');
    }

    private function customerPayload(Customer $customer, bool $includeProjects = false): array
    {
        $payload = [
            'id' => $customer->id,
            'uuid' => $customer->uuid,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'full_name' => $customer->full_name,
            'display_name' => $customer->display_name,
            'company_name' => $customer->company_name,
            'email' => $customer->email,
            'phone_number' => $customer->phone_number,
            'contact_person' => $customer->contact_person,
            'address' => $customer->address,
            'city' => $customer->city,
            'state' => $customer->state,
            'zip_code' => $customer->zip_code,
            'country' => $customer->country,
            'status' => $customer->status,
            'notes' => $customer->notes,
            'projects_count' => $customer->projects_count ?? $customer->projects()->count(),
            'created_at' => $customer->created_at?->toDateTimeString(),
            'updated_at' => $customer->updated_at?->toDateTimeString(),
        ];

        if ($includeProjects) {
            $payload['projects'] = $customer->projects
                ->map(fn ($project): array => [
                    'id' => $project->id,
                    'uuid' => $project->uuid,
                    'name' => $project->name,
                    'status' => $project->status,
                    'priority' => $project->priority,
                    'is_public' => $project->is_public,
                    'created_at' => $project->created_at?->toDateTimeString(),
                ])
                ->values()
                ->all();
        }

        return $payload;
    }
}
