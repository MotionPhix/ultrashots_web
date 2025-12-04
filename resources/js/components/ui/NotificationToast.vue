<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { IconX, IconCheck, IconAlertTriangle, IconInfoCircle, IconAlertCircle } from '@tabler/icons-vue';

interface Props {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  title?: string;
  message: string;
  duration?: number;
  persistent?: boolean;
  actions?: Array<{
    label: string;
    action: () => void;
    style?: 'primary' | 'secondary';
  }>;
}

const props = withDefaults(defineProps<Props>(), {
  duration: 5000,
  persistent: false,
});

const emit = defineEmits<{
  remove: [id: string];
}>();

const isVisible = ref(false);
const isRemoving = ref(false);

const typeConfig = {
  success: {
    icon: IconCheck,
    bgColor: 'bg-green-50 dark:bg-green-900/20',
    borderColor: 'border-green-200 dark:border-green-800',
    iconColor: 'text-green-600 dark:text-green-400',
    titleColor: 'text-green-800 dark:text-green-200',
    messageColor: 'text-green-700 dark:text-green-300',
  },
  error: {
    icon: IconAlertCircle,
    bgColor: 'bg-red-50 dark:bg-red-900/20',
    borderColor: 'border-red-200 dark:border-red-800',
    iconColor: 'text-red-600 dark:text-red-400',
    titleColor: 'text-red-800 dark:text-red-200',
    messageColor: 'text-red-700 dark:text-red-300',
  },
  warning: {
    icon: IconAlertTriangle,
    bgColor: 'bg-yellow-50 dark:bg-yellow-900/20',
    borderColor: 'border-yellow-200 dark:border-yellow-800',
    iconColor: 'text-yellow-600 dark:text-yellow-400',
    titleColor: 'text-yellow-800 dark:text-yellow-200',
    messageColor: 'text-yellow-700 dark:text-yellow-300',
  },
  info: {
    icon: IconInfoCircle,
    bgColor: 'bg-blue-50 dark:bg-blue-900/20',
    borderColor: 'border-blue-200 dark:border-blue-800',
    iconColor: 'text-blue-600 dark:text-blue-400',
    titleColor: 'text-blue-800 dark:text-blue-200',
    messageColor: 'text-blue-700 dark:text-blue-300',
  },
};

const config = typeConfig[props.type];

const remove = () => {
  isRemoving.value = true;
  setTimeout(() => {
    emit('remove', props.id);
  }, 300);
};

onMounted(() => {
  // Animate in
  setTimeout(() => {
    isVisible.value = true;
  }, 50);

  // Auto-remove if not persistent
  if (!props.persistent && props.duration) {
    setTimeout(() => {
      remove();
    }, props.duration);
  }
});
</script>

<template>
  <Transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="transform translate-x-full opacity-0"
    enter-to-class="transform translate-x-0 opacity-100"
    leave-active-class="transition-all duration-300 ease-in"
    leave-from-class="transform translate-x-0 opacity-100"
    leave-to-class="transform translate-x-full opacity-0"
  >
    <div
      v-if="isVisible && !isRemoving"
      :class="[
        'max-w-sm w-full shadow-lg rounded-lg pointer-events-auto border',
        config.bgColor,
        config.borderColor
      ]"
    >
      <div class="p-4">
        <div class="flex items-start">
          <!-- Icon -->
          <div class="shrink-0">
            <component
              :is="config.icon"
              :class="['w-5 h-5', config.iconColor]"
            />
          </div>

          <!-- Content -->
          <div class="ml-3 w-0 flex-1">
            <p
              v-if="title"
              :class="['text-sm font-medium', config.titleColor]"
            >
              {{ title }}
            </p>
            <p
              :class="[
                'text-sm',
                config.messageColor,
                title ? 'mt-1' : ''
              ]"
            >
              {{ message }}
            </p>

            <!-- Actions -->
            <div v-if="actions && actions.length > 0" class="mt-3 flex space-x-2">
              <button
                v-for="action in actions"
                :key="action.label"
                @click="action.action"
                :class="[
                  'text-xs font-medium rounded-md px-2 py-1 transition-colors',
                  action.style === 'primary'
                    ? 'bg-white text-gray-900 hover:bg-gray-50 border border-gray-300'
                    : 'text-gray-600 hover:text-gray-500'
                ]"
              >
                {{ action.label }}
              </button>
            </div>
          </div>

          <!-- Close button -->
          <div class="ml-4 shrink-0 flex">
            <button
              @click="remove"
              class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md"
            >
              <IconX class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>
