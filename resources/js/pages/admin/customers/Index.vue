<script setup lang="ts">
import Navheader from "@/components/backend/Navheader.vue";
import ContactActionMenu from "@/components/contact/ContactActionMenu.vue";
import NoContactFound from "@/components/contact/NoContactFound.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import { Customer } from "@/types";
import { Head, Link, router } from "@inertiajs/vue3";
import { IconPlus, IconSearch, IconArrowRight, IconFilter, IconGrid3x3, IconList, IconUsers, IconTrendingUp, IconBriefcase, IconMail, IconPhone, IconBuilding, IconCheck, IconChevronLeft, IconChevronRight } from "@tabler/icons-vue";
import { ref, computed, onMounted, nextTick } from "vue";
import VueApexCharts from "vue3-apexcharts";
import { gsap } from "gsap";

// UI Components
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { Skeleton } from "@/components/ui/skeleton";
import { ScrollArea } from "@/components/ui/scroll-area";
import { Separator } from "@/components/ui/separator";
import { Checkbox } from "@/components/ui/checkbox";
import {useStorage} from "@vueuse/core";

// Props and options
const props = defineProps<{
  customers: { data: Customer[]; links: any; meta: any };
  filters: {
    search: string;
    status: string;
    sort_by: string;
    sort_direction: string;
  };
  stats: {
    total_customers: number;
    active_customers: number;
    customers_with_projects: number;
    customers_by_status: Record<string, number>;
    top_customers_by_projects: any[];
    growth_rate: string;
    growth_data: {
      labels: string[];
      data: number[];
    };
  };
}>();

defineOptions({
  layout: AuthLayout
});

// Reactive state
const searchQuery = ref("");
const viewMode = useStorage<"grid" | "list">("customers_view_mode", "grid");
const containerRef = ref<HTMLElement>();
const cardsRef = ref<HTMLElement[]>([]);
const loading = ref(false);
const selectedCustomers = ref<string[]>([]);

// Enhanced chart configuration with real data and better styling
const growthChartData = computed(() => ({
  chart: {
    type: 'area',
    toolbar: {
      show: false
    },
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 800,
      animateGradually: {
        enabled: true,
        delay: 150
      }
    },
    fontFamily: 'inherit',
  },
  series: [{
    name: 'New Customers',
    data: props.stats.growth_data?.data || []
  }],
  stroke: {
    curve: 'smooth',
    width: 3,
    lineCap: 'round',
    colors: ['hsl(var(--primary))']
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.45,
      opacityTo: 0.05,
      stops: [50, 100],
      colorStops: [
        {
          offset: 0,
          color: 'hsl(var(--primary))',
          opacity: 0.4
        },
        {
          offset: 100,
          color: 'hsl(var(--primary))',
          opacity: 0.1
        }
      ]
    }
  },
  grid: {
    borderColor: 'hsl(var(--border))',
    strokeDashArray: 4,
    xaxis: {
      lines: {
        show: true
      }
    },
    padding: {
      top: -20,
      right: 0,
      bottom: 0,
      left: 0
    }
  },
  xaxis: {
    categories: props.stats.growth_data?.labels || [],
    tooltip: {
      enabled: false
    },
    axisBorder: {
      show: false
    },
    labels: {
      style: {
        colors: 'hsl(var(--muted-foreground))',
        fontFamily: 'inherit'
      }
    }
  },
  yaxis: {
    labels: {
      style: {
        colors: 'hsl(var(--muted-foreground))',
        fontFamily: 'inherit'
      },
      formatter: (value) => Math.round(value)
    }
  },
  tooltip: {
    theme: 'dark',
    x: {
      show: false
    },
    y: {
      title: {
        formatter: () => 'New Customers:'
      }
    }
  }
}));

// Selection functionality
const isCustomerSelected = (customerId: string) => {
  return selectedCustomers.value.includes(customerId);
};

