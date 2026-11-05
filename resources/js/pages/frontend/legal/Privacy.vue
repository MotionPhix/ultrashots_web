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

const collected = [
    {
        title: 'Account details',
        body: 'Your name, email address, date of birth and gender, plus your password in hashed form. We never store the password itself.',
    },
    {
        title: 'Phone number',
        body: 'The number you verify, kept as a one-way digest beside a record of when it was verified. The six-digit code we send is also stored only as a digest, and it expires.',
    },
    {
        title: 'Play data',
        body: 'Matches you log or take part in, the games and scores in them, the venue, and the rating that follows. These are what the ladder is built from.',
    },
    {
        title: 'Preferences and activity',
        body: 'The settings you choose — such as where you play — and the notification tokens needed to reach your device.',
    },
];
</script>

<template>
    <Head title="Privacy notice" />

    <article class="section-frame mx-auto w-full max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <Heading title="Privacy notice" :description="`Last updated ${updated}.`" />

        <div class="mt-10 space-y-6 text-sm leading-relaxed text-white/72">
            <p>
                This notice explains what {{ business.trading_name }} holds about you when you use
                this website or Vibora, why we hold it, and how to get it removed.
            </p>
        </div>

        <section class="mt-12">
            <h2 class="font-display text-sm font-bold tracking-[0.18em] text-white/50 uppercase">What we hold</h2>
            <ul class="mt-5 space-y-4">
                <li v-for="item in collected" :key="item.title" class="rounded-2xl border border-white/10 bg-white/4 p-5">
                    <h3 class="font-display text-sm font-bold text-white">{{ item.title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-white/65">{{ item.body }}</p>
                </li>
            </ul>
        </section>

        <div class="mt-12 space-y-10 text-sm leading-relaxed text-white/72">
            <section>
                <h2 class="font-display text-base font-bold text-white">Why we hold it</h2>
                <p class="mt-3">
                    To run your account, to show standings that everyone can trust, to send the
                    verification code and the notifications you have asked for, and to answer you
                    when you write to us. A ranking is only useful if the people in it are real,
                    which is the reason the phone number is verified at all.
                </p>
            </section>

            <section>
                <h2 class="font-display text-base font-bold text-white">Who else sees it</h2>
                <p class="mt-3">
                    Other players see your name, your picture, where you play and your rating — the
                    things a ladder is made of. They do not see your email address or your phone
                    number. Your verified number itself is not shown to anyone.
                </p>
                <p class="mt-3">
                    We use Meta's WhatsApp Business Platform to deliver the verification code, so
                    your number is passed to WhatsApp for that delivery. We do not sell your data
                    and we do not pass it to advertisers.
                </p>
            </section>

            <section>
                <h2 class="font-display text-base font-bold text-white">How long we keep it</h2>
                <p class="mt-3">
                    For as long as your account exists. Verification codes expire in minutes. When
                    you delete your account, the personal details go with it; results you were part
                    of may remain in the record of matches already played, without your details
                    attached to them.
                </p>
            </section>

            <section>
                <h2 class="font-display text-base font-bold text-white">Your choices</h2>
                <p class="mt-3">
                    You can see and correct your details in the application, and delete your account
                    from it. To ask what is held about you, or to have it removed, write to
                    <a :href="`mailto:${business.email}`" class="text-mint-300 hover:text-mint-200">{{ business.email }}</a>.
                </p>
            </section>

            <section>
                <h2 class="font-display text-base font-bold text-white">Cookies</h2>
                <p class="mt-3">
                    This website sets the session and security cookies it needs to keep you signed
                    in and to protect forms. It does not set advertising cookies.
                </p>
            </section>

            <section class="rounded-3xl border border-white/10 bg-white/4 p-6">
                <h2 class="font-display text-base font-bold text-white">Who we are</h2>
                <p class="mt-3">
                    {{ business.trading_name }} · {{ business.address_lines.join(', ') }} ·
                    {{ business.phone_local }} ·
                    <a :href="`mailto:${business.email}`" class="text-mint-300 hover:text-mint-200">{{ business.email }}</a>
                </p>
                <p class="mt-3">
                    See also the <Link href="/terms" class="text-mint-300 hover:text-mint-200">terms of use</Link>.
                </p>
            </section>
        </div>
    </article>
</template>
