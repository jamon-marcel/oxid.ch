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
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
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
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="discourse.description.de"
                  ></tinymce-editor>
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row is-last">
                  <label>Info</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
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
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="discourse.description_short.en"
                  ></tinymce-editor>
                </div>
                <div class="form-row">
                  <label>Beschreibung</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="discourse.description.en"
                  ></tinymce-editor>
                </div>
                <div class="form-row is-last">
                  <label>Info</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="discourse.info.en"
                  ></tinymce-editor>
                </div>
              </div>
            </div>
          </div>
          <div v-show="tabs.images.active">
            <image-upload
              :labelNew="'Upload Bilder'"
              :labelExisting="'Existierende Bilder'"
              :labelRestrictions="'jpg, png | max. 8 MB'"
              :maxFiles="99"
              :maxFilesize="8"
              :assets="discourse.images"
              :assetType="'image'"
              :acceptedFiles="'.png,.jpg'"
              :uploadUrl="'/api/media/upload'"
            ></image-upload>
          </div>
          <div v-show="tabs.files.active">
            <file-upload
              :labelNew="'Upload Dokumente'"
              :labelExisting="'Existierende Dokumente'"
              :labelRestrictions="'pdf | max. 8 MB'"
              :maxFiles="99"
              :maxFilesize="8"
              :assets="discourse.documents"
              :assetType="'file'"
              :acceptedFiles="'.pdf'"
              :uploadUrl="'/api/media/upload'"
            ></file-upload>
          </div>
          <form-buttons :route="'discourses'"></form-buttons>
        </form>
      </div>
    </main>
  </div>
</template>
<script>
import PageHeader from "@/layout/PageHeader.vue";
import FormButtons from "@/components/global/buttons/FormButtons.vue";
import Tabs from "@/components/global/tabs/Tabs.vue";

import ImageUpload from "@/components/discourses/upload/ImageUpload.vue";
import FileUpload from "@/components/discourses/upload/FileUpload.vue";
import tinyConfig from "@/config/tinyconfig.js";
import Editor from "@tinymce/tinymce-vue";
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

import discourseModel from "@/components/discourses/config/model.js";
import discourseTabs from "@/components/discourses/config/tabs.js";
import discourseErrors from "@/components/discourses/config/errors.js";

export default {
  components: {
    FormButtons: FormButtons,
    tinymceEditor: Editor,
    ImageUpload: ImageUpload,
    FileUpload: FileUpload,
    Tabs: Tabs,
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
      discourse: discourseModel,

      // settings
      categories: [],

      // tinymce config
      tinyConfig: tinyConfig
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

    // Upload image callback
    uploadImage(file) {
      if (file.status == "error" && file.accepted == false) {
        this.$notify({ type: "error", text: "Invalid format or file to big!" });
      } 
      else {
        let file_response = JSON.parse(file.xhr.response);
        file_response.id = null;
        file_response.caption = { de: null, en: null };
        file_response.order = -1;
        file_response.is_preview = 0;
        file_response.publish = 1;
        this.discourse.images.push(file_response);
      }
    },

    // Delete image by its name
    deleteImage(file, event) {
      if (confirm("Please confirm!")) {
        let uri = `/api/discourse/image/destroy/${file}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          const index = this.discourse.images.findIndex(x => x.name === file);
          this.discourse.images.splice(index, 1);
          this.progress(el);
        });
      }
    },

    // Toggle image status
    toggleImage(asset, event) {
      if (asset.id === null) {
        const index = this.discourse.images.findIndex(x => x.name === asset.name);
        this.discourse.images[index].publish = asset.publish == 1 ? 0 : 1;
      } else {
        let uri = `/api/discourse/image/status/${asset.id}`;
        let el = this.progress(event.target);
        this.axios.get(uri).then(response => {
          const index = this.discourse.images.findIndex(x => x.id === asset.id);
          this.discourse.images[index].publish = response.data;
          this.progress(el);
        });
      }
    },

    // Upload file callback
    uploadFile(file) {
      if (file.status == "error" && file.accepted == false) {
        this.$notify({ type: "error", text: "Invalid format or file to big!" });
      } 
      else {
        let file_response = JSON.parse(file.xhr.response);
        file_response.id = null;
        file_response.caption = { de: null, en: null };
        file_response.publish = 1;
        this.discourse.documents.push(file_response);
      }
    },

    // Delete file by its name
    deleteFile(file, event) {
      if (confirm("Please confirm!")) {
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
    toggleFile(asset, event) {
      if (asset.id === null) {
        const index = this.discourse.documents.findIndex(x => x.name === asset.name);
        this.discourse.documents[index].publish = asset.publish == 1 ? 0 : 1;
      } else {
        let uri = `/api/discourse/document/status/${asset.id}`;
        let el = this.progress(event.target);
        this.axios.get(uri).then(response => {
          const index = this.discourse.documents.findIndex(x => x.id === asset.id);
          this.discourse.documents[index].publish = response.data;
          this.progress(el);
        });
      }
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