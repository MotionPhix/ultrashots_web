<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { MoveRight } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    href: string;
    label: string;
}

defineProps<Props>();

// Refs
const bgFillRef = ref<HTMLDivElement>();
const textRef = ref<HTMLSpanElement>();

// Glow position state
const glowX = ref(50);
const glowY = ref(50);

const glowStyle = computed(() => ({
    background: `radial-gradient(circle at ${glowX.value}% ${glowY.value}%, rgba(255,255,255,0.15) 0%, transparent 60%)`,
}));

// Magnetic hover effect
const handleMouseMove = (e: MouseEvent) => {
    const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;

    glowX.value = x;
    glowY.value = y;

    // Subtle magnetic pull on the button
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;
    const deltaX = (e.clientX - rect.left - centerX) * 0.1;
    const deltaY = (e.clientY - rect.top - centerY) * 0.1;

    if (bgFillRef.value) {
        bgFillRef.value.style.transform = `translate(${deltaX}px, ${deltaY}px) scaleX(1)`;
    }
};

const handleMouseEnter = () => {
    // Add entrance animation
    if (textRef.value) {
        textRef.value.style.transform = 'translateY(0)';
    }
};

const handleMouseLeave = () => {
    // Reset magnetic effect
    if (bgFillRef.value) {
        bgFillRef.value.style.transform = 'scaleX(0)';
    }
    glowX.value = 50;
    glowY.value = 50;
};
</script>

<template>
    <Link
        :href="href"
        class="animated-cta group relative inline-flex items-center gap-3 overflow-hidden rounded-full bg-foreground text-background px-8 py-4 font-heading text-sm font-semibold uppercase tracking-[0.15em] transition-all duration-500 hover:shadow-2xl hover:shadow-foreground/20"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
        @mousemove="handleMouseMove"
    >
        <!-- Background fill animation -->
        <div
            ref="bgFillRef"
            class="absolute inset-0 origin-left scale-x-0 bg-primary transition-transform duration-500 ease-out group-hover:scale-x-100"
        />

        <!-- Text content -->
        <span ref="textRef" class="relative z-10 flex items-center gap-3">
            <span class="transition-transform duration-300 group-hover:-translate-x-1">
              {{ label }}
            </span>

            <MoveRight
                ref="arrowRef"
                class="size-5 transition-all duration-300 group-hover:translate-x-2"
            />
        </span>

        <!-- Magnetic glow effect -->
        <div
            ref="glowRef"
            class="pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
            :style="glowStyle"
        />
    </Link>
</template>

<style scoped>
.animated-cta {
    position: relative;
    overflow: hidden;
}

.animated-cta::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        transparent 0%,
        rgba(255, 255, 255, 0.1) 50%,
        transparent 100%
    );
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.animated-cta:hover::before {
    transform: translateX(100%);
}
</style>
