<template>
  <ResourceForm :title="title" :is-edit="isEdit" :loading="!isFetched" :busy="isLoading" @submit="submit" @cancel="router.push({ name: 'team' })">
    <Tabs v-model="tab" :tabs="tabs" :errors="Object.keys(errors).length ? ['data'] : []">
      <Tab name="data">
        <div class="grid grid-cols-2 gap-24">
          <FormField v-model="record.firstname" name="firstname" label="Vorname" :errors="errors" required />
          <FormField v-model="record.name" name="name" label="Nachname" :errors="errors" required />
          <FormField v-model="record.role.de" name="role.de" label="Funktion" placeholder="z.B. Architekt ETH SIA" :errors="errors" />
          <FormField v-model="record.position.de" name="position.de" label="Position" placeholder="z.B. Partner" :errors="errors" />
          <FormField v-model="record.phone" name="phone" label="Telefon" :errors="errors" />
          <FormField v-model="record.email" name="email" label="E-Mail" type="email" :errors="errors" />
        </div>
      </Tab>
      <Tab name="translation">
        <div class="grid grid-cols-2 gap-24">
          <FormField v-model="record.role.en" name="role.en" label="Funktion" :errors="errors" />
          <FormField v-model="record.position.en" name="position.en" label="Position" :errors="errors" />
        </div>
      </Tab>
      <Tab name="files">
        <FileField v-model:files="record.documents" languages @uploaded="files.store" @toggle="files.toggle" @destroy="files.destroy" />
      </Tab>
    </Tabs>

    <template #sidebar>
      <FormField v-model="record.publish" name="publish" label="Veröffentlichen" type="checkbox" />
      <FormField v-model="record.category" name="category" label="Kategorie" type="select" :options="toOptions(categories)" :errors="errors" />
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
import FileField from '@/components/media/FileField.vue';
import { useResourceForm } from '@/composables/useResourceForm';
import { useFiles } from '@/composables/useFiles';
import { translations, toOptions } from '@/lib/utils';
import http from '@/lib/http';

const props = defineProps({
  type: { type: String, required: true },
});

const router = useRouter();

const tabs = [
  { key: 'data', label: 'Daten' },
  { key: 'translation', label: 'Übersetzung' },
  { key: 'files', label: 'Dokumente' },
];
const tab = ref('data');

const categories = ref({});

// 210 older members have role/position null; the model defaults fill them in
const { record, errors, isEdit, isLoading, isFetched, title, submit } = useResourceForm({
  type: props.type,
  endpoint: 'team',
  model: () => ({
    firstname: null,
    name: null,
    email: null,
    phone: null,
    role: translations(),
    position: translations(),
    documents: [],
    publish: 0,
    category: 1,
  }),
  redirect: { name: 'team' },
  titles: { create: 'Neues Mitglied', edit: 'Mitglied bearbeiten' },
  load: [() => http.get('/api/settings/teamCategories').then(({ data }) => categories.value = data)],
});

const files = useFiles({ record, isLoading, endpoint: 'team', fields: () => ({ language: 0 }) });
</script>
