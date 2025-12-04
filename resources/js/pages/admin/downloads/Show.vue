<script setup lang="ts">
import {Head, Link, router} from "@inertiajs/vue3";
import {ref, computed, onMounted, nextTick} from "vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import {gsap} from "gsap";

// Icons
import {
  ArrowLeft,
  Download as DownloadIcon,
  Trash2,
  Star,
  Eye,
  EyeOff,
  Calendar,
  FileText,
  Image,
  Users,
  TrendingUp,
  Copy,
  ExternalLink,
  Share2,
  MoreHorizontal,
  Globe,
  Lock,
  Pencil,
  Tag,
  Building,
  FolderOpen,
  Clock,
  CheckCircle,
  AlertCircle,
  Loader2,
  RefreshCw,
  BarChart3,
  Activity,
  Zap,
  TrendingDown
} from "lucide-vue-next";

// UI Components
import {Card, CardContent, CardDescription, CardHeader, CardTitle} from "@/components/ui/card";
import {Button} from "@/components/ui/button";
import {Badge} from "@/components/ui/badge";
import {Avatar, AvatarFallback, AvatarImage} from "@/components/ui/avatar";
import {Progress} from "@/components/ui/progress";
import {Label} from "@/components/ui/label";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import {Alert, AlertDescription} from "@/components/ui/alert";
import {Tabs, TabsContent, TabsList, TabsTrigger} from "@/components/ui/tabs";
import {useStorage} from "@vueuse/core";

interface Download {
  id: number;
  uuid: string;
  title: string;
  description: string | null;
  brand: string | null;
  category: string | null;
  file_type: string | null;
  file_size: number | null;
  formatted_file_size: string;
  file_extension: string | null;
  download_count: number;
  is_featured: boolean;
  is_public: boolean;
  sort_order: number;
  meta_title: string | null;
  meta_description: string | null;
  tags: string[];
  poster_url: string | null;
  thumb_url: string | null;
  medium_url: string | null;
  download_url: string | null;
  public_url: string;
  created_at: string;
  updated_at: string;
  created_at_human: string;
  updated_at_human: string;
  created_at_formatted: string;
  updated_at_formatted: string;
  has_poster: boolean;
  has_file: boolean;
  status: string;
  status_color: string;
  file_type_color: string;
  completion_percentage: number;
}

interface DownloadStats {
  total_downloads: number;
  downloads_today: number;
  downloads_this_week: number;
  downloads_this_month: number;
  peak_download_day: string;
  avg_downloads_per_day: number;
  download_trend: 'up' | 'down' | 'stable';
  trend_percentage: number;
  recent_downloads: Array<{
    date: string;
    count: number;
    formatted_date: string;
  }>;
  growth_data: {
    labels: string[];
    data: number[];
  };
}

interface Props {
  download: {
    data: Download
  };
  stats: DownloadStats;
  related_downloads: {
    data: Download[]
  };
}

const props = defineProps<Props>();

defineOptions({
  layout: AuthLayout
});

// Reactive state
const activeTab = useStorage('download_show_tabs', 'overview');
const showDeleteDialog = ref(false);
const isDeleting = ref(false);
const copySuccess = ref(false);

// Refs for animations
const headerRef = ref<HTMLElement>();
const contentRef = ref<HTMLElement>();
const cardsRef = ref<HTMLElement[]>([]);

// Computed
const downloadUrl = computed(() => {
  return props.download.data.download_url || '#';
});

const publicUrl = computed(() => {
  return props.download.data.public_url;
});

const trendIcon = computed(() => {
  switch (props.stats.download_trend) {
    case 'up':
      return TrendingUp;
    case 'down':
      return TrendingDown;
    default:
      return Activity;
  }
});

const trendColor = computed(() => {
  switch (props.stats.download_trend) {
    case 'up':
      return 'text-green-600 dark:text-green-400';
    case 'down':
      return 'text-red-600 dark:text-red-400';
    default:
      return 'text-blue-600 dark:text-blue-400';
  }
});

const maxDownloadsInWeek = computed(() => {
  return Math.max(...props.stats.recent_downloads.map(d => d.count), 1);
});

