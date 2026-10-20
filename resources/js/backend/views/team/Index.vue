<template>
  <ResourceIndex
    title="Team"
    :listing="team"
    :columns="columns"
    :routes="{ create: 'team-create', edit: 'team-edit' }"
    create-label="Neues Mitglied"
    :order="order"
    :group-by="row => ({ key: row.category, label: categories[row.category] ?? '' })"
    :row-label="row => `${row.firstname} ${row.name}`"
    :searchable="row => [row.firstname, row.name, row.role?.de, row.position?.de, categories[row.category]]"
    search-placeholder="Name, Funktion, Position oder Kategorie"
  />
</template>
<script setup>
import { ref, reactive } from 'vue';
import ResourceIndex from '@/components/resource/ResourceIndex.vue';
import { useListing } from '@/composables/useListing';
import { useOrder } from '@/composables/useOrder';
import http from '@/lib/http';

// Categories in this order, not by id; members are ordered within theirs
const displayOrder = [1, 4, 5, 2, 3];

const categories = ref({});
http.get('/api/settings/teamCategories').then(({ data }) => categories.value = data);

const columns = [
  { key: 'name', label: 'Name', value: row => `${row.firstname} ${row.name}`, primary: true },
  { key: 'role', label: 'Funktion', value: row => row.role?.de },
  { key: 'position', label: 'Position', value: row => row.position?.de },
];

// The API returns the members grouped by category
const team = reactive(useListing({
  list: '/api/teams/get',
  resource: 'team',
  transform: data => Object.values(data).flat()
    .sort((a, b) => displayOrder.indexOf(Number(a.category)) - displayOrder.indexOf(Number(b.category))),
}));
const order = useOrder({ url: '/api/team/order', key: 'teams' });
</script>
