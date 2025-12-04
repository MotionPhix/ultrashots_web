<script setup lang="ts">
import { IconTrendingUp, IconTrendingDown } from '@tabler/icons-vue';

interface Props {
  title: string;
  value: number | string;
  icon: any;
  iconColor?: string;
  change?: number;
  changeLabel?: string;
  loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  iconColor: 'text-blue-600',
  loading: false,
});

const formatValue = (value: number | string) => {
  if (typeof value === 'number' && value >= 1000) {
    return (value / 1000).toFixed(1) + 'k';
  }
  return value.toString();
};
</script>

<template>
  <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <div class="flex items-center">
      <div class="shrink-0">
        <component :is="icon" :class="['w-8 h-8', iconColor]" />
      </div>
      <div class="ml-4 flex-1">
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ title }}</p>
        <div class="flex items-baseline">
          <p v-if="!loading" class="text-2xl font-semibold text-gray-900 dark:text-white">
            {{ formatValue(value) }}
          </p>
          <div v-else class="h-8 w-16 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>

          <span
            v-if="change !== undefined && change !== 0 && !loading"
            :class="[
              'ml-2 text-sm font-medium flex items-center',
              change > 0 ? 'text-green-600' : 'text-red-600'
            ]"
          >
            <IconTrendingUp v-if="change > 0" class="w-4 h-4 mr-1" />
            <IconTrendingDown v-else class="w-4 h-4 mr-1" />
            {{ Math.abs(change) }}%
            <span v-if="changeLabel" class="ml-1 text-xs text-gray-500">{{ changeLabel }}</span>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
