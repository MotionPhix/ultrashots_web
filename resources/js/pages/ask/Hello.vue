<script setup lang="ts">
import {Head, useForm} from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import {onMounted, ref, watch} from 'vue';
import {gsap} from 'gsap';
import {ScrollTrigger} from 'gsap/ScrollTrigger';
import {toast} from 'vue-sonner';

// Shadcn Vue Components
import {Button} from '@/components/ui/button';
import {Input} from '@/components/ui/input';
import {Label} from '@/components/ui/label';
import {Textarea} from '@/components/ui/textarea';
import {Card, CardContent, CardDescription, CardHeader, CardTitle} from '@/components/ui/card';
import {Badge} from '@/components/ui/badge';
import {Separator} from '@/components/ui/separator';

// Icons
import {
  Send,
  Mail,
  Phone,
  Building2,
  MessageSquare,
  User,
  MapPin,
  Clock,
  CheckCircle,
  Sparkles,
  ArrowRight,
  Globe,
  Linkedin,
  Github,
  Twitter
} from 'lucide-vue-next';

import {User as UserType} from '@/types';

gsap.registerPlugin(ScrollTrigger);

defineProps<{
  user?: UserType
}>();

const contactForm = useForm({
  name: '',
  email: '',
  phone: '',
  company: '',
  message: '',
});

const isSubmitting = ref(false);
const mainRef = ref<HTMLElement>();
const heroRef = ref<HTMLElement>();
const formRef = ref<HTMLElement>();
const contactInfoRef = ref<HTMLElement>();
const socialRef = ref<HTMLElement>();

