<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '#',
    },
];

defineOptions({ layout: AppLayout });
</script>

<template>
    <Head title="My Dashboard" />

    <div class="flex flex-col gap-6 p-6">
        <div>
            <h1 class="text-3xl font-bold">Welcome to Your Dashboard</h1>
            <p class="mt-2 text-muted-foreground">View your projects and track their progress</p>
        </div>

        <!-- Projects Section -->
        <div class="rounded-lg border border-border bg-card p-6">
            <h2 class="text-xl font-semibold mb-4">Your Projects</h2>
            <p class="text-muted-foreground">You don't have any projects yet. Please contact us to get started.</p>
        </div>

        <!-- Recent Activity Section -->
        <div class="rounded-lg border border-border bg-card p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Activity</h2>
            <p class="text-muted-foreground">No recent activity to display.</p>
        </div>
    </div>
</template>
