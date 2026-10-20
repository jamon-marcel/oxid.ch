<template>
  <div class="fixed right-32 bottom-32 z-50 flex w-full max-w-280 flex-col gap-6" aria-live="polite">
    <TransitionGroup
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0 translate-y-[-8px]"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-[-8px]"
    >
      <ToastMessage v-for="notification in notifications" :key="notification.id" :toast="notification" @close="dismiss(notification)" />
    </TransitionGroup>
  </div>
</template>
<script setup>
import ToastMessage from '@/components/ui/toast/Message.vue';
import { notifications, dismiss } from '@/lib/notify';
</script>
