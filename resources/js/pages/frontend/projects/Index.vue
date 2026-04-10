<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index as projectIndexRoute, show as projectShow } from '@/routes/projects';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
// Project type is globally available from types/index.d.ts
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

import AnimatedCTAWithAvailability from '@/components/frontend/ui/AnimatedCTAWithAvailability.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import { Project } from '@/types';
import { useStorage } from '@vueuse/core';
import { ArrowRight, ArrowUpRight, Building2, ExternalLink, Filter, Github, Search, Sparkles, X } from 'lucide-vue-next';

gsap.registerPlugin(ScrollTrigger);

// Props
const props = defineProps<{
    projects: {
        data: Project[];
    };
    filters: {
        search: string | null;
        category: string | null;
        status: string | null;
        year: string | null;
        sort: string;
    };
    categories: string[];
    statuses: string[];
    years: number[];
}>();

// Refs
const heroRef = ref<HTMLElement>();
const heroCanvasRef = ref<HTMLCanvasElement>();
const filtersRef = ref<HTMLElement>();
const projectsGridRef = ref<HTMLElement>();
const projectCards = ref<HTMLElement[]>([]);

// State
const searchInput = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || 'all');
const selectedStatus = ref(props.filters.status || 'all');
const selectedYear = ref(props.filters.year || 'all');
const sortBy = ref(props.filters.sort || 'newest');
const viewMode = useStorage<'grid' | 'list'>('projects_view_mode', 'grid');
const showFilters = ref(false);

// Computed
const filteredProjects = computed(() => props.projects.data);

const sortedYears = computed(() => [...props.years].sort((a, b) => b - a));

const featuredProjects = computed(() => props.projects.data.filter(p => p.is_featured).slice(0, 3));

