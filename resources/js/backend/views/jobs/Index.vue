<template>
  <ResourceIndex
    title="Jobs"
    :listing="jobs"
    :columns="columns"
    :routes="{ create: 'job-create', edit: 'job-edit' }"
    create-label="Neuer Job"
    :order="order"
  />
</template>
<script setup>
import { reactive } from 'vue';
import ResourceIndex from '@/components/resource/ResourceIndex.vue';
import { useListing } from '@/composables/useListing';
import { useOrder } from '@/composables/useOrder';

const columns = [
  { key: 'title', label: 'Titel', value: row => row.title?.de, primary: true },
];

const jobs = reactive(useListing({ list: '/api/jobs/get', resource: 'job' }));
const order = useOrder({ url: '/api/job/order', key: 'jobs' });
</script>
