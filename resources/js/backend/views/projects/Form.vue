<template>
  <ResourceForm :title="title" :is-edit="isEdit" :loading="!isFetched" :busy="isLoading" :sidebar="tab !== 'grid'" @submit="submit" @cancel="router.push({ name: 'projects' })">
    <Tabs v-model="tab" :tabs="tabs" :errors="errorTabs">
      <Tab name="data">
        <div class="flex flex-col gap-24">
          <FormField v-model="record.title.de" name="title.de" label="Titel" :errors="errors" required />
          <div class="grid grid-cols-2 gap-24">
            <FormField v-model="record.title_short.de" name="title_short.de" label="Kurztitel" :errors="errors" required />
            <FormField v-model="record.location.de" name="location.de" label="Ort" :errors="errors" required />
          </div>
          <FormField v-model="record.description.de" name="description.de" label="Beschreibung" type="editor" :errors="errors" />
          <FormField v-model="record.info.de" name="info.de" label="Info" type="editor" :errors="errors" />
          <h2 class="pt-16 text-xs font-medium tracking-[0.08em] text-gray-400 uppercase dark:text-warm-500">Werkliste</h2>
          <div class="grid grid-cols-2 gap-24">
            <FormField v-for="field in worksFields" :key="field.key" v-model="record[field.key]" :name="field.key" :label="field.label" :errors="errors" />
          </div>
        </div>
      </Tab>
      <Tab name="translation">
        <div class="flex flex-col gap-24">
          <FormField v-model="record.title.en" name="title.en" label="Titel" :errors="errors" />
          <div class="grid grid-cols-2 gap-24">
            <FormField v-model="record.title_short.en" name="title_short.en" label="Kurztitel" :errors="errors" />
            <FormField v-model="record.location.en" name="location.en" label="Ort" :errors="errors" />
          </div>
          <FormField v-model="record.description.en" name="description.en" label="Beschreibung" type="editor" :errors="errors" />
          <FormField v-model="record.info.en" name="info.en" label="Info" type="editor" :errors="errors" />
        </div>
      </Tab>
      <Tab name="images">
        <ImageField
          v-model:images="record.images"
          endpoint="project"
          :ratio="cropRatio"
          :is-protected="image => Number(image.is_grid) === 1"
          :error="!!errors.images"
          @uploaded="upload => { images.store(upload); delete errors.images; }"
          @toggle="images.toggle"
          @destroy="images.destroy"
          @save-coords="images.saveCoords"
        >
          <template #badges="{ image }">
            <Badge v-if="Number(image.is_grid) === 1">Im Layout</Badge>
            <Badge v-if="Number(image.is_plan) === 1">Plan</Badge>
          </template>
          <template #fields="{ image }">
            <FormField v-model="image.is_preview_navigation" name="is_preview_navigation" label="Vorschaubild Navigation" type="checkbox" />
            <FormField v-model="image.is_preview_works" name="is_preview_works" label="Vorschaubild Werkliste" type="checkbox" />
            <FormField v-model="image.is_plan" name="is_plan" label="Plan" type="checkbox" />
          </template>
        </ImageField>
      </Tab>
      <Tab name="files">
        <FileField v-model:files="record.documents" @uploaded="files.store" @toggle="files.toggle" @destroy="files.destroy" />
      </Tab>
      <Tab v-if="isEdit" name="grid">
        <GridEditor v-if="tab === 'grid'" :project-id="Number(route.params.id)" />
      </Tab>
    </Tabs>

    <template #sidebar>
      <div class="flex flex-col gap-12">
        <FormField v-model="record.publish" name="publish" label="Veröffentlichen" type="checkbox" />
        <FormField v-for="flag in flags" :key="flag.key" v-model="record[flag.key]" :name="flag.key" :label="flag.label" type="checkbox" />
      </div>
      <FormField v-model="record.year" name="year" label="Jahr" :errors="errors" required />
      <FormField v-for="select in selects" :key="select.key" v-model="record[select.key]" :name="select.key" :label="select.label" type="select" :options="toOptions(settings[select.key])" :errors="errors" />
    </template>
  </ResourceForm>
