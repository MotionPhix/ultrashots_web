<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Business {
    name: string;
    email: string;
    website: string;
}

defineProps<{ business: Business }>();

defineOptions({ layout: AppLayout });

const capabilities = [
    {
        title: 'Log a match, not a spreadsheet',
        body: 'Players record the result of a match they played — teams, games, and where it happened — and it becomes part of the ladder immediately.',
    },
    {
        title: 'A rating that moves',
        body: 'Results feed a rating engine that accounts for the margin of a win and the strength of the opposition, so a level reflects who you beat, not how often you play.',
    },
    {
        title: 'One number, one account',
        body: 'Every player verifies a phone number with a single-use code before their account is live, which keeps duplicates and throwaway accounts out of the ladder.',
    },
    {
        title: 'Clubs and places',
        body: 'Courts and clubs across Malawi are listed by place, so a match is tied to a real venue rather than free text.',
    },
];

const facts = [
    { label: 'Platform', value: 'iOS and Android, built with Flutter' },
    { label: 'Markets', value: 'Malawi' },
    { label: 'Status', value: 'In development — pre-release' },
    { label: 'Built by', value: 'Ultrashots' },
];
</script>

<template>
    <Head title="Vibora" />

    <article class="section-frame mx-auto w-full max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <Heading
            title="Vibora"
            description="A platform for padel clubs and players, designed and built by Ultrashots."
        />

        <div class="mt-10 space-y-6 text-base leading-relaxed text-white/72">
            <p>
                Padel in Malawi is organised in clubs and groups that know their own standings by
                memory and settle arguments over a phone screen. Vibora gives a club one place to
                record results, keep a fair ranking, and know who is actually playing.
            </p>
        </div>

        <ul class="mt-12 space-y-4">
            <li
                v-for="capability in capabilities"
                :key="capability.title"
                class="rounded-2xl border border-white/10 bg-white/4 p-6"
            >
                <h2 class="font-display text-base font-bold text-white">{{ capability.title }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-white/65">{{ capability.body }}</p>
            </li>
        </ul>

        <dl class="mt-12 grid gap-5 sm:grid-cols-2">
            <div v-for="fact in facts" :key="fact.label">
                <dt class="text-xs font-semibold tracking-wide text-white/45 uppercase">{{ fact.label }}</dt>
                <dd class="mt-1 text-sm text-white/85">{{ fact.value }}</dd>
            </div>
        </dl>

        <section class="mt-14 rounded-3xl border border-white/10 bg-white/4 p-6 sm:p-8">
            <h2 class="font-display text-lg font-bold text-white">About the maker</h2>
            <p class="mt-3 text-sm leading-relaxed text-white/65">
                Vibora is a product of {{ business.name }}, the design and software studio behind
                this site. Questions about the platform, or a club wanting to be listed, can go to
                <a :href="`mailto:${business.email}`" class="text-mint-300 hover:text-mint-200">{{ business.email }}</a>.
            </p>
            <p class="mt-4 text-sm">
                <Link href="/about" class="text-mint-300 hover:text-mint-200">More about Ultrashots</Link>
                <span class="text-white/30"> · </span>
                <Link href="/contact" class="text-mint-300 hover:text-mint-200">Contact</Link>
            </p>
        </section>
    </article>
</template>
