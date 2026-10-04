<template>
  <div class="container">
    <LoadingIndicator v-if="isLoading" />
    <main class="content" role="main">
      <div>
        <h1>{{ title }}</h1>
        <div class="form-row">
          <Uploader v-bind="imageUpload" @uploaded="library.store" />
        </div>
        <div class="form-row" v-if="library.images.length">
          <ImageManager
            v-model:images="library.images"
            :endpoint="endpoint"
            :sortable="sortable"
            :ratio="() => ratio"
            save-on-close
            @toggle="library.toggle"
            @destroy="library.destroy"
            @update="library.update"
            @save-coords="library.saveCoords"
          />
        </div>
      </div>
    </main>
  </div>
</template>
<script setup>
import { ref, reactive } from 'vue';
import LoadingIndicator from '@/components/ui/LoadingIndicator.vue';
import Uploader from '@/components/ui/Uploader.vue';
import ImageManager from '@/components/images/ImageManager.vue';
import { useImageLibrary } from '@/composables/useImageLibrary';
import { imageUpload } from '@/composables/useImages';

// Home, team, jobs and profile images: one page, configured by the route
const props = defineProps({
  title: { type: String, required: true },
  endpoint: { type: String, required: true },
  sortable: { type: Boolean, default: true },
  ratio: { type: Number, default: 16 / 10 },
});

const isLoading = ref(false);
const library = reactive(useImageLibrary({ endpoint: props.endpoint, isLoading }));
</script>
