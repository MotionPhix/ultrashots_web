<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Project } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { index as projectsIndex, show as projectsShow } from '@/routes/projects';
import { computed, onMounted, ref, nextTick, onUnmounted } from 'vue';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AnimatedCTAWithAvailability from '@/components/frontend/ui/AnimatedCTAWithAvailability.vue';

import {
    ArrowLeft,
    ArrowRight,
    ArrowUpRight,
    Github,
    Figma,
    ExternalLink,
    Sparkles,
    Target,
    Lightbulb,
    Layers,
    Zap,
     TrendingUp,
    Heart,
    Maximize2,
    ChevronLeft,
    ChevronRight,
    X,
    CheckCircle2,
    Monitor,
    Palette,
    PenTool,
    Users,
    Eye,
    Star,
    Quote,
    CircleDot,
    BarChart3,
    Award,
} from 'lucide-vue-next';

gsap.registerPlugin(ScrollTrigger);

const props = defineProps<{
    project: {
        data: Project;
        meta?: any;
    };
    relatedProjects?: {
        data: Project[];
        meta?: any;
    };
}>();

defineOptions({ layout: AppLayout });

// Refs
const heroRef = ref<HTMLElement>();
const heroContentRef = ref<HTMLElement>();
const overviewRef = ref<HTMLElement>();
const briefRef = ref<HTMLElement>();
const resultsRef = ref<HTMLElement>();
const processRef = ref<HTMLElement>();
const solutionRef = ref<HTMLElement>();
const galleryRef = ref<HTMLElement>();
const testimonialRef = ref<HTMLElement>();
const ctaRef = ref<HTMLElement>();
const nextProjectRef = ref<HTMLElement>();

const animations = ref<gsap.core.Tween[]>([]);

// Computed
const projectData = computed(() => props.project.data);
const relatedProjectsData = computed(() => props.relatedProjects?.data || []);

const heroImage = computed(() => {
    if (projectData.value.poster_url) return projectData.value.poster_url;
    if (projectData.value.gallery_images?.length > 0) {
        return projectData.value.gallery_images[0].large_url || projectData.value.gallery_images[0].url;
    }
    return '/assets/placeholder-project.jpg';
});

const galleryImages = computed(() => projectData.value.gallery_images || []);

const customerName = computed(() => {
    const customer = projectData.value.customer;
    if (customer?.company_name) return customer.company_name;
    if (customer?.display_name) return customer.display_name;
    return `${customer?.first_name || ''} ${customer?.last_name || ''}`.trim() || 'Client';
});

const projectYear = computed(() => {
    if (projectData.value.end_date) return new Date(projectData.value.end_date).getFullYear();
    if (projectData.value.created_at) return new Date(projectData.value.created_at).getFullYear();
    return new Date().getFullYear();
});

const projectMetrics = computed(() => {
    const results = projectData.value.results;
    if (!results) return [];
    if (Array.isArray(results)) {
        return results.map((item: any) => ({
            label: item.metric || item.label || 'Improvement',
            value: item.value || '+30%',
        }));
    }
    if (typeof results === 'string') {
        try {
            const parsed = JSON.parse(results);
            if (Array.isArray(parsed)) {
                return parsed.map((item: any) => ({
                    label: item.metric || item.label || 'Improvement',
                    value: item.value || '+30%',
                }));
            }
        } catch {
            return [];
        }
    }
    return [];
});

const externalLinks = computed(() => {
    const links = [];
    const project = projectData.value;
    if (project.live_url) links.push({ name: 'Live Site', url: project.live_url, icon: ArrowUpRight });
    if (project.github_url) links.push({ name: 'GitHub', url: project.github_url, icon: Github });
    if (project.figma_url) links.push({ name: 'Figma', url: project.figma_url, icon: Figma });
    if (project.behance_url) links.push({ name: 'Behance', url: project.behance_url, icon: ExternalLink });
    if (project.dribbble_url) links.push({ name: 'Dribbble', url: project.dribbble_url, icon: ExternalLink });
    return links;
});

