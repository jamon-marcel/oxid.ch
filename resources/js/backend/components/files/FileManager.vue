<template>
  <div>
    <div class="upload-listing is-files">
      <div>
        <figure
          v-for="file in files"
          :key="file.id ?? file.name"
          :class="[file.publish == 0 ? 'is-disabled' : '', 'upload-item is-file']"
        >
          <a :href="fileUrl(file)" target="_blank">
            <img src="/assets/backend/img/icons/file.svg" height="100" width="100">
          </a>
          <div class="upload__actions">
            <div class="list-item-actions">
              <a href="javascript:;" class="feather-icon" :title="file.publish == 1 ? 'Verbergen' : 'Publizieren'" @click.prevent="emit('toggle', file)">
                <PhEye v-if="file.publish == 1" :size="18" weight="light" />
                <PhEyeSlash v-else :size="18" weight="light" class="is-off" />
              </a>
              <a href="javascript:;" class="feather-icon" title="Bearbeiten" @click.prevent="openEdit(file)">
                <PhPencilSimple :size="18" weight="light" />
              </a>
              <a :href="fileUrl(file)" target="_blank" class="feather-icon" title="Öffnen">
                <PhArrowSquareOut :size="18" weight="light" />
              </a>
              <a href="javascript:;" class="feather-icon" title="Löschen" @click.prevent="emit('destroy', file)">
                <PhTrash :size="18" weight="light" />
              </a>
            </div>
          </div>
        </figure>
      </div>
    </div>
    <div :class="[editItem ? 'is-visible' : '', 'upload-overlay-edit']">
      <div v-if="editItem">
        <a href="javascript:;" class="feather-icon upload-overlay__close" title="Schliessen" @click.prevent="editItem = null">
          <PhX :size="24" weight="light" />
        </a>
        <div>
          <figure>
            <img src="/assets/backend/img/icons/file.svg" height="100" width="100">
            <figcaption v-if="editItem.caption.de || editItem.caption.en">
              <span v-if="editItem.caption.de">{{ editItem.caption.de }}</span>
              <span v-if="editItem.caption.en">{{ editItem.caption.en }}</span>
            </figcaption>
          </figure>
        </div>
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
              <input type="radio" class="visually-hidden" v-model="editItem.language" :value="0" id="file_language_0">
              <label for="file_language_0" class="form-control">DE</label>
              <input type="radio" class="visually-hidden" v-model="editItem.language" :value="1" id="file_language_1">
              <label for="file_language_1" class="form-control">EN</label>
            </div>
          </div>
          <div class="form-row-button">
            <a href="javascript:;" class="btn-secondary" @click.prevent="editItem = null">Schliessen</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { PhEye, PhEyeSlash, PhPencilSimple, PhArrowSquareOut, PhTrash, PhX } from '@phosphor-icons/vue';
import { useEscape } from '@/composables/useEscape';

defineProps({
  // documents have a language (team)
  languages: { type: Boolean, default: false },
});

const files = defineModel('files', { type: Array, required: true });

const emit = defineEmits(['toggle', 'destroy']);

// Captions are saved with the record
const editItem = ref(null);
function openEdit(file) {
  file.caption ??= { de: null, en: null };
  if (typeof file.language === 'string') {
    file.language = Number(file.language);
  }
  editItem.value = file;
}

function fileUrl(file) {
  return `/storage/uploads/${file.name}`;
}

useEscape(() => editItem.value = null);
</script>
