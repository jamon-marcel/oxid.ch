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
                <div :class="[errors.firstname ? 'has-error' : '', 'form-row']">
                  <label>Vorname *</label>
                  <input type="text" v-model="record.firstname" @focus="clearError('firstname')">
                  <LabelRequired />
                </div>
                <div :class="[errors.name ? 'has-error' : '', 'form-row']">
                  <label>Name *</label>
                  <input type="text" v-model="record.name" @focus="clearError('name')">
                  <LabelRequired />
                </div>
                <div class="form-row">
                  <label>Funktion</label>
                  <input type="text" v-model="record.role.de">
                </div>
                <div class="form-row">
                  <label>Position</label>
                  <input type="text" v-model="record.position.de">
                </div>
                <div class="form-row">
                  <label>E-Mail</label>
                  <input type="text" v-model="record.email">
                </div>
                <div class="form-row">
                  <label>Telefon</label>
                  <input type="text" v-model="record.phone">
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
                <div class="form-row">
                  <label>Funktion</label>
                  <input type="text" v-model="record.role.en">
                </div>
                <div class="form-row">
                  <label>Position</label>
                  <input type="text" v-model="record.position.en">
                </div>
              </div>
            </div>
          </div>
          <div v-show="tab === 'files'">
            <div class="form-row">
              <Uploader v-bind="fileUpload" @uploaded="files.store" />
            </div>
            <div class="form-row">
              <FileManager v-model:files="record.documents" languages @toggle="files.toggle" @destroy="files.destroy" />
            </div>
          </div>
          <FormFooter :back="{ name: 'team' }" />
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
import Toggle from '@/components/ui/Toggle.vue';
import FormFooter from '@/components/ui/FormFooter.vue';
import Uploader from '@/components/ui/Uploader.vue';
import FileManager from '@/components/files/FileManager.vue';
import { useResourceForm } from '@/composables/useResourceForm';
import { useFiles, fileUpload } from '@/composables/useFiles';
import { translations } from '@/lib/utils';
import http from '@/lib/http';

const props = defineProps({
  type: { type: String, required: true },
});

const tabs = [
  { key: 'data', label: 'Daten' },
  { key: 'translation', label: 'Übersetzung' },
  { key: 'files', label: 'Dokumente' },
];
const tab = ref('data');

const categories = ref({});

// 210 older members have role/position null; the model defaults fill them in
const { record, errors, isLoading, isFetched, title, submit, clearError } = useResourceForm({
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
  titles: { create: 'Teammitglied hinzufügen', edit: 'Teammitglied bearbeiten' },
  load: [() => http.get('/api/settings/teamCategories').then(({ data }) => categories.value = data)],
});

const files = useFiles({ record, isLoading, endpoint: 'team', fields: () => ({ language: 0 }) });
</script>
