<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

// Icons
import {
  IconUsers,
  IconFolders,
  IconDownload,
  IconMail,
  IconTrendingUp,
  IconTrendingDown,
  IconClock,
  IconAlertTriangle,
  IconCheck,
  IconActivity,
  IconPlus,
  IconEye,
  IconCalendar,
  IconTarget,
  IconCurrencyDollar,
  IconChartLine,
  IconRefresh,
  IconBell,
  IconX,
  IconArrowRight,
  IconChartBar,
  IconChartPie
} from '@tabler/icons-vue';
import Navheader from "@/components/backend/Navheader.vue";
import ContactActionMenu from "@/components/contact/ContactActionMenu.vue";

// Types
interface Stats {
  customers_count: number;
  projects_count: number;
  downloads_count: number;
  subscribers_count: number;
}

interface ProjectStats {
  total_projects: number;
  active_projects: number;
  completed_projects: number;
  featured_projects: number;
  public_projects: number;
  overdue_projects: number;
  projects_by_status: Record<string, number>;
  projects_by_production_type: Record<string, number>;
  average_project_duration: number;
  total_estimated_hours: number;
  total_actual_hours: number;
  total_budget: number;
}

interface CustomerStats {
  total_customers: number;
  active_customers: number;
  customers_with_projects: number;
  customers_by_status: Record<string, number>;
  top_customers_by_projects: Array<{
    first_name: string;
    last_name: string;
    company_name: string;
    projects_count: number;
  }>;
}

interface RecentActivity {
  customers: Array<{
    uuid: string;
    name: string;
    company: string;
    status: string;
    created_at: string;
    avatar: string;
  }>;
  projects: Array<{
    uuid: string;
    name: string;
    status: string;
    priority: string;
    progress: number;
    customer: {
      name: string;
      company: string;
    };
    created_at: string;
    status_color: string;
    priority_color: string;
  }>;
  timeline: Array<{
    type: string;
    title: string;
    subtitle: string;
    time: string;
    icon: string;
    color: string;
  }>;
}

interface Trends {
  daily: Array<{
    date: string;
    label: string;
    projects: number;
    customers: number;
    subscribers: number;
  }>;
  monthly_comparison: {
    current: Record<string, number>;
    previous: Record<string, number>;
    changes: Record<string, number>;
  };
}

interface ProjectInsights {
  status_distribution: Record<string, number>;
  priority_distribution: Record<string, number>;
  overdue_projects: number;
  upcoming_deadlines: Array<{
    uuid: string;
    name: string;
    end_date: string;
    days_left: number;
    customer: string;
    priority: string;
    priority_color: string;
  }>;
}

interface PerformanceMetrics {
  total_estimated_hours: number;
  total_actual_hours: number;
  total_budget: number;
  hours_variance: number;
  efficiency_rate: number;
  average_project_duration: number;
  completion_rate: number;
}

interface QuickAction {
  title: string;
  description: string;
  route: string;
  icon: string;
  color: string;
}

interface Notification {
  type: string;
  title: string;
  message: string;
  action: string;
  action_text: string;
}

const props = defineProps<{
  stats: Stats;
  project_stats: ProjectStats;
  customer_stats: CustomerStats;
  recent_activity: RecentActivity;
  trends: Trends;
  project_insights: ProjectInsights;
  performance_metrics: PerformanceMetrics;
  quick_actions: QuickAction[];
  notifications: Notification[];
}>();

defineOptions({
  layout: AuthLayout
});

// Reactive state
const isRefreshing = ref(false);
const showNotifications = ref(false);

