<script setup lang="ts">
import { Modal } from '@inertiaui/modal-vue'
import { ref, nextTick, watch } from 'vue'
import { ArrowUp, Sparkles } from 'lucide-vue-next'
import { ModalFooter, ModalHeader, ModalRoot, ModalScrollable } from '@/components/modal'
import {
  InputGroup,
  InputGroupAddon,
  InputGroupButton,
  InputGroupTextarea,
} from '@/components/ui/input-group'

interface Message {
  id: number
  text: string
  isUser: boolean
  timestamp: Date
}

const messages = ref<Message[]>([])
const conversationId = ref<string | null>(null)
const isLoading = ref(false)

const newMessage = ref('')
const messagesContainer = ref<HTMLElement>()

const sendMessage = async () => {
  if (!newMessage.value.trim()) return

  const userMessage = newMessage.value.trim()

  // Add user message to UI
  messages.value.push({
    id: messages.value.length + 1,
    text: userMessage,
    isUser: true,
    timestamp: new Date(),
  })

  newMessage.value = ''
  isLoading.value = true

  try {
    // Call the backend API
    const response = await fetch('/chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({
        message: userMessage,
        conversation_id: conversationId.value,
      }),
    })

    const data = await response.json()

    // Save conversation ID for subsequent messages
    if (data.conversation_id) {
      conversationId.value = data.conversation_id
    }

    // Add AI response to UI
    messages.value.push({
      id: messages.value.length + 1,
      text: data.message,
      isUser: false,
      timestamp: new Date(),
    })
  } catch (error) {
    console.error('Chat error:', error)
    messages.value.push({
      id: messages.value.length + 1,
      text: 'I apologize, but I encountered an error. Please try again.',
      isUser: false,
      timestamp: new Date(),
    })
  } finally {
    isLoading.value = false
  }
}

const handleKeyPress = (event: KeyboardEvent) => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault()
    sendMessage()
  }
}

const formatTime = (date: Date) => {
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
  })
}

// Auto-scroll to bottom when messages change
watch(
  messages,
  () => {
    nextTick(() => {
      if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
      }
    })
  },
  { deep: true }
)
</script>

<template>
  <Modal
    :close-button="true"
    :close-explicitly="true"
    slideover
    max-width="lg"
    position="right"
  >
    <ModalRoot class="flex h-[100vh] flex-col !overflow-hidden">
      <ModalHeader
        title="Let's build your brand"
        description="Start a conversation about your project"
      />

      <!-- Messages Area -->
      <ModalScrollable ref="messagesContainer" class="flex-1 space-y-4 py-4">
        <div
          v-for="message in messages"
          :key="message.id"
          class="flex gap-3"
          :class="message.isUser ? 'flex-row-reverse' : 'flex-row'"
        >
          <!-- Avatar -->
          <div
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
            :class="
              message.isUser
                ? 'bg-primary'
                : 'bg-muted border border-border'
            "
          >
            <Sparkles
              v-if="!message.isUser"
              class="h-4 w-4 text-muted-foreground"
            />
            <span v-else class="text-xs font-semibold text-primary-foreground">U</span>
          </div>

          <!-- Message Content -->
          <div class="max-w-[80%] space-y-1">
            <div
              class="rounded-2xl px-4 py-2.5 text-sm"
              :class="
                message.isUser
                  ? 'bg-primary text-primary-foreground rounded-tr-sm'
                  : 'bg-muted text-foreground rounded-tl-sm'
              "
            >
              <p class="whitespace-pre-wrap leading-relaxed">{{ message.text }}</p>
            </div>
            <p
              class="px-1 text-xs text-muted-foreground"
              :class="message.isUser ? 'text-right' : 'text-left'"
            >
              {{ formatTime(message.timestamp) }}
            </p>
          </div>
        </div>

        <!-- Loading indicator -->
        <div v-if="isLoading" class="flex gap-3">
          <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-muted border border-border">
            <Sparkles class="h-4 w-4 text-muted-foreground animate-pulse" />
          </div>
          <div class="max-w-[80%] space-y-1">
            <div class="rounded-2xl rounded-tl-sm bg-muted px-4 py-2.5 text-sm text-foreground">
              <div class="flex gap-1">
                <span class="size-2 rounded-full bg-muted-foreground/50 animate-bounce" style="animation-delay: 0ms"></span>
                <span class="size-2 rounded-full bg-muted-foreground/50 animate-bounce" style="animation-delay: 150ms"></span>
                <span class="size-2 rounded-full bg-muted-foreground/50 animate-bounce" style="animation-delay: 300ms"></span>
              </div>
            </div>
          </div>
        </div>
      </ModalScrollable>
      <form @submit.prevent="sendMessage">
        <ModalFooter class="border-t py-4">
          <InputGroup class="[--radius:12px]">
            <InputGroupTextarea
              v-model="newMessage"
              @keydown="handleKeyPress"
              placeholder="Tell us about your project..."
              rows="1"
              class="min-h-[44px] max-h-32 resize-none"
            />
            <InputGroupAddon align="block-end">
              <div class="flex w-full items-center justify-between">
                <p class="text-[11px] text-muted-foreground">
                  Press Enter to send, Shift+Enter for new line
                </p>
                <InputGroupButton
                  variant="default"
                  size="icon-xs"
                  :disabled="!newMessage.trim()"
                  class="rounded-full"
                >
                  <ArrowUp class="size-3.5" />
                  <span class="sr-only">Send</span>
                </InputGroupButton>
              </div>
            </InputGroupAddon>
          </InputGroup>
        </ModalFooter>
      </form>
    </ModalRoot>
  </Modal>
</template>
