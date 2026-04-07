<script setup lang="ts">
import { download as downloadFile, edit, index } from '@/routes/admin/downloads';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface DownloadRecord {
    uuid: string;
    title?: string | null;
    brand?: string | null;
    description?: string | null;
    category?: string | null;
    file_type?: string | null;
    formatted_file_size?: string | null;
    download_count: number;
    is_featured: boolean;
    is_public: boolean;
    sort_order?: number | null;
    published_at?: string | null;
    poster_url?: string | null;
    file_name?: string | null;
    poster_name?: string | null;
    has_file: boolean;
}

defineProps<{
    download: DownloadRecord;
}>();

defineOptions({ layout: AdminLayout });
</script>

<template>
    <Head :title="download.title || 'Download'" />

    <div class="flex flex-col gap-6 p-4">
        <section class="rounded-3xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-muted-foreground">Download asset</p>
                    <h1 class="mt-2 text-3xl font-semibold">{{ download.title || 'Untitled asset' }}</h1>
                    <p class="mt-2 text-sm text-muted-foreground">{{ [download.brand, download.category].filter(Boolean).join(' · ') || 'Library item' }}</p>
                </div>

                <div class="flex gap-3">
                    <Link :href="index.url()" class="rounded-full border border-input px-4 py-2 text-sm font-medium">All downloads</Link>
                    <Link :href="edit.url(download.uuid)" class="rounded-full bg-foreground px-4 py-2 text-sm font-medium text-background">Edit asset</Link>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[0.95fr,1.05fr]">
            <div class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <div v-if="download.poster_url" class="overflow-hidden rounded-2xl border border-input">
                    <img :src="download.poster_url" :alt="download.title || 'Download poster'" class="h-72 w-full object-cover">
                </div>
                <div v-else class="rounded-2xl border border-dashed border-input px-4 py-12 text-center text-sm text-muted-foreground">
                    No poster uploaded yet.
                </div>
            </div>

            <div class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-lg font-semibold">Asset details</h2>
                <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-muted-foreground">Brand</dt>
                        <dd class="mt-1 font-medium">{{ download.brand || 'n/a' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Category</dt>
                        <dd class="mt-1 font-medium">{{ download.category || 'n/a' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Visibility</dt>
                        <dd class="mt-1 font-medium">{{ download.is_public ? 'Public' : 'Private' }} · {{ download.is_featured ? 'Featured' : 'Standard' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Downloads</dt>
                        <dd class="mt-1 font-medium">{{ download.download_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">File metadata</dt>
                        <dd class="mt-1 font-medium">{{ [download.file_type, download.formatted_file_size].filter(Boolean).join(' · ') || 'n/a' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Published at</dt>
                        <dd class="mt-1 font-medium">{{ download.published_at ? new Date(download.published_at).toLocaleString() : 'n/a' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-muted-foreground">Description</dt>
                        <dd class="mt-1 whitespace-pre-line font-medium">{{ download.description || 'No description yet.' }}</dd>
                    </div>
                </dl>

                <div class="mt-6 flex flex-wrap gap-3">
                    <Link v-if="download.has_file" :href="downloadFile.url(download.uuid)" class="rounded-full bg-foreground px-4 py-2 text-sm font-medium text-background">
                        Download file
                    </Link>
                    <span v-else class="rounded-full border border-dashed border-input px-4 py-2 text-sm text-muted-foreground">
                        No file attached
                    </span>
                </div>
            </div>
        </section>
    </div>
</template>