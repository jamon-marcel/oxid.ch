<template>
  <div ref="el" class="vue-dropzone dropzone"></div>
</template>
<script>
import Dropzone from 'dropzone';

Dropzone.autoDiscover = false;

// Thin wrapper around Dropzone 6, in place of vue2-dropzone. Emits the
// upload endpoint's JSON response (name, orientation, …) per file.
export default {
  props: {
    url: { type: String, default: '/api/media/upload' },
    acceptedFiles: { type: String, required: true },
    maxFiles: { type: Number, default: 1 },
    maxFilesize: { type: Number, required: true },
  },

  emits: ['uploaded'],

  mounted() {
    const token = document.head.querySelector('meta[name="csrf-token"]');

    this.dropzone = new Dropzone(this.$refs.el, {
      url: this.url,
      method: 'post',
      acceptedFiles: this.acceptedFiles,
      maxFiles: this.maxFiles,
      maxFilesize: this.maxFilesize,
      createImageThumbnails: false,
      headers: token ? { 'X-CSRF-TOKEN': token.content } : {},
      hiddenInputContainer: this.$refs.el.parentElement,
    });

    // Rejected in the browser (type, size, count) or by the server
    this.dropzone.on('error', (file, message, xhr) => {
      this.$notify({ type: 'error', text: xhr ? `Upload fehlgeschlagen (${xhr.status})` : 'Ungültiges Format oder Datei zu gross!' });
    });

    this.dropzone.on('complete', file => {
      if (file.status === 'success') {
        this.$emit('uploaded', JSON.parse(file.xhr.response));
      }
      this.dropzone.removeFile(file);
    });
  },

  beforeUnmount() {
    this.dropzone?.destroy();
  },
};
</script>
