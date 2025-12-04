<script setup lang="ts">
import AuthLayout from "@/layouts/AuthLayout.vue";
import Navheader from "@/components/backend/Navheader.vue";
import {Head, Link, router} from "@inertiajs/vue3";
import {
  IconArrowLeft,
  IconUser,
  IconBuilding,
  IconMail,
  IconPhone,
  IconGlobe,
  IconMapPin,
  IconFileText,
  IconPencil,
  IconPlus,
  IconBriefcase,
  IconCalendar,
  IconTrash,
  IconExternalLink
} from "@tabler/icons-vue";
import {ref, onMounted, nextTick, computed} from "vue";
import {gsap} from "gsap";
import {Customer} from "@/types";

// UI Components
import {Card, CardContent, CardDescription, CardHeader, CardTitle} from "@/components/ui/card";
import {Button} from "@/components/ui/button";
import {Badge} from "@/components/ui/badge";
import {Separator} from "@/components/ui/separator";
import {Avatar, AvatarFallback, AvatarImage} from "@/components/ui/avatar";
import {ScrollArea} from "@/components/ui/scroll-area";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger
} from "@/components/ui/alert-dialog";

// Props - Handle both direct customer object and wrapped data structure
const props = defineProps<{
  customer?: {
    data: Customer;
    meta?: any;
  };
}>();

// Get customer data from either direct prop or nested data structure
const customerData = computed(() => {
  return props.customer.data;
});

console.log("Customer data:", customerData.value);

// Reactive refs
const contentRef = ref<HTMLElement>();
const isDeleting = ref(false);

// Computed properties
const hasAddress = computed(() => {
  const addr = customerData.value?.address;
  return addr && (addr.street || addr.city || addr.state || addr.country || addr.postal_code);
});

const hasContactInfo = computed(() => {
  return customerData.value?.email || customerData.value?.phone_number || customerData.value?.website;
});

const projectStats = computed(() => {
  const projects = customerData.value?.projects || [];
  return {
    total: customerData.value?.total_projects || projects.length,
    active: projects.filter(p => p.status === 'active' || p.status === 'in_progress').length,
    completed: projects.filter(p => p.status === 'completed').length,
    pending: projects.filter(p => p.status === 'pending' || p.status === 'not_started').length,
    on_hold: projects.filter(p => p.status === 'on_hold').length,
    cancelled: projects.filter(p => p.status === 'cancelled').length,
  };
});

