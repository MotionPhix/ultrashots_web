<script setup lang="ts">
import { destroy as adminDownloadsDestroy } from '@/routes/admin/downloads';
import { getDownload } from '@/routes/public';
import type { Logo } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ImageDown, Trash2 } from 'lucide-vue-next';

defineProps<{
  logoFile: Logo;
}>();
</script>

<template>
  <div class="group relative w-full overflow-hidden rounded-lg bg-white">
    <div class="relative block w-full p-2">
      <img
        loading="lazy"
        decoding="async"
        @contextmenu.prevent
        class="w-full rounded-lg object-contain"
        :src="logoFile.poster_url || '/assets/placeholder-project.jpg'"
        :alt="logoFile.brand || 'Download preview'"
      />

      <div
        class="absolute end-2 bottom-2 opacity-0 transition group-hover:opacity-100"
      >
        <a
          :href="getDownload(logoFile.uuid).url"
          class="flex items-center gap-x-1 rounded bg-white px-2 py-1 text-gray-800 dark:bg-neutral-900 dark:text-neutral-200"
        >
          <ImageDown class="size-3 shrink-0" />

          <span class="text-xs">Download</span>
        </a>
      </div>
    </div>

    <Link
      class="absolute bottom-2 left-2 hidden size-6 items-center justify-center rounded bg-white text-gray-800 group-hover:flex dark:bg-neutral-900 dark:text-neutral-200"
      :href="adminDownloadsDestroy(logoFile.uuid).url"
      method="delete"
      as="button"
      v-if="$page.url.startsWith('/admin')"
    >
      <Trash2 class="size-4" />
    </Link>
  </div>
</template>