// Methods
const copyToClipboard = async (text: string) => {
  try {
    await navigator.clipboard.writeText(text);
    copySuccess.value = true;
    setTimeout(() => {
      copySuccess.value = false;
    }, 2000);
    showNotification('Link copied to clipboard!', 'success');
  } catch (error) {
    showNotification('Failed to copy link', 'error');
  }
};

const downloadFile = () => {
  if (props.download.data.download_url) {
    window.open(props.download.data.download_url, '_blank');
    showNotification('Download started!', 'success');
  }
};

const toggleFeatured = () => {
  router.patch(route('admin.downloads.update', props.download.data.uuid), {
    is_featured: !props.download.data.is_featured
  }, {
    preserveScroll: true,
    onSuccess: () => {
      showNotification(
        `Download ${props.download.data.is_featured ? 'unfeatured' : 'featured'} successfully!`,
        'success'
      );
    }
  });
};

const toggleVisibility = () => {
  router.patch(route('admin.downloads.update', props.download.data.uuid), {
    is_public: !props.download.data.is_public
  }, {
    preserveScroll: true,
    onSuccess: () => {
      showNotification(
        `Download ${props.download.data.is_public ? 'made private' : 'made public'} successfully!`,
        'success'
      );
    }
  });
};

const confirmDelete = () => {
  showDeleteDialog.value = true;
};

const deleteDownload = () => {
  isDeleting.value = true;

  router.delete(route('admin.downloads.destroy', props.download.data.uuid), {
    onSuccess: () => {
      showNotification('Download deleted successfully!', 'success');
    },
    onError: () => {
      showNotification('Failed to delete download', 'error');
      isDeleting.value = false;
      showDeleteDialog.value = false;
    }
  });
};

const shareDownload = () => {
  if (navigator.share) {
    navigator.share({
      title: props.download.data.title,
      text: props.download.data.description || '',
      url: publicUrl.value,
    });
  } else {
    copyToClipboard(publicUrl.value);
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
      {y: -30, opacity: 0},
      {y: 0, opacity: 1, duration: 0.8, ease: "power3.out"}
    );
  }
};

const animateContent = () => {
  if (contentRef.value) {
    gsap.fromTo(contentRef.value,
      {y: 20, opacity: 0},
      {y: 0, opacity: 1, duration: 0.6, delay: 0.2, ease: "power2.out"}
    );
  }
};

const animateCards = () => {
  if (cardsRef.value.length > 0) {
    gsap.fromTo(cardsRef.value,
      {y: 30, opacity: 0, scale: 0.95},
      {
        y: 0,
        opacity: 1,
        scale: 1,
        duration: 0.6,
        stagger: 0.1,
        ease: "back.out(1.7)"
      }
    );
  }
};

// Lifecycle
onMounted(() => {
  nextTick(() => {
    animateHeader();
    animateContent();
    animateCards();
  });
});
</script>

