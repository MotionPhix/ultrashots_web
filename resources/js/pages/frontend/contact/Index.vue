<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { send } from '@/routes/contact';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ArrowRight } from 'lucide-vue-next';
import { TipTapEditor } from '@/components/editor';
import Heading from '@/components/Heading.vue';
import { Field, FieldError, FieldGroup, FieldLabel } from '@/components/ui/field';

const contactForm = useForm({
  name: '',
  email: '',
  phone: '',
  company: '',
  message: '',
}).withPrecognition('post', send().url)

const sendQuery = () => {
  contactForm.post(send().url, {
    preserveScroll: true,
    onSuccess: () => contactForm.resetAndClearErrors()
  })
}

defineOptions({
  layout: AppLayout
})
</script>

<template>

  <Head title="Contact Me" />

  <article
    class="w-full max-w-2xl px-4 pt-10 mx-auto md:pt-16 sm:px-6 lg:px-8 py-16">

    <!-- Title -->
    <div class="w-full mb-10 lg:mb-14 title-section">

      <Heading
        title="Contact me"
        :description="contactForm.validating ? `Validating...` : `I'd love to talk about how I can help you.`"
      />

    </div>
    <!-- End Title -->

    <form class="space-y-6" @submit.prevent="sendQuery">
      <FieldGroup>
        <Field :data-invalid="contactForm.invalid('name')">
          <FieldLabel for="name">Full name</FieldLabel>
          <Input
            id="name"
            v-model="contactForm.name"
            :class="contactForm.invalid('name') ? 'border-red-500' : ''"
            @change="contactForm.validate('name')"
            placeholder="Enter your full name"
          />

          <FieldError v-if="contactForm.invalid('name')">
            {{ contactForm.errors.name }}
          </FieldError>
        </Field>
      </FieldGroup>

      <FieldGroup>
        <Field :data-invalid="contactForm.invalid('email')">
          <FieldLabel for="email">Email address</FieldLabel>
          <Input
            id="email"
            type="email"
            v-model="contactForm.email"
            :class="contactForm.invalid('email') ? 'border-red-500' : ''"
            placeholder="Enter your email address"
            @change="contactForm.validate('email')"
          />

          <FieldError v-if="contactForm.invalid('email')">
            {{ contactForm.errors.email }}
          </FieldError>
        </Field>
      </FieldGroup>

      <FieldGroup>
        <Field :data-invalid="contactForm.invalid('phone')">
          <FieldLabel for="phone">Phone</FieldLabel>
          <Input
            id="phone"
            type="tel"
            v-model="contactForm.phone"
            :class="contactForm.invalid('phone') ? 'border-red-500' : ''"
            @change="contactForm.validate('phone')"
            placeholder="Enter your phone number"
          />

          <FieldError v-if="contactForm.invalid('phone')">
            {{ contactForm.errors.phone }}
          </FieldError>
        </Field>
      </FieldGroup>

      <FieldGroup>
        <Field :data-invalid="contactForm.invalid('company')">
          <FieldLabel for="company">Company</FieldLabel>
          <Input
            id="company"
            v-model="contactForm.company"
            :class="contactForm.invalid('company') ? 'border-red-500' : ''"
            @change="contactForm.validate('company')"
            placeholder="Enter your company name"
          />

          <FieldError v-if="contactForm.invalid('company')">
            {{ contactForm.errors.company }}
          </FieldError>
        </Field>
      </FieldGroup>

      <FieldGroup>
        <Field :data-invalid="contactForm.invalid('message')">
          <FieldLabel for="message">Message</FieldLabel>
          <TipTapEditor
            v-model="contactForm.message"
            @blur="contactForm.validate('message')"
            :class="contactForm.invalid('message') ? 'border-red-500' : ''"
            placeholder="How can I assist you? Be verbose"
          />

          <FieldError v-if="contactForm.invalid('message')">
            {{ contactForm.errors.message }}
          </FieldError>
        </Field>
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
