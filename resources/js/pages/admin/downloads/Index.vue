<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, computed, onMounted, nextTick, watch } from "vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import { gsap } from "gsap";

// Icons
import {
  Search,
  Filter,
  Grid3X3,
  List,
  Plus,
  Star,
  Download as DownloadIcon,
  Eye,
  EyeOff,
  Edit,
  Trash2,
  MoreHorizontal,
  FileText,
  TrendingUp,
  Users,
  Calendar,
  ArrowUpDown,
  X,
  Pencil,
  AlertCircle,
  Loader2,
  RefreshCw,
  SortAsc,
  SortDesc
} from "lucide-vue-next";

// UI Components
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Checkbox } from "@/components/ui/checkbox";
import { Progress } from "@/components/ui/progress";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
  DropdownMenuRadioGroup,
  DropdownMenuRadioItem,
} from "@/components/ui/dropdown-menu";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { useStorage } from "@vueuse/core";

interface Download {
  uuid: string;
  id: number;
  title: string;
  description: string | null;
  brand: string | null;
  category: string | null;
  is_featured: boolean;
  is_public: boolean;
  download_count: number;
  file_size: number | null;
  formatted_file_size: string;
  file_extension: string | null;
  file_type: string | null;
  poster_url: string | null;
  thumb_url: string | null;
  created_at: string;
  updated_at: string;
}

interface Props {
  downloads: {
    data: Download[];
    links: any[];
    from: number;
    to: number;
    total: number;
    last_page: number;
  };
  filters: {
    search: string;
    category: string | null;
    file_type: string | null;
    sort_by: string;
    sort_direction: string;
  };
  options: {
    categories: string[];
    file_types: string[];
  };
  stats: {
    total_downloads: number;
    public_downloads: number;
    featured_downloads: number;
    total_download_count: number;
    avg_downloads_per_item: number;
    categories_count: number;
    file_types_count: number;
    growth_rate: string;
  };
}

const props = defineProps<Props>();

defineOptions({
  layout: AuthLayout
});

// Reactive state
const searchQuery = ref(props.filters.search || '');
const viewMode = useStorage<'grid' | 'list'>('downloads_view_mode', 'grid');
const selectedDownloads = ref<number[]>([]);
const bulkActionLoading = ref(false);
const sortBy = ref(props.filters.sort_by || 'created_at');
const sortDirection = ref(props.filters.sort_direction || 'desc');

// Filter state
const filters = ref({
  category: props.filters.category,
  file_type: props.filters.file_type,
});

const downloadKind = ref('all');

// Refs for animations
const headerRef = ref<HTMLElement>();
const statsRef = ref<HTMLElement[]>([]);
const containerRef = ref<HTMLElement>();
const cardsRef = ref<HTMLElement[]>([]);

// Enhanced stats with computed values
const enhancedStats = computed(() => ({
  ...props.stats,
  completion_rate: Math.round((props.stats.public_downloads / props.stats.total_downloads) * 100),
  avg_file_size: '2.4 MB', // This would come from backend
}));

// Computed
const hasSelectedDownloads = computed(() => selectedDownloads.value.length > 0);

const allSelected = computed({
  get: () => selectedDownloads.value.length === props.downloads.data.length && props.downloads.data.length > 0,
  set: (value: boolean) => {
    if (value) {
      selectedDownloads.value = props.downloads.data.map(d => d.id);
    } else {
      selectedDownloads.value = [];
    }
  }
});

const someSelected = computed(() =>
  selectedDownloads.value.length > 0 && selectedDownloads.value.length < props.downloads.data.length
);

// File type color mapping
const getFileTypeColor = (fileType: string) => {
  const colors = {
    'pdf': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'doc': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'docx': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'xls': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'xlsx': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'ppt': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    'pptx': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    'zip': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    'rar': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    'jpg': 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
    'jpeg': 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
    'png': 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
    'gif': 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
    'mp4': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
    'mp3': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    'default': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
  };

  return colors[fileType?.toLowerCase() as keyof typeof colors] || colors.default;
};

