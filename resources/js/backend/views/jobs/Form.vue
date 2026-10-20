<template>
  <ResourceForm :title="title" :is-edit="isEdit" :loading="!isFetched" :busy="isLoading" @submit="submit" @cancel="router.push({ name: 'jobs' })">
    <Tabs v-model="tab" :tabs="tabs" :errors="Object.keys(errors).length ? ['data'] : []">
      <Tab name="data">
        <div class="flex flex-col gap-24">
          <FormField v-model="record.title.de" name="title.de" label="Titel" :errors="errors" required />
          <FormField v-model="record.description.de" name="description.de" label="Beschreibung" type="editor" :errors="errors" required />
          <FormField v-model="record.info.de" name="info.de" label="Info" type="editor" :errors="errors" required />
        </div>
      </Tab>
      <Tab name="translation">
        <div class="flex flex-col gap-24">
          <FormField v-model="record.title.en" name="title.en" label="Titel" :errors="errors" />
          <FormField v-model="record.description.en" name="description.en" label="Beschreibung" type="editor" :errors="errors" />
          <FormField v-model="record.info.en" name="info.en" label="Info" type="editor" :errors="errors" />
        </div>
      </Tab>
      <Tab name="files">
        <FileField v-model:files="record.documents" @uploaded="files.store" @toggle="files.toggle" @destroy="files.destroy" />
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
import FileField from '@/components/media/FileField.vue';
import { useResourceForm } from '@/composables/useResourceForm';
import { useFiles } from '@/composables/useFiles';
import { translations, requiredErrors } from '@/lib/utils';

const props = defineProps({
  type: { type: String, required: true },
});

const router = useRouter();

const tabs = [
  { key: 'data', label: 'Job' },
  { key: 'translation', label: 'Übersetzung' },
  { key: 'files', label: 'Dokumente' },
];
const tab = ref('data');

const { record, errors, isEdit, isLoading, isFetched, title, submit } = useResourceForm({
  type: props.type,
  endpoint: 'job',
  model: () => ({
    title: translations(),
    description: translations(),
    info: translations(),
    documents: [],
    publish: 0,
  }),
  redirect: { name: 'jobs' },
  titles: { create: 'Neuer Job', edit: 'Job bearbeiten' },
  // Checked here too, so all errors show at once: the API doesn't check Info
  validate: job => requiredErrors(job, ['title.de', 'description.de', 'info.de']),
});

const files = useFiles({ record, isLoading, endpoint: 'job' });
</script>
