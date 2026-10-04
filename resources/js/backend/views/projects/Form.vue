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
                <div :class="[errors.year ? 'has-error' : '', 'form-row']">
                  <label>Jahr *</label>
                  <input type="text" v-model="record.year" @focus="clearError('year')">
                  <LabelRequired />
                </div>
                <div class="form-row">
                  <label>Beschreibung</label>
                  <Editor v-model="record.description.de" />
                </div>
                <div class="form-row">
                  <label>Info</label>
                  <Editor v-model="record.info.de" />
                </div>
                <h3 class="is-label">Infos Werkliste</h3>
                <div class="form-row" v-for="field in worksFields" :key="field.key" :class="{ 'is-last': field.key === 'author_works' }">
                  <label>{{ field.label }}</label>
                  <input type="text" v-model="record[field.key]">
                </div>
              </div>
              <div class="column-sidebar">
                <div>
                  <div class="form-row is-sm" v-for="select in selects" :key="select.key">
                    <label>{{ select.label }}</label>
                    <div class="select-wrapper is-sidebar">
                      <select v-model="record[select.key]" :name="select.key">
                        <option v-for="(label, id) in settings[select.key]" :key="id" :value="Number(id)">{{ label }}</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-row is-sm" v-for="flag in flags" :key="flag.key">
                    <RadioButton :label="flag.label" :name="flag.key" v-model="record[flag.key]" />
                  </div>
                  <div class="form-row is-sm is-last">
                    <RadioButton label="Publizieren?" name="publish" v-model="record.publish" />
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
                endpoint="project"
                :ratio="cropRatio"
                :is-protected="image => image.is_grid == 1"
                @toggle="images.toggle"
                @destroy="images.destroy"
                @save-coords="images.saveCoords"
              >
                <template #fields="{ image }">
                  <div class="form-row">
                    <label>Vorschaubild für:</label>
                    <input type="checkbox" class="visually-hidden" v-model="image.is_preview_navigation" :true-value="1" :false-value="0" id="is_preview_navigation">
                    <label for="is_preview_navigation" class="form-control is-auto">Navigation</label>
                    <input type="checkbox" class="visually-hidden" v-model="image.is_preview_works" :true-value="1" :false-value="0" id="is_preview_works">
                    <label for="is_preview_works" class="form-control is-auto">Werkliste</label>
                  </div>
                  <div class="form-row">
                    <RadioButton label="Plan?" name="is_plan" v-model="image.is_plan" />
                  </div>
                </template>
              </ImageManager>
            </div>
          </div>
          <div v-show="tab === 'files'">
            <div class="form-row">
              <Uploader v-bind="fileUpload" @uploaded="files.store" />
            </div>
            <div class="form-row" v-if="record.documents.length">
              <FileManager v-model:files="record.documents" @toggle="files.toggle" @destroy="files.destroy" />
            </div>
          </div>
          <FormFooter :back="{ name: 'projects' }" />
        </form>
      </div>
    </main>
  </div>
</template>
<script setup>
import { ref, reactive, computed } from 'vue';
import LoadingIndicator from '@/components/ui/LoadingIndicator.vue';
import Tabs from '@/components/ui/Tabs.vue';
import LabelRequired from '@/components/ui/LabelRequired.vue';
import RadioButton from '@/components/ui/RadioButton.vue';
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
  { key: 'data', label: 'Projekt' },
  { key: 'translation', label: 'Übersetzung' },
  { key: 'images', label: 'Bilder' },
  { key: 'files', label: 'Dokumente' },
];
const tab = ref('data');

const requiredFields = [
  { key: 'title', label: 'Titel' },
  { key: 'title_short', label: 'Kurztitel' },
  { key: 'location', label: 'Ort' },
];

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
  { key: 'has_detail', label: 'Detailseite?' },
  { key: 'is_highlight', label: 'Highlight?' },
  { key: 'is_filter_wood', label: 'Filter - Holz?' },
  { key: 'is_filter_reuse', label: 'Filter - Umnutzung?' },
  { key: 'is_filter_area', label: 'Filter - Areal?' },
];

const settings = reactive({ program: {}, author: {}, state: {} });
const setting = (key, url) => () => http.get(url).then(({ data }) => settings[key] = data);

const { record, errors, isLoading, isFetched, title, submit, clearError } = useResourceForm({
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
  titles: { create: 'Projekt hinzufügen', edit: 'Projekt bearbeiten' },
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