// Selection methods
const toggleDownloadSelection = (downloadId: number) => {
  const index = selectedDownloads.value.indexOf(downloadId);
  if (index === -1) {
    selectedDownloads.value.push(downloadId);
  } else {
    selectedDownloads.value.splice(index, 1);
  }
};

const toggleAllSelection = () => {
  allSelected.value = !allSelected.value;
};

const clearSelection = () => {
  selectedDownloads.value = [];
};

// Filter methods
const updateFilter = (key: string, value: any) => {
  filters.value[key as keyof typeof filters.value] = value;
  applyFilters();
};

const clearFilters = () => {
  searchQuery.value = '';
  filters.value.category = null;
  filters.value.file_type = null;
  downloadKind.value = 'all';
  applyFilters();
};

const applyFilters = () => {
  const params = new URLSearchParams();

  if (searchQuery.value) params.set('search', searchQuery.value);
  if (filters.value.category) params.set('category', filters.value.category);
  if (filters.value.file_type) params.set('file_type', filters.value.file_type);
  if (sortBy.value) params.set('sort_by', sortBy.value);
  if (sortDirection.value) params.set('sort_direction', sortDirection.value);

  router.get(route('admin.downloads.index'), Object.fromEntries(params), {
    preserveState: true,
    preserveScroll: true,
  });
};

// Sorting
const updateSort = (field: string) => {
  if (sortBy.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortBy.value = field;
    sortDirection.value = 'desc';
  }
  applyFilters();
};

// Bulk actions
const performBulkAction = async (action: string) => {
  if (!hasSelectedDownloads.value) return;

  bulkActionLoading.value = true;

  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000));

    console.log(`Performing ${action} on ${selectedDownloads.value.length} downloads`);

    // Clear selection after successful action
    clearSelection();

    // Show success message
    showNotification(`${action} completed successfully!`, 'success');

  } catch (error) {
    showNotification(`Failed to ${action} downloads`, 'error');
  } finally {
    bulkActionLoading.value = false;
  }
};

// Notification system
const showNotification = (message: string, type: 'success' | 'error') => {
  console.log(`${type}: ${message}`);
};

// Animation functions
const animateHeader = () => {
  if (headerRef.value) {
    gsap.fromTo(headerRef.value,
      { y: -20, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.6, ease: "power2.out" }
    );
  }
};

const animateStats = () => {
  if (statsRef.value.length > 0) {
    gsap.fromTo(statsRef.value,
      { y: 20, opacity: 0, scale: 0.95 },
      {
        y: 0,
        opacity: 1,
        scale: 1,
        duration: 0.5,
        stagger: 0.1,
        ease: "back.out(1.7)"
      }
    );
  }
};

const animateCards = () => {
  if (cardsRef.value.length > 0) {
    gsap.fromTo(cardsRef.value,
      { y: 30, opacity: 0, scale: 0.95 },
      {
        y: 0,
        opacity: 1,
        scale: 1,
        duration: 0.6,
        stagger: 0.08,
        ease: "power2.out"
      }
    );
  }
};

// Watch for search changes
watch(searchQuery, () => {
  const timeoutId = setTimeout(() => {
    applyFilters();
  }, 300);

  return () => clearTimeout(timeoutId);
});

// Lifecycle
onMounted(() => {
  nextTick(() => {
    animateHeader();
    animateStats();
    animateCards();
  });
});

// Watch for view mode changes
watch(viewMode, () => {
  nextTick(() => {
    animateCards();
  });
});
</script>

