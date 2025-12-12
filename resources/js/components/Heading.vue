<script setup lang="ts">
interface Props {
  title: string;
  description?: string;
}

defineProps<Props>();
</script>

<template>
  <div class="mb-8 space-y-0.5">
    <h2 class="text-2xl sm:text-4xl font-semibold tracking-tight">{{ title }}</h2>
    <p v-if="description" class="sm:text-lg text-muted-foreground">
      {{ description }}
    </p>
  </div>
</template>
