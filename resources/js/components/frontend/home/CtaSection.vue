<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { index as quoteIndex } from '@/routes/quote';
import type { LandingCta } from '@/types/home';
import { Form, Link } from '@inertiajs/vue3';
import { ArrowRight, CalendarDays, Clock, MessageCircle, TrendingUp } from 'lucide-vue-next';

defineProps<{
    cta: LandingCta;
    calendlyUrl?: string;
}>();
</script>

<template>
    <section id="cta" class="relative bg-secondary/30 py-20 lg:py-28">
        <div class="section-frame">
            <!-- Main CTA Banner -->
            <div class="flex flex-col relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary via-primary/95 to-primary/90 px-8 py-16 shadow-2xl sm:px-12 lg:flex lg:justify-between lg:gap-12">
                <div class="space-y-6 lg:max-w-2/3">
                    <span class="inline-flex items-center gap-2 rounded-full border border-primary-foreground/20 bg-primary-foreground/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-primary-foreground font-display">
                        Let's Work Together
                    </span>
                    <h2 class="font-display text-3xl font-bold leading-tight text-primary-foreground sm:text-4xl lg:text-5xl">
                        {{ cta.title }}
                    </h2>
                    <p class="text-base leading-relaxed text-primary-foreground/85 font-sans sm:text-lg">
                        {{ cta.description }}
                    </p>
                </div>

                <div class="mt-10 flex shrink-0 flex-col gap-4 sm:flex-row lg:mt-0">
                    <Button
                        v-if="calendlyUrl"
                        :as="'a'"
                        :href="calendlyUrl"
                        target="_blank"
                        rel="noreferrer"
                        class="gap-2 bg-background px-8 py-3.5 text-sm font-bold text-foreground hover:bg-background/90 transition-all duration-200 shadow-lg"
                    >
                        <CalendarDays class="size-4" />
                        {{ cta.calendly_label }}
                    </Button>

                    <Button
                        v-else
                        :as="Link"
                        :href="quoteIndex().url"
                        class="gap-2 bg-background !h-12 text-sm font-bold text-foreground hover:bg-background/90 rounded-full"
                    >
                        <CalendarDays class="size-4" />
                        {{ cta.calendly_label }}
                    </Button>

                    <Button
                        :as="Link"
                        :href="quoteIndex().url"
                        variant="outline"
                        class="gap-2 border-primary-foreground/30 bg-transparent h-12 text-sm font-semibold text-primary-foreground hover:bg-primary-foreground/10 rounded-full"
                    >
                        Get a full quote
                        <ArrowRight class="size-4" />
                    </Button>
                </div>

                <div class="hidden lg:block absolute inset-y-0 right-0 w-1/3 h-full opacity-50">
                  <img class="h-full object-cover" src="https://images.unsplash.com/photo-1556761175-4b46a572b786?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80" alt="">
                </div>
            </div>

            <!-- Two-column layout: Benefits + Contact Form -->
            <div class="mt-12 grid gap-8 lg:grid-cols-[1fr_1.2fr]">
                <!-- Left: Value proposition -->
                 <article>
                  <div class="p-8">
                      <h3 class="font-heading text-2xl font-bold text-foreground mb-6">
                          {{ cta.fallback_title }}
                      </h3>
                      <p class="text-base leading-relaxed text-muted-foreground font-sans mb-8">
                          {{ cta.fallback_copy }}
                      </p>

                      <div class="space-y-5">
                          <div class="flex items-start gap-4">
                              <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                  <Clock class="size-5" />
                              </div>
                              <div>
                                  <p class="font-heading text-sm font-semibold text-foreground">24-Hour Response Guarantee</p>
                                  <p class="mt-1 text-sm text-muted-foreground font-sans">No ghosting, no back-and-forth delays</p>
                              </div>
                          </div>

                          <div class="flex items-start gap-4">
                              <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                  <MessageCircle class="size-5" />
                              </div>
                              <div>
                                  <p class="font-heading text-sm font-semibold text-foreground">Free Strategy Call</p>
                                  <p class="mt-1 text-sm text-muted-foreground font-sans">Make sure we're the perfect fit before you spend a dime</p>
                              </div>
                          </div>

                          <div class="flex items-start gap-4">
                              <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                  <TrendingUp class="size-5" />
                              </div>
                              <div>
                                  <p class="font-heading text-sm font-semibold text-foreground">Revenue-Focused Design</p>
                                  <p class="mt-1 text-sm text-muted-foreground font-sans">Work with a strategist who cares about your growth, not just aesthetics</p>
                              </div>
                          </div>
                      </div>
                  </div>
                </article>

                <!-- Right: Contact form -->
                <div class="rounded-2xl border border-border bg-background p-8 shadow-sm">
                    <div class="mb-6">
                        <h3 class="font-heading text-xl font-bold text-foreground">Send a project brief</h3>
                        <p class="mt-1 text-sm text-muted-foreground font-sans">Tell me what you're building — I'll tell you how to make it unforgettable.</p>
                    </div>

                    <Form action="/get-in-touch" method="post" class="space-y-5" #default="{ errors, processing }">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-wider text-muted-foreground font-heading" for="cta-name">Full name</label>
                                <input
                                    id="cta-name"
                                    name="name"
                                    type="text"
                                    class="w-full rounded-lg border border-border bg-secondary/30 px-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-200 font-sans"
                                    placeholder="Jane Banda"
                                />
                                <p v-if="errors.name" class="text-xs text-red-600 dark:text-red-400 font-sans">{{ errors.name }}</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-wider text-muted-foreground font-heading" for="cta-email">Email</label>
                                <input
                                    id="cta-email"
                                    name="email"
                                    type="email"
                                    class="w-full rounded-lg border border-border bg-secondary/30 px-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-200 font-sans"
                                    placeholder="jane@company.com"
                                />
                                <p v-if="errors.email" class="text-xs text-red-600 dark:text-red-400 font-sans">{{ errors.email }}</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-wider text-muted-foreground font-heading" for="cta-phone">Phone</label>
                                <input
                                    id="cta-phone"
                                    name="phone"
                                    type="tel"
                                    class="w-full rounded-lg border border-border bg-secondary/30 px-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-200 font-sans"
                                    placeholder="+265 999 000 000"
                                />
                                <p v-if="errors.phone" class="text-xs text-red-600 dark:text-red-400 font-sans">{{ errors.phone }}</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-wider text-muted-foreground font-heading" for="cta-company">Company</label>
                                <input
                                    id="cta-company"
                                    name="company"
                                    type="text"
                                    class="w-full rounded-lg border border-border bg-secondary/30 px-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-200 font-sans"
                                    placeholder="Your organization"
                                />
                                <p v-if="errors.company" class="text-xs text-red-600 dark:text-red-400 font-sans">{{ errors.company }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-wider text-muted-foreground font-heading" for="cta-message">What needs building?</label>
                            <textarea
                                id="cta-message"
                                name="message"
                                rows="4"
                                class="w-full rounded-lg border border-border bg-secondary/30 px-4 py-3 text-sm text-foreground outline-none placeholder:text-muted-foreground focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-200 resize-none font-sans"
                                placeholder="We need a brand identity that turns heads and closes deals..."
                            />
                            <p v-if="errors.message" class="text-xs text-red-600 dark:text-red-400 font-sans">{{ errors.message }}</p>
                        </div>

                        <Button
                            type="submit"
                            class="w-full gap-2 bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-200 font-semibold py-6"
                            :disabled="processing"
                        >
                            {{ processing ? 'Sending brief...' : 'Send project brief — it\'s free' }}
                            <ArrowRight class="size-4" />
                        </Button>
                    </Form>
                </div>
            </div>
        </div>
    </section>
</template>
