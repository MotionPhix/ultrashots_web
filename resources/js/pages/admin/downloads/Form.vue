<script setup lang="ts">
import { create as createRoute, index, store, update } from '@/routes/admin/downloads';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, setLayoutProps, useForm } from '@inertiajs/vue3';

interface DownloadFormRecord {
    uuid?: string;
    title?: string | null;
    brand?: string | null;
    description?: string | null;
    category?: string | null;
    is_featured?: boolean;
    is_public?: boolean;
    sort_order?: number | null;
    published_at?: string | null;
    file_name?: string | null;
    poster_name?: string | null;
}

const props = defineProps<{
    download: DownloadFormRecord | null;
}>();

const form = useForm({
    title: props.download?.title ?? '',
    brand: props.download?.brand ?? '',
    description: props.download?.description ?? '',
    category: props.download?.category ?? '',
    is_featured: props.download?.is_featured ?? false,
    is_public: props.download?.is_public ?? true,
    sort_order: props.download?.sort_order ?? '',
    published_at: props.download?.published_at ?? '',
    poster_image: null as File | null,
    download_file: null as File | null,
});

const setPosterImage = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.poster_image = target.files?.[0] ?? null;
};

const setDownloadFile = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.download_file = target.files?.[0] ?? null;
};

const submit = () => {
    if (props.download?.uuid) {
        form.put(update.url(props.download.uuid), {
            preserveScroll: true,
            forceFormData: true,
        });

        return;
    }

    form.post(store.url(), {
        preserveScroll: true,
        forceFormData: true,
    });
};

setLayoutProps({
    breadcrumbs: [
        { title: 'Downloads', href: index.url() },
        { title: props.download ? 'Edit Download' : 'New Download', href: '' },
    ],
});

defineOptions({ layout: AdminLayout });
</script>

<template>
    <Head :title="props.download ? 'Edit Download' : 'New Download'" />

    <div class="flex flex-col gap-6 p-4">
        <section class="rounded-3xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-muted-foreground">Admin</p>
                    <h1 class="mt-2 text-3xl font-semibold">{{ download ? 'Edit download' : 'Create download' }}</h1>
                </div>

                <Link :href="index.url()" class="rounded-full border border-input px-4 py-2 text-sm font-medium">All downloads</Link>
            </div>
        </section>

        <form class="grid gap-6" @submit.prevent="submit">
            <section class="grid gap-6 rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border lg:grid-cols-2">
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Title</span>
                    <input v-model="form.title" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                    <span v-if="form.errors.title" class="text-xs text-red-600">{{ form.errors.title }}</span>
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Brand</span>
                    <input v-model="form.brand" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Category</span>
                    <input v-model="form.category" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Description</span>
                    <textarea v-model="form.description" rows="5" class="rounded-2xl border border-input bg-background px-4 py-2.5" />
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Published at</span>
                    <input v-model="form.published_at" type="datetime-local" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Sort order</span>
                    <input v-model="form.sort_order" type="number" min="0" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <div class="grid gap-3 text-sm lg:col-span-2 lg:grid-cols-2">
                    <label class="flex items-center gap-3 rounded-2xl border border-input px-4 py-3">
                        <input v-model="form.is_featured" type="checkbox" class="size-4">
                        <span class="font-medium">Featured asset</span>
                    </label>
                    <label class="flex items-center gap-3 rounded-2xl border border-input px-4 py-3">
                        <input v-model="form.is_public" type="checkbox" class="size-4">
                        <span class="font-medium">Visible on public site</span>
                    </label>
                </div>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Poster image</span>
                    <input type="file" accept="image/*" class="rounded-2xl border border-input bg-background px-4 py-2.5" @change="setPosterImage">
                    <span v-if="download?.poster_name" class="text-xs text-muted-foreground">Current poster: {{ download.poster_name }}</span>
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Download file</span>
                    <input type="file" class="rounded-2xl border border-input bg-background px-4 py-2.5" @change="setDownloadFile">
                    <span v-if="download?.file_name" class="text-xs text-muted-foreground">Current file: {{ download.file_name }}</span>
                </label>
            </section>

            <div class="flex justify-end gap-3">
                <Link :href="props.download?.uuid ? createRoute.url() : index.url()" class="rounded-full border border-input px-4 py-2.5 text-sm font-medium">
                    {{ download ? 'Create new instead' : 'Cancel' }}
                </Link>
                <button type="submit" :disabled="form.processing" class="rounded-full bg-foreground px-5 py-2.5 text-sm font-medium text-background disabled:opacity-60">
                    {{ form.processing ? 'Saving...' : download ? 'Save download' : 'Create download' }}
                </button>
            </div>
        </form>
    </div>
</template>