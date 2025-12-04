<script setup lang="ts">
import {Head, Link, router} from '@inertiajs/vue3';
import {ref, computed} from 'vue';
import AuthLayout from "@/layouts/AuthLayout.vue";
import {
  Plus,
  Search,
  Filter,
  Grid3X3,
  List,
  Eye,
  Edit,
  Trash2,
  MoreVertical,
  Calendar,
  User,
  Tag,
  Star,
  Clock,
  TrendingUp,
  FolderOpen
} from 'lucide-vue-next';
import {Project, Customer} from "@/types";
import {useStorage} from "@vueuse/core";
import {cn} from '@/lib/utils';
import {Input} from '@/components/ui/input'
import {DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger} from "@/components/ui/dropdown-menu";
import {Select, SelectContent, SelectItem, SelectTrigger, SelectValue} from "@/components/ui/select";

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

interface ProjectFilters {
  search?: string;
  status?: string;
  production_type?: string;
  category?: string;
  priority?: string;
  customer_id?: number;
  featured?: boolean;
  start_date?: string;
  end_date?: string;
  sort_by?: string;
  sort_direction?: string;
}

const props = defineProps<{
  projects: {
    data: Project[];
    links: any;
    meta: any;
  };
  filters: ProjectFilters;
  customers: Customer[];
  productionTypes: string[];
  categories: string[];
  stats: ProjectStats;
  can?: {
    create_project?: boolean;
  };
}>();

defineOptions({
  layout: AuthLayout
});

// Local state
const viewMode = useStorage<'grid' | 'list'>('projects_view', 'grid');
const searchQuery = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');
const selectedCategory = ref(props.filters.category || '');

// Computed properties
const statusOptions = [
  {value: null, label: 'All Status'},
  {value: 'not_started', label: 'Not Started'},
  {value: 'in_progress', label: 'In Progress'},
  {value: 'on_hold', label: 'On Hold'},
  {value: 'completed', label: 'Completed'},
  {value: 'cancelled', label: 'Cancelled'}
];

const getStatusColor = (status: string) => {
  const colors = {
    'not_started': 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
    'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'on_hold': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
  };
  return colors[status as keyof typeof colors] || colors.not_started;
};

const getPriorityColor = (priority: string) => {
  const colors = {
    'low': 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
    'medium': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'high': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
    'urgent': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
  };
  return colors[priority as keyof typeof colors] || colors.low;
};

// Methods
const handleSearch = () => {
  router.get(route('admin.projects.index'), {
    ...props.filters,
    search: searchQuery.value,
    page: 1
  }, {
    preserveState: true,
    replace: true
  });
};

const handleFilter = () => {
  router.get(route('admin.projects.index'), {
    ...props.filters,
    status: selectedStatus.value,
    category: selectedCategory.value,
    page: 1
  }, {
    preserveState: true,
    replace: true
  });
};
</script>

