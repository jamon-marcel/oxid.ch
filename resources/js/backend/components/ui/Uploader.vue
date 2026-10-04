<template>
  <div>
    <label v-if="label">{{ label }}</label>
    <div
      :class="['uploader', { 'is-dragover': isDragover, 'is-uploading': uploading }]"
      @click="input.click()"
      @dragover.prevent="isDragover = true"
      @dragleave.prevent="isDragover = false"
      @drop.prevent="drop"
    >
      <span v-if="uploading">{{ uploading }}</span>
      <span v-else>Dateien hierher ziehen oder klicken</span>
      <input ref="input" type="file" :accept="acceptedFiles" :multiple="maxFiles > 1" hidden @change="choose">
    </div>
    <span class="bubble is-restriction">{{ restrictions }}</span>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import http from '@/lib/http';
import { notify } from '@/lib/notify';

// Drop zone + file picker; uploads one file after the other and emits the
// upload endpoint's JSON response ({ name, filetype, orientation }) for each
const props = defineProps({
  url: { type: String, default: '/api/media/upload' },
  label: { type: String, default: 'Upload' },
  restrictions: { type: String, default: '' },
  // e.g. '.png,.jpg'
  acceptedFiles: { type: String, required: true },
  maxFiles: { type: Number, default: 99 },
  // MB
  maxFilesize: { type: Number, required: true },
});

const emit = defineEmits(['uploaded']);

const input = ref(null);
const isDragover = ref(false);
const uploading = ref(null);

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
    return `Dateityp nicht erlaubt (erlaubt: ${props.restrictions.split('|')[0].trim()}).`;
  }
  if (file.size > props.maxFilesize * 1024 * 1024) {
    return `Datei ist zu gross (max. ${props.maxFilesize} MB).`;
  }
  return null;
}

function failure(error) {
  const response = error.response;
  if (response?.status === 413) {
    return 'Datei ist zu gross für den Server.';
  }
  // The API's validation message (type, size)
  if (response?.status === 422) {
    return Object.values(response.data.errors ?? {}).flat()[0] ?? 'Datei abgelehnt.';
  }
  return `Upload fehlgeschlagen (${response?.status ?? 'Netzwerk'}).`;
}

async function upload(files) {
  if (files.length > props.maxFiles) {
    notify({ type: 'error', text: `Zu viele Dateien (max. ${props.maxFiles} auf einmal).` });
    return;
  }
  for (const [index, file] of files.entries()) {
    const reason = rejection(file);
    if (reason) {
      notify({ type: 'error', text: `«${file.name}»: ${reason}` });
      continue;
    }
    uploading.value = files.length > 1 ? `Hochladen… ${index + 1} / ${files.length}` : 'Hochladen…';
    const data = new FormData();
    data.append('file', file);
    try {
      const response = await http.post(props.url, data, { handleErrors: false });
      emit('uploaded', response.data);
    }
    catch (error) {
      notify({ type: 'error', text: `«${file.name}»: ${failure(error)}` });
    }
  }
  uploading.value = null;
}
</script>
