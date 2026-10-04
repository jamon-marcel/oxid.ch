<template>
  <div ref="el">
    <slot />
  </div>
</template>
<script setup>
// Drag ordering for the v-for in the slot; its .is-draggable children are
// the items. Sortable moves the dragged node: it is put back and the array
// reordered instead, so Vue keeps rendering the DOM.
import { onMounted, onBeforeUnmount, useTemplateRef } from 'vue';
import Sortable from 'sortablejs';

const items = defineModel({ type: Array, required: true });

// (items) after a change of order
const emit = defineEmits(['end']);

const el = useTemplateRef('el');
let sortable = null;
let next = null;

onMounted(() => {
  sortable = Sortable.create(el.value, {
    draggable: '.is-draggable',
    ghostClass: 'draggable-ghost',
    onStart: ({ item }) => {
      next = item.nextSibling;
    },
    onEnd: ({ item, from, oldDraggableIndex, newDraggableIndex }) => {
      from.insertBefore(item, next);
      if (oldDraggableIndex === newDraggableIndex) {
        return;
      }
      const list = [...items.value];
      list.splice(newDraggableIndex, 0, ...list.splice(oldDraggableIndex, 1));
      items.value = list;
      emit('end', list);
    },
  });
});

onBeforeUnmount(() => sortable?.destroy());
</script>
