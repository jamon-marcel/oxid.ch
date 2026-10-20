<template>
  <ResourceIndex
    title="Profil"
    :listing="profile"
    :columns="columns"
    :routes="{ edit: 'profile-edit' }"
    :deletable="false"
  />
</template>
<script setup>
import { reactive } from 'vue';
import ResourceIndex from '@/components/resource/ResourceIndex.vue';
import { useListing } from '@/composables/useListing';

const columns = [
  { key: 'title', label: 'Titel', value: row => row.title?.de, primary: true },
];

const profile = reactive(useListing({ list: '/api/profile/get', resource: 'profile' }));
</script>
