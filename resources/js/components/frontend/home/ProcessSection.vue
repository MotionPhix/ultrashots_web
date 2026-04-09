<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { index as quoteIndex } from '@/routes/quote';
import type { ProcessStep } from '@/types/home';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Clock } from 'lucide-vue-next';

defineProps<{
    steps: ProcessStep[];
}>();
</script>

<template>
    <section id="process" class="relative bg-secondary/30 py-20 lg:py-28">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section header -->
            <div class="mb-16 max-w-3xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-border bg-background px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground font-display">
                    My Process
                </span>
                <h2 class="mt-6 font-display text-4xl font-bold text-foreground sm:text-5xl lg:text-6xl">
                    From brief to brand in a structured sprint.
                </h2>
                <p class="mt-4 text-base leading-relaxed text-muted-foreground font-sans sm:text-lg">
                    No back-and-forth guessing games. A clear four-step process keeps every project sharp, fast, and on-brand from day one.
                </p>
            </div>

            <!-- Process steps grid -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <article
                    v-for="(step, index) in steps"
                    :key="step.title"
                    class="group relative flex flex-col rounded-2xl border border-border bg-background p-8 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
                >
                    <!-- Step number badge -->
                    <div class="mb-6 flex size-14 items-center justify-center rounded-full bg-primary/10 font-display text-2xl font-bold text-primary transition-colors duration-300 group-hover:bg-primary group-hover:text-primary-foreground">
                        {{ String(index + 1).padStart(2, '0') }}
                    </div>

                    <!-- Content -->
                    <div class="flex-1 space-y-4">
                        <h3 class="font-heading text-xl font-semibold text-foreground">
                            {{ step.title }}
                        </h3>

                        <p class="text-sm leading-relaxed text-muted-foreground font-sans">
                            {{ step.summary }}
                        </p>
                    </div>

                    <!-- Duration at bottom -->
                    <div class="mt-6 pt-6 border-t border-border">
                        <div class="flex items-center gap-2 text-xs font-medium text-muted-foreground font-sans">
                            <Clock class="size-3.5 text-primary" />
                            <span>{{ step.duration }}</span>
                        </div>
                    </div>
                </article>
            </div>

            <!-- CTA -->
            <div class="mt-12 flex justify-center">
                <Button
                    :as="Link"
                    :href="quoteIndex().url"
                    class="rounded-full !h-12 text-sm font-semibold text-primary-foreground hover:bg-primary/90 transition-all duration-200 shadow-lg hover:shadow-xl"
                >
                    Start your project
                    <ArrowRight class="size-4" />
                </Button>
            </div>
        </div>
    </section>
</template>