const toggleCustomerSelection = (customerId: string, event?: Event) => {
  if (event) {
    event.stopPropagation();
  }

  const index = selectedCustomers.value.indexOf(customerId);
  if (index === -1) {
    selectedCustomers.value.push(customerId);
  } else {
    selectedCustomers.value.splice(index, 1);
  }
};

const selectAllCustomers = () => {
  if (selectedCustomers.value.length === props.customers.data.length) {
    selectedCustomers.value = [];
  } else {
    selectedCustomers.value = props.customers.data.map(customer => customer.uuid);
  }
};

const isAllSelected = computed(() => {
  return props.customers.data.length > 0 && selectedCustomers.value.length === props.customers.data.length;
});

const isIndeterminate = computed(() => {
  return selectedCustomers.value.length > 0 && selectedCustomers.value.length < props.customers.data.length;
});

// Delete selected customers
const deleteSelectedCustomers = async () => {
  if (!selectedCustomers.value.length) return;

  try {
    loading.value = true;
    // Implement delete logic here
    console.log(`${selectedCustomers.value.length} customers deleted`);
    selectedCustomers.value = [];
  } catch (error) {
    console.error("Failed to delete customers");
  } finally {
    loading.value = false;
  }
};

// Handle status change
const updateCustomerStatus = async (customer: Customer, newStatus: string) => {
  try {
    loading.value = true;
    // Implement status update logic here
    console.log(`Customer status updated to ${newStatus}`);
  } catch (error) {
    console.error("Failed to update customer status");
  } finally {
    loading.value = false;
  }
};

// Animation functions
const animateCards = () => {
  if (cardsRef.value.length === 0) return;

  gsap.fromTo(cardsRef.value,
    {
      opacity: 0,
      y: 30,
      scale: 0.95
    },
    {
      opacity: 1,
      y: 0,
      scale: 1,
      duration: 0.6,
      stagger: 0.1,
      ease: "power2.out"
    }
  );
};

const animateHeader = () => {
  gsap.fromTo(".header-content",
    { opacity: 0, y: -20 },
    { opacity: 1, y: 0, duration: 0.8, ease: "power2.out" }
  );
};

// Lifecycle
onMounted(() => {
  nextTick(() => {
    animateHeader();
    animateCards();
  });
});

// Watch for view mode changes and re-animate
const handleViewModeChange = () => {
  console.log(`View mode changed to: ${viewMode.value}`);
  nextTick(() => {
    animateCards();
  });
};

// Helper functions
const getInitials = (customer: Customer) => {
  return customer.initials || `${customer.first_name[0]}${customer.last_name[0]}`.toUpperCase();
};

const getCustomerStatus = (customer: Customer) => {
  return customer.status || 'inactive';
};

// Pagination helpers
const goToPage = (url: string) => {
  if (url) {
    router.visit(url, {
      preserveState: true,
      preserveScroll: true
    });
  }
};
</script>

