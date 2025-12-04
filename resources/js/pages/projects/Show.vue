<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
import { Project } from "@/types";
import { Head, Link } from "@inertiajs/vue3";
import { computed, onMounted, ref, nextTick, onUnmounted } from 'vue';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { cn } from '@/lib/utils';

// Shadcn-Vue Components
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";

// Icons
import {
  ArrowLeft,
  ArrowUpRight,
  Github,
  Figma,
  Eye,
  Calendar,
  User,
  Tag,
  Code,
  Star,
  Globe,
  Target,
  CheckCircle2,
  AlertCircle,
  Zap,
  Download,
  ImageIcon,
  Award,
  Layers,
  Heart,
  Share2,
  ChevronLeft,
  ChevronRight,
  X,
  Maximize2
} from "lucide-vue-next";

gsap.registerPlugin(ScrollTrigger);

const props = defineProps<{
  project: {
    data: Project;
    meta?: any;
  };
  relatedProjects?: {
    data: Project[];
    meta?: any;
  };
}>();

defineOptions({
  layout: AppLayout
});

// Refs for animations
const heroRef = ref<HTMLElement>();
const contentRef = ref<HTMLElement>();
const galleryRef = ref<HTMLElement>();
const techStackRef = ref<HTMLElement>();
const featuresRef = ref<HTMLElement>();

// State
const selectedImageIndex = ref(0);
const isImageDialogOpen = ref(false);

// Store animation instances for cleanup
const animations = ref<gsap.core.Tween[]>([]);

// Computed properties
const projectData = computed(() => props.project.data);
const relatedProjectsData = computed(() => props.relatedProjects?.data || []);

const heroImage = computed(() => {
  if (projectData.value.poster_url) {
    return projectData.value.poster_url;
  }

  if (projectData.value.gallery_images && projectData.value.gallery_images.length > 0) {
    return projectData.value.gallery_images[0].large_url || projectData.value.gallery_images[0].url;
  }

  return '/assets/placeholder-project.jpg';
});

const galleryImages = computed(() => {
  if (projectData.value.gallery_images && projectData.value.gallery_images.length > 0) {
    return projectData.value.gallery_images;
  }
  return [];
});

const customerName = computed(() => {
  const customer = projectData.value.customer;
  if (customer?.display_name) return customer.display_name;
  if (customer?.company_name) return customer.company_name;
  if (customer?.full_name) return customer.full_name;
  if (customer?.first_name || customer?.last_name) {
    return `${customer.first_name || ''} ${customer.last_name || ''}`.trim();
  }
  return 'Client';
});

const projectYear = computed(() => {
  if (projectData.value.end_date) {
    return new Date(projectData.value.end_date).getFullYear();
  }
  if (projectData.value.created_at) {
    return new Date(projectData.value.created_at).getFullYear();
  }
  return new Date().getFullYear();
});

const externalLinks = computed(() => {
  const links = [];
  const project = projectData.value;

  if (project.live_url) {
    links.push({
      name: 'Live Site',
      url: project.live_url,
      icon: ArrowUpRight,
      variant: 'default' as const
    });
  }

  if (project.github_url) {
    links.push({
      name: 'GitHub',
      url: project.github_url,
      icon: Github,
      variant: 'outline' as const
    });
  }

  if (project.figma_url) {
    links.push({
      name: 'Figma',
      url: project.figma_url,
      icon: Figma,
      variant: 'outline' as const
    });
  }

  return links;
});

