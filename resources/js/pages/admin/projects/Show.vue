<script setup lang="ts">
import {Head, Link, router} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import {Project} from "@/types";
import AuthLayout from "@/layouts/AuthLayout.vue";

// Shadcn-Vue Components
import {Button} from "@/components/ui/button";
import {Badge} from "@/components/ui/badge";
import {Card, CardContent, CardDescription, CardHeader, CardTitle} from "@/components/ui/card";
import {Separator} from "@/components/ui/separator";
import {Avatar, AvatarFallback, AvatarImage} from "@/components/ui/avatar";
import {Progress} from "@/components/ui/progress";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle,} from "@/components/ui/dialog";

// Icons
import {
  AlertCircle,
  ArrowLeft,
  ArrowUpRight,
  Award,
  Bookmark,
  Building2,
  Calendar,
  CheckCircle2,
  Clock,
  Code,
  DollarSign,
  Download,
  ExternalLink,
  Eye,
  Figma,
  Github,
  Globe,
  Heart,
  ImageIcon,
  Layers,
  Mail,
  MapPin,
  MoreVertical,
  Pencil,
  Phone,
  Share2,
  Star,
  Tag,
  Target,
  Trash2,
  TrendingUp,
  User,
  Zap
} from "lucide-vue-next";
import {useDark} from "@vueuse/core";

const props = defineProps<{
  project: {
    data: Project
  };
}>();

defineOptions({
  layout: AuthLayout,
});

const isDark = useDark()

// State
const selectedImage = ref<any>(null);
const isImageDialogOpen = ref(false);

// Computed properties
const statusConfig = computed(() => {
  const configs = {
    'not_started': {
      color: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
      icon: AlertCircle,
      progress: 0
    },
    'in_progress': {
      color: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
      icon: TrendingUp,
      progress: 60
    },
    'on_hold': {
      color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
      icon: Clock,
      progress: 30
    },
    'completed': {
      color: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
      icon: CheckCircle2,
      progress: 100
    },
    'cancelled': {
      color: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
      icon: AlertCircle,
      progress: 0
    }
  };
  return configs[props.project.data.status as keyof typeof configs] || configs.not_started;
});

