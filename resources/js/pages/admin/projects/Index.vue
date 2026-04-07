<script setup lang="ts">
import { create, destroy, edit, index, show } from '@/routes/admin/projects';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

interface ProjectRow {
    uuid: string;
    name: string;
    category?: string | null;
    production_type?: string | null;
    status: string;
    priority: string;
    is_featured: boolean;
    is_public: boolean;
    customer?: {
        full_name?: string | null;
        company_name?: string | null;
    } | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Pagination<T> {
    data: T[];
    links: PaginationLink[];
}

const props = defineProps<{
    projects: Pagination<ProjectRow>;
    filters: {
        search?: string | null;
        status?: string | null;
        category?: string | null;
        priority?: string | null;
    };
    stats: {
        total_projects: number;
        active_projects: number;
        completed_projects: number;
        overdue_projects: number;
    };
    statuses: string[];
    priorities: string[];
    categories: string[];
}>();

const filters = useForm({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
    category: props.filters.category ?? '',
    priority: props.filters.priority ?? '',
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

const deleteProject = (projectUuid: string) => {
    router.delete(destroy.url(projectUuid), {
        preserveScroll: true,
    });
};

defineOptions({ layout: AdminLayout });
</script>

<template>
    <Head title="Projects" />

    <div class="flex flex-col gap-6 p-4">
        <section class="rounded-3xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-muted-foreground">Admin</p>
                    <h1 class="mt-2 text-3xl font-semibold">Projects</h1>
                    <p class="mt-2 max-w-2xl text-sm text-muted-foreground">
                        Keep delivery status, client assignments, and public portfolio visibility aligned.
                    </p>
                </div>

                <Link :href="create.url()" class="inline-flex items-center justify-center rounded-full bg-foreground px-5 py-3 text-sm font-medium text-background">
                    New project
                </Link>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Total projects</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.total_projects }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Active</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.active_projects }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Completed</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.completed_projects }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Overdue</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.overdue_projects }}</p>
            </div>
        </section>

        <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
            <form class="grid gap-4 lg:grid-cols-5" @submit.prevent="applyFilters">
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Search</span>
                    <input v-model="filters.search" type="text" placeholder="Search projects" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Status</span>
                    <select v-model="filters.status" class="rounded-2xl border border-input bg-background px-4 py-2.5">
                        <option value="">All statuses</option>
                        <option v-for="status in statuses" :key="status" :value="status">{{ status.replace('_', ' ') }}</option>
                    </select>
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Category</span>
                    <select v-model="filters.category" class="rounded-2xl border border-input bg-background px-4 py-2.5">
                        <option value="">All categories</option>
                        <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
                    </select>
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Priority</span>
                    <select v-model="filters.priority" class="rounded-2xl border border-input bg-background px-4 py-2.5">
                        <option value="">All priorities</option>
                        <option v-for="priority in priorities" :key="priority" :value="priority">{{ priority }}</option>
                    </select>
                </label>
                <div class="flex gap-3 lg:col-span-5 lg:justify-end">
                    <button type="button" class="rounded-full border border-input px-4 py-2.5 text-sm font-medium" @click="resetFilters">Reset</button>
                    <button type="submit" class="rounded-full bg-foreground px-4 py-2.5 text-sm font-medium text-background">Apply filters</button>
                </div>
            </form>
        </section>

        <section class="rounded-3xl border border-sidebar-border/70 bg-card p-3 dark:border-sidebar-border">
            <div v-if="projects.data.length === 0" class="rounded-2xl border border-dashed border-input px-6 py-12 text-center text-sm text-muted-foreground">
                No projects matched the current filters.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="text-xs uppercase tracking-[0.2em] text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3 font-medium">Project</th>
                            <th class="px-4 py-3 font-medium">Client</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Visibility</th>
                            <th class="px-4 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="project in projects.data" :key="project.uuid" class="border-t border-sidebar-border/60 dark:border-sidebar-border">
                            <td class="px-4 py-4">
                                <div class="font-medium">{{ project.name }}</div>
                                <div class="text-xs text-muted-foreground">{{ [project.production_type, project.category].filter(Boolean).join(' · ') || 'Uncategorised' }}</div>
                            </td>
                            <td class="px-4 py-4 text-muted-foreground">{{ project.customer?.company_name || project.customer?.full_name || 'Unassigned' }}</td>
                            <td class="px-4 py-4">
                                <div class="font-medium capitalize">{{ project.status.replace('_', ' ') }}</div>
                                <div class="text-xs text-muted-foreground">{{ project.priority }}</div>
                            </td>
                            <td class="px-4 py-4 text-muted-foreground">{{ project.is_public ? 'Public' : 'Private' }} · {{ project.is_featured ? 'Featured' : 'Standard' }}</td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link :href="show.url(project.uuid)" class="rounded-full border border-input px-3 py-1.5 text-xs font-medium">View</Link>
                                    <Link :href="edit.url(project.uuid)" class="rounded-full border border-input px-3 py-1.5 text-xs font-medium">Edit</Link>
                                    <button type="button" class="rounded-full border border-red-400/40 px-3 py-1.5 text-xs font-medium text-red-600" @click="deleteProject(project.uuid)">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="projects.links.length > 3" class="mt-4 flex flex-wrap gap-2 px-2 pb-2">
                <Link
                    v-for="link in projects.links"
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