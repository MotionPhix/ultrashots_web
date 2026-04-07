<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Project } from "@/types";
import { show as projectShow } from '@/routes/projects';

const props = defineProps<{
  project: Project;
}>();
</script>

<template>
  <Link
    class="relative block w-full overflow-hidden rounded-lg group"
    :href="projectShow(project.uuid).url">

    <img
      loading="lazy"
      decoding="async" data-nimg="1"
      class="object-cover w-full bg-gray-100 rounded-lg size-40 dark:bg-neutral-800"
      :src="project.poster_url || '/assets/placeholder-project.jpg'" :alt="project.name">

    <div
      class="absolute transition opacity-0 bottom-1 end-1 group-hover:opacity-100">

      <div
        class="flex items-center px-2 py-1 text-gray-800 bg-white border border-gray-200 rounded-lg gap-x-1 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200">

        <svg
          class="shrink-0 size-3"
          xmlns="http://www.w3.org/2000/svg"
          width="24" height="24" viewBox="0 0 24 24"
          fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8" />
          <path d="m21 21-4.3-4.3" />
        </svg>

        <span class="text-xs">View</span>

      </div>

    </div>

  </Link>
</template>
