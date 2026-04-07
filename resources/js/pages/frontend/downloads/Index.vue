<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import {
  ArrowRight,
  Download as DownloadIcon,
  FileUp,
  Search,
  ShieldCheck,
  Sparkles,
  Wrench,
} from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { downloadFile, index as downloadsIndex, uploadFile } from '@/actions/App/Http/Controllers/Frontend/DownloadController';
import type { Download } from '@/types';

defineOptions({
  layout: AppLayout,
});

const props = defineProps<{
  downloads: Download[];
  filters: {
    search: string;
    category: string;
    file_type: string;
  };
  categories: string[];
  fileTypes: string[];
  stats: {
    count: number;
    featured: number;
    download_events: number;
  };
}>();

const filters = reactive({ ...props.filters });

const fixForm = useForm({
  name: '',
  email: '',
  notes: '',
  file: null as File | null,
});

const hasActiveFilters = computed(() => {
  return Boolean(filters.search || filters.category || filters.file_type);
});

function applyFilters(): void {
  const query = Object.fromEntries(
    Object.entries(filters).filter(([, value]) => value !== ''),
  );

  router.get(downloadsIndex.url(), query, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  });
}

function resetFilters(): void {
  filters.search = '';
  filters.category = '';
  filters.file_type = '';
  applyFilters();
}

function setFile(event: Event): void {
  const input = event.target as HTMLInputElement;

  fixForm.file = input.files?.[0] ?? null;
}

function submitFixRequest(): void {
  fixForm.post(uploadFile.url(), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => fixForm.reset(),
  });
}
</script>

