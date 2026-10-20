<template>
  <div>
    <div class="mb-32 flex gap-32">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        type="button"
        class="cursor-pointer pb-4 text-xs font-medium tracking-[0.08em] uppercase transition-colors"
        :class="[
          activeTab === tab.key
            ? 'border-b border-neutral-900 text-gray-900 dark:border-warm-200 dark:text-warm-100'
            : 'text-gray-400 hover:text-gray-600 dark:text-warm-500 dark:hover:text-warm-300',
          errors.includes(tab.key) ? '!text-red-500' : '',
        ]"
        @click="activeTab = tab.key"
      >
        {{ tab.label }}
      </button>
    </div>
    <div>
      <slot :active-tab="activeTab" />
    </div>
  </div>
</template>
<script setup>
import { provide, computed } from 'vue';

const props = defineProps({
  modelValue: { type: String, default: null },
  tabs: { type: Array, required: true },
  // keys of the tabs that hold a field with an error
  errors: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:modelValue']);

const activeTab = computed({
  get: () => props.modelValue || props.tabs[0]?.key,
  set: value => emit('update:modelValue', value),
});

provide('activeTab', activeTab);
</script>
