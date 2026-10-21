<template>
  <ResourceIndex
    title="Benutzer"
    :listing="users"
    :columns="columns"
    :routes="{ create: 'user-create', edit: 'user-edit' }"
    create-label="Neuer Benutzer"
    :togglable="false"
    :row-label="row => row.name"
  />
</template>
<script setup>
import { reactive } from 'vue';
import ResourceIndex from '@/components/resource/ResourceIndex.vue';
import { useListing } from '@/composables/useListing';

const columns = [
  { key: 'name', label: 'Name', primary: true },
  { key: 'email', label: 'E-Mail' },
];

const users = reactive(useListing({ list: '/api/users/get', resource: 'user' }));
</script>
