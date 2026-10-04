<template>
  <div :class="['editor', { 'editor--error': hasError }]">
    <Toolbar v-if="editor" :editor="editor" />
    <EditorContent :editor="editor" class="editor__content" />
  </div>
</template>
<script setup>
import { watch, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Superscript from '@tiptap/extension-superscript';
import Toolbar from './Toolbar.vue';
import { serialize } from './serialize';
import { Div } from './div';
import { NoWrap } from './noWrap';

// Replaces TinyMCE 5. Same features as its oxid config: bold, superscript,
// link, "Worttrennung deaktivieren", headings 1–3, remove formatting.
// Ported from luvo; see .rewrite/tools/tiptap-roundtrip.mjs for the check
// that stored content survives it.

defineProps({
  hasError: { type: Boolean, default: false },
});

const model = defineModel({ type: String, default: '' });

const editor = useEditor({
  content: model.value ?? '',
  extensions: [
    StarterKit.configure({
      heading: { levels: [1, 2, 3] },
      blockquote: false,
      code: false,
      codeBlock: false,
      horizontalRule: false,
      strike: false,
      underline: false,
      link: false,
    }),
    Link.configure({
      openOnClick: false,
      autolink: false,
      HTMLAttributes: { target: null, rel: null },
    }),
    Superscript,
    NoWrap,
    Div,
  ],
  // TinyMCE pasted as plain text; keep pasted Word/web formatting out
  editorProps: {
    transformPastedHTML: html => html.replace(/ style="[^"]*"/gi, '').replace(/ class="[^"]*"/gi, ''),
  },
  onUpdate: ({ editor }) => {
    model.value = serialize(editor);
  },
});

// Content set from outside (e.g. after loading the record)
watch(model, value => {
  if (!editor.value || value === serialize(editor.value)) {
    return;
  }
  editor.value.commands.setContent(value ?? '', { emitUpdate: false });
});

onBeforeUnmount(() => editor.value?.destroy());
</script>
