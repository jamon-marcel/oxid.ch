<template>
  <div v-if="isFetched" class="flex flex-col gap-24">
    <div class="flex items-center gap-16">
      <span class="text-xs text-gray-400 dark:text-warm-500">{{ rows.length }} {{ rows.length === 1 ? 'Zeile' : 'Zeilen' }}</span>
      <span class="flex-1" />
      <button v-if="rows.length > 1" type="button" class="cursor-pointer text-xs text-gray-500 hover:text-gray-900 dark:text-warm-400 dark:hover:text-warm-100" @click="toggleAll">
        {{ allCollapsed ? 'Alle ausklappen' : 'Alle einklappen' }}
      </button>
      <a :href="`/projekt/${projectId}`" target="_blank" class="text-xs text-gray-500 hover:text-gray-900 dark:text-warm-400 dark:hover:text-warm-100">Vorschau</a>
      <button type="button" class="cursor-pointer rounded-md border border-gray-200 px-12 py-6 text-sm text-gray-700 hover:border-gray-400 dark:border-warm-700 dark:text-warm-300" @click="isLayoutPickerOpen = true">
        + Zeile hinzufügen
      </button>
    </div>
    <p v-if="!rows.length" class="text-sm text-gray-400 dark:text-warm-500">Noch keine Zeilen.</p>

    <draggable v-model="rows" item-key="id" handle=".row-handle" class="flex flex-col gap-16" ghost-class="opacity-30" :animation="150" @end="order(rows)">
      <template #item="{ element: row }">
        <GridRow
          :row="row"
          :layout="layouts[row.layout.key]"
          :collapsed="collapsed.has(row.id)"
          @add="position => openPicker(row, position)"
          @remove="removeElement"
          @delete="deleteRow(row)"
          @collapse="toggleCollapse(row)"
        />
      </template>
    </draggable>

    <LayoutPicker :open="isLayoutPickerOpen" :options="layoutOptions" @close="isLayoutPickerOpen = false" @select="addRow" />
    <GridPicker :open="!!picker" :images="images" @close="picker = null" @select="placeImage" />
  </div>
  <div v-else class="text-sm text-gray-400 dark:text-warm-500">Laden...</div>
</template>
<script setup>
import { ref, computed } from 'vue';
import draggable from 'vuedraggable';
import GridRow from '@/components/grid/GridRow.vue';
import GridPicker from '@/components/grid/GridPicker.vue';
import LayoutPicker from '@/components/grid/LayoutPicker.vue';
import { layouts } from '@/components/grid/layouts';
import { useOrder } from '@/composables/useOrder';
import { notify } from '@/lib/notify';
import { confirmDelete } from '@/lib/utils';
import http from '@/lib/http';

/**
 * The project layout: rows with a layout, each box holding a project
 * image. Changes are saved right away.
 */
const props = defineProps({
  projectId: { type: Number, required: true },
});

const rows = ref([]);
const isFetched = ref(false);
const collapsed = ref(new Set());
const layoutOptions = ref([]);
const isLayoutPickerOpen = ref(false);
const images = ref([]);
// { row, position } while choosing an image
const picker = ref(null);

async function request(call, message = null) {
  try {
    const response = await call();
    if (message) {
      notify({ type: 'success', text: message });
    }
    return response;
  }
  catch {
    // Notified by the http error handler
    return null;
  }
}

async function load() {
  const response = await request(() => http.get(`/api/project/grids/${props.projectId}`));
  if (!response) {
    return;
  }
  const known = new Set(rows.value.map(row => row.id));
  rows.value = response.data.data;
  // Rows start collapsed on the first load; rows added later stay open
  if (!isFetched.value) {
    collapsed.value = new Set(rows.value.map(row => row.id));
  }
  else {
    rows.value.filter(row => !known.has(row.id)).forEach(row => collapsed.value.delete(row.id));
  }
  isFetched.value = true;
}

const allCollapsed = computed(() => rows.value.length > 0 && rows.value.every(row => collapsed.value.has(row.id)));

function toggleCollapse(row) {
  const set = new Set(collapsed.value);
  set.has(row.id) ? set.delete(row.id) : set.add(row.id);
  collapsed.value = set;
}

function toggleAll() {
  collapsed.value = allCollapsed.value ? new Set() : new Set(rows.value.map(row => row.id));
}

async function addRow(layoutId) {
  isLayoutPickerOpen.value = false;
  if (await request(() => http.get(`/api/project/grid/store/${props.projectId}/${layoutId}`), 'Zeile hinzugefügt')) {
    await load();
  }
}

async function deleteRow(row) {
  if (!await confirmDelete('Zeile löschen', { text: 'Zeile löschen? Die Bilder bleiben im Projekt.' })) {
    return;
  }
  if (await request(() => http.delete(`/api/project/grid/delete/${row.id}`), 'Zeile gelöscht')) {
    rows.value = rows.value.filter(item => item.id !== row.id);
  }
}

const order = useOrder({ url: '/api/project/grids/order', key: 'grids', delay: 1000 });

async function openPicker(row, position) {
  picker.value = { row, position };
  const response = await request(() => http.get(`/api/project/image/get/${props.projectId}`));
  images.value = response?.data.data ?? [];
}

async function placeImage(imageId) {
  const { row, position } = picker.value;
  picker.value = null;
  const payload = { position, grid_id: row.id, project_image_id: imageId, project_id: props.projectId };
  if (await request(() => http.post('/api/project/grid/image/store', payload), 'Bild platziert')) {
    await load();
  }
}

async function removeElement(element) {
  if (await request(() => http.delete(`/api/project/grid/image/delete/${element.id}`), 'Bild entfernt')) {
    await load();
  }
}

http.get('/api/project/grid/layouts').then(({ data }) => layoutOptions.value = data.data);
load();
</script>
