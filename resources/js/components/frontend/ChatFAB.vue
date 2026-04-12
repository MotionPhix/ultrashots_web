<script setup lang="ts">
import { index as chatIndex } from '@/routes/chat';
import { ModalLink } from '@inertiaui/modal-vue';
import { MessageCircle } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

const isVisible = ref(false);

onMounted(() => {
  // Show FAB after a short delay for smooth entrance
  setTimeout(() => {
    isVisible.value = true;
  }, 500);
});
</script>

<template>
  <Transition
    enter-active-class="transition-all duration-500 ease-out"
    enter-from-class="opacity-0 translate-y-4 scale-95"
    enter-to-class="opacity-100 translate-y-0 scale-100"
    leave-active-class="transition-all duration-300 ease-in"
    leave-from-class="opacity-100 translate-y-0 scale-100"
    leave-to-class="opacity-0 translate-y-4 scale-95"
  >
    <div
      v-if="isVisible"
      class="fixed bottom-6 right-6 z-50 sm:bottom-8 sm:right-8"
    >
      <ModalLink
        :href="chatIndex().url"
        class="group relative flex h-14 w-14 items-center justify-center rounded-full bg-primary text-primary-foreground shadow-lg transition-all duration-300 hover:scale-110 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-background sm:h-16 sm:w-16"
        aria-label="Start a conversation"
      >
        <!-- Pulse animation ring -->
        <span
          class="absolute inset-0 animate-ping rounded-full bg-primary opacity-20"
        ></span>

        <!-- Icon -->
        <MessageCircle
          class="h-6 w-6 transition-transform duration-300 group-hover:scale-110 sm:h-7 sm:w-7"
        />

        <!-- Tooltip -->
        <span
          class="absolute right-full mr-3 hidden whitespace-nowrap rounded-md bg-popover px-3 py-1.5 text-sm font-medium text-popover-foreground shadow-md opacity-0 transition-opacity duration-200 group-hover:block group-hover:opacity-100 sm:inline-block"
        >
          Let's Chat!
        </span>
      </ModalLink>
    </div>
  </Transition>
</template>

<style scoped>
/* Ensure smooth animations */
.animate-ping {
  animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

@keyframes ping {
  75%,
  100% {
    transform: scale(1.5);
    opacity: 0;
  }
}
</style>
