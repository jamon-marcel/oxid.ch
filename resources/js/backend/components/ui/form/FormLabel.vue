<template>
  <label
    :for="$props.for"
    class="mb-8 block text-xs"
    :class="error ? 'text-red-500 dark:text-red-400' : 'text-gray-500 dark:text-warm-400'"
  >
    {{ error ? message : undefined }}<slot v-if="!error" />
  </label>
</template>
<script setup>
import { computed } from 'vue';

// The field's label; while the field has an error, the error takes its place.
// error: the API's messages, a message, or true (a required field left empty)
const props = defineProps({
  for: { type: String, default: null },
  error: { type: [String, Array, Boolean], default: null },
  // shown for error === true
  required: { type: String, default: 'Pflichtfeld' },
});

const message = computed(() => {
  if (Array.isArray(props.error)) {
    return props.error[0];
  }
  return typeof props.error === 'string' ? props.error : props.required;
});
</script>