<template>
  <Head title="Downloads Management" />

  <div class="min-h-screen bg-background">
    <!-- Enhanced Header -->
    <div ref="headerRef" class="bg-linear-to-r from-background via-background to-background/95 border-b backdrop-blur-xl sticky top-0 z-40">
      <div class="mx-auto max-w-4xl px-4 py-6">
        <div class="flex lg:flex-row lg:items-center justify-between gap-6">
          <!-- Title Section -->
          <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-linear-to-br from-primary/20 to-primary/10 ring-1 ring-primary/20">
              <DownloadIcon class="h-6 w-6 text-primary" />
            </div>

            <div>
              <h1 class="text-2xl font-bold bg-linear-to-r from-foreground to-foreground/70 bg-clip-text text-transparent">
                Downloads Management
              </h1>

              <p class="text-sm text-muted-foreground mt-1">
                Manage downloadable content and track performance
              </p>
            </div>
          </div>

          <!-- Action Button -->
          <Button
            @click="() => router.visit(route('admin.downloads.create'))"
            class="hidden md:flex" size="sm">
            <Plus class="w-4 h-4" />
            Add Download
          </Button>

          <Button
            @click="() => router.visit(route('admin.downloads.create'))"
            size="icon" class="flex md:hidden">
            <Plus class="w-4 h-4" />
          </Button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="mx-auto max-w-4xl px-4 py-8">
      <!-- Enhanced Stats Overview -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <Card
          ref="el => statsRef[0] = el as HTMLElement"
          class="relative overflow-hidden bg-linear-to-br from-blue-50 via-blue-50 to-blue-100 dark:from-blue-950 dark:via-blue-950 dark:to-blue-900 border-blue-200 dark:border-blue-800 hover:shadow-lg transition-all duration-300 group">
          <CardContent class="p-4">
            <div class="flex items-center justify-between">
              <div class="space-y-1">
                <p class="text-xs font-medium text-blue-900 dark:text-blue-100 uppercase tracking-wide">Total</p>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                  {{ enhancedStats.total_downloads }}
                </p>

                <p class="text-xs text-blue-700 dark:text-blue-300">
                  {{ enhancedStats.public_downloads }} public
                </p>
              </div>

              <div class="p-3 bg-blue-200 dark:bg-blue-800 rounded-xl group-hover:scale-110 transition-transform duration-300">
                <FileText class="w-5 h-5 text-blue-600 dark:text-blue-400" />
              </div>
            </div>

            <div class="absolute inset-0 bg-linear-to-r from-blue-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
          </CardContent>
        </Card>

        <Card
          ref="el => statsRef[1] = el as HTMLElement"
          class="relative overflow-hidden bg-linear-to-br from-green-50 via-green-50 to-green-100 dark:from-green-950 dark:via-green-950 dark:to-green-900 border-green-200 dark:border-green-800 hover:shadow-lg transition-all duration-300 group"
        >
          <CardContent class="p-4">
            <div class="flex items-center justify-between">
              <div class="space-y-1">
                <p class="text-xs font-medium text-green-900 dark:text-green-100 uppercase tracking-wide">Downloads</p>
                <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                  {{ enhancedStats.total_download_count.toLocaleString() }}
                </p>

                <p class="text-xs text-green-700 dark:text-green-300">
                  Avg: {{ enhancedStats.avg_downloads_per_item }}
                </p>
              </div>
              <div class="p-3 bg-green-200 dark:bg-green-800 rounded-xl group-hover:scale-110 transition-transform duration-300">
                <TrendingUp class="w-5 h-5 text-green-600 dark:text-green-400" />
              </div>
            </div>
            <div class="absolute inset-0 bg-linear-to-r from-green-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
          </CardContent>
        </Card>

        <Card
          ref="el => statsRef[2] = el as HTMLElement"
          class="relative overflow-hidden bg-linear-to-br from-purple-50 via-purple-50 to-purple-100 dark:from-purple-950 dark:via-purple-950 dark:to-purple-900 border-purple-200 dark:border-purple-800 hover:shadow-lg transition-all duration-300 group"
        >
          <CardContent class="p-4">
            <div class="flex items-center justify-between">
              <div class="space-y-1">
                <p class="text-xs font-medium text-purple-900 dark:text-purple-100 uppercase tracking-wide">Categories</p>
                <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                  {{ enhancedStats.categories_count }}
                </p>

                <p class="text-xs text-purple-700 dark:text-purple-300">
                  {{ enhancedStats.file_types_count }} file types
                </p>
              </div>

              <div class="p-3 bg-purple-200 dark:bg-purple-800 rounded-xl group-hover:scale-110 transition-transform duration-300">
                <Grid3X3 class="w-5 h-5 text-purple-600 dark:text-purple-400" />
              </div>
            </div>
            <div class="absolute inset-0 bg-linear-to-r from-purple-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
          </CardContent>
        </Card>

        <Card
          ref="el => statsRef[3] = el as HTMLElement"
          class="relative overflow-hidden bg-linear-to-br from-amber-50 via-amber-50 to-amber-100 dark:from-amber-950 dark:via-amber-950 dark:to-amber-900 border-amber-200 dark:border-amber-800 hover:shadow-lg transition-all duration-300 group"
        >
          <CardContent class="p-4">
            <div class="flex items-center justify-between">
              <div class="space-y-1">
                <p class="text-xs font-medium text-amber-900 dark:text-amber-100 uppercase tracking-wide">Featured</p>
                <p class="text-2xl font-bold text-amber-900 dark:text-amber-100">
                  {{ enhancedStats.featured_downloads }}
                </p>
                <p class="text-xs text-amber-700 dark:text-amber-300">
                  {{ enhancedStats.growth_rate }} growth
                </p>
              </div>
              <div class="p-3 bg-amber-200 dark:bg-amber-800 rounded-xl group-hover:scale-110 transition-transform duration-300">
                <Star class="w-5 h-5 text-amber-600 dark:text-amber-400" />
              </div>
            </div>
            <div class="absolute inset-0 bg-linear-to-r from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
          </CardContent>
        </Card>
      </div>

      <!-- Enhanced Filters and Controls -->
      <Card class="mb-6 bg-card/50 backdrop-blur-sm">
        <CardContent class="p-6">
          <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
            <!-- Search and Filters -->
            <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center flex-1 w-full lg:w-auto">
              <!-- Enhanced Search -->
              <div class="relative flex-1 max-w-md">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                <Input
                  v-model="searchQuery"
                  placeholder="Search downloads..."
                  class="pl-10 bg-background/80 backdrop-blur-sm focus:border-primary/50 transition-all duration-300 focus:ring-2 focus:ring-primary/20"
                />
                <Button
                  v-if="searchQuery"
                  variant="ghost"
                  size="sm"
                  class="absolute right-1 top-1/2 transform -translate-y-1/2 h-7 w-7 p-0"
                  @click="searchQuery = ''"
                >
                  <X class="w-3 h-3" />
                </Button>
              </div>

              <!-- Enhanced Filters -->
              <div class="flex gap-2 flex-wrap">
                <!-- Category Filter -->
                <Select :value="filters.category" @update:value="(value) => updateFilter('category', value)">
                  <SelectTrigger class="w-[140px] bg-background/80">
                    <SelectValue placeholder="Category" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem :value="null">All Categories</SelectItem>
                    <SelectItem v-for="category in options.categories" :key="category" :value="category">
                      {{ category }}
                    </SelectItem>
                  </SelectContent>
                </Select>

                <!-- File Type Filter -->
                <Select :value="filters.file_type" @update:value="(value) => updateFilter('file_type', value)">
                  <SelectTrigger class="w-[120px] bg-background/80">
                    <SelectValue placeholder="File Type" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem :value="null">All Types</SelectItem>
                    <SelectItem v-for="fileType in options.file_types" :key="fileType" :value="fileType">
                      {{ fileType.toUpperCase() }}
                    </SelectItem>
                  </SelectContent>
                </Select>

                <!-- Sort Dropdown -->
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="outline"  class="gap-2 bg-background/80">
                      <ArrowUpDown class="w-4 h-4" />
                      Sort
                      <SortAsc v-if="sortDirection === 'asc'" class="w-3 h-3" />
                      <SortDesc v-else class="w-3 h-3" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end" class="w-48">
                    <DropdownMenuItem @click="updateSort('title')">
                      Title
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="updateSort('download_count')">
                      Download Count
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="updateSort('created_at')">
                      Date Created
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="updateSort('file_size')">
                      File Size
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>

                <!-- Clear Filters -->
                <Button
                  v-if="searchQuery || filters.category || filters.file_type"
                  variant="ghost"
                  size="sm"
                  @click="clearFilters"
                  class="gap-2"
                >
                  <X class="w-4 h-4" />
                  Clear
                </Button>
              </div>
            </div>

            <!-- View Controls -->
            <div class="flex items-center gap-3 w-full lg:w-auto justify-between lg:justify-end">
              <!-- Bulk Actions -->
              <div v-if="hasSelectedDownloads" class="flex items-center gap-2">
                <span class="text-sm text-muted-foreground">
                  {{ selectedDownloads.length }} selected
                </span>

                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="outline" size="sm" :disabled="bulkActionLoading" class="gap-2">
                      <Loader2 v-if="bulkActionLoading" class="w-4 h-4 animate-spin" />
                      <MoreHorizontal v-else class="w-4 h-4" />
                      Actions
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem @click="performBulkAction('feature')">
                      <Star class="w-4 h-4 mr-2" />
                      Feature
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="performBulkAction('unfeature')">
                      <Star class="w-4 h-4 mr-2" />
                      Unfeature
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="performBulkAction('publish')">
                      <Eye class="w-4 h-4 mr-2" />
                      Publish
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="performBulkAction('unpublish')">
                      <EyeOff class="w-4 h-4 mr-2" />
                      Unpublish
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem
                      @click="performBulkAction('delete')"
                      class="text-destructive focus:text-destructive"
                    >
                      <Trash2 class="w-4 h-4 mr-2" />
                      Delete
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>

                <Button variant="ghost" size="sm" @click="clearSelection">
                  <X class="w-4 h-4" />
                </Button>
              </div>

              <!-- View Mode Toggle -->
              <div class="flex items-center gap-1 p-1 bg-muted/50 rounded-lg backdrop-blur-sm">
                <Button
                  variant="ghost"
                  size="sm"
                  :class="viewMode === 'grid' ? 'bg-background shadow-sm' : ''"
                  @click="viewMode = 'grid'"
                >
                  <Grid3X3 class="w-4 h-4" />
                </Button>

                <Button
                  variant="ghost"
                  size="sm"
                  :class="viewMode === 'list' ? 'bg-background shadow-sm' : ''"
                  @click="viewMode = 'list'"
                >
                  <List class="w-4 h-4" />
                </Button>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Downloads Grid/List -->
      <div v-if="downloads.data.length > 0" ref="containerRef" class="transition-all duration-300">
        <!-- Enhanced Grid View -->
        <div
          v-if="viewMode === 'grid'"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <Card
            v-for="(download, index) in downloads.data"
            :key="download.uuid"
            :ref="el => cardsRef[index] = el as HTMLElement"
            class="group hover:shadow-xl transition-all duration-300 cursor-pointer hover:border-primary/30 bg-card hover:bg-card/80 overflow-hidden relative">
            <!-- Selection Checkbox -->
            <div class="absolute top-3 left-3 z-10 opacity-0 group-hover:opacity-100 transition-all duration-300">
              <Checkbox
                :model-value="selectedDownloads.includes(download.id)"
                @update:model-value="() => toggleDownloadSelection(download.id)"
                class="bg-background/90 backdrop-blur-sm border-2 data-[state=checked]:bg-primary data-[state=checked]:border-primary"
                @click.stop
              />
            </div>

            <!-- Featured Badge -->
            <div v-if="download.is_featured" class="absolute top-3 right-3 z-10">
              <Badge class="bg-linear-to-r from-amber-500 to-amber-600 text-white border-0 shadow-lg">
                <Star class="w-3 h-3 mr-1 fill-current" />
                Featured
              </Badge>
            </div>

            <!-- Enhanced Image Section -->
            <div class="aspect-video relative overflow-hidden bg-linear-to-br from-muted/50 to-muted">
              <img
                v-if="download.poster_url"
                :src="download.thumb_url || download.poster_url"
                :alt="download.title"
                class="w-full h-full object-cover transition-all duration-500 group-hover:scale-110"
                loading="lazy"
              />
              <div
                v-else
                class="w-full h-full flex items-center justify-center bg-linear-to-br from-muted/80 to-muted"
              >
                <div class="text-center space-y-2">
                  <DownloadIcon class="w-12 h-12 text-muted-foreground mx-auto" />
                  <p class="text-xs text-muted-foreground font-medium">No Preview</p>
                </div>
              </div>

              <!-- Enhanced Overlay -->
              <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300" />

              <!-- File Type Badge -->
              <div class="absolute bottom-3 left-3">
                <Badge :class="getFileTypeColor(download.file_type || '')" class="font-mono text-xs font-semibold shadow-lg">
                  {{ download.file_extension?.toUpperCase() || 'FILE' }}
                </Badge>
              </div>

              <!-- File Size -->
              <div class="absolute bottom-3 right-3">
                <Badge variant="secondary" class="bg-background/90 backdrop-blur-sm text-xs font-medium shadow-lg">
                  {{ download.formatted_file_size }}
                </Badge>
              </div>

              <!-- Quick Actions Overlay -->
              <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                <div class="flex gap-2">
                  <Button
                    size="sm"
                    variant="secondary"
                    class="bg-background/90 backdrop-blur-sm hover:bg-background shadow-lg"
                    @click.stop="router.visit(route('admin.downloads.edit', download.uuid))">
                    <Pencil class="w-4 h-4" />
                  </Button>

                  <Button
                    size="sm"
                    variant="secondary"
                    class="bg-background/90 backdrop-blur-sm hover:bg-background shadow-lg"
                    @click.stop="router.visit(route('admin.downloads.show', download.uuid))">
                    <Eye class="w-4 h-4" />
                  </Button>
                </div>
              </div>
            </div>

            <!-- Enhanced Content -->
            <CardContent class="p-5 space-y-4 flex flex-col">
              <!-- Title and Brand -->
              <div class="space-y-2">
                <h3 class="font-semibold text-foreground group-hover:text-primary transition-colors duration-300 line-clamp-2 leading-tight">
                  {{ download.title }}
                </h3>

                <p v-if="download.brand" class="text-sm text-muted-foreground line-clamp-1 font-medium">
                  {{ download.brand }}
                </p>
              </div>

              <!-- Category and Stats -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <Badge v-if="download.category" variant="outline" class="text-xs">
                    {{ download.category }}
                  </Badge>

                  <Badge v-if="!download.is_public" variant="secondary" class="text-xs">
                    <EyeOff class="w-3 h-3 mr-1" />
                    Private
                  </Badge>
                </div>

                <div class="flex items-center gap-3 text-xs text-muted-foreground">
                  <span class="flex items-center gap-1">
                    <DownloadIcon class="w-3 h-3" />
                    {{ download.download_count }}
                  </span>
                  <span class="flex items-center gap-1">
                    <Calendar class="w-3 h-3" />
                    {{ new Date(download.created_at).toLocaleDateString() }}
                  </span>
                </div>
              </div>

              <div class="flex-1"></div>

              <!-- Progress Bar for Download Count -->
              <div class="space-y-1">
                <div class="flex justify-between text-xs">
                  <span class="text-muted-foreground">Downloads</span>
                  <span class="font-medium">{{ download.download_count }}</span>
                </div>
                <Progress
                  model-value="Math.min((download.download_count / 100) * 100, 100)"
                  class="h-1.5"
                />
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Enhanced List View -->
        <Card v-else class="bg-card overflow-hidden">
          <CardContent class="p-0">
            <!-- Enhanced Header -->
            <div class="flex items-center gap-4 p-4 border-b bg-muted/20">
              <Checkbox
                :model-value="allSelected"
                :indeterminate="someSelected"
                @update:model-value="toggleAllSelection"
                class="data-[state=checked]:bg-primary data-[state=checked]:border-primary"
              />
              <div class="flex-1 grid grid-cols-12 gap-4 text-sm font-medium text-muted-foreground">
                <div class="col-span-4">Download</div>
                <div class="col-span-2">Category</div>
                <div class="col-span-1">Type</div>
                <div class="col-span-2">Size</div>
