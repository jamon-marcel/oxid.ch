<template>
  <dialog
    ref="dialog"
    class="m-auto w-full rounded-2xl bg-white p-0 backdrop:bg-black/30 dark:bg-warm-900"
    :class="sizes[size]"
    @close="onClose"
    @click.self="dismissible && close()"
  >
    <div class="p-24">
      <div v-if="title || $slots.header" class="mb-20 flex items-center justify-between">
        <slot name="header">
          <h2 class="text-sm font-medium text-gray-900 dark:text-warm-100">{{ title }}</h2>
        </slot>
        <button type="button" class="cursor-pointer text-gray-400 transition-colors hover:text-gray-900 dark:text-warm-500 dark:hover:text-warm-100" title="Schliessen" @click="close">
          <PhX :size="16" weight="light" />
        </button>
      </div>
      <div>
        <slot />
      </div>
      <div v-if="$slots.footer" class="mt-24">
        <slot name="footer" />
      </div>
    </div>
  </dialog>
</template>
<script setup>
import { ref, watch, onMounted } from 'vue';
import { PhX } from '@phosphor-icons/vue';

// A modal <dialog> (Escape and focus come with it)
const props = defineProps({
  open: { type: Boolean, default: false },
  title: { type: String, default: null },
  // sm | md | lg
  size: { type: String, default: 'sm' },
  // closes on a click on the backdrop
  dismissible: { type: Boolean, default: true },
});

const emit = defineEmits(['close']);

const dialog = ref(null);

const sizes = {
  sm: 'max-w-360',
  md: 'max-w-480',
  lg: 'max-w-[40rem]',
};

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

// The close event comes a task after close(): when the dialog was opened
// again in between (cancel one confirm, the next delete), it is stale
function onClose() {
  if (dialog.value.open) {
    return;
  }
  document.activeElement?.blur();
  emit('close');
}

onMounted(() => sync(props.open));
watch(() => props.open, sync);
</script>
