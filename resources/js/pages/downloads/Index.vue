<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import { ref, onMounted, computed, watch, nextTick } from 'vue';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { useStorage } from "@vueuse/core";

import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

// Icons
import {
  Search,
  Filter,
  Grid3X3,
  List,
  Star,
  Calendar,
  Download as DownloadIcon,
  ExternalLink,
  Eye,
  ArrowUpRight,
  Sparkles,
  FileText,
  HardDrive,
  TrendingUp,
  Users,
  ChevronRight,
  X,
  File,
  Image,
  Video,
  Music,
  Archive,
  FileCode,
  Clock,
  Heart
} from "lucide-vue-next";

gsap.registerPlugin(ScrollTrigger);

// Types
interface Download {
  id: number;
  uuid: string;
  title: string;
  description: string;
  brand?: string;
  category?: string;
  file_type?: string;
  file_size: number;
  formatted_file_size: string;
  file_extension?: string;
  download_count: number;
  is_featured: boolean;
  is_public: boolean;
  poster_url?: string;
  thumb_url?: string;
  medium_url?: string;
  download_url?: string;
  public_url: string;
  created_at: string;
  created_at_human: string;
  created_at_formatted: string;
  file_type_color: string;
  tags?: string[];
}

interface DownloadsData {
  data: Download[];
  total: number;
  per_page: number;
  current_page: number;
  last_page: number;
}

// Props
const props = defineProps<{
  downloads: DownloadsData;
  featured_downloads: {
    data: Download[]
  };
  popular_downloads: {
    data: Download[]
  };
  recent_downloads: {
    data: Download[]
  };
  filters: {
    search: string;
    category: string;
    file_type: string;
    brand: string;
    sort_by: string;
    sort_direction: string;
  };
  options: {
    categories: string[];
    file_types: string[];
    brands: string[];
  };
  stats: {
    total_downloads: number;
    total_files: number;
  };
}>();

// Refs
const headerRef = ref<HTMLElement>();
const filtersRef = ref<HTMLElement>();
const downloadsGridRef = ref<HTMLElement>();
const searchInput = ref(props.filters.search);
const selectedCategory = ref(props.filters.category || 'all');
const selectedFileType = ref(props.filters.file_type || 'all');
const selectedBrand = ref(props.filters.brand || 'all');
const sortBy = ref(props.filters.sort_by || 'sort_order');
const viewMode = useStorage<'grid' | 'list'>('downloads_view_mode', 'grid');
const isFilterOpen = ref(false);

// Store animation instances
const animations = ref<gsap.core.Tween[]>([]);

// Computed properties
const filteredDownloads = computed(() => {
  let filtered = [...props.downloads.data];

  // Search filter
  if (searchInput.value.trim()) {
    const searchTerm = searchInput.value.toLowerCase();
    filtered = filtered.filter(download =>
      download.title.toLowerCase().includes(searchTerm) ||
      download.description?.toLowerCase().includes(searchTerm) ||
      download.brand?.toLowerCase().includes(searchTerm) ||
      download.category?.toLowerCase().includes(searchTerm) ||
      download.tags?.some(tag => tag.toLowerCase().includes(searchTerm))
    );
  }

  // Category filter
  if (selectedCategory.value !== 'all') {
    filtered = filtered.filter(download => download.category === selectedCategory.value);
  }

  // File type filter
  if (selectedFileType.value !== 'all') {
    filtered = filtered.filter(download => download.file_type === selectedFileType.value);
  }

  // Brand filter
  if (selectedBrand.value !== 'all') {
    filtered = filtered.filter(download => download.brand === selectedBrand.value);
  }

  // Sort
  filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'newest':
        return new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
      case 'oldest':
        return new Date(a.created_at).getTime() - new Date(b.created_at).getTime();
      case 'title':
        return a.title.localeCompare(b.title);
      case 'downloads':
        return b.download_count - a.download_count;
      case 'size':
        return b.file_size - a.file_size;
      case 'featured':
        return (b.is_featured ? 1 : 0) - (a.is_featured ? 1 : 0);
      default:
        return 0;
    }
  });

  return filtered;
});

