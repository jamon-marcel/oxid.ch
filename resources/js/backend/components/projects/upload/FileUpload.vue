<template>
  <div>
    <div class="form-row">
      <label for="document">{{labelNew}}</label>
      <vue-dropzone
        ref="dropzone"
        id="dropzone"
        :options="dropzoneConfig"
        @vdropzone-complete="uploadFile"
      ></vue-dropzone>
      <span class="bubble is-restriction">{{labelRestrictions}}</span>
    </div>
    <div class="form-row" v-if="assets.length">
      <label>{{labelExisting}}</label>
      <div :class="[sortable ? 'has-sortable-images' : 'has-images', 'dropzone-existing-assets']">
        <div>
          <figure
            :class="[asset.publish == 0 ? 'is-disabled' : '', 'dz-existing-asset is-image']"
            v-for="asset in assets"
            :key="asset.id"
          >
            <a :href="getSource(asset.name)" target="_blank" class="dz-file-preview">
              <img src="/assets/backend/img/icons/file.svg" height="100" width="100">
            </a>
            <div class="dz-toolbar">
              <a
                href="javascript:;"
                :class="[asset.publish == 1 ? 'icon-eye' : 'icon-eye-off', 'icon-mini']"
                @click.prevent="toggleFile(asset,$event)">
              </a>
              <a
                href="javascript:;"
                class="icon-edit icon-mini"
                @click.prevent="showAssetEdit($event)"
              ></a>
              <a
                href="javascript:;"
                class="icon-trash icon-mini"
                @click.prevent="deleteFile(asset.name,$event)">
              </a>
            </div>
            <div class="overlay-asset">
              <div>
                <div class="overlay-grid is-files">
                  <div>
                    <img src="/assets/backend/img/icons/file.svg" height="100" width="100">
                    <figcaption>{{asset.caption.de}}<br>{{asset.caption.en}}</figcaption>
                  </div>
                  <div>
                    <div class="form-row">
                      <label>Datei:</label>
                      <span>{{asset.name}}</span>
                    </div>
                    <div class="form-row">
                      <label>Legende:</label>
                      <input type="text" v-model="asset.caption.de" class="is-caption">
                    </div>
                    <div class="form-row">
                      <label>Legende (en):</label>
                      <input type="text" v-model="asset.caption.en" class="is-caption">
                    </div>
                    <div class="form-row-button">
                      <a
                        href="javascript:;"
                        class="btn-secondary"
                        @click.prevent="hideAssetEdit($event)"
                      >Speichern</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </figure>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import vue2Dropzone from "vue2-dropzone";
import draggable from 'vuedraggable';
import dropzoneConfig from "@/config/dz-file.js";

export default {

  components: {
    vueDropzone: vue2Dropzone,
    draggable: draggable,
  },

  props: {
    labelNew: String,
    labelExisting: String,
    labelRestrictions: String,
    assets: Array,
    assetType: String,
    acceptedFiles: String,
    maxFiles: Number,
    maxFilesize: Number,
    uploadUrl: String,
    sortable: Boolean,
  },

  data() {
    return {
      dropzoneConfig: dropzoneConfig,
    };
  },

  created() {
    this.dropzoneConfig.url = this.uploadUrl;
    this.dropzoneConfig.acceptedFiles = this.acceptedFiles;
    this.dropzoneConfig.maxFiles = this.maxFiles;
    this.dropzoneConfig.maxFilesize = this.maxFilesize;
  },

  methods: {

    uploadFile(asset) {
      this.$refs.dropzone.removeFile(asset);
      this.$parent.uploadFile(asset);
    },

    deleteFile(asset,event) {
      this.$parent.deleteFile(asset,event);
    },

    toggleFile(asset,event) {
      this.$parent.toggleFile(asset,event);
    },

    showAssetEdit(e) {
      let editForm = e.target.parentNode.nextElementSibling;
      editForm.classList.toggle('is-visible');
    },

    hideAssetEdit(e) {
      let editForm= e.target.parentNode.parentNode.parentNode.parentNode.parentNode;
      editForm.classList.remove('is-visible');
    },

    getSource(asset) {
      return `/storage/uploads/${asset}`;
    }
  }
};
</script>