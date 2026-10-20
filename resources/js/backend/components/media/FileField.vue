<template>
  <div>
    <FormLabel>{{ label }}</FormLabel>
    <div class="mt-8 flex flex-col gap-16">
      <MediaUploader v-bind="fileUpload" label="Dateien hinzufügen" :compact="files.length > 0" @uploaded="emit('uploaded', $event)" />
      <div v-if="files.length" class="grid grid-cols-2 gap-20 sm:grid-cols-3">
        <MediaCard
          v-for="file in files"
          :key="file.id ?? file.name"
          :href="`/storage/uploads/${file.name}`"
          :label="displayName(file.name)"
          :meta="file.caption?.de ?? ''"
          :hidden="Number(file.publish) === 0"
          file
          @toggle="emit('toggle', file)"
          @edit="openEdit(file)"
          @delete="emit('destroy', file)"
        >
          <template v-if="languages" #badges>
            <Badge>{{ Number(file.language ?? 0) === 1 ? 'EN' : 'DE' }}</Badge>
          </template>
        </MediaCard>
      </div>
    </div>

    <MediaEdit :item="editing" file @close="editing = null">
      <template v-if="languages" #default="{ item }">
        <FormGroup>
          <FormLabel for="file_language">Sprache</FormLabel>
          <FormSelect id="file_language" v-model="item.language" :options="[{ value: 0, label: 'Deutsch' }, { value: 1, label: 'Englisch' }]" />
        </FormGroup>
      </template>
    </MediaEdit>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import Badge from '@/components/ui/badge/Badge.vue';
import FormGroup from '@/components/ui/form/FormGroup.vue';
import FormLabel from '@/components/ui/form/FormLabel.vue';
import FormSelect from '@/components/ui/form/FormSelect.vue';
import MediaUploader from '@/components/media/MediaUploader.vue';
import MediaCard from '@/components/media/MediaCard.vue';
import MediaEdit from '@/components/media/MediaEdit.vue';
import { fileUpload } from '@/composables/useFiles';
import { displayName } from '@/lib/utils';

// Upload + manage the documents (PDF) of a record; captions go with the record
const props = defineProps({
  label: { type: String, default: 'Dokumente' },
  // documents have a language (team)
  languages: { type: Boolean, default: false },
});

const files = defineModel('files', { type: Array, required: true });

const emit = defineEmits(['uploaded', 'toggle', 'destroy']);

const editing = ref(null);

function openEdit(file) {
  file.caption ??= { de: null, en: null };
  // The API returns '0'/'1' as strings
  if (props.languages) {
    file.language = Number(file.language ?? 0);
  }
  editing.value = file;
}
</script>
