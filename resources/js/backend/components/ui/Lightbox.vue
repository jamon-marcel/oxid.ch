<template>
  <dialog ref="dialog" :class="['lightbox', `lightbox--${size}`]" @close="emit('close')" @click.self="close()">
    <div class="lightbox__frame">
      <header class="lightbox__header">
        <h2>{{ title }}</h2>
        <a href="javascript:;" class="feather-icon" title="Schliessen" @click.prevent="close()">
          <PhX :size="24" weight="light" />
        </a>
      </header>
      <div :class="['lightbox__body', { 'lightbox__body--fill': fill }]">
        <slot />
      </div>
      <footer class="lightbox__footer" v-if="$slots.footer">
        <slot name="footer" />
      </footer>
    </div>
  </dialog>
</template>
<script setup>
import { ref, watch, onMounted } from 'vue';
import { PhX } from '@phosphor-icons/vue';

// The one shell for every overlay of the admin: a modal <dialog> (Escape,
// focus and the backdrop come with it). Header with title and close,
// scrolling body, footer for the buttons.
const props = defineProps({
  open: { type: Boolean, default: false },
  title: { type: String, default: '' },
  // 'full': the whole window; 'small': a centred panel (link dialog)
  size: { type: String, default: 'full' },
  // the body takes the height between header and footer instead of
  // scrolling (the cropper fits the window)
  fill: { type: Boolean, default: false },
});

// Closed by the close button, Escape or a click on the backdrop
const emit = defineEmits(['close']);

const dialog = ref(null);

function sync(open) {
  if (open && !dialog.value.open) {
    dialog.value.showModal();
  }
  else if (!open && dialog.value.open) {
    dialog.value.close();
  }
}

function close() {
  dialog.value.close();
}

onMounted(() => sync(props.open));
watch(() => props.open, sync);

defineExpose({ close });
</script>
