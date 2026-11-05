<script setup lang="ts">
import { useHomepageContentStore } from '@/stores/useHomepageContentStore';
import type { LandingOwner } from '@/types/home';
import { Link } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { storeToRefs } from 'pinia';
import { load as contactLoad } from '@/actions/App/Http/Controllers/Frontend/ContactController';
import { index as projectsIndex } from '@/actions/App/Http/Controllers/Frontend/ProjectController';
import { index as quoteIndex } from '@/actions/App/Http/Controllers/Frontend/QuoteController';

defineProps<{
    owner: LandingOwner;
}>();

const homepageContentStore = useHomepageContentStore();
const { mobileNavigationOpen } = storeToRefs(homepageContentStore);

const navigationItems = [
    { label: 'About', href: '/about' },
    { label: 'Work', href: projectsIndex.url() },
    { label: 'Vibora', href: '/vibora' },
    { label: 'Services', href: quoteIndex.url() },
    { label: 'Contact', href: contactLoad.url() },
];
</script>

<template>
    <header class="sticky top-0 z-50 border-b border-white/8 bg-black/25 backdrop-blur-xl">
        <div class="section-frame flex items-center justify-between gap-4 py-4">
            <Link class="flex items-center gap-3" href="/">
                <span class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-lagoon-400 to-mint-400 text-sm font-black text-ink-950">U</span>
                <div>
                    <p class="font-display text-sm font-bold tracking-[0.2em] text-white">ULTRASHOTS</p>
                    <p class="text-[0.65rem] text-white/50">{{ owner.location }}</p>
                </div>
            </Link>

            <nav class="hidden items-center gap-1 lg:flex">
                <Link
                    v-for="item in navigationItems"
                    :key="item.href"
                    :href="item.href"
                    class="rounded-full px-4 py-2 text-sm font-medium text-white/68 transition hover:bg-white/8 hover:text-white"
                >
                    {{ item.label }}
                </Link>
            </nav>

            <div class="flex items-center gap-3">
                <Link
                    :href="quoteIndex.url()"
                    class="hidden items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-ink-950 transition hover:bg-mint-300 lg:inline-flex"
                >
                    Get a Quote
                </Link>
                <button
                    class="inline-flex size-10 items-center justify-center rounded-full border border-white/12 bg-white/5 text-white lg:hidden"
                    type="button"
                    @click="homepageContentStore.toggleMobileNavigation()"
                >
                    <Menu v-if="!mobileNavigationOpen" class="size-5" />
                    <X v-else class="size-5" />
                </button>
            </div>
        </div>

        <div v-if="mobileNavigationOpen" class="border-t border-white/8 bg-[#041318]/96 lg:hidden">
            <div class="section-frame flex flex-col gap-2 py-5">
                <Link
                    v-for="item in navigationItems"
                    :key="item.href"
                    :href="item.href"
                    class="rounded-2xl border border-white/8 px-5 py-3.5 text-sm font-semibold text-white/80 transition hover:border-white/16 hover:bg-white/6"
                    @click="homepageContentStore.closeMobileNavigation()"
                >
                    {{ item.label }}
                </Link>
                <Link
                    :href="quoteIndex.url()"
                    class="mt-1 rounded-2xl bg-white px-5 py-3.5 text-center text-sm font-semibold text-ink-950"
                    @click="homepageContentStore.closeMobileNavigation()"
                >
                    Get a Quote
                </Link>
            </div>
        </div>
    </header>
</template>