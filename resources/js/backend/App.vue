<template>
  <div>
    <notifications classes="notification" />
    <PageHeader v-if="!isAuth" />
    <router-view :key="route.fullPath" />
  </div>
</template>
<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import PageHeader from '@/views/layout/PageHeader.vue';

const route = useRoute();
const isAuth = computed(() => ['login', 'logout'].includes(route.name));
</script>
