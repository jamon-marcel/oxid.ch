<template>
  <ResourceForm :title="title" :is-edit="isEdit" :loading="!isFetched" :busy="isLoading" :sidebar="false" @submit="submit" @cancel="router.push({ name: 'users' })">
    <div class="flex max-w-[32rem] flex-col gap-24">
      <FormField v-model="record.name" name="name" label="Name" :errors="errors" required />
      <FormField v-model="record.email" name="email" label="E-Mail" type="email" autocomplete="off" :errors="errors" required />
      <FormField
        v-model="record.password"
        name="password"
        label="Passwort"
        type="password"
        autocomplete="new-password"
        :hint="isEdit ? 'Leer lassen, um das Passwort zu behalten. Ein neues Passwort meldet den Benutzer auf anderen Geräten ab.' : 'Mindestens 8 Zeichen.'"
        :errors="errors"
        :required="!isEdit"
      />
      <FormField v-model="record.password_confirmation" name="password_confirmation" label="Passwort bestätigen" type="password" autocomplete="new-password" :errors="errors" :required="!isEdit" />
    </div>
  </ResourceForm>
</template>
<script setup>
import { useRouter } from 'vue-router';
import ResourceForm from '@/components/resource/ResourceForm.vue';
import FormField from '@/components/ui/form/FormField.vue';
import { useResourceForm } from '@/composables/useResourceForm';

const props = defineProps({
  type: { type: String, required: true },
});

const router = useRouter();

const { record, errors, isEdit, isLoading, isFetched, title, submit } = useResourceForm({
  type: props.type,
  endpoint: 'user',
  model: () => ({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
  }),
  redirect: { name: 'users' },
  titles: { create: 'Neuer Benutzer', edit: 'Benutzer bearbeiten' },
  // 'bereits vergeben', 'mindestens 8 Zeichen' say more than Pflichtfeld
  messages: true,
});
</script>
