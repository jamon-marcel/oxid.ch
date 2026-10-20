<template>
  <ResourceForm :title="title" :is-edit="isEdit" :loading="!isFetched" :busy="isLoading" @submit="submit" @cancel="router.push({ name: 'profile' })">
    <Tabs v-model="tab" :tabs="tabs" :errors="Object.keys(errors).length ? ['data'] : []">
      <Tab name="data">
        <div class="flex flex-col gap-24">
          <FormField v-model="record.title.de" name="title.de" label="Titel" :errors="errors" required />
          <FormField v-model="record.description.de" name="description.de" label="Text" type="editor" :errors="errors" tall />
        </div>
      </Tab>
      <Tab name="translation">
        <div class="flex flex-col gap-24">
          <FormField v-model="record.title.en" name="title.en" label="Titel" :errors="errors" />
          <FormField v-model="record.description.en" name="description.en" label="Text" type="editor" :errors="errors" tall />
        </div>
      </Tab>
    </Tabs>

    <template #sidebar>
      <FormField v-model="record.publish" name="publish" label="Veröffentlichen" type="checkbox" />
    </template>
  </ResourceForm>
</template>
<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import ResourceForm from '@/components/resource/ResourceForm.vue';
import FormField from '@/components/ui/form/FormField.vue';
import Tabs from '@/components/ui/tabs/Tabs.vue';
import Tab from '@/components/ui/tabs/Tab.vue';
import { useResourceForm } from '@/composables/useResourceForm';
import { translations } from '@/lib/utils';

const props = defineProps({
  type: { type: String, required: true },
});

const router = useRouter();

const tabs = [
  { key: 'data', label: 'Text' },
  { key: 'translation', label: 'Übersetzung' },
];
const tab = ref('data');

const { record, errors, isEdit, isLoading, isFetched, title, submit } = useResourceForm({
  type: props.type,
  endpoint: 'profile',
  model: () => ({
    title: translations(),
    description: translations(),
    publish: 0,
  }),
  redirect: { name: 'profile' },
  titles: { create: 'Neuer Profiltext', edit: 'Profil bearbeiten' },
});
</script>
