<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import { index as customersIndex } from '@/routes/admin/customers';
import { refresh } from '@/routes/admin/dashboard';
import { index as projectsIndex } from '@/routes/admin/projects';
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';

setLayoutProps({
    breadcrumbs: [{ title: 'Dashboard', href: dashboardIndex.url() }],
});

interface MonthlyComparison {
    current: { projects: number; customers: number; subscribers: number };
    previous: { projects: number; customers: number; subscribers: number };
    changes: { projects: number; customers: number; subscribers: number };
}

interface Alert {
    type: 'warning' | 'info';
    title: string;
    message: string;
    route: string;
    action_text: string;
}

interface TimelineItem {
    type: 'customer' | 'project';
    title: string;
    subtitle: string | null;
    occurred_at: string;
    time: string;
    icon: string;
    color: string;
}

interface DeadlineItem {
    uuid: string;
    name: string;
    end_date: string;
    days_left: number;
    customer: string;
    priority: string;
    priority_color: string;
}

const props = defineProps<{
    stats: {
        customers_count: number;
        projects_count: number;
        downloads_count: number;
        subscribers_count: number;
    };
    trends: {
        daily: Array<{ date: string; label: string; projects: number; customers: number; subscribers: number }>;
        monthly_comparison: MonthlyComparison;
    };
    recent_activity: {
        timeline: TimelineItem[];
    };
    project_insights: {
        status_distribution: Record<string, number>;
        priority_distribution: Record<string, number>;
        overdue_projects: number;
        upcoming_deadlines: DeadlineItem[];
    };
    performance_metrics: {
        total_estimated_hours: number;
        total_actual_hours: number;
        total_budget: number;
        efficiency_rate: number;
        completion_rate: number;
        average_project_duration: number;
    };
    alerts: Alert[];
}>();

const formatChange = (value: number): string => {
    if (value === 0) return '—';
    return `${value > 0 ? '+' : ''}${value}%`;
};

const changeColor = (value: number): string => {
    if (value > 0) return 'text-green-600 dark:text-green-400';
    if (value < 0) return 'text-red-500 dark:text-red-400';
    return 'text-muted-foreground';
};

const totalStatusCount = (): number => {
    return Object.values(props.project_insights.status_distribution).reduce((sum, n) => sum + n, 0);
};

const totalPriorityCount = (): number => {
    return Object.values(props.project_insights.priority_distribution).reduce((sum, n) => sum + n, 0);
};

const statusBarWidth = (count: number): string => {
    const total = totalStatusCount();
    if (total === 0) return '0%';
    return `${Math.round((count / total) * 100)}%`;
};

const priorityBarWidth = (count: number): string => {
    const total = totalPriorityCount();
    if (total === 0) return '0%';
    return `${Math.round((count / total) * 100)}%`;
};

const statusBarColor = (status: string): string => {
    const colors: Record<string, string> = {
        active: 'bg-green-500',
        in_progress: 'bg-blue-500',
        completed: 'bg-emerald-500',
        on_hold: 'bg-yellow-500',
        cancelled: 'bg-red-400',
        pending: 'bg-slate-400',
    };
    return colors[status] ?? 'bg-muted-foreground';
};

const priorityBarColor = (priority: string): string => {
    const colors: Record<string, string> = {
        critical: 'bg-red-500',
        high: 'bg-orange-500',
        medium: 'bg-yellow-500',
        low: 'bg-slate-400',
    };
    return colors[priority] ?? 'bg-muted-foreground';
};

const doRefresh = () => {
    router.post(refresh.url(), {}, { preserveScroll: true });
};

