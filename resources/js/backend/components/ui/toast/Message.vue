<template>
  <div class="flex cursor-pointer items-center gap-10 rounded-md px-16 py-12 text-xs shadow-sm" :class="styles[toast.type] ?? styles.error" @click="emit('close')">
    <component :is="icons[toast.type] ?? icons.error" :size="14" weight="light" class="shrink-0" />
    <span class="flex-1">{{ toast.text }}</span>
    <PhX :size="12" weight="light" class="shrink-0 opacity-60 transition-opacity hover:opacity-100" />
  </div>
</template>
<script setup>
import { PhX, PhCheck, PhWarning } from '@phosphor-icons/vue';

// notify({ type: 'success' | 'error' | 'warn', text }); warnings look like errors
defineProps({
  toast: { type: Object, required: true },
});

const emit = defineEmits(['close']);

const styles = {
  success: 'bg-emerald-600 text-white',
  error: 'bg-red-500 text-white',
};

const icons = {
  success: PhCheck,
  error: PhWarning,
};
</script>