// Helper functions
const getInitials = (customer: Customer) => {
  if (customer.initials) return customer.initials;
  const firstName = customer.first_name || '';
  const lastName = customer.last_name || '';
  return `${firstName[0] || ''}${lastName[0] || ''}`.toUpperCase();
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const getStatusColor = (status: string) => {
  switch (status) {
    case 'active':
    case 'in_progress':
      return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
    case 'completed':
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
    case 'pending':
    case 'not_started':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
    case 'on_hold':
      return 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400';
    case 'cancelled':
      return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
  }
};

const getPriorityColor = (priority: string) => {
  switch (priority) {
    case 'urgent':
      return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
    case 'high':
      return 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400';
    case 'medium':
      return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
    case 'low':
      return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
    default:
      return 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
  }
};

// Delete customer
const deleteCustomer = async () => {
  if (isDeleting.value || !customerData.value) return;

  isDeleting.value = true;

  router.delete(route('admin.customers.destroy', customerData.value.uuid), {
    onSuccess: () => {
      // Redirect handled by controller
    },
    onError: () => {
      isDeleting.value = false;
    },
    onFinish: () => {
      isDeleting.value = false;
    }
  });
};

// Animation
const animateContent = () => {
  if (contentRef.value) {
    gsap.fromTo(contentRef.value.children,
      {opacity: 0, y: 30},
      {opacity: 1, y: 0, duration: 0.6, stagger: 0.1, ease: "power2.out"}
    );
  }
};

onMounted(() => {
  nextTick(() => {
    animateContent();
  });
});

defineOptions({
  layout: AuthLayout,
});
</script>

<template>
  <Head :title="`${customerData?.first_name} ${customerData?.last_name} - Customer Details`"/>

  <div v-if="customerData" class="min-h-screen bg-background">
    <!-- Header -->
    <Navheader>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 w-full">
        <div class="flex items-center gap-4">
          <div>
            <Link :href="route('admin.customers.index')" as="button">
              <Button variant="link" class="px-0 text-muted-foreground hover:text-foreground">
                <IconArrowLeft class="size-4" />
                All customers
              </Button>
            </Link>

            <article class="flex gap-x-4 items-center">
              <h1 class="text-2xl font-bold text-foreground font-mono">
                {{ customerData.first_name }} {{ customerData.last_name }}
              </h1>

              <div>
                <span
                  :class="getStatusColor(customerData.status)"
                  class="text-xs capitalize flex items-center font-semibold px-2 py-1 rounded">
                  {{ customerData.status }}
                </span>
              </div>
            </article>

            <div class="flex items-center gap-2 mt-1 text-sm text-muted-foreground">
              <p>
                {{ customerData.job_title || 'Customer' }}
              </p>

              <span v-if="customerData.company_name" class="text-muted-foreground">|</span>

              <p v-if="customerData.company_name" class="font-mono font-semibold">
                {{ customerData.company_name }}
              </p>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <Link :href="route('admin.projects.create', { customer: customerData.uuid })" as="button">
            <Button variant="outline">
              <IconPlus />
              New Project
            </Button>
          </Link>

          <Link
            :href="route('admin.customers.edit', customerData.uuid)"
            as="button">
            <Button variant="default">
              <IconPencil />
              Edit
            </Button>
          </Link>
        </div>
      </div>
    </Navheader>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 max-w-4xl">
      <div ref="contentRef" class="space-y-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <Card>
            <CardContent class="p-4">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                  <IconBriefcase class="w-5 h-5 text-blue-600 dark:text-blue-400"/>
                </div>

                <div>
                  <p class="text-sm text-muted-foreground">Total Projects</p>
                  <p class="text-2xl font-bold">{{ projectStats.total }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="p-4">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                  <IconBriefcase class="w-5 h-5 text-green-600 dark:text-green-400"/>
                </div>

                <div>
                  <p class="text-sm text-muted-foreground">Active</p>
                  <p class="text-2xl font-bold">{{ projectStats.active }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="p-4">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                  <IconBriefcase class="w-5 h-5 text-purple-600 dark:text-purple-400"/>
                </div>

                <div>
                  <p class="text-sm text-muted-foreground">Completed</p>
                  <p class="text-2xl font-bold">{{ projectStats.completed }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="p-4">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                  <IconCalendar class="w-5 h-5 text-amber-600 dark:text-amber-400"/>
                </div>
                <div>
                  <p class="text-sm text-muted-foreground">Member Since</p>
                  <p class="text-sm font-semibold">{{ formatDate(customerData.created_at) }}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Customer Information Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Contact Information -->
          <Card v-if="hasContactInfo">
            <CardHeader>
              <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                  <IconMail class="w-5 h-5 text-green-600 dark:text-green-400"/>
                </div>
                <div>
                  <CardTitle class="text-lg">Contact Information</CardTitle>
                  <CardDescription>How to reach this customer</CardDescription>
                </div>
              </div>
            </CardHeader>
            <CardContent class="space-y-4">
              <div v-if="customerData.email" class="flex items-center gap-3 p-3 bg-muted/50 rounded-lg">
                <IconMail class="w-5 h-5 text-muted-foreground shrink-0"/>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium">Email</p>
                  <a :href="`mailto:${customerData.email}`" class="text-sm text-primary hover:underline truncate block">
                    {{ customerData.email }}
                  </a>
                </div>
              </div>

              <div v-if="customerData.phone_number" class="flex items-center gap-3 p-3 bg-muted/50 rounded-lg">
                <IconPhone class="w-5 h-5 text-muted-foreground shrink-0"/>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium">Phone</p>
                  <a :href="`tel:${customerData.phone_number}`" class="text-sm text-primary hover:underline">
                    {{ customerData.phone_number }}
                  </a>
                </div>
              </div>

              <div v-if="customerData.website" class="flex items-center gap-3 p-3 bg-muted/50 rounded-lg">
                <IconGlobe class="w-5 h-5 text-muted-foreground shrink-0"/>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium">Website</p>
                  <a :href="customerData.website" target="_blank"
                     class="text-sm text-primary hover:underline truncate block flex items-center gap-1">
                    {{ customerData.website }}
                    <IconExternalLink class="w-3 h-3"/>
                  </a>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Address Information -->
          <Card v-if="hasAddress">
            <CardHeader>
              <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                  <IconMapPin class="w-5 h-5 text-amber-600 dark:text-amber-400"/>
                </div>
                <div>
                  <CardTitle class="text-lg">Address</CardTitle>
                  <CardDescription>Physical location</CardDescription>
                </div>
              </div>
            </CardHeader>
            <CardContent>
              <div class="p-3 bg-muted/50 rounded-lg">
                <p class="text-sm whitespace-pre-line">{{ customerData.formatted_address }}</p>
              </div>
            </CardContent>
          </Card>

          <!-- Company Information -->
          <Card v-if="customerData.company_name">
            <CardHeader>
              <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                  <IconBuilding class="w-5 h-5 text-purple-600 dark:text-purple-400"/>
                </div>
                <div>
                  <CardTitle class="text-lg">Company Information</CardTitle>
                  <CardDescription>Organization details</CardDescription>
                </div>
              </div>
            </CardHeader>
            <CardContent>
              <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 bg-muted/50 rounded-lg">
                  <IconBuilding class="w-5 h-5 text-muted-foreground shrink-0"/>
                  <div>
                    <p class="text-sm font-medium">Company</p>
                    <p class="text-sm">{{ customerData.company_name }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-muted/50 rounded-lg">
                  <IconUser class="w-5 h-5 text-muted-foreground shrink-0"/>
                  <div>
                    <p class="text-sm font-medium">Position</p>
                    <p class="text-sm">{{ customerData.job_title || 'Not specified' }}</p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Notes -->
          <Card v-if="customerData.notes" class="lg:col-span-2">
            <CardHeader>
              <div class="flex items-center gap-3">
                <div class="p-2 bg-slate-100 dark:bg-slate-900/30 rounded-lg">
                  <IconFileText class="w-5 h-5 text-slate-600 dark:text-slate-400"/>
                </div>
                <div>
                  <CardTitle class="text-lg">Notes</CardTitle>
                  <CardDescription>Additional information</CardDescription>
                </div>
              </div>
            </CardHeader>

            <CardContent>
              <div class="p-4 bg-muted/50 rounded-lg">
                <p
                  class="text-sm whitespace-pre-wrap"
                  v-html="customerData.notes">
                </p>
              </div>
            </CardContent>
          </Card>
        </div>

        <!--        <Avatar class="w-16 h-16 ring-2 ring-primary/20">-->
        <!--          <AvatarImage v-if="customerData.avatar_url" :src="customerData.avatar_url" />-->
        <!--          <AvatarFallback class="bg-linear-to-br from-primary/20 to-primary/10 text-primary font-semibold text-lg">-->
        <!--            {{ getInitials(customerData) }}-->
        <!--          </AvatarFallback>-->
        <!--        </Avatar>-->

        <!-- Projects Section -->
        <Card>
          <CardHeader>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                  <IconBriefcase class="w-5 h-5 text-blue-600 dark:text-blue-400"/>
                </div>
                <div>
                  <CardTitle class="text-lg">Projects</CardTitle>
                  <CardDescription>All projects for this customer</CardDescription>
                </div>
              </div>
              <Link :href="route('admin.projects.create', { customer: customerData.uuid })" as="button">
                <Button size="sm">
                  <IconPlus class="w-4 h-4 mr-2"/>
                  New Project
                </Button>
              </Link>
            </div>
          </CardHeader>
          <CardContent>
            <div v-if="customerData.projects && customerData.projects.length > 0">
              <ScrollArea class="h-[400px]">
                <div class="space-y-4">
                  <div
                    v-for="project in customerData.projects"
                    :key="project.id"
                    class="flex items-center justify-between p-4 border rounded-lg hover:bg-muted/50 transition-colors"
                  >
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <h4 class="font-medium truncate">{{ project.name }}</h4>
                        <Badge :class="getStatusColor(project.status)" class="text-xs">
                          {{ project.status.replace('_', ' ') }}
                        </Badge>
                        <Badge :class="getPriorityColor(project.priority)" class="text-xs" variant="outline">
                          {{ project.priority }}
                        </Badge>
                        <Badge v-if="project.is_overdue" variant="destructive" class="text-xs">
                          Overdue
                        </Badge>
                      </div>
                      <p class="text-sm text-muted-foreground truncate mb-2">
                        {{ project.short_description || project.description || 'No description' }}</p>
                      <div class="flex items-center gap-4 text-xs text-muted-foreground flex-wrap">
                        <span>Type: {{ project.production_type }}</span>
                        <span>Category: {{ project.category }}</span>
                        <span v-if="project.progress">Progress: {{ project.progress }}%</span>
                        <span v-if="project.budget">Budget: ${{ parseFloat(project.budget).toLocaleString() }}</span>
                      </div>
                      <div class="flex items-center gap-4 mt-1 text-xs text-muted-foreground">
                        <span>Start: {{ formatDate(project.start_date) }}</span>
                        <span>End: {{ formatDate(project.end_date) }}</span>
                        <span v-if="project.duration">Duration: {{ project.duration }} days</span>
                      </div>
                    </div>
                    <div class="flex items-center gap-2 ml-4">
                      <Link :href="route('admin.projects.show', project.uuid)" as="button">
                        <Button variant="outline" size="sm">
                          View
                        </Button>
                      </Link>
                      <Link :href="route('admin.projects.edit', project.uuid)" as="button">
                        <Button variant="outline" size="sm">
                          Edit
                        </Button>
                      </Link>
                    </div>
                  </div>
                </div>
              </ScrollArea>
            </div>
            <div v-else class="text-center py-8">
              <div class="mx-auto w-16 h-16 bg-muted rounded-full flex items-center justify-center mb-4">
                <IconBriefcase class="w-8 h-8 text-muted-foreground"/>
              </div>
              <h3 class="text-lg font-semibold mb-2">No projects yet</h3>
              <p class="text-muted-foreground mb-4">This customer doesn't have any projects yet.</p>
              <Link :href="route('admin.projects.create', { customer: customerData.uuid })" as="button">
                <Button>
                  <IconPlus class="w-4 h-4 mr-2"/>
                  Create First Project
                </Button>
              </Link>
            </div>
          </CardContent>
        </Card>

        <!-- Danger Zone -->
        <Card class="border-destructive/20">
          <CardHeader>
            <CardTitle class="text-lg text-destructive">Danger Zone</CardTitle>
            <CardDescription>Irreversible and destructive actions</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="flex items-center justify-between p-4 border border-destructive/20 rounded-lg">
              <div>
                <h4 class="font-medium">Delete Customer</h4>
                <p class="text-sm text-muted-foreground">
                  Permanently delete this customer and all associated data.
                </p>
              </div>
              <AlertDialog>
                <AlertDialogTrigger as-child>
                  <Button variant="destructive" :disabled="projectStats.total > 0">
                    <IconTrash class="w-4 h-4 mr-2"/>
                    Delete Customer
                  </Button>
                </AlertDialogTrigger>
                <AlertDialogContent>
                  <AlertDialogHeader>
                    <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                    <AlertDialogDescription>
                      This action cannot be undone. This will permanently delete the customer
                      "{{ customerData.first_name }} {{ customerData.last_name }}" and all associated data.
                    </AlertDialogDescription>
                  </AlertDialogHeader>
                  <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction @click="deleteCustomer" :disabled="isDeleting">
                      {{ isDeleting ? 'Deleting...' : 'Delete Customer' }}
                    </AlertDialogAction>
                  </AlertDialogFooter>
                </AlertDialogContent>
              </AlertDialog>
            </div>
            <p v-if="projectStats.total > 0" class="text-xs text-muted-foreground mt-2">
              Cannot delete customer with existing projects. Please delete or reassign all projects first.
            </p>
          </CardContent>
        </Card>
      </div>
    </div>
  </div>

  <!-- Loading/Error State -->
  <div v-else class="min-h-screen bg-background flex items-center justify-center">
    <div class="text-center">
      <h2 class="text-xl font-semibold mb-2">Customer not found</h2>
      <p class="text-muted-foreground mb-4">The requested customer could not be loaded.</p>
      <Link :href="route('admin.customers.index')" as="button">
        <Button>
          <IconArrowLeft class="w-4 h-4 mr-2"/>
          Back to Customers
        </Button>
      </Link>
    </div>
  </div>
</template>

<style scoped>
/* Custom animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

/* Smooth transitions */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Link hover effects */
a:hover {
  text-decoration: underline;
}

/* Card hover effects */
.hover\:bg-muted\/50:hover {
  background-color: hsl(var(--muted) / 0.5);
}
</style>