defineOptions({ layout: AdminLayout });
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6 p-4">
        <!-- Header -->
        <section class="rounded-3xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-muted-foreground">Admin</p>
                    <h1 class="mt-2 text-3xl font-semibold">Dashboard</h1>
                    <p class="mt-2 max-w-2xl text-sm text-muted-foreground">
                        An overview of your projects, clients, and studio performance.
                    </p>
                </div>
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-full border border-input px-5 py-3 text-sm font-medium transition-opacity hover:opacity-70"
                    @click="doRefresh"
                >
                    Refresh data
                </button>
            </div>
        </section>

        <!-- Alerts -->
        <div v-if="alerts.length > 0" class="flex flex-col gap-3">
            <div
                v-for="(alert, i) in alerts"
                :key="i"
                :class="[
                    'flex items-center justify-between gap-4 rounded-2xl border px-5 py-4 text-sm',
                    alert.type === 'warning'
                        ? 'border-yellow-400/40 bg-yellow-50 text-yellow-900 dark:bg-yellow-900/10 dark:text-yellow-300'
                        : 'border-blue-400/40 bg-blue-50 text-blue-900 dark:bg-blue-900/10 dark:text-blue-300',
                ]"
            >
                <div>
                    <span class="font-semibold">{{ alert.title }}: </span>{{ alert.message }}
                </div>
                <Link
                    :href="projectsIndex.url()"
                    class="shrink-0 rounded-full border border-current px-3 py-1.5 text-xs font-medium opacity-80 hover:opacity-100"
                >
                    {{ alert.action_text }}
                </Link>
            </div>
        </div>

        <!-- Stats Cards -->
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Customers</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.customers_count.toLocaleString() }}</p>
                <p :class="['mt-2 text-xs font-medium', changeColor(trends.monthly_comparison.changes.customers)]">
                    {{ formatChange(trends.monthly_comparison.changes.customers) }} this month
                </p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Projects</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.projects_count.toLocaleString() }}</p>
                <p :class="['mt-2 text-xs font-medium', changeColor(trends.monthly_comparison.changes.projects)]">
                    {{ formatChange(trends.monthly_comparison.changes.projects) }} this month
                </p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Downloads</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.downloads_count.toLocaleString() }}</p>
            </div>
            <div class="rounded-2xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border">
                <p class="text-sm text-muted-foreground">Subscribers</p>
                <p class="mt-3 text-3xl font-semibold">{{ stats.subscribers_count.toLocaleString() }}</p>
                <p :class="['mt-2 text-xs font-medium', changeColor(trends.monthly_comparison.changes.subscribers)]">
                    {{ formatChange(trends.monthly_comparison.changes.subscribers) }} this month
                </p>
            </div>
        </section>

        <!-- Activity + Project Status -->
        <div class="grid gap-6 lg:grid-cols-5">
            <!-- Recent Activity -->
            <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border lg:col-span-3">
                <h2 class="text-sm font-semibold">Recent Activity</h2>
                <p class="mt-1 text-xs text-muted-foreground">Latest customers and projects</p>

                <div v-if="recent_activity.timeline.length === 0" class="mt-6 rounded-2xl border border-dashed border-input px-6 py-10 text-center text-sm text-muted-foreground">
                    No recent activity yet.
                </div>

                <ul v-else class="mt-5 flex flex-col gap-4">
                    <li v-for="(item, i) in recent_activity.timeline" :key="i" class="flex items-start gap-3">
                        <div
                            :class="[
                                'mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white',
                                item.color === 'blue' ? 'bg-blue-500' : 'bg-emerald-500',
                            ]"
                        >
                            {{ item.type === 'customer' ? 'C' : 'P' }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ item.title }}</p>
                            <p v-if="item.subtitle" class="truncate text-xs text-muted-foreground">{{ item.subtitle }}</p>
                        </div>
                        <span class="shrink-0 text-xs text-muted-foreground">{{ item.time }}</span>
                    </li>
                </ul>
            </section>

            <!-- Project Distribution -->
            <section class="flex flex-col gap-6 lg:col-span-2">
                <div class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold">By Status</h2>
                        <span
                            v-if="project_insights.overdue_projects > 0"
                            class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 dark:bg-red-900/20 dark:text-red-400"
                        >
                            {{ project_insights.overdue_projects }} overdue
                        </span>
                    </div>
                    <ul class="mt-4 flex flex-col gap-3">
                        <li
                            v-for="(count, status) in project_insights.status_distribution"
                            :key="status"
                            class="flex flex-col gap-1"
                        >
                            <div class="flex justify-between text-xs">
                                <span class="capitalize text-muted-foreground">{{ String(status).replace('_', ' ') }}</span>
                                <span class="font-medium">{{ count }}</span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-muted">
                                <div
                                    :class="['h-full rounded-full', statusBarColor(String(status))]"
                                    :style="{ width: statusBarWidth(count) }"
                                />
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <h2 class="text-sm font-semibold">By Priority</h2>
                    <ul class="mt-4 flex flex-col gap-3">
                        <li
                            v-for="(count, priority) in project_insights.priority_distribution"
                            :key="priority"
                            class="flex flex-col gap-1"
                        >
                            <div class="flex justify-between text-xs">
                                <span class="capitalize text-muted-foreground">{{ priority }}</span>
                                <span class="font-medium">{{ count }}</span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-muted">
                                <div
                                    :class="['h-full rounded-full', priorityBarColor(String(priority))]"
                                    :style="{ width: priorityBarWidth(count) }"
                                />
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
        </div>

        <!-- Upcoming Deadlines + Performance -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Upcoming Deadlines -->
            <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold">Upcoming Deadlines</h2>
                        <p class="mt-1 text-xs text-muted-foreground">Projects due in the next 7 days</p>
                    </div>
                    <Link
                        :href="projectsIndex.url()"
                        class="rounded-full border border-input px-3 py-1.5 text-xs font-medium"
                    >
                        All projects
                    </Link>
                </div>

                <div
                    v-if="project_insights.upcoming_deadlines.length === 0"
                    class="mt-6 rounded-2xl border border-dashed border-input px-6 py-10 text-center text-sm text-muted-foreground"
                >
                    No upcoming deadlines this week.
                </div>

                <ul v-else class="mt-5 flex flex-col gap-3">
                    <li
                        v-for="deadline in project_insights.upcoming_deadlines"
                        :key="deadline.uuid"
                        class="flex items-center justify-between gap-3 rounded-2xl border border-sidebar-border/50 p-3 dark:border-sidebar-border"
                    >
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium">{{ deadline.name }}</p>
                            <p class="truncate text-xs text-muted-foreground">{{ deadline.customer }} · {{ deadline.end_date }}</p>
                        </div>
                        <span
                            :class="[
                                'shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold',
                                deadline.days_left <= 1
                                    ? 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400'
                                    : deadline.days_left <= 3
                                        ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400'
                                        : 'bg-muted text-muted-foreground',
                            ]"
                        >
                            {{ deadline.days_left === 0 ? 'Today' : `${deadline.days_left}d left` }}
                        </span>
                    </li>
                </ul>
            </section>

            <!-- Performance Metrics -->
            <section class="rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <h2 class="text-sm font-semibold">Performance</h2>
                <p class="mt-1 text-xs text-muted-foreground">Hours, budget, and completion across all projects</p>

                <div class="mt-5 grid grid-cols-2 gap-4">
                    <div class="rounded-2xl bg-muted/40 p-4">
                        <p class="text-xs text-muted-foreground">Completion Rate</p>
                        <p class="mt-2 text-2xl font-semibold">{{ performance_metrics.completion_rate }}%</p>
                        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-muted">
                            <div
                                class="h-full rounded-full bg-emerald-500"
                                :style="{ width: `${performance_metrics.completion_rate}%` }"
                            />
                        </div>
                    </div>
                    <div class="rounded-2xl bg-muted/40 p-4">
                        <p class="text-xs text-muted-foreground">Efficiency Rate</p>
                        <p class="mt-2 text-2xl font-semibold">{{ performance_metrics.efficiency_rate }}%</p>
                        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-muted">
                            <div
                                class="h-full rounded-full bg-blue-500"
                                :style="{ width: `${Math.min(performance_metrics.efficiency_rate, 100)}%` }"
                            />
                        </div>
                    </div>
                    <div class="rounded-2xl bg-muted/40 p-4">
                        <p class="text-xs text-muted-foreground">Estimated Hours</p>
                        <p class="mt-2 text-2xl font-semibold">{{ performance_metrics.total_estimated_hours.toLocaleString() }}</p>
                        <p class="mt-1 text-xs text-muted-foreground">Actual: {{ performance_metrics.total_actual_hours.toLocaleString() }}h</p>
                    </div>
                    <div class="rounded-2xl bg-muted/40 p-4">
                        <p class="text-xs text-muted-foreground">Avg. Duration</p>
                        <p class="mt-2 text-2xl font-semibold">{{ performance_metrics.average_project_duration }}</p>
                        <p class="mt-1 text-xs text-muted-foreground">days per project</p>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-between gap-4">
                    <Link
                        :href="projectsIndex.url()"
                        class="rounded-full border border-input px-4 py-2 text-xs font-medium"
                    >
                        Manage projects
                    </Link>
                    <Link
                        :href="customersIndex.url()"
                        class="rounded-full border border-input px-4 py-2 text-xs font-medium"
                    >
                        Manage customers
                    </Link>
                </div>
            </section>
        </div>
    </div>
</template>
