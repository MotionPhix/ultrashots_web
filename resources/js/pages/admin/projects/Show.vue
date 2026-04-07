<script setup lang="ts">
import { edit, index } from '@/routes/admin/projects';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface GalleryImage {
    id: number;
    thumb_url?: string | null;
    url: string;
    name?: string | null;
}

interface ProjectRecord {
    uuid: string;
    name: string;
    description?: string | null;
    short_description?: string | null;
    production_type?: string | null;
    category?: string | null;
    status: string;
    priority: string;
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
    is_featured: boolean;
    is_public: boolean;
    customer?: {
        full_name?: string | null;
        company_name?: string | null;
    } | null;
    gallery_images?: GalleryImage[];
}

defineProps<{
    project: ProjectRecord;
}>();

defineOptions({ layout: AdminLayout });
</script>

<template>
    <Head :title="project.name" />

    <div class="flex flex-col gap-6 p-4">
        <section class="rounded-3xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-muted-foreground">Project profile</p>
                    <h1 class="mt-2 text-3xl font-semibold">{{ project.name }}</h1>
                    <p class="mt-2 text-sm text-muted-foreground">
                        {{ [project.production_type, project.category].filter(Boolean).join(' · ') || 'Internal project record' }}
                    </p>
                </div>

                <div class="flex gap-3">
                    <Link :href="index.url()" class="rounded-full border border-input px-4 py-2 text-sm font-medium">All projects</Link>
                    <Link :href="edit.url(project.uuid)" class="rounded-full bg-foreground px-4 py-2 text-sm font-medium text-background">Edit project</Link>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.1fr,0.9fr]">
            <div class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-lg font-semibold">Delivery summary</h2>
                <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-muted-foreground">Client</dt>
                        <dd class="mt-1 font-medium">{{ project.customer?.company_name || project.customer?.full_name || 'Unassigned' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Status</dt>
                        <dd class="mt-1 font-medium capitalize">{{ project.status.replace('_', ' ') }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Priority</dt>
                        <dd class="mt-1 font-medium">{{ project.priority }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Visibility</dt>
                        <dd class="mt-1 font-medium">{{ project.is_public ? 'Public' : 'Private' }} · {{ project.is_featured ? 'Featured' : 'Standard' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Start date</dt>
                        <dd class="mt-1 font-medium">{{ project.start_date ? new Date(project.start_date).toLocaleString() : 'n/a' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">End date</dt>
                        <dd class="mt-1 font-medium">{{ project.end_date ? new Date(project.end_date).toLocaleString() : 'n/a' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Estimated hours</dt>
                        <dd class="mt-1 font-medium">{{ project.estimated_hours || 'n/a' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Actual hours</dt>
                        <dd class="mt-1 font-medium">{{ project.actual_hours || 'n/a' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Budget</dt>
                        <dd class="mt-1 font-medium">{{ project.budget || 'n/a' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-muted-foreground">Description</dt>
                        <dd class="mt-1 whitespace-pre-line font-medium">{{ project.description || project.short_description || 'No description yet.' }}</dd>
                    </div>
                    <div v-if="project.technologies?.length" class="sm:col-span-2">
                        <dt class="text-muted-foreground">Technologies</dt>
                        <dd class="mt-1 flex flex-wrap gap-2">
                            <span v-for="technology in project.technologies" :key="technology" class="rounded-full border border-input px-3 py-1 text-xs font-medium">
                                {{ technology }}
                            </span>
                        </dd>
                    </div>
                    <div v-if="project.features?.length" class="sm:col-span-2">
                        <dt class="text-muted-foreground">Features</dt>
                        <dd class="mt-1 whitespace-pre-line font-medium">{{ project.features.join(', ') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-lg font-semibold">Media</h2>

                <div v-if="!project.gallery_images?.length" class="mt-6 rounded-2xl border border-dashed border-input px-4 py-8 text-center text-sm text-muted-foreground">
                    No gallery media attached.
                </div>

                <div v-else class="mt-6 grid gap-4 sm:grid-cols-2">
                    <a v-for="image in project.gallery_images" :key="image.id" :href="image.url" target="_blank" rel="noreferrer" class="overflow-hidden rounded-2xl border border-input">
                        <img :src="image.thumb_url || image.url" :alt="image.name || project.name" class="h-40 w-full object-cover">
                    </a>
                </div>
            </div>
        </section>
    </div>
</template>