const getStatusConfig = (status: string) => {
    const config: Record<string, { label: string; color: string }> = {
        completed: { label: 'Completed', color: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/20' },
        in_progress: { label: 'In Progress', color: 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/20' },
        on_hold: { label: 'On Hold', color: 'bg-slate-500/10 text-slate-700 dark:text-slate-400 border-slate-500/20' },
        archived: { label: 'Archived', color: 'bg-slate-500/10 text-slate-700 dark:text-slate-400 border-slate-500/20' },
    };
    return config[status] || { label: 'Unknown', color: '' };
};

const getProjectYear = (project: Project) => {
    return project.created_at ? new Date(project.created_at).getFullYear() : 'N/A';
};

const clearFilters = () => {
    searchInput.value = '';
    selectedCategory.value = 'all';
    selectedStatus.value = 'all';
    selectedYear.value = 'all';
    sortBy.value = 'newest';
    applyFilters();
};

const applyFilters = () => {
    const params: Record<string, any> = {};

    if (searchInput.value.trim()) {
        params.search = searchInput.value;
    }

    if (selectedCategory.value !== 'all') {
        params.category = selectedCategory.value;
    }

    if (selectedStatus.value !== 'all') {
        params.status = selectedStatus.value;
    }

    if (selectedYear.value !== 'all') {
        params.year = selectedYear.value;
    }

    if (sortBy.value !== 'newest') {
        params.sort = sortBy.value;
    }

    router.get(projectIndexRoute().url, params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

watch([searchInput, selectedCategory, selectedStatus, selectedYear, sortBy], () => {
    const timeout = setTimeout(() => applyFilters(), 500);
    return () => clearTimeout(timeout);
});

// GSAP Animations
const animateHero = () => {
    if (!heroRef.value) return;

    const elements = heroRef.value.querySelectorAll('[data-animate]');

    gsap.fromTo(elements,
        { opacity: 0, y: 60 },
        {
            opacity: 1,
            y: 0,
            duration: 1.2,
            stagger: 0.2,
            ease: 'power3.out',
        }
    );

    // Animate background grid lines
    if (heroCanvasRef.value) {
        gsap.fromTo(heroCanvasRef.value,
            { opacity: 0 },
            { opacity: 1, duration: 2, ease: 'power2.out' }
        );
        drawGrid(heroCanvasRef.value);
    }
};

// Draw animated grid on canvas
const drawGrid = (canvas: HTMLCanvasElement) => {
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;

    const spacing = 60;
    const dotSize = 1.5;

    const animate = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const isDark = document.documentElement.classList.contains('dark');
        const dotColor = isDark ? 'rgba(255, 255, 255, 0.15)' : 'rgba(0, 0, 0, 0.08)';
        const lineColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.03)';

        // Draw dots
        for (let x = 0; x < canvas.width; x += spacing) {
            for (let y = 0; y < canvas.height; y += spacing) {
                const distance = Math.sqrt(
                    Math.pow(x - canvas.width / 2, 2) +
                    Math.pow(y - canvas.height / 2, 2)
                );
                const maxDistance = Math.sqrt(
                    Math.pow(canvas.width / 2, 2) +
                    Math.pow(canvas.height / 2, 2)
                );
                const opacity = 1 - (distance / maxDistance);
                const size = dotSize * (0.5 + opacity * 0.5);

                ctx.beginPath();
                ctx.arc(x, y, size, 0, Math.PI * 2);
                ctx.fillStyle = dotColor;
                ctx.fill();

                // Draw connecting lines (subtle)
                if (x < canvas.width - spacing && y < canvas.height - spacing) {
                    ctx.beginPath();
                    ctx.moveTo(x, y);
                    ctx.lineTo(x + spacing, y);
                    ctx.lineTo(x + spacing, y + spacing);
                    ctx.strokeStyle = lineColor;
                    ctx.lineWidth = 0.5;
                    ctx.stroke();
                }
            }
        }

        requestAnimationFrame(animate);
    };

    animate();

    // Handle resize
    window.addEventListener('resize', () => {
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
    });
};

const animateFilters = () => {
    if (!filtersRef.value) return;

    gsap.fromTo(filtersRef.value,
        { opacity: 0, y: 30 },
        {
            opacity: 1,
            y: 0,
            duration: 0.6,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: filtersRef.value,
                start: 'top 85%',
                once: true,
            },
        }
    );
};

const animateProjectCards = () => {
    if (!projectCards.value.length) return;

    projectCards.value.forEach((card, index) => {
        gsap.fromTo(card,
            { opacity: 0, y: 80, scale: 0.95 },
            {
                opacity: 1,
                y: 0,
                scale: 1,
                duration: 0.8,
                delay: index * 0.08,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: card,
                    start: 'top 90%',
                    once: true,
                },
            }
        );
    });
};

// Lifecycle
onMounted(async () => {
    await nextTick();

    setTimeout(() => {
        animateHero();
        animateFilters();
        animateProjectCards();
    }, 100);
});

defineOptions({ layout: AppLayout });
</script>

