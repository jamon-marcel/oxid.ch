<template>
  <div>
    <page-header/>
    <notifications classes="notification"/>
    <div class="container">
      <main class="content" role="main">
        <div>
          <h1>Team - Bilder</h1>
          <div class="form-row" v-if="images.length">
            <label>Existierende Bilder</label>
            <div class="has-images dropzone-existing-assets">
              <div>
                <figure
                  :class="[asset.publish == 0 ? 'is-disabled' : '', 'dz-existing-asset is-image']"
                  v-for="asset in images"
                  :key="asset.id"
                >
                  <a :href="getSource(asset.name, 'large')" target="_blank" class="dz-file-preview">
                    <img :src="getSource(asset.name, 'thumbnail')" height="300" width="300">
                  </a>
                  <div class="dz-toolbar">
                    <a
                      href="javascript:;"
                      :class="[asset.publish == 1 ? 'icon-eye' : 'icon-eye-off', 'icon-mini']"
                      @click.prevent="toggleAsset(asset,$event)">
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
                      @click.prevent="deleteAsset(asset,$event)">
                    </a>
                  </div>
                  <div :class="[asset.publish == 1 ? 'icon-eye' : 'icon-eye-off', 'overlay-asset']">
                    <div>
                      <div class="overlay-grid">
                        <div>
                          <img :src="getSource(asset.name, 'large')" height="300" width="300">
                          <figcaption>
                            {{asset.caption.de}}<br>
                            {{asset.caption.en}}
                          </figcaption>
                        </div>
                        <div>
                          <div class="form-row">
                            <label>Legende (de):</label>
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
                              @click.prevent="updateAsset(asset, $event)"
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
          <div class="form-row">
            <upload
              :labelNew="'Upload'"
              :labelExisting="'Existierende Bilder'"
              :labelRestrictions="'jpg, png | max. 8 MB'"
              :maxFiles="99"
              :maxFilesize="8"
              :assets="images"
              :assetType="'image'"
              :acceptedFiles="'.png,.jpg'"
              :uploadUrl="'/api/media/upload'"
            >
            </upload>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
<script>
import PageHeader from "@/layout/PageHeader.vue";
import Upload from "@/components/team/images/Upload.vue";
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

export default {
  components: {
    PageHeader: PageHeader,
    Upload: Upload,
  },

  mixins: [Utils, Progress],

  data() {
    return {
      images: [],
    };
  },

  created() {
    this.fetch();
  },

  methods: {
    fetch() {
      let uri = "/api/team/images/get";
      this.axios.get(uri).then(response => {
        this.images = response.data.data;
      });
    },

    // Upload callback
    uploadComplete(file) {
      if (file.status == "error" && file.accepted == false) {
        this.$notify({ type: "error", text: "Invalid format or file to big!" });
      } 
      else {
        let image = JSON.parse(file.xhr.response);
        this.storeAsset(image);
      }
    },

    // Save image
    storeAsset(image) {
      let uri = "/api/team/image/create";
      let data = {
        name: image.name,
        caption: {
          de: null,
          en: null,
        },
        publish: 0
      };

      this.axios.post(uri, data).then(response => {
        data.id = response.data.imageId;
        this.images.push(data);
        this.$notify({ type: "success", text: "Bild gespeichert!" });
      });
    },

    deleteAsset(image, event) {
      if (confirm("Please confirm!")) {
        let uri = `/api/team/image/destroy/${image.id}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          const index = this.images.findIndex(x => x.name === image.name);
          this.images.splice(index, 1);
          this.progress(el);
        });
      }
    },

    toggleAsset(asset, event) {
      let uri = `/api/team/image/status/${asset.id}`;
      let el = this.progress(event.target);
      this.axios.get(uri).then(response => {
        const index = this.images.findIndex(x => x.id === asset.id);
        this.images[index].publish = response.data;
        this.progress(el);
      });
    },

    updateAsset(asset, event) {
      let uri = `/api/team/image/update/${asset.id}`;
      this.axios.post(uri, asset).then(response => {
        this.hideAssetEdit(event);
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
      });
    },

    showAssetEdit(e) {
      let editForm = e.target.parentNode.nextElementSibling;
      editForm.classList.toggle('is-visible');
    },

    hideAssetEdit(e) {
      let editForm= e.target.parentNode.parentNode.parentNode.parentNode.parentNode;
      editForm.classList.remove('is-visible');
    },

    getSource(asset, size) {
      return `/image/${size}/${asset}`;
    }

  },
};
</script>