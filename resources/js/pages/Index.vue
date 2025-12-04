<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Projects from '@/components/front/Projects.vue';
import Skills from '@/components/front/Skills.vue';
import Expertise from '@/components/front/Expertise.vue';
import Subscription from '@/components/front/Subscription.vue';
import { Project, User } from '@/types';
import { onMounted, ref, computed, onUnmounted } from 'vue';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import IconEmail from '@/components/icon/IconEmail.vue';

// Shadcn Vue Components
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';

// Register ScrollTrigger plugin
gsap.registerPlugin(ScrollTrigger);

const props = defineProps<{
  projects?: Project[];
  user?: User;
}>();

// Refs for animation and interactivity
const containerRef = ref<HTMLElement>();
const heroContentRef = ref<HTMLElement>();
const contentSections = ref<HTMLElement[]>([]);
const showQuickContact = ref(false);

// Computed properties
const fullName = computed(() => {
  if (!props.user) return 'Portfolio Owner';
  return `${props.user.first_name || ''} ${props.user.last_name || ''}`.trim() || 'Portfolio Owner';
});

const firstName = computed(() => {
  return props.user?.first_name || 'there';
});

const userLocation = computed(() => {
  return props.user?.location || 'Lilongwe, Malawi';
});

const userBio = computed(() => {
  return props.user?.bio || `With more than a decade of experience, I have refined my abilities to produce visually appealing, user-centric designs that function seamlessly. I am enthusiastic about transforming creative concepts into digital realities, from the creation of intuitive user interface designs to the development of cohesive design systems and custom illustrations.`;
});

const userTitle = computed(() => {
  return props.user?.job_title || 'Graphic Designer, & Web Developer';
});

// Enhanced availability status
const availabilityStatus = computed(() => ({
  available: true,
  status: 'Available for new projects',
  responseTime: 'Usually responds within 2 hours',
  nextAvailable: null
}));

// Social media links with proper URL handling
const socialLinks = computed(() => {
  const socials = props.user?.socials || {};
  return [
    {
      name: 'LinkedIn',
      icon: IconBrandLinkedin,
      url: socials.linkedin?.startsWith('http') ? socials.linkedin : `https://linkedin.com/in/${socials.linkedin}`,
      show: !!socials.linkedin,
      color: 'hover:text-blue-600',
      description: 'Professional Network'
    },
    {
      name: 'Twitter/X',
      icon: IconBrandX,
      url: socials.twitter?.startsWith('http') ? socials.twitter : `https://x.com/${socials.twitter}`,
      show: !!socials.twitter,
      color: 'hover:text-gray-900 dark:hover:text-gray-100',
      description: 'Latest Updates'
    },
    {
      name: 'Behance',
      icon: IconBrandBehance,
      url: socials.behance?.startsWith('http') ? socials.behance : `https://be.net/${socials.behance}`,
      show: !!socials.behance,
      color: 'hover:text-blue-500',
      description: 'Creative Portfolio'
    },
    {
      name: 'Email',
      icon: IconEmail,
      url: `mailto:${props.user?.email}`,
      show: !!props.user?.email,
      color: 'hover:text-green-600',
      description: 'Get in Touch'
    }
  ].filter(link => link.show);
});

// Avatar URL with fallback
const avatarUrl = computed(() => {
  return props.user?.avatar_url || '/assets/profile_400x400.jpg';
});

// Enhanced stats data with animations
const stats = computed(() => [
  {
    label: 'Years Experience',
    value: '10+',
    icon: IconStar,
    color: 'text-amber-600',
    bgColor: 'bg-amber-100 dark:bg-amber-900/20',
    description: 'Professional experience in design'
  },
  {
    label: 'Happy Clients',
    value: '50+',
    icon: IconUsers,
    color: 'text-green-600',
    bgColor: 'bg-green-100 dark:bg-green-900/20',
    description: 'Satisfied clients worldwide'
  },
  {
    label: 'Projects',
    value: '100+',
    icon: IconBriefcase,
    color: 'text-blue-600',
    bgColor: 'bg-blue-100 dark:bg-blue-900/20',
    description: 'Completed projects'
  },
  {
    label: 'Coffee Cups',
    value: '∞',
    icon: IconRocket,
    color: 'text-purple-600',
    bgColor: 'bg-purple-100 dark:bg-purple-900/20',
    description: 'Fuel for creativity'
  }
]);