<template>
    <Head title="Projects | UltraShots Portfolio" />

    <div class="min-h-screen bg-background">
        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-background pt-24 pb-20 lg:pt-40 lg:pb-32">
            <!-- Animated Grid Background -->
            <canvas
                ref="heroCanvasRef"
                class="absolute inset-0 h-full w-full opacity-30 dark:opacity-20"
            />

            <!-- Floating Geometric Shapes -->
            <div class="absolute inset-0 overflow-hidden">
                <!-- Large circle - top right -->
                <div class="absolute -right-32 -top-32 size-[600px] animate-[float_20s_ease-in-out_infinite] rounded-full border border-primary/10 dark:border-primary/20" />

                <!-- Medium circle - bottom left -->
                <div class="absolute -bottom-48 -left-48 size-[500px] animate-[float_25s_ease-in-out_infinite_reverse] rounded-full border border-primary/10 dark:border-primary/20" />

                <!-- Small circles scattered -->
                <div class="absolute right-1/4 top-1/3 size-32 animate-[float_15s_ease-in-out_infinite] rounded-full bg-primary/5 dark:bg-primary/10 blur-xl" />
                <div class="absolute bottom-1/3 left-1/4 size-24 animate-[float_18s_ease-in-out_infinite_reverse] rounded-full bg-primary/5 dark:bg-primary/10 blur-xl" />
                <div class="absolute right-1/3 bottom-1/4 size-16 animate-[float_12s_ease-in-out_infinite] rounded-full bg-primary/5 dark:bg-primary/10 blur-lg" />

                <!-- Diagonal lines -->
                <div class="absolute left-0 top-1/4 h-px w-1/3 rotate-45 bg-gradient-to-r from-transparent via-primary/20 to-transparent" />
                <div class="absolute right-0 top-1/2 h-px w-1/4 -rotate-45 bg-gradient-to-r from-transparent via-primary/20 to-transparent" />
                <div class="absolute bottom-1/3 left-1/4 h-px w-1/5 rotate-12 bg-gradient-to-r from-transparent via-primary/15 to-transparent" />
            </div>

            <!-- Vertical accent lines -->
            <div class="absolute left-8 top-0 hidden h-full w-px bg-gradient-to-b from-transparent via-primary/20 to-transparent lg:block" />
            <div class="absolute right-8 top-0 hidden h-full w-px bg-gradient-to-b from-transparent via-primary/20 to-transparent lg:block" />

            <!-- Content -->
            <div ref="heroRef" class="relative mx-auto max-w-7xl px-6 lg:px-8">
                <!-- Eyebrow with animated border -->
                <div data-animate class="mb-8 flex justify-center">
                    <div class="relative overflow-hidden rounded-full border border-primary/20 px-6 py-3 backdrop-blur-sm">
                        <!-- Animated shimmer border -->
                        <div class="absolute inset-0 animate-[shimmer_3s_linear_infinite] bg-gradient-to-r from-transparent via-primary/30 to-transparent" />
                        <span class="relative flex items-center gap-3 font-heading text-xs font-semibold uppercase tracking-[0.25em] text-primary">
                            <Sparkles class="size-4 animate-pulse" />
                            Portfolio & Case Studies
                        </span>
                    </div>
                </div>

                <!-- Main Headline with split animation -->
                <div data-animate class="text-center">
                    <h1 class="font-display text-5xl font-bold leading-[0.9] tracking-tight text-foreground sm:text-7xl lg:text-9xl">
                        <span class="block">Selected</span>
                        <span class="block mt-2 bg-gradient-to-r from-foreground via-foreground to-muted-foreground bg-clip-text text-transparent">
                            Works
                        </span>
                    </h1>
                </div>

                <!-- Description with fade-in -->
                <div data-animate class="mx-auto mt-8 max-w-2xl text-center">
                    <p class="font-sans text-lg leading-relaxed text-muted-foreground sm:text-xl">
                        A curated collection of brand identities, UI systems, and digital experiences that transformed businesses and exceeded expectations.
                    </p>
                </div>

                <!-- Decorative divider with dots -->
                <div data-animate class="mx-auto mt-12 flex max-w-xs items-center justify-between">
                    <div class="h-px flex-1 bg-gradient-to-r from-transparent to-primary/30" />
                    <div class="flex items-center gap-3 px-4">
                        <div class="size-2 rounded-full bg-primary/40" />
                        <div class="size-1.5 rounded-full bg-primary/60" />
                        <div class="size-2 rounded-full bg-primary/40" />
                    </div>
                    <div class="h-px flex-1 bg-gradient-to-l from-transparent to-primary/30" />
                </div>
            </div>
        </section>

        <!-- Featured Projects -->
        <section v-if="featuredProjects.length > 0" class="bg-background py-16 lg:py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-12 text-center">
                    <span class="font-heading text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">
                        Highlighted
                    </span>
                    <h2 class="mt-3 font-display text-3xl font-bold text-foreground sm:text-4xl">
                        Featured Case Studies
                    </h2>
                    <p class="mt-4 font-sans text-muted-foreground">
                        Deep dives into our most impactful brand transformations
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <Link
                        v-for="project in featuredProjects"
                        :key="project.uuid"
                        :href="projectShow(project.uuid).url"
                        class="group relative overflow-hidden rounded-3xl bg-background shadow-xl transition-all duration-500 hover:shadow-2xl"
                    >
                        <div class="relative aspect-[4/5] overflow-hidden">
                            <img
                                :src="project.poster_url || '/assets/placeholder-project.jpg'"
                                :alt="project.name"
                                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                loading="lazy"
                            />

                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent dark:from-black/95 dark:via-black/50" />

                            <!-- Featured Badge -->
                            <div class="absolute top-4 left-4">
                                <span class="inline-flex items-center gap-2 rounded-full text-black bg-white/95 dark:bg-foreground/95 backdrop-blur-sm px-4 py-2 text-sm font-semibold shadow-lg">
                                    <Sparkles class="size-4 text-amber-500 dark:text-amber-400" />
                                    Featured
                                </span>
                            </div>

                            <!-- View Project Button -->
                            <div class="absolute top-4 right-4">
                                <button class="flex size-10 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm text-white transition-colors hover:bg-white/30 dark:hover:bg-white/40">
                                    <ArrowUpRight class="size-5" />
                                </button>
                            </div>

                            <!-- Content Overlay -->
                            <div class="absolute bottom-0 left-0 right-0 p-6">
                                <h3 class="font-display text-2xl font-bold text-white leading-tight">
                                    {{ project.name }}
                                </h3>
                                <p class="mt-2 font-sans text-sm text-white/80 line-clamp-2">
                                    {{ project.short_description }}
                                </p>
                                <div class="mt-4 flex items-center gap-3">
                                    <Badge variant="outline" class="border-white/30 bg-white/10 text-white backdrop-blur-sm dark:border-white/40">
                                        {{ project.category || 'Branding' }}
                                    </Badge>
                                    <Badge variant="outline" class="border-white/30 bg-white/10 text-white backdrop-blur-sm dark:border-white/40">
                                        {{ getProjectYear(project) }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Main Projects Grid -->
        <section class="bg-secondary/30 py-16 lg:py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="mb-12">
                    <h2 class="font-display text-3xl font-bold text-foreground sm:text-4xl">
                        All Projects
                    </h2>
                    <p class="mt-3 font-sans text-muted-foreground">
                        Browse our complete portfolio of brand transformations and digital experiences
                    </p>
                </div>

                <!-- Filters -->
                <div ref="filtersRef" class="mb-10 space-y-6">
                    <!-- Search and View Toggle -->
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="relative flex-1 sm:max-w-md">
                            <Search class="absolute left-4 top-1/2 size-5 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                v-model="searchInput"
                                placeholder="Search projects..."
                                class="h-12 pl-12 pr-4 font-sans bg-background border-input text-foreground placeholder:text-muted-foreground"
                            />
                        </div>

                        <div class="flex items-center gap-3">
                            <ToggleGroup
                                v-model="viewMode"
                                type="single"
                                class="rounded-lg border border-input bg-background p-1"
                            >
                                <ToggleGroupItem
                                    value="grid"
                                    aria-label="Grid view"
                                    class="data-[state=on]:bg-primary data-[state=on]:text-primary-foreground text-muted-foreground"
                                >
                                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                </ToggleGroupItem>
                                <ToggleGroupItem
                                    value="list"
                                    aria-label="List view"
                                    class="data-[state=on]:bg-primary data-[state=on]:text-primary-foreground text-muted-foreground"
                                >
                                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                </ToggleGroupItem>
                            </ToggleGroup>

                            <Button
                                variant="outline"
                                size="icon"
                                class="size-12 lg:hidden"
                                @click="showFilters = !showFilters"
                            >
                                <Filter class="size-5" />
                            </Button>
                        </div>
                    </div>

                    <!-- Filter Controls -->
                    <div :class="['grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4', showFilters ? 'block' : 'hidden lg:grid']">
                        <div class="relative">
                            <select
                                v-model="selectedCategory"
                                class="h-12 w-full rounded-md border border-input bg-background px-4 py-2 font-sans text-sm text-foreground ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option value="all">All Categories</option>
                                <option v-for="category in categories" :key="category" :value="category">
                                    {{ category }}
                                </option>
                            </select>
                        </div>

                        <div class="relative">
                            <select
                                v-model="selectedStatus"
                                class="h-12 w-full rounded-md border border-input bg-background px-4 py-2 font-sans text-sm text-foreground ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option value="all">All Status</option>
                                <option v-for="status in statuses" :key="status" :value="status">
                                    {{ getStatusConfig(status).label }}
                                </option>
                            </select>
                        </div>

                        <div class="relative">
                            <select
                                v-model="selectedYear"
                                class="h-12 w-full rounded-md border border-input bg-background px-4 py-2 font-sans text-sm text-foreground ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option value="all">All Years</option>
                                <option v-for="year in sortedYears" :key="year" :value="year.toString()">
                                    {{ year }}
                                </option>
                            </select>
                        </div>

                        <div class="relative">
                            <select
                                v-model="sortBy"
                                class="h-12 w-full rounded-md border border-input bg-background px-4 py-2 font-sans text-sm text-foreground ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="name">Name A-Z</option>
                                <option value="featured">Featured First</option>
                            </select>
                        </div>
                    </div>

                    <!-- Active Filters -->
                    <div
                        v-if="searchInput || selectedCategory !== 'all' || selectedStatus !== 'all' || selectedYear !== 'all'"
                        class="flex flex-wrap items-center gap-2 rounded-lg border border-border bg-background p-4"
                    >
                        <span class="font-sans text-sm font-medium text-muted-foreground">Active filters:</span>

                        <Badge
                            v-if="searchInput"
                            variant="secondary"
                            class="flex items-center gap-1 bg-secondary/50 text-secondary-foreground dark:bg-secondary/30"
                        >
                            Search: {{ searchInput }}
                            <X class="size-3 cursor-pointer" @click="searchInput = ''" />
                        </Badge>

                        <Badge
                            v-if="selectedCategory !== 'all'"
                            variant="secondary"
                            class="flex items-center gap-1 bg-secondary/50 text-secondary-foreground dark:bg-secondary/30"
                        >
                            Category: {{ selectedCategory }}
                            <X class="size-3 cursor-pointer" @click="selectedCategory = 'all'" />
                        </Badge>

                        <Badge
                            v-if="selectedStatus !== 'all'"
                            variant="secondary"
                            class="flex items-center gap-1 bg-secondary/50 text-secondary-foreground dark:bg-secondary/30"
                        >
                            Status: {{ getStatusConfig(selectedStatus).label }}
                            <X class="size-3 cursor-pointer" @click="selectedStatus = 'all'" />
                        </Badge>

                        <Badge
                            v-if="selectedYear !== 'all'"
                            variant="secondary"
                            class="flex items-center gap-1 bg-secondary/50 text-secondary-foreground dark:bg-secondary/30"
                        >
                            Year: {{ selectedYear }}
                            <X class="size-3 cursor-pointer" @click="selectedYear = 'all'" />
                        </Badge>

                        <Button variant="ghost" size="sm" @click="clearFilters" class="ml-auto text-muted-foreground hover:text-foreground">
                            Clear All
                        </Button>
                    </div>
                </div>

                <!-- Results Count -->
                <div class="mb-8 flex items-center justify-between">
                    <p class="font-sans text-sm text-muted-foreground">
                        Showing <span class="font-semibold text-foreground">{{ filteredProjects.length }}</span> of
                        <span class="font-semibold text-foreground">{{ props.projects.data.length }}</span> projects
                    </p>
                </div>

                <!-- Projects Grid/List -->
                <div ref="projectsGridRef">
                    <!-- Grid View -->
                    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <Link
                            v-for="project in filteredProjects"
                            :key="project.uuid"
                            :href="projectShow(project.uuid).url"
                            :ref="(el: any) => { if (el) projectCards.push(el.$el) }"
                            class="project-card group relative overflow-hidden rounded-3xl bg-background shadow-lg transition-all duration-500 hover:shadow-2xl"
                        >
                            <div class="relative aspect-[4/5] overflow-hidden">
                                <img
                                    :src="project.poster_url || '/assets/placeholder-project.jpg'"
                                    :alt="project.name"
                                    class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    loading="lazy"
                                />

                                <!-- Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent" />

                                <!-- Status Badge (Top Left) -->
                                <div class="absolute top-4 left-4">
                                    <Badge
                                        :class="getStatusConfig(project.status).color"
                                        variant="outline"
                                        class="border-white/20 bg-white/95 backdrop-blur-sm font-sans text-xs"
                                    >
                                        {{ getStatusConfig(project.status).label }}
                                    </Badge>
                                </div>

                                <!-- Featured Badge -->
                                <div v-if="project.is_featured" class="absolute top-4 right-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500 dark:bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white shadow-lg">
                                        <Sparkles class="size-3" />
                                        Featured
                                    </span>
                                </div>

                                <!-- Quick Actions -->
                                <div class="absolute top-20 right-4 flex flex-col gap-2 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    <button
                                        v-if="project.live_url"
                                        class="flex size-10 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm text-white transition-colors hover:bg-white/30 dark:hover:bg-white/40"
                                        @click.stop
                                        @click.prevent
                                    >
                                        <a :href="project.live_url" target="_blank" rel="noopener noreferrer" class="size-full flex items-center justify-center">
                                            <ExternalLink class="size-4" />
                                        </a>
                                    </button>
                                    <button
                                        v-if="project.github_url"
                                        class="flex size-10 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm text-white transition-colors hover:bg-white/30 dark:hover:bg-white/40"
                                        @click.stop
                                        @click.prevent
                                    >
                                        <a :href="project.github_url" target="_blank" rel="noopener noreferrer" class="size-full flex items-center justify-center">
                                            <Github class="size-4" />
                                        </a>
                                    </button>
                                </div>

                                <!-- Content Overlay (Bottom) -->
                                <div class="absolute bottom-0 left-0 right-0 p-6">
                                    <!-- Client/Category -->
                                    <div class="mb-3 flex items-center gap-2">
                                        <Badge variant="outline" class="border-white/30 bg-white/10 text-white backdrop-blur-sm font-sans text-xs">
                                            {{ project.category || 'Branding' }}
                                        </Badge>
                                        <span class="font-sans text-xs text-white/70">
                                            {{ getProjectYear(project) }}
                                        </span>
                                    </div>

                                    <!-- Title -->
                                    <h3 class="font-display text-2xl font-bold text-white leading-tight">
                                        {{ project.name }}
                                    </h3>

                                    <!-- Description -->
                                    <p class="mt-2 font-sans text-sm text-white/80 line-clamp-2">
                                        {{ project.short_description }}
                                    </p>

                                    <!-- CTA Button -->
                                    <div class="mt-4">
                                        <Button
                                            variant="secondary"
                                            class="w-full gap-2 font-sans"
                                        >
                                            View Case Study
                                            <ArrowRight class="size-4" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- List View -->
                    <div v-else class="space-y-6">
                        <Link
                            v-for="project in filteredProjects"
                            :key="project.uuid"
                            :href="projectShow(project.uuid).url"
                            :ref="(el: any) => { if (el) projectCards.push(el.$el) }"
                            class="project-card group flex gap-6 overflow-hidden rounded-2xl bg-background p-4 shadow-md transition-all duration-300 hover:shadow-xl dark:bg-card dark:border dark:border-border"
                        >
                            <div class="relative h-40 w-40 shrink-0 overflow-hidden rounded-xl">
                                <img
                                    :src="project.poster_url || '/assets/placeholder-project.jpg'"
                                    :alt="project.name"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    loading="lazy"
                                />
                            </div>

                            <div class="flex flex-1 flex-col justify-between">
                                <div>
                                    <div class="mb-2 flex items-center gap-2">
                                        <Badge variant="outline" class="font-sans text-xs">
                                            {{ project.category || 'Branding' }}
                                        </Badge>
                                        <Badge :class="getStatusConfig(project.status).color" variant="outline" class="font-sans text-xs">
                                            {{ getStatusConfig(project.status).label }}
                                        </Badge>
                                        <span v-if="project.is_featured" class="inline-flex items-center gap-1 rounded-full bg-amber-500 dark:bg-amber-600 px-2 py-0.5 text-xs font-semibold text-white">
                                            <Sparkles class="size-3" />
                                            Featured
                                        </span>
                                    </div>

                                    <h3 class="font-display text-xl font-bold text-foreground group-hover:text-primary">
                                        {{ project.name }}
                                    </h3>

                                    <p class="mt-1 font-sans text-sm text-muted-foreground line-clamp-2">
                                        {{ project.short_description }}
                                    </p>
                                </div>

                                <div class="mt-3 flex items-center justify-between">
                                    <div class="flex items-center gap-4 font-sans text-xs text-muted-foreground">
                                        <span class="flex items-center gap-1">
                                            <Building2 class="size-3" />
                                            {{ getProjectYear(project) }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="gap-1 text-xs text-muted-foreground hover:text-foreground"
                                        >
                                            View Details
                                            <ArrowUpRight class="size-3" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- No Results -->
                    <div v-if="filteredProjects.length === 0" class="py-24 text-center">
                        <div class="mx-auto mb-6 flex size-20 items-center justify-center rounded-full bg-muted dark:bg-secondary/30">
                            <Search class="size-10 text-muted-foreground" />
                        </div>
                        <h3 class="font-display text-2xl font-bold text-foreground">
                            No projects found
                        </h3>
                        <p class="mt-3 font-sans text-muted-foreground">
                            Try adjusting your search criteria or clearing the filters.
                        </p>
                        <Button variant="outline" @click="clearFilters" class="mt-6">
                            Clear Filters
                        </Button>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-background py-16 lg:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <AnimatedCTAWithAvailability
                    :messages="[
                        'Have a Project?',
                        'Let\'s Chat!',
                        'Schedule a Call',
                        'Start Something Amazing',
                    ]"
                    availability-date="Immediately"
                    cta-text="Get in Touch"
                    cta-href="/contact"
                    :switch-interval="3500"
                />
            </div>
        </section>
    </div>
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

.bg-grid-black\/\[0\.02\] {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='rgb(0 0 0 / 0.02)'%3e%3cpath d='m0 .5h32m-32 32v-32'/%3e%3c/svg%3e");
}

.dark .bg-grid-white\/\[0\.02\] {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='rgb(255 255 255 / 0.02)'%3e%3cpath d='m0 .5h32m-32 32v-32'/%3e%3c/svg%3e");
}

/* Custom Animations */
@keyframes float {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(10px, -20px) rotate(5deg);
    }
    50% {
        transform: translate(-5px, 10px) rotate(-5deg);
    }
    75% {
        transform: translate(15px, 5px) rotate(3deg);
    }
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

@keyframes gradient-shift {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}
</style>