</template>
<script setup>
import { ref, reactive, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ResourceForm from '@/components/resource/ResourceForm.vue';
import FormField from '@/components/ui/form/FormField.vue';
import Tabs from '@/components/ui/tabs/Tabs.vue';
import Tab from '@/components/ui/tabs/Tab.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import ImageField from '@/components/media/ImageField.vue';
import FileField from '@/components/media/FileField.vue';
import GridEditor from '@/components/grid/GridEditor.vue';
import { useResourceForm } from '@/composables/useResourceForm';
import { useImages } from '@/composables/useImages';
import { useFiles } from '@/composables/useFiles';
import { translations, requiredErrors, toOptions } from '@/lib/utils';
import http from '@/lib/http';

const props = defineProps({
  type: { type: String, required: true },
});

const route = useRoute();
const router = useRouter();

// The layout ("Raster") is saved right away; it needs a saved project
const tabs = computed(() => [
  { key: 'data', label: 'Projekt' },
  { key: 'translation', label: 'Übersetzung' },
  { key: 'images', label: 'Bilder' },
  { key: 'files', label: 'Dokumente' },
  ...(props.type === 'edit' ? [{ key: 'grid', label: 'Raster' }] : []),
]);
const tab = ref(route.query.tab === 'grid' && props.type === 'edit' ? 'grid' : 'data');

const worksFields = [
  { key: 'year_works', label: 'Jahr' },
  { key: 'client_works', label: 'Auftrag' },
  { key: 'principal_works', label: 'Bauherrschaft' },
  { key: 'author_works', label: 'Autorenschaft' },
];

const selects = [
  { key: 'program', label: 'Programm' },
  { key: 'author', label: 'Autorenschaft' },
  { key: 'state', label: 'Status' },
];

const flags = [
  { key: 'has_detail', label: 'Detailseite' },
  { key: 'is_highlight', label: 'Highlight' },
  { key: 'is_filter_wood', label: 'Filter Holz' },
  { key: 'is_filter_reuse', label: 'Filter Umnutzung' },
  { key: 'is_filter_area', label: 'Filter Areal' },
];

const settings = reactive({ program: {}, author: {}, state: {} });
const setting = (key, url) => () => http.get(url).then(({ data }) => settings[key] = data);

const { record, errors, isEdit, isLoading, isFetched, title, submit } = useResourceForm({
  type: props.type,
  endpoint: 'project',
  model: () => ({
    title: translations(),
    title_short: translations(),
    location: translations(),
    description: translations(),
    info: translations(),
    year: null,
    year_works: null,
    client_works: null,
    principal_works: null,
    author_works: null,
    images: [],
    documents: [],
    is_filter_wood: 0,
    is_filter_reuse: 0,
    is_filter_area: 0,
    is_highlight: 0,
    has_detail: 0,
    publish: 0,
    program: 1,
    state: 1,
    author: 1,
  }),
  redirect: { name: 'projects' },
  titles: { create: 'Neues Projekt', edit: 'Projekt bearbeiten' },
  load: [
    setting('program', '/api/settings/program'),
    setting('state', '/api/settings/state'),
    setting('author', '/api/settings/authors'),
  ],
  // Checked here too, so all errors show at once: the API doesn't check images
  validate: project => requiredErrors(project, ['title.de', 'title_short.de', 'location.de', 'year', 'images']),
});

const errorTabs = computed(() => [
  ...(Object.keys(errors.value).some(key => key !== 'images') ? ['data'] : []),
  ...(errors.value.images ? ['images'] : []),
]);

// Landscape 16:10 (plans a touch taller), portrait 12:16; free without orientation
function cropRatio(image) {
  if (image.orientation === 'l') {
    return image.is_plan == 1 ? 16 / 10.66666 : 16 / 10;
  }
  if (image.orientation === 'p') {
    return 12 / 16;
  }
  return null;
}

const images = useImages({
  record, isLoading,
  endpoint: 'project',
  fields: () => ({ is_preview_navigation: 0, is_preview_works: 0, is_plan: 0 }),
});

const files = useFiles({ record, isLoading, endpoint: 'project' });
</script>
