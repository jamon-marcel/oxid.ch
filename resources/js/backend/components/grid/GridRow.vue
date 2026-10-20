<template>
  <div class="rounded-md border border-gray-200 bg-white dark:border-warm-700 dark:bg-warm-900">
    <div class="flex h-52 items-center gap-12 border-b px-12 text-gray-400 dark:text-warm-500" :class="collapsed ? 'border-transparent' : 'border-gray-100 dark:border-warm-800'">
      <span class="row-handle cursor-grab active:cursor-grabbing" title="Zeile verschieben"><PhDotsSixVertical :size="16" /></span>
      <LayoutIcon :layout="layout" :width="24" />
      <!-- collapsed: small previews keep the row recognisable while sorting -->
      <span v-if="collapsed" class="flex items-center gap-4 overflow-hidden">
        <img v-for="element in placed" :key="element.id" :src="imageUrl(element.image, 'thumbnail')" class="size-24 rounded-sm object-cover" alt="">
      </span>
      <span class="flex-1" />
      <button type="button" class="cursor-pointer hover:text-red-600" title="Zeile löschen" @click="emit('delete')">
        <PhTrash :size="16" weight="light" />
      </button>
      <button type="button" class="cursor-pointer transition-transform hover:text-gray-900 dark:hover:text-warm-100" :class="collapsed ? 'rotate-90' : ''" :title="collapsed ? 'Aufklappen' : 'Zuklappen'" @click="emit('collapse')">
        <PhCaretDown :size="14" />
      </button>
    </div>

    <div v-show="!collapsed" class="p-12">
      <div class="grid max-w-[48rem] gap-12" :style="{ gridTemplateColumns: layout.columns.map(column => `${column.fr}fr`).join(' ') }">
        <div v-for="(column, c) in layout.columns" :key="c" class="flex flex-col gap-12">
          <GridCell
            v-for="cell in column.cells"
            :key="cell.position"
            :element="elementAt(cell.position)"
            :ratio="cell.ratio"
            @add="emit('add', cell.position)"
            @remove="emit('remove', elementAt(cell.position))"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { computed } from 'vue';
import { PhDotsSixVertical, PhTrash, PhCaretDown } from '@phosphor-icons/vue';
import GridCell from '@/components/grid/GridCell.vue';
import LayoutIcon from '@/components/grid/LayoutIcon.vue';
import { imageUrl } from '@/lib/images';

// A row of the project layout, drawn from its layout spec
const props = defineProps({
  // { id, layout: { key }, elements: [{ id, position, image }] }
  row: { type: Object, required: true },
  layout: { type: Object, required: true },
  collapsed: { type: Boolean, default: false },
});

const emit = defineEmits(['add', 'remove', 'delete', 'collapse']);

const placed = computed(() => (props.row.elements ?? []).filter(element => element.image));
const elementAt = position => placed.value.find(element => Number(element.position) === position) ?? null;
</script>
