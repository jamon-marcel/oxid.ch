<template>
  <ResourceIndex
    title="Diskurs"
    :listing="discourses"
    :columns="columns"
    :routes="{ create: 'discourse-create', edit: 'discourse-edit' }"
    create-label="Neuer Eintrag"
    :order="order"
    :searchable="row => [row.title?.de, row.heading?.de, row.date?.de, categories[row.category]]"
    search-placeholder="Titel, Rubrik, Datum oder Kategorie"
  />
</template>
<script setup>
import { ref, reactive } from 'vue';
import ResourceIndex from '@/components/resource/ResourceIndex.vue';
import { useListing } from '@/composables/useListing';
import { useOrder } from '@/composables/useOrder';
import http from '@/lib/http';

const categories = ref({});
http.get('/api/settings/discourseCategories').then(({ data }) => categories.value = data);

const columns = [
  { key: 'title', label: 'Titel', value: row => row.title?.de, primary: true, limit: 60 },
  { key: 'heading', label: 'Rubrik', value: row => row.heading?.de },
  { key: 'date', label: 'Datum', value: row => row.date?.de },
  { key: 'category', label: 'Kategorie', value: row => categories.value[row.category] },
];

const discourses = reactive(useListing({ list: '/api/discourses/get', resource: 'discourse' }));
const order = useOrder({ url: '/api/discourse/order', key: 'discourses' });
</script>