<!--                <div class="col-span-2">Downloads</div>-->
                <div class="col-span-2">Status</div>
                <div class="col-span-1"></div>
              </div>
            </div>

            <!-- Enhanced List Items -->
            <div class="divide-y divide-border/50">
              <div
                v-for="(download, index) in downloads.data"
                :key="download.uuid"
                :ref="el => cardsRef[index] = el as HTMLElement"
                class="flex items-center gap-4 p-4 hover:bg-muted/30 transition-all duration-200 group"
              >
                <Checkbox
                  :model-value="selectedDownloads.includes(download.id)"
                  @update:model-value="() => toggleDownloadSelection(download.id)"
                  class="data-[state=checked]:bg-primary data-[state=checked]:border-primary"
                />

                <div class="flex-1 grid grid-cols-12 gap-4 items-center">
                  <!-- Enhanced Download Info -->
                  <div class="col-span-4 flex items-center gap-3">
                    <Avatar class="w-12 h-12 rounded-lg ring-2 ring-primary/10 group-hover:ring-primary/20 transition-all duration-300">
                      <AvatarImage :src="download.thumb_url" :alt="download.title" class="object-cover" />
                      <AvatarFallback class="rounded-lg bg-linear-to-br from-primary/10 to-primary/5 text-primary">
                        <DownloadIcon class="w-5 h-5" />
                      </AvatarFallback>
                    </Avatar>

                    <div class="min-w-0 space-y-1">
                      <h4 class="font-medium text-foreground group-hover:text-primary transition-colors truncate">
                        {{ download.title }}
                      </h4>

                      <p v-if="download.brand" class="text-sm text-muted-foreground truncate">
                        {{ download.brand }}
                      </p>

                      <div class="flex items-center gap-2">
                        <Badge v-if="download.is_featured" variant="default" class="text-xs">
                          <Star class="w-3 h-3 mr-1 fill-current" />
                          Featured
                        </Badge>
                      </div>
                    </div>
                  </div>

                  <!-- Category -->
                  <div class="col-span-2">
                    <Badge v-if="download.category" variant="outline" class="text-xs">
                      {{ download.category }}
                    </Badge>
                    <span v-else class="text-xs text-muted-foreground">—</span>
                  </div>

                  <!-- File Type -->
                  <div class="col-span-1">
                    <Badge :class="getFileTypeColor(download.file_type || '')" class="text-xs font-mono font-semibold">
                      {{ download.file_extension?.toUpperCase() || 'FILE' }}
                    </Badge>
                  </div>

                  <!-- File Size -->
                  <div class="col-span-2 text-sm font-medium text-muted-foreground">
                    {{ download.formatted_file_size }}
                  </div>

                  <!-- Download Count with Progress -->
