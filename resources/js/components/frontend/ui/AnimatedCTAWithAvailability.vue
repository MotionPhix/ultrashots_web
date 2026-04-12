<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

interface Props {
    messages?: string[];
    availabilityLabel?: string;
    availabilityDate?: string;
    ctaText?: string;
    ctaHref?: string;
    switchInterval?: number;
}

const props = withDefaults(defineProps<Props>(), {
    messages: () => [
        'Have a Project?',
        "Let's Chat!",
        'Schedule a Call',
        'Start Something Amazing',
    ],
    availabilityLabel: "I'm available for new projects from",
    availabilityDate: undefined,
    ctaText: 'Book a Free Call',
    ctaHref: '/get-in-touch',
    switchInterval: 3500,
});

// Generate a random date between 1-3 days from today
const computedAvailabilityDate = computed(() => {
    // If custom date is provided, use it
    if (props.availabilityDate) {
        return props.availabilityDate;
    }

    // Generate random date between 1-3 days from now
    const randomDays = Math.floor(Math.random() * 3) + 1; // 1, 2, or 3 days
    const targetDate = new Date();
    targetDate.setDate(targetDate.getDate() + randomDays);

    // Format date nicely (e.g., "12th April 2026")
    const day = targetDate.getDate();
    const month = targetDate.toLocaleString('en-US', { month: 'long' });
    const year = targetDate.getFullYear();

    // Add ordinal suffix (st, nd, rd, th)
    const ordinalSuffix = (n: number) => {
        const s = ['th', 'st', 'nd', 'rd'];
        const v = n % 100;
        return s[(v - 20) % 10] || s[v] || s[0];
    };

    return `${day}${ordinalSuffix(day)} ${month} ${year}`;
});

// State
const currentMessageIndex = ref(0);
let intervalId: ReturnType<typeof setInterval> | null = null;

// Lifecycle
onMounted(() => {
    // Auto-switch messages
    intervalId = setInterval(() => {
        currentMessageIndex.value = (currentMessageIndex.value + 1) % props.messages.length;
    }, props.switchInterval);
});

onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId);
    }
});
</script>

<template>
    <div class="animated-cta-container relative w-full overflow-hidden rounded-3xl bg-gradient-to-br from-[#0a0a0a] via-[#0f0f0f] to-[#050505] px-6 py-16 sm:px-12">
        <!-- Animated Wave Background Pattern -->
        <div class="absolute inset-0 opacity-30">
            <svg class="absolute inset-0 h-full w-full" viewBox="0 0 1440 320" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="waveGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#1a1a1a" stop-opacity="0" />
                        <stop offset="50%" stop-color="#2a2a2a" stop-opacity="0.5" />
                        <stop offset="100%" stop-color="#1a1a1a" stop-opacity="0" />
                    </linearGradient>
                </defs>
                <path fill="url(#waveGradient)" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,208C1248,224,1344,192,1392,176L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" />
                <animate attributeName="d" dur="20s" repeatCount="indefinite" values="
                    M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,208C1248,224,1344,192,1392,176L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z;
                    M0,160L48,176C96,192,192,224,288,229.3C384,235,480,213,576,197.3C672,181,768,171,864,181.3C960,192,1056,224,1152,218.7C1248,213,1344,171,1392,149.3L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z;
                    M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,208C1248,224,1344,192,1392,176L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z
                " />
            </svg>
        </div>

        <!-- Grid Lines Overlay -->
        <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(to right, rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;" />

        <!-- Content -->
        <div class="relative flex flex-col items-center gap-12 lg:flex-row lg:items-end lg:justify-between lg:gap-20">
            <!-- Left: CTA Text and Button -->
            <div class="text-center lg:text-left">
                <!-- Animated switching text -->
                <div class="relative mb-8 h-[64px]">
                    <TransitionGroup name="text-slide" tag="div" class="relative flex h-full w-full max-w-sm items-center">
                        <h2
                            v-for="(message, index) in messages"
                            v-show="currentMessageIndex === index"
                            :key="index"
                            class="font-display text-4xl text-white lg:text-5xl lg:whitespace-nowrap"
                        >
                            {{ message }}
                        </h2>
                    </TransitionGroup>
                </div>

                <!-- CTA Button -->
                <div class="flex justify-center lg:justify-start">
                    <Link
                        :href="ctaHref"
                        class="group inline-flex items-center gap-2 rounded-full border border-white/30 bg-transparent px-6 py-3 text-sm font-medium uppercase tracking-wider text-white transition-all duration-300 hover:border-white hover:bg-white/5"
                    >
                        {{ ctaText }}
                        <svg class="size-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>
            </div>

            <!-- Right: Availability Status -->
            <div class="text-center lg:text-right">
                <p class="mb-2 font-sans text-base text-white/70">
                    {{ availabilityLabel }}
                </p>
                <p class="font-heading font-bold text-white text-3xl sm:text-4xl">
                    {{ computedAvailabilityDate }}
                </p>
            </div>
        </div>

        <!-- Decorative Gradient Orbs -->
        <div class="absolute -top-20 right-1/4 size-64 rounded-full bg-green-500/10 blur-3xl" />
        <div class="absolute -bottom-20 left-1/4 size-64 rounded-full bg-emerald-500/10 blur-3xl" />
    </div>
</template>

<style scoped>
/* Text slide animation */
.text-slide-enter-active {
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    position: absolute;
    left: 0;
    right: 0;
}

.text-slide-leave-active {
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    position: absolute;
    left: 0;
    right: 0;
}

.text-slide-enter-from {
    opacity: 0;
    transform: translateY(40px);
}

.text-slide-leave-to {
    opacity: 0;
    transform: translateY(-40px);
}

.text-slide-enter-to {
    opacity: 1;
    transform: translateY(0);
}

.text-slide-leave-from {
    opacity: 1;
    transform: translateY(0);
}
</style>
