<template>
  <div>
    <page-header/>
    <notifications classes="notification"/>
    <div class="container">
      <main class="content" role="main">
        <div>
          <h1>Profil - Bilder</h1>
          <div class="form-row">
            <image-upload
              :restrictions="'jpg, png | max. 8 MB'"
              :maxFiles="99"
              :maxFilesize="8"
              :acceptedFiles="'.png,.jpg'"
            ></image-upload>
          </div>
          <div class="form-row" v-if="images.length">
            <a href="" class="icon-view" @click.prevent="toggleView()">
              <span v-if="view == 'grid'">Grid Ansicht</span>
              <span v-if="view == 'list'">Listen Ansicht</span>
            </a>
            <template v-if="view == 'list'">
              <div class="upload-listing-rows">
                <draggable 
                  :disabled="false"
                  v-model="images" 
                  @end="order"
                  ghost-class="draggable-ghost"
                  draggable=".is-draggable">
                  <div class="upload-item-row is-draggable" v-for="(image) in images" :key="image.id">
                    <figure>
                      <img :src="getSource(image.name, 'thumbnail')" height="300" width="300">
                    </figure>
                    <div>
                      <span class="icon-move"></span>
                    </div>
                  </div>
                </draggable>
              </div>
            </template>
            <template v-if="view == 'grid'">
              <image-listing 
                :images="images"
                :updateOnChange="true"
              ></image-listing>
            </template>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
<script>
// Layout
import PageHeader from "@/layout/PageHeader.vue";

// Upload
import ImageUpload from "@/components/global/images/Upload.vue";
import ImageListing from "@/components/global/images/Listing.vue";

// Mixins
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

import draggable from 'vuedraggable';

export default {
  components: {
    PageHeader,
    ImageUpload,
    ImageListing,
    draggable
  },

  mixins: [Utils, Progress],

  data() {
    return {
      images: [],
      view: 'grid',
    };
  },

  created() {
    this.fetch();
  },

  methods: {
    fetch() {
      let uri = "/api/profile/images/get";
      this.axios.get(uri).then(response => {
        this.images = response.data.data;
      });
    },

    // Upload callback
    uploadComplete(image) {
      this.storeImage(image);
    },

    // Save image
    storeImage(image) {
      let uri = "/api/profile/image/create";
      let data = {
        name: image.name,
        caption: {
          de: null,
          en: null,
        },
        coords: null,
        publish: 0
      };

      this.axios.post(uri, data).then(response => {
        data.id = response.data.imageId;
        this.images.push(data);
        this.$notify({ type: "success", text: "Bild gespeichert!" });
      });
    },

    destroyImage(image, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/profile/image/destroy/${image}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          const index = this.images.findIndex(x => x.name === image);
          this.images.splice(index, 1);
          this.progress(el);
        });
      }
    },

    toggleImage(image, event) {
      let uri = `/api/profile/image/status/${image.id}`;
      let el = this.progress(event.target);
      this.axios.get(uri).then(response => {
        const index = this.images.findIndex(x => x.id === image.id);
        this.images[index].publish = response.data;
        this.progress(el);
      });
    },

    updateImage(image, event) {
      let uri = `/api/profile/image/update/${image.id}`;
      this.axios.post(uri, image).then(response => {
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
      });
    },

    saveImageCoords(image) {
      if (image.id === null) {
        const index = this.images.findIndex(x => x.name === image.name);
        this.images[index].coords = image.coords;
      } 
      else {
        let uri = `/api/profile/image/coords/${image.id}`;
        this.axios.post(uri, image).then(response => {
          this.$notify({ type: "success", text: "Änderungen gespeichert!" });
        });
      }
    },

    order() {
      let images = this.images.map(function(image, index) {
        image.order = index;
        return image;
      });
      if (this.debounce) return;
      this.debounce = setTimeout(function(images) {
        this.debounce = false 
        let uri = `/api/profile/image/order`;
        this.axios.post(uri, {images: images}).then((response) => {
          this.$notify({type: 'success', text: 'Reihenfolge angepasst'});
        });
      }.bind(this, images), 1000);
    },

    toggleView() {
      this.view = this.view == 'grid' ? 'list' : 'grid';
    }
  },
};
</script>