<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { gsap } from 'gsap'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { IconMailFast as IconSend } from '@tabler/icons-vue'

gsap.registerPlugin(); // Register GSAP plugins

const form = useForm({
  email: null,
})

const formRef = ref(null)
const submitting = ref(false)

function onSubmit() {
  if (submitting.value) return;
  submitting.value = true;

  form.post('/subscribe', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('email');
      gsap.fromTo(formRef.value,
        { scale: 1 },
        {
          scale: 1.02,
          duration: 0.2,
          yoyo: true,
          repeat: 1,
          ease: 'back.out(1.7)',
          onComplete: () => submitting.value = false
        }
      );
    },
    onError: () => {
      submitting.value = false;
    }
  })
}
</script>

<template>
  <Card class="relative overflow-hidden bg-background/50 backdrop-blur-sm border-0">
    <div class="absolute inset-0">
      <!-- Background pattern -->
      <div class="absolute inset-0 pattern-dots opacity-[0.05] dark:opacity-[0.08]"></div>
      <!-- Gradient overlay -->
      <div class="absolute inset-0 bg-linear-to-br from-primary/5 via-background/50 to-secondary/5"></div>
      <!-- Decorative circles -->
      <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-secondary/10 rounded-full blur-3xl"></div>
    </div>

    <CardContent class="relative z-10 p-8 sm:p-12">
      <div class="space-y-6">
        <div class="text-center space-y-2">
          <h3 class="text-2xl font-bold tracking-tight">
            Join Our Newsletter
          </h3>

          <p class="text-muted-foreground">
            Stay updated with our latest projects and insights
          </p>
        </div>

        <form
          @submit.prevent="onSubmit" ref="formRef" class="space-y-4">
          <div class="relative">
            <Input
              v-model="form.email"
              type="email"
              placeholder="Enter your email"
              class="pr-12 py-6 text-lg bg-background/50"
              :class="{ 'border-red-500': form.errors.email }"
              required
            />

            <Button
              type="submit"
              size="icon"
              class="absolute right-2 top-1/2 -translate-y-1/2"
              :disabled="submitting">
              <IconSend :class="[
                'size-6 transition-transform',
                submitting ? 'animate-pulse' : 'group-hover:translate-x-1'
              ]" />
            </Button>
          </div>

          <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform -translate-y-2 opacity-0"
            enter-to-class="transform translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-y-0 opacity-100"
            leave-to-class="transform -translate-y-2 opacity-0">
            <p v-if="form.errors.email" class="text-sm text-red-500">
              {{ form.errors.email }}
            </p>
          </transition>

          <p class="text-sm text-center text-muted-foreground">
            Join our community of {{ Math.floor(Math.random() * 900) + 100 }}+ subscribers
          </p>
        </form>
      </div>
    </CardContent>
  </Card>
</template>

<style scoped>
.pattern-dots {
  background-image: radial-gradient(currentColor 1px, transparent 1px);
  background-size: 24px 24px;
}

@media (prefers-color-scheme: dark) {
  .pattern-dots {
    opacity: 0.08;
  }
}

/* Add a subtle shine effect on hover */
.card {
  position: relative;
  overflow: hidden;
}

.card::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 200%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.1),
    transparent
  );
  transition: 0.5s;
}

.card:hover::after {
  left: 100%;
}
</style>
