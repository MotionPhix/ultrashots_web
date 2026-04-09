<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { index as projectsIndex } from '@/routes/projects';
import { index as quoteIndex } from '@/routes/quote';
import type { CaseStudy, LandingHero, LandingOwner } from '@/types/home';
import { Link } from '@inertiajs/vue3';
import { gsap } from 'gsap';
import { ArrowRight, CalendarDays, MapPin } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

defineProps<{
    hero: LandingHero;
    owner: LandingOwner;
    caseStudies: CaseStudy[];
}>();

const heroRef = ref<HTMLElement | null>(null);

onMounted(() => {
    if (!heroRef.value) {
        return;
    }

    const targets = heroRef.value.querySelectorAll('[data-animate]');

    gsap.fromTo(
        targets,
        { opacity: 0, y: 28 },
        { opacity: 1, y: 0, duration: 0.85, stagger: 0.11, ease: 'power3.out', delay: 0.08 },
    );
});
</script>

<template>
    <section
        ref="heroRef"
        class="relative overflow-hidden bg-background pb-20 pt-16 lg:pb-32 lg:pt-24"
    >
        <!-- Subtle grid background -->
        <div class="absolute inset-0 hero-grid opacity-[0.03] dark:opacity-[0.05]" />

        <div class="section-frame">
            <div class="grid items-center gap-12 lg:grid-cols-[1.2fr_0.8fr] lg:gap-16">

                <!-- Left: Strategic messaging -->
                <div class="flex flex-col gap-8">
                    <!-- Eyebrow text -->
                    <div data-animate class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center gap-2 rounded-full border border-border bg-secondary/50 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground font-display">
                            <span class="size-2 animate-pulse rounded-full bg-primary" />
                            {{ hero.eyebrow }}
                        </span>
                    </div>

                    <!-- Main headline - Alfa Slab One -->
                    <div data-animate class="space-y-4">
                        <h1 class="font-display text-5xl font-bold leading-[1.05] tracking-tight text-foreground sm:text-6xl lg:text-7xl xl:text-8xl">
                            {{ hero.headline }}
                        </h1>
                        <p class="max-w-2xl text-lg leading-relaxed text-muted-foreground font-sans sm:text-xl">
                            {{ hero.description }}
                        </p>
                    </div>

                    <!-- Proof points -->
                    <div data-animate class="grid grid-cols-3 gap-6 divide-x divide-border">
                        <div
                            v-for="point in (hero.proof_points ?? []).slice(0, 3)"
                            :key="point.label"
                            class="flex flex-col gap-1 px-4 first:pl-0"
                        >
                            <span class="font-display text-3xl font-bold text-foreground">{{ point.value }}</span>
                            <span class="text-xs leading-snug text-muted-foreground font-sans">{{ point.label }}</span>
                        </div>
                    </div>

                    <!-- CTAs -->
                    <div data-animate class="flex flex-wrap items-center gap-3 pt-2">
                        <Button
                          :as="Link" :href="quoteIndex().url"
                          class="gap-2 bg-primary !h-12 rounded-full">
                            <CalendarDays class="size-4" />
                            {{ hero.primary_cta_label }}
                        </Button>

                        <Button :as="Link" :href="projectsIndex().url" variant="outline" class="gap-2 border-border px-8 py-3.5 text-sm font-semibold hover:bg-secondary transition-all duration-200">
                            {{ hero.secondary_cta_label }}
                            <ArrowRight class="size-4" />
                        </Button>
                    </div>

                    <!-- Trust badges -->
                    <div data-animate class="flex flex-wrap gap-2 pt-2">
                        <span
                            v-for="badge in hero.badges"
                            :key="badge"
                            class="rounded-full border border-border bg-secondary/30 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.15em] text-muted-foreground font-display"
                        >
                            {{ badge }}
                        </span>
                    </div>
                </div>

                <!-- Right: Professional portrait (smaller, integrated) -->
                <div data-animate class="relative hidden lg:block">
                    <div class="relative aspect-[4/5] w-full max-w-md mx-auto">
                        <!-- Background accent -->
                        <div class="absolute -inset-4 rounded-3xl bg-gradient-to-br from-primary/10 to-transparent blur-2xl" />

                        <!-- Image container -->
                        <div class="relative h-full overflow-hidden rounded-2xl border border-border bg-secondary/30">
                            <img
                                :src="'/assets/profile_400x400.jpg'"
                                :alt="owner.name"
                                class="h-full w-full object-cover"
                            />

                            <!-- Overlay gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-background/60 via-transparent to-transparent" />

                            <!-- Name tag -->
                            <div class="absolute bottom-6 left-6 right-6">
                                <div class="rounded-xl border border-border bg-background/95 backdrop-blur-sm p-4 shadow-lg">
                                    <p class="font-display text-lg font-bold text-foreground">{{ owner.name }}</p>
                                    <p class="text-sm text-muted-foreground font-sans">{{ owner.title }}</p>
                                    <p class="mt-2 flex items-center gap-1.5 text-xs text-muted-foreground font-sans">
                                        <MapPin class="size-3" />
                                        {{ owner.location }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
