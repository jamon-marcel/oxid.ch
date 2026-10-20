<template>
  <div>
    <PageHeader :title="title" />
    <ImageField
      v-model:images="library.images"
      :label="sortable ? 'Bilder (Reihenfolge mit der Maus ändern)' : 'Bilder'"
      :endpoint="endpoint"
      :sortable="sortable"
      :ratio="() => ratio"
      save-on-edit
      wide
      @uploaded="library.store"
      @toggle="library.toggle"
      @destroy="library.destroy"
      @update="library.update"
      @save-coords="library.saveCoords"
    />
  </div>
</template>
<script setup>
import { reactive } from 'vue';
import PageHeader from '@/components/layout/PageHeader.vue';
import ImageField from '@/components/media/ImageField.vue';
import { useImageLibrary } from '@/composables/useImageLibrary';

// Home, team, jobs and profile images: one page, configured by the route
const props = defineProps({
  title: { type: String, required: true },
  endpoint: { type: String, required: true },
  sortable: { type: Boolean, default: true },
  ratio: { type: Number, default: 16 / 10 },
});

const library = reactive(useImageLibrary({ endpoint: props.endpoint }));
</script>
