<template>
  <ResourceForm :title="title" :is-edit="isEdit" :loading="!isFetched" :busy="isLoading" @submit="submit" @cancel="router.push({ name: 'discourses' })">
    <Tabs v-model="tab" :tabs="tabs" :errors="errorTabs">
      <Tab name="data">
        <div class="flex flex-col gap-24">
          <div class="grid grid-cols-2 gap-24">
            <FormField v-model="record.heading.de" name="heading.de" label="Rubrik" :errors="errors" required />
            <FormField v-model="record.date.de" name="date.de" label="Datum/Zeitraum" :errors="errors" required />
          </div>
          <FormField v-model="record.title.de" name="title.de" label="Titel" :errors="errors" required />
          <FormField v-model="record.description_short.de" name="description_short.de" label="Kurzbeschreibung" type="editor" :errors="errors" required />
          <FormField v-model="record.description.de" name="description.de" label="Beschreibung" type="editor" :errors="errors" />
          <FormField v-model="record.info.de" name="info.de" label="Info" type="editor" :errors="errors" />
        </div>
      </Tab>
      <Tab name="translation">
        <div class="flex flex-col gap-24">
          <div class="grid grid-cols-2 gap-24">
            <FormField v-model="record.heading.en" name="heading.en" label="Rubrik" :errors="errors" />
            <FormField v-model="record.date.en" name="date.en" label="Datum/Zeitraum" :errors="errors" />
          </div>
          <FormField v-model="record.title.en" name="title.en" label="Titel" :errors="errors" />
          <FormField v-model="record.description_short.en" name="description_short.en" label="Kurzbeschreibung" type="editor" :errors="errors" />
          <FormField v-model="record.description.en" name="description.en" label="Beschreibung" type="editor" :errors="errors" />
          <FormField v-model="record.info.en" name="info.en" label="Info" type="editor" :errors="errors" />
        </div>
      </Tab>
      <Tab name="images">
        <ImageField
          v-model:images="record.images"
          endpoint="discourse"
          sortable
          :error="!!errors.images"
          @uploaded="upload => { images.store(upload); delete errors.images; }"
          @toggle="images.toggle"
          @destroy="images.destroy"
          @save-coords="images.saveCoords"
        >
          <template #badges="{ image }">
            <Badge v-if="Number(image.is_preview) === 1">Vorschaubild</Badge>
          </template>
          <template #fields="{ image }">
            <div class="flex flex-col gap-12">
              <FormField v-model="image.is_preview" name="is_preview" label="Vorschaubild" type="checkbox" />
              <FormField v-model="image.theme" name="theme" label="Helles Theme" type="checkbox" />
            </div>
          </template>
        </ImageField>
      </Tab>
      <Tab name="files">
        <FileField v-model:files="record.documents" @uploaded="files.store" @toggle="files.toggle" @destroy="files.destroy" />
      </Tab>
    </Tabs>

    <template #sidebar>
      <FormField v-model="record.publish" name="publish" label="Veröffentlichen" type="checkbox" />
      <FormField v-model="record.category" name="category" label="Kategorie" type="select" :options="toOptions(categories)" :errors="errors" />
    </template>
  </ResourceForm>
</template>
<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import ResourceForm from '@/components/resource/ResourceForm.vue';
import FormField from '@/components/ui/form/FormField.vue';
import Tabs from '@/components/ui/tabs/Tabs.vue';
import Tab from '@/components/ui/tabs/Tab.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import ImageField from '@/components/media/ImageField.vue';
import FileField from '@/components/media/FileField.vue';
import { useResourceForm } from '@/composables/useResourceForm';
import { useImages } from '@/composables/useImages';
import { useFiles } from '@/composables/useFiles';
import { translations, requiredErrors, toOptions } from '@/lib/utils';
import http from '@/lib/http';

const props = defineProps({
  type: { type: String, required: true },
});

const router = useRouter();

const tabs = [
  { key: 'data', label: 'Diskurs' },
  { key: 'translation', label: 'Übersetzung' },
  { key: 'images', label: 'Bilder' },
  { key: 'files', label: 'Dokumente' },
];
const tab = ref('data');

const categories = ref({});

const { record, errors, isEdit, isLoading, isFetched, title, submit } = useResourceForm({
  type: props.type,
  endpoint: 'discourse',
  model: () => ({
    heading: translations(),
    date: translations(),
    title: translations(),
    description_short: translations(),
    description: translations(),
    info: translations(),
    images: [],
    documents: [],
    publish: 0,
    category: 1,
  }),
  redirect: { name: 'discourses' },
  titles: { create: 'Neuer Eintrag', edit: 'Eintrag bearbeiten' },
  load: [() => http.get('/api/settings/discourseCategories').then(({ data }) => categories.value = data)],
  // Checked here too, so all errors show at once: the API doesn't check
  // the title or the images
  validate: discourse => requiredErrors(discourse, ['heading.de', 'date.de', 'title.de', 'description_short.de', 'images']),
});

const errorTabs = computed(() => [
  ...(Object.keys(errors.value).some(key => key !== 'images') ? ['data'] : []),
  ...(errors.value.images ? ['images'] : []),
]);

const images = useImages({
  record, isLoading,
  endpoint: 'discourse',
  fields: () => ({ is_preview: 0, theme: 0 }),
});

const files = useFiles({ record, isLoading, endpoint: 'discourse' });
</script>
