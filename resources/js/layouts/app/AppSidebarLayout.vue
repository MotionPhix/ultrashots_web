<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" class="sticky top-0 z-50 bg-background" />
            <section class="overflow-y-auto absolute top-16 bottom-0 w-full scrollbar-thin">
              <slot />
            </section>
        </AppContent>
    </AppShell>
</template>
