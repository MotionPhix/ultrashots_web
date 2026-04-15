<script setup lang="ts">
import { TipTapEditor } from '@/components/editor';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { index as customerIndex } from '@/routes/admin/customers';
import { create as createRoute, index, store, update } from '@/routes/admin/projects';
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';

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
    results?: string | string[] | null;
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

const normalizeResults = (r: ProjectFormRecord['results']): string => {
    if (!r) { return ''; }
    if (Array.isArray(r)) { return r.join('\n'); }
    return String(r);
};

const form = useForm({
    customer_id: props.project?.customer?.uuid ?? props.selected_customer_uuid ?? null,
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
    results: normalizeResults(props.project?.results),
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
        form.put(update.url(props.project.uuid), { preserveScroll: true });
        return;
    }
    form.post(store.url(), { preserveScroll: true });
};

setLayoutProps({
    breadcrumbs: [
        { title: 'Projects', href: index.url() },
        { title: props.project ? 'Edit Project' : 'New Project', href: '' },
    ],
});

defineOptions({ layout: AdminLayout });
</script>

<template>
    <Head :title="props.project ? 'Edit Project' : 'New Project'" />

    <div class="flex flex-col gap-6 p-4">
        <!-- Page header -->
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

        <form class="flex flex-col gap-6" @submit.prevent="submit">

            <!-- Project details -->
            <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-base font-semibold">Project details</h2>
                <Separator class="my-4" />
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="customer_id">Customer</Label>
                        <Select v-model="form.customer_id">
                            <SelectTrigger id="customer_id" class="w-full">
                                <SelectValue placeholder="No customer" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="null">No customer</SelectItem>
                                <SelectItem v-for="customer in customers" :key="customer.value" :value="customer.value">
                                    {{ customer.label }}{{ customer.company ? ` Â· ${customer.company}` : '' }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Project name</Label>
                        <Input id="name" v-model="form.name" type="text" placeholder="e.g. Malawi NGO Coalition" />
                        <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="production_type">Production type</Label>
                        <Input id="production_type" v-model="form.production_type" type="text" placeholder="e.g. Branding & Digital Strategy" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="category">Category</Label>
                        <Input id="category" v-model="form.category" type="text" placeholder="e.g. NGO Campaign" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="status">Status</Label>
                        <Select v-model="form.status">
                            <SelectTrigger id="status" class="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="status in statuses" :key="status" :value="status">
                                    {{ status.replace(/_/g, ' ') }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid gap-2">
                        <Label for="priority">Priority</Label>
                        <Select v-model="form.priority">
                            <SelectTrigger id="priority" class="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="priority in priorities" :key="priority" :value="priority">
                                    {{ priority }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid gap-2">
                        <Label for="sort_order">Sort order</Label>
                        <Input id="sort_order" v-model="form.sort_order" type="number" min="0" />
                    </div>
                </div>
            </section>

            <!-- Dates & Metrics -->
            <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-base font-semibold">Dates &amp; Metrics</h2>
                <Separator class="my-4" />
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="start_date">Start date</Label>
                        <Input id="start_date" v-model="form.start_date" type="datetime-local" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="end_date">End date</Label>
                        <Input id="end_date" v-model="form.end_date" type="datetime-local" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="estimated_hours">Estimated hours</Label>
                        <Input id="estimated_hours" v-model="form.estimated_hours" type="number" min="0" step="0.01" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="actual_hours">Actual hours</Label>
                        <Input id="actual_hours" v-model="form.actual_hours" type="number" min="0" step="0.01" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="budget">Budget</Label>
                        <Input id="budget" v-model="form.budget" type="number" min="0" step="0.01" />
                    </div>
                </div>
            </section>

            <!-- Content -->
            <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-base font-semibold">Content</h2>
                <Separator class="my-4" />
                <div class="grid gap-6">
                    <div class="grid gap-2">
                        <Label>Description</Label>
                        <TipTapEditor
                            v-model="form.description"
                            placeholder="Full project description..."
                            min-height="200px"
                        />
                        <p v-if="form.errors.description" class="text-xs text-destructive">{{ form.errors.description }}</p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="short_description">Short description</Label>
                        <Textarea
                            id="short_description"
                            v-model="form.short_description"
                            rows="3"
                            placeholder="One-line summary shown in project cards..."
                        />
                        <p v-if="form.errors.short_description" class="text-xs text-destructive">{{ form.errors.short_description }}</p>
                    </div>
                </div>
            </section>

            <!-- Tags & Technologies -->
            <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-base font-semibold">Tags &amp; Technologies</h2>
                <Separator class="my-4" />
                <div class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="technologies">Technologies</Label>
                        <Input id="technologies" v-model="form.technologies" type="text" placeholder="Figma, Adobe Creative Suite, Meta Ads Manager" />
                        <p class="text-xs text-muted-foreground">Comma-separated list</p>
                    </div>

                    <div class="grid gap-2">
                        <Label for="features">Features</Label>
                        <Input id="features" v-model="form.features" type="text" placeholder="Brand identity system, Social media strategy, Impact reporting" />
                        <p class="text-xs text-muted-foreground">Comma-separated list</p>
                    </div>
                </div>
            </section>

            <!-- Case Study -->
            <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-base font-semibold">Case Study</h2>
                <Separator class="my-4" />
                <div class="grid gap-8">
                    <div class="grid gap-2">
                        <Label>Challenges</Label>
                        <TipTapEditor
                            v-model="form.challenges"
                            placeholder="What challenges did the client face?"
                            min-height="150px"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label>Solutions</Label>
                        <TipTapEditor
                            v-model="form.solutions"
                            placeholder="How did you solve them?"
                            min-height="150px"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label>Results</Label>
                        <TipTapEditor
                            v-model="form.results"
                            placeholder="What were the outcomes and measurable results?"
                            min-height="150px"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label>Client feedback</Label>
                        <TipTapEditor
                            v-model="form.client_feedback"
                            placeholder="What did the client say?"
                            min-height="120px"
                        />
                    </div>
                </div>
            </section>

            <!-- SEO -->
            <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-base font-semibold">SEO</h2>
                <Separator class="my-4" />
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="meta_title">Meta title</Label>
                        <Input id="meta_title" v-model="form.meta_title" type="text" placeholder="Page title for search engines" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="meta_description">Meta description</Label>
                        <Input id="meta_description" v-model="form.meta_description" type="text" placeholder="Short description for search results" />
                    </div>
                </div>
            </section>

            <!-- Links -->
            <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-base font-semibold">Links</h2>
                <Separator class="my-4" />
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="live_url">Live URL</Label>
                        <Input id="live_url" v-model="form.live_url" type="url" placeholder="https://example.com" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="github_url">GitHub URL</Label>
                        <Input id="github_url" v-model="form.github_url" type="url" placeholder="https://github.com/..." />
                    </div>

                    <div class="grid gap-2">
                        <Label for="figma_url">Figma URL</Label>
                        <Input id="figma_url" v-model="form.figma_url" type="url" placeholder="https://figma.com/..." />
                    </div>

                    <div class="grid gap-2">
                        <Label for="behance_url">Behance URL</Label>
                        <Input id="behance_url" v-model="form.behance_url" type="url" placeholder="https://behance.net/..." />
                    </div>

                    <div class="grid gap-2">
                        <Label for="dribbble_url">Dribbble URL</Label>
                        <Input id="dribbble_url" v-model="form.dribbble_url" type="url" placeholder="https://dribbble.com/..." />
                    </div>
                </div>
            </section>

            <!-- Visibility -->
            <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-base font-semibold">Visibility</h2>
                <Separator class="my-4" />
                <div class="grid gap-4 lg:grid-cols-2">
                    <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-input px-4 py-3">
                        <Checkbox
                            id="is_featured"
                            :checked="form.is_featured"
                            @update:checked="(v) => (form.is_featured = !!v)"
                        />
                        <div class="grid gap-0.5">
                            <span class="text-sm font-medium">Featured project</span>
                            <span class="text-xs text-muted-foreground">Show in featured section on the public site</span>
                        </div>
                    </label>

                    <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-input px-4 py-3">
                        <Checkbox
                            id="is_public"
                            :checked="form.is_public"
                            @update:checked="(v) => (form.is_public = !!v)"
                        />
                        <div class="grid gap-0.5">
                            <span class="text-sm font-medium">Visible on public site</span>
                            <span class="text-xs text-muted-foreground">Publish this project to the portfolio</span>
                        </div>
                    </label>
                </div>
            </section>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
                <Link
                    :as="Button"
                    variant="outline"
                    :href="props.project?.uuid ? createRoute.url() : index.url()">
                    {{ project ? 'Create new instead' : 'Cancel' }}
                </Link>
                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : project ? 'Save project' : 'Create project' }}
                </Button>
            </div>

        </form>
    </div>
</template>
