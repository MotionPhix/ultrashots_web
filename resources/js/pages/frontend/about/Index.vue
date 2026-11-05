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
    website: string;
    services: string[];
}

defineProps<{ business: Business }>();

defineOptions({ layout: AppLayout });
</script>

<template>
    <Head title="About Ultrashots" />

    <article class="section-frame mx-auto w-full max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <Heading
            title="About Ultrashots"
            description="A design and software studio in Lilongwe, Malawi."
        />

        <div class="mt-10 space-y-6 text-base leading-relaxed text-white/72">
            <p>
                Ultrashots is a studio practice working across graphic design, printing, web
                development and custom software. We build the printed things a business hands to
                a customer and the systems it runs on afterwards — brand and layout work,
                websites, and applications written for a specific job that off-the-shelf software
                does not do.
            </p>
            <p>
                The practice is run from Lilongwe and works with clients in Malawi and abroad.
                Some of that work is our own: <Link href="/vibora" class="font-medium text-mint-300 hover:text-mint-200">Vibora</Link>,
                a platform for padel clubs and players, is designed and built here.
            </p>
        </div>

        <section class="mt-14">
            <h2 class="font-display text-sm font-bold tracking-[0.18em] text-white/50 uppercase">
                What we do
            </h2>
            <ul class="mt-5 grid gap-3 sm:grid-cols-2">
                <li
                    v-for="service in business.services"
                    :key="service"
                    class="rounded-2xl border border-white/10 bg-white/4 px-5 py-4 text-sm font-medium text-white/85"
                >
                    {{ service }}
                </li>
            </ul>
        </section>

        <!-- The block a verification reviewer is looking for: who this business
             is, where it is, and how to reach it, stated plainly and matching
             the registered details. -->
        <section class="mt-14 rounded-3xl border border-white/10 bg-white/4 p-6 sm:p-8">
            <h2 class="font-display text-sm font-bold tracking-[0.18em] text-white/50 uppercase">
                Business details
            </h2>

            <dl class="mt-6 space-y-5 text-sm">
                <div>
                    <dt class="text-xs font-semibold tracking-wide text-white/45 uppercase">Registered name</dt>
                    <dd class="mt-1 text-white/85">{{ business.trading_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold tracking-wide text-white/45 uppercase">Address</dt>
                    <dd class="mt-1 text-white/85">
                        <span v-for="(line, index) in business.address_lines" :key="line">
                            {{ line }}<template v-if="index < business.address_lines.length - 1"><br /></template>
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold tracking-wide text-white/45 uppercase">Phone</dt>
                    <dd class="mt-1 text-white/85">
                        <a :href="`tel:${business.phone.replace(/\s/g, '')}`" class="hover:text-mint-300">{{ business.phone_local }}</a>
                        <span class="text-white/40"> ({{ business.phone }})</span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold tracking-wide text-white/45 uppercase">Email</dt>
                    <dd class="mt-1 text-white/85">
                        <a :href="`mailto:${business.email}`" class="hover:text-mint-300">{{ business.email }}</a>
                    </dd>
                </div>
            </dl>
        </section>

        <p class="mt-10 text-sm text-white/50">
            For enquiries, <Link href="/contact" class="text-mint-300 hover:text-mint-200">write to us</Link>
            or ask for a <Link href="/quote" class="text-mint-300 hover:text-mint-200">quotation</Link>.
        </p>
    </article>
</template>
