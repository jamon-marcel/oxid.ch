<template>
  <div
    class="group relative w-full overflow-hidden rounded-sm"
    :class="element ? 'bg-gray-100 dark:bg-warm-800' : 'border border-dashed border-gray-300 dark:border-warm-600'"
    :style="{ paddingTop: `${ratio}%` }"
  >
    <div class="absolute inset-0">
      <template v-if="element">
        <!-- The /img/crop/… URL redirects to the signed rendition (05-image-pipeline.md) -->
        <img :src="`/img/crop/${element.image.name}/2400/1500/${element.image.coords}`" alt="" class="h-full w-full object-cover">
        <span v-if="caption" class="absolute inset-x-0 bottom-0 truncate bg-black/50 px-8 py-6 text-xs text-white">{{ caption }}</span>
        <button
          type="button"
          class="absolute top-8 right-8 cursor-pointer rounded bg-black/70 p-6 text-white opacity-0 transition-opacity group-hover:opacity-100 focus-visible:opacity-100"
          title="Entfernen"
          @click="emit('remove')"
        >
          <PhTrash :size="14" weight="light" />
        </button>
      </template>
      <button
        v-else
        type="button"
        class="flex h-full w-full cursor-pointer flex-col items-center justify-center gap-4 text-gray-400 transition-colors hover:bg-gray-50 hover:text-gray-900 dark:text-warm-500 dark:hover:bg-warm-800 dark:hover:text-warm-100"
        @click="emit('add')"
      >
        <PhPlus :size="18" weight="light" />
        <span class="text-xs">Bild</span>
      </button>
    </div>
  </div>
</template>
<script setup>
import { computed } from 'vue';
import { PhPlus, PhTrash } from '@phosphor-icons/vue';

// One box of a layout row: the image, or a button to place one
const props = defineProps({
  element: { type: Object, default: null },
  // height in % of the width
  ratio: { type: Number, required: true },
});

const emit = defineEmits(['add', 'remove']);

const caption = computed(() => {
  const caption = props.element?.image?.caption?.de;
  return caption && caption !== 'null' && caption !== 'undefined' ? caption : null;
});
</script>
