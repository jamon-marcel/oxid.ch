<template>
  <div>
    <div
      class="cursor-pointer rounded-md border bg-white transition-colors hover:border-gray-300 hover:ring-2 hover:ring-gray-200 dark:bg-warm-800 dark:hover:border-warm-600 dark:hover:ring-warm-700"
      :class="isDragover ? 'border-gray-900 !bg-gray-50 dark:border-warm-400 dark:!bg-warm-700' : 'border-gray-200 dark:border-warm-700'"
      role="button"
      tabindex="0"
      @click="input.click()"
      @keydown.enter.prevent="input.click()"
      @dragover.prevent="isDragover = true"
      @dragleave.prevent="isDragover = false"
      @drop.prevent="drop"
    >
      <!-- Compact: a slim bar once there are items -->
      <div v-if="compact" class="flex items-center justify-center gap-8 py-24">
        <PhPlus :size="14" weight="light" class="text-gray-400 dark:text-warm-500" />
        <span class="text-xs text-gray-500 dark:text-warm-400">{{ label }}</span>
        <span class="ml-4 text-xs text-gray-400 dark:text-warm-500">{{ hint }}</span>
      </div>
      <div v-else class="flex flex-col items-center justify-center px-24 py-[8rem]">
        <PhUploadSimple :size="24" weight="light" class="mb-12 text-gray-400 dark:text-warm-500" />
        <p class="text-xs text-gray-500 dark:text-warm-400">
          <span class="text-gray-900 underline decoration-gray-300 underline-offset-4 dark:text-warm-100 dark:decoration-warm-600">Dateien auswählen</span>
          oder hierhin ziehen
        </p>
        <p class="mt-8 text-xs text-gray-400 dark:text-warm-500">{{ hint }}</p>
      </div>
    </div>

    <div v-if="isUploading" class="mt-8">
      <div class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-warm-700">
        <div class="h-full bg-gray-900 transition-all duration-300 dark:bg-warm-100" :style="{ width: `${progress}%` }" />
      </div>
      <p class="mt-6 text-xs text-gray-400 dark:text-warm-500">Hochladen… {{ position }} / {{ count }}</p>
    </div>

    <input ref="input" type="file" name="files" class="hidden" :accept="acceptedFiles" :multiple="maxFiles > 1" @change="choose">
  </div>
</template>
<script setup>
import { ref, computed } from 'vue';
import { PhPlus, PhUploadSimple } from '@phosphor-icons/vue';
import { notify } from '@/lib/notify';
import http from '@/lib/http';

// Drop zone + file picker. Files go up one after the other; a rejected file
// is a toast. Emits the upload endpoint's JSON response ({ name, filetype,
// orientation }) per file.
const props = defineProps({
  url: { type: String, default: '/api/media/upload' },
  label: { type: String, default: 'Bilder hinzufügen' },
  // e.g. 'JPG, PNG | max. 8 MB'
  restrictions: { type: String, default: '' },
  // e.g. '.png,.jpg'
  acceptedFiles: { type: String, required: true },
  maxFiles: { type: Number, default: 99 },
  // MB
  maxFilesize: { type: Number, required: true },
  // a slim bar instead of the drop zone (there are items already)
  compact: { type: Boolean, default: false },
});

const emit = defineEmits(['uploaded']);

const input = ref(null);
const isDragover = ref(false);
const isUploading = ref(false);
const position = ref(0);
const count = ref(0);
const fileProgress = ref(0);

// Over all files of this upload
const progress = computed(() => count.value ? Math.round(((position.value - 1) + fileProgress.value / 100) / count.value * 100) : 0);

const hint = computed(() => props.restrictions.replace(' | ', ', '));
const extensions = props.acceptedFiles.split(',').map(extension => extension.trim().toLowerCase());

function drop(event) {
  isDragover.value = false;
  upload([...event.dataTransfer.files]);
}

function choose() {
  upload([...input.value.files]);
  input.value.value = '';
}

// Checked in the browser too, so a wrong file fails before it is sent
function rejection(file) {
  if (!extensions.some(extension => file.name.toLowerCase().endsWith(extension))) {
    return `${file.name}: Dateityp nicht erlaubt (erlaubt: ${props.restrictions.split('|')[0].trim()}).`;
  }
  if (file.size > props.maxFilesize * 1024 * 1024) {
    return `${file.name}: Datei ist zu gross (max. ${props.maxFilesize} MB).`;
  }
  return null;
}

function failure(file, error) {
  const response = error.response;
  if (response?.status === 413) {
    return `${file.name}: Datei ist zu gross für den Server.`;
  }
  // The API's validation message (type, size)
  if (response?.status === 422) {
    return Object.values(response.data.errors ?? {}).flat()[0] ?? `${file.name}: Datei abgelehnt.`;
  }
  return `${file.name}: Upload fehlgeschlagen (${response?.status ?? 'Netzwerk'}).`;
}

async function upload(files) {
  if (isUploading.value || !files.length) {
    return;
  }
  if (files.length > props.maxFiles) {
    notify({ type: 'error', text: `Zu viele Dateien (max. ${props.maxFiles} auf einmal).` });
    files = files.slice(0, props.maxFiles);
  }

  const accepted = files.filter(file => {
    const message = rejection(file);
    if (message) {
      notify({ type: 'error', text: message, duration: 6000 });
    }
    return !message;
  });

  isUploading.value = true;
  count.value = accepted.length;
  position.value = 0;
  for (const file of accepted) {
    position.value++;
    fileProgress.value = 0;
    const data = new FormData();
    data.append('file', file);
    try {
      const response = await http.post(props.url, data, {
        handleErrors: false,
        onUploadProgress: event => fileProgress.value = event.total ? Math.round(event.loaded / event.total * 100) : 0,
      });
      emit('uploaded', response.data);
    }
    catch (error) {
      notify({ type: 'error', text: failure(file, error), duration: 6000 });
    }
  }
  isUploading.value = false;
}
</script>
