<template>
  <div class="container">
    <LoadingIndicator v-if="isLoading" />
    <main class="content" role="main" v-if="isFetched">
      <div>
        <h1>{{ title }}</h1>
        <Tabs :tabs="tabs" v-model="tab" :errors="errorTabs" />
        <form @submit.prevent="submit">
          <div v-show="tab === 'data'">
            <div class="grid-main-sidebar">
              <div class="column-main">
                <div v-for="field in requiredFields" :key="field.key" :class="[errors[field.key + '.de'] ? 'has-error' : '', 'form-row']">
                  <label>{{ field.label }} *</label>
                  <input type="text" v-model="record[field.key].de" @focus="clearError(field.key + '.de')">
                  <LabelRequired />
                </div>
                <div :class="[errors['description_short.de'] ? 'has-error' : '', 'form-row']" @focusin="clearError('description_short.de')">
                  <label>Kurzbeschreibung *</label>
                  <Editor v-model="record.description_short.de" :has-error="!!errors['description_short.de']" />
                  <LabelRequired />
                </div>
                <div class="form-row">
                  <label>Beschreibung</label>
                  <Editor v-model="record.description.de" />
                </div>
                <div class="form-row is-last">
                  <label>Info</label>
                  <Editor v-model="record.info.de" />
                </div>
              </div>
              <div class="column-sidebar">
                <div>
                  <div class="form-row is-sm">
                    <label>Kategorie</label>
                    <div class="select-wrapper is-sidebar">
                      <select v-model="record.category" name="category">
                        <option v-for="(label, id) in categories" :key="id" :value="Number(id)">{{ label }}</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-row is-sm is-last">
                    <Toggle label="Publizieren?" name="publish" v-model="record.publish" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-show="tab === 'translation'">
            <div class="grid-main-sidebar">
              <div class="column-main">
                <div class="form-row" v-for="field in requiredFields" :key="field.key">
                  <label>{{ field.label }}</label>
                  <input type="text" v-model="record[field.key].en">
                </div>
                <div class="form-row">
                  <label>Kurzbeschreibung</label>
                  <Editor v-model="record.description_short.en" />
                </div>
                <div class="form-row">
                  <label>Beschreibung</label>
                  <Editor v-model="record.description.en" />
                </div>
                <div class="form-row is-last">
                  <label>Info</label>
                  <Editor v-model="record.info.en" />
                </div>
              </div>
            </div>
          </div>
          <div v-show="tab === 'images'">
            <div class="form-row">
              <Uploader v-bind="imageUpload" @uploaded="images.store" />
            </div>
            <div class="form-row" v-if="record.images.length">
              <ImageManager
                v-model:images="record.images"
                endpoint="discourse"
                sortable
                @toggle="images.toggle"
                @destroy="images.destroy"
                @save-coords="images.saveCoords"
              >
                <template #fields="{ image }">
                  <div class="form-row">
                    <Toggle label="Vorschaubild?" name="is_preview" v-model="image.is_preview" />
                  </div>
                  <div class="form-row">
                    <Toggle label="Theme?" name="theme" label-true="light" label-false="dark" v-model="image.theme" />
                  </div>
                </template>
              </ImageManager>
            </div>
          </div>
          <div v-show="tab === 'files'">
            <div class="form-row">
              <Uploader v-bind="fileUpload" @uploaded="files.store" />
            </div>
            <div class="form-row">
              <FileManager v-model:files="record.documents" @toggle="files.toggle" @destroy="files.destroy" />
            </div>
          </div>
          <FormFooter :back="{ name: 'discourses' }" />
        </form>
      </div>
    </main>
  </div>
</template>
<script setup>
import { ref, computed } from 'vue';
import LoadingIndicator from '@/components/ui/LoadingIndicator.vue';
import Tabs from '@/components/ui/Tabs.vue';
import LabelRequired from '@/components/ui/LabelRequired.vue';
import Toggle from '@/components/ui/Toggle.vue';
import FormFooter from '@/components/ui/FormFooter.vue';
import Uploader from '@/components/ui/Uploader.vue';
import ImageManager from '@/components/images/ImageManager.vue';
import FileManager from '@/components/files/FileManager.vue';
import Editor from '@/components/ui/editor/Editor.vue';
import { useResourceForm } from '@/composables/useResourceForm';
import { useImages, imageUpload } from '@/composables/useImages';
import { useFiles, fileUpload } from '@/composables/useFiles';
import { translations, requiredErrors } from '@/lib/utils';
import http from '@/lib/http';

const props = defineProps({
  type: { type: String, required: true },
});

const tabs = [
  { key: 'data', label: 'Diskurs' },
  { key: 'translation', label: 'Übersetzung' },
  { key: 'images', label: 'Bilder' },
  { key: 'files', label: 'Dokumente' },
];
const tab = ref('data');

const requiredFields = [
  { key: 'heading', label: 'Rubrik' },
  { key: 'date', label: 'Datum/Zeitraum' },
  { key: 'title', label: 'Titel' },
];

const categories = ref({});

const { record, errors, isLoading, isFetched, title, submit, clearError } = useResourceForm({
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
  titles: { create: 'Diskurs hinzufügen', edit: 'Diskurs bearbeiten' },
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
