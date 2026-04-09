<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import type { LandingFooter } from '@/types/home';
import { ArrowUpRight, BriefcaseBusiness, Instagram, Linkedin, Mail, MapPin } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    footer: LandingFooter;
}>();

const socialIcons = computed(() => ({
    instagram: Instagram,
    linkedin: Linkedin,
    behance: BriefcaseBusiness,
}));
</script>

<template>
    <footer class="section-frame pb-10 pt-8 sm:pb-12">
        <div class="surface-panel overflow-hidden px-6 py-8 sm:px-8 sm:py-10">
            <div class="grid gap-8 lg:grid-cols-[0.72fr_0.6fr_0.68fr]">
                <div class="space-y-5">
                    <div>
                        <p class="font-display text-2xl font-bold text-white">Ultrashots</p>
                        <p class="mt-3 max-w-md text-sm leading-7 text-white/62">
                            Premium branding and UI design for Malawi-based teams and regional clients that want a cleaner first impression and stronger conversion paths.
                        </p>
                    </div>

                    <div class="space-y-3 text-sm text-white/68">
                        <a :href="`mailto:${footer.email}`" class="flex items-center gap-3 transition hover:text-white">
                            <Mail class="size-4 text-mint-300" />
                            {{ footer.email }}
                        </a>
                        <p class="flex items-center gap-3">
                            <MapPin class="size-4 text-mint-300" />
                            {{ footer.location }}
                        </p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <p class="font-display text-2xl font-bold text-white">Newsletter</p>
                        <p class="mt-2 text-sm leading-7 text-white/62">
                            {{ footer.newsletter_copy }}
                        </p>
                    </div>

                    <Form action="/subscribe" method="post" class="space-y-3" #default="{ errors, processing }">
                        <input
                            name="email"
                            type="email"
                            class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none placeholder:text-white/30"
                            :placeholder="footer.newsletter_title"
                        >
                        <p v-if="errors.email" class="text-sm text-red-300">{{ errors.email }}</p>
                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-full border border-white/12 bg-white/6 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-70"
                            :disabled="processing"
                        >
                            {{ processing ? 'Subscribing...' : 'Subscribe' }}
                        </button>
                    </Form>
                </div>

                <div class="space-y-5">
                    <div>
                        <p class="font-display text-2xl font-bold text-white">Elsewhere</p>
                        <p class="mt-2 text-sm leading-7 text-white/62">
                            Follow the work, the process, and the latest visual systems across these channels.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <a
                            v-for="social in footer.socials"
                            :key="social.platform"
                            :href="social.url"
                            target="_blank"
                            rel="noreferrer"
                            class="flex items-center justify-between rounded-[22px] border border-white/10 bg-white/4 px-4 py-4 text-sm font-semibold text-white/78 transition hover:bg-white/8"
                        >
                            <span class="flex items-center gap-3">
                                <component :is="socialIcons[social.platform as keyof typeof socialIcons] ?? ArrowUpRight" class="size-4 text-mint-300" />
                                {{ social.label }}
                            </span>
                            <ArrowUpRight class="size-4 text-white/42" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>