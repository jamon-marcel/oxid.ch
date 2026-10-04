<template></template>
<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import http from '@/lib/http';

const router = useRouter();

onMounted(async () => {
  try {
    await http.post('/api/auth/logout');
  }
  catch {
    // Logged out either way
  }
  router.push({ name: 'login' });
});
</script>
