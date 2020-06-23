<template>
  <div class="container">
    <notifications classes="notification"/>
    <main class="content" role="main">
      <div>
        <h1>{{title}}</h1>
        <tabs :tabs="tabs" :errors="errors"></tabs>
        <form @submit.prevent="submit">
          <div v-show="tabs.data.active">
            <div class="grid-main-sidebar">
              <div class="column-main">
                <div class="form-row" :class="errors.heading.de ? 'has-error': ''">
                  <label>Rubrik *</label>
                  <input type="text" @focus="removeError('heading', 'de')" v-model="discourse.heading.de">
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row" :class="errors.date.de ? 'has-error': ''">
                  <label>Datum/Zeitraum *</label>
                  <input
                    type="text"
                    @focus="removeError('date', 'de')"
                    v-model="discourse.date.de"
                  >
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row" :class="errors.title.de ? 'has-error': ''">
                  <label>Titel *</label>
                  <input
                    type="text"
                    @focus="removeError('title', 'de')"
                    v-model="discourse.title.de"
                  >
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div
                  class="form-row"
                  :class="errors.description_short.de ? 'has-error': ''"
                  @mouseenter="removeError('description_short', 'de')"
                >
                  <label>Kurzbeschreibung *</label>
                  <tinymce-editor
                    :api-key="tinyApiKey"
                    :init="tinyConfig"
                    v-model="discourse.description_short.de"
                  ></tinymce-editor>
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div
                  class="form-row"
                  :class="errors.description.de ? 'has-error': ''"
                  @mouseenter="removeError('description', 'de')"
                >
                  <label>Beschreibung *</label>
                  <tinymce-editor
                    :api-key="tinyApiKey"
                    :init="tinyConfig"
                    v-model="discourse.description.de"
                  ></tinymce-editor>
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row is-last">
                  <label>Info</label>
                  <tinymce-editor
                    :api-key="tinyApiKey"
                    :init="tinyConfig"
                    v-model="discourse.info.de"
                  ></tinymce-editor>
                </div>
              </div>
              <div class="column-sidebar">
                <div>
                  <div class="form-row is-sm">
                    <label>Kategorie</label>
                    <div class="select-wrapper is-sidebar">
                      <select v-model="discourse.category" name="category">
                        <option v-for="(c, index) in categories" :key="index" :value="index">{{ c }}</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-row is-sm is-last">
                    <label class="is-sm">Publizieren?</label>
                    <div class="form-radio">
                      <input
                        v-model="discourse.publish"
                        type="radio"
                        name="publish_1"
                        id="publish_1"
                        value="1"
                        class="visually-hidden"
                      >
                      <label for="publish_1" class="form-control">Ja</label>
                      <input
                        v-model="discourse.publish"
                        type="radio"
                        name="publish_0"
                        id="publish_0"
                        value="0"
                        class="visually-hidden"
                      >
                      <label for="publish_0" class="form-control">Nein</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-show="tabs.translation.active">
            <div class="grid-main-sidebar">
              <div class="column-main">
                <div class="form-row">
                  <label>Rubrik</label>
                  <input type="text" v-model="discourse.heading.en">
                </div>
                <div class="form-row">
                  <label>Datum/Zeitraum</label>
                  <input type="text" v-model="discourse.date.en">
                </div>
                <div class="form-row">
                  <label>Titel</label>
                  <input type="text" v-model="discourse.title.en">
                </div>
                <div class="form-row">
                  <label>Kurzbeschreibung</label>
                  <tinymce-editor
                    :api-key="tinyApiKey"
                    :init="tinyConfig"
                    v-model="discourse.description_short.en"
                  ></tinymce-editor>
                </div>
                <div class="form-row">
                  <label>Beschreibung</label>
                  <tinymce-editor
                    :api-key="tinyApiKey"
                    :init="tinyConfig"
                    v-model="discourse.description.en"
                  ></tinymce-editor>
                </div>
                <div class="form-row is-last">
                  <label>Info</label>
                  <tinymce-editor
                    :api-key="tinyApiKey"
                    :init="tinyConfig"
                    v-model="discourse.info.en"
                  ></tinymce-editor>
                </div>
              </div>
            </div>
          </div>
          <div v-show="tabs.images.active">
            <div class="form-row">
              <image-upload
                :restrictions="'jpg, png | max. 8 MB'"
                :maxFiles="99"
                :maxFilesize="8"
                :acceptedFiles="'.png,.jpg'"
              ></image-upload>
            </div>
            <div class="form-row" v-if="discourse.images.length">

              <a href="" class="icon-view" @click.prevent="toggleView()">
                <span v-if="view == 'grid'">Grid Ansicht</span>
                <span v-if="view == 'list'">Listen Ansicht</span>
              </a>

              <template v-if="view == 'list'">
                <div class="upload-listing-rows">
                  <draggable 
                    :disabled="false"
                    v-model="discourse.images" 
                    @end="order"
                    ghost-class="draggable-ghost"
                    draggable=".is-draggable">
                    <div class="upload-item-row is-draggable" v-for="(image) in discourse.images" :key="image.id">
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
                  :images="discourse.images"
                ></image-listing>
              </template>
            </div>
          </div>
          <div v-show="tabs.files.active">
            <div class="form-row">
              <file-upload
                :restrictions="'pdf | max. 8 MB'"
                :maxFiles="99"
                :maxFilesize="8"
                :acceptedFiles="'.pdf'"
              ></file-upload>
            </div>
            <div class="form-row" v-if="discourse.documents.length">
              <file-listing 
                :files="discourse.documents"
              ></file-listing>
            </div>
          </div>
          <form-footer :route="'discourses'"></form-footer>
        </form>
      </div>
    </main>
  </div>
