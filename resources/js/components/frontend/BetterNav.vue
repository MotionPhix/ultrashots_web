<script setup lang="ts">
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import { load as contactLoad } from '@/routes/contact';
import { home } from '@/routes/index';
import { index as projectsIndex } from '@/routes/projects';
import { Link } from '@inertiajs/vue3';
import BetterLink from '../BetterLink.vue';
</script>

<template>
  <!-- ========== HEADER ========== -->
  <header
    class="sticky top-0 z-20 flex w-full flex-wrap bg-neutral-50 md:flex-nowrap md:justify-start dark:bg-neutral-900"
  >
    <nav
      class="relative mx-auto flex w-full max-w-7xl basis-full flex-wrap items-center px-4 md:grid md:grid-cols-12 md:px-6"
    >
      <div class="md:col-span-3">
        <!-- Logo -->
        <Link
          as="button"
          class="inline-block flex-none focus:opacity-80 focus:outline-none dark:text-neutral-100"
          :href="home().url"
          aria-label="Ultrashots"
        >
          <div class="flex items-center gap-2">
            <ApplicationLogo class="size-8" />
            <span class="text-2xl font-semibold">ultrashots</span>
          </div>
        </Link>
        <!-- End Logo -->
      </div>

      <!-- Button Group -->
      <div
        class="ms-auto flex items-center gap-x-1 py-1 md:order-3 md:col-span-3 md:gap-x-2 md:ps-6"
      >
        <!--      <ModalLink-->
        <!--        as="button"-->
        <!--        class="inline-flex items-center px-2 py-1 text-sm font-medium text-black transition border border-transparent rounded-md gap-x-2 bg-lime-400 hover:bg-lime-500 focus:outline-none focus:bg-lime-500 disabled:opacity-50 disabled:pointer-events-none"-->
        <!--        :href="route('fix-my-logo')">-->
        <!--        <IconFileSymlink size="24" />-->
        <!--        <span>Fix my logo</span>-->
        <!--      </ModalLink>-->

        <div class="md:hidden">
          <button
            type="button"
            class="hs-collapse-toggle flex size-[38px] items-center justify-center rounded-xl border border-gray-200 text-sm font-semibold text-black hover:bg-gray-100 focus:bg-gray-100 focus:outline-none disabled:pointer-events-none disabled:opacity-50 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
            id="hs-navbar-hcail-collapse"
            aria-expanded="false"
            aria-controls="hs-navbar-hcail"
            aria-label="Toggle navigation"
            data-hs-collapse="#hs-navbar-hcail"
          >
            <Menu class="hs-collapse-open:hidden size-5 shrink-0" />

            <X class="hs-collapse-open:block hidden size-4 shrink-0" />
          </button>
        </div>
      </div>
      <!-- End Button Group -->

      <!-- Collapse -->
      <div
        id="hs-navbar-hcail"
        class="hs-collapse hidden grow basis-full overflow-hidden transition-all duration-300 md:order-2 md:col-span-6 md:block md:w-auto md:basis-auto"
        aria-labelledby="hs-navbar-hcail-collapse"
      >
        <div
          class="mt-5 flex flex-col gap-x-0 gap-y-4 md:mt-0 md:flex-row md:items-center md:justify-center md:gap-x-7 md:gap-y-0"
        >
          <div>
            <BetterLink
              as="button"
              :href="home().url"
              :active="$page.url === home().url"
            >
              Home
            </BetterLink>
          </div>

          <div>
            <BetterLink
              as="button"
              :href="projectsIndex().url"
              :active="$page.url.startsWith(projectsIndex().url)"
            >
              Work
            </BetterLink>
          </div>

          <div>
            <BetterLink
              as="button"
              :href="contactLoad().url"
              :active="$page.url.startsWith(contactLoad().url)"
            >
              Contact Me
            </BetterLink>
          </div>
        </div>
      </div>
      <!-- End Collapse -->
    </nav>
  </header>
  <!-- ========== END HEADER ========== -->
</template>
