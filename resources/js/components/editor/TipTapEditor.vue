<script setup lang="ts">
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Placeholder from '@tiptap/extension-placeholder'
import { watch, onBeforeUnmount } from 'vue'
import { cn } from '@/lib/utils'
import { 
  Bold, 
  Italic, 
  List, 
  ListOrdered, 
  Quote,
  Undo,
  Redo,
  Heading1,
  Heading2,
  Heading3,
  Minus,
} from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

interface Props {
  modelValue?: string
  placeholder?: string
  class?: string
  disabled?: boolean
  minHeight?: string
  maxHeight?: string
  fontSize?: 'sm' | 'base' | 'lg'
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Start typing...',
  disabled: false,
  minHeight: '200px',
  maxHeight: undefined,
  fontSize: 'base',
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const editor = useEditor({
  content: props.modelValue || '',
  extensions: [
    StarterKit.configure({
      heading: {
        levels: [1, 2, 3],
      },
    }),
    Placeholder.configure({
      placeholder: props.placeholder,
    }),
  ],
  editorProps: {
    attributes: {
      class: cn(
        'prose prose-sm sm:prose-sm focus:outline-none',
        'max-w-none p-4',
        'prose-headings:font-bold prose-headings:text-foreground prose-h1:text-2xl prose-h2:text-xl prose-h3:text-lg',
        'prose-p:text-foreground prose-p:leading-relaxed prose-p:my-1',
        'prose-strong:text-foreground prose-strong:font-semibold',
        'prose-em:text-foreground',
        'prose-ul:list-disc prose-ul:pl-4 prose-ul:my-1',
        'prose-ol:list-decimal prose-ol:pl-4 prose-ol:my-1',
        'prose-li:text-foreground prose-li:my-0.5',
        'prose-blockquote:border-l-4 prose-blockquote:border-primary prose-blockquote:pl-4 prose-blockquote:italic prose-blockquote:my-1',
        'prose-code:text-xs prose-code:bg-muted prose-code:px-1 prose-code:py-0.5 prose-code:rounded',
        'dark:prose-invert',
      ),
    },
  },
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML())
  },
  editable: !props.disabled,
})

watch(() => props.modelValue, (value) => {
  const isSame = editor.value?.getHTML() === value
  if (!isSame && editor.value) {
    editor.value.commands.setContent(value || '')
  }
})

watch(() => props.disabled, (disabled) => {
  if (editor.value) {
    editor.value.setEditable(!disabled)
  }
})

onBeforeUnmount(() => {
  editor.value?.destroy()
})

const toggleBold = () => {
  editor.value?.chain().focus().toggleBold().run()
}

const toggleItalic = () => {
  editor.value?.chain().focus().toggleItalic().run()
}

const toggleHeading = (level: 1 | 2 | 3) => {
  editor.value?.chain().focus().toggleHeading({ level }).run()
}

const insertHorizontalRule = () => {
  editor.value?.chain().focus().setHorizontalRule().run()
}

const toggleBulletList = () => {
  editor.value?.chain().focus().toggleBulletList().run()
}

const toggleOrderedList = () => {
  editor.value?.chain().focus().toggleOrderedList().run()
}

const toggleBlockquote = () => {
  editor.value?.chain().focus().toggleBlockquote().run()
}

const undo = () => {
  editor.value?.chain().focus().undo().run()
}

const redo = () => {
  editor.value?.chain().focus().redo().run()
}
</script>

