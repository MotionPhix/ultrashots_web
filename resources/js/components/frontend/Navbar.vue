<script setup>
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import { IconMenu4, IconX } from '@tabler/icons-vue';
import { Link } from '@inertiajs/vue3';
import NavLinkMode from '@/components/NavLinkMode.vue';
import { home } from '@/routes/index';
import { index as projectsIndex } from '@/routes/projects';
import { index as publicDownloadsIndex } from '@/routes/public/downloads';
import { index as contactIndex } from '@/routes/contact';

</script>

<template>
  <!-- ========== HEADER ========== -->
  <header
    class="sticky inset-x-0 top-0 z-50 flex flex-wrap w-full md:justify-start md:flex-nowrap sm:text-sm">

    <nav
      class="mt-4 relative max-w-2xl w-full bg-white border border-gray-200 rounded-4xl mx-2 py-2.5 md:flex md:items-center md:justify-between md:py-0 md:px-4 md:mx-auto dark:bg-neutral-900 dark:border-neutral-700">

      <div class="flex items-center justify-between px-4 md:px-0">
        <!-- Logo -->
        <div>
          <Link
            class="flex items-center gap-2 text-xl font-semibold rounded-md dark:text-gray-300 focus:outline-none focus:opacity-80" :href="home().url" aria-label="Ultrashots">
            <ApplicationLogo class="w-7" />
            <span class="text-2xl ">ultrashots</span>
          </Link>
        </div>
        <!-- End Logo -->

        <div class="md:hidden">
          <!-- Toggle Button -->
          <button
            type="button"
            class="flex items-center justify-center p-4 text-gray-500 border border-gray-200 rounded-full hs-collapse-toggle size-6 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 dark:border-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" id="hs-navbar-header-floating-collapse"
            aria-expanded="false" aria-controls="hs-navbar-header-floating" aria-label="Toggle navigation" data-hs-collapse="#hs-navbar-header-floating">

            <IconMenu4 stroke="2.5" class="hs-collapse-open:hidden shrink-0 size-6" />

            <IconX stroke="2.5" class="hidden hs-collapse-open:block shrink-0 size-5" />

          </button>
          <!-- End Toggle Button -->
        </div>
      </div>

      <div
        id="hs-navbar-header-floating"
        class="hidden overflow-hidden transition-all duration-300 hs-collapse basis-full grow md:block" aria-labelledby="hs-navbar-header-floating-collapse">

        <div
          class="flex flex-col gap-2 py-2 mt-3 md:flex-row md:items-center md:justify-end md:gap-3 md:mt-0 md:py-0 md:ps-7">

          <NavLinkMode
            :href="home().url"
            :active="$page.url === home().url">
            Home
          </NavLinkMode>

          <NavLinkMode
            :href="projectsIndex().url"
            :active="$page.url.startsWith(projectsIndex().url)">
            Projects
          </NavLinkMode>

          <NavLinkMode
            :href="publicDownloadsIndex().url"
            :active="$page.url.startsWith(publicDownloadsIndex().url)">
            Downloads
          </NavLinkMode>

          <NavLinkMode
            :href="contactIndex().url"
            :active="$page.url.startsWith(contactIndex().url)">
            Contact Me
          </NavLinkMode>

        </div>

      </div>

    </nav>

  </header>
  <!-- ========== END HEADER ========== -->

</template>