// Chart configurations
const trendChartOptions = computed(() => ({
  chart: {
    type: 'area',
    height: 350,
    toolbar: { show: false },
    background: 'transparent',
  },
  theme: {
    mode: 'light',
  },
  dataLabels: { enabled: false },
  stroke: {
    curve: 'smooth',
    width: 2,
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.7,
      opacityTo: 0.1,
    },
  },
  xaxis: {
    categories: props.trends.daily.map(d => d.label),
    labels: {
      style: {
        colors: '#6B7280',
        fontSize: '12px',
      },
    },
  },
  yaxis: {
    labels: {
      style: {
        colors: '#6B7280',
        fontSize: '12px',
      },
    },
  },
  grid: {
    borderColor: '#E5E7EB',
    strokeDashArray: 4,
  },
  legend: {
    position: 'top',
    horizontalAlign: 'right',
  },
  colors: ['#3B82F6', '#10B981', '#F59E0B'],
}));

const trendChartSeries = computed(() => [
  {
    name: 'Projects',
    data: props.trends.daily.map(d => d.projects),
  },
  {
    name: 'Customers',
    data: props.trends.daily.map(d => d.customers),
  },
  {
    name: 'Subscribers',
    data: props.trends.daily.map(d => d.subscribers),
  },
]);

const statusChartOptions = computed(() => ({
  chart: {
    type: 'donut',
    height: 300,
  },
  labels: Object.keys(props.project_insights.status_distribution).map(status =>
    status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
  ),
  colors: ['#6B7280', '#3B82F6', '#F59E0B', '#10B981', '#EF4444'],
  legend: {
    position: 'bottom',
  },
  plotOptions: {
    pie: {
      donut: {
        size: '70%',
      },
    },
  },
  dataLabels: {
    enabled: true,
    formatter: function (val: number) {
      return Math.round(val) + '%';
    },
  },
}));

const statusChartSeries = computed(() =>
  Object.values(props.project_insights.status_distribution)
);

const priorityChartOptions = computed(() => ({
  chart: {
    type: 'bar',
    height: 300,
    toolbar: { show: false },
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '55%',
      borderRadius: 4,
    },
  },
  dataLabels: { enabled: false },
  xaxis: {
    categories: Object.keys(props.project_insights.priority_distribution).map(priority =>
      priority.charAt(0).toUpperCase() + priority.slice(1)
    ),
  },
  colors: ['#10B981', '#F59E0B', '#F97316', '#EF4444'],
  grid: {
    borderColor: '#E5E7EB',
  },
}));

const priorityChartSeries = computed(() => [{
  name: 'Projects',
  data: Object.values(props.project_insights.priority_distribution),
}]);

// Utility functions
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount);
};

