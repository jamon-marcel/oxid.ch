<template>
  <!-- v-show: fields keep their state (editors, uploads) between tabs -->
  <div v-show="activeTab === name">
    <slot />
  </div>
</template>
<script setup>
import { inject } from 'vue';

defineProps({
  name: { type: String, required: true },
});

const activeTab = inject('activeTab');
</script>
