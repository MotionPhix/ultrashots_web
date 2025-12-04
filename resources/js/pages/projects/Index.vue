<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/layouts/AppLayout.vue";
import { ref, onMounted, computed, watch, nextTick } from 'vue';
import { Project } from '@/types';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Link } from "@inertiajs/vue3";

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
  Code,
  ExternalLink,
  Github,
  Eye,
  ArrowUpRight,
  Sparkles,
  Zap,
  Target,
  Award,
  ChevronRight,
  SortAsc,
  SortDesc,
  X
} from "lucide-vue-next";
import {useStorage} from "@vueuse/core";

gsap.registerPlugin(ScrollTrigger);

// Props
const props = defineProps<{
  projects: {
    data: Project[]
  };
}>();

// Refs
const headerRef = ref<HTMLElement>();
const filtersRef = ref<HTMLElement>();
const projectsGridRef = ref<HTMLElement>();
const searchInput = ref('');
const selectedCategory = ref('all');
const selectedStatus = ref('all');
const selectedYear = ref('all');
const sortBy = ref('newest');
const viewMode = useStorage<'grid' | 'list'>('front_end_projects_view_mode', 'grid');
const isFilterOpen = ref(false);

// Store animation instances
const animations = ref<gsap.core.Tween[]>([]);

// Computed properties
const filteredProjects = computed(() => {
  let filtered = [...props.projects.data];

  // Search filter
  if (searchInput.value.trim()) {
    const searchTerm = searchInput.value.toLowerCase();
    filtered = filtered.filter(project =>
      project.name.toLowerCase().includes(searchTerm) ||
      project.short_description?.toLowerCase().includes(searchTerm) ||
      project.description?.toLowerCase().includes(searchTerm) ||
      project.technologies?.some(tech => tech.toLowerCase().includes(searchTerm))
    );
  }

  // Category filter
  if (selectedCategory.value !== 'all') {
    filtered = filtered.filter(project => project.category === selectedCategory.value);
  }

  // Status filter
  if (selectedStatus.value !== 'all') {
    filtered = filtered.filter(project => project.status === selectedStatus.value);
  }

  // Year filter
  if (selectedYear.value !== 'all') {
    filtered = filtered.filter(project => {
      const projectYear = project.end_date
        ? new Date(project.end_date).getFullYear()
        : new Date(project.created_at).getFullYear();
      return projectYear.toString() === selectedYear.value;
    });
  }

  // Sort
  filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'newest':
        return new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
      case 'oldest':
        return new Date(a.created_at).getTime() - new Date(b.created_at).getTime();
      case 'name':
        return a.name.localeCompare(b.name);
      case 'featured':
        return (b.is_featured ? 1 : 0) - (a.is_featured ? 1 : 0);
      default:
        return 0;
    }
  });

  return filtered;
});

const categories = computed(() => {
  const cats = new Set(props.projects.data.map(p => p.category).filter(Boolean));
  return Array.from(cats);
});

const years = computed(() => {
  const yearSet = new Set(props.projects.data.map(project => {
    const date = project.end_date || project.created_at;
    return new Date(date).getFullYear();
  }));
  return Array.from(yearSet).sort((a, b) => b - a);
});

const featuredProjects = computed(() =>
  props.projects.data.filter(p => p.is_featured).slice(0, 3)
);

const projectStats = computed(() => ({
  total: props.projects.data.length,
  completed: props.projects.data.filter(p => p.status === 'completed').length,
  inProgress: props.projects.data.filter(p => p.status === 'in_progress').length,
  featured: props.projects.data.filter(p => p.is_featured).length
}));

// Methods
const clearFilters = () => {
  searchInput.value = '';
  selectedCategory.value = 'all';
  selectedStatus.value = 'all';
  selectedYear.value = 'all';
  sortBy.value = 'newest';
};

const getProjectYear = (project: Project) => {
  const date = project.end_date || project.created_at;
  return new Date(date).getFullYear();
};

