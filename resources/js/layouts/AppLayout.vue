<script setup lang="ts">
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { useFlashMessages } from '@/composables/useFlashMessages';
import { Toaster } from 'vue-sonner';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

useFlashMessages()

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => []
});
</script>

<template>
  <Toaster
    rich-colors
    position="top-right"
  />

  <AppLayout :breadcrumbs="breadcrumbs">
    <slot />
  </AppLayout>
</template>
