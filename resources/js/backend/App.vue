<template>
  <router-view v-if="isAuth" :key="route.fullPath" />
  <AppLayout v-else>
    <router-view :key="route.fullPath" />
  </AppLayout>
</template>
<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AppLayout from '@/components/layout/AppLayout.vue';

const route = useRoute();
const isAuth = computed(() => ['login', 'logout'].includes(route.name));
</script>
