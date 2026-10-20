<template>
  <ResourceForm :title="title" :is-edit="isEdit" :loading="!isFetched" :busy="isLoading" @submit="submit" @cancel="router.push({ name: 'news' })">
    <Tabs v-model="tab" :tabs="tabs" :errors="Object.keys(errors).length ? ['data'] : []">
      <Tab name="data">
        <div class="flex flex-col gap-24">
          <FormField v-model="record.title.de" name="title.de" label="Titel" :errors="errors" required />
          <FormField v-model="record.subtitle.de" name="subtitle.de" label="Untertitel" :errors="errors" />
          <FormField v-model="record.text.de" name="text.de" label="Text" type="editor" :errors="errors" />
        </div>
      </Tab>
      <Tab name="translation">
        <div class="flex flex-col gap-24">
          <FormField v-model="record.title.en" name="title.en" label="Titel" :errors="errors" />
          <FormField v-model="record.subtitle.en" name="subtitle.en" label="Untertitel" :errors="errors" />
          <FormField v-model="record.text.en" name="text.en" label="Text" type="editor" :errors="errors" />
        </div>
      </Tab>
    </Tabs>

    <template #sidebar>
      <FormField v-model="record.publish" name="publish" label="Veröffentlichen" type="checkbox" />
      <FormField v-model="dateEnd" name="date_end" label="Publizieren bis" placeholder="TT.MM.JJJJ" :errors="errors" />
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
import { useResourceForm } from '@/composables/useResourceForm';
import { translations, formatDate, dateMask } from '@/lib/utils';

const props = defineProps({
  type: { type: String, required: true },
});

const router = useRouter();

const tabs = [
  { key: 'data', label: 'Artikel' },
  { key: 'translation', label: 'Übersetzung' },
];
const tab = ref('data');

const { record, errors, isEdit, isLoading, isFetched, title, submit } = useResourceForm({
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
  titles: { create: 'Neue News', edit: 'News bearbeiten' },
  loaded: news => news.date_end = formatDate(news.date_end),
});

// dd.mm.yyyy, the dots added while typing
const dateEnd = computed({
  get: () => record.value.date_end,
  set: value => record.value.date_end = dateMask(value),
});
</script>
