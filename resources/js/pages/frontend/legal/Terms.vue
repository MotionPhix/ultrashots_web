<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Business {
    name: string;
    trading_name: string;
    address_lines: string[];
    phone: string;
    phone_local: string;
    email: string;
}

defineProps<{ business: Business; updated: string }>();

defineOptions({ layout: AppLayout });
</script>

<template>
    <Head title="Terms of use" />

    <article class="section-frame mx-auto w-full max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <Heading title="Terms of use" :description="`Last updated ${updated}.`" />

        <div class="mt-10 space-y-10 text-sm leading-relaxed text-white/72">
            <section>
                <h2 class="font-display text-base font-bold text-white">1. Who these terms are with</h2>
                <p class="mt-3">
                    This website and the Vibora application are operated by {{ business.trading_name }},
                    {{ business.address_lines.join(', ') }}. By using either, you agree to these terms.
                </p>
            </section>

            <section>
                <h2 class="font-display text-base font-bold text-white">2. Your account</h2>
                <p class="mt-3">
                    You need an account to use Vibora. You are responsible for the information on it
                    being accurate and for keeping your password to yourself. A phone number can be
                    verified to one account only, and a number stays with that account.
                </p>
            </section>

            <section>
                <h2 class="font-display text-base font-bold text-white">3. Results and ratings</h2>
                <p class="mt-3">
                    Match results are entered by players and confirmed by the players involved.
                    Ratings and standings are calculated by us from those results; the method, and
                    the level it produces, are ours to determine and may be adjusted as the system
                    develops. Deliberately entering false results, or arranging matches to move a
                    rating rather than to play, is a breach of these terms and can cost you access.
                </p>
            </section>

            <section>
                <h2 class="font-display text-base font-bold text-white">4. Acceptable use</h2>
                <p class="mt-3">
                    Do not use the service to break the law, to harass another player, to attempt to
                    access accounts or data that are not yours, or to interfere with the service
                    running for anyone else.
                </p>
            </section>

            <section>
                <h2 class="font-display text-base font-bold text-white">5. Our content</h2>
                <p class="mt-3">
                    The design, text, software and marks on this site belong to
                    {{ business.name }}. You may read and share links to it; you may not present it
                    as your own work.
                </p>
            </section>

            <section>
                <h2 class="font-display text-base font-bold text-white">6. Availability</h2>
                <p class="mt-3">
                    The service is provided as it is. We work to keep it available and correct, but
                    we do not promise uninterrupted access, and features may change while the
                    product is in development.
                </p>
            </section>

            <section>
                <h2 class="font-display text-base font-bold text-white">7. Ending access</h2>
                <p class="mt-3">
                    You can delete your account at any time from the application. We can suspend or
                    close an account that breaches these terms.
                </p>
            </section>

            <section>
                <h2 class="font-display text-base font-bold text-white">8. Changes and law</h2>
                <p class="mt-3">
                    We may update these terms; the date above will change when we do. They are
                    governed by the laws of Malawi.
                </p>
            </section>

            <section class="rounded-3xl border border-white/10 bg-white/4 p-6">
                <h2 class="font-display text-base font-bold text-white">Contact</h2>
                <p class="mt-3">
                    {{ business.trading_name }} · {{ business.address_lines.join(', ') }} ·
                    {{ business.phone_local }} ·
                    <a :href="`mailto:${business.email}`" class="text-mint-300 hover:text-mint-200">{{ business.email }}</a>
                </p>
                <p class="mt-3">
                    See also the <Link href="/privacy" class="text-mint-300 hover:text-mint-200">privacy notice</Link>.
                </p>
            </section>
        </div>
    </article>
</template>
