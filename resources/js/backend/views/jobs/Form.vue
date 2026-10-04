<template>
  <div class="container">
    <LoadingIndicator v-if="isLoading" />
    <main class="content" role="main" v-if="isFetched">
      <div>
        <h1>{{ title }}</h1>
        <Tabs :tabs="tabs" v-model="tab" :errors="Object.keys(errors).length ? ['data'] : []" />
        <form @submit.prevent="submit">
          <div v-show="tab === 'data'">
            <div class="grid-main-sidebar">
              <div class="column-main">
                <div :class="[errors['title.de'] ? 'has-error' : '', 'form-row']">
                  <label>Titel *</label>
                  <input type="text" v-model="record.title.de" @focus="clearError('title.de')">
                  <LabelRequired />
                </div>
                <div :class="[errors['description.de'] ? 'has-error' : '', 'form-row']" @focusin="clearError('description.de')">
                  <label>Beschreibung *</label>
                  <Editor v-model="record.description.de" :has-error="!!errors['description.de']" />
                  <LabelRequired />
                </div>
                <div :class="[errors['info.de'] ? 'has-error' : '', 'form-row is-last']" @focusin="clearError('info.de')">
                  <label>Info *</label>
                  <Editor v-model="record.info.de" :has-error="!!errors['info.de']" />
                  <LabelRequired />
                </div>
              </div>
              <div class="column-sidebar">
                <div>
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
                <div class="form-row">
                  <label>Titel</label>
                  <input type="text" v-model="record.title.en">
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
          <div v-show="tab === 'files'">
            <div class="form-row">
              <Uploader v-bind="fileUpload" @uploaded="files.store" />
            </div>
            <div class="form-row">
              <FileManager v-model:files="record.documents" @toggle="files.toggle" @destroy="files.destroy" />
            </div>
          </div>
          <FormFooter :back="{ name: 'jobs' }" />
        </form>
      </div>
    </main>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import LoadingIndicator from '@/components/ui/LoadingIndicator.vue';
import Tabs from '@/components/ui/Tabs.vue';
import LabelRequired from '@/components/ui/LabelRequired.vue';
import RadioButton from '@/components/ui/RadioButton.vue';
import FormFooter from '@/components/ui/FormFooter.vue';
import Uploader from '@/components/ui/Uploader.vue';
import FileManager from '@/components/files/FileManager.vue';
import Editor from '@/components/ui/editor/Editor.vue';
import { useResourceForm } from '@/composables/useResourceForm';
import { useFiles, fileUpload } from '@/composables/useFiles';
import { translations, requiredErrors } from '@/lib/utils';

const props = defineProps({
  type: { type: String, required: true },
});

const tabs = [
  { key: 'data', label: 'Job' },
  { key: 'translation', label: 'Übersetzung' },
  { key: 'files', label: 'Dokumente' },
];
const tab = ref('data');

const { record, errors, isLoading, isFetched, title, submit, clearError } = useResourceForm({
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
  titles: { create: 'Job hinzufügen', edit: 'Job bearbeiten' },
  // Checked here too, so all errors show at once: the API doesn't check Info
  validate: job => requiredErrors(job, ['title.de', 'description.de', 'info.de']),
});

const files = useFiles({ record, isLoading, endpoint: 'job' });
</script>
