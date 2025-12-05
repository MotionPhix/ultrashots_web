<script setup lang="ts">
// import Spinner from "@/components/Spinner.vue";

import InputError from "@/components/InputError.vue";

import { Modal } from '@inertiaui/modal-vue'

import {useForm} from "@inertiajs/vue3";

import { Customer } from "@/types";

import { ref } from "vue";

const props = defineProps<{

  customer: Customer;

}>();

const createCustomerRef = ref();

const form = useForm({
  first_name: props.customer.first_name,
  last_name: props.customer.last_name,
  job_title: props.customer.job_title ?? "",
  company_name: props.customer.company_name ?? ""
});

function onSubmit() {
  form.transform((data) => {
    let formData: Partial<Customer> = {
      first_name: data.first_name,
      last_name: data.last_name,
      job_title: data.job_title,
      company_name: data.company_name,
    };

    // Include optional fields only if they are filled
    if (!!data?.job_title) formData.job_title = data?.job_title;

    if (!!data.company_name) formData.company_name = data.company_name;

    return formData;
  });

  if (props.customer.cid) {

    form.patch(route("auth.customer.update", props.customer.cid), {

      preserveScroll: true,

      onSuccess: () => {

        form.reset();

      },

    });

    return;
  }

  form.post(route("auth.customer.store", { modal: 'show' }), {

    preserveScroll: true,

    onSuccess: () => {

      form.reset();

      createCustomerRef.value.close();

    },

  });
}
</script>

<template>

  <Modal
    ref="createCustomerRef"
    panel-classes="bg-gray-100 rounded-lg dark:bg-gray-700"
    position="top">

    <form
      class="flex flex-col w-full gap-4 md:mx-auto">

      <div class="pb-6 mb-4 border-b border-gray-300 dark:border-gray-500">

        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">
          New customer
        </h2>

        <p class="text-sm text-gray-600 dark:text-gray-400">
          Quickly create, and add the newly created customer.
        </p>

      </div>

      <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">

        <div>
          <label
            for="name"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            First name
          </label>

          <MazInput
            id="name"
            type="text"
            v-model="form.first_name"
            placeholder="Enter first name"
            rounded-size="md"
            size="lg"
            block
          />

          <InputError :message="$page.props.errors.first_name" />
        </div>

        <div>
          <label
            for="last_name"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Surname
          </label>

          <MazInput
            type="text"
            id="last_name"
            v-model="form.last_name"
            placeholder="Type surname"
            rounded-size="md"
            size="lg"
            block
          />

          <InputError :message="$page.props.errors.last_name" />
        </div>
    </section>

      <section
        class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Company
          </label>

          <MazInput
            type="text"
            id="company_name"
            v-model="form.company_name"
            placeholder="Type company name"
            rounded-size="md"
            size="lg"
            block
          />

          <InputError :message="$page.props.errors['company_name']" />
        </div>

        <div>
          <label
            for="job_title"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Job title
          </label>

          <MazInput
            type="text"
            id="job_title"
            v-model="form.job_title"
            placeholder="Enter job title"
            rounded-size="md"
            size="lg"
            block
          />

          <InputError :message="$page.props.errors['job_title']" />
        </div>

        <div class="col-span-1 sm:col-span-2">

          <MazBtn
            :loading="form.processing"
            color="white"
            rounded-size="md"
            @click="onSubmit"
            size="lg">
            Save
          </MazBtn>

        </div>
      </section>
    </form>

  </Modal>
</template>
