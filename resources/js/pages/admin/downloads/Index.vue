<script setup lang="ts">
import { create, destroy, download as downloadAsset, duplicate, edit, index, show } from '@/routes/admin/downloads';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router, setLayoutProps, useForm } from '@inertiajs/vue3';

setLayoutProps({
    breadcrumbs: [{ title: 'Downloads', href: index.url() }],
});

interface DownloadRow {
    id: number;
    uuid: string;
    title?: string | null;
    brand?: string | null;
    category?: string | null;
    file_type?: string | null;
    formatted_file_size?: string | null;
    download_count: number;
    is_featured: boolean;
    is_public: boolean;
    has_file: boolean;
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
    downloads: Pagination<DownloadRow>;
    filters: {
        search?: string | null;
        category?: string | null;
        file_type?: string | null;
        brand?: string | null;
    };
    stats: {
        total_downloads: number;
        public_downloads: number;
        featured_downloads: number;
        download_events: number;
    };
    categories: string[];
    file_types: string[];
    brands: string[];
}>();

const filters = useForm({
    search: props.filters.search ?? '',
    category: props.filters.category ?? '',
    file_type: props.filters.file_type ?? '',
    brand: props.filters.brand ?? '',
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

const deleteDownload = (downloadUuid: string) => {
    router.delete(destroy.url(downloadUuid), {
        preserveScroll: true,
    });
};

const duplicateDownload = (downloadUuid: string) => {
    router.post(duplicate.url(downloadUuid), {}, {
        preserveScroll: true,
    });
};

defineOptions({ layout: AdminLayout });
</script>

<template>
    <Head title="Downloads" />

    <div class="flex flex-col gap-6 p-4">
        <section class="rounded-3xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-muted-foreground">Admin</p>
                    <h1 class="mt-2 text-3xl font-semibold">Downloads</h1>
                    <p class="mt-2 max-w-2xl text-sm text-muted-foreground">
                        Manage your public resource library, attached files, and asset performance.
                    </p>
                </div>

                <Link :href="create.url()" class="inline-flex items-center justify-center rounded-full bg-foreground px-5 py-3 text-sm font-medium text-background">
                    New download
                </Link>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Total assets</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.total_downloads }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Public assets</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.public_downloads }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Featured assets</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.featured_downloads }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Download events</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.download_events }}</p>
            </div>
        </section>

        <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
            <form class="grid gap-4 lg:grid-cols-5" @submit.prevent="applyFilters">
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Search</span>
                    <input v-model="filters.search" type="text" placeholder="Search downloads" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Category</span>
                    <select v-model="filters.category" class="rounded-2xl border border-input bg-background px-4 py-2.5">
                        <option value="">All categories</option>
                        <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
                    </select>
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">File type</span>
                    <select v-model="filters.file_type" class="rounded-2xl border border-input bg-background px-4 py-2.5">
                        <option value="">All file types</option>
                        <option v-for="fileType in file_types" :key="fileType" :value="fileType">{{ fileType }}</option>
                    </select>
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Brand</span>
                    <select v-model="filters.brand" class="rounded-2xl border border-input bg-background px-4 py-2.5">
                        <option value="">All brands</option>
                        <option v-for="brand in brands" :key="brand" :value="brand">{{ brand }}</option>
                    </select>
                </label>
                <div class="flex gap-3 lg:col-span-5 lg:justify-end">
                    <button type="button" class="rounded-full border border-input px-4 py-2.5 text-sm font-medium" @click="resetFilters">Reset</button>
                    <button type="submit" class="rounded-full bg-foreground px-4 py-2.5 text-sm font-medium text-background">Apply filters</button>
                </div>
            </form>
        </section>

        <section class="rounded-3xl border border-sidebar-border/70 bg-card p-3 dark:border-sidebar-border">
            <div v-if="downloads.data.length === 0" class="rounded-2xl border border-dashed border-input px-6 py-12 text-center text-sm text-muted-foreground">
                No downloads matched the current filters.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="text-xs uppercase tracking-[0.2em] text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3 font-medium">Asset</th>
                            <th class="px-4 py-3 font-medium">Type</th>
                            <th class="px-4 py-3 font-medium">Performance</th>
                            <th class="px-4 py-3 font-medium">Visibility</th>
                            <th class="px-4 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="download in downloads.data" :key="download.uuid" class="border-t border-sidebar-border/60 dark:border-sidebar-border">
                            <td class="px-4 py-4">
                                <div class="font-medium">{{ download.title || 'Untitled asset' }}</div>
                                <div class="text-xs text-muted-foreground">{{ [download.brand, download.category].filter(Boolean).join(' · ') || 'Uncategorised' }}</div>
                            </td>
                            <td class="px-4 py-4 text-muted-foreground">{{ [download.file_type, download.formatted_file_size].filter(Boolean).join(' · ') || 'No file metadata' }}</td>
                            <td class="px-4 py-4 text-muted-foreground">{{ download.download_count }} downloads · {{ download.has_file ? 'file attached' : 'missing file' }}</td>
                            <td class="px-4 py-4 text-muted-foreground">{{ download.is_public ? 'Public' : 'Private' }} · {{ download.is_featured ? 'Featured' : 'Standard' }}</td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <Link :href="show.url(download.uuid)" class="rounded-full border border-input px-3 py-1.5 text-xs font-medium">View</Link>
                                    <Link :href="edit.url(download.uuid)" class="rounded-full border border-input px-3 py-1.5 text-xs font-medium">Edit</Link>
                                    <Link v-if="download.has_file" :href="downloadAsset.url(download.uuid)" class="rounded-full border border-input px-3 py-1.5 text-xs font-medium">File</Link>
                                    <button type="button" class="rounded-full border border-input px-3 py-1.5 text-xs font-medium" @click="duplicateDownload(download.uuid)">Duplicate</button>
                                    <button type="button" class="rounded-full border border-red-400/40 px-3 py-1.5 text-xs font-medium text-red-600" @click="deleteDownload(download.uuid)">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="downloads.links.length > 3" class="mt-4 flex flex-wrap gap-2 px-2 pb-2">
                <Link
                    v-for="link in downloads.links"
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