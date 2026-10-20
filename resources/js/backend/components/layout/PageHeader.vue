<template>
  <div class="mb-40 flex items-center">
    <h1 class="text-lg font-medium text-gray-900 dark:text-warm-100">{{ title }}</h1>
  </div>
</template>
<script setup>
defineProps({
  title: { type: String, required: true },
});
</script>
