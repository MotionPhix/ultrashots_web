<script setup>
import {UseDark} from "@vueuse/components";
</script>

<template>
  <!-- ========== FOOTER ========== -->
  <footer class="w-full max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">
    <div class="py-6">
      <div class="flex flex-wrap items-center justify-between gap-2">

        <div>
          <p class="text-gray-600 dark:text-neutral-400">
            ©{{ new Date().getFullYear() }} <strong>Ultrashots</strong>.
          </p>
        </div>
        <!-- End Col -->

        <!-- List -->
        <ul class="flex items-center w-full sm:w-auto">

          <li
            class="inline-block relative pe-4 last:pe-0 last-of-type:before:hidden before:absolute before:top-1/2 before:end-1.5 before:-translate-y-1/2 before:size-[3px] before:rounded-full before:bg-gray-400 dark:text-neutral-500 dark:before:bg-neutral-600">

            <a
              class="text-sm text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 dark:text-neutral-500 dark:hover:text-neutral-400"
              href="https://x.com/ultrashoots" target="_blank">
              X (Twitter)
            </a>

          </li>

          <li
            class="inline-block relative pe-4 last:pe-0 last-of-type:before:hidden before:absolute before:top-1/2 before:end-1.5 before:-translate-y-1/2 before:size-[3px] before:rounded-full before:bg-gray-400 dark:text-neutral-500 dark:before:bg-neutral-600">

            <a
              class="text-sm text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 dark:text-neutral-500 dark:hover:text-neutral-400"
              href="https://be.net/ultrashots" target="_blank">
              Behance
            </a>

          </li>

          <li class="inline-block pe-4">

            <a
              class="text-sm text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 dark:text-neutral-500 dark:hover:text-neutral-400"
              href="https://github.com/motionphix" target="_blank">
              Github
            </a>

          </li>

          <li class="block flex-1" />

          <li class="inline-block">

            <UseDark v-slot="{ isDark, toggleDark }">
              <button
                class="p-2 text-muted-foreground hover:text-foreground transition-colors duration-200 rounded-md hover:bg-muted/50"
                @click="toggleDark()"
                aria-label="Toggle theme">
                <IconSun class="size-4" stroke="2" v-if="isDark" />
                <IconMoon class="size-4" stroke="2" v-else />
              </button>
            </UseDark>

          </li>

        </ul>
        <!-- End List -->

      </div>

    </div>

  </footer>
  <!-- ========== END FOOTER ========== -->
</template>