<template>
  <Head :title="download.data.title"/>

  <div class="min-h-screen bg-background">
    <!-- Enhanced Header -->
    <div
      ref="headerRef"
      class="bg-linear-to-r from-background via-background to-background/95 border-b backdrop-blur-xl sticky top-0 z-40">
      <div class="mx-auto max-w-4xl px-4 py-2">
        <div class="flex lg:flex-row lg:items-center justify-between gap-6">
          <!-- Navigation and Title -->
          <div class="flex flex-col">

            <Link
              :href="route('admin.downloads.index')"
              class="mb-2 flex items-center gap-x-2 text-muted-foreground text-sm"
              as="button">
              <ArrowLeft class="h-4 w-4"/>
              <span class="hidden sm:inline">All downloadable files</span>
            </Link>

            <div class="flex items-center gap-3">
              <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-linear-to-br from-primary/20 to-primary/10 ring-1 ring-primary/20">
                <DownloadIcon class="h-6 w-6 text-primary"/>
              </div>

              <div class="min-w-0">
                <h1 class="text-xl font-bold text-foreground truncate">
                  {{ download.data.title }}
                </h1>

                <div class="flex items-center gap-2 mt-1">
                  <Badge
                    v-if="download.data.is_featured"
                    class="bg-linear-to-r from-amber-500 to-amber-600 text-white">
                    <Star class="w-3 h-3 mr-1 fill-current"/>
                    Featured
                  </Badge>

                  <Badge :variant="download.data.status_color" class="text-xs">
                    <Globe v-if="download.data.is_public" class="w-3 h-3 mr-1"/>
                    <Lock v-else class="w-3 h-3 mr-1"/>
                    {{ download.data.status }}
                  </Badge>

                  <Badge :class="download.data.file_type_color" class="text-xs font-mono font-semibold">
                    {{ download.data.file_extension || 'FILE' }}
                  </Badge>
                </div>
              </div>
            </div>

          </div>

          <!-- Action Buttons -->
          <div class="flex items-center gap-3">
            <!-- Quick Stats -->
            <div class="hidden lg:flex items-center gap-4 text-sm text-muted-foreground">
              <div class="flex items-center gap-1">
                <DownloadIcon class="h-4 w-4"/>
                <span>{{ download.data.download_count }} downloads</span>
              </div>

              <div class="flex items-center gap-1">
                <Calendar class="h-4 w-4"/>
                <span>{{ download.data.created_at_formatted }}</span>
              </div>
            </div>

            <div class="h-6 w-px bg-border hidden lg:block"/>

            <Button
              @click="() => router.visit(route('admin.downloads.edit', download.data.uuid))"
              variant="outline" class="h-11">
              <Pencil class="w-4 h-4"/>
              <span class="hidden sm:inline">Edit</span>
            </Button>

            <!-- More Actions -->
            <DropdownMenu>
              <DropdownMenuTrigger>
                <Button variant="outline" class="w-11" size="icon">
                  <MoreHorizontal class="w-4 h-4"/>
                </Button>
              </DropdownMenuTrigger>

              <DropdownMenuContent align="end" class="w-48">
                <DropdownMenuItem  @click="downloadFile">
                  <DownloadIcon class="w-4 h-4"/>
                  <span class="hidden sm:inline">Download</span>
                </DropdownMenuItem>

                <DropdownMenuItem @click="shareDownload">
                  <Share2 class="w-4 h-4 mr-2"/>
                  Share
                </DropdownMenuItem>
                <DropdownMenuItem @click="copyToClipboard(publicUrl)">
                  <Copy class="w-4 h-4 mr-2"/>
                  Copy Link
                </DropdownMenuItem>
                <DropdownMenuItem @click="toggleFeatured">
                  <Star class="w-4 h-4 mr-2"/>
                  {{ download.data.is_featured ? 'Unfeature' : 'Feature' }}
                </DropdownMenuItem>
                <DropdownMenuItem @click="toggleVisibility">
                  <component :is="download.data.is_public ? EyeOff : Eye" class="w-4 h-4 mr-2"/>
                  {{ download.data.is_public ? 'Make Private' : 'Make Public' }}
                </DropdownMenuItem>
                <DropdownMenuSeparator/>
                <DropdownMenuItem @click="confirmDelete" class="text-destructive focus:text-destructive">
                  <Trash2 class="w-4 h-4 mr-2"/>
                  Delete
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </div>
      </div>
    </div>

    <!-- Copy Success Alert -->
    <div v-if="copySuccess" class="fixed top-20 right-4 z-50">
      <Alert class="bg-green-50 border-green-200 dark:bg-green-950 dark:border-green-800">
        <CheckCircle class="h-4 w-4 text-green-600 dark:text-green-400"/>
        <AlertDescription class="text-green-800 dark:text-green-200">
          Link copied to clipboard!
        </AlertDescription>
      </Alert>
    </div>

    <!-- Main Content -->
    <div ref="contentRef" class="mx-auto max-w-4xl px-4 py-8">

      <!-- Enhanced Tabs -->
      <Tabs v-model="activeTab" class="space-y-6">
        <!-- Tab Navigation -->
        <div>
          <TabsList class="max-w-md mx-auto grid w-full grid-cols-2 lg:grid-cols-4 h-auto p-1 bg-muted/50">
            <TabsTrigger value="overview" class="flex-col gap-1 py-3 data-[state=active]:bg-background">
              <Eye class="size-8"/>
              <span class="text-lg">Overview</span>
            </TabsTrigger>

            <TabsTrigger value="stats" class="flex-col gap-1 py-3 data-[state=active]:bg-background">
              <BarChart3 class="size-8"/>
              <span class="text-lg">Analytics</span>
            </TabsTrigger>

            <TabsTrigger value="details" class="flex-col gap-1 py-3 data-[state=active]:bg-background">
              <FileText class="size-8"/>
              <span class="text-lg">Details</span>
            </TabsTrigger>

            <TabsTrigger value="related" class="flex-col gap-1 py-3 data-[state=active]:bg-background">
              <Users class="size-8"/>
              <span class="text-lg">Related</span>
            </TabsTrigger>
          </TabsList>
        </div>

        <!-- Overview Tab -->
        <TabsContent value="overview" class="space-y-6">

          <!-- Hero Section -->
          <Card ref="el => cardsRef[0] = el as HTMLElement" class="overflow-hidden">
            <div class="grid lg:grid-cols-2 gap-0">
              <!-- Image Section -->
              <div class="aspect-video lg:aspect-square relative bg-linear-to-br from-muted/50 to-muted">
                <img
                  v-if="download.data.poster_url"
                  :src="download.data.poster_url"
                  :alt="download.data.title"
                  class="w-full h-full object-cover"
                />
                <div
                  v-else
                  class="w-full h-full flex items-center justify-center bg-linear-to-br from-muted/80 to-muted"
                >
                  <div class="text-center space-y-3">
                    <DownloadIcon class="w-16 h-16 text-muted-foreground mx-auto"/>
                    <p class="text-sm text-muted-foreground font-medium">No Preview Available</p>
                  </div>
                </div>

                <!-- Overlay Info -->
                <div class="absolute bottom-4 left-4 right-4">
                  <div class="flex items-center justify-between">
                    <Badge variant="secondary" class="bg-background/90 backdrop-blur-sm">
                      {{ download.data.formatted_file_size }}
                    </Badge>
                    <Button
                      @click="downloadFile"
                      class="bg-primary/90 hover:bg-primary backdrop-blur-sm"
                    >
                      <DownloadIcon class="w-4 h-4 mr-2"/>
                      Download
                    </Button>
                  </div>
                </div>
              </div>

              <!-- Content Section -->
              <CardContent class="p-6 space-y-6">
                <div class="space-y-4">
                  <div>
                    <h2 class="text-2xl font-bold mb-2">{{ download.data.title }}</h2>
                    <p v-if="download.data.brand" class="text-lg text-muted-foreground font-medium">
                      {{ download.data.brand }}
                    </p>
                  </div>

                  <div v-if="download.data.description" class="prose prose-sm dark:prose-invert max-w-none">
                    <p>{{ download.data.description }}</p>
                  </div>

                  <!-- Tags -->
                  <div v-if="download.data.tags.length > 0" class="flex flex-wrap gap-2">
                    <Badge v-for="tag in download.data.tags" :key="tag" variant="outline" class="text-xs">
                      <Tag class="w-3 h-3 mr-1"/>
                      {{ tag }}
                    </Badge>
                  </div>

                  <!-- Quick Stats -->
                  <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                    <div class="text-center">
                      <p class="text-2xl font-bold text-primary">
                        {{ download.data.download_count }}
                      </p>
                      <p class="text-sm text-muted-foreground">Total Downloads</p>
                    </div>
                    <div class="text-center">
                      <p class="text-2xl font-bold text-primary">
                        {{ stats.downloads_today }}
                      </p>
                      <p class="text-sm text-muted-foreground">Today</p>
                    </div>
                  </div>

                  <!-- Completion Progress -->
                  <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                      <span class="text-muted-foreground">Profile Completion</span>
                      <span class="font-medium">
                        {{ download.data.completion_percentage }}%
                      </span>
                    </div>
                    <Progress v-model="download.data.completion_percentage" class="h-2"/>
                  </div>
                </div>
              </CardContent>
            </div>
          </Card>

          <!-- Quick Info Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Card ref="el => cardsRef[1] = el as HTMLElement">
              <CardContent class="p-4 text-center">
                <div
                  class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 mx-auto mb-3">
                  <Calendar class="w-6 h-6 text-blue-600 dark:text-blue-400"/>
                </div>
                <h3 class="font-semibold mb-1">Created</h3>
                <p class="text-sm text-muted-foreground">
                  {{ download.data.created_at_formatted }}
                </p>
                <p class="text-xs text-muted-foreground mt-1">
                  {{ download.data.created_at_human }}
                </p>
              </CardContent>
            </Card>

            <Card ref="el => cardsRef[2] = el as HTMLElement">
              <CardContent class="p-4 text-center">
                <div
                  class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-900 mx-auto mb-3">
                  <FileText class="w-6 h-6 text-green-600 dark:text-green-400"/>
                </div>
                <h3 class="font-semibold mb-1">File Type</h3>
                <p class="text-sm text-muted-foreground">
                  {{ download.data.file_extension || 'Unknown' }} File
                </p>
                <p class="text-xs text-muted-foreground mt-1">
                  {{ download.data.formatted_file_size }}
                </p>
              </CardContent>
            </Card>

            <Card ref="el => cardsRef[3] = el as HTMLElement">
              <CardContent class="p-4 text-center">
                <div
                  class="flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900 mx-auto mb-3">
                  <FolderOpen class="w-6 h-6 text-purple-600 dark:text-purple-400"/>
                </div>
                <h3 class="font-semibold mb-1">Category</h3>
                <p class="text-sm text-muted-foreground">
                  {{ download.data.category || 'Uncategorized' }}
                </p>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        <!-- Analytics Tab -->
        <TabsContent value="stats" class="space-y-6">

          <!-- Download Trend -->
          <Card ref="el => cardsRef[4] = el as HTMLElement">
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <component :is="trendIcon" :class="[trendColor, 'w-5 h-5']"/>
                Download Analytics
              </CardTitle>
              <CardDescription>
                Performance metrics and download trends
              </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">

              <!-- Key Metrics -->
              <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="text-center p-4 rounded-lg bg-muted/50">
                  <p class="text-2xl font-bold text-primary">{{ stats.total_downloads }}</p>
                  <p class="text-sm text-muted-foreground">Total Downloads</p>
                </div>
                <div class="text-center p-4 rounded-lg bg-muted/50">
                  <p class="text-2xl font-bold text-green-600">{{ stats.downloads_this_week }}</p>
                  <p class="text-sm text-muted-foreground">This Week</p>
                </div>
                <div class="text-center p-4 rounded-lg bg-muted/50">
                  <p class="text-2xl font-bold text-blue-600">{{ stats.downloads_this_month }}</p>
                  <p class="text-sm text-muted-foreground">This Month</p>
                </div>
                <div class="text-center p-4 rounded-lg bg-muted/50">
                  <p class="text-2xl font-bold text-purple-600">{{ Math.round(stats.avg_downloads_per_day) }}</p>
                  <p class="text-sm text-muted-foreground">Daily Average</p>
                </div>
              </div>

              <!-- Trend Indicator -->
              <div class="flex items-center justify-center p-4 rounded-lg border">
                <div class="text-center">
                  <div class="flex items-center justify-center gap-2 mb-2">
                    <component :is="trendIcon" :class="[trendColor, 'w-6 h-6']"/>
                    <span :class="trendColor" class="text-lg font-semibold">
                      {{ stats.trend_percentage }}%
                    </span>
                  </div>
                  <p class="text-sm text-muted-foreground">
                    {{
                      stats.download_trend === 'up' ? 'Increase' : stats.download_trend === 'down' ? 'Decrease' : 'Stable'
                    }}
                    compared to last period
                  </p>
                </div>
              </div>

              <!-- Peak Day -->
              <div class="text-center p-4 rounded-lg bg-linear-to-r from-primary/10 to-primary/5">
                <h4 class="font-semibold mb-1">Peak Download Day</h4>
                <p class="text-sm text-muted-foreground">
                  {{
                    new Date(stats.peak_download_day).toLocaleDateString('en-US', {
                      weekday: 'long',
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric'
                    })
                  }}
                </p>
              </div>
            </CardContent>
          </Card>

          <!-- Recent Activity -->
          <Card ref="el => cardsRef[5] = el as HTMLElement">
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <Activity class="w-5 h-5"/>
                Recent Download Activity
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-3">
                <div v-for="(activity, index) in stats.recent_downloads" :key="index"
                     class="flex items-center justify-between p-3 rounded-lg bg-muted/30">
                  <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-primary"></div>
                    <span class="text-sm font-medium">
                      {{ activity.formatted_date }}
                    </span>
                  </div>
                  <div class="flex items-center gap-3">
                    <Badge variant="outline">
                      {{ activity.count }} downloads
                    </Badge>
                    <div class="w-16 h-2 bg-muted rounded-full overflow-hidden">
                      <div
                        class="h-full bg-primary transition-all duration-300"
                        :style="{ width: `${(activity.count / maxDownloadsInWeek) * 100}%` }"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <!-- Details Tab -->
        <TabsContent value="details" class="space-y-6">

          <!-- File Information -->
          <Card ref="el => cardsRef[6] = el as HTMLElement">
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <FileText class="w-5 h-5"/>
                File Information
              </CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-3">
                  <div>
                    <Label class="text-sm font-medium text-muted-foreground">File Name</Label>
                    <p class="font-medium">{{ download.data.title }}.{{
                        download.data.file_extension?.toLowerCase()
                      }}</p>
                  </div>
                  <div>
                    <Label class="text-sm font-medium text-muted-foreground">File Size</Label>
                    <p class="font-medium">{{ download.data.formatted_file_size }}</p>
                  </div>
                  <div>
                    <Label class="text-sm font-medium text-muted-foreground">File Type</Label>
                    <Badge :class="download.data.file_type_color" class="font-mono">
                      {{ download.data.file_extension || 'Unknown' }}
                    </Badge>
                  </div>
                </div>
                <div class="space-y-3">
                  <div>
                    <Label class="text-sm font-medium text-muted-foreground">Category</Label>
                    <p class="font-medium">{{ download.data.category || 'Uncategorized' }}</p>
                  </div>
                  <div>
                    <Label class="text-sm font-medium text-muted-foreground">Brand</Label>
                    <p class="font-medium">{{ download.data.brand || 'No brand' }}</p>
                  </div>
                  <div>
                    <Label class="text-sm font-medium text-muted-foreground">Sort Order</Label>
                    <p class="font-medium">{{ download.data.sort_order }}</p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- SEO Information -->
          <Card ref="el => cardsRef[7] = el as HTMLElement">
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <Globe class="w-5 h-5"/>
                SEO & Meta Information
              </CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div>
                <Label class="text-sm font-medium text-muted-foreground">Meta Title</Label>
                <p class="font-medium">{{ download.data.meta_title || download.data.title }}</p>
              </div>
              <div>
                <Label class="text-sm font-medium text-muted-foreground">Meta Description</Label>
                <p class="text-sm">
                  {{ download.data.meta_description || download.data.description || 'No description provided' }}
                </p>
              </div>
              <div>
                <Label class="text-sm font-medium text-muted-foreground">Public URL</Label>
                <div class="flex items-center gap-2 mt-1">
                  <code class="flex-1 p-2 bg-muted rounded text-sm">{{ publicUrl }}</code>
                  <Button variant="outline" size="sm" @click="copyToClipboard(publicUrl)">
                    <Copy class="w-4 h-4"/>
                  </Button>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- System Information -->
          <Card ref="el => cardsRef[8] = el as HTMLElement">
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <Clock class="w-5 w-5"/>
                System Information
              </CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Created</Label>
                  <p class="font-medium">
                    {{ new Date(download.data.created_at).toLocaleString() }}
                  </p>
                  <p class="text-xs text-muted-foreground">{{ download.data.created_at_human }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">Last Updated</Label>
                  <p class="font-medium">
                    {{ new Date(download.data.updated_at).toLocaleString() }}
                  </p>
                  <p class="text-xs text-muted-foreground">{{ download.data.updated_at_human }}</p>
                </div>
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">UUID</Label>
                  <code class="text-sm bg-muted px-2 py-1 rounded">{{ download.data.uuid }}</code>
                </div>
                <div>
                  <Label class="text-sm font-medium text-muted-foreground">ID</Label>
                  <p class="font-medium">#{{ download.data.id }}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <!-- Related Downloads Tab -->
        <TabsContent value="related" class="space-y-6">

          <Card ref="el => cardsRef[9] = el as HTMLElement">
            <CardHeader>
              <CardTitle class="flex items-center gap-2">
                <Users class="w-5 h-5"/>
                Related Downloads
              </CardTitle>
              <CardDescription>
                Other downloads in the same category or with similar tags
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div v-if="related_downloads.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                  v-for="relatedDownload in related_downloads.data"
                  :key="relatedDownload.uuid"
                  class="flex items-center gap-3 p-3 rounded-lg border hover:bg-muted/50 transition-colors cursor-pointer"
                  @click="router.visit(route('admin.downloads.show', relatedDownload.uuid))"
                >
                  <Avatar class="w-12 h-12 rounded-lg">
                    <AvatarImage :src="relatedDownload.thumb_url" :alt="relatedDownload.title"/>
                    <AvatarFallback class="rounded-lg">
                      <DownloadIcon class="w-5 h-5"/>
                    </AvatarFallback>
                  </Avatar>
                  <div class="flex-1 min-w-0">
                    <h4 class="font-medium truncate">{{ relatedDownload.title }}</h4>
                    <p class="text-sm text-muted-foreground">
                      {{ relatedDownload.download_count }} downloads
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                      <Badge v-if="relatedDownload.category" variant="outline" class="text-xs">
                        {{ relatedDownload.category }}
                      </Badge>
                      <Badge v-if="relatedDownload.is_featured" variant="default" class="text-xs">
                        <Star class="w-3 h-3 mr-1 fill-current"/>
                        Featured
                      </Badge>
                    </div>
                  </div>
                  <ExternalLink class="w-4 h-4 text-muted-foreground"/>
                </div>
              </div>
              <div v-else class="text-center py-8">
                <Users class="w-12 h-12 text-muted-foreground mx-auto mb-3"/>
                <p class="text-muted-foreground">No related downloads found</p>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:open="showDeleteDialog">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle class="flex items-center gap-2 text-destructive">
            <AlertCircle class="h-5 w-5"/>
            Delete Download
          </DialogTitle>
          <DialogDescription>
            Are you sure you want to delete "{{ download.data.title }}"? This action cannot be undone.
          </DialogDescription>
        </DialogHeader>

        <div class="py-4">
          <Alert variant="destructive">
            <AlertCircle class="h-4 w-4"/>
            <AlertDescription>
              This will permanently delete the download and all associated data.
            </AlertDescription>
          </Alert>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="showDeleteDialog = false" :disabled="isDeleting">
            Cancel
          </Button>
          <Button variant="destructive" @click="deleteDownload" :disabled="isDeleting" class="gap-2">
            <Loader2 v-if="isDeleting" class="h-4 w-4 animate-spin"/>
            <Trash2 v-else class="h-4 w-4"/>
            {{ isDeleting ? 'Deleting...' : 'Delete' }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>

<style scoped>
/* Smooth transitions */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Enhanced hover effects */
.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}

/* Tab animations */
[data-state="active"] {
  animation: slideIn 0.2s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Custom focus states */
.focus\:ring-primary\/20:focus {
  --tw-ring-color: hsl(var(--primary) / 0.2);
}

/* Prose styling for description */
.prose {
  max-width: none;
}

.prose p {
  margin-bottom: 0;
}

/* Hardware acceleration for better performance */
.group {
  transform: translateZ(0);
  will-change: transform;
}
</style>