<!--                  <div class="col-span-2 space-y-1">-->
<!--                    <div class="flex items-center justify-between">-->
<!--                      <span class="text-sm font-medium">{{ download.download_count }}</span>-->
<!--                      <span class="text-xs text-muted-foreground">downloads</span>-->
<!--                    </div>-->
<!--                    <Progress-->
<!--                      v-model="Math.min((download.download_count / 100) * 100, 100)"-->
<!--                      class="h-1"-->
<!--                    />-->
<!--                  </div>-->

                  <!-- Status -->
                  <div class="col-span-2">
                    <Badge
                      :variant="download.is_public ? 'default' : 'secondary'"
                      class="text-xs">
                      {{ download.is_public ? 'Public' : 'Private' }}
                    </Badge>
                  </div>

                  <!-- Actions -->
                  <div class="col-span-1 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <Button
                      variant="ghost"
                      size="icon"
                      @click="router.visit(route('admin.downloads.edit', download.uuid))">
                      <Pencil class="w-4 h-4" />
                    </Button>

                    <Button
                      variant="ghost"
                      size="icon"
                      @click="router.visit(route('admin.downloads.show', download.uuid))">
                      <Eye class="w-4 h-4" />
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Enhanced Empty State -->
      <div v-else class="text-center py-16">
        <div class="mx-auto w-24 h-24 bg-linear-to-br from-muted to-muted/50 rounded-full flex items-center justify-center mb-6 ring-8 ring-muted/20">
          <DownloadIcon class="w-12 h-12 text-muted-foreground" />
        </div>
        <div class="space-y-3 max-w-md mx-auto">
          <h3 class="text-xl font-semibold text-foreground">
            {{ searchQuery || filters.category || filters.file_type ? 'No downloads found' : 'No downloads yet' }}
          </h3>
          <p class="text-muted-foreground">
            {{ searchQuery || filters.category || filters.file_type
            ? 'Try adjusting your search or filters to find what you\'re looking for.'
            : 'Get started by adding your first downloadable content to share with your audience.'
            }}
          </p>
        </div>
        <div class="flex gap-3 justify-center mt-8">
          <Button v-if="searchQuery || filters.category || filters.file_type" variant="outline" @click="clearFilters" class="gap-2">
            <RefreshCw class="w-4 h-4" />
            Clear Filters
          </Button>
          <Link :href="route('admin.downloads.create')" as="button">
            <Button class="bg-primary hover:bg-primary/90 text-primary-foreground gap-2 shadow-lg">
              <Plus class="w-4 h-4" />
              Add Your First Download
            </Button>
          </Link>
        </div>
      </div>

      <!-- Enhanced Pagination -->
      <div v-if="downloads.data.length > 0 && downloads.last_page > 1" class="mt-8">
        <Card class= bg-card/50">
          <CardContent class="p-4">
            <div class="flex items-center justify-between">
              <div class="text-sm text-muted-foreground">
                Showing <span class="font-medium">{{ downloads.from }}</span> to <span class="font-medium">{{ downloads.to }}</span> of <span class="font-medium">{{ downloads.total }}</span> downloads
              </div>

              <div class="flex items-center gap-2">
                <!-- Pagination would go here -->
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Enhanced scrollbar */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: hsl(var(--muted));
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: hsl(var(--muted-foreground) / 0.3);
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: hsl(var(--muted-foreground) / 0.5);
}

/* Enhanced line clamp utilities */
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Enhanced hover effects */
.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}

/* Smooth transitions for all interactive elements */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.5s ease-out;
}

/* Hardware acceleration for better performance */
.group {
  transform: translateZ(0);
  will-change: transform;
}

/* Enhanced gradient backgrounds */
.bg-linear-to-br {
  background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
}

/* Custom focus states */
.focus\:ring-primary\/20:focus {
  --tw-ring-color: hsl(var(--primary) / 0.2);
}
</style>