<template>
  <Head title="Projects"/>

  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
      <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Projects</h1>
            <div class="ml-4 flex items-center space-x-2">
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                {{ stats.total_projects }} total
              </span>
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                {{ stats.active_projects }} active
              </span>
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <!-- View Toggle -->
            <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
              <button
                @click="viewMode = 'grid'"
                :class="cn(
                  'p-1.5 rounded-md transition-colors',
                  viewMode === 'grid'
                    ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm'
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                )"
              >
                <Grid3X3 class="h-4 w-4"/>
              </button>
              <button
                @click="viewMode = 'list'"
                :class="cn(
                  'p-1.5 rounded-md transition-colors',
                  viewMode === 'list'
                    ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm'
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                )"
              >
                <List class="h-4 w-4"/>
              </button>
            </div>

            <!-- Add Project Button -->
            <Link
              v-if="can?.create_project"
              :href="route('admin.projects.create')"
              class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
            >
              <Plus class="h-4 w-4 mr-2"/>
              New Project
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="px-4 sm:px-6 lg:px-8 py-6 max-w-4xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center">
            <div class="shrink-0">
              <TrendingUp class="h-8 w-8 text-blue-600"/>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Projects</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.total_projects }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center">
            <div class="shrink-0">
              <Clock class="h-8 w-8 text-green-600"/>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.active_projects }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center">
            <div class="shrink-0">
              <Star class="h-8 w-8 text-yellow-600"/>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Featured</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.featured_projects }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center">
            <div class="shrink-0">
              <Calendar class="h-8 w-8 text-red-600"/>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Overdue</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stats.overdue_projects }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
          <!-- Search -->
          <div class="flex-1">
            <div class="relative">
              <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400"/>
              <Input
                v-model="searchQuery"
                @update:model-value="handleSearch"
                type="text"
                placeholder="Search projects..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>

          <!-- Status Filter -->
          <div class="sm:w-48">
            <Select
              v-model="selectedStatus"
              @update:model-value="handleFilter">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Filter by status" />
              </SelectTrigger>

              <SelectContent>
                <SelectItem
                  v-for="option in statusOptions"
                  :key="option.value" :value="option.value">
                  {{ option.label }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Category Filter -->
          <div class="sm:w-48">
            <Select
              v-model="selectedCategory"
              @update:model-value="handleFilter">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Filter by category" />
              </SelectTrigger>

              <SelectContent>
                <SelectItem :value="null">
                  All Categories
                </SelectItem>

                <SelectItem
                  v-for="category in categories"
                  :key="category" :value="category">
                  {{ category }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
      </div>

      <!-- Projects Content -->
      <div v-if="!projects.data.length" class="text-center py-12">
        <div class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-500 flex items-center justify-center">
          <FolderOpen class="h-24 w-24"/>
        </div>
        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No projects found</h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          Get started by creating your first project.
        </p>
        <div class="mt-6">
          <Link
            v-if="can?.create_project"
            :href="route('admin.projects.create')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
          >
            <Plus class="h-4 w-4 mr-2"/>
            New Project
          </Link>
        </div>
      </div>

      <!-- Grid View -->
      <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="project in projects.data"
          :key="project.uuid"
          class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-200"
        >
          <!-- Project Image -->
          <div class="aspect-video bg-gray-100 dark:bg-gray-700 relative group">
            <img
              v-if="project.poster_url"
              :src="project.poster_url"
              :alt="project.name"
              class="w-full h-full object-cover"
            />
            <div v-else class="flex items-center justify-center h-full">
              <Tag class="h-12 w-12 text-gray-400"/>
            </div>

            <!-- Actions Overlay -->
            <div
              class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center space-x-2">
              <Link
                :href="route('admin.projects.show', project.uuid)"
                class="p-2 bg-white rounded-full text-gray-700 hover:bg-gray-100 transition-colors"
              >
                <Eye class="h-4 w-4"/>
              </Link>

              <Link
                :href="route('admin.projects.edit', project.uuid)"
                class="p-2 bg-white rounded-full text-gray-700 hover:bg-gray-100 transition-colors"
              >
                <Edit class="h-4 w-4"/>
              </Link>
            </div>

            <!-- Featured Badge -->
            <div v-if="project.is_featured" class="absolute top-2 left-2">
              <span
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                <Star class="h-3 w-3 mr-1"/>
                Featured
              </span>
            </div>
          </div>

          <!-- Project Info -->
          <div class="p-4">
            <div class="flex items-start justify-between mb-2">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                {{ project.name }}
              </h3>

              <DropdownMenu>
                <DropdownMenuTrigger>
                  <MoreVertical class="h-4 w-4"/>
                </DropdownMenuTrigger>

                <DropdownMenuContent align="end">
                  <DropdownMenuItem as-child>
                    <Link
                      :href="route('admin.projects.show', project.uuid)">
                      <Eye />
                      View Details
                    </Link>
                  </DropdownMenuItem>

                  <DropdownMenuItem as-child>
                    <Link
                      :href="route('admin.projects.edit', project.uuid)">
                      <Edit />
                      Edit Project
                    </Link>
                  </DropdownMenuItem>

                  <DropdownMenuItem as-child>
                    <Link
                      :href="route('admin.projects.destroy', project.uuid)"
                      method="delete"
                      as="button">
                      <Trash2 />
                      Delete Project
                    </Link>
                  </DropdownMenuItem>
                </DropdownMenuContent>

              </DropdownMenu>
            </div>

            <p v-if="project.short_description" class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
              {{ project.short_description }}
            </p>

            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <User class="h-4 w-4 mr-1"/>
                <span>{{ project.customer?.first_name }} {{ project.customer?.last_name }}</span>
              </div>
              <span v-if="project.status"
                    :class="cn('inline-flex items-center px-2 py-1 rounded-full text-xs font-medium', getStatusColor(project.status))">
                {{ project.status?.replace('_', ' ') }}
              </span>
            </div>

            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
              <span v-if="project.category">{{ project.category }}</span>
              <span v-if="project.priority"
                    :class="cn('inline-flex items-center px-2 py-1 rounded-full text-xs font-medium', getPriorityColor(project.priority))">
                {{ project.priority }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- List View -->
      <div v-else
           class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Project
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Client
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Priority
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Category
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Created
              </th>
              <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="project in projects.data" :key="project.uuid" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="shrink-0 h-10 w-10">
                    <img v-if="project.poster_url" class="h-10 w-10 rounded-lg object-cover" :src="project.poster_url"
                         :alt="project.name"/>
                    <div v-else
                         class="h-10 w-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                      <Tag class="h-5 w-5 text-gray-400"/>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ project.name }}</div>
                    <div v-if="project.short_description"
                         class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">{{
                        project.short_description
                      }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ project.customer?.first_name }} {{ project.customer?.last_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="project.status"
                        :class="cn('inline-flex items-center px-2 py-1 rounded-full text-xs font-medium', getStatusColor(project.status))">
                    {{ project.status?.replace('_', ' ') }}
                  </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="project.priority"
                        :class="cn('inline-flex items-center px-2 py-1 rounded-full text-xs font-medium', getPriorityColor(project.priority))">
                    {{ project.priority }}
                  </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ project.category || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ new Date(project.created_at || '').toLocaleDateString() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <DropdownMenu>
                  <DropdownMenuTrigger class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <MoreVertical />
                  </DropdownMenuTrigger>

                  <DropdownMenuContent>
                    <DropdownMenuItem as-child>
                      <Link
                        :href="route('admin.projects.show', project.uuid)">
                        <Eye />
                        View Details
                      </Link>
                    </DropdownMenuItem>

                    <DropdownMenuItem as-child>
                      <Link
                        :href="route('admin.projects.edit', project.uuid)">
                        <Edit />
                        Edit Project
                      </Link>
                    </DropdownMenuItem>

                    <DropdownMenuItem as-child>
                      <Link
                        :href="route('admin.projects.destroy', project.uuid)"
                        method="delete"
                        as="button">
                        <Trash2 />
                        Delete Project
                      </Link>
                    </DropdownMenuItem>
                  </DropdownMenuContent>

                </DropdownMenu>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="projects.meta && projects.meta.last_page > 1" class="mt-6 flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
          <Link
            v-if="projects.meta.current_page > 1"
            :href="projects.links.prev"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            Previous
          </Link>
          <Link
            v-if="projects.meta.current_page < projects.meta.last_page"
            :href="projects.links.next"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            Next
          </Link>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700 dark:text-gray-300">
              Showing
              <span class="font-medium">{{ projects.meta.from }}</span>
              to
              <span class="font-medium">{{ projects.meta.to }}</span>
              of
              <span class="font-medium">{{ projects.meta.total }}</span>
              results
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <Link
                v-if="projects.meta.current_page > 1"
                :href="projects.links.prev"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                Previous
              </Link>
              <Link
                v-if="projects.meta.current_page < projects.meta.last_page"
                :href="projects.links.next"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                Next
              </Link>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

