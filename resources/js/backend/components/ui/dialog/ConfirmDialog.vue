<template>
  <AppDialog :open="dialog.open" :title="dialog.title" @close="answer(false)">
    <p class="text-sm text-gray-600 dark:text-warm-400">{{ dialog.text }}</p>
    <template #footer>
      <div class="flex justify-end gap-16">
        <FormButton variant="secondary" @click="answer(false)">Abbrechen</FormButton>
        <FormButton variant="destructive" autofocus @click="answer(true)">{{ dialog.action }}</FormButton>
      </div>
    </template>
  </AppDialog>
</template>
<script setup>
import AppDialog from '@/components/ui/dialog/AppDialog.vue';
import FormButton from '@/components/ui/form/FormButton.vue';
import { dialog, answer } from '@/lib/confirm';
</script>
