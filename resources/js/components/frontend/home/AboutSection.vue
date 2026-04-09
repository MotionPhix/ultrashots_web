<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { index as projectsIndex } from '@/routes/projects';
import type { LandingAbout } from '@/types/home';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Eye, Target, TrendingUp } from 'lucide-vue-next';

defineProps<{
    about: LandingAbout;
}>();

// Icon mapping for design tips
const iconMap: Record<string, any> = {
    eye: Eye,
    target: Target,
    'trending-up': TrendingUp,
};

function getIcon(iconName: string) {
    return iconMap[iconName] || Target;
}
</script>

<template>
    <section id="about" class="relative bg-secondary/30 py-20 lg:py-28">
        <div class="section-frame">
            <!-- Section header -->
            <div class="mb-16 max-w-4xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-border bg-background px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground font-display">
                    About Me
                </span>

                <h2 class="mt-6 font-display text-4xl font-bold leading-tight tracking-tight text-foreground sm:text-5xl lg:text-6xl">
                    {{ about.title }}
                </h2>
            </div>

            <div class="grid gap-12 lg:grid-cols-[1fr_0.9fr] lg:items-start">
                <!-- Left: Story & Design Tips -->
                <div class="space-y-10">
                    <!-- Story paragraphs -->
                    <div class="space-y-5 text-base leading-relaxed text-muted-foreground font-sans sm:text-lg">
                        <p v-for="(paragraph, idx) in about.story" :key="idx">
                            {{ paragraph }}
                        </p>
                    </div>

                    <!-- Stats -->
                    <div class="grid gap-4 sm:grid-cols-3">
                        <article
                            v-for="stat in about.stats"
                            :key="stat.label"
                            class="rounded-2xl border border-border bg-background p-6 shadow-sm transition-all duration-200 hover:shadow-md"
                        >
                            <p class="font-display text-4xl font-bold text-primary">{{ stat.value }}</p>
                            <p class="mt-2 text-sm text-muted-foreground font-sans">{{ stat.label }}</p>
                        </article>
                    </div>

                    <!-- Design Tips (expertise showcase) -->
                    <div v-if="about.design_tips && about.design_tips.length > 0" class="space-y-6 pt-6 border-t border-border">
                        <h3 class="font-display text-xl font-semibold text-foreground">Design Insights</h3>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <article
                                v-for="tip in about.design_tips"
                                :key="tip.title"
                                class="group rounded-xl border border-border bg-background p-5 transition-all duration-200 hover:border-primary/30 hover:shadow-md"
                            >
                                <div class="mb-3 flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                    <component :is="getIcon(tip.icon)" class="size-5" />
                                </div>
                                <h4 class="font-heading text-sm font-semibold text-foreground">{{ tip.title }}</h4>
                                <p class="mt-2 text-xs leading-relaxed text-muted-foreground font-sans">{{ tip.description }}</p>
                                <p class="mt-3 text-xs font-medium text-primary font-sans">{{ tip.action }}</p>
                            </article>
                        </div>
                    </div>
                </div>

                <!-- Right: Recent Work Showcase -->
                <div class="lg:sticky lg:top-24">
                    <div v-if="about.recent_work" class="group relative overflow-hidden rounded-3xl bg-background">
                        <!-- Project Image with Full Bleed -->
                        <Link
                          :href="about.recent_work.url"
                          class="relative aspect-[4/5] overflow-hidden">
                            <img
                                v-if="about.recent_work.poster_url"
                                :src="about.recent_work.poster_url"
                                :alt="about.recent_work.title"
                                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                            />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/20 to-secondary/30"
                            >
                                <span class="font-display text-8xl font-bold text-primary/40">
                                    {{ about.recent_work.title.charAt(0) }}
                                </span>
                            </div>

                            <!-- Gradient Overlay for Text Readability -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent" />

                            <!-- Status Badge - Top Left -->
                            <div class="absolute top-4 left-4">
                                <span class="inline-flex items-center gap-2 rounded-full bg-white/95 backdrop-blur-sm px-4 py-2 text-sm font-semibold text-foreground shadow-lg">
                                    <span class="size-2 animate-pulse rounded-full bg-emerald-500" />
                                    Completed
                                </span>
                            </div>

                            <!-- Action Buttons - Top Right -->
                            <div class="absolute top-4 right-4 flex flex-col gap-2 z-30">
                                <Link
                                  as="button"
                                  class="size-5 flex size-10 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm text-white transition-colors hover:bg-white/30"
                                  :href="about.recent_work.url">
                                  <ArrowRight class="size-5 -rotate-45" />
                                </Link>
                            </div>

                            <!-- Content Overlay at Bottom -->
                            <div class="absolute bottom-0 left-0 right-0 p-6 space-y-4">
                                <!-- Title and Category -->
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70 font-heading mb-2">
                                        Recent Work
                                    </p>
                                    <h3 class="font-display text-2xl font-bold text-white leading-tight">
                                        {{ about.recent_work.title }}
                                    </h3>
                                    <p class="text-sm text-white/80 font-sans mt-1 flex items-center gap-2">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        {{ about.recent_work.client }}
                                    </p>
                                </div>

                                <!-- Description -->
                                <p class="text-sm leading-relaxed text-white/70 font-sans line-clamp-2">
                                    {{ about.recent_work.description }}
                                </p>

                                <!-- Metrics Row -->
                                <div v-if="about.recent_work.metric" class="flex items-center gap-6 border-t border-white/20 pt-4">
                                    <div class="flex-1">
                                        <p class="text-xs uppercase tracking-wider text-white/60 font-sans mb-1">
                                            {{ about.recent_work.metric.label }}
                                        </p>
                                        <p class="text-3xl font-bold text-white font-display">
                                            {{ about.recent_work.metric.value }}
                                        </p>
                                    </div>
                                    <div class="h-10 w-px bg-white/20" />
                                    <div class="flex-1">
                                        <p class="text-xs uppercase tracking-wider text-white/60 font-sans mb-1">
                                            Category
                                        </p>
                                        <p class="text-lg font-semibold text-white font-display">
                                            {{ about.recent_work.category }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Fallback if no recent work -->
                    <div v-else class="rounded-2xl border border-border bg-background p-8 text-center shadow-sm">
                        <p class="text-muted-foreground font-sans mb-4">Check out our latest projects</p>
                        <Button
                            :as="Link"
                            :href="projectsIndex().url"
                            variant="outline"
                            class="gap-2"
                        >
                            Browse Portfolio
                            <ArrowRight class="size-4" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