const onSubmit = async () => {
  if (isSubmitting.value) return;

  isSubmitting.value = true;

  contactForm.post(route('contact.send'), {
    preserveScroll: true,
    onSuccess: () => {
      contactForm.reset();
      toast.success('Message sent successfully!', {
        description: 'Thank you for reaching out. I\'ll get back to you soon.',
        duration: 5000,
      });
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0] as string;
      toast.error('Failed to send message', {
        description: firstError || 'Please check your form and try again.',
        duration: 5000,
      });
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

// Watch for form errors and show toasts
watch(() => contactForm.errors, (errors) => {
  if (Object.keys(errors).length > 0) {
    const firstError = Object.values(errors)[0] as string;
    toast.error('Validation Error', {
      description: firstError,
      duration: 4000,
    });
  }
}, {deep: true});

onMounted(() => {
  setTimeout(() => {
    try {
      // Hero section animation
      if (heroRef.value) {
        const heroElements = heroRef.value.children;
        gsap.fromTo(heroElements,
          {opacity: 0, y: 50},
          {
            opacity: 1,
            y: 0,
            duration: 0.8,
            stagger: 0.2,
            ease: 'power3.out'
          }
        );
      }

      // Form animation
      if (formRef.value) {
        gsap.fromTo(formRef.value,
          {opacity: 0, x: -50},
          {
            opacity: 1,
            x: 0,
            duration: 0.8,
            delay: 0.4,
            ease: 'power2.out',
            scrollTrigger: {
              trigger: formRef.value,
              start: 'top bottom-=100',
              toggleActions: 'play none none reverse'
            }
          }
        );
      }

      // Contact info animation
      if (contactInfoRef.value) {
        gsap.fromTo(contactInfoRef.value,
          {opacity: 0, x: 50},
          {
            opacity: 1,
            x: 0,
            duration: 0.8,
            delay: 0.6,
            ease: 'power2.out',
            scrollTrigger: {
              trigger: contactInfoRef.value,
              start: 'top bottom-=100',
              toggleActions: 'play none none reverse'
            }
          }
        );
      }

      // Social links animation
      if (socialRef.value) {
        const socialItems = socialRef.value.querySelectorAll('.social-item');
        gsap.fromTo(socialItems,
          {opacity: 0, y: 30, scale: 0.8},
          {
            opacity: 1,
            y: 0,
            scale: 1,
            duration: 0.6,
            stagger: 0.1,
            delay: 0.8,
            ease: 'back.out(1.7)',
            scrollTrigger: {
              trigger: socialRef.value,
              start: 'top bottom-=50',
              toggleActions: 'play none none reverse'
            }
          }
        );
      }

      // Form fields stagger animation
      const formFields = document.querySelectorAll('.form-field');
      gsap.fromTo(formFields,
        {opacity: 0, y: 20},
        {
          opacity: 1,
          y: 0,
          duration: 0.5,
          stagger: 0.1,
          delay: 1,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: formFields[0],
            start: 'top bottom-=50',
            toggleActions: 'play none none reverse'
          }
        }
      );

    } catch (error) {
      console.warn('Animation setup failed:', error);
    }
  }, 100);
});

defineOptions({
  layout: AppLayout
});
</script>

<template>
  <Head title="Contact Me"/>

  <div class="min-h-screen bg-background" ref="mainRef">
    <!-- Hero Section -->
    <section
      class="relative overflow-hidden bg-linear-to-br from-background via-background to-muted/20 py-20 lg:py-32">
      <div class="absolute inset-0 bg-grid-black/[0.02] dark:bg-grid-white/[0.02]"/>
      <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div ref="heroRef" class="text-center space-y-8">
          <div class="space-y-4">
            <Badge variant="secondary" class="mb-4">
              <MessageSquare class="h-3 w-3 mr-1"/>
              Get in Touch
            </Badge>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight font-mono">
              Let's start a
              <span class="bg-linear-to-r from-primary to-primary/60 bg-clip-text text-transparent">
                conversation
              </span>
            </h1>

            <p class="text-lg text-muted-foreground max-w-3xl mx-auto leading-relaxed">
              I'd love to hear about your project and discuss how I can help bring your ideas to life.
              Whether it's a quick question or a detailed project discussion, I'm here to help.
            </p>
          </div>

          <!-- Quick Stats -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-2xl mx-auto">
            <div class="text-center">
              <div class="text-2xl font-bold text-primary">< 24h</div>
              <div class="text-sm text-muted-foreground">Response Time</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">100%</div>
              <div class="text-sm text-muted-foreground">Satisfaction Rate</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600">50+</div>
              <div class="text-sm text-muted-foreground">Projects Completed</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <section class="py-16">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

          <!-- Contact Form -->
          <div ref="formRef" class="lg:col-span-2">
            <Card class="border-0 shadow-xl bg-card/50 backdrop-blur-sm">
              <CardHeader class="space-y-4">
                <div class="flex items-center gap-2">
                  <div class="p-2 bg-primary/10 rounded-lg">
                    <Send class="h-5 w-5 text-primary"/>
                  </div>
                  <div>
                    <CardTitle class="text-2xl">Send me a message</CardTitle>
                    <CardDescription>
                      Fill out the form below and I'll get back to you as soon as possible.
                    </CardDescription>
                  </div>
                </div>
              </CardHeader>

              <CardContent>
                <form @submit.prevent="onSubmit" class="space-y-6">
                  <!-- Name Field -->
                  <div class="form-field space-y-2">
                    <Label for="name" class="flex items-center gap-2">
                      <User class="h-4 w-4"/>
                      Full Name
                    </Label>
                    <Input
                      id="name"
                      v-model="contactForm.name"
                      placeholder="Enter your full name"
                      :class="contactForm.errors.name ? 'border-destructive' : ''"
                      class="h-12"
                    />
                    <p v-if="contactForm.errors.name" class="text-sm text-destructive">
                      {{ contactForm.errors.name }}
                    </p>
                  </div>

                  <!-- Email Field -->
                  <div class="form-field space-y-2">
                    <Label for="email" class="flex items-center gap-2">
                      <Mail class="h-4 w-4"/>
                      Email Address
                    </Label>
                    <Input
                      id="email"
                      type="email"
                      v-model="contactForm.email"
                      placeholder="Enter your email address"
                      :class="contactForm.errors.email ? 'border-destructive' : ''"
                      class="h-12"
                    />
                    <p v-if="contactForm.errors.email" class="text-sm text-destructive">
                      {{ contactForm.errors.email }}
                    </p>
                  </div>

                  <!-- Phone Field -->
                  <div class="form-field space-y-2">
                    <Label for="phone" class="flex items-center gap-2">
                      <Phone class="h-4 w-4"/>
                      Phone Number
                    </Label>
                    <Input
                      id="phone"
                      type="tel"
                      v-model="contactForm.phone"
                      placeholder="Enter your phone number"
                      :class="contactForm.errors.phone ? 'border-destructive' : ''"
                      class="h-12"
                    />
                    <p v-if="contactForm.errors.phone" class="text-sm text-destructive">
                      {{ contactForm.errors.phone }}
                    </p>
                  </div>

                  <!-- Company Field -->
                  <div class="form-field space-y-2">
                    <Label for="company" class="flex items-center gap-2">
                      <Building2 class="h-4 w-4"/>
                      Company (Optional)
                    </Label>
                    <Input
                      id="company"
                      v-model="contactForm.company"
                      placeholder="Enter your company name"
                      class="h-12"
                    />
                  </div>

                  <!-- Message Field -->
                  <div class="form-field space-y-2">
                    <Label for="message" class="flex items-center gap-2">
                      <MessageSquare class="h-4 w-4"/>
                      Message
                    </Label>
                    <Textarea
                      id="message"
                      v-model="contactForm.message"
                      placeholder="Tell me about your project or how I can help you..."
                      :class="contactForm.errors.message ? 'border-destructive' : ''"
                      class="min-h-[120px] resize-none"
                    />
                    <p v-if="contactForm.errors.message" class="text-sm text-destructive">
                      {{ contactForm.errors.message }}
                    </p>
                  </div>

                  <!-- Submit Button -->
                  <Button
                    type="submit"
                    :disabled="isSubmitting || contactForm.processing"
                    class="w-full h-12 text-base font-medium"
                    size="lg"
                  >
                    <template v-if="isSubmitting || contactForm.processing">
                      <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                      Sending...
                    </template>
                    <template v-else>
                      Send Message
                      <ArrowRight class="h-4 w-4 ml-2"/>
                    </template>
                  </Button>
                </form>
              </CardContent>
            </Card>
          </div>

          <!-- Contact Information -->
          <div ref="contactInfoRef" class="space-y-8 lg:col-span-2 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Contact Details -->
            <section>
              <Card class="border-0 shadow-xl bg-card/50 backdrop-blur-sm">
                <CardHeader>
                  <div class="flex items-center gap-2">
                    <div class="p-2 bg-green-500/10 rounded-lg">
                      <CheckCircle class="h-5 w-5 text-green-600"/>
                    </div>
                    <div>
                      <CardTitle class="text-xl">Contact Information</CardTitle>
                      <CardDescription>
                        Prefer to reach out directly? Here are my contact details.
                      </CardDescription>
                    </div>
                  </div>
                </CardHeader>

                <CardContent class="space-y-6">
                  <!-- Email -->
                  <div v-if="user?.email"
                       class="flex items-start gap-4 p-4 rounded-lg bg-muted/50 hover:bg-muted/70 transition-colors">
                    <div class="p-2 bg-blue-500/10 rounded-lg">
                      <Mail class="h-5 w-5 text-blue-600"/>
                    </div>
                    <div class="flex-1">
                      <h3 class="font-medium text-foreground">Email</h3>
                      <p class="text-sm text-muted-foreground mb-2">Send me an email anytime</p>
                      <a
                        :href="`mailto:${user.email}`"
                        class="text-primary hover:text-primary/80 font-medium transition-colors"
                      >
                        {{ user.email }}
                      </a>
                    </div>
                  </div>

                  <!-- Phone -->
                  <div v-if="user?.phone_number"
                       class="flex items-start gap-4 p-4 rounded-lg bg-muted/50 hover:bg-muted/70 transition-colors">
                    <div class="p-2 bg-green-500/10 rounded-lg">
                      <Phone class="h-5 w-5 text-green-600"/>
                    </div>
                    <div class="flex-1">
                      <h3 class="font-medium text-foreground">Phone</h3>
                      <p class="text-sm text-muted-foreground mb-2">Call me for urgent matters</p>
                      <a
                        :href="`tel:${user.phone_number}`"
                        class="text-primary hover:text-primary/80 font-medium transition-colors"
                      >
                        {{ user.phone_number }}
                      </a>
                    </div>
                  </div>

                  <!-- Location -->
                  <div class="flex items-start gap-4 p-4 rounded-lg bg-muted/50">
                    <div class="p-2 bg-purple-500/10 rounded-lg">
                      <MapPin class="h-5 w-5 text-purple-600"/>
                    </div>
                    <div class="flex-1">
                      <h3 class="font-medium text-foreground">Location</h3>
                      <p class="text-sm text-muted-foreground mb-2">Based in</p>
                      <p class="text-primary font-medium">Lilongwe, Malawi</p>
                    </div>
                  </div>

                  <!-- Response Time -->
                  <div class="flex items-start gap-4 p-4 rounded-lg bg-muted/50">
                    <div class="p-2 bg-orange-500/10 rounded-lg">
                      <Clock class="h-5 w-5 text-orange-600"/>
                    </div>
                    <div class="flex-1">
                      <h3 class="font-medium text-foreground">Response Time</h3>
                      <p class="text-sm text-muted-foreground mb-2">I typically respond within</p>
                      <p class="text-primary font-medium">24 hours</p>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </section>

            <!-- Social Links -->
            <section>
              <Card class="border-0 shadow-xl bg-card/50 backdrop-blur-sm">
                <CardHeader>
                  <div class="flex items-center gap-2">
                    <div class="p-2 bg-purple-500/10 rounded-lg">
                      <Sparkles class="h-5 w-5 text-purple-600"/>
                    </div>
                    <div>
                      <CardTitle class="text-xl">Let's Connect</CardTitle>
                      <CardDescription>
                        Follow me on social media for updates and insights.
                      </CardDescription>
                    </div>
                  </div>
                </CardHeader>

                <CardContent>
                  <div ref="socialRef" class="grid grid-cols-2 gap-4">
                    <a
                      href="#"
                      class="social-item flex items-center gap-3 p-4 rounded-lg bg-muted/50 hover:bg-muted/70 transition-all duration-300 hover:scale-105"
                    >
                      <div class="p-2 bg-blue-500/10 rounded-lg">
                        <Linkedin class="h-5 w-5 text-blue-600"/>
                      </div>
                      <div>
                        <p class="font-medium text-sm">LinkedIn</p>
                        <p class="text-xs text-muted-foreground">Professional</p>
                      </div>
                    </a>

                    <a
                      href="#"
                      class="social-item flex items-center gap-3 p-4 rounded-lg bg-muted/50 hover:bg-muted/70 transition-all duration-300 hover:scale-105"
                    >
                      <div class="p-2 bg-gray-500/10 rounded-lg">
                        <Github class="h-5 w-5 text-gray-600"/>
                      </div>
                      <div>
                        <p class="font-medium text-sm">GitHub</p>
                        <p class="text-xs text-muted-foreground">Code</p>
                      </div>
                    </a>

                    <a
                      href="#"
                      class="social-item flex items-center gap-3 p-4 rounded-lg bg-muted/50 hover:bg-muted/70 transition-all duration-300 hover:scale-105"
                    >
                      <div class="p-2 bg-blue-400/10 rounded-lg">
                        <Twitter class="h-5 w-5 text-blue-500"/>
                      </div>
                      <div>
                        <p class="font-medium text-sm">Twitter</p>
                        <p class="text-xs text-muted-foreground">Updates</p>
                      </div>
                    </a>

                    <a
                      href="#"
                      class="social-item flex items-center gap-3 p-4 rounded-lg bg-muted/50 hover:bg-muted/70 transition-all duration-300 hover:scale-105"
                    >
                      <div class="p-2 bg-green-500/10 rounded-lg">
                        <Globe class="h-5 w-5 text-green-600"/>
                      </div>
                      <div>
                        <p class="font-medium text-sm">Website</p>
                        <p class="text-xs text-muted-foreground">Portfolio</p>
                      </div>
                    </a>
                  </div>
                </CardContent>
              </Card>
            </section>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.bg-grid-black\/\[0\.02\] {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='rgb(0 0 0 / 0.02)'%3e%3cpath d='m0 .5h32m-32 32v-32'/%3e%3c/svg%3e");
}

.dark .bg-grid-white\/\[0\.02\] {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='rgb(255 255 255 / 0.02)'%3e%3cpath d='m0 .5h32m-32 32v-32'/%3e%3c/svg%3e");
}
</style>
