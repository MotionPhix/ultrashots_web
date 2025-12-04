<script setup lang="ts">
import AddressInput from "@/components/AddressInput.vue";
import Spinner from "@/components/Spinner.vue";
import InputError from "@/components/InputError.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import Navheader from "@/components/backend/Navheader.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { IconArrowLeft, IconUser, IconBuilding, IconMail, IconPhone, IconGlobe, IconMapPin, IconFileText, IconEye } from "@tabler/icons-vue";
import { ref, onMounted, nextTick, computed } from "vue";
import { gsap } from "gsap";
import { Customer } from "@/types";
import { SaveIcon as IconSave } from "lucide-vue-next";

// UI Components
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";

// Props - Handle CustomerResource structure
const props = defineProps<{
  customer: {
    data: Customer;
  };
}>();

// Form setup with existing customer data
const form = useForm({
  first_name: props.customer.data.first_name || "",
  last_name: props.customer.data.last_name || "",
  job_title: props.customer.data.job_title || "",
  company_name: props.customer.data.company_name || "",
  email: props.customer.data.email || "",
  phone_number: props.customer.data.phone_number || "",
  website: props.customer.data.website || "",
  address: {
    street: props.customer.data.address?.street || "",
    city: props.customer.data.address?.city || "",
    state: props.customer.data.address?.state || "",
    country: props.customer.data.address?.country || "",
    postal_code: props.customer.data.address?.postal_code || "",
    type: "business", // Default type for AddressInput
  },
  notes: props.customer.data.notes || "",
  status: props.customer.data.status || "active",
});

// Reactive refs
const formRef = ref<HTMLElement>();
const isSubmitting = ref(false);

// Computed properties
const hasChanges = computed(() => {
  return form.isDirty;
});

const getInitials = (customer: Customer) => {
  return customer.initials || `${customer.first_name[0]}${customer.last_name[0]}`.toUpperCase();
};

// Form submission
function onSubmit() {
  if (isSubmitting.value) return;

  isSubmitting.value = true;

  form.transform((data) => {
    let formData = {
      first_name: data.first_name,
      last_name: data.last_name,
      job_title: data.job_title,
      company_name: data.company_name,
      email: data.email,
      phone_number: data.phone_number,
      website: data.website,
      notes: data.notes,
      status: data.status,
    };

    // Include address only if any field is filled (excluding type)
    if (data.address.street || data.address.city || data.address.state || data.address.country || data.address.postal_code) {
      // Remove the type field as it's only for AddressInput component
      const { type, ...addressData } = data.address;
      formData.address = addressData;
    }

    return formData;
  });

  form.patch(route("admin.customers.update", props.customer.data.uuid), {
    preserveScroll: true,
    onSuccess: () => {
      // Success handled by redirect in controller
    },
    onError: () => {
      isSubmitting.value = false;
    },
    onFinish: () => {
      isSubmitting.value = false;
    },
  });
}

// Animation
const animateForm = () => {
  if (formRef.value) {
    gsap.fromTo(formRef.value.children,
      { opacity: 0, y: 30 },
      { opacity: 1, y: 0, duration: 0.6, stagger: 0.1, ease: "power2.out" }
    );
  }
};

onMounted(() => {
  nextTick(() => {
    animateForm();
  });
});

defineOptions({
  layout: AuthLayout,
});
</script>

<template>
  <Head :title="`Edit ${customer.data.first_name} ${customer.data.last_name}`" />

  <div class="min-h-screen bg-background">
    <!-- Header -->
    <Navheader>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 w-full">
        <div class="flex items-center gap-4">