<template>
  <div 
    :class="cn(
      'rounded-md border border-input bg-background ring-offset-background',
      'focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2',
      props.disabled && 'cursor-not-allowed opacity-50',
      props.class
    )"
  >
    <!-- Toolbar -->
    <div 
      v-if="!disabled"
      class="flex items-center gap-1 border-b border-input bg-muted/30 p-2"
    >
      <div class="flex items-center gap-1">
        <Button
          type="button"
          variant="ghost"
          size="icon"
          class="h-8 w-8"
          :class="{ 'bg-accent': editor?.isActive('bold') }"
          @click="toggleBold"
          title="Bold"
        >
          <Bold class="h-4 w-4" />
        </Button>
        <Button
          type="button"
          variant="ghost"
          size="icon"
          class="h-8 w-8"
          :class="{ 'bg-accent': editor?.isActive('italic') }"
          @click="toggleItalic"
          title="Italic"
        >
          <Italic class="h-4 w-4" />
        </Button>
      </div>

      <div class="mx-1 h-6 w-px bg-border" />

      <div class="flex items-center gap-1">
        <Button
          type="button"
          variant="ghost"
          size="icon"
          class="h-8 w-8"
          :class="{ 'bg-accent': editor?.isActive('heading', { level: 1 }) }"
          @click="toggleHeading(1)"
          title="Heading 1"
        >
          <Heading1 class="h-4 w-4" />
        </Button>
        <Button
          type="button"
          variant="ghost"
          size="icon"
          class="h-8 w-8"
          :class="{ 'bg-accent': editor?.isActive('heading', { level: 2 }) }"
          @click="toggleHeading(2)"
          title="Heading 2"
        >
          <Heading2 class="h-4 w-4" />
        </Button>
        <Button
          type="button"
          variant="ghost"
          size="icon"
          class="h-8 w-8"
          :class="{ 'bg-accent': editor?.isActive('heading', { level: 3 }) }"
          @click="toggleHeading(3)"
          title="Heading 3"
        >
          <Heading3 class="h-4 w-4" />
        </Button>
      </div>

      <div class="mx-1 h-6 w-px bg-border" />

      <div class="flex items-center gap-1">
        <Button
          type="button"
          variant="ghost"
          size="icon"
          class="h-8 w-8"
          :class="{ 'bg-accent': editor?.isActive('bulletList') }"
          @click="toggleBulletList"
          title="Bullet List"
        >
          <List class="h-4 w-4" />
        </Button>
        <Button
          type="button"
          variant="ghost"
          size="icon"
          class="h-8 w-8"
          :class="{ 'bg-accent': editor?.isActive('orderedList') }"
          @click="toggleOrderedList"
          title="Numbered List"
        >
          <ListOrdered class="h-4 w-4" />
        </Button>
        <Button
          type="button"
          variant="ghost"
          size="icon"
          class="h-8 w-8"
          :class="{ 'bg-accent': editor?.isActive('blockquote') }"
          @click="toggleBlockquote"
          title="Quote"
        >
          <Quote class="h-4 w-4" />
        </Button>
        <Button
          type="button"
          variant="ghost"
          size="icon"
          class="h-8 w-8"
          @click="insertHorizontalRule"
          title="Divider"
        >
          <Minus class="h-4 w-4" />
        </Button>
      </div>

      <div class="mx-1 h-6 w-px bg-border" />

      <div class="flex items-center gap-1">
        <Button
          type="button"
          variant="ghost"
          size="icon"
          class="h-8 w-8"
          :disabled="!editor?.can().undo()"
          @click="undo"
          title="Undo"
        >
          <Undo class="h-4 w-4" />
        </Button>
        <Button
          type="button"
          variant="ghost"
          size="icon"
          class="h-8 w-8"
          :disabled="!editor?.can().redo()"
          @click="redo"
          title="Redo"
        >
          <Redo class="h-4 w-4" />
        </Button>
      </div>
    </div>

    <!-- Editor Content -->
    <div :style="{ minHeight: minHeight, maxHeight: maxHeight, overflow: maxHeight ? 'auto' : 'visible' }">
      <EditorContent :editor="editor" />
    </div>
  </div>
</template>

<style>
.ProseMirror {
  outline: none;
}

.ProseMirror p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left;
  color: hsl(var(--muted-foreground));
  pointer-events: none;
  height: 0;
}
</style>
