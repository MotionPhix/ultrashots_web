<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import Modal from '@/components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import Spinner from '@/components/Spinner.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
  password: '',
});

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true;

  nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
  form.delete(route('profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value?.focus(),
    onFinish: () => {
      form.reset();
    },
  });
};

const closeModal = () => {
  confirmingUserDeletion.value = false;

  form.reset();
};
</script>

<template>
  <section class="space-y-6">
    <header>
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Delete Account</h2>

      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting
        your account, please download any data or information that you wish to retain.
      </p>
    </header>

    <MazBtn
      color="danger"
      @click="confirmUserDeletion"
      rounded-size="md">
      Delete Account
    </MazBtn>

    <Modal
      :show="confirmingUserDeletion"
      @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
          Are you sure you want to delete your account?
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          Once your account is deleted, all of its resources and data will be permanently deleted. Please
          enter your password to confirm you would like to permanently delete your account.
        </p>

        <div class="mt-6">
          <InputLabel
            for="password"
            value="Password"
            class="sr-only" />

          <MazInput
            id="password"
            ref="passwordInput"
            v-model="form.password"
            placeholder="Password"
            @keyup.enter="deleteUser"
            rounded-size="md"
            type="password"
            class="mt-1"
            size="lg"
            block />

          <InputError
            :message="form.errors.password"
              class="mt-2" />
        </div>

        <div class="flex justify-end mt-6">
          <MazBtn
            @click="closeModal"
            rounded-size="md"
            pastel>
            Cancel
          </MazBtn>

          <MazBtn
            class="ms-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="deleteUser"
            rounded-size="md"
            color="danger">
            <template #left-icon>
              <Spinner
                class="text-white"
                v-if="form.processing" />
            </template>

            Delete
          </MazBtn>
        </div>
      </div>
    </Modal>
  </section>
</template>
