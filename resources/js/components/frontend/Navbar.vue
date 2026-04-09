<script setup lang="ts">
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import NavLinkMode from '@/components/NavLinkMode.vue';
import { load as contactLoad } from '@/routes/contact';
import { home } from '@/routes/index';
import { index as projectsIndex } from '@/routes/projects';
import { index as publicDownloadsIndex } from '@/routes/public/downloads';
import { Link } from '@inertiajs/vue3';
import { Equal, X } from 'lucide-vue-next';
</script>

<template>
  <!-- ========== HEADER ========== -->
  <header
    class="sticky inset-x-0 top-0 z-50 flex w-full flex-wrap sm:text-sm md:flex-nowrap md:justify-start"
  >
    <nav
      class="relative mx-2 mt-4 w-full max-w-2xl rounded-4xl border border-gray-200 bg-white py-2.5 md:mx-auto md:flex md:items-center md:justify-between md:px-4 md:py-0 dark:border-neutral-700 dark:bg-neutral-900"
    >
      <div class="flex items-center justify-between px-4 md:px-0">
        <!-- Logo -->
        <div>
          <Link
            class="flex items-center gap-2 rounded-md text-xl font-semibold focus:opacity-80 focus:outline-none dark:text-gray-300"
            :href="home().url"
            aria-label="Ultrashots"
          >
            <ApplicationLogo class="w-7" />
            <span class="text-2xl">ultrashots</span>
          </Link>
        </div>
        <!-- End Logo -->

        <div class="md:hidden">
          <!-- Toggle Button -->
          <button
            type="button"
            class="hs-collapse-toggle flex size-6 items-center justify-center rounded-full border border-gray-200 p-4 text-gray-500 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none dark:border-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
            id="hs-navbar-header-floating-collapse"
            aria-expanded="false"
            aria-controls="hs-navbar-header-floating"
            aria-label="Toggle navigation"
            data-hs-collapse="#hs-navbar-header-floating"
          >
            <Equal class="hs-collapse-open:hidden size-6 shrink-0" />

            <X class="hs-collapse-open:block hidden size-5 shrink-0" />
          </button>
          <!-- End Toggle Button -->
        </div>
      </div>

      <div
        id="hs-navbar-header-floating"
        class="hs-collapse hidden grow basis-full overflow-hidden transition-all duration-300 md:block"
        aria-labelledby="hs-navbar-header-floating-collapse"
      >
        <div
          class="mt-3 flex flex-col gap-2 py-2 md:mt-0 md:flex-row md:items-center md:justify-end md:gap-3 md:py-0 md:ps-7"
        >
          <NavLinkMode :href="home().url" :active="$page.url === home().url">
            Home
          </NavLinkMode>

          <NavLinkMode
            :href="projectsIndex().url"
            :active="$page.url.startsWith(projectsIndex().url)"
          >
            Projects
          </NavLinkMode>

          <NavLinkMode
            :href="publicDownloadsIndex().url"
            :active="$page.url.startsWith(publicDownloadsIndex().url)"
          >
            Downloads
          </NavLinkMode>

          <NavLinkMode
            :href="contactLoad().url"
            :active="$page.url.startsWith(contactLoad().url)"
          >
            Contact Me
          </NavLinkMode>
        </div>
      </div>
    </nav>
  </header>
  <!-- ========== END HEADER ========== -->
</template>
