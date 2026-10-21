<template>
  <!-- The page's actions, fixed top right -->
  <div class="fixed top-16 right-16 z-20 flex items-center gap-12 p-24">
    <slot name="before" />
    <slot>
      <FormButton v-if="cancelLabel" variant="secondary" @click="emit('cancel')">{{ cancelLabel }}</FormButton>
      <FormButton v-if="submitLabel" type="submit" :disabled="busy">{{ submitLabel }}</FormButton>
    </slot>
  </div>
</template>
<script setup>
import FormButton from '@/components/ui/form/FormButton.vue';

defineProps({
  submitLabel: { type: String, default: null },
  cancelLabel: { type: String, default: null },
  // saving: the submit button waits
  busy: { type: Boolean, default: false },
});

const emit = defineEmits(['cancel']);
</script>
