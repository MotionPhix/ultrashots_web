<script lang="ts" setup>
import { Modal } from '@inertiaui/modal-vue'
import VueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import { useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';
import { FilePond } from 'filepond';
import InputError from '@/components/InputError.vue';
import PreTap from '@/components/PreTap.vue';
import InputLabel from '@/components/InputLabel.vue';
import toast from '@/stores/toast';

const FilePondInput = VueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginFileValidateSize,
  FilePondPluginImagePreview
);

const logoFile = ref<FilePond | null>(null)
const logoFix = ref()
const tipTap = ref()
const step = ref(1)

const form = useForm({
  name: '',
  email: '',
  description: '',
  phone: '',
  company: '',
  logo: null
})

const onSubmit = () => {

  form
    .transform((data) => {

      const formData: Partial<any> = {
        ...data,
        logo: logoFile.value?.getFile()?.file
      };

      return formData;

    }).post(route('upload-my-logo'), {

      preserveScroll: true,

      onSuccess: () => {
        form.reset()
        logoFile.value?.removeFiles();
        tipTap.value.resetEditorContent()
        logoFix.value.close()
      },

    });

};

const handleFilePond = (file: FilePond) => {

  form.logo = file as any

};


const nextStep = () => {

  if (step.value === 1) {

    form.post(route('upload-my-logo'), {

      preserveScroll: true,

      onError: (errors) => {

        if (errors.name || errors.phone || errors.email) {

          toast.add({
            type: 'danger',
            title: 'Fix Errors',
            message: errors.name ?? errors.phone ?? errors.email,
          });

        } else {

          form.clearErrors();

          step.value = 2;

        }

      }

    })

  }

};

const previousStep = () => {
  step.value = 1;
};

onBeforeUnmount(() => {
  logoFile.value?.destroy
  step.value = 1;
})
</script>

<template>

  <Modal
    max-width="xl"
    position="top"
    panel-classes="dark:text-neutral-100 bg-gray-100 rounded-lg dark:bg-neutral-600"
    ref="logoFix">

    <div class="w-full mx-auto">

      <h1 class="mb-8 text-2xl font-bold">
        <span v-if="step === 1">
          Let's get to know you.
        </span>

        <span v-else>
          Upload your pixelated logo.
        </span>
      </h1>

      <form
        @submit.prevent="onSubmit">

        <div v-if="step === 1">

          <section class="grid grid-cols-1 gap-2 sm:grid-cols-2">

            <div class="mb-4">
              <InputLabel
                class="mb-2">
                Full Name
              </InputLabel>

              <MazInput
                v-model="form.name"
                placeholder="Enter your full name"
                rounded-size="md"
                type="text"
                size="lg"
                required
                block />

              <InputError :message="form.errors.name" />
            </div>

            <div class="mb-4">

              <InputLabel
                class="mb-2">
                Company
              </InputLabel>

              <MazInput
                v-model="form.company"
                placeholder="Enter your company name"
                rounded-size="md"
                type="text"
                size="lg"
                block />

            </div>

          </section>

          <section
            class="grid grid-cols-1 gap-2 sm:grid-cols-2">

            <div class="mb-4">
              <InputLabel
                class="mb-2">
                Phone
              </InputLabel>

              <MazPhoneNumberInput
                :show-code-on-list="false"
                v-model="form.phone"
                v-model:country-code="countryCode"
                placeholder="Enter your phone"
                orientation="responsive"
                :preferred-countries="['MW', 'ZM', 'ZA', 'ZW', 'GB', 'US']"
                no-country-selector
                rounded-size="md"
                no-example
                size="lg"
                required
                block
              />

              <InputError :message="form.errors.phone"/>
            </div>

            <div class="mb-4">
              <InputLabel
                class="mb-2">
                Email
              </InputLabel>

              <MazInput
                v-model="form.email"
                placeholder="Enter your email address"
                rounded-size="md"
                type="email"
                size="lg"
                required
                block />

              <InputError :message="form.errors.email"/>
            </div>

          </section>

          <div class="flex justify-end mt-4">
            <MazBtn
              type="button"
              @click="nextStep"
              color="primary" size="sm"
              rounded-size="md"
              pastel>
              <template #right-icon>
                <IconArrowRight class="size-6" />
              </template>

              Next
            </MazBtn>
          </div>

        </div>

        <div v-else>

          <div class="mb-4">
            <InputLabel
              class="mb-2">
              Upload Logo
            </InputLabel>

            <FilePondInput
              name="Client logo"
              ref="logoFile"
              max-file-size="2MB"
              credits="false"
              accepted-file-types="image/*"
              label-idle="Drop your logo image here..."
              :allow-multiple="false"
              :allow-mage-preview="true"
              :image-preview-max-height="150"
              @updatefiles="handleFilePond" />

            <InputError :message="form.errors.logo" />
          </div>

          <div class="mb-4">
            <InputLabel
              class="mb-2">
              Description
            </InputLabel>

            <PreTap
              v-model="form.description"
              placeholder="Describe your logo and desired output"
              custom-height="h-40"
              ref="tipTap"/>

            <InputError :message="form.errors.description" />
          </div>

          <div class="flex items-center justify-between mt-4">

            <MazBtn
              @click="previousStep"
              rounded-size="md"
              color="primary"
              size="sm"
              pastel>
              <template #left-icon>
                <IconArrowLeft class="size-6" />
              </template>

              Back
            </MazBtn>

            <MazBtn
              type="submit"
              rounded-size="md"
              color="success"
              size="md">
              Upload
            </MazBtn>

          </div>

        </div>

      </form>

    </div>

  </Modal>

</template>
