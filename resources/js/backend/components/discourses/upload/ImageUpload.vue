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
                @click.prevent="toggleImage(asset,$event)">
              </a>
              <a
                href="javascript:;"
                class="icon-edit icon-mini"
                @click.prevent="showAssetEdit($event)"
              ></a>
              <a 
                :href="getSource(asset.name, 'large')" 
                target="_blank" 
                class="icon-external-link icon-mini">
              </a>
              <a
                href="javascript:;"
                class="icon-trash icon-mini"
                @click.prevent="deleteImage(asset.name,$event)">
              </a>
            </div>
            <div :class="[asset.publish == 1 ? 'icon-eye' : 'icon-eye-off', 'overlay-asset']">
              <div>
                <div class="overlay-grid">
                  <div>
                    <img :src="getSource(asset.name, 'large')" height="300" width="300">
                    <figcaption>{{asset.caption.de}}<br>{{asset.caption.en}}</figcaption>
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
                      <label>Vorschaubild?</label>
                      <div class="form-radio">
                        <input type="radio" class="visually-hidden" v-model="asset.is_preview" value="1" :id="'is_preview_1_'+index">
                        <label :for="'is_preview_1_'+index" class="form-control">Ja</label>
                        <input type="radio" class="visually-hidden" v-model="asset.is_preview" value="0" :id="'is_preview_0_'+index">
                        <label :for="'is_preview_0_'+index" class="form-control">Nein</label>
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
          </figure>
        </div>
      </div>
    </div>
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

    uploadImage(asset) {
      console.log(asset);
      this.$refs.dropzone.removeFile(asset);
      this.$parent.uploadImage(asset);
    },

    deleteImage(asset,event) {
      this.$parent.deleteImage(asset,event);
    },

    toggleImage(asset,event) {
      this.$parent.toggleImage(asset,event);
    },

    showAssetEdit(e) {
      let editForm = e.target.parentNode.nextElementSibling;
      editForm.classList.toggle('is-visible');
    },

    hideAssetEdit(e) {
      let editForm= e.target.parentNode.parentNode.parentNode.parentNode.parentNode;
      editForm.classList.remove('is-visible');
    },

    updateOrder() {
      let assets = this.assets.map(function(asset, index) {
        asset.order = index;
        return asset;
      });

      console.log(assets);
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

    getSource(asset, size) {
      return `/image/${size}/${asset}`;
    }
  }
};
</script>