<script setup lang="ts">
import { index as adminCustomers } from '@/actions/App/Http/Controllers/Admin/CustomerController';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import { index as adminDownloads } from '@/actions/App/Http/Controllers/Admin/DownloadController';
import { index as adminNotifications } from '@/actions/App/Http/Controllers/Admin/NotificationController';
import { listing as adminProjects } from '@/actions/App/Http/Controllers/Admin/ProjectController';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarSeparator
} from '@/components/ui/sidebar';
import { home } from '@/routes';
import { edit as editSettings } from '@/routes/profile';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
  Bell,
  Download,
  ExternalLink,
  Folder,
  LayoutGrid,
  Settings,
  Users
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage();

const mainNavItems: NavItem[] = [
  {
    title: 'Home',
    href: home(),
    icon: ExternalLink
  }
];

const adminNavItems: NavItem[] = [
  {
    title: 'Dashboard',
    href: adminDashboard.url(),
    icon: LayoutGrid,
  },
  {
    title: 'Projects',
    href: adminProjects.url(),
    icon: Folder,
  },
  {
    title: 'Customers',
    href: adminCustomers.url(),
    icon: Users,
  },
  {
    title: 'Downloads',
    href: adminDownloads.url(),
    icon: Download,
  },
  {
    title: 'Notifications',
    href: adminNotifications.url(),
    icon: Bell,
  },
];

const footerNavItems: NavItem[] = [
  {
    title: 'Settings',
    href: editSettings().url,
    icon: Settings
  }
];
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="home()">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="mainNavItems" />
      <NavMain v-if="page.props.auth.is_admin" :items="adminNavItems" label="Admin" />
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />

      <SidebarSeparator />

      <NavUser v-if="$page.props.auth.user" />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