const getStatusConfig = (status: string) => {
  const configs = {
    'not_started': { color: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300', label: 'Not Started' },
    'in_progress': { color: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', label: 'In Progress' },
    'on_hold': { color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300', label: 'On Hold' },
    'completed': { color: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300', label: 'Completed' },
    'cancelled': { color: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300', label: 'Cancelled' }
  };
  return configs[status as keyof typeof configs] || configs.completed;
};

const animateProjectCards = () => {
  nextTick(() => {
    const cards = document.querySelectorAll('.project-card');
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
watch([searchInput, selectedCategory, selectedStatus, selectedYear, sortBy], () => {
  animateProjectCards();
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

      // Initial project cards animation
      animateProjectCards();

    } catch (error) {
      console.warn('Animation setup failed:', error);
    }
  }, 100);
});

defineOptions({ layout: AppLayout });
</script>

<template>
  <Head title="Projects" />

  <div class="min-h-screen bg-background">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-linear-to-br from-background via-background to-muted/20">
      <div class="absolute inset-0 bg-grid-black/[0.02] dark:bg-grid-white/[0.02]" />
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        <div ref="headerRef" class="text-center space-y-8">
          <div class="space-y-4">
            <Badge variant="secondary" class="mb-4">
              <Sparkles class="h-3 w-3 mr-1" />
              Portfolio
            </Badge>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight">
              Things I've made trying to put my
              <span class="bg-linear-to-r from-primary to-primary/60 bg-clip-text text-transparent">
                dent in the universe
              </span>
            </h1>
            <p class="text-xl text-muted-foreground max-w-3xl mx-auto leading-relaxed">
              I've worked on tons of little projects over the years but these are the ones that I'm most proud of.
              Each project represents a unique challenge and learning experience.
            </p>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-2xl mx-auto">
            <div class="text-center">
              <div class="text-3xl font-bold text-primary">{{ projectStats.total }}</div>
              <div class="text-sm text-muted-foreground">Total Projects</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-green-600">{{ projectStats.completed }}</div>
              <div class="text-sm text-muted-foreground">Completed</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-blue-600">{{ projectStats.inProgress }}</div>
              <div class="text-sm text-muted-foreground">In Progress</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-yellow-600">{{ projectStats.featured }}</div>
              <div class="text-sm text-muted-foreground">Featured</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Projects -->
    <section v-if="featuredProjects.length > 0" class="py-16 bg-muted/30">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold mb-4">Featured Projects</h2>
          <p class="text-muted-foreground">Highlighted work that showcases my best capabilities</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <Card
            v-for="project in featuredProjects"
            :key="project.uuid"
            class="group cursor-pointer hover:shadow-xl transition-all duration-300 border-0 bg-background/60 backdrop-blur-sm"
            asChild
          >
            <Link :href="route('projects.show', project.uuid)">
              <div class="relative aspect-video bg-muted rounded-t-lg overflow-hidden">
                <img
                  :src="project.poster_url || '/assets/placeholder-project.jpg'"
                  :alt="project.name"
                  class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                  loading="lazy"
                />
                <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
                <Badge class="absolute top-3 left-3 bg-yellow-500/90 text-white">
                  <Star class="h-3 w-3 mr-1" />
                  Featured
                </Badge>
              </div>
              <CardContent class="p-6">
                <h3 class="font-bold text-lg mb-2 group-hover:text-primary transition-colors">
                  {{ project.name }}
                </h3>
                <p class="text-muted-foreground text-sm mb-4 line-clamp-2">
                  {{ project.short_description }}
                </p>
                <div class="flex items-center justify-between">
                  <Badge :class="getStatusConfig(project.status).color" variant="secondary">
                    {{ getStatusConfig(project.status).label }}
                  </Badge>
                  <ArrowUpRight class="h-4 w-4 text-muted-foreground group-hover:text-primary transition-colors" />
                </div>
              </CardContent>
            </Link>
          </Card>
        </div>
      </div>
    </section>

    <!-- Main Projects Section -->
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
                  placeholder="Search projects..."
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
                <SelectItem v-for="category in categories" :key="category" :value="category">
                  {{ category }}
                </SelectItem>
              </SelectContent>
            </Select>

            <Select v-model="selectedStatus">
              <SelectTrigger>
                <SelectValue placeholder="All Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Status</SelectItem>
                <SelectItem value="completed">Completed</SelectItem>
                <SelectItem value="in_progress">In Progress</SelectItem>
                <SelectItem value="on_hold">On Hold</SelectItem>
                <SelectItem value="not_started">Not Started</SelectItem>
              </SelectContent>
            </Select>

            <Select v-model="selectedYear">
              <SelectTrigger>
                <SelectValue placeholder="All Years" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Years</SelectItem>
                <SelectItem v-for="year in years" :key="year" :value="year.toString()">
                  {{ year }}
                </SelectItem>
              </SelectContent>
            </Select>

            <Select v-model="sortBy">
              <SelectTrigger>
                <SelectValue placeholder="Sort by" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="newest">Newest First</SelectItem>
                <SelectItem value="oldest">Oldest First</SelectItem>
                <SelectItem value="name">Name A-Z</SelectItem>
                <SelectItem value="featured">Featured First</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Active Filters & Clear -->
          <div v-if="searchInput || selectedCategory !== 'all' || selectedStatus !== 'all' || selectedYear !== 'all'"
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
            <Badge v-if="selectedStatus !== 'all'" variant="secondary">
              Status: {{ getStatusConfig(selectedStatus).label }}
              <X class="h-3 w-3 ml-1 cursor-pointer" @click="selectedStatus = 'all'" />
            </Badge>
            <Badge v-if="selectedYear !== 'all'" variant="secondary">
              Year: {{ selectedYear }}
              <X class="h-3 w-3 ml-1 cursor-pointer" @click="selectedYear = 'all'" />
            </Badge>
            <Button variant="ghost" size="sm" @click="clearFilters" class="ml-auto">
              Clear All
            </Button>
          </div>
        </div>

        <!-- Results Count -->
        <div class="flex items-center justify-between mb-8">
          <p class="text-muted-foreground">
            Showing {{ filteredProjects.length }} of {{ props.projects.data.length }} projects
          </p>
        </div>

        <!-- Projects Grid/List -->
        <div ref="projectsGridRef">
          <!-- Grid View -->
          <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <Card
              v-for="project in filteredProjects"
              :key="project.uuid"
              class="project-card group cursor-pointer hover:shadow-xl transition-all duration-300 border-0 bg-card"
              asChild
            >
              <Link :href="route('projects.show', project.uuid)">
                <div class="relative aspect-video bg-muted rounded-t-lg overflow-hidden">
                  <img
                    :src="project.poster_url || '/assets/placeholder-project.jpg'"
                    :alt="project.name"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                    loading="lazy"
                  />
                  <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />

                  <!-- Badges -->
                  <div class="absolute top-3 left-3 flex gap-2">
                    <Badge v-if="project.is_featured" class="bg-yellow-500/90 text-white">
                      <Star class="h-3 w-3 mr-1" />
                      Featured
                    </Badge>
                  </div>

                  <!-- Quick Actions -->
                  <div class="absolute top-3 right-3 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <Button v-if="project.live_url" size="sm" variant="secondary" class="h-8 w-8 p-0" asChild>
                      <a :href="project.live_url" target="_blank" rel="noopener noreferrer" @click.stop>
                        <ExternalLink class="h-3 w-3" />
                      </a>
                    </Button>
                    <Button v-if="project.github_url" size="sm" variant="secondary" class="h-8 w-8 p-0" asChild>
                      <a :href="project.github_url" target="_blank" rel="noopener noreferrer" @click.stop>
                        <Github class="h-3 w-3" />
                      </a>
                    </Button>
                  </div>
                </div>

                <CardContent class="p-6">
                  <div class="flex items-start justify-between mb-3">
                    <h3 class="font-bold text-lg group-hover:text-primary transition-colors line-clamp-1">
                      {{ project.name }}
                    </h3>
                    <Badge :class="getStatusConfig(project.status).color" variant="secondary" class="ml-2 shrink-0">
                      {{ getStatusConfig(project.status).label }}
                    </Badge>
                  </div>

                  <p class="text-muted-foreground text-sm mb-4 line-clamp-2">
                    {{ project.short_description || project.description }}
                  </p>

                  <!-- Technologies -->
                  <div v-if="project.technologies && project.technologies.length > 0" class="flex flex-wrap gap-1 mb-4">
                    <Badge
                      v-for="tech in project.technologies.slice(0, 3)"
                      :key="tech"
                      variant="outline"
                      class="text-xs"
                    >
                      {{ tech }}
                    </Badge>
                    <Badge v-if="project.technologies.length > 3" variant="outline" class="text-xs">
                      +{{ project.technologies.length - 3 }}
                    </Badge>
                  </div>

                  <div class="flex items-center justify-between text-sm text-muted-foreground">
                    <div class="flex items-center">
                      <Calendar class="h-3 w-3 mr-1" />
                      {{ getProjectYear(project) }}
                    </div>
                    <ArrowUpRight class="h-4 w-4 group-hover:text-primary transition-colors" />
                  </div>
                </CardContent>
              </Link>
            </Card>
          </div>

          <!-- List View -->
          <div v-else class="space-y-4">
            <Card
              v-for="project in filteredProjects"
              :key="project.uuid"
              class="project-card group cursor-pointer hover:shadow-lg transition-all duration-300"
              asChild
            >
              <Link :href="route('projects.show', project.uuid)">
                <CardContent class="p-6">
                  <div class="flex items-start gap-6">
                    <div class="relative w-32 h-20 bg-muted rounded-lg overflow-hidden shrink-0">
                      <img
                        :src="project.poster_url || '/assets/placeholder-project.jpg'"
                        :alt="project.name"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                        loading="lazy"
                      />
                    </div>

                    <div class="flex-1 min-w-0">
                      <div class="flex items-start justify-between mb-2">
                        <h3 class="font-bold text-lg group-hover:text-primary transition-colors">
                          {{ project.name }}
                        </h3>
                        <div class="flex items-center gap-2 shrink-0 ml-4">
                          <Badge v-if="project.is_featured" class="bg-yellow-500/90 text-white">
                            <Star class="h-3 w-3 mr-1" />
                            Featured
                          </Badge>
                          <Badge :class="getStatusConfig(project.status).color" variant="secondary">
                            {{ getStatusConfig(project.status).label }}
                          </Badge>
                        </div>
                      </div>

                      <p class="text-muted-foreground mb-3 line-clamp-2">
                        {{ project.short_description || project.description }}
                      </p>

                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4 text-sm text-muted-foreground">
                          <div class="flex items-center">
                            <Calendar class="h-3 w-3 mr-1" />
                            {{ getProjectYear(project) }}
                          </div>
                          <div v-if="project.category" class="flex items-center">
                            <Target class="h-3 w-3 mr-1" />
                            {{ project.category }}
                          </div>
                        </div>

                        <div class="flex items-center gap-2">
                          <Button v-if="project.live_url" size="sm" variant="ghost" class="h-8 w-8 p-0" asChild>
                            <a :href="project.live_url" target="_blank" rel="noopener noreferrer" @click.stop>
                              <ExternalLink class="h-3 w-3" />
                            </a>
                          </Button>
                          <Button v-if="project.github_url" size="sm" variant="ghost" class="h-8 w-8 p-0" asChild>
                            <a :href="project.github_url" target="_blank" rel="noopener noreferrer" @click.stop>
                              <Github class="h-3 w-3" />
                            </a>
                          </Button>
                          <ChevronRight class="h-4 w-4 text-muted-foreground group-hover:text-primary transition-colors" />
                        </div>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Link>
            </Card>
          </div>

          <!-- No Results -->
          <div v-if="filteredProjects.length === 0" class="text-center py-16">
            <div class="max-w-md mx-auto">
              <div class="w-16 h-16 bg-muted rounded-full flex items-center justify-center mx-auto mb-4">
                <Search class="h-8 w-8 text-muted-foreground" />
              </div>
              <h3 class="text-lg font-semibold mb-2">No projects found</h3>
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