<template>
  <div class="bg-[radial-gradient(circle_at_top,rgba(251,191,36,0.18),transparent_30%),linear-gradient(180deg,#fffef8_0%,#ffffff_40%,#f8fafc_100%)] dark:bg-[radial-gradient(circle_at_top,rgba(251,191,36,0.12),transparent_30%),linear-gradient(180deg,#171717_0%,#0a0a0a_45%,#111827_100%)]">
    <Head title="Downloads" />

    <section class="mx-auto flex w-full max-w-6xl flex-col gap-8 px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
      <div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
        <Card class="overflow-hidden border-neutral-200/70 bg-white/85 shadow-lg shadow-amber-100/50 backdrop-blur-sm dark:border-neutral-800 dark:bg-neutral-950/80 dark:shadow-none">
          <CardContent class="grid gap-8 p-8 lg:grid-cols-[1.1fr_0.9fr] lg:p-10">
            <div class="flex flex-col gap-5">
              <div class="flex items-center gap-2 text-sm font-medium uppercase tracking-[0.22em] text-amber-700 dark:text-amber-300">
                <Sparkles class="size-4" />
                Studio Download Library
              </div>

              <div class="space-y-4">
                <h1 class="max-w-xl font-serif text-4xl leading-tight text-neutral-950 dark:text-neutral-50 sm:text-5xl">
                  Brand assets, templates, and ready-to-use creative packs.
                </h1>

                <p class="max-w-2xl text-base leading-7 text-neutral-600 dark:text-neutral-300">
                  Browse the public asset shelf, grab what you need, and send a repair request if a file needs a cleaner export,
                  a missing format, or a production-ready revision.
                </p>
              </div>

              <div class="flex flex-wrap gap-3">
                <a href="#download-catalogue" class="inline-flex">
                  <Button class="gap-2 bg-neutral-950 text-white hover:bg-neutral-800 dark:bg-white dark:text-neutral-950 dark:hover:bg-neutral-200">
                    Browse downloads
                    <ArrowRight class="size-4" />
                  </Button>
                </a>

                <a href="#fix-request" class="inline-flex">
                  <Button variant="outline" class="gap-2 border-amber-300 bg-amber-50/70 text-amber-900 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-100 dark:hover:bg-amber-950/50">
                    Request a fix
                    <Wrench class="size-4" />
                  </Button>
                </a>
              </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
              <div class="rounded-3xl border border-neutral-200 bg-neutral-50 p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center justify-between text-sm text-neutral-500 dark:text-neutral-400">
                  <span>Public files</span>
                  <DownloadIcon class="size-4" />
                </div>
                <div class="mt-3 text-3xl font-semibold text-neutral-950 dark:text-neutral-50">
                  {{ stats.count }}
                </div>
              </div>

              <div class="rounded-3xl border border-neutral-200 bg-neutral-50 p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center justify-between text-sm text-neutral-500 dark:text-neutral-400">
                  <span>Featured picks</span>
                  <Sparkles class="size-4" />
                </div>
                <div class="mt-3 text-3xl font-semibold text-neutral-950 dark:text-neutral-50">
                  {{ stats.featured }}
                </div>
              </div>

              <div class="rounded-3xl border border-neutral-200 bg-neutral-50 p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center justify-between text-sm text-neutral-500 dark:text-neutral-400">
                  <span>Total downloads</span>
                  <ShieldCheck class="size-4" />
                </div>
                <div class="mt-3 text-3xl font-semibold text-neutral-950 dark:text-neutral-50">
                  {{ stats.download_events }}
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card class="border-dashed border-neutral-300 bg-white/70 shadow-sm backdrop-blur-sm dark:border-neutral-800 dark:bg-neutral-950/70">
          <CardHeader>
            <CardTitle class="text-xl text-neutral-950 dark:text-neutral-50">Quick Filter</CardTitle>
            <CardDescription>
              Search the library by keyword, category, or file type.
            </CardDescription>
          </CardHeader>

          <CardContent>
            <form class="space-y-4" @submit.prevent="applyFilters">
              <div class="space-y-2">
                <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200" for="search">Search</label>
                <div class="relative">
                  <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-neutral-400" />
                  <Input id="search" v-model="filters.search" class="pl-10" placeholder="Brand kit, mockup, template..." />
                </div>
              </div>

              <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                  <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200" for="category">Category</label>
                  <select id="category" v-model="filters.category" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:bg-neutral-950">
                    <option value="">All categories</option>
                    <option v-for="category in categories" :key="category" :value="category">
                      {{ category }}
                    </option>
                  </select>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200" for="file_type">File type</label>
                  <select id="file_type" v-model="filters.file_type" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:bg-neutral-950">
                    <option value="">All types</option>
                    <option v-for="fileType in fileTypes" :key="fileType" :value="fileType">
                      {{ fileType }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="flex flex-wrap gap-3">
                <Button type="submit" class="gap-2">
                  Apply filters
                  <ArrowRight class="size-4" />
                </Button>

                <Button v-if="hasActiveFilters" type="button" variant="ghost" @click="resetFilters">
                  Reset
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>

      <section id="download-catalogue" class="space-y-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
          <div>
            <h2 class="text-2xl font-semibold text-neutral-950 dark:text-neutral-50">Available downloads</h2>
            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">
              Curated public assets with direct download access.
            </p>
          </div>

          <Badge variant="outline" class="rounded-full border-neutral-300 px-3 py-1 text-xs uppercase tracking-[0.18em] text-neutral-600 dark:border-neutral-700 dark:text-neutral-300">
            {{ downloads.length }} results
          </Badge>
        </div>

        <div v-if="downloads.length" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
          <Card v-for="download in downloads" :key="download.uuid" class="overflow-hidden border-neutral-200 bg-white/90 shadow-sm transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-200/70 dark:border-neutral-800 dark:bg-neutral-950/90 dark:hover:shadow-black/30">
            <div class="relative aspect-[4/3] overflow-hidden bg-[linear-gradient(135deg,#fef3c7_0%,#fde68a_26%,#f8fafc_100%)] dark:bg-[linear-gradient(135deg,#451a03_0%,#92400e_35%,#111827_100%)]">
              <img v-if="download.poster_url" :src="download.poster_url" :alt="download.title || 'Download preview'" class="h-full w-full object-cover" />
              <div v-else class="flex h-full items-end justify-between p-6 text-neutral-900 dark:text-white">
                <div class="max-w-[14rem] text-xl font-semibold leading-tight">
                  {{ download.title || 'Untitled asset' }}
                </div>
                <FileUp class="size-8 opacity-70" />
              </div>
              <Badge v-if="download.is_featured" class="absolute left-4 top-4 rounded-full bg-white/90 text-neutral-950 shadow-sm dark:bg-neutral-950/90 dark:text-neutral-50">
                Featured
              </Badge>
            </div>

            <CardContent class="space-y-4 p-5">
              <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.16em] text-neutral-500 dark:text-neutral-400">
                  <span>{{ download.category || 'General' }}</span>
                  <span class="size-1 rounded-full bg-neutral-300 dark:bg-neutral-700"></span>
                  <span>{{ download.file_type || 'file' }}</span>
                </div>

                <div>
                  <h3 class="text-lg font-semibold text-neutral-950 dark:text-neutral-50">
                    {{ download.title || 'Untitled asset' }}
                  </h3>

                  <p v-if="download.brand" class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ download.brand }}
                  </p>
                </div>

                <p class="line-clamp-3 text-sm leading-6 text-neutral-600 dark:text-neutral-300">
                  {{ download.description || 'Ready for use, production-safe, and available as a direct download.' }}
                </p>
              </div>

              <div class="flex flex-wrap gap-2 text-xs text-neutral-500 dark:text-neutral-400">
                <span class="rounded-full bg-neutral-100 px-3 py-1 dark:bg-neutral-900">
                  {{ download.formatted_file_size || 'Unknown size' }}
                </span>
                <span class="rounded-full bg-neutral-100 px-3 py-1 dark:bg-neutral-900">
                  {{ download.download_count || 0 }} downloads
                </span>
                <span v-if="download.created_at" class="rounded-full bg-neutral-100 px-3 py-1 dark:bg-neutral-900">
                  {{ download.created_at }}
                </span>
              </div>

              <div class="flex items-center justify-between gap-3">
                <a v-if="download.has_file" :href="downloadFile(download.uuid).url" class="inline-flex">
                  <Button class="gap-2">
                    Download
                    <DownloadIcon class="size-4" />
                  </Button>
                </a>

                <Button v-else variant="outline" disabled>
                  File unavailable
                </Button>

                <Link href="#fix-request" class="text-sm font-medium text-neutral-700 transition hover:text-neutral-950 dark:text-neutral-300 dark:hover:text-white">
                  Need a different format?
                </Link>
              </div>
            </CardContent>
          </Card>
        </div>

        <Card v-else class="border-dashed border-neutral-300 bg-white/80 dark:border-neutral-800 dark:bg-neutral-950/80">
          <CardContent class="flex flex-col items-center gap-3 p-10 text-center">
            <div class="rounded-full bg-neutral-100 p-4 dark:bg-neutral-900">
              <Search class="size-5 text-neutral-500" />
            </div>
            <h3 class="text-lg font-semibold text-neutral-950 dark:text-neutral-50">No downloads matched those filters</h3>
            <p class="max-w-md text-sm leading-6 text-neutral-600 dark:text-neutral-300">
              Reset the filters or request a custom export if the file you need is not currently published.
            </p>
            <Button type="button" variant="outline" @click="resetFilters">Reset filters</Button>
          </CardContent>
        </Card>
      </section>

      <section id="fix-request" class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
        <Card class="border-neutral-200 bg-white/85 shadow-sm dark:border-neutral-800 dark:bg-neutral-950/85">
          <CardHeader>
            <CardTitle class="text-2xl text-neutral-950 dark:text-neutral-50">Request a file fix</CardTitle>
            <CardDescription>
              Send the original file and explain what needs to change. Use this for broken exports, missing variants, or production cleanup.
            </CardDescription>
          </CardHeader>

          <CardContent class="space-y-4 text-sm leading-6 text-neutral-600 dark:text-neutral-300">
            <div class="rounded-3xl bg-neutral-50 p-5 dark:bg-neutral-900">
              <h3 class="font-semibold text-neutral-950 dark:text-neutral-50">What to include</h3>
              <ul class="mt-3 space-y-2">
                <li>The original asset or closest working source file</li>
                <li>A short explanation of the defect or required export</li>
                <li>Your best email address for follow-up</li>
              </ul>
            </div>

            <div class="rounded-3xl bg-amber-50 p-5 text-amber-900 dark:bg-amber-950/30 dark:text-amber-100">
              <h3 class="font-semibold">Accepted formats</h3>
              <p class="mt-2">
                ZIP, PDF, AI, EPS, PSD, XD, FIG, JPG, PNG, SVG, and WEBP up to 20 MB.
              </p>
            </div>
          </CardContent>
        </Card>

        <Card class="border-neutral-200 bg-white/92 shadow-sm dark:border-neutral-800 dark:bg-neutral-950/92">
          <CardHeader>
            <CardTitle>Upload request</CardTitle>
            <CardDescription>
              We store the file and the issue details together so the request can be actioned cleanly.
            </CardDescription>
          </CardHeader>

          <CardContent>
            <form class="space-y-4" @submit.prevent="submitFixRequest">
              <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                  <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200" for="name">Name</label>
                  <Input id="name" v-model="fixForm.name" placeholder="Your name" />
                  <p v-if="fixForm.errors.name" class="text-sm text-red-500">{{ fixForm.errors.name }}</p>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200" for="email">Email</label>
                  <Input id="email" v-model="fixForm.email" type="email" placeholder="you@example.com" />
                  <p v-if="fixForm.errors.email" class="text-sm text-red-500">{{ fixForm.errors.email }}</p>
                </div>
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200" for="notes">What needs fixing?</label>
                <Textarea id="notes" v-model="fixForm.notes" rows="5" placeholder="Example: export as transparent SVG, clean overlapping paths, and include a monochrome version." />
                <p v-if="fixForm.errors.notes" class="text-sm text-red-500">{{ fixForm.errors.notes }}</p>
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200" for="file">Attach file</label>
                <input id="file" type="file" class="block w-full rounded-md border border-dashed border-neutral-300 bg-neutral-50 px-3 py-3 text-sm text-neutral-700 file:mr-4 file:rounded-md file:border-0 file:bg-neutral-950 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-neutral-800 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-200 dark:file:bg-white dark:file:text-neutral-950 dark:hover:file:bg-neutral-200" @change="setFile" />
                <p v-if="fixForm.errors.file" class="text-sm text-red-500">{{ fixForm.errors.file }}</p>
              </div>

              <Button type="submit" class="gap-2" :disabled="fixForm.processing">
                <span>{{ fixForm.processing ? 'Uploading…' : 'Send fix request' }}</span>
                <FileUp class="size-4" />
              </Button>
            </form>
          </CardContent>
        </Card>
      </section>
    </section>
  </div>
</template>