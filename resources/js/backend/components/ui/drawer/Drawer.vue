<template>
  <Teleport to="body">
    <div v-if="mounted" class="fixed inset-0 z-50">
      <Transition name="fade">
        <div v-if="visible" class="absolute inset-0 bg-black/30" @click="dismissible && close()" />
      </Transition>

      <div
        class="absolute top-16 right-16 bottom-16 flex w-full flex-col rounded-2xl bg-white shadow-xl transition-transform duration-200 ease-out dark:bg-warm-900 dark:shadow-none"
        :class="[sizes[size], visible ? 'translate-x-0' : 'translate-x-full']"
        role="dialog"
        aria-modal="true"
        :aria-label="title"
      >
        <div class="flex items-center justify-between border-b border-neutral-200 px-24 py-20 dark:border-warm-800">
          <slot name="header">
            <h3 class="text-sm font-medium text-gray-900 dark:text-warm-100">{{ title }}</h3>
          </slot>
          <button type="button" class="cursor-pointer text-gray-400 transition-colors hover:text-gray-900 dark:text-warm-500 dark:hover:text-warm-100" title="Schliessen" @click="close">
            <PhX :size="16" weight="light" />
          </button>
        </div>

        <div class="flex-1 overflow-y-auto">
          <slot />
        </div>

        <div v-if="$slots.footer" class="px-24 pt-16 pb-24">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </Teleport>
</template>
<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { PhX } from '@phosphor-icons/vue';

// A panel sliding in from the right (edit an image, pick a layout or an image)
const props = defineProps({
  open: { type: Boolean, default: false },
  title: { type: String, default: null },
  // sm | md | lg
  size: { type: String, default: 'sm' },
  // closes on a click on the backdrop
  dismissible: { type: Boolean, default: true },
});

const emit = defineEmits(['close']);

const visible = ref(false);
const mounted = ref(false);
const closing = ref(false);

const sizes = {
  sm: 'max-w-[420px]',
  md: 'max-w-[560px]',
  lg: 'max-w-[720px]',
};

// Pending unmount; cancelled when the drawer opens again within 200 ms
let unmountTimer = null;

function unmountLater(callback = null) {
  clearTimeout(unmountTimer);
  unmountTimer = setTimeout(() => {
    mounted.value = false;
    callback?.();
  }, 200);
}

watch(() => props.open, open => {
  if (open) {
    clearTimeout(unmountTimer);
    closing.value = false;
    mounted.value = true;
    requestAnimationFrame(() => visible.value = true);
  }
  else if (!closing.value && mounted.value) {
    visible.value = false;
    unmountLater();
  }
}, { immediate: true });

function close() {
  if (closing.value) {
    return;
  }
  closing.value = true;
  visible.value = false;
  unmountLater(() => emit('close'));
}

function onKeydown(event) {
  if (event.key === 'Escape' && props.open) {
    document.activeElement?.blur();
    close();
  }
}

onMounted(() => document.addEventListener('keydown', onKeydown));
onUnmounted(() => {
  clearTimeout(unmountTimer);
  document.removeEventListener('keydown', onKeydown);
});
</script>
<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity .2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