const downloadStats = computed(() => ({
  total: props.stats.total_downloads ?? 0,
  featured: props.featured_downloads.data.length ?? 0,
  popular: props.popular_downloads.data.length ?? 0,
  recent: props.recent_downloads.data.length ?? 0
}));

// Methods
const clearFilters = () => {
  searchInput.value = '';
  selectedCategory.value = 'all';
  selectedFileType.value = 'all';
  selectedBrand.value = 'all';
  sortBy.value = 'sort_order';
};

const getFileIcon = (fileType: string) => {
  const type = fileType?.toLowerCase();
  switch (type) {
    case 'pdf':
    case 'doc':
    case 'docx':
    case 'txt':
      return FileText;
    case 'jpg':
    case 'jpeg':
    case 'png':
    case 'gif':
    case 'webp':
    case 'svg':
      return Image;
    case 'mp4':
    case 'avi':
    case 'mov':
    case 'wmv':
      return Video;
    case 'mp3':
    case 'wav':
    case 'flac':
      return Music;
    case 'zip':
    case 'rar':
    case '7z':
      return Archive;
    case 'js':
    case 'ts':
    case 'html':
    case 'css':
    case 'php':
    case 'py':
      return FileCode;
    default:
      return File;
  }
};

const handleDownload = (download: Download) => {
  window.open(route('public.get-download', download.uuid), '_blank');
};

const animateDownloadCards = () => {
  nextTick(() => {
    const cards = document.querySelectorAll('.download-card');
    if (cards.length > 0) {
      gsap.fromTo(cards,
        { opacity: 0, y: 30, scale: 0.95 },
        {
          opacity: 1,
          y: 0,
          scale: 1,
          duration: 0.6,
          stagger: 0.1,
          ease: 'power2.out'
        }
      );
    }
  });
};

// Watch for filter changes to re-animate
watch([searchInput, selectedCategory, selectedFileType, selectedBrand, sortBy], () => {
  animateDownloadCards();
});

// Animations
onMounted(async () => {
  await nextTick();

  setTimeout(() => {
    try {
      // Header animation
      if (headerRef.value) {
        const headerAnimation = gsap.fromTo(headerRef.value.children,
          { opacity: 0, y: 50 },
          {
            opacity: 1,
            y: 0,
            duration: 0.8,
            stagger: 0.2,
            ease: 'power3.out'
          }
        );
        animations.value.push(headerAnimation);
      }

      // Filters animation
      if (filtersRef.value) {
        const filtersAnimation = gsap.fromTo(filtersRef.value,
          { opacity: 0, y: 30 },
          {
            opacity: 1,
            y: 0,
            duration: 0.6,
            delay: 0.4,
            ease: 'power2.out'
          }
        );
        animations.value.push(filtersAnimation);
      }

      // Initial download cards animation
      animateDownloadCards();

    } catch (error) {
      console.warn('Animation setup failed:', error);
    }
  }, 100);
});

defineOptions({ layout: AppLayout });
</script>

