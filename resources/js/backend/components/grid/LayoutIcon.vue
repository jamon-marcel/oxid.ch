<template>
  <svg :width="width" :height="height" :viewBox="`0 0 ${width} ${height}`" class="shrink-0" aria-hidden="true">
    <rect v-for="(r, i) in rects" :key="i" :x="r.x" :y="r.y" :width="r.w" :height="r.h" rx="1" class="fill-current" />
  </svg>
</template>
<script setup>
import { computed } from 'vue';

// A small preview drawn from a layout spec (layouts.js), no icon per layout
const props = defineProps({
  layout: { type: Object, required: true },
  width: { type: Number, default: 36 },
});

const gap = 2;
const height = computed(() => props.width * 2 / 3);

const rects = computed(() => {
  const columns = props.layout.columns;
  const total = columns.reduce((sum, column) => sum + column.fr, 0);
  const unit = (props.width - gap * (columns.length - 1)) / total;
  const out = [];
  let x = 0;

  for (const column of columns) {
    const w = column.fr * unit;
    const sum = column.cells.reduce((a, cell) => a + cell.ratio, 0);
    const available = height.value - gap * (column.cells.length - 1);
    let y = 0;
    for (const cell of column.cells) {
      const h = cell.ratio / sum * available;
      out.push({ x, y, w, h });
      y += h + gap;
    }
    x += w + gap;
  }

  return out;
});
</script>