const statusConfig = computed(() => {
  const configs = {
    'not_started': { color: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300', icon: AlertCircle },
    'in_progress': { color: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', icon: Zap },
    'on_hold': { color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300', icon: AlertCircle },
    'completed': { color: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300', icon: CheckCircle2 },
    'cancelled': { color: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300', icon: X }
  };
  return configs[projectData.value.status as keyof typeof configs] || configs.completed;
});

// Methods
const openImageModal = (index: number) => {
  selectedImageIndex.value = index;
  isImageDialogOpen.value = true;
};

const nextImage = () => {
  selectedImageIndex.value = (selectedImageIndex.value + 1) % galleryImages.value.length;
};

const prevImage = () => {
  selectedImageIndex.value = selectedImageIndex.value === 0
    ? galleryImages.value.length - 1
    : selectedImageIndex.value - 1;
};

const getCustomerInitials = (customer: any) => {
  if (!customer) return 'CL';
  return `${customer.first_name?.[0] || ''}${customer.last_name?.[0] || ''}`.toUpperCase();
};

// Helper function to safely query elements
const safeQuerySelector = (selector: string, container?: HTMLElement): NodeListOf<Element> | null => {
  try {
    const elements = container ? container.querySelectorAll(selector) : document.querySelectorAll(selector);
    return elements.length > 0 ? elements : null;
  } catch (error) {
    console.warn(`Failed to query selector: ${selector}`, error);
    return null;
  }
};

// Helper function to check if element exists and has getBoundingClientRect
const isValidElement = (element: any): element is HTMLElement => {
  return element &&
    typeof element.getBoundingClientRect === 'function' &&
    element.nodeType === Node.ELEMENT_NODE;
};

// Animations
onMounted(async () => {
  await nextTick();

  // Wait a bit more to ensure all elements are rendered
  setTimeout(() => {
    try {
      // Hero animation
      if (heroRef.value && isValidElement(heroRef.value)) {
        const heroAnimation = gsap.fromTo(heroRef.value,
          { opacity: 0, scale: 1.1 },
          { opacity: 1, scale: 1, duration: 1.2, ease: 'power3.out' }
        );
        animations.value.push(heroAnimation);
      }

      // Content stagger animation
      if (contentRef.value && isValidElement(contentRef.value) && contentRef.value.children.length > 0) {
        const contentAnimation = gsap.fromTo(contentRef.value.children,
          { opacity: 0, y: 50 },
          {
            opacity: 1,
            y: 0,
            duration: 0.8,
            stagger: 0.2,
            ease: 'power3.out',
            delay: 0.3
          }
        );
        animations.value.push(contentAnimation);
      }

      // Tech stack animation
      if (techStackRef.value && isValidElement(techStackRef.value)) {
        const techBadges = safeQuerySelector('.tech-badge', techStackRef.value);
        if (techBadges && techBadges.length > 0) {
          const techAnimation = gsap.fromTo(techBadges,
            { opacity: 0, scale: 0.8, y: 20 },
            {
              opacity: 1,
              scale: 1,
              y: 0,
              duration: 0.5,
              stagger: 0.1,
              ease: 'back.out(1.7)',
              scrollTrigger: {
                trigger: techStackRef.value,
                start: 'top bottom-=100',
                toggleActions: 'play none none reverse'
              }
            }
          );
          animations.value.push(techAnimation);
        }
      }

      // Features animation
      if (featuresRef.value && isValidElement(featuresRef.value)) {
        const featureItems = safeQuerySelector('.feature-item', featuresRef.value);
        if (featureItems && featureItems.length > 0) {
          const featuresAnimation = gsap.fromTo(featureItems,
            { opacity: 0, x: -30 },
            {
              opacity: 1,
              x: 0,
              duration: 0.6,
              stagger: 0.1,
              ease: 'power2.out',
              scrollTrigger: {
                trigger: featuresRef.value,
                start: 'top bottom-=100',
                toggleActions: 'play none none reverse'
              }
            }
          );
          animations.value.push(featuresAnimation);
        }
      }

      // Gallery animation
      if (galleryRef.value && isValidElement(galleryRef.value)) {
        const galleryItems = safeQuerySelector('.gallery-item', galleryRef.value);
        if (galleryItems && galleryItems.length > 0) {
          const galleryAnimation = gsap.fromTo(galleryItems,
            { opacity: 0, y: 30, scale: 0.9 },
            {
              opacity: 1,
              y: 0,
              scale: 1,
              duration: 0.6,
              stagger: 0.1,
              ease: 'power2.out',
              scrollTrigger: {
                trigger: galleryRef.value,
                start: 'top bottom-=100',
                toggleActions: 'play none none reverse'
              }
            }
          );
          animations.value.push(galleryAnimation);
        }
      }

      // Parallax effect for hero image
      if (heroRef.value && isValidElement(heroRef.value)) {
        const heroImage = heroRef.value.querySelector('img');
        if (heroImage && isValidElement(heroImage)) {
          const parallaxAnimation = gsap.to(heroImage, {
            yPercent: -20,
            ease: 'none',
            scrollTrigger: {
              trigger: heroRef.value,
              start: 'top bottom',
              end: 'bottom top',
              scrub: true
            }
          });
          animations.value.push(parallaxAnimation);
        }
      }
    } catch (error) {
      console.warn('Animation setup failed:', error);
    }
  }, 100);
});

// Cleanup animations on unmount
onUnmounted(() => {
  animations.value.forEach(animation => {
    if (animation && typeof animation.kill === 'function') {
      animation.kill();
    }
  });
  ScrollTrigger.getAll().forEach(trigger => trigger.kill());
});
</script>

<template>
  <Head :title="`${projectData.name} - Portfolio Project`">
    <meta name="description" :content="projectData.short_description || projectData.description?.substring(0, 160)" />
    <meta property="og:title" :content="projectData.name" />
    <meta property="og:description" :content="projectData.short_description || projectData.description?.substring(0, 160)" />
    <meta property="og:image" :content="heroImage" />
    <meta property="og:type" content="article" />
  </Head>

  <div class="min-h-screen bg-background">
    <!-- Navigation -->
    <div class="max-w-7xl mx-auto px-4 py-6">
      <Button variant="ghost" size="sm" asChild class="group">
        <Link :href="route('projects.index')">
          <ArrowLeft class="h-4 w-4 mr-2 transition-transform group-hover:-translate-x-1" />
          All work
        </Link>
      </Button>
    </div>

    <!-- Hero Section -->
    <section ref="heroRef" class="relative overflow-hidden">
      <div class="max-w-7xl mx-auto px-4">
        <div class="relative aspect-video lg:aspect-21/9 rounded-2xl overflow-hidden bg-muted">
          <img
            :src="heroImage"
            :alt="`${projectData.name} hero image`"
            class="w-full h-full object-cover"
          />

          <!-- Overlay -->
          <div class="absolute inset-0 bg-linear-to-t from-black/60 via-black/20 to-transparent" />

          <!-- Content Overlay -->
          <div class="absolute bottom-0 left-0 right-0 p-6 lg:p-12">
            <div class="max-w-4xl">
              <div class="flex items-center space-x-3 mb-4">
                <Badge v-if="projectData.is_featured" variant="secondary" class="bg-yellow-500/90 text-white">
                  <Star class="h-3 w-3 mr-1" />
                  Featured
                </Badge>
                <Badge :class="statusConfig.color" variant="secondary">
                  <component :is="statusConfig.icon" class="h-3 w-3 mr-1" />
                  {{ projectData.status?.replace('_', ' ') }}
                </Badge>
              </div>

              <h1 class="text-3xl lg:text-5xl font-bold text-white mb-4 max-w-2xl">
                {{ projectData.name }}
              </h1>

              <p
                v-if="projectData.short_description"
                class="text-lg lg:text-xl text-white/90 max-w-2xl font-mono">
                {{ projectData.short_description }}
              </p>

              <!-- External Links -->
              <div v-if="externalLinks.length > 0" class="flex flex-wrap gap-3 mt-6">
                <Button
                  v-for="link in externalLinks"
                  :key="link.name"
                  :variant="link.variant"
                  size="lg"
                  asChild
                  class="bg-white/10 backdrop-blur-sm border-white/20 text-white hover:bg-white/20"
                >
                  <a :href="link.url" target="_blank" rel="noopener noreferrer">
                    <span>{{ link.name }}</span>
                    <component :is="link.icon" class="size-5" />
                  </a>
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Content -->
        <div ref="contentRef" class="lg:col-span-2 space-y-6">

          <!-- Project Description -->
          <Card v-if="projectData.description">
            <CardHeader>
              <CardTitle class="flex items-center sub-caption">
                <Target class="h-5 w-5 mr-2" />
                About This Project
              </CardTitle>
            </CardHeader>

            <CardContent>
              <div
                class="prose dark:prose-invert max-w-none"
                v-html="projectData.description">
              </div>
            </CardContent>
          </Card>

          <!-- Technologies -->
          <Card v-if="projectData.technologies && projectData.technologies.length > 0" ref="techStackRef">
            <CardHeader>
              <CardTitle class="flex items-center sub-caption">
                <Code class="h-5 w-5 mr-2" />
                Technologies Used
              </CardTitle>
              <CardDescription>Technical stack and tools used in this project</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="flex flex-wrap gap-2">
                <Badge
                  v-for="tech in projectData.technologies"
                  :key="tech"
                  variant="secondary"
                  class="tech-badge bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                >
                  {{ tech }}
                </Badge>
              </div>
            </CardContent>
          </Card>

          <!-- Features -->
          <Card v-if="projectData.features && projectData.features.length > 0" ref="featuresRef">
            <CardHeader>
              <CardTitle class="flex items-center sub-caption">
                <Award class="h-5 w-5 mr-2" />
                Key Features
              </CardTitle>
              <CardDescription>Highlight important project features and capabilities</CardDescription>
            </CardHeader>
            <CardContent>
              <ul class="space-y-3">
                <li
                  v-for="feature in projectData.features"
                  :key="feature"
                  class="feature-item flex items-center"
                >
                  <CheckCircle2 class="h-4 w-4 mr-3 text-green-600 shrink-0" />
                  <span>{{ feature }}</span>
                </li>
              </ul>
            </CardContent>
          </Card>

          <!-- Project Journey -->
          <Card v-if="projectData.challenges || projectData.solutions || projectData.results">
            <CardHeader>
              <CardTitle class="flex items-center sub-caption">
                <Zap class="h-5 w-5 mr-2" />
                Project Journey
              </CardTitle>
              <CardDescription>
                Challenges faced, solutions implemented, and results achieved
              </CardDescription>
            </CardHeader>

            <CardContent>
              <div class="grid grid-cols-1 gap-6">
                <div v-if="projectData.challenges">
                  <h4 class="font-medium mb-2 flex items-center sub-caption">
                    <AlertCircle class="h-4 w-4 mr-2 text-red-600" />
                    Challenges
                  </h4>
                  <p class="text-muted-foreground text-sm">{{ projectData.challenges }}</p>
                </div>

                <Separator v-if="projectData.solutions" />

                <div v-if="projectData.solutions">
                  <h4 class="font-medium mb-2 flex items-center sub-caption">
                    <Target class="h-4 w-4 mr-2 text-blue-600" />
                    Solutions
                  </h4>
                  <p class="text-muted-foreground text-sm">{{ projectData.solutions }}</p>
                </div>

                <Separator v-if="projectData.results" />

                <div v-if="projectData.results">
                  <h4 class="font-medium mb-2 flex items-center sub-caption">
                    <CheckCircle2 class="h-4 w-4 mr-2 text-green-600" />
                    Results
                  </h4>
                  <p class="text-muted-foreground text-sm">{{ projectData.results }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Client Feedback -->
          <Card v-if="projectData.client_feedback">
            <CardHeader>
              <CardTitle class="flex items-center sub-caption">
                <Heart class="h-5 w-5 mr-2" />
                Client Feedback
              </CardTitle>
            </CardHeader>
            <CardContent>
              <blockquote class="border-l-4 border-primary pl-4 italic text-lg">
                "{{ projectData.client_feedback }}"
              </blockquote>
            </CardContent>
          </Card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">

          <!-- Project Details -->
          <Card>
            <CardHeader>
              <CardTitle class="sub-caption">
                Project For
              </CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
              <!-- Client -->
              <div v-if="projectData.customer">
                <div class="font-medium">{{ customerName }}</div>
                <div class="text-sm text-muted-foreground">
                  {{ projectData.customer.full_name }}
                </div>
              </div>

              <Separator />

              <!-- Year -->
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <Calendar class="h-4 w-4 mr-2 text-muted-foreground" />
                  <span class="text-sm text-muted-foreground">Year</span>
                </div>
                <span class="text-sm font-medium font-mono">{{ projectYear }}</span>
              </div>

              <!-- Category -->
              <div v-if="projectData.category" class="flex items-center justify-between">
                <div class="flex items-center">
                  <Tag class="h-4 w-4 mr-2 text-muted-foreground" />
                  <span class="text-sm text-muted-foreground">Category</span>
                </div>

                <span class="text-sm font-medium font-mono">
                  {{ projectData.category }}
                </span>
              </div>

              <!-- Production Type -->
              <div v-if="projectData.production_type" class="flex items-center justify-between">
                <div class="flex items-center">
                  <Layers class="h-4 w-4 mr-2 text-muted-foreground" />
                  <span class="text-sm text-muted-foreground">Type</span>
                </div>

                <span class="text-sm font-medium font-mono">
                  {{ projectData.production_type }}
                </span>
              </div>
            </CardContent>
          </Card>

          <!-- Share Project -->
          <Card>
            <CardHeader>
              <CardTitle class="flex items-center">
                <Share2 class="h-5 w-5 mr-2" />
                Share Project
              </CardTitle>
            </CardHeader>
            <CardContent>
              <Button variant="outline" size="sm" class="w-full" @click="navigator.share?.({ title: projectData.name, url: window.location.href }) || navigator.clipboard?.writeText(window.location.href)">
                <Share2 class="h-4 w-4 mr-2" />
                Share This Project
              </Button>
            </CardContent>
          </Card>
        </div>
      </div>

      <!-- Project Gallery -->
      <section v-if="galleryImages.length > 0" ref="galleryRef" class="mt-20">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold mb-4 font-mono">Project Gallery</h2>
          <p class="text-muted-foreground max-w-2xl mx-auto">
            Explore the visual journey of this project through detailed screenshots and design iterations.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(image, index) in galleryImages"
            :key="image.id"
            class="gallery-item group cursor-pointer"
            @click="openImageModal(index)">
            <div class="relative aspect-video bg-muted rounded-lg overflow-hidden">
              <img
                :src="image.url"
                :alt="image.name || `Project image ${index + 1}`"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
              />
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <Button size="sm" variant="secondary">
                    <Maximize2 class="h-4 w-4 mr-2" />
                    View Full Size
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Related Projects -->
      <section v-if="relatedProjectsData.length > 0" class="mt-20">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold mb-4 font-mono">Related Projects</h2>
          <p class="text-muted-foreground max-w-2xl mx-auto">
            Discover more projects from my portfolio that showcase similar skills and technologies.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <Card
            v-for="relatedProject in relatedProjectsData"
            :key="relatedProject.uuid"
            class="group cursor-pointer hover:shadow-lg transition-shadow duration-300"
            asChild
          >
            <Link :href="route('projects.show', relatedProject.uuid)">
              <div class="aspect-video bg-muted rounded-t-lg overflow-hidden">
                <img
                  :src="relatedProject.poster_url || '/assets/placeholder-project.jpg'"
                  :alt="relatedProject.name"
                  class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                  loading="lazy"
                />
              </div>
              <CardContent class="p-4">
                <h3 class="font-semibold group-hover:text-primary transition-colors">
                  {{ relatedProject.name }}
                </h3>
                <p v-if="relatedProject.production_type" class="text-sm text-muted-foreground mt-1">
                  {{ relatedProject.production_type }}
                </p>
              </CardContent>
            </Link>
          </Card>
        </div>
      </section>
    </div>

    <!-- Image Modal -->
    <Dialog v-model:open="isImageDialogOpen">
      <DialogContent class="max-w-2xl p-0">
        <div class="relative">
          <img
            v-if="galleryImages[selectedImageIndex]"
            :src="galleryImages[selectedImageIndex].url"
            :alt="galleryImages[selectedImageIndex].name || `Project image ${selectedImageIndex + 1}`"
            class="w-full h-auto max-h-[80vh] object-contain"
          />

          <!-- Navigation -->
          <div v-if="galleryImages.length > 1" class="absolute inset-y-0 left-0 flex items-center">
            <Button
              variant="ghost"
              size="sm"
              class="ml-2 bg-black/20 hover:bg-black/40 text-white"
              @click="prevImage"
            >
              <ChevronLeft class="h-4 w-4" />
            </Button>
          </div>

          <div v-if="galleryImages.length > 1" class="absolute inset-y-0 right-0 flex items-center">
            <Button
              variant="ghost"
              size="sm"
              class="mr-2 bg-black/20 hover:bg-black/40 text-white"
              @click="nextImage"
            >
              <ChevronRight class="h-4 w-4" />
            </Button>
          </div>

          <!-- Image Info -->
          <div class="absolute bottom-0 left-0 right-0 bg-linear-to-t from-black/60 to-transparent p-4">
            <div class="text-white">
              <h3 class="font-medium">{{ galleryImages[selectedImageIndex]?.name || `Image ${selectedImageIndex + 1}` }}</h3>
              <p class="text-sm text-white/80">{{ selectedImageIndex + 1 }} of {{ galleryImages.length }}</p>
            </div>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  </div>
</template>

<style scoped>
.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
  color: inherit;
}

.prose p {
  margin-bottom: 1rem;
}

.prose ul, .prose ol {
  margin-bottom: 1rem;
}

.prose li {
  margin-bottom: 0.5rem;
}
</style>
