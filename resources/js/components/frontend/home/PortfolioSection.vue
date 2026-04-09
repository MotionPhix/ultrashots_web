<script setup lang="ts">
import type { CaseStudy } from '@/types/home';
import { Link } from '@inertiajs/vue3';
import { index as projectsIndex } from '@/routes/projects';
import { ArrowRight, TrendingUp } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';

defineProps<{ caseStudies: CaseStudy[] }>();
</script>

<template>
    <section id="portfolio" class="relative bg-background py-20 lg:py-28">
        <div class="section-frame">
            <!-- Section header -->
            <div class="mb-16 flex items-end justify-between gap-6">
                <div class="max-w-2xl space-y-4">
                    <span class="inline-flex items-center gap-2 rounded-full border border-border bg-secondary/50 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground font-display">
                        Selected Work
                    </span>
                    <h2 class="font-display text-4xl font-bold leading-tight tracking-tight text-foreground sm:text-5xl lg:text-6xl">
                        Strategic design that drives measurable results.
                    </h2>
                    <p class="text-base leading-relaxed text-muted-foreground font-sans sm:text-lg">
                        Every project solves a business problem through strategy and visual craft. These outcomes speak louder than portfolios.
                    </p>
                </div>
                <Button
                    :as="Link"
                    :href="projectsIndex().url"
                    variant="outline"
                    class="hidden shrink-0 items-center gap-2 rounded-full border border-border bg-background px-6 py-3 text-sm font-semibold text-foreground transition-all duration-200 hover:bg-secondary sm:inline-flex"
                >
                    View all work
                    <ArrowRight class="size-4" />
                </Button>
            </div>

            <!-- Masonry-style grid (Typefolio/Unfold inspired) -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="(study, idx) in caseStudies.slice(0, 9)"
                    :key="study.id"
                    class="group relative overflow-hidden rounded-2xl border border-border bg-secondary/30 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
                    :class="{
                        'sm:col-span-2 lg:col-span-2': idx === 0,
                        'row-span-2': idx === 1 || idx === 4,
                    }"
                >
                    <!-- Image -->
                    <div class="relative aspect-[4/3] overflow-hidden sm:aspect-auto sm:h-full">
                        <img
                            v-if="study.poster_url"
                            :src="study.poster_url"
                            :alt="study.alt || study.title"
                            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/10 to-secondary/20"
                        >
                            <span class="font-display text-6xl font-bold text-primary/30">
                                {{ study.title.charAt(0) }}
                            </span>
                        </div>
                        
                        <!-- Gradient overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-background via-background/20 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100" />
                    </div>

                    <!-- Content overlay -->
                    <div class="absolute inset-x-0 bottom-0 p-6 translate-y-4 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-primary font-heading mb-1">
                                    {{ study.category || study.sector }}
                                </p>
                                <h3 class="font-display text-xl font-bold text-foreground sm:text-2xl">
                                    {{ study.title }}
                                </h3>
                            </div>
                            
                            <p class="text-sm leading-relaxed text-muted-foreground font-sans line-clamp-2">
                                {{ study.short_description || study.summary }}
                            </p>

                            <!-- Metrics -->
                            <div v-if="study.metrics && study.metrics.length > 0" class="flex flex-wrap gap-2 pt-2">
                                <div
                                    v-for="m in study.metrics.slice(0, 2)"
                                    :key="m.label"
                                    class="flex items-center gap-1.5 rounded-lg border border-border bg-background/95 backdrop-blur-sm px-3 py-2"
                                >
                                    <TrendingUp class="size-3.5 text-primary" />
                                    <div>
                                        <p class="text-sm font-bold text-foreground font-display">{{ m.value }}</p>
                                        <p class="text-[10px] uppercase tracking-wider text-muted-foreground font-sans">{{ m.label }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- CTA -->
                            <Button
                                v-if="study.project_url"
                                :as="Link"
                                :href="study.project_url"
                                variant="ghost"
                                class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary/80 transition-colors font-sans h-auto px-0 py-0"
                            >
                                View case study
                                <ArrowRight class="size-4" />
                            </Button>
                        </div>
                    </div>

                    <!-- Always visible minimal info (mobile) -->
                    <div class="p-5 sm:hidden">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-muted-foreground font-heading mb-1">
                            {{ study.category || study.sector }}
                        </p>
                        <h3 class="font-display text-lg font-bold text-foreground">{{ study.title }}</h3>
                        <div v-if="study.metrics?.[0]" class="mt-2 flex items-center gap-1.5">
                            <TrendingUp class="size-3.5 text-primary" />
                            <span class="text-sm font-bold text-foreground font-display">{{ study.metrics[0].value }}</span>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Empty state -->
            <div v-if="caseStudies.length === 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="n in 6"
                    :key="n"
                    class="aspect-[4/3] animate-pulse rounded-2xl border border-border bg-secondary/50"
                />
            </div>

            <!-- Mobile CTA -->
            <div class="mt-10 flex items-center justify-center sm:hidden">
                <Button
                    :as="Link"
                    :href="projectsIndex().url"
                    variant="outline"
                    class="inline-flex items-center gap-2 rounded-full border border-border bg-background px-7 py-3.5 text-sm font-semibold text-foreground shadow-sm transition-all duration-200 hover:bg-secondary"
                >
                    Browse all projects
                    <ArrowRight class="size-4" />
                </Button>
            </div>
        </div>
    </section>
</template>
