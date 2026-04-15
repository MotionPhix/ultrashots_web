<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, setLayoutProps } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { index } from '@/actions/App/Http/Controllers/Admin/NotificationController';

setLayoutProps({
    breadcrumbs: [{ title: 'Notifications', href: index.url() }],
});
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

defineProps<{
  notifications: any;
  unread_count: number;
}>();

const selectedNotifications = ref<number[]>([]);

const allSelected = computed(() => {
  return selectedNotifications.value.length > 0;
});

const getNotificationIcon = (type: string) => {
  const icons: Record<string, string> = {
    quote_requested: '📋',
    contact_submitted: '📧',
    project_reaction: '👍',
    project_viewed: '👁️',
  };
  return icons[type] || '🔔';
};

const getNotificationColor = (type: string) => {
  const colors: Record<string, string> = {
    quote_requested: 'bg-blue-100 text-blue-800',
    contact_submitted: 'bg-purple-100 text-purple-800',
    project_reaction: 'bg-green-100 text-green-800',
    project_viewed: 'bg-gray-100 text-gray-800',
  };
  return colors[type] || 'bg-gray-100 text-gray-800';
};

defineOptions({ layout: AdminLayout });
</script>

<template>
  <Head title="Notifications" />

  <div class="min-h-screen bg-background">
    <!-- Header -->
    <div class="border-b bg-card">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold tracking-tight">Notifications</h1>
            <p class="text-muted-foreground mt-1">
              {{ unread_count }} unread notification{{ unread_count !== 1 ? 's' : '' }}
            </p>
          </div>
          <Button v-if="unread_count > 0" variant="outline">
            Mark all as read
          </Button>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="notifications.data.length === 0" class="text-center py-12">
        <p class="text-muted-foreground">No notifications yet</p>
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="notification in notifications.data"
          :key="notification.id"
          class="flex items-start gap-4 p-4 bg-card rounded-lg border hover:border-primary/50 transition-colors cursor-pointer"
        >
          <!-- Icon -->
          <div class="text-2xl flex-shrink-0">
            {{ getNotificationIcon(notification.type) }}
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h3 class="font-semibold text-foreground">
                  {{ notification.title }}
                </h3>
                <p class="text-sm text-muted-foreground mt-1">
                  {{ notification.message }}
                </p>
                <div class="flex items-center gap-2 mt-3">
                  <Badge
                    :class="getNotificationColor(notification.type)"
                    variant="secondary"
                  >
                    {{ notification.type.replace('_', ' ') }}
                  </Badge>
                  <span class="text-xs text-muted-foreground">
                    {{ new Date(notification.created_at).toLocaleDateString() }}
                  </span>
                </div>
              </div>

              <!-- Status -->
              <div class="flex-shrink-0">
                <div
                  v-if="!notification.read_at"
                  class="w-2 h-2 bg-blue-500 rounded-full"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="notifications.links.length > 1" class="mt-8 flex justify-center gap-2">
        <!-- Add pagination controls here based on notifications.links -->
      </div>
    </div>
  </div>
</template>
