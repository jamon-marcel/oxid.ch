<template>
  <!-- Floats at the bottom of the content area; Escape clears it -->
  <div class="pointer-events-none fixed right-16 bottom-32 left-256 z-20 flex justify-center px-24">
    <div class="pointer-events-auto relative w-full max-w-400">
      <PhMagnifyingGlass :size="16" weight="light" class="absolute top-1/2 left-16 -translate-y-1/2 text-gray-400 dark:text-warm-500" />
      <input
        v-model="model"
        type="search"
        name="search"
        :placeholder="placeholder"
        :aria-label="placeholder"
        class="block w-full rounded-full border border-gray-200 bg-white py-12 pr-40 pl-40 text-sm shadow-lg transition-all placeholder:text-gray-400 focus:ring-1 focus:ring-gray-200 focus:outline-none dark:border-warm-700 dark:bg-warm-800 dark:text-warm-100 dark:placeholder:text-warm-600 dark:focus:ring-warm-700 [&::-webkit-search-cancel-button]:hidden"
        @keydown.esc="model = ''"
      >
      <button
        v-if="model"
        type="button"
        class="absolute top-1/2 right-12 -translate-y-1/2 cursor-pointer rounded-full p-4 text-gray-400 hover:text-gray-900 dark:text-warm-500 dark:hover:text-warm-100"
        title="Suche zurücksetzen"
        @click="model = ''"
      >
        <PhX :size="14" />
      </button>
    </div>
  </div>
</template>
<script setup>
import { PhMagnifyingGlass, PhX } from '@phosphor-icons/vue';

defineProps({
  placeholder: { type: String, default: 'Suchen' },
});

const model = defineModel({ type: String, default: '' });
</script>
