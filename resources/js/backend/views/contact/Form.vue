<template>
  <ResourceForm :title="title" :is-edit="isEdit" :loading="!isFetched" :busy="isLoading" :sidebar="false" @submit="submit" @cancel="router.push({ name: 'contact' })">
    <Tabs v-model="tab" :tabs="tabs">
      <Tab name="data">
        <div class="flex max-w-[48rem] flex-col gap-24">
          <FormField v-model="record.address.de" name="address.de" label="Adresse" type="editor" :errors="errors" tall />
          <FormField v-model="record.google_maps_url" name="google_maps_url" label="Google Maps URL" :errors="errors" />
          <FormField v-for="field in textFields" :key="field.key" v-model="record[field.key].de" :name="`${field.key}.de`" :label="field.label" type="editor" :errors="errors" tall />
        </div>
      </Tab>
      <Tab name="translation">
        <div class="flex max-w-[48rem] flex-col gap-24">
          <FormField v-model="record.address.en" name="address.en" label="Adresse" type="editor" :errors="errors" tall />
          <FormField v-for="field in textFields" :key="field.key" v-model="record[field.key].en" :name="`${field.key}.en`" :label="field.label" type="editor" :errors="errors" tall />
        </div>
      </Tab>
    </Tabs>
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
  { key: 'data', label: 'Kontakt' },
  { key: 'translation', label: 'Übersetzung' },
];
const tab = ref('data');

const textFields = [
  { key: 'contacts', label: 'Kontakte' },
  { key: 'info', label: 'Info' },
  { key: 'imprint', label: 'Impressum' },
];

const { record, errors, isEdit, isLoading, isFetched, title, submit } = useResourceForm({
  type: props.type,
  endpoint: 'contact',
  model: () => ({
    address: translations(),
    contacts: translations(),
    info: translations(),
    imprint: translations(),
    google_maps_url: null,
  }),
  redirect: { name: 'contact' },
  titles: { create: 'Neuer Kontakt', edit: 'Kontakt bearbeiten' },
});
</script>
