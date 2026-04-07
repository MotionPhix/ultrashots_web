<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, useAttrs } from 'vue';

const props = defineProps<{
    active?: boolean;
}>();

const attrs = useAttrs();

const classes = computed(() => [
    'inline-flex items-center text-sm font-medium transition-colors',
    props.active
        ? 'text-foreground'
        : 'text-foreground/70 hover:text-foreground',
]);
</script>

<template>
    <Link v-bind="attrs" :class="classes">
        <slot />
    </Link>
</template>