// Image Modal
const selectedImageIndex = ref(0);
const isImageDialogOpen = ref(false);

const openImageModal = (index: number) => {
    selectedImageIndex.value = index;
    isImageDialogOpen.value = true;
};

const nextImage = () => {
    selectedImageIndex.value = (selectedImageIndex.value + 1) % galleryImages.value.length;
};

const prevImage = () => {
    selectedImageIndex.value = selectedImageIndex.value === 0 ? galleryImages.value.length - 1 : selectedImageIndex.value - 1;
};

// Helpers
const isValidElement = (element: any): element is HTMLElement => {
    return element && typeof element.getBoundingClientRect === 'function' && element.nodeType === Node.ELEMENT_NODE;
};

// Animations
onMounted(async () => {
    await nextTick();

    setTimeout(() => {
        try {
            // Hero parallax
            if (heroRef.value && isValidElement(heroRef.value)) {
                const heroImg = heroRef.value.querySelector('img');
                if (heroImg) {
                    const anim = gsap.fromTo(heroImg,
                        { scale: 1.15, opacity: 0 },
                        { scale: 1, opacity: 1, duration: 1.5, ease: 'power3.out' }
                    );
                    animations.value.push(anim);
                }

                // Hero content reveal
                if (heroContentRef.value) {
                    const anim = gsap.fromTo(heroContentRef.value.children,
                        { y: 60, opacity: 0 },
                        { y: 0, opacity: 1, duration: 0.8, stagger: 0.15, ease: 'power3.out', delay: 0.3 }
                    );
                    animations.value.push(anim);
                }
            }

            // Overview
            if (overviewRef.value && isValidElement(overviewRef.value)) {
                const anim = gsap.fromTo(overviewRef.value.children,
                    { y: 40, opacity: 0 },
                    {
                        y: 0, opacity: 1, duration: 0.7, stagger: 0.1, ease: 'power3.out',
                        scrollTrigger: { trigger: overviewRef.value, start: 'top 85%', toggleActions: 'play none none reverse' }
                    }
                );
                animations.value.push(anim);
            }

            // Results counter
            if (resultsRef.value && isValidElement(resultsRef.value)) {
                const anim = gsap.fromTo(resultsRef.value.children,
                    { scale: 0.9, opacity: 0, y: 40 },
                    {
                        scale: 1, opacity: 1, y: 0, duration: 0.7, stagger: 0.1, ease: 'back.out(1.4)',
                        scrollTrigger: { trigger: resultsRef.value, start: 'top 80%', toggleActions: 'play none none reverse' }
                    }
                );
                animations.value.push(anim);
            }

            // Process steps
            if (processRef.value && isValidElement(processRef.value)) {
                const steps = processRef.value.querySelectorAll('.process-step');
                if (steps.length > 0) {
                    const anim = gsap.fromTo(steps,
                        { x: -60, opacity: 0 },
                        {
                            x: 0, opacity: 1, duration: 0.7, stagger: 0.15, ease: 'power3.out',
                            scrollTrigger: { trigger: processRef.value, start: 'top 80%', toggleActions: 'play none none reverse' }
                        }
                    );
                    animations.value.push(anim);
                }
            }

            // Solution
            if (solutionRef.value && isValidElement(solutionRef.value)) {
                const anim = gsap.fromTo(solutionRef.value.children,
                    { y: 60, opacity: 0 },
                    {
                        y: 0, opacity: 1, duration: 0.8, stagger: 0.15, ease: 'power3.out',
                        scrollTrigger: { trigger: solutionRef.value, start: 'top 80%', toggleActions: 'play none none reverse' }
                    }
                );
                animations.value.push(anim);
            }

            // Gallery
            if (galleryRef.value && isValidElement(galleryRef.value)) {
                const items = galleryRef.value.querySelectorAll('.gallery-item');
                if (items.length > 0) {
                    const anim = gsap.fromTo(items,
                        { y: 80, opacity: 0 },
                        {
                            y: 0, opacity: 1, duration: 0.7, stagger: 0.1, ease: 'power3.out',
                            scrollTrigger: { trigger: galleryRef.value, start: 'top 80%', toggleActions: 'play none none reverse' }
                        }
                    );
                    animations.value.push(anim);
                }
            }

            // Testimonial
            if (testimonialRef.value && isValidElement(testimonialRef.value)) {
                const anim = gsap.fromTo(testimonialRef.value,
                    { y: 60, opacity: 0 },
                    {
                        y: 0, opacity: 1, duration: 0.8, ease: 'power3.out',
                        scrollTrigger: { trigger: testimonialRef.value, start: 'top 85%', toggleActions: 'play none none reverse' }
                    }
                );
                animations.value.push(anim);
            }

            // CTA
            if (ctaRef.value && isValidElement(ctaRef.value)) {
                const anim = gsap.fromTo(ctaRef.value,
                    { y: 60, opacity: 0 },
                    {
                        y: 0, opacity: 1, duration: 0.8, ease: 'power3.out',
                        scrollTrigger: { trigger: ctaRef.value, start: 'top 85%', toggleActions: 'play none none reverse' }
                    }
                );
                animations.value.push(anim);
            }

            // Next project
            if (nextProjectRef.value && isValidElement(nextProjectRef.value)) {
                const anim = gsap.fromTo(nextProjectRef.value,
                    { y: 60, opacity: 0 },
                    {
                        y: 0, opacity: 1, duration: 0.7, ease: 'power3.out',
                        scrollTrigger: { trigger: nextProjectRef.value, start: 'top 85%', toggleActions: 'play none none reverse' }
                    }
                );
                animations.value.push(anim);
            }

        } catch (error) {
            console.warn('Animation setup failed:', error);
        }
    }, 50);
});