// Services/Specializations
const services = computed(() => [
  {
    title: 'UI/UX Design',
    description: 'Creating intuitive and beautiful user experiences',
    icon: IconPalette,
    color: 'text-pink-600'
  },
  {
    title: 'Web Development',
    description: 'Building responsive and modern web applications',
    icon: IconCode,
    color: 'text-blue-600'
  },
  {
    title: 'Brand Identity',
    description: 'Developing cohesive visual identity systems',
    icon: IconEye,
    color: 'text-green-600'
  }
]);

// Animation functions
function initializeAnimations() {
  const tl = gsap.timeline({ defaults: { ease: 'power2.out' } });

  if (heroContentRef.value) {
    tl.fromTo(
      heroContentRef.value.children,
      {
        opacity: 0,
        translateY: 20
      },
      {
        opacity: 1,
        translateY: 0,
        duration: 0.7,
        stagger: 0.15,
      }
    );
  }

  // Content sections animations
  contentSections.value?.forEach((section) => {
    gsap.fromTo(
      section,
      {
        opacity: 0,
        translateY: 50
      },
      {
        opacity: 1,
        translateY: 0,
        duration: 0.8,
        scrollTrigger: {
          trigger: section,
          start: 'top 80%',
          end: 'bottom 20%',
          toggleActions: 'play none none reverse'
        }
      }
    );
  });
}

// Mouse parallax effect
function handleMouseMove(e: MouseEvent) {
  if (!containerRef.value) return;

  const { clientX, clientY } = e;
  const { innerWidth, innerHeight } = window;

  const moveX = (clientX - innerWidth / 2) * 0.01;
  const moveY = (clientY - innerHeight / 2) * 0.01;

  const heroPattern = containerRef.value.querySelector('.bg-grid-pattern');
  if (heroPattern) {
    gsap.to(heroPattern, {
      x: moveX,
      y: moveY,
      duration: 1,
      ease: 'power3.out'
    });
  }
}

// Toggle quick contact panel
function toggleQuickContact() {
  showQuickContact.value = !showQuickContact.value;
}

// Lifecycle hooks
onMounted(() => {
  initializeAnimations();
  window.addEventListener('mousemove', handleMouseMove);
});

onUnmounted(() => {
  window.removeEventListener('mousemove', handleMouseMove);
  ScrollTrigger.getAll().forEach(trigger => trigger.kill());
});

defineOptions({layout: AppLayout});
</script>

