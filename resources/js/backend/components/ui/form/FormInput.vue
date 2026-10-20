<template>
  <input
    :id="id"
    v-model="model"
    :type="type"
    :name="name ?? id"
    :placeholder="placeholder"
    class="block w-full rounded-md border bg-white px-12 py-10 text-sm transition-all focus:ring-2 focus:outline-none dark:bg-warm-800 dark:text-warm-100 dark:placeholder:text-warm-600"
    :class="hasError
      ? 'border-red-400 focus:border-red-400 focus:ring-red-200 dark:border-red-500 dark:focus:border-red-500 dark:focus:ring-red-800'
      : 'border-gray-200 focus:border-gray-300 focus:ring-gray-200 dark:border-warm-700 dark:focus:border-warm-600 dark:focus:ring-warm-700'"
  >
</template>
<script setup>
const model = defineModel();

defineProps({
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: null },
  id: { type: String, default: null },
  name: { type: String, default: null },
  hasError: { type: Boolean, default: false },
});
</script>
