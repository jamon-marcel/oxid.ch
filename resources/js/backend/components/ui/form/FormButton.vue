<template>
  <button
    :type="type"
    class="inline-flex cursor-pointer items-center justify-center gap-8 rounded-md px-16 py-8 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-gray-200 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:focus-visible:ring-warm-700"
    :class="{
      'bg-gray-900 text-white hover:bg-gray-800 dark:bg-warm-100 dark:text-warm-900 dark:hover:bg-warm-200': variant === 'primary',
      'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-warm-800 dark:text-warm-300 dark:hover:bg-warm-700': variant === 'secondary',
      'bg-red-600 text-white hover:bg-red-700': variant === 'destructive',
    }"
  >
    <slot />
  </button>
</template>
<script setup>
defineProps({
  type: { type: String, default: 'button' },
  // primary | secondary | destructive
  variant: { type: String, default: 'primary' },
});
</script>
