<template>
  <div class="container">
    <LoadingIndicator v-if="isLoading" />
    <main class="content" role="main" v-if="isFetched">
      <div>
        <h1>{{ title }}</h1>
        <Tabs :tabs="tabs" v-model="tab" />
        <form @submit.prevent="submit">
          <div v-show="tab === 'data'">
            <div class="grid-main-sidebar">
              <div class="column-main">
                <div class="form-row">
                  <label>Adresse</label>
                  <Editor v-model="record.address.de" class="is-tall" />
                </div>
                <div class="form-row">
                  <label>Google Maps URL</label>
                  <input type="text" v-model="record.google_maps_url">
                </div>
                <div class="form-row" v-for="field in textFields" :key="field.key">
                  <label>{{ field.label }}</label>
                  <Editor v-model="record[field.key].de" class="is-tall" />
                </div>
              </div>
            </div>
          </div>
          <div v-show="tab === 'translation'">
            <div class="grid-main-sidebar">
              <div class="column-main">
                <div class="form-row">
                  <label>Adresse</label>
                  <Editor v-model="record.address.en" class="is-tall" />
                </div>
                <div class="form-row" v-for="field in textFields" :key="field.key">
                  <label>{{ field.label }}</label>
                  <Editor v-model="record[field.key].en" class="is-tall" />
                </div>
              </div>
            </div>
          </div>
          <FormFooter :back="{ name: 'contact' }" />
        </form>
      </div>
    </main>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import LoadingIndicator from '@/components/ui/LoadingIndicator.vue';
import Tabs from '@/components/ui/Tabs.vue';
import FormFooter from '@/components/ui/FormFooter.vue';
import Editor from '@/components/ui/editor/Editor.vue';
import { useResourceForm } from '@/composables/useResourceForm';
import { translations } from '@/lib/utils';

const props = defineProps({
  type: { type: String, required: true },
});

const tabs = [
  { key: 'data', label: 'Daten' },
  { key: 'translation', label: 'Übersetzung' },
];
const tab = ref('data');

const textFields = [
  { key: 'contacts', label: 'Kontakte' },
  { key: 'info', label: 'Info' },
  { key: 'imprint', label: 'Impressum' },
];

const { record, isLoading, isFetched, title, submit } = useResourceForm({
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
  titles: { create: 'Kontakt hinzufügen', edit: 'Kontakt bearbeiten' },
});
</script>
