<template>
  <div>
    <label v-if="label">{{ label }}</label>
    <div
      :class="['uploader', { 'is-dragover': isDragover, 'is-uploading': isUploading }]"
      @click="input.click()"
      @dragover.prevent="isDragover = true"
      @dragleave.prevent="isDragover = false"
      @drop.prevent="drop"
    >
      <span>{{ isUploading ? `Hochladen… ${position} / ${sending.length}` : 'Dateien hierher ziehen oder klicken' }}</span>
      <input ref="input" type="file" :accept="acceptedFiles" :multiple="maxFiles > 1" hidden @change="choose">
    </div>
    <span class="bubble is-restriction">{{ restrictions }}</span>
    <ul class="uploader-queue" v-if="queue.length">
      <li v-for="item in queue" :key="item.id" :class="`is-${item.state}`">
        <PhCheckCircle v-if="item.state === 'done'" :size="18" weight="light" class="uploader-queue__icon" />
        <PhWarningCircle v-else-if="item.state === 'error'" :size="18" weight="light" class="uploader-queue__icon" />
        <PhFile v-else :size="18" weight="light" class="uploader-queue__icon" />
        <span class="uploader-queue__name">{{ item.name }}</span>
        <span class="uploader-queue__state">{{ stateLabel(item) }}</span>
        <span class="uploader-queue__bar" v-if="item.state === 'uploading'" :style="{ width: `${item.progress}%` }"></span>
      </li>
    </ul>
  </div>
</template>
<script setup>
import { ref, computed } from 'vue';
import { PhFile, PhCheckCircle, PhWarningCircle } from '@phosphor-icons/vue';
import http from '@/lib/http';

// Drop zone + file picker. Files go up one after the other; each gets a row
// with its progress, then "fertig" or the reason it was rejected. Emits the
// upload endpoint's JSON response ({ name, filetype, orientation }) per file.
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
const isUploading = ref(false);

// { id, name, progress 0–100, state: waiting | uploading | done | error, message }
const queue = ref([]);
let id = 0;
let clearTimer = null;

// The counter counts only files that are sent (not those rejected up front)
const sending = computed(() => queue.value.filter(item => item.sent));
const position = computed(() => sending.value.filter(item => item.state !== 'waiting').length);

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

function stateLabel(item) {
  switch (item.state) {
    case 'waiting': return 'wartet';
    case 'uploading': return item.progress < 100 ? `${item.progress} %` : 'wird verarbeitet…';
    case 'done': return 'fertig';
    default: return item.message;
  }
}

async function upload(files) {
  if (isUploading.value || !files.length) {
    return;
  }
  clearTimeout(clearTimer);

  const items = files.slice(0, props.maxFiles).map(file => {
    const message = rejection(file);
    return { id: ++id, file, name: file.name, progress: 0, state: message ? 'error' : 'waiting', message, sent: !message };
  });
  files.slice(props.maxFiles).forEach(file => items.push({
    id: ++id, file, name: file.name, progress: 0, state: 'error', message: `Zu viele Dateien (max. ${props.maxFiles} auf einmal).`,
  }));
  queue.value = items;

  isUploading.value = true;
  for (const item of queue.value.filter(item => item.state === 'waiting')) {
    item.state = 'uploading';
    const data = new FormData();
    data.append('file', item.file);
    try {
      const response = await http.post(props.url, data, {
        handleErrors: false,
        onUploadProgress: event => item.progress = event.total ? Math.round(event.loaded / event.total * 100) : 0,
      });
      item.state = 'done';
      item.progress = 100;
      emit('uploaded', response.data);
    }
    catch (error) {
      item.state = 'error';
      item.message = failure(error);
    }
  }
  isUploading.value = false;

  // Finished rows go; rejected ones stay until the next upload
  clearTimer = setTimeout(() => {
    queue.value = queue.value.filter(item => item.state === 'error');
  }, 2000);
}
</script>
