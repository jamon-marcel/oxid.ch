<template>
  <Drawer :open="open" title="Neue Zeile" size="sm" @close="emit('close')">
    <div class="grid grid-cols-2 gap-12 px-24 py-16">
      <button
        v-for="layout in available"
        :key="layout.id"
        type="button"
        class="flex cursor-pointer flex-col items-center gap-10 rounded-md border border-gray-200 px-12 py-16 text-gray-400 hover:border-gray-400 hover:text-gray-900 dark:border-warm-700 dark:text-warm-500 dark:hover:text-warm-100"
        @click="emit('select', layout.id)"
      >
        <LayoutIcon :layout="layouts[layout.key]" :width="72" />
        <span class="text-center text-xs">{{ layouts[layout.key].label }}</span>
      </button>
    </div>
  </Drawer>
</template>
<script setup>
import { computed } from 'vue';
import Drawer from '@/components/ui/drawer/Drawer.vue';
import LayoutIcon from '@/components/grid/LayoutIcon.vue';
import { layouts } from '@/components/grid/layouts';

// Drawer to choose the layout of a new row
const props = defineProps({
  open: { type: Boolean, default: false },
  // from the API: [{ id, key }]
  options: { type: Array, default: () => [] },
});

const emit = defineEmits(['close', 'select']);

const available = computed(() => props.options.filter(layout => layouts[layout.key]));
</script>
