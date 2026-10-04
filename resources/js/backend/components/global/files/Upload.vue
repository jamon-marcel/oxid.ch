<template>
  <div>
    <label>Upload</label>
    <dropzone
      :accepted-files="acceptedFiles"
      :max-files="maxFiles"
      :max-filesize="maxFilesize"
      @uploaded="$parent.storeFile($event)"
    ></dropzone>
    <span class="bubble is-restriction">{{restrictions}}</span>
  </div>
</template>
<script>
import Dropzone from "@/components/global/upload/Dropzone.vue";

export default {
  components: {
    Dropzone,
  },

  props: {
    restrictions: String,
    acceptedFiles: String,
    maxFiles: Number,
    maxFilesize: Number,
  },
};
</script>
