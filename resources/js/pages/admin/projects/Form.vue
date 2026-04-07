<script setup lang="ts">
import { index as customerIndex } from '@/routes/admin/customers';
import { create as createRoute, index, store, update } from '@/routes/admin/projects';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface ProjectFormRecord {
    uuid?: string;
    name?: string;
    description?: string | null;
    short_description?: string | null;
    production_type?: string | null;
    category?: string | null;
    status?: string;
    priority?: string;
    start_date?: string | null;
    end_date?: string | null;
    estimated_hours?: number | string | null;
    actual_hours?: number | string | null;
    budget?: number | string | null;
    technologies?: string[];
    features?: string[];
    challenges?: string | null;
    solutions?: string | null;
    results?: string | null;
    client_feedback?: string | null;
    is_featured?: boolean;
    is_public?: boolean;
    sort_order?: number | null;
    meta_title?: string | null;
    meta_description?: string | null;
    live_url?: string | null;
    github_url?: string | null;
    figma_url?: string | null;
    behance_url?: string | null;
    dribbble_url?: string | null;
    customer?: {
        uuid?: string;
    } | null;
}

interface CustomerOption {
    label: string;
    company?: string | null;
    value: string;
}

const props = defineProps<{
    project: ProjectFormRecord | null;
    customers: CustomerOption[];
    selected_customer_uuid?: string | null;
    statuses: string[];
    priorities: string[];
}>();

const form = useForm({
    customer_id: props.project?.customer?.uuid ?? props.selected_customer_uuid ?? '',
    name: props.project?.name ?? '',
    description: props.project?.description ?? '',
    short_description: props.project?.short_description ?? '',
    production_type: props.project?.production_type ?? '',
    category: props.project?.category ?? '',
    status: props.project?.status ?? 'not_started',
    priority: props.project?.priority ?? 'medium',
    start_date: props.project?.start_date ?? '',
    end_date: props.project?.end_date ?? '',
    estimated_hours: props.project?.estimated_hours ?? '',
    actual_hours: props.project?.actual_hours ?? '',
    budget: props.project?.budget ?? '',
    technologies: props.project?.technologies?.join(', ') ?? '',
    features: props.project?.features?.join(', ') ?? '',
    challenges: props.project?.challenges ?? '',
    solutions: props.project?.solutions ?? '',
    results: props.project?.results ?? '',
    client_feedback: props.project?.client_feedback ?? '',
    is_featured: props.project?.is_featured ?? false,
    is_public: props.project?.is_public ?? true,
    sort_order: props.project?.sort_order ?? '',
    meta_title: props.project?.meta_title ?? '',
    meta_description: props.project?.meta_description ?? '',
    live_url: props.project?.live_url ?? '',
    github_url: props.project?.github_url ?? '',
    figma_url: props.project?.figma_url ?? '',
    behance_url: props.project?.behance_url ?? '',
    dribbble_url: props.project?.dribbble_url ?? '',
});

const submit = () => {
    if (props.project?.uuid) {
        form.put(update.url(props.project.uuid), {
            preserveScroll: true,
        });

        return;
    }

    form.post(store.url(), {
        preserveScroll: true,
    });
};

defineOptions({ layout: AdminLayout });
</script>

