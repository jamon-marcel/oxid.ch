<template>
  <div class="flex min-h-dvh min-w-[64rem] bg-gray-100 dark:bg-warm-950">
    <AppSidebar />
    <div class="ml-240 flex-1 p-16">
      <main class="relative min-h-[calc(100dvh-2rem)] rounded-2xl bg-white p-24 pb-32 dark:bg-warm-900">
        <ToastContainer />
        <slot />
      </main>
    </div>
    <ConfirmDialog />
  </div>
</template>
<script setup>
import AppSidebar from '@/components/layout/AppSidebar.vue';
import ToastContainer from '@/components/ui/toast/Container.vue';
import ConfirmDialog from '@/components/ui/dialog/ConfirmDialog.vue';
</script>
