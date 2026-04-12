<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <div
    :class="
      cn(
        'flex flex-col max-h-[100vh] bg-white dark:bg-gray-900 rounded-xl',
        props.class,
      )
    ">
    <slot />
  </div>
</template>
