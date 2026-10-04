<template>
  <div class="editor__toolbar">
    <button
      v-for="action in actions"
      :key="action.title"
      type="button"
      :class="['editor__button', { 'is-active': action.active?.() }]"
      :disabled="action.disabled?.()"
      :title="action.title"
      @click="action.run()"
    >
      <component :is="action.icon" :size="16" weight="light" />
    </button>
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
  heading(1, PhTextHOne),
  heading(2, PhTextHTwo),
  heading(3, PhTextHThree),
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
  {
    title: 'Formatierung entfernen',
    icon: PhEraser,
    run: () => chain().unsetAllMarks().clearNodes().run(),
  },
];
</script>