<!--          <Avatar class="w-12 h-12 ring-2 ring-primary/20">-->
<!--            <AvatarImage v-if="customer.data.avatar_url" :src="customer.data.avatar_url" />-->
<!--            <AvatarFallback class="bg-linear-to-br from-primary/20 to-primary/10 text-primary font-semibold">-->
<!--              {{ getInitials(customer.data) }}-->
<!--            </AvatarFallback>-->
<!--          </Avatar>-->

          <div>
            <Link :href="route('admin.customers.index')" as="button">
              <Button variant="link" class="px-0 text-muted-foreground hover:text-foreground">
                <IconArrowLeft class="size-4" />
                All customers
              </Button>
            </Link>

            <h1 class="text-2xl font-bold text-foreground font-mono">
              Edit {{ customer.data.first_name }} {{ customer.data.last_name }}
            </h1>

            <div class="flex items-center gap-2 mt-1">
              <p class="text-sm text-muted-foreground">
                Update customer information and details
              </p>

              <Badge
                :variant="customer.data.status === 'active'
                ? 'default' : customer.data.status === 'prospect'
                ? 'outline' : 'secondary'"
                class="text-xs">
                {{ customer.data.status }}
              </Badge>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <Link :href="route('admin.customers.show', customer.data.uuid)" as="button">
            <Button variant="outline">
              <IconEye class="w-4 h-4 mr-2" />
              Details
            </Button>
          </Link>

          <Button
            type="submit"
            @click.prevent="onSubmit"
            :disabled="form.processing || isSubmitting || !hasChanges"
            class="bg-primary hover:bg-primary/90 text-primary-foreground shadow-lg hover:shadow-xl transition-all duration-300">
            <IconSave class="w-4 h-4 mr-2" />
            {{ hasChanges ? 'Save Changes' : 'No Changes' }}
            <Spinner v-if="form.processing || isSubmitting" class="ml-2" />
          </Button>
        </div>
      </div>
    </Navheader>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 max-w-4xl">
      <form ref="formRef" @submit.prevent="onSubmit" class="space-y-8">
        <!-- Personal Information -->
        <Card>
          <CardHeader>
            <div class="flex items-center gap-3">
              <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <IconUser class="w-5 h-5 text-blue-600 dark:text-blue-400" />
              </div>
              <div>
                <CardTitle class="text-xl">Personal Information</CardTitle>
                <CardDescription>Basic details about the customer</CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent class="space-y-6">
            <!-- Name Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label for="first_name" class="text-sm font-medium text-foreground">
                  First Name <span class="text-destructive">*</span>
                </label>
                <Input
                  id="first_name"
                  v-model="form.first_name"
                  placeholder="Enter first name"
                  class="h-12"
                  :class="{ 'border-destructive': $page.props.errors.first_name }"
                />
                <InputError :message="$page.props.errors.first_name" />
              </div>

              <div class="space-y-2">
                <label for="last_name" class="text-sm font-medium text-foreground">
                  Last Name <span class="text-destructive">*</span>
                </label>
                <Input
                  id="last_name"
                  v-model="form.last_name"
                  placeholder="Enter last name"
                  class="h-12"
                  :class="{ 'border-destructive': $page.props.errors.last_name }"
                />
                <InputError :message="$page.props.errors.last_name" />
              </div>
            </div>

            <!-- Job Title and Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label for="job_title" class="text-sm font-medium text-foreground">
                  Job Title <span class="text-destructive">*</span>
                </label>
                <Input
                  id="job_title"
                  v-model="form.job_title"
                  placeholder="Enter job title"
                  class="h-12"
                  :class="{ 'border-destructive': $page.props.errors.job_title }"
                />
                <InputError :message="$page.props.errors.job_title" />
              </div>

              <div class="space-y-2">
                <label for="status" class="text-sm font-medium text-foreground">
                  Status
                </label>
                <Select v-model="form.status">
                  <SelectTrigger class="h-12">
                    <SelectValue placeholder="Select status" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="active">
                      <div class="flex items-center gap-2">
                        <Badge variant="default" class="w-2 h-2 p-0 rounded-full"></Badge>
                        Active
                      </div>
                    </SelectItem>
                    <SelectItem value="inactive">
                      <div class="flex items-center gap-2">
                        <Badge variant="secondary" class="w-2 h-2 p-0 rounded-full"></Badge>
                        Inactive
                      </div>
                    </SelectItem>
                    <SelectItem value="prospect">
                      <div class="flex items-center gap-2">
                        <Badge variant="outline" class="w-2 h-2 p-0 rounded-full"></Badge>
                        Prospect
                      </div>
                    </SelectItem>
                  </SelectContent>
                </Select>
                <InputError :message="$page.props.errors.status" />
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Company Information -->
        <Card>
          <CardHeader>
            <div class="flex items-center gap-3">
              <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                <IconBuilding class="w-5 h-5 text-purple-600 dark:text-purple-400" />
              </div>
              <div>
                <CardTitle class="text-xl">Company Information</CardTitle>
                <CardDescription>Company details and affiliation</CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div class="space-y-2">
              <label for="company_name" class="text-sm font-medium text-foreground">
                Company Name
              </label>
              <Input
                id="company_name"
                v-model="form.company_name"
                placeholder="Enter company name"
                class="h-12"
                :class="{ 'border-destructive': $page.props.errors.company_name }"
              />
              <InputError :message="$page.props.errors.company_name" />
            </div>
          </CardContent>
        </Card>

        <!-- Contact Information -->
        <Card>
          <CardHeader>
            <div class="flex items-center gap-3">
              <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                <IconMail class="w-5 h-5 text-green-600 dark:text-green-400" />
              </div>
              <div>
                <CardTitle class="text-xl">Contact Information</CardTitle>
                <CardDescription>How to reach this customer</CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent class="space-y-6">
            <!-- Email and Phone -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label for="email" class="text-sm font-medium text-foreground">
                  Email Address <span class="text-destructive">*</span>
                </label>
                <Input
                  id="email"
                  type="email"
                  v-model="form.email"
                  placeholder="Enter email address"
                  class="h-12"
                  :class="{ 'border-destructive': $page.props.errors.email }"
                />
                <InputError :message="$page.props.errors.email" />
              </div>

              <div class="space-y-2">
                <label for="phone_number" class="text-sm font-medium text-foreground">
                  Phone Number
                </label>
                <Input
                  id="phone_number"
                  v-model="form.phone_number"
                  placeholder="Enter phone number"
                  class="h-12"
                  :class="{ 'border-destructive': $page.props.errors.phone_number }"
                />
                <InputError :message="$page.props.errors.phone_number" />
              </div>
            </div>

            <!-- Website -->
            <div class="space-y-2">
              <label for="website" class="text-sm font-medium text-foreground">
                Website
              </label>
              <Input
                id="website"
                type="url"
                v-model="form.website"
                placeholder="https://example.com"
                class="h-12"
                :class="{ 'border-destructive': $page.props.errors.website }"
              />
              <InputError :message="$page.props.errors.website" />
            </div>
          </CardContent>
        </Card>

        <!-- Address Information -->
        <Card>
          <CardHeader>
            <div class="flex items-center gap-3">
              <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                <IconMapPin class="w-5 h-5 text-amber-600 dark:text-amber-400" />
              </div>
              <div>
                <CardTitle class="text-xl">Address Information</CardTitle>
                <CardDescription>Physical location details</CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <AddressInput v-model="form.address" />
          </CardContent>
        </Card>

        <!-- Notes -->
        <Card>
          <CardHeader>
            <div class="flex items-center gap-3">
              <div class="p-2 bg-slate-100 dark:bg-slate-900/30 rounded-lg">
                <IconFileText class="w-5 h-5 text-slate-600 dark:text-slate-400" />
              </div>
              <div>
                <CardTitle class="text-xl">Additional Notes</CardTitle>
                <CardDescription>Any additional information about this customer</CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div class="space-y-2">
              <label for="notes" class="text-sm font-medium text-foreground">
                Notes
              </label>
              <Textarea
                id="notes"
                v-model="form.notes"
                placeholder="Enter any additional notes about this customer..."
                rows="4"
                class="resize-none"
              />
              <InputError :message="$page.props.errors.notes" />
            </div>
          </CardContent>
        </Card>

        <!-- Form Actions -->
        <Card>
          <CardContent class="pt-6">
            <div class="flex flex-col sm:flex-row gap-4 justify-between">
              <div class="flex flex-col sm:flex-row gap-4">
                <Link :href="route('admin.customers.index')" as="button">
                  <Button variant="outline" class="w-full sm:w-auto">
                    Cancel
                  </Button>
                </Link>

                <Link :href="route('admin.customers.show', customer.data.uuid)" as="button">
                  <Button variant="outline" class="w-full sm:w-auto">
                    <IconEye class="w-4 h-4 mr-2" />
                    Details
                  </Button>
                </Link>
              </div>

              <Button
                type="submit"
                :disabled="form.processing || isSubmitting || !hasChanges"
                class="w-full sm:w-auto bg-primary hover:bg-primary/90">
                <IconSave class="w-4 h-4 mr-2" />
                {{ hasChanges ? 'Save Changes' : 'No Changes' }}
                <Spinner v-if="form.processing || isSubmitting" class="ml-2" />
              </Button>
            </div>

            <!-- Change Indicator -->
            <div v-if="hasChanges" class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
              <p class="text-sm text-amber-800 dark:text-amber-200">
                <strong>Unsaved changes detected.</strong> Don't forget to save your changes before leaving this page.
              </p>
            </div>
          </CardContent>
        </Card>
      </form>
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

/* Form focus states */
.form-input:focus {
  ring: 2px;
  ring-color: hsl(var(--primary));
  border-color: hsl(var(--primary));
}

/* Smooth transitions */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Change indicator animation */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-down {
  animation: slideDown 0.3s ease-out;
}
</style>