const formatHours = (hours: number) => {
  return `${Math.round(hours)}h`;
};

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    'not_started': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    'on_hold': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
    'completed': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
    'active': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    'inactive': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'prospect': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
  };
  return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const getPriorityColor = (priority: string) => {
  const colors: Record<string, string> = {
    'low': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    'medium': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
    'high': 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
    'urgent': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
  };
  return colors[priority] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const getIconColor = (color: string) => {
  const colors: Record<string, string> = {
    'blue': 'text-blue-600 dark:text-blue-400',
    'green': 'text-green-600 dark:text-green-400',
    'purple': 'text-purple-600 dark:text-purple-400',
    'orange': 'text-orange-600 dark:text-orange-400',
    'red': 'text-red-600 dark:text-red-400',
    'yellow': 'text-yellow-600 dark:text-yellow-400',
  };
  return colors[color] || 'text-gray-600 dark:text-gray-400';
};

const getNotificationColor = (type: string) => {
  const colors: Record<string, string> = {
    'warning': 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-200',
    'info': 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-200',
    'success': 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-200',
    'error': 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-200',
  };
  return colors[type] || 'bg-gray-50 border-gray-200 text-gray-800 dark:bg-gray-900/20 dark:border-gray-800 dark:text-gray-200';
};

// Actions
const refreshDashboard = async () => {
  isRefreshing.value = true;
  try {
    await router.post(route('admin.dashboard.refresh'));
  } finally {
    isRefreshing.value = false;
  }
};

const dismissNotification = (index: number) => {
  // In a real app, you'd send this to the backend
  props.notifications.splice(index, 1);
  if (props.notifications.length === 0) {
    showNotifications.value = false;
  }
};
</script>

<template>
  <Head title="Dashboard" />

  <Navheader>

    <h2 class="text-xl font-semibold dark:text-gray-300 sm:inline-block">
      Explore Modules
    </h2>

    <!-- Notifications -->
    <div class="relative" v-if="notifications.length > 0">
      <button
        @click="showNotifications = !showNotifications"
        class="relative p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
        <IconBell class="w-6 h-6" />
        <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
      </button>

      <!-- Notifications Dropdown -->
      <div
        v-if="showNotifications"
        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-sm font-medium text-gray-900 dark:text-white">Notifications</h3>
        </div>
        <div class="max-h-64 overflow-y-auto">
          <div
            v-for="(notification, index) in notifications"
            :key="index"
            :class="['p-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0', getNotificationColor(notification.type)]">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <p class="text-sm font-medium">{{ notification.title }}</p>
                <p class="text-xs mt-1">{{ notification.message }}</p>
                <Link
                  :href="route(notification.action)"
                  class="text-xs font-medium hover:underline mt-2 inline-block">
                  {{ notification.action_text }}
                </Link>
              </div>
              <button
                @click="dismissNotification(index)"
                class="ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <IconX class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </Navheader>

  <div class="max-w-4xl px-4 py-12 mx-auto space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Customers -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center">
          <div class="shrink-0">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
              <IconUsers class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
          </div>
          <div class="ml-4 flex-1">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Customers</p>
            <div class="flex items-baseline">
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.customers_count }}</p>
              <span
                v-if="trends.monthly_comparison.changes.customers !== 0"
                :class="[
                  'ml-2 text-sm font-medium flex items-center',
                  trends.monthly_comparison.changes.customers > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                ]">
                <IconTrendingUp v-if="trends.monthly_comparison.changes.customers > 0" class="w-4 h-4 mr-1" />
                <IconTrendingDown v-else class="w-4 h-4 mr-1" />
                {{ Math.abs(trends.monthly_comparison.changes.customers) }}%
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Projects -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center">
          <div class="shrink-0">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <IconFolders class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
          </div>
          <div class="ml-4 flex-1">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Projects</p>
            <div class="flex items-baseline">
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.projects_count }}</p>
              <span
                v-if="trends.monthly_comparison.changes.projects !== 0"
                :class="[
                  'ml-2 text-sm font-medium flex items-center',
                  trends.monthly_comparison.changes.projects > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                ]">
                <IconTrendingUp v-if="trends.monthly_comparison.changes.projects > 0" class="w-4 h-4 mr-1" />
                <IconTrendingDown v-else class="w-4 h-4 mr-1" />
                {{ Math.abs(trends.monthly_comparison.changes.projects) }}%
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Downloads -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center">
          <div class="shrink-0">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
              <IconDownload class="w-6 h-6 text-purple-600 dark:text-purple-400" />
            </div>
          </div>
          <div class="ml-4 flex-1">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Downloads</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.downloads_count }}</p>
          </div>
        </div>
      </div>

      <!-- Subscribers -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center">
          <div class="shrink-0">
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
              <IconMail class="w-6 h-6 text-orange-600 dark:text-orange-400" />
            </div>
          </div>
          <div class="ml-4 flex-1">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Subscribers</p>
            <div class="flex items-baseline">
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.subscribers_count }}</p>
              <span
                v-if="trends.monthly_comparison.changes.subscribers !== 0"
                :class="[
                  'ml-2 text-sm font-medium flex items-center',
                  trends.monthly_comparison.changes.subscribers > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                ]">
                <IconTrendingUp v-if="trends.monthly_comparison.changes.subscribers > 0" class="w-4 h-4 mr-1" />
                <IconTrendingDown v-else class="w-4 h-4 mr-1" />
                {{ Math.abs(trends.monthly_comparison.changes.subscribers) }}%
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Trends Chart -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <IconChartLine class="w-5 h-5 mr-2" />
            30-Day Trends
          </h3>
        </div>
        <VueApexCharts
          type="area"
          height="350"
          :options="trendChartOptions"
          :series="trendChartSeries"
        />
      </div>

      <!-- Project Status Distribution -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <IconChartPie class="w-5 h-5 mr-2" />
            Project Status Distribution
          </h3>
        </div>
        <VueApexCharts
          type="donut"
          height="300"
          :options="statusChartOptions"
          :series="statusChartSeries"
        />
      </div>
    </div>

    <!-- Performance Metrics & Priority Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Project Performance -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
          <IconTarget class="w-5 h-5 mr-2" />
          Performance
        </h3>
        <div class="space-y-6">
          <div>
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm text-gray-600 dark:text-gray-400">Completion Rate</span>
              <span class="text-sm font-medium text-gray-900 dark:text-white">{{ performance_metrics.completion_rate }}%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div
                class="bg-green-600 h-2 rounded-full transition-all duration-300"
                :style="{ width: `${performance_metrics.completion_rate}%` }"
              ></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm text-gray-600 dark:text-gray-400">Efficiency Rate</span>
              <span class="text-sm font-medium text-gray-900 dark:text-white">{{ performance_metrics.efficiency_rate }}%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div
                class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                :style="{ width: `${Math.min(performance_metrics.efficiency_rate, 100)}%` }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Time & Budget -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
          <IconCurrencyDollar class="w-5 h-5 mr-2" />
          Resources
        </h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div class="flex items-center">
              <IconClock class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" />
              <span class="text-sm text-gray-600 dark:text-gray-400">Total Hours</span>
            </div>
            <span class="text-sm font-medium text-gray-900 dark:text-white">
              {{ formatHours(performance_metrics.total_actual_hours) }}
            </span>
          </div>

          <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div class="flex items-center">
              <IconCurrencyDollar class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" />
              <span class="text-sm text-gray-600 dark:text-gray-400">Total Budget</span>
            </div>
            <span class="text-sm font-medium text-gray-900 dark:text-white">
              {{ formatCurrency(performance_metrics.total_budget) }}
            </span>
          </div>

          <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div class="flex items-center">
              <IconCalendar class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-3" />
              <span class="text-sm text-gray-600 dark:text-gray-400">Avg Duration</span>
            </div>
            <span class="text-sm font-medium text-gray-900 dark:text-white">
              {{ Math.round(performance_metrics.average_project_duration) }} days
            </span>
          </div>
        </div>
      </div>

      <!-- Priority Distribution Chart -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <IconChartBar class="w-5 h-5 mr-2" />
            Priority Distribution
          </h3>
        </div>
        <VueApexCharts
          type="bar"
          height="250"
          :options="priorityChartOptions"
          :series="priorityChartSeries"
        />
      </div>
    </div>

    <!-- Alerts -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
        <IconAlertTriangle class="w-5 h-5 mr-2" />
        Alerts & Notifications
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div v-if="project_insights.overdue_projects > 0" class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
          <div class="flex items-center">
            <IconAlertTriangle class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" />
            <div>
              <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ project_insights.overdue_projects }} Overdue Projects</p>
              <p class="text-xs text-red-600 dark:text-red-400">Require immediate attention</p>
            </div>
          </div>
        </div>

        <div v-if="project_insights.upcoming_deadlines.length > 0" class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
          <div class="flex items-center">
            <IconCalendar class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3" />
            <div>
              <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ project_insights.upcoming_deadlines.length }} Upcoming Deadlines</p>
              <p class="text-xs text-yellow-600 dark:text-yellow-400">Due within 7 days</p>
            </div>
          </div>
        </div>

        <div v-if="project_stats.active_projects > 0" class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
          <div class="flex items-center">
            <IconCheck class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" />
            <div>
              <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ project_stats.active_projects }} Active Projects</p>
              <p class="text-xs text-green-600 dark:text-green-400">In progress</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent Activity -->
      <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <IconActivity class="w-5 h-5 mr-2" />
            Recent Activity
          </h3>
          <Link
            :href="route('admin.projects.index')"
            class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 flex items-center">
            View all
            <IconArrowRight class="w-4 h-4 ml-1" />
          </Link>
        </div>

        <div class="space-y-4">
          <div
            v-for="activity in recent_activity.timeline.slice(0, 6)"
            :key="`${activity.type}-${activity.title}`"
            class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <div :class="[
              'shrink-0 w-10 h-10 rounded-full flex items-center justify-center',
              activity.color === 'blue' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400'
            ]">
              <IconActivity class="w-5 h-5" />
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ activity.title }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ activity.subtitle }}</p>
            </div>
            <div class="shrink-0 text-xs text-gray-500 dark:text-gray-400">
              {{ activity.time }}
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Quick Actions</h3>
        <div class="space-y-3">
          <Link
            v-for="action in quick_actions"
            :key="action.route"
            :href="route(action.route)"
            class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
            <div :class="[
              'shrink-0 w-10 h-10 rounded-lg flex items-center justify-center mr-3 transition-colors',
              `bg-${action.color}-100 dark:bg-${action.color}-900/30`,
              getIconColor(action.color)
            ]">
              <IconPlus v-if="action.icon === 'folder-plus' || action.icon === 'user-plus'" class="w-5 h-5" />
              <IconFolders v-else-if="action.icon === 'folders'" class="w-5 h-5" />
              <IconUsers v-else-if="action.icon === 'users'" class="w-5 h-5" />
              <IconEye v-else class="w-5 h-5" />
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ action.title }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ action.description }}</p>
            </div>
            <IconArrowRight class="w-4 h-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" />
          </Link>
        </div>
      </div>
    </div>

    <!-- Recent Projects & Customers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Projects -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Projects</h3>
          <Link
            :href="route('admin.projects.index')"
            class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 flex items-center">
            View all
            <IconArrowRight class="w-4 h-4 ml-1" />
          </Link>
        </div>

        <div class="space-y-4">
          <div
            v-for="project in recent_activity.projects"
            :key="project.uuid"
            class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ project.name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ project.customer.name }}</p>
              <div class="flex items-center space-x-2 mt-2">
                <span :class="['px-2 py-1 text-xs rounded-full', getStatusColor(project.status)]">
                  {{ project.status.replace('_', ' ') }}
                </span>
                <span :class="['px-2 py-1 text-xs rounded-full', getPriorityColor(project.priority)]">
                  {{ project.priority }}
                </span>
              </div>
            </div>
            <div class="shrink-0 ml-4">
              <div class="w-16 h-2 bg-gray-200 dark:bg-gray-700 rounded-full">
                <div
                  class="h-2 bg-blue-600 rounded-full transition-all duration-300"
                  :style="{ width: `${project.progress}%` }"
                ></div>
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">{{ project.progress }}%</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Customers -->
      <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Customers</h3>
          <Link
            :href="route('admin.customers.index')"
            class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 flex items-center">
            View all
            <IconArrowRight class="w-4 h-4 ml-1" />
          </Link>
        </div>

        <div class="space-y-4">
          <div
            v-for="customer in recent_activity.customers"
            :key="customer.uuid"
            class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            <div class="shrink-0 w-10 h-10 bg-blue-600 dark:bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
              {{ customer.avatar }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ customer.name }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ customer.company }}</p>
            </div>
            <div class="shrink-0">
              <span :class="['px-2 py-1 text-xs rounded-full', getStatusColor(customer.status)]">
                {{ customer.status }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar for notifications */
.max-h-64::-webkit-scrollbar {
  width: 4px;
}

.max-h-64::-webkit-scrollbar-track {
  background: transparent;
}

.max-h-64::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 2px;
}

.max-h-64::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Dark mode scrollbar */
.dark .max-h-64::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.dark .max-h-64::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>
