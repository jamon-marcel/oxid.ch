<template>
  <div>
    <label for="document">{{labelNew}}</label>
    <vue-dropzone
      ref="dropzone"
      id="dropzone"
      :options="dropzoneConfig"
      @vdropzone-complete="uploadComplete"
    ></vue-dropzone>
    <span class="bubble is-restriction">{{labelRestrictions}}</span>
  </div>
</template>
<script>
import vue2Dropzone from "vue2-dropzone";
import draggable from 'vuedraggable';
import dropzoneConfig from "@/config/dz-image.js";

export default {

  components: {
    vueDropzone: vue2Dropzone,
    draggable: draggable,
  },

  props: {
    labelNew: String,
    labelRestrictions: String,
    acceptedFiles: String,
    maxFiles: Number,
    maxFilesize: Number,
    uploadUrl: String,
  },

  data() {
    return {
      dropzoneConfig: dropzoneConfig,
    };
  },

  created() {
    this.dropzoneConfig.url           = this.uploadUrl;
    this.dropzoneConfig.acceptedFiles = this.acceptedFiles;
    this.dropzoneConfig.maxFiles      = this.maxFiles;
    this.dropzoneConfig.maxFilesize   = this.maxFilesize;
  },

  methods: {

    uploadComplete(asset) {
      this.$refs.dropzone.removeFile(asset);
      this.$parent.uploadComplete(asset);
    },
  }
};
</script>