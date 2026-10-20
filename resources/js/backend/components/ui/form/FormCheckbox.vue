<template>
  <label class="group inline-flex cursor-pointer items-center gap-10">
    <span class="relative flex size-14 items-center justify-center">
      <input
        :id="id"
        type="checkbox"
        :name="name ?? id"
        :checked="checked"
        class="peer size-14 cursor-pointer appearance-none rounded border border-gray-200 bg-white transition-colors checked:border-gray-900 checked:bg-gray-900 focus-visible:ring-2 focus-visible:ring-gray-200 focus-visible:ring-offset-0 focus-visible:outline-none dark:border-warm-600 dark:bg-warm-800 dark:checked:border-warm-100 dark:checked:bg-warm-100 dark:focus-visible:ring-warm-700"
        @change="model = $event.target.checked ? 1 : 0"
      >
      <svg
        class="pointer-events-none absolute text-white opacity-0 transition-opacity peer-checked:opacity-100 dark:text-warm-900"
        width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
      >
        <polyline points="1.5 5.5 4 8 8.5 2.5" />
      </svg>
    </span>
    <span class="text-sm text-gray-500 transition-colors select-none group-hover:text-gray-900 dark:text-warm-400 dark:group-hover:text-warm-100">
      <slot />
    </span>
  </label>
</template>
<script setup>
import { computed } from 'vue';

// The API's flags are 0/1 (sometimes as strings, older records booleans);
// the checkbox stores 1 or 0
const model = defineModel();

defineProps({
  id: { type: String, default: null },
  name: { type: String, default: null },
});

const checked = computed(() => model.value === true || Number(model.value) === 1);
</script>