<template>
    <Head :title="project ? 'Edit Project' : 'New Project'" />

    <div class="flex flex-col gap-6 p-4">
        <section class="rounded-3xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-muted-foreground">Admin</p>
                    <h1 class="mt-2 text-3xl font-semibold">{{ project ? 'Edit project' : 'Create project' }}</h1>
                </div>

                <div class="flex gap-3">
                    <Link :href="index.url()" class="rounded-full border border-input px-4 py-2 text-sm font-medium">All projects</Link>
                    <Link :href="customerIndex.url()" class="rounded-full border border-input px-4 py-2 text-sm font-medium">Customers</Link>
                </div>
            </div>
        </section>

        <form class="grid gap-6" @submit.prevent="submit">
            <section class="grid gap-6 rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border lg:grid-cols-2">
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Customer</span>
                    <select v-model="form.customer_id" class="rounded-2xl border border-input bg-background px-4 py-2.5">
                        <option value="">No customer</option>
                        <option v-for="customer in customers" :key="customer.value" :value="customer.value">
                            {{ customer.label }}{{ customer.company ? ` · ${customer.company}` : '' }}
                        </option>
                    </select>
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Project name</span>
                    <input v-model="form.name" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                    <span v-if="form.errors.name" class="text-xs text-red-600">{{ form.errors.name }}</span>
                </label>
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Description</span>
                    <textarea v-model="form.description" rows="5" class="rounded-2xl border border-input bg-background px-4 py-2.5" />
                </label>
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Short description</span>
                    <textarea v-model="form.short_description" rows="2" class="rounded-2xl border border-input bg-background px-4 py-2.5" />
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Production type</span>
                    <input v-model="form.production_type" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Category</span>
                    <input v-model="form.category" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Status</span>
                    <select v-model="form.status" class="rounded-2xl border border-input bg-background px-4 py-2.5">
                        <option v-for="status in statuses" :key="status" :value="status">{{ status.replace('_', ' ') }}</option>
                    </select>
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Priority</span>
                    <select v-model="form.priority" class="rounded-2xl border border-input bg-background px-4 py-2.5">
                        <option v-for="priority in priorities" :key="priority" :value="priority">{{ priority }}</option>
                    </select>
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Start date</span>
                    <input v-model="form.start_date" type="datetime-local" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">End date</span>
                    <input v-model="form.end_date" type="datetime-local" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Estimated hours</span>
                    <input v-model="form.estimated_hours" type="number" min="0" step="0.01" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Actual hours</span>
                    <input v-model="form.actual_hours" type="number" min="0" step="0.01" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Budget</span>
                    <input v-model="form.budget" type="number" min="0" step="0.01" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Sort order</span>
                    <input v-model="form.sort_order" type="number" min="0" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Technologies</span>
                    <input v-model="form.technologies" type="text" placeholder="Laravel, Vue, Tailwind" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Features</span>
                    <input v-model="form.features" type="text" placeholder="Dashboards, CMS, Reporting" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Challenges</span>
                    <textarea v-model="form.challenges" rows="3" class="rounded-2xl border border-input bg-background px-4 py-2.5" />
                </label>
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Solutions</span>
                    <textarea v-model="form.solutions" rows="3" class="rounded-2xl border border-input bg-background px-4 py-2.5" />
                </label>
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Results</span>
                    <textarea v-model="form.results" rows="3" class="rounded-2xl border border-input bg-background px-4 py-2.5" />
                </label>
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Client feedback</span>
                    <textarea v-model="form.client_feedback" rows="3" class="rounded-2xl border border-input bg-background px-4 py-2.5" />
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Meta title</span>
                    <input v-model="form.meta_title" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Meta description</span>
                    <input v-model="form.meta_description" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Live URL</span>
                    <input v-model="form.live_url" type="url" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">GitHub URL</span>
                    <input v-model="form.github_url" type="url" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Figma URL</span>
                    <input v-model="form.figma_url" type="url" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Behance URL</span>
                    <input v-model="form.behance_url" type="url" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Dribbble URL</span>
                    <input v-model="form.dribbble_url" type="url" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <div class="grid gap-3 text-sm lg:col-span-2 lg:grid-cols-2">
                    <label class="flex items-center gap-3 rounded-2xl border border-input px-4 py-3">
                        <input v-model="form.is_featured" type="checkbox" class="size-4">
                        <span class="font-medium">Featured project</span>
                    </label>
                    <label class="flex items-center gap-3 rounded-2xl border border-input px-4 py-3">
                        <input v-model="form.is_public" type="checkbox" class="size-4">
                        <span class="font-medium">Visible on public site</span>
                    </label>
                </div>
            </section>

            <div class="flex justify-end gap-3">
                <Link :href="props.project?.uuid ? createRoute.url() : index.url()" class="rounded-full border border-input px-4 py-2.5 text-sm font-medium">
                    {{ project ? 'Create new instead' : 'Cancel' }}
                </Link>
                <button type="submit" :disabled="form.processing" class="rounded-full bg-foreground px-5 py-2.5 text-sm font-medium text-background disabled:opacity-60">
                    {{ form.processing ? 'Saving...' : project ? 'Save project' : 'Create project' }}
                </button>
            </div>
        </form>
    </div>
</template>