</template>
<script>
// Layout
import PageHeader from "@/layout/PageHeader.vue";

// Form elements
import FormFooter from "@/components/global/form/Footer.vue";

// Tabs
import Tabs from "@/components/global/tabs/Tabs.vue";

// Upload
import ImageUpload from "@/components/global/images/Upload.vue";
import ImageListing from "@/components/discourses/images/Listing.vue";
import FileUpload from "@/components/global/files/Upload.vue";
import FileListing from "@/components/global/files/Listing.vue";

// TinyMCE
import tinyConfig from "@/config/tinyconfig.js";
import TinymceEditor from "@tinymce/tinymce-vue";

// Mixins
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

// Config
import discourseTabs from "@/components/discourses/config/tabs.js";
import discourseErrors from "@/components/discourses/config/errors.js";

import draggable from 'vuedraggable';

export default {
  components: {
    FormFooter,
    TinymceEditor,
    FileUpload,
    FileListing,
    ImageUpload,
    ImageListing,
    Tabs,
    draggable
  },

  props: {
    type: String
  },

  mixins: [Utils, Progress],

  data() {
    return {

      // discourse validation
      errors: discourseErrors,

      // discourse tabs
      tabs: discourseTabs,

      // discourse model
      discourse: {
        heading: {
          de: null,
          en: null,
        },
        date: {
          de: null,
          en: null,
        },
        title: {
          de: null,
          en: null,
        },
        description_short: {
          de: null,
          en: null
        },
        description: {
          de: null,
          en: null
        },
        info: {
          de: null,
          en: null,
        },
        images: [],
        documents: [],
        publish: 0,
        category: 1,
      },

      // settings
      categories: [],

      // TinyMCE
      tinyConfig: tinyConfig,
      tinyApiKey: 'vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro',

      // view
      view: 'grid',
    };
  },

  created() {
    if (this.$props.type == "edit") {
      let uri = `/api/discourse/edit/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.discourse = response.data;
        this.tabs.data.active = true;
      });
    }

    this.axios.get(`/api/settings/discourseCategories`).then(response => {
      this.categories = response.data;
    });

  },

  mounted() {
    this.removeErrors();
  },

  methods: {
    // Validation methods
    validate() {
      if (
        this.discourse.heading.de &&
        this.discourse.date.de &&
        this.discourse.title.de &&
        this.discourse.description_short.de &&
        this.discourse.description.de &&
        this.discourse.images.length > 0
      ) {
        return true;
      }

      if (!this.discourse.heading.de) {
        this.errors.heading.de = true;
        this.tabs.data.error = true;
      }

      if (!this.discourse.date.de) {
        this.errors.date.de = true;
        this.tabs.data.error = true;
      }

      if (!this.discourse.title.de) {
        this.errors.title.de = true;
        this.tabs.data.error = true;
      }

      if (!this.discourse.description_short.de) {
        this.errors.description_short.de = true;
        this.tabs.data.error = true;
      }

      if (!this.discourse.description.de) {
        this.errors.description.de = true;
        this.tabs.data.error = true;
      }

      if (this.discourse.images.length == 0) {
        this.tabs.images.error = true;
      }

      return false;
    },

    // Submit form
    submit() {
      if (!this.validate()) {
        this.validationError();
        return;
      }

      if (this.$props.type == "edit") {
        this.update();
      }

      if (this.$props.type == "create") {
        this.store();
      }
    },

    // Store the project
    store() {
      let uri = "/api/discourse/create";
      this.axios.post(uri, this.discourse).then(response => {
        this.$router.push({ name: "discourses" });
        this.$notify({ type: "success", text: "Diskurs erfasst!" });
      });
    },

    // Update the project
    update() {
      let uri = `/api/discourse/update/${this.$route.params.id}`;
      this.axios.post(uri, this.discourse).then(response => {
        this.$router.push({ name: "discourses" });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
      });
    },

    // Store uploaded image
    storeImage(upload) {
      let image = {
        id: null,
        name: upload.name,
        caption: { de: null, en: null },
        is_preview: 0,
        publish: 1,
        theme: 0,
      }
      this.discourse.images.push(image);
    },

    // Delete by name
    destroyImage(image, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/discourse/image/destroy/${image}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          const index = this.discourse.images.findIndex(x => x.name === image);
          this.discourse.images.splice(index, 1);
          this.progress(el);
        });
      }
    },

    // Toggle image status
    toggleImage(image, event) {
      if (image.id === null) {
        const index = this.discourse.images.findIndex(x => x.name === image.name);
        this.discourse.images[index].publish = image.publish == 1 ? 0 : 1;
      } else {
        let uri = `/api/discourse/image/status/${image.id}`;
        let el = this.progress(event.target);
        this.axios.get(uri).then(response => {
          const index = this.discourse.images.findIndex(x => x.id === image.id);
          this.discourse.images[index].publish = response.data;
          this.progress(el);
        });
      }
    },

    // Store uploaded file
    storeFile(upload) {
      let file = {
        id: null,
        name: upload.name,
        caption: { de: null, en: null },
        publish: 1,
      }
      this.discourse.documents.push(file);
    },

    // Delete by name
    destroyFile(file, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/discourse/document/destroy/${file}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          const index = this.discourse.documents.findIndex(x => x.name === file);
          this.discourse.documents.splice(index, 1);
          this.progress(el);
        });
      }
    },

    // Toggle file status
    toggleFile(file, event) {
      if (file.id === null) {
        const index = this.discourse.documents.findIndex(x => x.name === file.name);
        this.discourse.documents[index].publish = file.publish == 1 ? 0 : 1;
      } else {
        let uri = `/api/discourse/document/status/${file.id}`;
        let el = this.progress(event.target);
        this.axios.get(uri).then(response => {
          const index = this.discourse.documents.findIndex(x => x.id === file.id);
          this.discourse.documents[index].publish = response.data;
          this.progress(el);
        });
      }
    },

    // Save coords
    saveImageCoords(image) {
      if (image.id === null) {
        const index = this.discourse.images.findIndex(x => x.name === image.name);
        this.discourse.images[index].coords = image.coords;
      } 
      else {
        let uri = `/api/discourse/image/coords/${image.id}`;
        this.axios.post(uri, image).then(response => {
          this.$notify({ type: "success", text: "Änderungen gespeichert!" });
        });
      }
    },

    order() {
      let images = this.discourse.images.map(function(image, index) {
        image.order = index;
        return image;
      });
      if (this.debounce) return;
      this.debounce = setTimeout(function(images) {
        this.debounce = false 
        let uri = `/api/discourse/image/order`;
        this.axios.post(uri, {images: images}).then((response) => {
          this.$notify({type: 'success', text: 'Reihenfolge angepasst'});
        });
      }.bind(this, images), 1000);
    },

    toggleView() {
      this.view = this.view == 'grid' ? 'list' : 'grid';
    }
  },

  computed: {
    title: function() {
      return this.$props.type == "edit"
        ? "Diskurs bearbeiten"
        : "Diskurs hinzufügen";
    }
  }
};
</script>