<template>
  <Head title="Downloads" />

  <div class="min-h-screen bg-background">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-linear-to-br from-background via-background to-muted/20">
      <div class="absolute inset-0 bg-grid-black/[0.02] dark:bg-grid-white/[0.02]" />
      <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        <div ref="headerRef" class="text-center space-y-8">
          <div class="space-y-4">
            <Badge variant="secondary" class="mb-4">
              <HardDrive class="h-3 w-3 mr-1" />
              Downloads
            </Badge>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight font-mono">
              Free resources & <br>
              <span class="bg-linear-to-r from-primary to-primary/60 bg-clip-text text-transparent">
                digital assets
              </span>
            </h1>
            <p class="text-xl text-muted-foreground max-w-2xl mx-auto leading-relaxed ">
              Discover a curated collection of high-quality digital resources, tools, and assets.
              All downloads are free and ready to use in your projects.
            </p>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-2xl mx-auto">
            <div class="text-center">
              <div class="text-3xl font-bold text-primary">{{ downloadStats.total }}</div>
              <div class="text-sm text-muted-foreground">Total Files</div>
            </div>

            <div class="text-center">
              <div class="text-3xl font-bold text-yellow-600">{{ downloadStats.featured }}</div>
              <div class="text-sm text-muted-foreground">Featured</div>
            </div>

            <div class="text-center">
              <div class="text-3xl font-bold text-green-600">{{ downloadStats.popular }}</div>
              <div class="text-sm text-muted-foreground">Popular</div>
            </div>

            <div class="text-center">
              <div class="text-3xl font-bold text-blue-600">{{ downloadStats.recent }}</div>
              <div class="text-sm text-muted-foreground">Recent</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Downloads -->
    <section v-if="featured_downloads.data.length > 0" class="py-16 bg-muted/30">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold mb-4">Featured Downloads</h2>
          <p class="text-muted-foreground">Hand-picked resources that showcase the best of our collection</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <Card
            v-for="download in featured_downloads.data"
            :key="download.uuid"
            class="group cursor-pointer hover:shadow-xl transition-all duration-300 border-0 bg-background/60 backdrop-blur-sm"
          >
            <div class="relative aspect-video bg-muted rounded-t-lg overflow-hidden">
              <img
                :src="download.thumb_url || '/assets/placeholder-download.jpg'"
                :alt="download.title"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                loading="lazy"
              />
              <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
              <Badge class="absolute top-3 left-3 bg-yellow-500/90 text-white">
                <Star class="h-3 w-3 mr-1" />
                Featured
              </Badge>
              <Button
                size="sm"
                class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                @click="handleDownload(download)"
              >
                <DownloadIcon class="h-3 w-3 mr-1" />
                Download
              </Button>
            </div>
            <CardContent class="p-6">
              <div class="flex items-start justify-between mb-3">
                <h3 class="font-bold text-lg group-hover:text-primary transition-colors line-clamp-1">
                  {{ download.title }}
                </h3>
                <component :is="getFileIcon(download.file_type)" class="h-5 w-5 text-muted-foreground shrink-0 ml-2" />
              </div>
              <p class="text-muted-foreground text-sm mb-4 line-clamp-2">
                {{ download.description }}
              </p>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                  <span>{{ download.formatted_file_size }}</span>
                  <span>•</span>
                  <span>{{ download.download_count }} downloads</span>
                </div>
                <Badge :class="download.file_type_color" variant="secondary">
                  {{ download.file_extension }}
                </Badge>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </section>

    <!-- Main Downloads Section -->
    <section class="py-16">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Filters and Search -->
        <div ref="filtersRef" class="mb-12">
          <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between mb-6">
            <div class="flex-1 max-w-md">
              <div class="relative">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                  v-model="searchInput"
                  placeholder="Search downloads..."
                  class="pl-10"
                />
              </div>
            </div>

            <div class="flex items-center gap-3">
              <!-- View Mode Toggle -->
              <div class="flex items-center border rounded-lg p-1">
                <Button
                  variant="ghost"
                  size="sm"
                  :class="viewMode === 'grid' ? 'bg-muted' : ''"
                  @click="viewMode = 'grid'">
                  <Grid3X3 class="h-4 w-4" />
                </Button>

                <Button
                  variant="ghost"
                  size="sm"
                  :class="viewMode === 'list' ? 'bg-muted' : ''"
                  @click="viewMode = 'list'">
                  <List class="h-4 w-4" />
                </Button>
              </div>

              <!-- Mobile Filter Toggle -->
              <Button
                size="lg"
                variant="outline"
                class="lg:hidden"
                @click="isFilterOpen = !isFilterOpen">
                <Filter class="h-4 w-4 mr-2" />
                Filters
              </Button>
            </div>
          </div>

          <!-- Filter Controls -->
          <div :class="['grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4', isFilterOpen || 'hidden lg:grid']">
            <Select v-model="selectedCategory">
              <SelectTrigger>
                <SelectValue placeholder="All Categories" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Categories</SelectItem>
                <SelectItem v-for="category in options.categories" :key="category" :value="category">
                  {{ category }}
                </SelectItem>
              </SelectContent>
            </Select>

            <Select v-model="selectedFileType">
              <SelectTrigger>
                <SelectValue placeholder="All File Types" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All File Types</SelectItem>
                <SelectItem v-for="fileType in options.file_types" :key="fileType" :value="fileType">
                  {{ fileType.toUpperCase() }}
                </SelectItem>
              </SelectContent>
            </Select>

            <Select v-model="selectedBrand">
              <SelectTrigger>
                <SelectValue placeholder="All Brands" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Brands</SelectItem>
                <SelectItem v-for="brand in options.brands" :key="brand" :value="brand">
                  {{ brand }}
                </SelectItem>
              </SelectContent>
            </Select>

            <Select v-model="sortBy">
              <SelectTrigger>
                <SelectValue placeholder="Sort by" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="sort_order">Default Order</SelectItem>
                <SelectItem value="newest">Newest First</SelectItem>
                <SelectItem value="oldest">Oldest First</SelectItem>
                <SelectItem value="title">Name A-Z</SelectItem>
                <SelectItem value="downloads">Most Downloaded</SelectItem>
                <SelectItem value="size">File Size</SelectItem>
                <SelectItem value="featured">Featured First</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Active Filters & Clear -->
          <div v-if="searchInput || selectedCategory !== 'all' || selectedFileType !== 'all' || selectedBrand !== 'all'"
               class="flex items-center gap-2 mt-4 pt-4 border-t">
            <span class="text-sm text-muted-foreground">Active filters:</span>
            <Badge v-if="searchInput" variant="secondary">
              Search: {{ searchInput }}
              <X class="h-3 w-3 ml-1 cursor-pointer" @click="searchInput = ''" />
            </Badge>
            <Badge v-if="selectedCategory !== 'all'" variant="secondary">
              Category: {{ selectedCategory }}
              <X class="h-3 w-3 ml-1 cursor-pointer" @click="selectedCategory = 'all'" />
            </Badge>
            <Badge v-if="selectedFileType !== 'all'" variant="secondary">
              Type: {{ selectedFileType.toUpperCase() }}
              <X class="h-3 w-3 ml-1 cursor-pointer" @click="selectedFileType = 'all'" />
            </Badge>
            <Badge v-if="selectedBrand !== 'all'" variant="secondary">
              Brand: {{ selectedBrand }}
              <X class="h-3 w-3 ml-1 cursor-pointer" @click="selectedBrand = 'all'" />
            </Badge>
            <Button variant="ghost" size="sm" @click="clearFilters" class="ml-auto">
              Clear All
            </Button>
          </div>
        </div>

        <!-- Results Count -->
        <div class="flex items-center justify-between mb-8">
          <p class="text-muted-foreground">
            Showing {{ filteredDownloads.length }} of {{ props.downloads.total }} downloads
          </p>
        </div>

        <!-- Downloads Grid/List -->
        <div ref="downloadsGridRef">
          <!-- Grid View -->
          <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <Card
              v-for="download in filteredDownloads"
              :key="download.uuid"
              class="download-card group cursor-pointer hover:shadow-xl transition-all duration-300 border-0 bg-card"
            >
              <div class="relative aspect-video bg-muted rounded-t-lg overflow-hidden">
                <img
                  :src="download.thumb_url || '/assets/placeholder-download.jpg'"
                  :alt="download.title"
                  class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                  loading="lazy"
                />
                <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />

                <!-- Badges -->
                <div class="absolute top-3 left-3 flex gap-2">
                  <Badge v-if="download.is_featured" class="bg-yellow-500/90 text-white">
                    <Star class="h-3 w-3 mr-1" />
                    Featured
                  </Badge>
                </div>

                <!-- Download Button -->
                <Button
                  size="sm"
                  class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                  @click="handleDownload(download)"
                >
                  <DownloadIcon class="h-3 w-3 mr-1" />
                  Download
                </Button>
              </div>

              <CardContent class="p-6">
                <div class="flex items-start justify-between mb-3">
                  <h3 class="font-bold text-lg group-hover:text-primary transition-colors line-clamp-1">
                    {{ download.title }}
                  </h3>
                  <component :is="getFileIcon(download.file_type)" class="h-5 w-5 text-muted-foreground shrink-0 ml-2" />
                </div>

                <p class="text-muted-foreground text-sm mb-4 line-clamp-2">
                  {{ download.description }}
                </p>

                <!-- Tags -->
                <div v-if="download.tags && download.tags.length > 0" class="flex flex-wrap gap-1 mb-4">
                  <Badge
                    v-for="tag in download.tags.slice(0, 3)"
                    :key="tag"
                    variant="outline"
                    class="text-xs"
                  >
                    {{ tag }}
                  </Badge>
                  <Badge v-if="download.tags.length > 3" variant="outline" class="text-xs">
                    +{{ download.tags.length - 3 }}
                  </Badge>
                </div>

                <div class="flex items-center justify-between text-sm text-muted-foreground">
                  <div class="flex items-center gap-2">
                    <span>{{ download.formatted_file_size }}</span>
                    <span>•</span>
                    <span>{{ download.download_count }} downloads</span>
                  </div>
                  <Badge :class="download.file_type_color" variant="secondary">
                    {{ download.file_extension }}
                  </Badge>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- List View -->
          <div v-else class="space-y-4">
            <Card
              v-for="download in filteredDownloads"
              :key="download.uuid"
              class="download-card group cursor-pointer hover:shadow-lg transition-all duration-300"
            >
              <CardContent class="p-6">
                <div class="flex items-start gap-6">
                  <div class="relative w-32 h-20 bg-muted rounded-lg overflow-hidden shrink-0">
                    <img
                      :src="download.thumb_url || '/assets/placeholder-download.jpg'"
                      :alt="download.title"
                      class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                      loading="lazy"
                    />
                  </div>

                  <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between mb-2">
                      <h3 class="font-bold text-lg group-hover:text-primary transition-colors">
                        {{ download.title }}
                      </h3>
                      <div class="flex items-center gap-2 shrink-0 ml-4">
                        <Badge v-if="download.is_featured" class="bg-yellow-500/90 text-white">
                          <Star class="h-3 w-3 mr-1" />
                          Featured
                        </Badge>
                        <Badge :class="download.file_type_color" variant="secondary">
                          {{ download.file_extension }}
                        </Badge>
                      </div>
                    </div>

                    <p class="text-muted-foreground mb-3 line-clamp-2">
                      {{ download.description }}
                    </p>

                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-4 text-sm text-muted-foreground">
                        <div class="flex items-center">
                          <component :is="getFileIcon(download.file_type)" class="h-3 w-3 mr-1" />
                          {{ download.formatted_file_size }}
                        </div>
                        <div class="flex items-center">
                          <DownloadIcon class="h-3 w-3 mr-1" />
                          {{ download.download_count }}
                        </div>
                        <div v-if="download.category" class="flex items-center">
                          <span>{{ download.category }}</span>
                        </div>
                      </div>

                      <div class="flex items-center gap-2">
                        <Button
                          size="sm"
                          @click="handleDownload(download)"
                        >
                          <DownloadIcon class="h-3 w-3 mr-1" />
                          Download
                        </Button>
                        <ChevronRight class="h-4 w-4 text-muted-foreground group-hover:text-primary transition-colors" />
                      </div>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- No Results -->
          <div v-if="filteredDownloads.length === 0" class="text-center py-16">
            <div class="max-w-md mx-auto">
              <div class="w-16 h-16 bg-muted rounded-full flex items-center justify-center mx-auto mb-4">
                <Search class="h-8 w-8 text-muted-foreground" />
              </div>
              <h3 class="text-lg font-semibold mb-2">No downloads found</h3>
              <p class="text-muted-foreground mb-4">
                Try adjusting your search criteria or clearing the filters.
              </p>
              <Button variant="outline" @click="clearFilters">
                Clear Filters
              </Button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
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

.bg-grid-black\/\[0\.02\] {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='rgb(0 0 0 / 0.02)'%3e%3cpath d='m0 .5h32m-32 32v-32'/%3e%3c/svg%3e");
}

.dark .bg-grid-white\/\[0\.02\] {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='rgb(255 255 255 / 0.02)'%3e%3cpath d='m0 .5h32m-32 32v-32'/%3e%3c/svg%3e");
}
</style>
