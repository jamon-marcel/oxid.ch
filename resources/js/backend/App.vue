<template>
  <div>
    <Notifications />
    <PageHeader v-if="!isAuth" />
    <router-view :key="route.fullPath" />
  </div>
</template>
<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import PageHeader from '@/views/layout/PageHeader.vue';
import Notifications from '@/components/ui/Notifications.vue';

const route = useRoute();
const isAuth = computed(() => ['login', 'logout'].includes(route.name));
</script>
