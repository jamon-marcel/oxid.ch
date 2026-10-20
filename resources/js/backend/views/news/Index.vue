<template>
  <ResourceIndex
    title="News"
    :listing="news"
    :columns="columns"
    :routes="{ create: 'news-create', edit: 'news-edit' }"
    create-label="Neue News"
    :order="order"
    :searchable="row => [row.title?.de, row.subtitle?.de]"
    search-placeholder="Titel oder Untertitel"
  >
    <template #cell-sticky="{ row }">
      <Badge v-if="Number(row.sticky) === 1">Angeheftet</Badge>
    </template>
  </ResourceIndex>
</template>
<script setup>
import { reactive } from 'vue';
import ResourceIndex from '@/components/resource/ResourceIndex.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import { useListing } from '@/composables/useListing';
import { useOrder } from '@/composables/useOrder';
import { formatDate } from '@/lib/utils';

const columns = [
  { key: 'title', label: 'Titel', value: row => row.title?.de, primary: true },
  { key: 'date_end', label: 'Publizieren bis', value: row => formatDate(row.date_end) },
  { key: 'sticky', label: '' },
];

const news = reactive(useListing({ list: '/api/news/get', resource: 'news' }));
const order = useOrder({ url: '/api/news/order', key: 'news' });
</script>
