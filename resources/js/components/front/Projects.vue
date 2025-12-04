<script setup lang="ts">
import { Project } from '@/types';
import { Link } from '@inertiajs/vue3';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { onMounted, onUnmounted, ref, computed } from 'vue';

gsap.registerPlugin(ScrollTrigger);

const props = defineProps<{
  projects?: Project[],
  smallColumns?: boolean
}>();

const main = ref();
let ctx: any;

// Computed property to get the image URL for a project
const getProjectImageUrl = (project: Project): string => {
  // First try to get poster URL
  if (project.poster_url) {
    return project.poster_url;
  }

  // Then try gallery images
  if (project.gallery_images && project.gallery_images.length > 0) {
    return project.gallery_images[0].medium_url || project.gallery_images[0].url;
  }

  // Fallback to media array (legacy)
  if (project.media && project.media.length > 0) {
    return project.media[0].original_url;
  }

  // Final fallback to placeholder
  return '/assets/placeholder-project.jpg';
};

// Computed property to get alt text for project image
const getProjectImageAlt = (project: Project): string => {
  if (project.gallery_images && project.gallery_images.length > 0) {
    return project.gallery_images[0].name || `${project.name} preview`;
  }

  if (project.media && project.media.length > 0) {
    return project.media[0].name || `${project.name} preview`;
  }

  return `${project.name} project preview`;
};

onMounted(() => {
  ctx = gsap.context(() => {
    const cards = gsap.utils.toArray('.card');

    cards.forEach((card: any, index) => {
      gsap.fromTo(card,
        { opacity: 0, y: 50 },
        {
          opacity: 1,
          y: 0,
          duration: 0.8,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: card,
            start: 'top bottom-=100',
            end: 'bottom center',
            toggleActions: 'play none none reverse',
          },
          delay: index * 0.2 // Stagger effect
        }
      );
    });
  }, main.value);
});

onUnmounted(() => {
  if (ctx) {
    ctx.revert(); // Clean up animations
  }
});
</script>

<template>
  <!-- projects -->
  <div class="mt-10 sm:mt-14">

    <!-- No projects message -->
    <div
      v-if="!projects || projects.length === 0"
      class="text-center py-12 text-gray-500 dark:text-neutral-400">
      <p>No featured projects available at the moment.</p>
    </div>

    <!-- Image Grid -->
    <div
      v-else
      ref="main"
      class="grid gap-4"
      :class="smallColumns ? 'grid-cols-3' : 'grid-cols-2'">

      <Link
        class="relative block overflow-hidden rounded-lg card group"
        :href="route('projects.show', project.uuid)"
        v-for="project in projects"
        :key="project.uuid">

        <!-- Project Image -->
        <div class="relative aspect-square overflow-hidden rounded-lg bg-gray-100 dark:bg-neutral-800">
          <img
            class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105"
            :src="getProjectImageUrl(project)"
            :alt="getProjectImageAlt(project)"
            loading="lazy" />

          <!-- Overlay gradient -->
          <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

          <!-- Project info overlay -->
          <div class="absolute bottom-0 left-0 right-0 p-4 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
            <h3 class="font-semibold text-sm mb-1 line-clamp-1">{{ project.name }}</h3>
            <p class="text-xs opacity-90 line-clamp-2" v-if="project.short_description">
              {{ project.short_description }}
            </p>
            <div class="flex items-center justify-between mt-2">
              <span class="text-xs bg-white/20 px-2 py-1 rounded-full" v-if="project.production_type">
                {{ project.production_type }}
              </span>
              <span class="text-xs" v-if="project.created_at">
                {{ project.created_at }}
              </span>
            </div>
          </div>
        </div>

        <!-- View button (always visible on hover) -->
        <div class="absolute transition-all duration-300 opacity-0 top-2 right-2 group-hover:opacity-100">
          <div
            class="flex items-center px-3 py-2 text-white bg-black/50 backdrop-blur-sm border border-white/20 rounded-lg gap-x-2 hover:bg-black/70 transition-colors">
            <svg
              class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg"
              width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round">
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.3-4.3" />
            </svg>
            <span class="text-sm font-medium">View</span>
          </div>
        </div>

        <!-- Technology badges -->
        <div class="absolute top-2 left-2 flex flex-wrap gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
             v-if="project.technologies && project.technologies.length > 0">
          <span
            v-for="tech in project.technologies.slice(0, 2)"
            :key="tech"
            class="text-xs bg-blue-500/80 text-white px-2 py-1 rounded-full backdrop-blur-sm">
            {{ tech }}
          </span>
        </div>

      </Link>

    </div>
    <!-- End Image Grid -->

    <!-- View all projects link -->
    <div class="mt-8 text-center" v-if="projects && projects.length > 0">
      <Link
        :href="route('projects.index')"
        class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-neutral-400 dark:hover:text-neutral-200 transition-colors">
        View all projects
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
      </Link>
    </div>
  </div>
  <!-- End projects -->
</template>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
