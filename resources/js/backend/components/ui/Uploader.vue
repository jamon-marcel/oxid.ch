<template>
  <div>
    <label v-if="label">{{ label }}</label>
    <div ref="el" class="vue-dropzone dropzone"></div>
    <span class="bubble is-restriction">{{ restrictions }}</span>
  </div>
</template>
<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import Dropzone from 'dropzone';
import { notify } from '@kyvg/vue3-notification';

Dropzone.autoDiscover = false;

const props = defineProps({
  url: { type: String, default: '/api/media/upload' },
  label: { type: String, default: 'Upload' },
  restrictions: { type: String, default: '' },
  acceptedFiles: { type: String, required: true },
  maxFiles: { type: Number, default: 99 },
  maxFilesize: { type: Number, required: true },
});

// The upload endpoint's JSON response: { name, filetype, orientation }
const emit = defineEmits(['uploaded']);

const el = ref(null);
let dropzone = null;

onMounted(() => {
  const token = document.head.querySelector('meta[name="csrf-token"]');
  dropzone = new Dropzone(el.value, {
    url: props.url,
    method: 'post',
    acceptedFiles: props.acceptedFiles,
    maxFiles: props.maxFiles,
    maxFilesize: props.maxFilesize,
    createImageThumbnails: false,
    headers: token ? { 'X-CSRF-TOKEN': token.content } : {},
    dictDefaultMessage: 'Dateien hierher ziehen oder klicken',
    dictInvalidFileType: `Dateityp nicht erlaubt (erlaubt: ${props.restrictions.split('|')[0].trim()}).`,
    dictFileTooBig: 'Datei ist zu gross ({{filesize}} MB, erlaubt: max. {{maxFilesize}} MB).',
    dictMaxFilesExceeded: 'Zu viele Dateien (max. {{maxFiles}} auf einmal).',
    hiddenInputContainer: el.value.parentElement,
  });

  // Rejected in the browser (type, size, count) or by the server
  dropzone.on('error', (file, message, xhr) => {
    notify({ type: 'error', text: `«${file.name}»: ${xhr ? `Upload fehlgeschlagen (${xhr.status})` : message}` });
  });

  dropzone.on('complete', file => {
    if (file.status === 'success') {
      emit('uploaded', JSON.parse(file.xhr.response));
    }
    dropzone.removeFile(file);
  });
});

onBeforeUnmount(() => dropzone?.destroy());
</script>
