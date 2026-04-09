<script setup lang="ts">
import type { Testimonial } from '@/types/home';
import { Quote, Star } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps<{ testimonials: Testimonial[] }>();

const activeIndex = ref(0);
let rotationTimer: number | undefined;

const activeTestimonial = computed(() => props.testimonials[activeIndex.value] ?? null);

onMounted(() => {
    if (props.testimonials.length <= 1) return;

    rotationTimer = globalThis.setInterval(() => {
        activeIndex.value = (activeIndex.value + 1) % props.testimonials.length;
    }, 6000);
});

onUnmounted(() => {
    if (rotationTimer) globalThis.clearInterval(rotationTimer);
});
</script>

<template>
    <section id="testimonials" class="relative bg-secondary/30 py-20 lg:py-28">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section header -->
            <div class="mb-16 text-center max-w-3xl mx-auto">
                <span class="inline-flex items-center gap-2 rounded-full border border-border bg-background px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground font-display">
                    Client Results
                </span>
                <h2 class="mt-6 font-display text-4xl font-bold leading-tight tracking-tight text-foreground sm:text-5xl lg:text-6xl">
                    They hired for the visuals. They stayed for the results.
                </h2>
                <p class="mt-4 text-base leading-relaxed text-muted-foreground font-sans sm:text-lg">
                    These aren't compliments about colors. These are measurable business outcomes: stronger conversions, premium positioning, and brands that command higher prices.
                </p>
            </div>

            <!-- Featured testimonial -->
            <div v-if="activeTestimonial" class="max-w-5xl mx-auto">
                <div class="relative rounded-3xl border border-border bg-background p-8 shadow-xl sm:p-12 lg:p-16">
                    <!-- Large quote icon -->
                    <Quote class="absolute top-8 left-8 size-16 text-primary/10" />
                    
                    <div class="relative space-y-8">
                        <!-- Quote -->
                        <blockquote class="font-heading text-2xl leading-relaxed text-foreground sm:text-3xl lg:text-4xl">
                            "{{ activeTestimonial.quote }}"
                        </blockquote>

                        <!-- Metrics highlight -->
                        <div v-if="activeTestimonial.project_type" class="flex items-center gap-3 pt-4 border-t border-border">
                            <span class="text-xs font-semibold uppercase tracking-[0.15em] text-muted-foreground font-sans">Project Type:</span>
                            <span class="rounded-full border border-border bg-secondary/50 px-3 py-1 text-xs font-medium text-foreground font-sans">
                                {{ activeTestimonial.project_type }}
                            </span>
                        </div>

                        <!-- Author info -->
                        <div class="flex items-center justify-between gap-6 pt-6 border-t border-border">
                            <div class="flex items-center gap-4">
                                <div class="flex size-16 shrink-0 items-center justify-center rounded-full bg-primary/10 font-display text-xl font-bold text-primary">
                                    {{ activeTestimonial.avatar_initials || activeTestimonial.name?.charAt(0) || 'C' }}
                                </div>
                                <div>
                                    <p class="font-display text-xl font-bold text-foreground">
                                        {{ activeTestimonial.name }}
                                    </p>
                                    <p class="text-sm text-muted-foreground font-sans">
                                        {{ activeTestimonial.role }} · {{ activeTestimonial.company }}
                                    </p>
                                </div>
                            </div>

                            <!-- Rating -->
                            <div v-if="activeTestimonial.rating" class="hidden sm:flex items-center gap-1">
                                <Star 
                                    v-for="star in 5" 
                                    :key="star" 
                                    class="size-5 fill-primary text-primary"
                                    :class="{ 'opacity-30': star > (activeTestimonial.rating || 0) }"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation dots -->
                <div v-if="testimonials.length > 1" class="flex justify-center gap-2 mt-8">
                    <button
                        v-for="(testimonial, index) in testimonials"
                        :key="testimonial.id || index"
                        type="button"
                        class="size-3 rounded-full transition-all duration-300"
                        :class="index === activeIndex 
                            ? 'bg-primary w-8' 
                            : 'bg-border hover:bg-primary/50'"
                        @click="activeIndex = index"
                        :aria-label="`View testimonial from ${testimonial.company || testimonial.name}`"
                    />
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="text-center py-16">
                <p class="text-muted-foreground font-sans">Loading client stories...</p>
            </div>

            <!-- Grid of all testimonials (below featured) -->
            <div v-if="testimonials.length > 1" class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="(testimonial, idx) in testimonials.slice(0, 6)"
                    :key="testimonial.id || idx"
                    class="group rounded-2xl border border-border bg-background p-6 shadow-sm transition-all duration-300 hover:shadow-lg hover:-translate-y-1 cursor-pointer"
                    @click="activeIndex = idx"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-primary/10 font-display text-lg font-bold text-primary">
                                {{ testimonial.avatar_initials || testimonial.name?.charAt(0) || 'C' }}
                            </div>
                            <div>
                                <p class="font-heading text-sm font-semibold text-foreground">
                                    {{ testimonial.name }}
                                </p>
                                <p class="text-xs text-muted-foreground font-sans">
                                    {{ testimonial.company }}
                                </p>
                            </div>
                        </div>
                        <Quote class="size-5 text-primary/20" />
                    </div>
                    
                    <p class="text-sm leading-relaxed text-muted-foreground font-sans line-clamp-3">
                        "{{ testimonial.quote }}"
                    </p>

                    <div v-if="testimonial.project_type" class="mt-4 pt-4 border-t border-border">
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-muted-foreground font-sans">
                            {{ testimonial.project_type }}
                        </span>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>
