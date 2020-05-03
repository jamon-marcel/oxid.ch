<template>
  <div>
    <div class="form-row">
      <label for="document">{{labelNew}}</label>
      <vue-dropzone
        ref="dropzone"
        id="dropzone"
        :options="dropzoneConfig"
        @vdropzone-complete="uploadImage"
      ></vue-dropzone>
      <span class="bubble is-restriction">{{labelRestrictions}}</span>
    </div>
    <div class="form-row" v-if="assets.length">
      <label>{{labelExisting}}</label>
      <div :class="[sortable ? 'has-sortable-images' : 'has-images', 'dropzone-existing-assets']">
        <div>
          <figure
            :class="[asset.publish == 0 ? 'is-disabled' : '', 'dz-existing-asset is-image']"
            v-for="(asset, index) in assets"
            :key="asset.id"
          >
            <a :href="getSource(asset.name, 'large')" target="_blank" class="dz-file-preview">
              <img :src="getSource(asset.name, 'thumbnail')" height="300" width="300">
            </a>
            <div class="dz-toolbar">
              <a
                href="javascript:;"
                :class="[asset.publish == 1 ? 'icon-eye' : 'icon-eye-off', 'icon-mini']"
                @click.prevent="toggleImage(asset,$event)"
              ></a>
              <a
                href="javascript:;"
                class="icon-edit icon-mini"
                @click.prevent="showAssetEdit($event)"
              ></a>
              <a
                :href="getSource(asset.name, 'large')"
                target="_blank"
                class="icon-external-link icon-mini"
              ></a>
              <a
                href="javascript:;"
                :class="[asset.is_grid == 1 ? 'icon-disabled' : '', 'icon-trash icon-mini']"
                @click.prevent="deleteImage(asset.name,$event)"
              ></a>
              <a
                href="javascript:;"
                class="icon-crop icon-mini"
                @click.prevent="showCropper(asset)"
              ></a>
            </div>
            <div class="overlay-asset">
              <div>
                <div class="overlay-grid">
                  <div>
                    <img :src="getSource(asset.name, 'large')" height="300" width="300">
                    <figcaption>
                      {{asset.caption.de}}
                      <br>
                      {{asset.caption.en}}
                    </figcaption>
                  </div>
                  <div>
                    <div class="form-row">
                      <label>Legende:</label>
                      <input type="text" v-model="asset.caption.de" class="is-caption">
                    </div>
                    <div class="form-row">
                      <label>Legende (en):</label>
                      <input type="text" v-model="asset.caption.en" class="is-caption">
                    </div>
                    <div class="form-row">
                      <label>Vorschaubild für:</label>
                      <input
                        type="checkbox"
                        class="visually-hidden"
                        v-model="asset.is_preview_navigation"
                        name="is_preview_navigation"
                        :id="'is_preview_navigation_'+index"
                      >
                      <label :for="'is_preview_navigation_'+index" class="form-control is-auto">Navigation</label>
                      <input
                        type="checkbox"
                        class="visually-hidden"
                        v-model="asset.is_preview_works"
                        name="is_preview_works"
                        :id="'is_preview_works_'+index"
                      >
                      <label :for="'is_preview_works_'+index" class="form-control is-auto">Werkliste</label>
                    </div>
                    <div class="form-row">

                      <label class="is-sm">Plan?</label>
                      <div class="form-radio">
                        <input
                          v-model="asset.is_plan"
                          type="radio"
                          :name="'is_plan_1_'+index"
                          :id="'is_plan_1_'+index"
                          value="1"
                          class="visually-hidden"
                        >
                        <label :for="'is_plan_1_'+index" class="form-control">Ja</label>
                        <input
                          v-model="asset.is_plan"
                          type="radio"
                          :name="'is_plan_0_'+index"
                          :id="'is_plan_0_'+index"
                          value="0"
                          class="visually-hidden"
                        >
                        <label :for="'is_plan_0_'+index" class="form-control">Nein</label>
                      </div>
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
            <div class="overlay-crop" :data-cropper="asset.name">
              <a href="javascript:;" @click.prevent="hideCropper()" class="icon-close-overlay"></a>
              <div>
                <span class="cropper-info">
                  Neue Grösse:
                  <br>
                  {{ cropW }}px x {{ cropH }}px
                </span>
                <div v-if="loader">Loading image...</div>
                <cropper
                  :src="cropImg"
                  :defaultPosition="defaultPosition"
                  :defaultSize="defaultSize"
                  :stencilProps="{
                    aspectRatio: 4/3,
                    linesClassnames: {
                      default: 'line',
                    },
                    handlersClassnames: {
                      default: 'handler'
                    }
                  }"
                  @change="change"
                ></cropper>
                <div class="form-buttons">
                  <a
                    href="javascript:;"
                    class="btn-secondary"
                    @click.prevent="saveCoords(asset)"
                  >Speichern</a>
                  <a href @click.prevent="hideCropper(asset.name)">Abbrechen</a>
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
import draggable from "vuedraggable";
import dropzoneConfig from "@/config/dz-image.js";
import { Cropper } from "vue-advanced-cropper";

