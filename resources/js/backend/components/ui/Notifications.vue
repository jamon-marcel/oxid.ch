<template>
  <div class="notifications" aria-live="polite">
    <TransitionGroup name="notification">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="['notification', notification.type]"
        @click="dismiss(notification)"
      >{{ notification.text }}</div>
    </TransitionGroup>
  </div>
</template>
<script setup>
import { notifications, dismiss } from '@/lib/notify';
</script>
