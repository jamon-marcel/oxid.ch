<template>
  <div>
    <div class="card-grid is-files">
      <Card
        v-for="file in files"
        :key="file.id ?? file.name"
        src="/assets/backend/img/icons/file.svg"
        :href="fileUrl(file)"
        :label="displayName(file.name)"
        :disabled="file.publish == 0"
        file
      >
        <a href="javascript:;" class="feather-icon" :title="file.publish == 1 ? 'Verbergen' : 'Publizieren'" @click.prevent="emit('toggle', file)">
          <PhEye v-if="file.publish == 1" :size="18" weight="light" />
          <PhEyeSlash v-else :size="18" weight="light" class="is-off" />
        </a>
        <a href="javascript:;" class="feather-icon" title="Bearbeiten" @click.prevent="openEdit(file)">
          <PhPencilSimple :size="18" weight="light" />
        </a>
        <a href="javascript:;" class="feather-icon" title="Löschen" @click.prevent="emit('destroy', file)">
          <PhTrash :size="18" weight="light" />
        </a>
      </Card>
    </div>

    <Lightbox :open="!!editItem" title="Datei bearbeiten" @close="editItem = null">
      <div class="lightbox-grid" v-if="editItem">
        <figure>
          <img src="/assets/backend/img/icons/file.svg" height="100" width="100">
          <figcaption v-if="editItem.caption.de || editItem.caption.en">
            <span v-if="editItem.caption.de">{{ editItem.caption.de }}</span>
            <span v-if="editItem.caption.en">{{ editItem.caption.en }}</span>
          </figcaption>
        </figure>
        <div>
          <div class="form-row">
            <label>Datei:</label>
            <span>{{ editItem.name }}</span>
          </div>
          <div class="form-row">
            <label>Legende</label>
            <input type="text" v-model="editItem.caption.de">
          </div>
          <div class="form-row">
            <label>Legende (en)</label>
            <input type="text" v-model="editItem.caption.en">
          </div>
          <div class="form-row" v-if="languages">
            <label>Sprache</label>
            <div class="form-radio">
              <input type="radio" class="visually-hidden" v-model.number="editItem.language" :value="0" id="file_language_0">
              <label for="file_language_0" class="form-control">DE</label>
              <input type="radio" class="visually-hidden" v-model.number="editItem.language" :value="1" id="file_language_1">
              <label for="file_language_1" class="form-control">EN</label>
            </div>
          </div>
        </div>
      </div>
      <template #footer>
        <a href="javascript:;" class="btn-secondary" @click.prevent="editItem = null">Schliessen</a>
      </template>
    </Lightbox>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { PhEye, PhEyeSlash, PhPencilSimple, PhTrash } from '@phosphor-icons/vue';
import Lightbox from '@/components/ui/Lightbox.vue';
import Card from '@/components/ui/Card.vue';
import { displayName } from '@/lib/utils';

const props = defineProps({
  // documents have a language (team)
  languages: { type: Boolean, default: false },
});

const files = defineModel('files', { type: Array, required: true });

const emit = defineEmits(['toggle', 'destroy']);

// Captions are saved with the record
const editItem = ref(null);
function openEdit(file) {
  file.caption ??= { de: null, en: null };
  // The API returns '0'/'1' as strings
  if (props.languages) {
    file.language = Number(file.language ?? 0);
  }
  editItem.value = file;
}

function fileUrl(file) {
  return `/storage/uploads/${file.name}`;
}
</script>
