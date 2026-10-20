<template>
  <ResourceIndex
    title="Projekte"
    :listing="projects"
    :columns="columns"
    :routes="{ create: 'project-create', edit: 'project-edit' }"
    create-label="Neues Projekt"
    :order="order"
    :row-label="row => row.title_short?.de"
    :searchable="row => [row.title?.de, row.title_short?.de, row.location?.de, row.year]"
    search-placeholder="Projekt, Ort oder Jahr"
  >
    <template #cell-highlight="{ row }">
      <Badge v-if="Number(row.is_highlight) === 1">Highlight</Badge>
    </template>
    <template #row-actions="{ row }">
      <router-link
        v-if="row.images?.length"
        :to="{ name: 'project-edit', params: { id: row.id }, query: { tab: 'grid' } }"
        class="rounded text-gray-400 transition-colors hover:text-gray-900 dark:text-warm-500 dark:hover:text-warm-100"
        title="Layout"
      >
        <PhSquaresFour :size="16" weight="light" />
      </router-link>
    </template>
  </ResourceIndex>
</template>
<script setup>
import { reactive } from 'vue';
import { PhSquaresFour } from '@phosphor-icons/vue';
import ResourceIndex from '@/components/resource/ResourceIndex.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import { useListing } from '@/composables/useListing';
import { useOrder } from '@/composables/useOrder';

const columns = [
  { key: 'title', label: 'Projekt', value: row => row.title_short?.de, primary: true },
  { key: 'location', label: 'Ort', value: row => row.location?.de },
  { key: 'year', label: 'Jahr' },
  { key: 'highlight', label: '' },
];

const projects = reactive(useListing({ list: '/api/projects/get', resource: 'project' }));
const order = useOrder({ url: '/api/project/order', key: 'projects' });
</script>