<template>
  <Head title="Customer Management" />

  <div class="min-h-screen bg-background">
    <!-- Enhanced Header -->
    <Navheader>
      <div class="header-content flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 w-full">
        <div class="flex items-center gap-4">
          <div class="flex items-start gap-3 flex-1">
            <div class="p-2 bg-primary/10 rounded-lg">
              <IconUsers class="w-6 h-6 text-primary" />
            </div>

            <div class="flex-1">
              <h1 class="text-2xl font-bold text-foreground">
                Customer Management
              </h1>

              <p class="text-sm text-muted-foreground">
                Manage and organize your customer relationships
              </p>
            </div>
          </div>
        </div>

        <!-- Stats Cards - Optimized for smaller container -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 w-full lg:w-auto">
          <Card class="bg-linear-to-br from-blue-50 to-blue-100 dark:from-blue-950 dark:to-blue-900 border-blue-200 dark:border-blue-800">
            <CardContent class="p-3">
              <div class="flex items-center gap-2">
                <IconUsers class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" />
                <div class="min-w-0">
                  <p class="text-xs font-medium text-blue-900 dark:text-blue-100">Total</p>
                  <p class="text-lg font-bold text-blue-900 dark:text-blue-100">
                    {{ stats.total_customers }}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card class="bg-linear-to-br from-green-50 to-green-100 dark:from-green-950 dark:to-green-900 border-green-200 dark:border-green-800">
            <CardContent class="p-3">
              <div class="flex items-center gap-2">
                <IconUsers class="w-4 h-4 text-green-600 dark:text-green-400 shrink-0" />
                <div class="min-w-0">
                  <p class="text-xs font-medium text-green-900 dark:text-green-100">Active</p>
                  <p class="text-lg font-bold text-green-900 dark:text-green-100">
                    {{ stats.active_customers }}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card class="bg-linear-to-br from-purple-50 to-purple-100 dark:from-purple-950 dark:to-purple-900 border-purple-200 dark:border-purple-800">
            <CardContent class="p-3">
              <div class="flex items-center gap-2">
                <IconBriefcase class="w-4 h-4 text-purple-600 dark:text-purple-400 shrink-0" />
                <div class="min-w-0">
                  <p class="text-xs font-medium text-purple-900 dark:text-purple-100">Projects</p>
                  <p class="text-lg font-bold text-purple-900 dark:text-purple-100">
                    {{ stats.customers_with_projects }}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card class="bg-linear-to-br from-amber-50 to-amber-100 dark:from-amber-950 dark:to-amber-900 border-amber-200 dark:border-amber-800">
            <CardContent class="p-3">
              <div class="flex items-center gap-2">
                <IconTrendingUp class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" />
                <div class="min-w-0">
                  <p class="text-xs font-medium text-amber-900 dark:text-amber-100">Growth</p>
                  <p class="text-lg font-bold text-amber-900 dark:text-amber-100">
                    {{ stats.growth_rate }}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </Navheader>

    <!-- Main Content with max-w-4xl -->
    <div class="container mx-auto px-4 py-8 max-w-4xl">
      <!-- Search and Controls -->
      <div class="mb-8 space-y-4">
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
          <!-- Search -->
          <div class="relative flex-1 max-w-md">
            <IconSearch class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted-foreground" />
            <Input
              v-model="searchQuery"
              placeholder="Search customers..."
              class="pl-10 bg-background/50 backdrop-blur-sm /50 focus:border-primary/50 transition-all duration-300"
            />
          </div>

          <!-- Controls -->
          <div class="flex items-center gap-3">
            <!-- Bulk Actions -->
            <div v-if="selectedCustomers.length > 0" class="flex items-center gap-2">
              <Badge variant="secondary" class="px-3 py-1">
                {{ selectedCustomers.length }} selected
              </Badge>
              <Button variant="destructive" size="sm" @click="deleteSelectedCustomers">
                Delete Selected
              </Button>
            </div>

            <!-- View Mode Toggle -->
            <div class="flex items-center gap-1 p-1 bg-muted rounded-lg">
              <Button
                variant="ghost"
                size="sm"
                :class="viewMode === 'grid' ? 'bg-background shadow-sm' : ''"
                @click="viewMode = 'grid'; handleViewModeChange()">
                <IconGrid3x3 class="w-4 h-4" />
              </Button>

              <Button
                variant="ghost"
                size="sm"
                :class="viewMode === 'list' ? 'bg-background shadow-sm' : ''"
                @click="viewMode = 'list'; handleViewModeChange()">
                <IconList class="w-4 h-4" />
              </Button>
            </div>

            <!-- Filter Dropdown -->
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="outline" size="sm">
                  <IconFilter class="w-4 h-4 mr-2" />
                  Filter
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" class="w-48">
                <DropdownMenuItem>All Customers</DropdownMenuItem>
                <DropdownMenuItem>Active Only</DropdownMenuItem>
                <DropdownMenuItem>Inactive Only</DropdownMenuItem>
                <DropdownMenuItem>Recent</DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </div>

        <!-- Select All Checkbox -->
        <div v-if="customers.data.length > 0" class="flex items-center gap-2">
          <Checkbox
            :checked="isAllSelected"
            :indeterminate="isIndeterminate"
            @click="selectAllCustomers"
          />
          <span class="text-sm text-muted-foreground">
            Select all customers on this page
          </span>
        </div>
      </div>

      <!-- Customer Stats Overview - Optimized for max-w-4xl -->
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
        <!-- Growth Chart -->
        <Card class="lg:col-span-3">
          <CardHeader class="flex flex-row items-center justify-between pb-2">
            <CardTitle class="text-lg">Customer Growth</CardTitle>
            <Badge variant="outline" class="font-mono text-xs">
              {{ stats.growth_rate }}
            </Badge>
          </CardHeader>
          <CardContent>
            <VueApexCharts
              width="100%"
              height="240"
              :options="growthChartData"
              :series="[{
                name: 'New Customers',
                data: stats.growth_data?.data || []
              }]"
            />
          </CardContent>
        </Card>

        <!-- Status Distribution -->
        <Card class="lg:col-span-2">
          <CardHeader>
            <CardTitle class="text-lg">Status Distribution</CardTitle>
          </CardHeader>
          <CardContent>
            <ScrollArea class="h-[200px]">
              <div class="space-y-4">
                <div v-for="(count, status) in stats.customers_by_status" :key="status">
                  <div class="flex items-center justify-between mb-2">
                    <Badge :variant="status === 'active' ? 'default' : 'secondary'" class="capitalize text-xs">
                      {{ status }}
                    </Badge>
                    <span class="text-sm text-muted-foreground font-medium">
                      {{ Math.round((count / stats.total_customers) * 100) }}%
                    </span>
                  </div>
                  <div class="h-2 bg-muted rounded-full overflow-hidden">
                    <div
                      class="h-full bg-primary transition-all duration-500"
                      :style="{
                        width: `${(count / stats.total_customers) * 100}%`,
                        backgroundColor: status === 'active' ? 'hsl(var(--primary))' :
                          status === 'inactive' ? 'hsl(var(--muted-foreground))' :
                          'hsl(var(--secondary))'
                      }"
                    ></div>
                  </div>
                </div>
              </div>
            </ScrollArea>
          </CardContent>
        </Card>
      </div>

      <!-- Top Customers -->
      <Card class="mb-8">
        <CardHeader>
          <CardTitle class="text-lg">Top Performing Customers</CardTitle>
          <CardDescription>Customers with the most projects</CardDescription>
        </CardHeader>
        <CardContent>
          <ScrollArea class="h-[300px]">
            <div class="space-y-4">
              <div
                v-for="(customer, index) in stats.top_customers_by_projects"
                :key="customer.uuid"
                class="relative flex items-center justify-between group p-3 rounded-lg hover:bg-muted/50 transition-colors"
              >
                <div class="flex items-center gap-3">
                  <div class="relative">
                    <Avatar class="w-10 h-10">
                      <AvatarFallback class="bg-primary/10 text-primary text-sm font-semibold">
                        {{ getInitials(customer) }}
                      </AvatarFallback>
                    </Avatar>
                    <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-xs font-bold">
                      {{ index + 1 }}
                    </div>
                  </div>
                  <div class="min-w-0 flex-1">
                    <h4 class="font-mono font-medium group-hover:text-primary transition-colors truncate">
                      {{ customer.first_name }} {{ customer.last_name }}
                    </h4>

                    <p class="text-sm text-muted-foreground truncate">
                      {{ customer.job_title }} | {{ customer.company_name || 'No company' }}
                    </p>
                  </div>
                </div>

                <div class="absolute flex items-center gap-3 right-3 top-6 transform -translate-y-1/2 group-hover:opacity-100 opacity-0 transition-opacity">
                  <div class="text-right flex items-center gap-x-4">
                    <p class="font-semibold text-lg">
                      {{ customer.projects_count }}
                    </p>

                    <p class="text-xs text-muted-foreground">
                      Projects
                    </p>
                  </div>

                  <Button
                    variant="ghost" size="icon"
                    @click="() => router.visit(route('admin.customers.show', customer.uuid))"
                    class="opacity-0 group-hover:opacity-100 transition-opacity">
                    <IconArrowRight class="w-4 h-4" />
                  </Button>
                </div>
              </div>
            </div>
          </ScrollArea>
        </CardContent>
      </Card>

      <!-- Loading State -->
      <div v-if="loading" class="space-y-4">
        <Skeleton class="h-12 w-full" />
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <Skeleton v-for="n in 6" :key="n" class="h-[240px]" />
        </div>
      </div>

      <!-- Customer Grid/List -->
      <div v-if="customers.data.length && !loading" ref="containerRef" class="transition-all duration-300">
        <!-- Enhanced Grid View -->
        <div
          v-if="viewMode === 'grid'"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <Card
            v-for="(customer, index) in customers.data"
            :key="customer.uuid"
            :ref="el => cardsRef[index] = el as HTMLElement"
            class="group hover:shadow-lg transition-all duration-300 cursor-pointer hover:border-primary/50 bg-card hover:bg-card/80"
            :class="{ 'ring-2 ring-primary/50': isCustomerSelected(customer.uuid!) }"
            @click="toggleCustomerSelection(customer.uuid!, $event)">
            <CardHeader>
              <div class="flex items-start justify-between">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                  <!-- Avatar with Selection -->
                  <div class="relative">
                    <Avatar
                      class="w-12 h-12 ring-2 ring-primary/20 group-hover:ring-primary/40 transition-all duration-300 cursor-pointer">
                      <AvatarImage v-if="customer.avatar_url" :src="customer.avatar_url" />
                      <AvatarFallback>
                        {{ isCustomerSelected(customer.uuid!) ? '' : getInitials(customer) }}
                      </AvatarFallback>
                    </Avatar>

                    <!-- Selection Indicator -->
                    <div
                      v-if="isCustomerSelected(customer.uuid!)"
                      class="absolute inset-0 bg-primary/20 rounded-full flex items-center justify-center">
                      <IconCheck class="w-6 h-6 text-primary" />
                    </div>

                    <!-- Hover Checkbox -->
                    <div
                      v-else
                      class="absolute inset-0 bg-background/80 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                      <div class="w-4 h-4 border-2 border-primary rounded"></div>
                    </div>
                  </div>

                  <div class="flex-1 min-w-0">
                    <CardTitle class="text-base truncate group-hover:text-primary transition-colors duration-300">
                      {{ customer.first_name }} {{ customer.last_name }}
                    </CardTitle>

                    <CardDescription class="truncate text-sm">
                      {{ customer.job_title || 'Customer' }}
                    </CardDescription>
                  </div>
                </div>

                <Badge
                  :variant="getCustomerStatus(customer) === 'active'
                  ? 'default' : getCustomerStatus(customer) === 'prospect'
                  ? 'outline' : 'secondary'"
                  class="text-xs shrink-0">
                  {{ getCustomerStatus(customer) }}
                </Badge>
              </div>
            </CardHeader>

            <CardContent class="space-y-3">
              <!-- Contact Information -->
              <div class="space-y-2">
                <div class="flex items-center gap-2 text-sm text-muted-foreground" v-if="customer.email">
                  <IconMail class="w-4 h-4 shrink-0" />
                  <span class="truncate">{{ customer.email }}</span>
                </div>

                <div class="flex items-center gap-2 text-sm text-muted-foreground" v-if="customer.phone_number">
                  <IconPhone class="w-4 h-4 shrink-0" />
                  <span class="truncate">{{ customer.phone_number }}</span>
                </div>

                <div class="flex items-center gap-2 text-sm text-muted-foreground" v-if="customer.company_name">
                  <IconBuilding class="w-4 h-4 shrink-0" />
                  <span class="truncate">{{ customer.company_name }}</span>
                </div>
              </div>

              <Separator />

              <!-- Action Buttons -->
              <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <Link :href="route('admin.customers.edit', customer.uuid)" as="button" class="flex-1">
                  <Button variant="outline" size="sm" class="w-full">
                    Edit
                  </Button>
                </Link>

                <Link :href="route('admin.projects.create', customer.uuid)" as="button">
                  <Button variant="outline" size="sm">
                    Project
                  </Button>
                </Link>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Enhanced List View -->
        <Card v-else class="bg-card">
          <CardContent class="p-0">
            <div class="divide-y divide-border/50">
              <div
                v-for="(customer, index) in customers.data"
                :key="customer.uuid"
                :ref="el => cardsRef[index] = el as HTMLElement"
                class="p-4 hover:bg-muted/30 transition-colors duration-200 cursor-pointer group"
                :class="{ 'bg-primary/5': isCustomerSelected(customer.uuid) }"
                @click="toggleCustomerSelection(customer.uuid, $event)">
                <div class="flex items-center justify-between gap-4">
                  <div class="flex items-center gap-4 flex-1 min-w-0">
                    <!-- Avatar with Selection -->
                    <div class="relative">
                      <Avatar
                        class="w-12 h-12 ring-2 ring-primary/20 group-hover:ring-primary/40 transition-all duration-300 cursor-pointer">
                        <AvatarImage v-if="customer.avatar_url" :src="customer.avatar_url" />
                        <AvatarFallback>
                          {{ isCustomerSelected(customer.uuid) ? '' : getInitials(customer) }}
                        </AvatarFallback>
                      </Avatar>

                      <!-- Selection Indicator -->
                      <div
                        v-if="isCustomerSelected(customer.uuid)"
                        class="absolute inset-0 bg-primary/20 rounded-full flex items-center justify-center">
                        <IconCheck class="w-6 h-6 text-primary" />
                      </div>

                      <!-- Hover Checkbox -->
                      <div
                        v-else
                        class="absolute inset-0 bg-background/80 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <div class="w-5 h-5 border-2 border-primary rounded"></div>
                      </div>
                    </div>

                    <div class="flex-1 min-w-0">
                      <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-mono font-semibold text-foreground group-hover:text-primary transition-colors duration-300 truncate">
                          {{ customer.first_name }} {{ customer.last_name }}
                        </h3>

                        <Badge
                          :variant="getCustomerStatus(customer) === 'active'
                          ? 'default' : getCustomerStatus(customer) === 'prospect'
                          ? 'outline' : 'secondary'"
                          class="capitalize text-xs shrink-0">
                          {{ getCustomerStatus(customer) }}
                        </Badge>
                      </div>

                      <p class="text-sm text-muted-foreground truncate mb-2">
                        {{ customer.job_title || 'Customer' }} | <span class="font-mono font-semibold">{{ customer.company_name || 'No company' }}</span>
                      </p>

                      <!-- Contact Details in List View -->
                      <div class="flex flex-wrap gap-4 text-xs text-muted-foreground">
                        <div class="flex items-center gap-1" v-if="customer.email">
                          <IconMail class="w-3 h-3" />
                          <span class="truncate max-w-[200px]">{{ customer.email }}</span>
                        </div>

                        <div class="flex items-center gap-1" v-if="customer.phone_number">
                          <IconPhone class="w-3 h-3" />
                          <span>{{ customer.phone_number }}</span>
                        </div>

                        <div class="flex items-center gap-1" v-if="customer.company_name">
                          <IconBuilding class="w-3 h-3" />
                          <span class="truncate max-w-[150px]">{{ customer.company_name }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 shrink-0">
                    <Link
                      :href="route('admin.customers.show', customer.uuid)"
                      as="button">
                      <Button variant="outline" size="sm">
                        View
                      </Button>
                    </Link>

                    <Link :href="route('admin.customers.edit', customer.uuid)" as="button">
                      <Button variant="outline" size="sm">
                        Edit
                      </Button>
                    </Link>

                    <Link
                      :href="route('admin.projects.create', customer.uuid)" as="button">
                      <Button variant="outline" size="sm">
                        Project
                      </Button>
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Enhanced Empty State -->
      <div v-else-if="!loading" class="text-center py-16">
        <NoContactFound>
          <div class="space-y-6">
            <div class="mx-auto w-24 h-24 bg-muted rounded-full flex items-center justify-center mb-6">
              <IconUsers class="w-12 h-12 text-muted-foreground" />
            </div>
            <div class="space-y-2">
              <h3 class="text-xl font-semibold text-foreground">
                {{ searchQuery ? 'No customers found' : 'No customers yet' }}
              </h3>
              <p class="text-muted-foreground max-w-md mx-auto">
                {{ searchQuery
                ? `No customers match "${searchQuery}". Try adjusting your search.`
                : 'Get started by adding your first customer to begin managing relationships.'
                }}
              </p>
            </div>
            <div class="flex gap-3 justify-center mt-6">
              <Button v-if="searchQuery" variant="outline" @click="searchQuery = ''">
                Clear Search
              </Button>
              <Link :href="route('admin.customers.create')" as="button">
                <Button class="bg-primary hover:bg-primary/90 text-primary-foreground">
                  <IconPlus class="w-4 h-4 mr-2" />
                  Add Your First Customer
                </Button>
              </Link>
            </div>
          </div>
        </NoContactFound>
      </div>

      <!-- Pagination -->
      <div v-if="customers.data.length > 0 && customers.meta" class="mt-8 flex items-center justify-between">
        <div class="text-sm text-muted-foreground">
          Showing {{ customers.meta.from }} to {{ customers.meta.to }} of {{ customers.meta.total }} customers
        </div>

        <div class="flex items-center gap-2">
          <!-- Previous Button -->
<!--          <Button-->
<!--            variant="outline"-->
<!--            size="sm"-->
<!--            :disabled="!customers.links.prev"-->
<!--            @click="goToPage(customers.links.prev)">-->
<!--            <IconChevronLeft class="w-4 h-4 mr-1" />-->
<!--            Previous-->
<!--          </Button>-->

          <!-- Page Numbers -->
          <div class="flex items-center gap-1">
            <template v-for="link in customers.meta.links" :key="link.label">
              <Button
                v-if="link.label !== '&laquo; Previous' && link.label !== 'Next &raquo;'"
                variant="outline"
                size="sm"
                :class="link.active ? 'bg-primary text-primary-foreground' : ''"
                :disabled="!link.url"
                @click="goToPage(link.url)"
                v-html="link.label">
              </Button>
            </template>
          </div>

          <!-- Next Button -->
<!--          <Button-->
<!--            variant="outline"-->
<!--            size="sm"-->
<!--            :disabled="!customers.links.next"-->
<!--            @click="goToPage(customers.links.next)">-->
<!--            Next-->
<!--            <IconChevronRight class="w-4 h-4 ml-1" />-->
<!--          </Button>-->
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar for horizontal scroll */
.overflow-x-auto::-webkit-scrollbar {
  height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: hsl(var(--muted));
  border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: hsl(var(--muted-foreground) / 0.3);
  border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: hsl(var(--muted-foreground) / 0.5);
}

/* Enhanced hover effects */
.group:hover .group-hover\:ring-primary\/40 {
  --tw-ring-color: hsl(var(--primary) / 0.4);
}

/* Smooth transitions for all interactive elements */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Selection ring animation */
@keyframes selection-ring {
  0% { transform: scale(0.8); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}

.ring-2.ring-primary\/50 {
  animation: selection-ring 0.2s ease-out;
}
</style>
