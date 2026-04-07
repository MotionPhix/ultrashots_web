<script setup lang="ts">
import { useAttrs } from 'vue';

import AppLogoIcon from '@/components/AppLogoIcon.vue';

const attrs = useAttrs();
</script>

<template>
    <AppLogoIcon v-bind="attrs" class="fill-current" />
</template>