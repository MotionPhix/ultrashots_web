<script setup lang="ts">
import { create, destroy, edit, index, show } from '@/routes/admin/customers';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router, setLayoutProps, useForm } from '@inertiajs/vue3';

setLayoutProps({
    breadcrumbs: [{ title: 'Customers', href: index.url() }],
});

interface CustomerRow {
    id: number;
    uuid: string;
    display_name: string;
    full_name: string;
    company_name?: string | null;
    email?: string | null;
    status: string;
    projects_count: number;
    created_at?: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Pagination<T> {
    data: T[];
    links: PaginationLink[];
    total: number;
}

const props = defineProps<{
    customers: Pagination<CustomerRow>;
    filters: {
        search?: string | null;
        status?: string | null;
        company_name?: string | null;
    };
    stats: {
        total_customers: number;
        active_customers: number;
        customers_with_projects: number;
    };
    statuses: string[];
}>();

const filters = useForm({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
    company_name: props.filters.company_name ?? '',
});

const applyFilters = () => {
    filters.get(index.url(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const resetFilters = () => {
    filters.reset();
    applyFilters();
};

const deleteCustomer = (customerUuid: string) => {
    router.delete(destroy.url(customerUuid), {
        preserveScroll: true,
    });
};

defineOptions({ layout: AdminLayout });
</script>

<template>
    <Head title="Customers" />

    <div class="flex flex-col gap-6 p-4">
        <section class="rounded-3xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-2">
                    <p class="text-sm uppercase tracking-[0.3em] text-muted-foreground">Admin</p>
                    <h1 class="text-3xl font-semibold">Customers</h1>
                    <p class="max-w-2xl text-sm text-muted-foreground">
                        Manage your client roster, company records, and linked project workload from one place.
                    </p>
                </div>

                <Link
                    :href="create.url()"
                    class="inline-flex items-center justify-center rounded-full bg-foreground px-5 py-3 text-sm font-medium text-background transition hover:opacity-90"
                >
                    New customer
                </Link>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Total customers</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.total_customers }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Active customers</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.active_customers }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Customers with projects</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.customers_with_projects }}</p>
            </div>
        </section>

        <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
            <form class="grid gap-4 lg:grid-cols-4" @submit.prevent="applyFilters">
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Search</span>
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Name, company, or email"
                        class="rounded-2xl border border-input bg-background px-4 py-2.5 outline-none ring-0 transition focus:border-foreground"
                    >
                </label>

                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Status</span>
                    <select
                        v-model="filters.status"
                        class="rounded-2xl border border-input bg-background px-4 py-2.5 outline-none transition focus:border-foreground"
                    >
                        <option value="">All statuses</option>
                        <option v-for="status in statuses" :key="status" :value="status">
                            {{ status.replace('_', ' ') }}
                        </option>
                    </select>
                </label>

                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Company</span>
                    <input
                        v-model="filters.company_name"
                        type="text"
                        placeholder="Filter by company"
                        class="rounded-2xl border border-input bg-background px-4 py-2.5 outline-none ring-0 transition focus:border-foreground"
                    >
                </label>

                <div class="flex items-end gap-3">
                    <button
                        type="submit"
                        class="inline-flex flex-1 items-center justify-center rounded-full bg-foreground px-4 py-2.5 text-sm font-medium text-background"
                    >
                        Apply filters
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-full border border-input px-4 py-2.5 text-sm font-medium"
                        @click="resetFilters"
                    >
                        Reset
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-3xl border border-sidebar-border/70 bg-card p-3 dark:border-sidebar-border">
            <div v-if="customers.data.length === 0" class="rounded-2xl border border-dashed border-input px-6 py-12 text-center text-sm text-muted-foreground">
                No customers matched the current filters.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="text-xs uppercase tracking-[0.2em] text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3 font-medium">Customer</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Projects</th>
                            <th class="px-4 py-3 font-medium">Created</th>
                            <th class="px-4 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="customer in customers.data" :key="customer.uuid" class="border-t border-sidebar-border/60 dark:border-sidebar-border">
                            <td class="px-4 py-4">
                                <div class="font-medium">{{ customer.display_name }}</div>
                                <div class="text-xs text-muted-foreground">{{ customer.email || customer.full_name }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded-full border border-input px-3 py-1 text-xs font-medium capitalize">
                                    {{ customer.status.replace('_', ' ') }}
                                </span>
                            </td>
                            <td class="px-4 py-4">{{ customer.projects_count }}</td>
                            <td class="px-4 py-4 text-muted-foreground">{{ customer.created_at ? new Date(customer.created_at).toLocaleDateString() : 'n/a' }}</td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link :href="show.url(customer.uuid)" class="rounded-full border border-input px-3 py-1.5 text-xs font-medium">View</Link>
                                    <Link :href="edit.url(customer.uuid)" class="rounded-full border border-input px-3 py-1.5 text-xs font-medium">Edit</Link>
                                    <button type="button" class="rounded-full border border-red-400/40 px-3 py-1.5 text-xs font-medium text-red-600" @click="deleteCustomer(customer.uuid)">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="customers.links.length > 3" class="mt-4 flex flex-wrap gap-2 px-2 pb-2">
                <Link
                    v-for="link in customers.links"
                    :key="`${link.label}-${link.url}`"
                    :href="link.url || ''"
                    :class="[
                        'rounded-full px-3 py-1.5 text-xs font-medium',
                        link.active ? 'bg-foreground text-background' : 'border border-input',
                        !link.url ? 'pointer-events-none opacity-40' : '',
                    ]"
                    v-html="link.label"
                />
            </div>
        </section>
    </div>
</template>