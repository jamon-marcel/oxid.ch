<template>
  <div class="relative">
    <select
      :id="id"
      v-model="model"
      :name="name ?? id"
      :disabled="disabled"
      class="block w-full appearance-none rounded-md border bg-white px-12 py-10 pr-32 text-sm transition-all focus:ring-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-40 dark:bg-warm-800 dark:text-warm-100"
      :class="hasError
        ? 'border-red-400 focus:border-red-400 focus:ring-red-200 dark:border-red-500 dark:focus:border-red-500 dark:focus:ring-red-800'
        : 'border-gray-200 focus:border-gray-300 focus:ring-gray-200 dark:border-warm-700 dark:focus:border-warm-600 dark:focus:ring-warm-700'"
    >
      <option v-if="placeholder" :value="null">{{ placeholder }}</option>
      <option v-for="option in options" :key="option.value" :value="option.value">{{ option.label }}</option>
    </select>
    <svg
      class="pointer-events-none absolute top-1/2 right-10 -translate-y-1/2 text-gray-400 dark:text-warm-400"
      width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
    >
      <polyline points="2 4 6 8 10 4" />
    </svg>
  </div>
</template>
<script setup>
const model = defineModel();

defineProps({
  id: { type: String, default: null },
  name: { type: String, default: null },
  // [{ value, label }]
  options: { type: Array, default: () => [] },
  placeholder: { type: String, default: null },
  disabled: { type: Boolean, default: false },
  hasError: { type: Boolean, default: false },
});
</script>