<template>
  <Head :title="`${fullName} - Portfolio`">
    <meta
      name="description"
      :content="`Portfolio of ${fullName}, a creative professional specializing in design and development.`"
    />

    <meta property="og:title" :content="`${fullName} - Portfolio`"/>
    <meta property="og:description" :content="userBio.substring(0, 160)"/>
    <meta property="og:type" content="website"/>
    <meta property="og:image" :content="avatarUrl"/>
  </Head>

  <!-- Hero Section -->
  <section
    ref="containerRef"
    class="relative flex items-center justify-center overflow-x-hidden">

    <!-- Subtle background -->
    <div class="absolute inset-0">
      <div class="absolute inset-0 bg-grid-pattern opacity-[0.03] dark:opacity-[0.05]"></div>
      <div class="absolute inset-0 bg-linear-to-b from-transparent via-primary/5 to-transparent"></div>
    </div>

    <!-- Main content -->
    <div class="relative z-10 w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div
        ref="heroContentRef"
        class="flex flex-col items-center md:flex-row md:items-center md:justify-between md:gap-12 text-center md:text-left">

        <!-- Content Side -->
        <div class="flex flex-col items-center md:items-start space-y-8 flex-1 order-2 sm:order-1">

          <!-- Availability badge -->
          <div
            class="animate-fade-in font-mono">
            {{ availabilityStatus.status }}
          </div>

          <!-- Main heading -->
          <div class="space-y-3">
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">
              Hi, I'm <span class="text-primary">{{ firstName }}</span>
            </h1>

            <p class="text-xl sm:text-2xl text-muted-foreground font-light font-mono">
              {{ userTitle }}
            </p>

            <p class="flex items-center justify-center md:justify-start gap-2 text-sm text-muted-foreground/80">
              <IconMapPin class="size-4" />
              {{ userLocation }}
            </p>
          </div>

          <!-- Action buttons -->
          <div class="flex flex-col sm:flex-row items-center md:items-start gap-4 sm:gap-6 w-full max-w-xl pt-6">
            <Link
              :href="route('projects.index')"
              class="group relative w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-lg font-semibold text-white transition-all duration-300 bg-primary hover:bg-primary/90 rounded overflow-hidden">

              <span class="relative z-10 flex items-center gap-2">
                My Work
                <IconArrowRight class="size-5 transition-transform group-hover:translate-x-1" />
              </span>

              <div class="absolute inset-0 bg-linear-to-r from-primary to-primary/80 opacity-0 group-hover:opacity-100 transition-opacity" />
            </Link>

            <a
              href="#"
              class="group w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-lg font-semibold transition-all duration-300 border-2 border-primary/20 hover:border-primary/30 rounded bg-background hover:bg-primary/5">
              <IconDownload class="size-5 transition-transform group-hover:-translate-y-1" />
              My CV
            </a>
          </div>

          <!-- Social links -->
          <div class="flex items-center justify-center md:justify-start gap-4">
            <a
              v-for="link in socialLinks"
              :key="link.name"
              :href="link.url"
              target="_blank"
              rel="noopener noreferrer"
              :title="link.name"
              class="p-2.5 rounded-lg transition-all duration-300 hover:bg-primary/10 hover:scale-105"
              :class="link.color">
              <component :is="link.icon" class="size-5" />
            </a>
          </div>
        </div>

        <!-- Profile Image Side -->
        <div class="relative mb-8 md:mb-0 order-1 sm:order-2">
          <div class="absolute inset-0 bg-linear-to-r from-primary/20 to-secondary/20 rounded-full blur-2xl"></div>
          <Avatar
            class="relative rounded-2xl sm:rounded-3xl size-32 sm:size-64 md:size-72 ring-4 ring-background shadow-2xl">
            <AvatarImage :src="avatarUrl" :alt="fullName" />
            <AvatarFallback>{{ firstName.charAt(0) }}</AvatarFallback>
          </Avatar>
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section class="py-20 bg-muted/30 overflow-x-hidden">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid gap-12 md:grid-cols-[1fr_2fr] items-start">
        <!-- Stats -->
        <div class="grid grid-cols-2 gap-4">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="relative group p-4 rounded-lg bg-background border hover:border-primary/20 transition-colors">
            <div class="flex flex-col gap-1">
              <span :class="['text-2xl font-bold', stat.color]">{{ stat.value }}</span>
              <span class="text-sm text-muted-foreground">{{ stat.label }}</span>
            </div>
          </div>
        </div>

        <!-- About Content -->
        <div class="space-y-6">
          <div class="space-y-2">
            <h2 class="text-3xl font-semibold tracking-tight">About Me</h2>
            <p class="text-muted-foreground leading-relaxed">
              {{ userBio }}
            </p>
          </div>

          <!-- Services/Specializations -->
          <div class="space-y-4">
            <h3 class="text-xl font-medium font-mono">What I Do</h3>
            <div class="grid gap-4 sm:grid-cols-2">
              <div
                v-for="service in services"
                :key="service.title"
                class="p-4 rounded-lg border bg-background/50 hover:bg-background transition-colors">
                <div class="flex items-start gap-4">
                  <div :class="['p-2 rounded-md', service.color.replace('text', 'bg') + '/10']">
                    <component
                      :is="service.icon"
                      :class="['size-5', service.color]"
                    />
                  </div>
                  <div class="space-y-1">
                    <h4 class="text-sm font-medium">{{ service.title }}</h4>
                    <p class="text-sm text-muted-foreground">
                      {{ service.description }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Enhanced Content Sections -->
  <div id="content" class="space-y-24 pt-20 overflow-x-hidden">
    <!-- Featured Projects -->
    <section
      id="projects"
      ref="el => contentSections[0] = el as HTMLElement"
      class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="space-y-4 mb-12">
        <Badge variant="outline">Featured Work</Badge>
        <h2 class="text-2xl font-semibold tracking-tight">
          My Latest Projects
        </h2>
        <p class="text-lg text-muted-foreground max-w-2xl">
          A showcase of my recent projects and creative solutions
        </p>
      </div>
      <Projects
        :projects="projects"
        :small-columns="false"
      />
    </section>

    <!-- Skills section -->
    <section
      ref="el => contentSections[1] = el as HTMLElement"
      class="bg-muted/30 py-20">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-4 mb-12">
          <Badge variant="outline">Expertise</Badge>
          <h2 class="text-2xl font-semibold tracking-tight">
            Skills & Technologies
          </h2>
          <p class="text-lg text-muted-foreground max-w-2xl">
            Tools and technologies I use to bring ideas to life
          </p>
        </div>
        <Skills/>
      </div>
    </section>

    <!-- Experience section -->
    <section
      ref="el => contentSections[2] = el as HTMLElement"
      class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="space-y-4 mb-12">
        <Badge variant="outline">Journey</Badge>
        <h2 class="text-2xl font-semibold tracking-tight">
          Professional Experience
        </h2>
        <p class="text-lg text-muted-foreground max-w-2xl">
          My journey through design and development
        </p>
      </div>
      <Expertise/>
    </section>

    <!-- Newsletter -->
    <section
      ref="el => contentSections[3] = el as HTMLElement"
      class="bg-muted/30 py-20">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
          <Badge variant="outline" class="mb-4">
            Stay Connected
          </Badge>
        </div>
        <Subscription/>
      </div>
    </section>
  </div>

  <!-- Quick Contact Modal/Panel -->
  <Transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95">
    <div
      v-if="showQuickContact"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-background/80 backdrop-blur-sm"
      @click.self="toggleQuickContact">
      <Card class="w-full max-w-md">
        <CardContent class="p-6 space-y-4">
          <div class="text-center">
            <h3 class="text-xl font-semibold">Get in Touch</h3>
            <p class="text-muted-foreground">Let's discuss your next project</p>
          </div>

          <div class="space-y-3">
            <Button
              v-for="link in socialLinks.filter(l => ['Email', 'LinkedIn'].includes(l.name))"
              :key="link.name"
              variant="outline"
              class="w-full justify-start gap-3"
              as-child>
              <a :href="link.url" target="_blank" rel="noopener noreferrer">
                <component :is="link.icon" class="w-4 h-4"/>
                {{ link.name }}
              </a>
            </Button>
          </div>

          <Button
            variant="ghost"
            class="w-full"
            @click="toggleQuickContact">
            Close
          </Button>
        </CardContent>
      </Card>
    </div>
  </Transition>
</template>

<style scoped>
.bg-grid-pattern {
  background-image: radial-gradient(
    circle,
    rgba(0, 0, 0, 0.1) 1px,
    transparent 1px
  );
  background-size: 24px 24px;
}

@media (prefers-color-scheme: dark) {
  .bg-grid-pattern {
    background-image: radial-gradient(
      circle,
      rgba(255, 255, 255, 0.1) 1px,
      transparent 1px
    );
  }
}

@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out forwards;
}

/* Add smooth scroll behavior */
html {
  scroll-behavior: smooth;
}

/* Custom button hover effect */
.custom-button {
  position: relative;
  overflow: hidden;
}

.custom-button::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
  transform: translateX(-100%);
  transition: transform 0.6s;
}

.custom-button:hover::after {
  transform: translateX(100%);
}
</style>
