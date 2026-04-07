<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Home, Briefcase, Download, Mail, FileText, Menu } from 'lucide-vue-next';
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

import { home } from '@/routes/index';
import { index as projectsIndex } from '@/routes/projects';
import { index as publicDownloadsIndex } from '@/routes/public/downloads';
import { index as quoteIndex } from '@/routes/quote';
import { load as contactLoad } from '@/routes/contact';

const $page = usePage()

// Navigation items
const navigationItems = [
  {
    name: 'Home',
    href: () => home().url,
    icon: Home,
    current: () => $page.url === home().url
  },
  {
    name: 'Work',
    href: () => projectsIndex().url,
    icon: Briefcase,
    current: () => $page.url.startsWith(projectsIndex().url)
  },
  {
    name: 'Downloads',
    href: () => publicDownloadsIndex().url,
    icon: Download,
    current: () => $page.url.startsWith(publicDownloadsIndex().url)
  },
  {
    name: 'Get Quote',
    href: () => quoteIndex().url,
    icon: FileText,
    current: () => $page.url.startsWith(quoteIndex().url)
  },
  {
    name: 'Contact',
    href: () => contactLoad().url,
    icon: Mail,
    current: () => $page.url.startsWith(contactLoad().url)
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
            :href="home().url"
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
            :href="item.href()"
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
                  :href="item.href()"
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
                <Link :href="quoteIndex().url" class="flex items-center space-x-2 w-full font-medium">
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
            <Link :href="quoteIndex().url">
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
