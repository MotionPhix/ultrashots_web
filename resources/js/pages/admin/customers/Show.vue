<script setup lang="ts">
import { edit, index } from '@/routes/admin/customers';
import { show as projectShow } from '@/routes/admin/projects';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface CustomerProject {
    uuid: string;
    name: string;
    status: string;
    priority: string;
    is_public: boolean;
}

interface CustomerRecord {
    uuid: string;
    display_name: string;
    full_name: string;
    company_name?: string | null;
    email?: string | null;
    phone_number?: string | null;
    contact_person?: string | null;
    address?: string | null;
    city?: string | null;
    state?: string | null;
    zip_code?: string | null;
    country?: string | null;
    status: string;
    notes?: string | null;
    projects_count: number;
    projects: CustomerProject[];
}

defineProps<{
    customer: CustomerRecord;
}>();

defineOptions({ layout: AdminLayout });
</script>

<template>
    <Head :title="customer.display_name" />

    <div class="flex flex-col gap-6 p-4">
        <section class="rounded-3xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-muted-foreground">Customer profile</p>
                    <h1 class="mt-2 text-3xl font-semibold">{{ customer.display_name }}</h1>
                    <p class="mt-2 text-sm text-muted-foreground">
                        {{ customer.email || customer.full_name }}
                    </p>
                </div>

                <div class="flex gap-3">
                    <Link :href="index.url()" class="rounded-full border border-input px-4 py-2 text-sm font-medium">All customers</Link>
                    <Link :href="edit.url(customer.uuid)" class="rounded-full bg-foreground px-4 py-2 text-sm font-medium text-background">Edit customer</Link>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
            <div class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-lg font-semibold">Contact details</h2>
                <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-muted-foreground">Email</dt>
                        <dd class="mt-1 font-medium">{{ customer.email || 'n/a' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Phone</dt>
                        <dd class="mt-1 font-medium">{{ customer.phone_number || 'n/a' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Contact person</dt>
                        <dd class="mt-1 font-medium">{{ customer.contact_person || 'n/a' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Status</dt>
                        <dd class="mt-1 font-medium capitalize">{{ customer.status }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-muted-foreground">Address</dt>
                        <dd class="mt-1 font-medium">
                            {{ [customer.address, customer.city, customer.state, customer.zip_code, customer.country].filter(Boolean).join(', ') || 'n/a' }}
                        </dd>
                    </div>
                    <div v-if="customer.notes" class="sm:col-span-2">
                        <dt class="text-muted-foreground">Notes</dt>
                        <dd class="mt-1 whitespace-pre-line font-medium">{{ customer.notes }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-lg font-semibold">Projects</h2>
                <p class="mt-1 text-sm text-muted-foreground">{{ customer.projects_count }} linked projects</p>

                <div v-if="customer.projects.length === 0" class="mt-6 rounded-2xl border border-dashed border-input px-4 py-8 text-center text-sm text-muted-foreground">
                    No projects are linked to this customer yet.
                </div>

                <div v-else class="mt-6 grid gap-3">
                    <Link
                        v-for="project in customer.projects"
                        :key="project.uuid"
                        :href="projectShow.url(project.uuid)"
                        class="rounded-2xl border border-input px-4 py-3 transition hover:border-foreground"
                    >
                        <div class="font-medium">{{ project.name }}</div>
                        <div class="mt-1 text-xs text-muted-foreground">
                            {{ project.status.replace('_', ' ') }} · {{ project.priority }} · {{ project.is_public ? 'public' : 'private' }}
                        </div>
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>