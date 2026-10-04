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
                  <label>Subtitel</label>
                  <input type="text" v-model="record.subtitle.de">
                </div>
                <div class="form-row is-last">
                  <label>Text</label>
                  <Editor v-model="record.text.de" />
                </div>
              </div>
              <div class="column-sidebar">
                <div>
                  <div class="form-row is-sm">
                    <RadioButton label="Publizieren?" name="publish" v-model="record.publish" />
                  </div>
                  <div class="form-row is-sm is-last">
                    <label class="is-sm">Publizieren bis</label>
                    <input
                      class="is-light"
                      :value="record.date_end"
                      @input="record.date_end = $event.target.value = dateMask($event.target.value)"
                      type="text"
                      inputmode="numeric"
                      placeholder="z.B. 01.06.2020"
                    >
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
                  <label>Subtitel</label>
                  <input type="text" v-model="record.subtitle.en">
                </div>
                <div class="form-row is-last">
                  <label>Text</label>
                  <Editor v-model="record.text.en" />
                </div>
              </div>
            </div>
          </div>
          <FormFooter :back="{ name: 'news' }" />
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
import Editor from '@/components/ui/editor/Editor.vue';
import { useResourceForm } from '@/composables/useResourceForm';
import { translations, formatDate, dateMask } from '@/lib/utils';

const props = defineProps({
  type: { type: String, required: true },
});

const tabs = [
  { key: 'data', label: 'Artikel' },
  { key: 'translation', label: 'Übersetzung' },
];
const tab = ref('data');

const { record, errors, isLoading, isFetched, title, submit, clearError } = useResourceForm({
  type: props.type,
  endpoint: 'news',
  model: () => ({
    title: translations(),
    subtitle: translations(),
    text: translations(),
    publish: 0,
    sticky: 0,
    date_end: null,
  }),
  redirect: { name: 'news' },
  titles: { create: 'News hinzufügen', edit: 'News bearbeiten' },
  loaded: news => news.date_end = formatDate(news.date_end),
});
</script>
