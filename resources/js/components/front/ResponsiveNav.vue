<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Home, Briefcase, Download, Mail, FileText } from 'lucide-vue-next';
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
  DropdownMenuSeparator
} from '@/components/ui/dropdown-menu';
import { cn } from '@/lib/utils';

// Navigation items
const navigationItems = [
  {
    name: 'Home',
    href: 'home',
    icon: Home,
    current: () => route().current('home')
  },
  {
    name: 'Work',
    href: 'projects.index',
    icon: Briefcase,
    current: () => route().current('projects.*') || route().current('projects.index')
  },
  {
    name: 'Downloads',
    href: 'public.downloads.index',
    icon: Download,
    current: () => route().current('public.downloads.index')
  },
  {
    name: 'Get Quote',
    href: 'quote.index',
    icon: FileText,
    current: () => route().current('quote.*')
  },
  {
    name: 'Contact',
    href: 'contact.index',
    icon: Mail,
    current: () => route().current('contact.index')
  }
];
</script>

<template>
  <header class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">

        <!-- Logo -->
        <div class="flex items-center">
          <Link
            :href="route('home')"
            class="flex items-center space-x-2 transition-opacity hover:opacity-80">
            <ApplicationLogo class="h-8 w-8" />
            <span class="text-2xl font-bold tracking-tight">
              ultrashots
            </span>
          </Link>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex md:items-center md:space-x-8">
          <Link
            v-for="item in navigationItems"
            :key="item.name"
            :href="route(item.href)"
            :class="cn(
              'relative px-3 py-2 text-sm font-medium transition-colors hover:text-foreground/80',
              item.current()
                ? 'text-foreground'
                : 'text-foreground/60'
            )">
            {{ item.name }}
            <!-- Active indicator -->
            <span
              v-if="item.current()"
              class="absolute inset-x-0 -bottom-px h-px bg-linear-to-r from-primary/0 via-primary/40 to-primary/0"
            />
          </Link>
        </nav>

        <!-- Mobile Navigation -->
        <div class="md:hidden">
          <DropdownMenu>
            <DropdownMenuTrigger class="size-9">
              <Button variant="ghost" size="icon">
                <Menu class="h-5 w-5" />
                <span class="sr-only">Toggle navigation menu</span>
              </Button>
            </DropdownMenuTrigger>

            <DropdownMenuContent align="end" class="w-56">
              <DropdownMenuItem
                v-for="item in navigationItems"
                :key="item.name"
                as-child
              >
                <Link
                  :href="route(item.href)"
                  :class="cn(
                    'flex items-center space-x-2 w-full',
                    item.current() ? 'bg-accent text-accent-foreground' : ''
                  )"
                >
                  <component :is="item.icon" class="h-4 w-4" />
                  <span>{{ item.name }}</span>
                </Link>
              </DropdownMenuItem>

              <DropdownMenuSeparator />

              <DropdownMenuItem as-child>
                <Link :href="route('quote.index')" class="flex items-center space-x-2 w-full font-medium">
                  <FileText class="h-4 w-4" />
                  <span>Get a Quote</span>
                </Link>
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>

        <!-- Desktop CTA -->
        <div class="hidden md:flex md:items-center">
          <Button as-child variant="default" size="sm">
            <Link :href="route('quote.index')">
              Get Quote
            </Link>
          </Button>
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped>
/* Custom active link animation */
@keyframes slideIn {
  from {
    transform: scaleX(0);
  }
  to {
    transform: scaleX(1);
  }
}

.active-indicator {
  animation: slideIn 0.2s ease-out;
}
</style>