onUnmounted(() => {
    animations.value.forEach(a => a.kill?.());
    ScrollTrigger.getAll().forEach(t => t.kill());
});
</script>

<template>
    <Head :title="`${projectData.name} | UltraShots Portfolio`">
        <meta name="description" :content="projectData.short_description || projectData.description?.substring(0, 160)" />
        <meta property="og:title" :content="projectData.name" />
        <meta property="og:description" :content="projectData.short_description || projectData.description?.substring(0, 160)" />
        <meta property="og:image" :content="heroImage" />
    </Head>

    <div class="min-h-screen bg-background">
        <!-- Fixed Navigation -->
        <div class="fixed top-12 left-16 right-0 z-20 flex items-center justify-between p-4 lg:p-6 mix-blend-difference text-white">
            <Link :href="projectsIndex().url" class="group flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition-all hover:bg-white/10">
                <ArrowLeft class="size-4 transition-transform group-hover:-translate-x-1" />
                <span>Back</span>
            </Link>
            <div v-if="externalLinks.length > 0" class="hidden items-center gap-2 sm:flex">
                <Link v-for="link in externalLinks" :key="link.name" :href="link.url" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition-all hover:bg-white/10">
                    <component :is="link.icon" class="size-4" />
                    <span>{{ link.name }}</span>
                </Link>
            </div>
        </div>

        <!-- ===================== 1. HERO - FULL SCREEN IMMERSIVE ===================== -->
        <section ref="heroRef" class="relative h-[95vh] min-h-[600px] overflow-hidden">
            <div class="absolute inset-0">
                <img :src="heroImage" :alt="projectData.name" class="h-full w-full object-cover" />
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/30 to-transparent" />
            </div>

            <div ref="heroContentRef" class="absolute bottom-0 left-0 right-0 p-6 sm:p-10 lg:p-16">
                <div class="mx-auto max-w-7xl">
                    <div class="mb-6 flex flex-wrap items-center gap-3">
                        <Badge variant="outline" class="border-white/30 bg-white/10 text-white backdrop-blur-sm font-sans text-xs uppercase tracking-wider">
                            {{ projectData.category || 'Branding' }}
                        </Badge>
                        <Badge variant="outline" class="border-white/30 bg-white/10 text-white backdrop-blur-sm font-sans text-xs">
                            {{ projectYear }}
                        </Badge>
                        <Badge v-if="projectData.is_featured" variant="outline" class="border-amber-500/30 bg-amber-500/10 text-amber-400 backdrop-blur-sm font-sans text-xs">
                            <Sparkles class="mr-1 size-3" />
                            Featured
                        </Badge>
                    </div>
                    <h1 class="font-display text-5xl font-bold leading-[0.9] tracking-tight text-white sm:text-6xl md:text-7xl lg:text-8xl xl:text-9xl">
                        {{ projectData.name }}
                    </h1>
                    <p v-if="projectData.short_description" class="mt-6 max-w-2xl font-sans text-lg leading-relaxed text-white/70 sm:text-xl lg:text-2xl">
                        {{ projectData.short_description }}
                    </p>
                    <div v-if="externalLinks.length > 0" class="mt-8 flex flex-wrap gap-3 sm:hidden">
                        <Link v-for="link in externalLinks" :key="link.name" :href="link.url" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm">
                            <component :is="link.icon" class="size-4" />
                            <span>{{ link.name }}</span>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===================== 2. CLIENT VISION & CONTEXT ===================== -->
        <section ref="overviewRef" class="bg-background">
            <div class="mx-auto max-w-4xl px-6 py-20 lg:py-28">
                <!-- Quick Facts Grid -->
                <div class="mb-16 grid grid-cols-2 gap-8 lg:grid-cols-4">
                    <div>
                        <p class="font-heading text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">Client</p>
                        <p class="mt-2 font-display text-lg font-semibold text-foreground lg:text-xl">{{ customerName }}</p>
                    </div>
                    <div>
                        <p class="font-heading text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">Category</p>
                        <p class="mt-2 font-display text-lg font-semibold text-foreground lg:text-xl">{{ projectData.category || 'Branding' }}</p>
                    </div>
                    <div>
                        <p class="font-heading text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">Year</p>
                        <p class="mt-2 font-display text-lg font-semibold text-foreground lg:text-xl">{{ projectYear }}</p>
                    </div>
                    <div>
                        <p class="font-heading text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">Status</p>
                        <p class="mt-2 font-display text-lg font-semibold text-emerald-600 dark:text-emerald-400 lg:text-xl">Completed</p>
                    </div>
                </div>

                <!-- Project Title & Description -->
                <div v-if="projectData.description" class="mb-16">
                    <h2 class="mb-8 font-display text-4xl font-bold leading-tight tracking-tight text-foreground sm:text-5xl lg:text-6xl">
                        {{ projectData.name }}
                    </h2>
                    <div class="prose prose-lg max-w-none dark:prose-invert" v-html="projectData.description"></div>
                </div>

                <!-- Technologies Used -->
                <div v-if="projectData.technologies?.length" class="flex flex-wrap items-center gap-3">
                    <span class="font-heading text-xs font-semibold uppercase tracking-[0.2em] text-muted-foreground">Tools:</span>
                    <Badge v-for="tech in projectData.technologies" :key="tech" variant="secondary" class="bg-secondary/50 px-4 py-2.5 font-sans text-sm">
                        {{ tech }}
                    </Badge>
                </div>
            </div>
        </section>

        <!-- ===================== 3. THE CHALLENGE ===================== -->
        <section v-if="projectData.challenges" ref="briefRef" class="bg-secondary/30">
            <div class="mx-auto max-w-4xl px-6 py-20 lg:py-28">
                <div class="mb-12">
                    <div class="inline-flex items-center gap-2 rounded-full border border-red-500/20 bg-red-500/5 px-4 py-2">
                        <Target class="size-4 text-red-600 dark:text-red-400" />
                        <span class="font-heading text-xs font-semibold uppercase tracking-[0.2em] text-red-600 dark:text-red-400">The Challenge</span>
                    </div>
                </div>
                <h2 class="mb-8 font-display text-3xl font-bold leading-tight tracking-tight text-foreground sm:text-4xl lg:text-5xl">
                    What Problem Were We Solving?
                </h2>
                <p class="font-sans text-lg leading-relaxed text-muted-foreground lg:text-xl">
                    {{ projectData.challenges }}
                </p>
            </div>
        </section>

        <!-- ===================== 6. RESULTS & IMPACT ===================== -->
        <section v-if="projectMetrics.length > 0" ref="resultsRef" class="bg-primary text-primary-foreground">
            <div class="mx-auto max-w-4xl px-6 py-20 lg:py-28">
                <div class="mb-12">
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2">
                        <TrendingUp class="size-4" />
                        <span class="font-heading text-xs font-semibold uppercase tracking-[0.2em]">Results & Impact</span>
                    </div>
                </div>
                <h2 class="mb-12 font-display text-3xl font-bold leading-tight tracking-tight sm:text-4xl lg:text-5xl">
                    The Numbers Speak
                </h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="(metric, index) in projectMetrics" :key="index" class="group rounded-3xl border border-white/20 bg-white/10 p-8 backdrop-blur-sm transition-all hover:bg-white/15">
                        <p class="font-display text-5xl font-bold tracking-tight lg:text-6xl">{{ metric.value }}</p>
                        <p class="mt-3 font-sans text-sm font-medium uppercase tracking-wider text-white/70">{{ metric.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===================== 4. STRATEGY & APPROACH ===================== -->
        <section v-if="projectData.process_steps?.length" ref="processRef" class="bg-background">
            <div class="mx-auto max-w-4xl px-6 py-20 lg:py-28">
                <div class="mb-12">
                    <div class="inline-flex items-center gap-2 rounded-full border border-blue-500/20 bg-blue-500/5 px-4 py-2">
                        <Layers class="size-4 text-blue-600 dark:text-blue-400" />
                        <span class="font-heading text-xs font-semibold uppercase tracking-[0.2em] text-blue-600 dark:text-blue-400">Strategy & Approach</span>
                    </div>
                </div>
                <h2 class="mb-8 font-display text-3xl font-bold leading-tight tracking-tight text-foreground sm:text-4xl lg:text-5xl">
                    How we tackled it
                </h2>
                <p class="mb-12 font-sans text-lg leading-relaxed text-muted-foreground lg:text-xl">
                    A strategic, phased approach to ensure every decision was intentional and aligned with business objectives.
                </p>

                <div class="space-y-8">
                    <div v-for="(step, index) in projectData.process_steps" :key="index" class="process-step flex gap-6">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-primary/10 font-display text-lg font-bold text-primary">
                            {{ String(index + 1).padStart(2, '0') }}
                        </div>
                        <div>
                            <h3 class="mb-2 font-display text-xl font-bold text-foreground">{{ step.title }}</h3>
                            <p class="font-sans text-base leading-relaxed text-muted-foreground">
                                {{ step.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===================== 5. THE SOLUTION ===================== -->
        <section v-if="projectData.solutions || projectData.features?.length" ref="solutionRef" class="bg-secondary/30">
            <div class="mx-auto max-w-4xl px-6 py-20 lg:py-28">
                <div class="mb-12">
                    <div class="inline-flex items-center gap-2 rounded-full border border-emerald-500/20 bg-emerald-500/5 px-4 py-2">
                        <Zap class="size-4 text-emerald-600 dark:text-emerald-400" />
                        <span class="font-heading text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-400">The Solution</span>
                    </div>
                </div>
                <h2 class="mb-8 font-display text-3xl font-bold leading-tight tracking-tight text-foreground sm:text-4xl lg:text-5xl">
                    What We Delivered
                </h2>
                <div v-if="projectData.solutions" class="mb-12">
                    <p class="font-sans text-lg leading-relaxed text-muted-foreground lg:text-xl">{{ projectData.solutions }}</p>
                </div>
                <div v-if="projectData.features?.length" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div v-for="(feature, index) in projectData.features" :key="index" class="flex items-start gap-4 rounded-2xl border border-border/50 bg-background p-6 transition-all hover:border-primary/30 hover:shadow-sm">
                        <CheckCircle2 class="mt-0.5 size-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                        <span class="font-sans text-base font-medium text-foreground">{{ feature }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===================== 7. GALLERY - VISUAL SHOWCASE ===================== -->
        <section v-if="galleryImages.length > 0" ref="galleryRef" class="bg-background">
            <div class="mx-auto max-w-4xl px-6 py-20 lg:py-28">
                <div class="mb-12">
                    <div class="inline-flex items-center gap-2 rounded-full border border-primary/20 px-4 py-2">
                        <Monitor class="size-4 text-primary" />
                        <span class="font-heading text-xs font-semibold uppercase tracking-[0.2em] text-primary">Visual Showcase</span>
                    </div>
                </div>
                <h2 class="mb-12 font-display text-3xl font-bold leading-tight tracking-tight text-foreground sm:text-4xl lg:text-5xl">
                    See It in Action
                </h2>

                <!-- Gallery Grid -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div v-for="(image, index) in galleryImages" :key="image.id" class="gallery-item cursor-pointer overflow-hidden rounded-3xl" @click="openImageModal(index)">
                        <div class="relative aspect-[4/3]">
                            <img :src="image.url" :alt="image.name" class="h-full w-full object-cover transition-transform duration-700 hover:scale-[1.02]" loading="lazy" />
                            <div class="absolute inset-0 bg-black/0 transition-colors hover:bg-black/10">
                                <div class="absolute bottom-4 right-4 flex items-center gap-2 rounded-full bg-black/50 px-3 py-1.5 text-xs text-white backdrop-blur-sm opacity-0 transition-opacity hover:bg-black/60">
                                    <Maximize2 class="size-3.5" />
                                    <span>View</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===================== 8. CLIENT TESTIMONIAL ===================== -->
        <section v-if="projectData.client_feedback" ref="testimonialRef" class="bg-secondary/30">
            <div class="mx-auto max-w-4xl px-6 py-20 lg:py-28">
                <div class="mb-12">
                    <div class="inline-flex items-center gap-2 rounded-full border border-red-500/20 bg-red-500/5 px-4 py-2">
                        <Heart class="size-4 text-red-500" />
                        <span class="font-heading text-xs font-semibold uppercase tracking-[0.2em] text-red-600 dark:text-red-400">Client Feedback</span>
                    </div>
                </div>
                <blockquote class="mb-10 font-heading text-3xl leading-tight text-foreground sm:text-4xl lg:text-5xl">
                    "{{ projectData.client_feedback }}"
                </blockquote>
                <div class="flex items-center gap-4">
                    <div class="size-14 rounded-full bg-primary/10" />
                    <div>
                        <p class="font-sans text-base font-semibold text-foreground">{{ customerName }}</p>
                        <p class="font-sans text-sm text-muted-foreground">Client</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===================== 9. CTA SECTION ===================== -->
        <section ref="ctaRef" class="bg-background">
            <div class="mx-auto max-w-4xl px-6 py-20 lg:py-28">
                <AnimatedCTAWithAvailability
                    :messages="[
                        'Ready for your transformation?',
                        'Let\'s build your brand',
                        'Start your project today',
                        'Elevate your business',
                    ]"
                    cta-text="Get in Touch"
                    cta-href="/contact"
                    :switch-interval="3500"
                />
            </div>
        </section>

        <!-- ===================== 10. NEXT PROJECT ===================== -->
        <section v-if="relatedProjectsData.length > 0" ref="nextProjectRef" class="bg-secondary/30">
            <div class="mx-auto max-w-4xl px-6 py-20 lg:py-28">
                <div class="mb-12">
                    <div class="inline-flex items-center gap-2 rounded-full border border-primary/20 px-4 py-2">
                        <ArrowRight class="size-4 text-primary" />
                        <span class="font-heading text-xs font-semibold uppercase tracking-[0.2em] text-primary">Continue Exploring</span>
                    </div>
                </div>
                <h2 class="mb-12 font-display text-3xl font-bold leading-tight tracking-tight text-foreground sm:text-4xl lg:text-5xl">
                    Next Project
                </h2>
                <Link :href="projectsShow(relatedProjectsData[0].uuid).url" class="group block">
                    <div class="relative aspect-[16/9] overflow-hidden rounded-3xl">
                        <img :src="relatedProjectsData[0].poster_url || '/assets/placeholder-project.jpg'" :alt="relatedProjectsData[0].name" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent" />
                        <div class="absolute bottom-0 left-0 right-0 p-8 lg:p-12">
                            <Badge variant="outline" class="mb-3 border-white/30 bg-white/10 text-white backdrop-blur-sm font-sans text-xs">
                                {{ relatedProjectsData[0].category || 'Branding' }}
                            </Badge>
                            <h3 class="font-display text-3xl font-bold leading-tight text-white lg:text-4xl">
                                {{ relatedProjectsData[0].name }}
                            </h3>
                            <p v-if="relatedProjectsData[0].short_description" class="mt-3 max-w-2xl font-sans text-white/70 lg:text-lg">
                                {{ relatedProjectsData[0].short_description }}
                            </p>
                            <div class="mt-6 flex items-center gap-2 text-white">
                                <span class="font-sans text-sm font-medium">View Case Study</span>
                                <ArrowRight class="size-4 transition-transform group-hover:translate-x-1" />
                            </div>
                        </div>
                    </div>
                </Link>
            </div>
        </section>

        <!-- ===================== IMAGE MODAL ===================== -->
        <div v-if="isImageDialogOpen" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95" @click="isImageDialogOpen = false">
            <div class="relative h-full w-full" @click.stop>
                <img v-if="galleryImages[selectedImageIndex]" :src="galleryImages[selectedImageIndex].url" :alt="galleryImages[selectedImageIndex].name" class="mx-auto h-full w-auto max-h-[90vh] object-contain" />
                <div v-if="galleryImages.length > 1" class="absolute inset-y-0 left-6 flex items-center">
                    <button class="flex size-12 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur-sm transition-colors hover:bg-white/20" @click.stop="prevImage">
                        <ChevronLeft class="size-6" />
                    </button>
                </div>
                <div v-if="galleryImages.length > 1" class="absolute inset-y-0 right-6 flex items-center">
                    <button class="flex size-12 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur-sm transition-colors hover:bg-white/20" @click.stop="nextImage">
                        <ChevronRight class="size-6" />
                    </button>
                </div>
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-6">
                    <div class="flex items-center justify-between text-white">
                        <h3 class="font-medium">{{ galleryImages[selectedImageIndex]?.name || `Image ${selectedImageIndex + 1}` }}</h3>
                        <p class="text-sm text-white/80">{{ selectedImageIndex + 1 }} of {{ galleryImages.length }}</p>
                    </div>
                </div>
                <button class="absolute right-6 top-6 flex size-10 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur-sm transition-colors hover:bg-white/20" @click="isImageDialogOpen = false">
                    <X class="size-5" />
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 { color: inherit; }
.prose p { margin-bottom: 1.25rem; }
.prose ul, .prose ol { margin-bottom: 1.25rem; }
.prose li { margin-bottom: 0.5rem; }
</style>