export default {
  components: {
    vueDropzone: vue2Dropzone,
    draggable: draggable,
    Cropper
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
    sortable: Boolean
  },

  data() {
    return {
      coords: {},
      cropW: null,
      cropH: null,
      cropImg: null,
      hasCropper: false,
      loader: false,

      dropzoneConfig: dropzoneConfig,

      currentAsset: null
    };
  },

  created() {
    this.dropzoneConfig.url = this.uploadUrl;
    this.dropzoneConfig.acceptedFiles = this.acceptedFiles;
    this.dropzoneConfig.maxFiles = this.maxFiles;
    this.dropzoneConfig.maxFilesize = this.maxFilesize;
  },

  mounted() {},

  methods: {
    change({ coordinates, canvas }) {
      this.coords.h = coordinates.height;
      this.coords.w = coordinates.width;
      this.coords.y = coordinates.top;
      this.coords.x = coordinates.left;
      this.cropW = Math.floor(coordinates.width);
      this.cropH = Math.floor(coordinates.height);
    },

    saveCoords(asset) {
      asset.coords_w = this.coords.w;
      asset.coords_h = this.coords.h;
      asset.coords_x = this.coords.x;
      asset.coords_y = this.coords.y;
      this.$parent.saveImageCoords(asset);
      this.hideCropper(asset.name);
    },

    uploadImage(asset) {
      this.$refs.dropzone.removeFile(asset);
      this.$parent.uploadImage(asset);
    },

    deleteImage(asset, event) {
      this.$parent.deleteImage(asset, event);
    },

    toggleImage(asset, event) {
      this.$parent.toggleImage(asset, event);
    },

    showAssetEdit(e) {
      let editForm = e.target.parentNode.nextElementSibling;
      editForm.classList.toggle("is-visible");
    },

    hideAssetEdit(e) {
      let editForm =
        e.target.parentNode.parentNode.parentNode.parentNode.parentNode;
      editForm.classList.remove("is-visible");
    },

    showCropper(asset) {
      this.currentAsset = asset;
      this.$el
        .querySelector('[data-cropper="' + asset.name + '"]')
        .classList.add("is-visible");
      this.loader = true;
      this.axios.get(this.getSource(asset.name, "original")).then(response => {
        this.cropImg = this.getSource(asset.name, "original");
        this.loader = false;
      });
    },

    hideCropper(img) {
      this.$el
        .querySelector('[data-cropper="' + img + '"]')
        .classList.remove("is-visible");
      this.cropImg = null;
    },

    updateOrder() {
      let assets = this.assets.map(function(asset, index) {
        asset.order = index;
        return asset;
      });
      return;
      // if (this.debounce) return;
      // this.debounce = setTimeout(function(categories) {
      //   this.debounce = false
      //   let uri = `/api/category/order`;
      //   this.axios.post(uri, {categories: categories}).then((response) => {
      //     this.$router.push({name: 'categories'});
      //   });
      // }.bind(this, categories), 1000);
      // this.$notify({type: 'success', text: 'Reihenfolge angepasst'});
    },

    defaultPosition() {
      let x = this.currentAsset.coords_x || 100;
      let y = this.currentAsset.coords_y || 100;
      return {
        left: x,
        top: y
      };
    },

    defaultSize() {
      let w = this.currentAsset.coords_w || 400;
      let h = this.currentAsset.coords_h || 300;
      return {
        width: w,
        height: h
      };
    },

    getSource(asset, size) {
      return `/image/${size}/${asset}`;
    }
  }
};
</script>
<style>
.cropper {
  max-height: 700px;
  background: transparent;
  padding-top: 80px;
}
.cropper-info {
  display: block;
  margin-bottom: 10px;
  text-align: left;
}
.line {
  border-style: dashed;
  border-color: #5cb85c;
}
.handler {
  background-color: #5cb85c;
}
.vue-advanced-cropper__image {
  opacity: 0.5 !important;
}
</style>