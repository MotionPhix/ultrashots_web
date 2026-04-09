<script setup lang="ts">
import type { ServiceTier } from '@/types/home';
import { Link } from '@inertiajs/vue3';
import { index as quoteIndex } from '@/routes/quote';
import { load as contactLoad } from '@/routes/contact';
import { Sparkles, Layers3, BadgeCheck, Package2, ArrowRight, CheckCircle2 } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';

defineProps<{ services: ServiceTier[] }>();

const serviceIcons = [Sparkles, Layers3, BadgeCheck, Package2];
</script>

<template>
    <section id="services" class="relative bg-background py-20 lg:py-28">
        <div class="section-frame">
            <!-- Section header -->
            <div class="mb-16 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="max-w-3xl space-y-4">
                    <span class="inline-flex items-center gap-2 rounded-full border border-border bg-secondary/50 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground font-display">
                        Services & Pricing
                    </span>
                    <h2 class="font-display text-4xl font-bold leading-tight tracking-tight text-foreground sm:text-5xl lg:text-6xl">
                        Invest in design that pays for itself.
                    </h2>
                    <p class="text-base leading-relaxed text-muted-foreground font-sans sm:text-lg">
                        Every package is built to deliver real business outcomes: more trust, faster conversions, and a brand that keeps working after launch day.
                    </p>
                </div>
                <Button 
                    :as="Link" 
                    :href="quoteIndex().url" 
                    variant="outline"
                    class="hidden shrink-0 items-center gap-2 sm:inline-flex"
                >
                    Request a quote
                    <ArrowRight class="size-4" />
                </Button>
            </div>

            <!-- Services grid -->
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                <article
                    v-for="(service, index) in services"
                    :key="service.name"
                    class="group relative flex h-full flex-col overflow-hidden rounded-2xl border bg-background p-8 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
                    :class="service.popular ? 'border-primary/30 ring-2 ring-primary/10' : 'border-border'"
                >
                    <!-- Popular badge -->
                    <div
                        v-if="service.popular"
                        class="absolute right-4 top-4 rounded-full bg-primary px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-primary-foreground"
                    >
                        Most Popular
                    </div>

                    <!-- Icon -->
                    <div 
                        class="mb-6 flex size-12 items-center justify-center rounded-xl bg-primary/10 text-primary transition-colors duration-300 group-hover:bg-primary group-hover:text-primary-foreground"
                    >
                        <component :is="serviceIcons[index] ?? Sparkles" class="size-6" />
                    </div>

                    <!-- Title & Price -->
                    <div class="space-y-2">
                        <h3 class="font-heading text-xl font-bold text-foreground">{{ service.name }}</h3>
                        <p class="font-display text-2xl font-bold text-primary">{{ service.price }}</p>
                    </div>

                    <!-- Description -->
                    <p class="mt-4 text-sm leading-relaxed text-muted-foreground font-sans">
                        {{ service.description }}
                    </p>

                    <!-- Deliverables -->
                    <div class="mt-6 flex-1 space-y-3">
                        <div v-for="deliverable in service.deliverables" :key="deliverable" class="flex items-start gap-3">
                            <CheckCircle2 class="mt-0.5 size-4 shrink-0 text-primary" />
                            <p class="text-sm leading-6 text-foreground font-sans">{{ deliverable }}</p>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <Button
                        :as="Link"
                        :href="service.popular ? quoteIndex().url : contactLoad().url"
                        class="mt-6 w-full gap-2"
                        :variant="service.popular ? 'default' : 'outline'"
                    >
                        {{ service.popular ? 'Start with this package' : 'Discuss this package' }}
                    </Button>
                </article>
            </div>

            <!-- Mobile CTA -->
            <div class="mt-10 flex justify-center sm:hidden">
                <Button 
                    :as="Link" 
                    :href="quoteIndex().url" 
                    variant="outline"
                    class="gap-2"
                >
                    Request a quote
                    <ArrowRight class="size-4" />
                </Button>
            </div>
        </div>
    </section>
</template>
