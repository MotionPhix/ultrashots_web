<script setup>
import GuestLayout from '@/layouts/AuthLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({
  layout: GuestLayout
})
</script>

<template>

  <Head title="404 Error" />


  <div class="flex flex-col mx-auto size-full">

    <!-- ========== MAIN CONTENT ========== -->
    <main id="content">
      <div class="px-4 py-10 text-center sm:px-6">

        <h1
          class="block font-bold text-gray-800 text-7xl sm:text-9xl dark:text-white">
          404
        </h1>

        <div
          class="mt-3 font-medium text-gray-600 dark:text-neutral-300">
          Oops, something went wrong!
        </div>

        <div
          class="text-gray-600 dark:text-neutral-500">
          We couldn't find your page.
        </div>

        <div
          class="flex flex-col items-center justify-center gap-2 mt-5 sm:flex-row sm:gap-3">

          <Link
            class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-white border border-transparent rounded-lg bg-lime-600 sm:w-auto gap-x-2 hover:bg-lime-700 focus:outline-none focus:bg-lime-700 disabled:opacity-50 disabled:pointer-events-none"
            as="button"
            href="/">
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Start over
          </Link>
        </div>
      </div>
    </main>
    <!-- ========== END MAIN CONTENT ========== -->

    <!-- ========== FOOTER ========== -->
    <footer class="py-5 mt-auto text-center">
      <div class="px-4 mx-auto sm:px-6">
        <p class="text-xs text-gray-500 dark:text-neutral-500">
          © Ultrashots. <br />All Rights Reserved. {{ new Date().getFullYear() }}.
        </p>
      </div>
    </footer>
    <!-- ========== END FOOTER ========== -->
  </div>


</template>

<script>
export default {
  name: 'NotFound',
};
</script>

<style scoped>
.not-found {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
  /* background-color: #f8f9fa; */
  text-align: center;
}

.not-found-content {
  max-width: 400px;
  margin: 0 auto;
}

h1 {
  font-size: 6rem;
  margin: 0;
  color: #dc3545;
}

h2 {
  font-size: 2rem;
  margin: 1rem 0;
}

p {
  margin-bottom: 2rem;
}

.btn {
  padding: 10px 20px;
  background-color: #007bff;
  color: #fff;
  text-decoration: none;
  border-radius: 5px;
}

.btn:hover {
  background-color: #0056b3;
}
</style>
