<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { send } from '@/routes/contact';
import InputError from '@/components/InputError.vue';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowRight } from 'lucide-vue-next';
import { TipTapEditor } from '@/components/editor';
import Heading from '@/components/Heading.vue';
import { Field, FieldError, FieldGroup, FieldLabel } from '@/components/ui/field';

gsap.registerPlugin(ScrollTrigger);

const contactForm = useForm({
  name: '',
  email: '',
  phone: '',
  company: '',
  message: '',
})

const countryCode = ref('MW')

const onSubmit = () => {

  contactForm.post(send().url, {
    preserveScroll: true,
    onSuccess: () => {
      contactForm.reset();
    }
  })

}

const mainRef = ref(null);

onMounted(() => {
  const main = mainRef.value as any;
  const title = main.querySelector('.title-section');
  const formFields = main.querySelectorAll('form > div');

  // Animate title
  gsap.from(title.children, {
    opacity: 0,
    y: 30,
    duration: 0.8,
    stagger: 0.2,
    ease: "power2.out"
  });

  // Animate form fields
  gsap.from(formFields, {
    opacity: 0,
    y: 20,
    duration: 0.6,
    stagger: 0.1,
    delay: 0.5,
    scrollTrigger: {
      trigger: formFields[0],
      start: 'top bottom-=100',
      toggleActions: 'play none none reverse'
    }
  });

  // Animate submit button
  gsap.from('button[type="submit"]', {
    opacity: 0,
    y: 20,
    duration: 0.6,
    delay: 1,
    scrollTrigger: {
      trigger: 'button[type="submit"]',
      start: 'top bottom-=20',
      toggleActions: 'play none none reverse'
    }
  });
});

defineOptions({
  layout: AppLayout
})
</script>

<template>

  <Head title="Contact Me" />

  <article
    class="w-full max-w-2xl px-4 pt-10 mx-auto md:pt-16 sm:px-6 lg:px-8 py-16"
    ref="mainRef">

    <!-- Title -->
    <div class="w-full mb-10 lg:mb-14 title-section">

      <Heading
        title="Contact me"
        description="I'd love to talk about how I can help you."
      />

    </div>
    <!-- End Title -->

    <form @submit.prevent="onSubmit">
      <FieldGroup>
        <FieldLabel for="name">Full name</FieldLabel>
        <Input
          id="name"
          v-model="contactForm.name"
          placeholder="Enter your full name"
          required
        />
        <InputError :message="contactForm.errors.name" />
      </FieldGroup>

      <FieldGroup>
        <FieldLabel for="email">Email address</FieldLabel>
        <Input
          id="email"
          v-model="contactForm.email"
          type="email"
          placeholder="Enter your email address"
          required
        />
        <InputError :message="contactForm.errors.email" />
      </FieldGroup>

      <FieldGroup>
        <FieldLabel for="phone">Phone</FieldLabel>
        <Input
          id="phone"
          v-model="contactForm.phone"
          type="tel"
          placeholder="Enter your phone number"
        />
        <InputError :message="contactForm.errors.phone" />
      </FieldGroup>

      <FieldGroup>
        <FieldLabel for="company">Company</FieldLabel>
        <Input
          id="company"
          v-model="contactForm.company"
          placeholder="Enter your company name"
        />

        <FieldError />
      </FieldGroup>

      <FieldGroup>
        <Field data-invalid>
          <FieldLabel for="message">Message</FieldLabel>
          <TipTapEditor
            v-model="contactForm.message"
            placeholder="How can I assist you? Be verbose"
          />
        </Field>

        <InputError :message="contactForm.errors.message" />
      </FieldGroup>

      <div class="flex justify-end">
        <Button
          type="submit"
          size="lg"
          :disabled="contactForm.processing">
          <span v-if="contactForm.processing">Sending...</span>
          <span v-else class="flex items-center gap-2">
            Send Message
            <ArrowRight class="h-4 w-4" />
          </span>
        </Button>
      </div>
    </form>

  </article>

</template>
