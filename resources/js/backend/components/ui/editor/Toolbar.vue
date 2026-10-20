<template>
  <div class="border-b border-gray-200 bg-gray-50 px-8 py-6 dark:border-warm-700 dark:bg-warm-800">
    <div class="flex flex-wrap items-center gap-2">
      <template v-for="action in actions" :key="action.title">
        <span v-if="action.divider" class="mx-4 h-16 w-px bg-gray-200 dark:bg-warm-700" aria-hidden="true"></span>
        <button
          v-else
          type="button"
          class="cursor-pointer rounded p-8 transition-colors focus-visible:ring-2 focus-visible:ring-gray-200 focus-visible:outline-none disabled:cursor-default disabled:opacity-40 dark:focus-visible:ring-warm-700"
          :class="action.active?.()
            ? 'bg-gray-200 text-gray-900 dark:bg-warm-700 dark:text-warm-100'
            : 'text-gray-400 hover:text-gray-900 dark:text-warm-500 dark:hover:text-warm-100'"
          :disabled="action.disabled?.()"
          :title="action.title"
          :aria-label="action.title"
          @click="action.run()"
        >
          <component :is="action.icon" :size="16" weight="light" />
        </button>
      </template>
    </div>
    <LinkDialog ref="linkDialog" :editor="editor" />
  </div>
</template>
<script setup>
import { ref } from 'vue';
import {
  PhArrowUUpLeft, PhArrowUUpRight, PhTextHOne, PhTextHTwo, PhTextHThree, PhTextB,
  PhTextSuperscript, PhArrowsInLineHorizontal, PhLink, PhEraser,
} from '@phosphor-icons/vue';
import LinkDialog from './LinkDialog.vue';

const props = defineProps({
  editor: { type: Object, required: true },
});

const linkDialog = ref(null);
const chain = () => props.editor.chain().focus();

const heading = (level, icon) => ({
  title: `Überschrift ${level}`,
  icon,
  active: () => props.editor.isActive('heading', { level }),
  run: () => chain().toggleHeading({ level }).run(),
});

const actions = [
  {
    title: 'Rückgängig',
    icon: PhArrowUUpLeft,
    disabled: () => !props.editor.can().undo(),
    run: () => chain().undo().run(),
  },
  {
    title: 'Wiederholen',
    icon: PhArrowUUpRight,
    disabled: () => !props.editor.can().redo(),
    run: () => chain().redo().run(),
  },
  { divider: true, title: 'd1' },
  heading(1, PhTextHOne),
  heading(2, PhTextHTwo),
  heading(3, PhTextHThree),
  { divider: true, title: 'd2' },
  {
    title: 'Fett',
    icon: PhTextB,
    active: () => props.editor.isActive('bold'),
    run: () => chain().toggleBold().run(),
  },
  {
    title: 'Hochgestellt',
    icon: PhTextSuperscript,
    active: () => props.editor.isActive('superscript'),
    run: () => chain().toggleSuperscript().run(),
  },
  {
    title: 'Worttrennung deaktivieren',
    icon: PhArrowsInLineHorizontal,
    active: () => props.editor.isActive('noWrap'),
    run: () => chain().toggleNoWrap().run(),
  },
  {
    title: 'Link',
    icon: PhLink,
    active: () => props.editor.isActive('link'),
    run: () => linkDialog.value.open(),
  },
  { divider: true, title: 'd3' },
  {
    title: 'Formatierung entfernen',
    icon: PhEraser,
    run: () => chain().unsetAllMarks().clearNodes().run(),
  },
];
</script>
