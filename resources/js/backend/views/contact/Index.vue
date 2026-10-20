<template>
  <ResourceIndex
    title="Kontakt"
    :listing="contact"
    :columns="columns"
    :routes="{ edit: 'contact-edit' }"
    :togglable="false"
    :deletable="false"
  />
</template>
<script setup>
import { reactive } from 'vue';
import ResourceIndex from '@/components/resource/ResourceIndex.vue';
import { useListing } from '@/composables/useListing';
import { truncate } from '@/lib/utils';

const columns = [
  { key: 'address', label: 'Adresse', value: row => truncate(row.address?.de, 80), primary: true },
];

const contact = reactive(useListing({ list: '/api/contact/get', resource: 'contact' }));
</script>
