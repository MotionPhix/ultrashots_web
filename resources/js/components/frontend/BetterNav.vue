<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { IconMenu, IconX } from '@tabler/icons-vue';
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import BetterLink from '../BetterLink.vue';
import { home } from '@/routes/index';
import { index as projectsIndex } from '@/routes/projects';
import { index as publicDownloadsIndex } from '@/routes/public/downloads';
import { load as contactLoad } from '@/routes/contact';

</script>

<template>
  <!-- ========== HEADER ========== -->
<header
  class="sticky top-0 z-20 flex flex-wrap w-full md:justify-start bg-neutral-50 md:flex-nowrap dark:bg-neutral-900">

  <nav
    class="relative flex flex-wrap items-center w-full px-4 mx-auto max-w-7xl md:grid md:grid-cols-12 basis-full md:px-6">

    <div class="md:col-span-3">

      <!-- Logo -->
      <Link
        as="button"
        class="flex-none inline-block dark:text-neutral-100 focus:outline-none focus:opacity-80"
        :href="home().url" aria-label="Ultrashots">
        <div class="flex items-center gap-2">
          <ApplicationLogo class="size-8" />
          <span class="text-2xl font-semibold">ultrashots</span>
        </div>
      </Link>
      <!-- End Logo -->

    </div>

    <!-- Button Group -->
    <div
      class="flex items-center py-1 gap-x-1 md:gap-x-2 ms-auto md:ps-6 md:order-3 md:col-span-3">

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
          class="hs-collapse-toggle size-[38px] flex justify-center items-center text-sm font-semibold rounded-xl border border-gray-200 text-black hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-neutral-700 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" id="hs-navbar-hcail-collapse" aria-expanded="false" aria-controls="hs-navbar-hcail" aria-label="Toggle navigation" data-hs-collapse="#hs-navbar-hcail">

          <IconMenu
            class="hs-collapse-open:hidden shrink-0 size-5" />

          <IconX
            class="hidden hs-collapse-open:block shrink-0 size-4" />
        </button>
      </div>
    </div>
    <!-- End Button Group -->

    <!-- Collapse -->
    <div
      id="hs-navbar-hcail"
      class="hidden overflow-hidden transition-all duration-300 hs-collapse basis-full grow md:block md:w-auto md:basis-auto md:order-2 md:col-span-6" aria-labelledby="hs-navbar-hcail-collapse">

      <div class="flex flex-col mt-5 gap-y-4 gap-x-0 md:flex-row md:justify-center md:items-center md:gap-y-0 md:gap-x-7 md:mt-0">
        <div>

          <BetterLink
            as="button"
            :href="home().url"
            :active="$page.url === home().url">
            Home
          </BetterLink>

        </div>

        <div>

          <BetterLink
            as="button"
            :href="projectsIndex().url"
            :active="$page.url.startsWith(projectsIndex().url)">
            Work
          </BetterLink>

        </div>

        <div>

          <BetterLink
            as="button"
            :href="publicDownloadsIndex().url"
            :active="$page.url.startsWith(publicDownloadsIndex().url)">
            Downloads
          </BetterLink>

        </div>

        <div>

          <BetterLink
            as="button"
            :href="contactLoad().url"
            :active="$page.url.startsWith(contactLoad().url)">
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