const priorityConfig = computed(() => {
  const configs = {
    'low': { color: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300', icon: '🟢' },
    'medium': { color: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', icon: '🟡' },
    'high': { color: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300', icon: '🟠' },
    'urgent': { color: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300', icon: '🔴' }
  };
  return configs[props.project.data.priority as keyof typeof configs] || configs.low;
});

const formatDate = (date: string | null) => {
  if (!date) return 'Not set';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const formatCurrency = (amount: number | string | null) => {
  if (!amount) return '$0';
  const numAmount = typeof amount === 'string' ? parseFloat(amount) : amount;
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(numAmount);
};

const formatFileSize = (bytes: number) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const heroImage = computed(() => {
  if (props.project.data.poster_url) return props.project.data.poster_url;
  if (props.project.data.gallery_images && props.project.data.gallery_images.length > 0) {
    return props.project.data.gallery_images[0].url;
  }
  return null;
});

const projectDuration = computed(() => {
  if (!props.project.data.start_date || !props.project.data.end_date) return null;
  const start = new Date(props.project.data.start_date);
  const end = new Date(props.project.data.end_date);
  const diffTime = Math.abs(end.getTime() - start.getTime());

  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
});

const hoursVariance = computed(() => {
  if (!props.project.data.estimated_hours || !props.project.data.actual_hours) return null;
  const estimated = parseFloat(props.project.data.estimated_hours.toString());
  const actual = parseFloat(props.project.data.actual_hours.toString());
  return actual - estimated;
});

// Methods
const openImageModal = (image: any) => {
  selectedImage.value = image;
  isImageDialogOpen.value = true;
};

const getCustomerInitials = (customer: any) => {
  if (!customer) return 'N/A';
  return `${customer.first_name?.[0] || ''}${customer.last_name?.[0] || ''}`.toUpperCase();
};
</script>

<template>
  <Head :title="project.name" />

  <div class="min-h-screen bg-background">
    <!-- Header -->
    <div class="border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-2">
          <div>
            <Button variant="link" class="px-0" asChild>
              <Link :href="route('admin.projects.index')">
                <ArrowLeft class="size-4" /> All projects
              </Link>
            </Button>

            <h1 class="text-xl font-semibold">{{ project.data.name }}</h1>

            <div class="flex items-center space-x-2 mt-1">
              <Badge :class="statusConfig.color" variant="secondary">
                <component :is="statusConfig.icon" class="h-3 w-3 mr-1" />
                {{ project.data.status?.replace('_', ' ') }}
              </Badge>

              <Badge
                v-if="project.data.is_featured" variant="secondary"
                class="bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                <Star class="h-3 w-3 mr-1" />
                Featured
              </Badge>
            </div>
          </div>

          <div class="flex items-center space-x-2">
            <!-- External Links -->
            <div class="flex items-center space-x-1">
              <Button
                v-if="project.data.live_url" variant="ghost" size="sm"
                asChild class="text-muted-foreground hover:text-primary">
                <a
                  :href="project.data.live_url"
                  target="_blank" rel="noopener noreferrer">
                  <ArrowUpRight />
                </a>
              </Button>

              <Button
                v-if="project.data.github_url" variant="ghost" size="sm"
                asChild class="text-muted-foreground hover:text-primary">
                <a
                  :href="project.data.github_url"
                  target="_blank" rel="noopener noreferrer">
                  <Github class="h-4 w-4" />
                </a>
              </Button>

              <Button v-if="project.data.figma_url" variant="ghost" size="sm" asChild>
                <a :href="project.data.figma_url" target="_blank" rel="noopener noreferrer">
                  <Figma class="h-4 w-4" />
                </a>
              </Button>
            </div>

            <Separator orientation="vertical" class="h-6" />

            <!-- Actions -->
            <Button as-child variant="outline">
              <Link :href="route('admin.projects.edit', project.data.uuid)">
                <Pencil class="h-4 w-4 mr-2" />
                Edit
              </Link>
            </Button>

            <!-- More Actions -->
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="sm">
                  <MoreVertical class="h-4 w-4" />
                </Button>
              </DropdownMenuTrigger>

              <DropdownMenuContent align="end">
                <DropdownMenuItem>
                  <Share2 class="h-4 w-4" />
                  Share Project
                </DropdownMenuItem>

                <DropdownMenuItem>
                  <Bookmark class="h-4 w-4" />
                  Add to Collection
                </DropdownMenuItem>

                <DropdownMenuSeparator />

                <DropdownMenuItem
                  @click="() => router.delete(route('admin.projects.destroy', project.data.uuid))">
                  <Trash2 class="h-4 w-4" /> Delete Project
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">

          <!-- Hero Section -->
          <Card>
            <CardContent class="p-0">
              <div v-if="heroImage" class="aspect-video bg-muted rounded-t-lg overflow-hidden">
                <img
                  :src="heroImage"
                  :alt="project.data.name"
                  class="w-full h-full object-cover"
                />
              </div>

              <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                  <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-2">{{ project.data.name }}</h2>
                    <p v-if="project.data.short_description" class="text-muted-foreground text-lg">
                      {{ project.data.short_description }}
                    </p>
                  </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-6">
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium">Project Progress</span>
                    <span class="text-sm text-muted-foreground">{{ statusConfig.progress }}%</span>
                  </div>
                  <Progress v-model="statusConfig.progress" class="h-1 rounded-none" />
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                  <div class="text-center p-4 bg-muted rounded-lg">
                    <div class="text-2xl font-bold text-primary">
                      {{ formatCurrency(project.data.budget) }}
                    </div>
                    <div class="text-sm text-muted-foreground">Budget</div>
                  </div>
                  <div class="text-center p-4 bg-muted rounded-lg">
                    <div class="text-2xl font-bold text-primary">
                      {{ project.data.estimated_hours || 0 }}h
                    </div>
                    <div class="text-sm text-muted-foreground">Estimated</div>
                  </div>
                  <div class="text-center p-4 bg-muted rounded-lg">
                    <div class="text-2xl font-bold text-primary">
                      {{ project.data.actual_hours || 0 }}h
                    </div>
                    <div class="text-sm text-muted-foreground">Actual</div>
                  </div>
                  <div class="text-center p-4 bg-muted rounded-lg">
                    <div class="text-2xl font-bold text-primary">
                      {{ projectDuration || 0 }}
                    </div>
                    <div class="text-sm text-muted-foreground">Days</div>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Project Description -->
          <Card v-if="project.data.description">
            <CardHeader>
              <CardTitle class="flex items-center">
                <Target class="h-5 w-5 mr-2" />
                Project Description
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div class="prose dark:prose-invert max-w-none" v-html="project.data.description"></div>
            </CardContent>
          </Card>

          <!-- Technologies -->
          <Card v-if="project.data.technologies && project.data.technologies.length > 0">
            <CardHeader>
              <CardTitle class="flex items-center">
                <Code class="h-5 w-5 mr-2" />
                Technologies Used
              </CardTitle>
              <CardDescription>Technical stack and tools used in this project</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="flex flex-wrap gap-2">
                <Badge
                  v-for="tech in project.data.technologies"
                  :key="tech"
                  variant="secondary"
                  class="bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                >
                  {{ tech }}
                </Badge>
              </div>
            </CardContent>
          </Card>

          <!-- Features -->
          <Card v-if="project.data.features && project.data.features.length > 0">
            <CardHeader>
              <CardTitle class="flex items-center">
                <Award class="h-5 w-5 mr-2" />
                Key Features
              </CardTitle>
              <CardDescription>Highlight important project features and capabilities</CardDescription>
            </CardHeader>
            <CardContent>
              <ul class="space-y-2">
                <li
                  v-for="feature in project.data.features"
                  :key="feature"
                  class="flex items-center"
                >
                  <CheckCircle2 class="h-4 w-4 mr-2 text-green-600 shrink-0" />
                  <span>{{ feature }}</span>
                </li>
              </ul>
            </CardContent>
          </Card>

          <!-- Project Story -->
          <Card v-if="project.data.challenges || project.data.solutions || project.data.results">
            <CardHeader>
              <CardTitle class="flex items-center">
                <Zap class="h-5 w-5 mr-2" />
                Project Journey
              </CardTitle>

              <CardDescription>
                Challenges faced, solutions implemented, and results achieved
              </CardDescription>
            </CardHeader>

            <CardContent>
              <div class="grid grid-cols-1 gap-6">
                <div v-if="project.data.challenges">
                  <h4 class="font-medium mb-2 flex items-center">
                    <AlertCircle class="h-4 w-4 mr-2 text-red-600" />
                    Challenges
                  </h4>
                  <p class="text-muted-foreground text-sm">{{ project.data.challenges }}</p>
                </div>

                <Separator v-if="project.data.solutions" />

                <div v-if="project.data.solutions">
                  <h4 class="font-medium mb-2 flex items-center">
                    <Target class="h-4 w-4 mr-2 text-blue-600" />
                    Solutions
                  </h4>
                  <p class="text-muted-foreground text-sm">{{ project.data.solutions }}</p>
                </div>

                <Separator v-if="project.data.results" />

                <div v-if="project.data.results">
                  <h4 class="font-medium mb-2 flex items-center">
                    <CheckCircle2 class="h-4 w-4 mr-2 text-green-600" />
                    Results
                  </h4>
                  <p class="text-muted-foreground text-sm">{{ project.data.results }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Client Feedback -->
          <Card v-if="project.data.client_feedback">
            <CardHeader>
              <CardTitle class="flex items-center">
                <Heart class="h-5 w-5 mr-2" />
                Client Feedback
              </CardTitle>
            </CardHeader>
            <CardContent>
              <blockquote class="border-l-4 border-primary pl-4 italic">
                "{{ project.data.client_feedback }}"
              </blockquote>
            </CardContent>
          </Card>

          <!-- Project Gallery -->
          <Card v-if="project.data.gallery_images && project.data.gallery_images.length > 0">
            <CardHeader>
              <CardTitle class="flex items-center">
                <ImageIcon class="h-5 w-5 mr-2" />
                Project Gallery
                <Badge variant="secondary" class="ml-2">
                  {{ project.data.gallery_images.length }} {{ project.data.gallery_images.length === 1 ? 'image' : 'images' }}
                </Badge>
              </CardTitle>

              <CardDescription>
                Project screenshots and visual documentation
              </CardDescription>
            </CardHeader>

            <CardContent>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                  v-for="(image, index) in project.data.gallery_images"
                  :key="image.id"
                  class="relative group aspect-video bg-muted rounded-lg overflow-hidden cursor-pointer"
                  @click="openImageModal(image)">
                  <img
                    :src="image.url"
                    :alt="image.name || `Project image ${index + 1}`"
                    class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105"
                    loading="lazy"
                  />
                  <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-200 flex items-center justify-center">
                    <div class="opacity-0 group-hover:opacity-100 flex space-x-2 transition-opacity duration-200">
                      <Button size="sm" variant="secondary">
                        <Eye class="h-4 w-4" />
                      </Button>
                      <Button size="sm" variant="secondary" asChild>
                        <a :href="image.url" download>
                          <Download class="h-4 w-4" />
                        </a>
                      </Button>
                    </div>
                  </div>

                  <!-- Image info overlay -->
                  <div class="absolute bottom-0 left-0 right-0 bg-linear-to-t from-black/60 to-transparent p-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <p class="text-white text-sm font-medium">{{ image.name }}</p>
                    <p class="text-white/80 text-xs">{{ formatFileSize(image.size) }}</p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">

          <!-- Client Information -->
          <Card v-if="project.data.customer">
            <CardHeader>
              <CardTitle class="flex items-center">
                <User class="h-5 w-5 mr-2 shrink-0" />
                Client Information
              </CardTitle>
            </CardHeader>

            <CardContent class="space-y-4">
              <div>
                <div class="font-medium">
                  {{ project.data.customer.first_name }} {{ project.data.customer.last_name }}
                </div>

                <div v-if="project.data.customer.job_title" class="text-sm text-muted-foreground">
                  {{ project.data.customer.job_title }}
                </div>
              </div>

              <Separator />

              <div v-if="project.data.customer.company_name" class="flex items-center space-x-2">
                <Building2 class="h-4 w-4 text-muted-foreground shrink-0" />
                <span class="text-sm">{{ project.data.customer.company_name }}</span>
              </div>

              <div v-if="project.data.customer.email" class="flex items-center space-x-2">
                <Mail class="h-4 w-4 text-muted-foreground shrink-0" />
                <a :href="`mailto:${project.data.customer.email}`" class="text-sm text-primary hover:underline">
                  {{ project.data.customer.email }}
                </a>
              </div>

              <div v-if="project.data.customer.phone_number" class="flex items-center space-x-2">
                <Phone class="h-4 w-4 text-muted-foreground shrink-0" />
                <a :href="`tel:${project.data.customer.phone_number}`" class="text-sm text-primary hover:underline">
                  {{ project.data.customer.phone_number }}
                </a>
              </div>

              <div v-if="project.data.customer.formatted_address" class="flex items-start space-x-2">
                <MapPin class="h-4 w-4 text-muted-foreground mt-0.5 shrink-0" />
                <span class="text-sm">{{ project.data.customer.formatted_address }}</span>
              </div>
            </CardContent>
          </Card>

          <!-- Project Details -->
          <Card>
            <CardHeader>
              <CardTitle>Project Details</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <!-- Status & Priority -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <div class="text-sm font-medium text-muted-foreground mb-1">Status</div>
                  <Badge :class="statusConfig.color" variant="secondary">
                    <component :is="statusConfig.icon" class="h-3 w-3 mr-1" />
                    {{ project.data.status?.replace('_', ' ') }}
                  </Badge>
                </div>
                <div v-if="project.data.priority">
                  <div class="text-sm font-medium text-muted-foreground mb-1">Priority</div>
                  <Badge :class="priorityConfig.color" variant="secondary">
                    <span class="mr-1">{{ priorityConfig.icon }}</span>
                    {{ project.data.priority }}
                  </Badge>
                </div>
              </div>

              <Separator />

              <!-- Category & Type -->
              <div v-if="project.data.category || project.data.production_type">
                <div v-if="project.data.category" class="mb-3 flex justify-between items-center">
                  <div class="text-sm font-medium text-muted-foreground mb-1 flex items-center">
                    <Tag class="h-4 w-4 mr-1 shrink-0" />
                    Category
                  </div>

                  <div class="text-sm">{{ project.data.category }}</div>
                </div>

                <div v-if="project.data.production_type" class="flex justify-between items-center">
                  <div class="text-sm font-medium text-muted-foreground mb-1 flex items-center">
                    <Layers class="h-4 w-4 mr-1 shrink-0" />
                    Production Type
                  </div>

                  <div class="text-sm">{{ project.data.production_type }}</div>
                </div>
              </div>

              <Separator />

              <!-- Timeline -->
              <div v-if="project.data.start_date || project.data.end_date">
                <div class="text-sm font-medium text-muted-foreground mb-2 flex items-center">
                  <Calendar class="h-4 w-4 mr-1 shrink-0" />
                  Timeline
                </div>
                <div class="space-y-4">
                  <div v-if="project.data.start_date" class="text-sm">
                    <span class="text-muted-foreground font-mono">Started</span> <br />
                    {{ formatDate(project.data.start_date) }}
                  </div>

                  <div v-if="project.data.end_date" class="text-sm">
                    <span class="text-muted-foreground font-mono">Ended</span> <br />
                    {{ formatDate(project.data.end_date) }}
                  </div>

                  <div v-if="projectDuration" class="text-sm">
                    <span class="text-muted-foreground font-mono">Duration</span> <br />
                    {{ projectDuration }} days
                  </div>
                </div>
              </div>

              <Separator />

              <!-- Budget & Hours -->
              <div>
                <div v-if="project.data.budget" class="mb-3">
                  <div class="text-sm font-medium text-muted-foreground mb-1 flex items-center">
                    <DollarSign class="h-4 w-4 mr-1" />
                    Budget
                  </div>
                  <div class="text-sm font-medium">{{ formatCurrency(project.data.budget) }}</div>
                </div>

                <div v-if="project.data.estimated_hours || project.data.actual_hours">
                  <div class="text-sm font-medium text-muted-foreground mb-2 flex items-center">
                    <Clock class="h-4 w-4 mr-1" />
                    Hours Tracking
                  </div>
                  <div class="space-y-1">
                    <div v-if="project.data.estimated_hours" class="text-sm">
                      <span class="text-muted-foreground">Estimated:</span> {{ project.data.estimated_hours }}h
                    </div>
                    <div v-if="project.data.actual_hours" class="text-sm">
                      <span class="text-muted-foreground">Actual:</span> {{ project.data.actual_hours }}h
                    </div>
                    <div v-if="hoursVariance !== null" class="text-sm">
                      <span class="text-muted-foreground">Variance:</span>
                      <span :class="hoursVariance > 0 ? 'text-red-600' : 'text-green-600'">
                        {{ hoursVariance > 0 ? '+' : '' }}{{ hoursVariance }}h
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- External Links -->
          <Card v-if="project.data.live_url || project.data.github_url || project.data.figma_url || project.data.behance_url || project.dribbble_url">
            <CardHeader>
              <CardTitle class="flex items-center">
                <Globe class="h-5 w-5 mr-2" />
                External Links
              </CardTitle>
            </CardHeader>

            <CardContent class="space-y-3">
              <Button
                v-if="project.data.live_url"
                variant="outline"
                size="sm"
                class="w-full justify-start"
                asChild>
                <a :href="project.data.live_url" target="_blank" rel="noopener noreferrer">
                  <ExternalLink class="h-4 w-4 mr-2" />
                  Live Site
                </a>
              </Button>

              <Button
                v-if="project.data.github_url"
                variant="outline"
                size="sm"
                class="w-full justify-start"
                asChild>
                <a :href="project.data.github_url" target="_blank" rel="noopener noreferrer">
                  <Github class="h-4 w-4 mr-2" />
                  GitHub Repository
                </a>
              </Button>

              <Button
                v-if="project.data.figma_url"
                variant="outline"
                size="sm"
                class="w-full justify-start"
                asChild
              >
                <a :href="project.data.figma_url" target="_blank" rel="noopener noreferrer">
                  <Figma class="h-4 w-4 mr-2" />
                  Figma Design
                </a>
              </Button>

              <Button
                v-if="project.data.behance_url"
                variant="outline"
                size="sm"
                class="w-full justify-start"
                asChild
              >
                <a :href="project.data.behance_url" target="_blank" rel="noopener noreferrer">
                  <Eye class="h-4 w-4 mr-2" />
                  Behance Project
                </a>
              </Button>

              <Button
                v-if="project.data.dribbble_url"
                variant="outline"
                size="sm"
                class="w-full justify-start"
                asChild>
                <a :href="project.data.dribbble_url" target="_blank" rel="noopener noreferrer">
                  <Zap class="h-4 w-4 mr-2" />
                  Dribbble Shot
                </a>
              </Button>
            </CardContent>
          </Card>

          <!-- Project Settings -->
          <Card>
            <CardHeader>
              <CardTitle>Project Settings</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-muted-foreground">Public Project</span>
                <Badge :variant="project.data.is_public ? 'default' : 'secondary'">
                  {{ project.data.is_public ? 'Yes' : 'No' }}
                </Badge>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-muted-foreground">Featured</span>
                <Badge :variant="project.data.is_featured ? 'default' : 'secondary'">
                  {{ project.data.is_featured ? 'Yes' : 'No' }}
                </Badge>
              </div>
              <div v-if="project.data.sort_order" class="flex items-center justify-between">
                <span class="text-sm text-muted-foreground">Sort Order</span>
                <Badge variant="secondary">{{ project.data.sort_order }}</Badge>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>

    <!-- Image Modal -->
    <Dialog v-model:open="isImageDialogOpen">
      <DialogContent class="max-w-xl">
        <DialogHeader>
          <DialogTitle>{{ selectedImage?.name || 'Project Image' }}</DialogTitle>
          <DialogDescription>
            {{ formatFileSize(selectedImage?.size || 0) }} • {{ selectedImage?.mime_type }}
          </DialogDescription>
        </DialogHeader>

        <div v-if="selectedImage" class="mt-4">
          <img
            :src="selectedImage.url"
            :alt="selectedImage.name"
            class="w-full h-auto rounded-lg"
          />
          <div class="flex justify-end mt-4 space-x-2">
            <Button variant="outline" asChild>
              <a :href="selectedImage.url" download>
                <Download class="h-4 w-4 mr-2" />
                Download Original
              </a>
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  </div>
</template>
