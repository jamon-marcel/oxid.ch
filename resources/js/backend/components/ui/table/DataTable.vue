<template>
  <div class="-mx-4 -my-2 overflow-x-auto whitespace-nowrap sm:-mx-6 lg:-mx-8">
    <div class="inline-block min-w-full px-4 py-2 align-middle sm:px-6 lg:px-8">
      <table class="w-full text-sm">
        <!-- grouped: the group headers replace the column headings (kept for screen readers) -->
        <thead :class="groupBy ? 'sr-only' : ''">
          <tr class="border-b border-gray-900/10 text-left dark:border-warm-700/50">
            <th
              v-for="col in columns"
              :key="col.key"
              class="py-12 text-xs font-medium whitespace-nowrap text-gray-400 dark:text-warm-500"
              :class="[
                col.class || '',
                col.align === 'right' ? 'text-right' : '',
                col.sortable && !draggableRows ? 'cursor-pointer select-none hover:text-gray-700 dark:hover:text-warm-300' : '',
              ]"
              @click="toggleSort(col)"
            >
              <span class="inline-flex items-center gap-4">
                {{ col.label }}
                <span v-if="col.sortable && !draggableRows && sortKey === col.key" class="text-gray-400 dark:text-warm-500">
                  {{ sortDir === 'asc' ? '↑' : '↓' }}
                </span>
              </span>
            </th>
          </tr>
        </thead>
        <template v-if="draggableRows">
          <draggable
            v-for="(group, groupIndex) in draggableGroups"
            :key="group.key"
            :model-value="group.rows"
            tag="tbody"
            item-key="id"
            ghost-class="opacity-30"
            :animation="150"
            @update:model-value="reorderGroup(groupIndex, $event)"
          >
            <template v-if="group.label" #header>
              <tr>
                <td :colspan="columns.length" :class="groupIndex === 0 ? 'pt-8' : 'pt-32'" class="border-b border-gray-900/10 pb-8 text-sm font-medium text-gray-900 dark:border-warm-700/50 dark:text-warm-100">{{ group.label }}</td>
              </tr>
            </template>
            <template #item="{ element: row }">
              <tr class="cursor-move border-b border-gray-900/6 hover:bg-gray-50 dark:border-warm-700/40 dark:hover:bg-warm-800">
                <td
                  v-for="col in columns"
                  :key="col.key"
                  class="py-16 first:pl-4 last:pr-4"
                  :class="cellClass(row, col)"
                >
                  <slot :name="`cell-${col.key}`" :row="row" :value="valueOf(row, col)">
                    {{ valueOf(row, col) }}
                  </slot>
                </td>
              </tr>
            </template>
          </draggable>
        </template>
        <template v-else>
          <tbody v-for="(group, groupIndex) in groups" :key="group.key">
            <tr v-if="group.label">
              <td :colspan="columns.length" :class="groupIndex === 0 ? 'pt-8' : 'pt-32'" class="border-b border-gray-900/10 pb-8 text-sm font-medium text-gray-900 dark:border-warm-700/50 dark:text-warm-100">{{ group.label }}</td>
            </tr>
            <tr
              v-for="(row, index) in group.rows"
              :key="row.id ?? index"
              class="border-b border-gray-900/6 hover:bg-gray-50 dark:border-warm-700/40 dark:hover:bg-warm-800"
            >
              <td
                v-for="col in columns"
                :key="col.key"
                class="py-16 first:pl-4 last:pr-4"
                :class="cellClass(row, col)"
              >
                <slot :name="`cell-${col.key}`" :row="row" :value="valueOf(row, col)">
                  {{ valueOf(row, col) }}
                </slot>
              </td>
            </tr>
          </tbody>
        </template>
      </table>
    </div>
  </div>
</template>
<script setup>
import { ref, computed } from 'vue';
import draggable from 'vuedraggable';

/**
 * columns: [{ key, label, value, primary, sortable, class, align }];
 *          value: row => the cell's value (default row[key])
 * groupBy: row => ({ key, label }); rows (already in group order) get a
 *          header per group, and dragging stays within a group
 * Emits reorder(rows of the group that changed, all rows).
 */
const props = defineProps({
  columns: { type: Array, required: true },
  rows: { type: Array, required: true },
  draggableRows: { type: Boolean, default: false },
  groupBy: { type: Function, default: null },
});

const model = defineModel();

const emit = defineEmits(['reorder']);

const sortKey = ref(null);
const sortDir = ref('asc');

function toggleSort(col) {
  if (!col.sortable || props.draggableRows) {
    return;
  }
  if (sortKey.value === col.key) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
  }
  else {
    sortKey.value = col.key;
    sortDir.value = 'asc';
  }
}

// Consecutive rows with the same group key form one group; ungrouped: one
// group without a label
function toGroups(rows) {
  if (!props.groupBy) {
    return [{ key: 'all', label: null, rows }];
  }
  return rows.reduce((groups, row) => {
    const { key, label } = props.groupBy(row);
    const last = groups[groups.length - 1];
    last?.key === key ? last.rows.push(row) : groups.push({ key, label, rows: [row] });
    return groups;
  }, []);
}

const draggableGroups = computed(() => toGroups(model.value ?? []));

function reorderGroup(index, rows) {
  model.value = draggableGroups.value.flatMap((group, i) => i === index ? rows : group.rows);
  emit('reorder', rows, model.value);
}

const sortedRows = computed(() => {
  if (!sortKey.value || props.draggableRows || props.groupBy) {
    return props.rows;
  }
  return [...props.rows].sort((a, b) => {
    const col = props.columns.find(column => column.key === sortKey.value);
    const result = String(valueOf(a, col)).localeCompare(String(valueOf(b, col)), undefined, { numeric: true, sensitivity: 'base' });
    return sortDir.value === 'asc' ? result : -result;
  });
});

const groups = computed(() => toGroups(sortedRows.value));

function valueOf(row, col) {
  const value = col.value ? col.value(row) : row[col.key];
  return typeof value === 'string' || typeof value === 'number' ? value : '';
}

// Unpublished rows are dimmed (but not their actions)
function cellClass(row, col) {
  return [
    col.class || '',
    col.align === 'right' ? 'text-right' : '',
    col.primary ? 'text-gray-900 dark:text-warm-100' : 'text-sm text-gray-400 dark:text-warm-500',
    row.publish !== undefined && Number(row.publish) === 0 && col.key !== 'actions' ? 'opacity-40' : '',
  ];
}
</script>
