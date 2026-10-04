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
                <div class="form-row">
                  <label>Text</label>
                  <Editor v-model="record.description.de" class="is-tall" />
                </div>
              </div>
              <div class="column-sidebar">
                <div>
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
                  <label>Titel</label>
                  <input type="text" v-model="record.title.en">
                </div>
                <div class="form-row">
                  <label>Text</label>
                  <Editor v-model="record.description.en" class="is-tall" />
                </div>
              </div>
            </div>
          </div>
          <FormFooter :back="{ name: 'profile' }" />
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

const { record, errors, isLoading, isFetched, title, submit, clearError } = useResourceForm({
  type: props.type,
  endpoint: 'profile',
  model: () => ({
    title: translations(),
    description: translations(),
    publish: 0,
  }),
  redirect: { name: 'profile' },
  titles: { create: 'Profil hinzufügen', edit: 'Profil bearbeiten' },
});
</script>
