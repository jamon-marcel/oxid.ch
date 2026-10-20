<template>
  <div :class="searchable ? 'pb-64' : ''">
    <PageHeader :title="title" />
    <FormActions v-if="createLabel || $slots.actions">
      <slot name="actions" />
      <FormButton v-if="createLabel" @click="router.push({ name: routes.create })">{{ createLabel }}</FormButton>
    </FormActions>

    <slot name="before" />

    <div v-if="!listing.isFetched && !rows.length" class="text-sm text-gray-400 dark:text-warm-500">Laden...</div>
    <div v-else-if="!rows.length" class="text-sm text-gray-400 dark:text-warm-500">Keine Einträge vorhanden.</div>
    <div v-else-if="!visibleRows.length" class="text-sm text-gray-400 dark:text-warm-500">Keine Treffer für «{{ search.trim() }}».</div>
    <DataTable
      v-else
      v-model="rows"
      :columns="tableColumns"
      :rows="visibleRows"
      :draggable-rows="!!order && !terms.length"
      :group-by="groupBy"
      @reorder="rows => order(rows)"
    >
      <template v-for="col in columns" #[`cell-${col.key}`]="scope">
        <slot :name="`cell-${col.key}`" v-bind="scope">
          <button v-if="col.primary" type="button" class="cursor-pointer text-left hover:underline" @click="edit(scope.row)">{{ scope.value }}</button>
          <span v-else-if="col.limit && scope.value?.length > col.limit" :title="scope.value">{{ scope.value.slice(0, col.limit).trimEnd() }}…</span>
          <template v-else>{{ scope.value }}</template>
        </slot>
      </template>
      <template #cell-actions="{ row }">
        <div class="flex items-center justify-end gap-12">
          <slot name="row-actions" :row="row" />
          <button
            v-if="togglable"
            type="button"
            class="cursor-pointer rounded transition-colors"
            :class="Number(row.publish) === 1
              ? 'text-gray-400 hover:text-gray-900 dark:text-warm-500 dark:hover:text-warm-100'
              : 'text-gray-300 hover:text-gray-600 dark:text-warm-700 dark:hover:text-warm-500'"
            :title="Number(row.publish) === 1 ? 'Veröffentlicht' : 'Nicht veröffentlicht'"
            @click="listing.toggle(row.id)"
          >
            <PhEye v-if="Number(row.publish) === 1" :size="16" weight="light" />
            <PhEyeSlash v-else :size="16" weight="light" />
          </button>
          <button type="button" class="cursor-pointer rounded text-gray-400 transition-colors hover:text-gray-900 dark:text-warm-500 dark:hover:text-warm-100" title="Bearbeiten" @click="edit(row)">
            <PhPencil :size="16" weight="light" />
          </button>
          <button v-if="deletable" type="button" class="cursor-pointer rounded text-gray-400 transition-colors hover:text-red-600 dark:text-warm-500" title="Löschen" @click="listing.destroy(row.id, rowLabel(row))">
            <PhTrash :size="16" weight="light" />
          </button>
        </div>
      </template>
    </DataTable>

    <SearchBar v-if="searchable" v-model="search" :placeholder="searchPlaceholder" />
  </div>
</template>
<script setup>
import { ref, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { PhPencil, PhTrash, PhEye, PhEyeSlash } from '@phosphor-icons/vue';
import PageHeader from '@/components/layout/PageHeader.vue';
import FormActions from '@/components/ui/form/FormActions.vue';
import FormButton from '@/components/ui/form/FormButton.vue';
import DataTable from '@/components/ui/table/DataTable.vue';
import SearchBar from '@/components/ui/search/SearchBar.vue';

/**
 * List page for a resource (useListing): table, publish toggle, edit,
 * delete and, with order (useOrder), drag & drop ordering. Cell slots go
 * to the table. Column options besides DataTable's: limit (max.
 * characters, full text on hover). searchable (row => [texts]) adds the
 * floating search bar; every word must match one of the texts. Ordering is
 * off while searching (only a subset is shown).
 */
const props = defineProps({
  title: { type: String, required: true },
  // reactive(useListing(…))
  listing: { type: Object, required: true },
  columns: { type: Array, required: true },
  // { create, edit } route names
  routes: { type: Object, required: true },
  createLabel: { type: String, default: null },
  // useOrder(…): saves the rows of the group that was reordered
  order: { type: Function, default: null },
  deletable: { type: Boolean, default: true },
  togglable: { type: Boolean, default: true },
  rowLabel: { type: Function, default: row => row.title?.de ?? row.title ?? '' },
  // row => ({ key, label }), see DataTable
  groupBy: { type: Function, default: null },
  searchable: { type: Function, default: null },
  searchPlaceholder: { type: String, default: 'Suchen' },
});

const route = useRoute();
const router = useRouter();
const rows = ref([]);

watch(() => props.listing.items, items => rows.value = [...items], { immediate: true });

// Kept per list for the browser tab: back from the form, the same results show
const searchKey = `search:${String(route.name)}`;
const search = ref(sessionStorage.getItem(searchKey) ?? '');
watch(search, value => value.trim() ? sessionStorage.setItem(searchKey, value) : sessionStorage.removeItem(searchKey));

const terms = computed(() => search.value.toLowerCase().split(/\s+/).filter(Boolean));
const visibleRows = computed(() => {
  if (!props.searchable || !terms.value.length) {
    return rows.value;
  }
  return rows.value.filter(row => {
    const text = props.searchable(row).filter(Boolean).join(' ').toLowerCase();
    return terms.value.every(term => text.includes(term));
  });
});

const tableColumns = [...props.columns, { key: 'actions', label: '', class: 'w-100', align: 'right' }];

function edit(row) {
  router.push({ name: props.routes.edit, params: { id: row.id } });
}
</script>
