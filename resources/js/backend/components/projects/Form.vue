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
                <div class="form-row" :class="errors.title.de ? 'has-error': ''">
                  <label>Titel *</label>
                  <input type="text" @focus="removeError('title', 'de')" v-model="project.title.de">
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row" :class="errors.title_short.de ? 'has-error': ''">
                  <label>Kurztitel *</label>
                  <input
                    type="text"
                    @focus="removeError('title_short', 'de')"
                    v-model="project.title_short.de"
                  >
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row" :class="errors.location.de ? 'has-error': ''">
                  <label>Ort *</label>
                  <input
                    type="text"
                    @focus="removeError('location', 'de')"
                    v-model="project.location.de"
                  >
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row" :class="errors.year ? 'has-error': ''">
                  <label>Jahr *</label>
                  <input type="text" @focus="removeError('year')" v-model="project.year">
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row">
                  <label>Beschreibung</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="project.description.de"
                  ></tinymce-editor>
                </div>
                <div class="form-row">
                  <label>Info</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="project.info.de"
                  ></tinymce-editor>
                </div>
                <h3 class="is-label">Infos Werkliste</h3>
                <div class="form-row">
                  <label>Jahr</label>
                  <input type="text" v-model="project.year_works">
                </div>
                <div class="form-row">
                  <label>Auftraggeber</label>
                  <input type="text" v-model="project.client_works">
                </div>
                <div class="form-row is-last">
                  <label>Bauherrschaft</label>
                  <input type="text" v-model="project.principal_works">
                </div>


              </div>
              <div class="column-sidebar">
                <div>
                  <div class="form-row is-sm">
                    <label>Programm</label>
                    <div class="select-wrapper is-sidebar">
                      <select v-model="project.program" name="program">
                        <option v-for="(p, index) in programs" :key="index" :value="index">{{ p }}</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-row is-sm">
                    <label>Autorenschaft</label>
                    <div class="select-wrapper is-sidebar">
                      <select v-model="project.author" name="author">
                        <option v-for="(a, index) in authors" :key="index" :value="index">{{ a }}</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-row is-sm">
                    <label>Status</label>
                    <div class="select-wrapper is-sidebar">
                      <select v-model="project.state" name="state">
                        <option v-for="(s, index) in states" :key="index" :value="index">{{ s }}</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-row is-sm">
                    <label class="is-sm">Detailseite?</label>
                    <div class="form-radio">
                      <input
                        v-model="project.has_detail"
                        type="radio"
                        name="has_detail_1"
                        id="has_detail_1"
                        value="1"
                        class="visually-hidden"
                      >
                      <label for="has_detail_1" class="form-control">Ja</label>
                      <input
                        v-model="project.has_detail"
                        type="radio"
                        name="has_detail_0"
                        id="has_detail_0"
                        value="0"
                        class="visually-hidden"
                      >
                      <label for="has_detail_0" class="form-control">Nein</label>
                    </div>
                  </div>
                  <div class="form-row is-sm">
                    <label class="is-sm">Highlight?</label>
                    <div class="form-radio">
                      <input
                        v-model="project.is_highlight"
                        type="radio"
                        name="is_highlight_1"
                        id="is_highlight_1"
                        value="1"
                        class="visually-hidden"
                      >
                      <label for="is_highlight_1" class="form-control">Ja</label>
                      <input
                        v-model="project.is_highlight"
                        type="radio"
                        name="is_highlight_0"
                        id="is_highlight_0"
                        value="0"
                        class="visually-hidden"
                      >
                      <label for="is_highlight_0" class="form-control">Nein</label>
                    </div>
                  </div>
                  <div class="form-row is-sm">
                    <label class="is-sm">Filter - Holz?</label>
                    <div class="form-radio">
                      <input
                        v-model="project.is_filter_wood"
                        type="radio"
                        name="is_filter_wood_1"
                        id="is_filter_wood_1"
                        value="1"
                        class="visually-hidden"
                      >
                      <label for="is_filter_wood_1" class="form-control">Ja</label>
                      <input
                        v-model="project.is_filter_wood"
                        type="radio"
                        name="is_filter_wood_0"
                        id="is_filter_wood_0"
                        value="0"
                        class="visually-hidden"
                      >
                      <label for="is_filter_wood_0" class="form-control">Nein</label>
                    </div>
                  </div>
                  <div class="form-row is-sm">
                    <label class="is-sm">Filter - Umnutzung?</label>
                    <div class="form-radio">
                      <input
                        v-model="project.is_filter_reuse"
                        type="radio"
                        name="is_filter_reuse_1"
                        id="is_filter_reuse_1"
                        value="1"
                        class="visually-hidden"
                      >
                      <label for="is_filter_reuse_1" class="form-control">Ja</label>
                      <input
                        v-model="project.is_filter_reuse"
                        type="radio"
                        name="is_filter_reuse_0"
                        id="is_filter_reuse_0"
                        value="0"
                        class="visually-hidden"
                      >
                      <label for="is_filter_reuse_0" class="form-control">Nein</label>
                    </div>
                  </div>
                  <div class="form-row is-sm">
                    <label class="is-sm">Filter - Areal?</label>
                    <div class="form-radio">
                      <input
                        v-model="project.is_filter_area"
                        type="radio"
                        name="is_filter_area_1"
                        id="is_filter_area_1"
                        value="1"
                        class="visually-hidden"
                      >
                      <label for="is_filter_area_1" class="form-control">Ja</label>
                      <input
                        v-model="project.is_filter_area"
                        type="radio"
                        name="is_filter_area_0"
                        id="is_filter_area_0"
                        value="0"
                        class="visually-hidden"
                      >
                      <label for="is_filter_area_0" class="form-control">Nein</label>
                    </div>
                  </div>
                  <div class="form-row is-sm is-last">
                    <label class="is-sm">Publizieren?</label>
                    <div class="form-radio">
                      <input
                        v-model="project.publish"
                        type="radio"
                        name="publish_1"
                        id="publish_1"
                        value="1"
                        class="visually-hidden"
                      >
                      <label for="publish_1" class="form-control">Ja</label>
                      <input
                        v-model="project.publish"
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
                  <label>Titel</label>
                  <input type="text" v-model="project.title.en">
                </div>
                <div class="form-row">
                  <label>Kurztitel</label>
                  <input type="text" v-model="project.title_short.en">
                </div>
                <div class="form-row">
                  <label>Ort</label>
                  <input type="text" v-model="project.location.en">
                </div>
                <div class="form-row">
                  <label>Beschreibung</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="project.description.en"
                  ></tinymce-editor>
                </div>
                <div class="form-row is-last">
                  <label>Info</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="project.info.en"
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
              :assets="project.images"
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
              :assets="project.documents"
              :assetType="'image'"
              :acceptedFiles="'.pdf'"
              :uploadUrl="'/api/media/upload'"
            ></file-upload>
          </div>
          <form-buttons :route="'projects'"></form-buttons>
        </form>
      </div>
    </main>
  </div>
</template>
<script>
import PageHeader from "@/layout/PageHeader.vue";
import FormButtons from "@/components/global/buttons/FormButtons.vue";
import Tabs from "@/components/global/tabs/Tabs.vue";

import ImageUpload from "@/components/projects/upload/ImageUpload.vue";
import FileUpload from "@/components/projects/upload/FileUpload.vue";
import tinyConfig from "@/config/tinyconfig.js";
import Editor from "@tinymce/tinymce-vue";
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

import projectModel from "@/components/projects/config/model.js";
import projectTabs from "@/components/projects/config/tabs.js";
import projectErrors from "@/components/projects/config/errors.js";

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

      // project validation
      errors: projectErrors,

      // project tabs
      tabs: projectTabs,

      // project model
      project: projectModel,

      // settings
      programs: [],
      states: [],
      authors: [],

      // tinymce config
      tinyConfig: tinyConfig
    };
  },

  created() {
    if (this.$props.type == "edit") {
      let uri = `/api/project/edit/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.project = response.data;
        this.tabs.data.active = true;
      });
    }

    this.axios.get(`/api/settings/program`).then(response => {
      this.programs = response.data;
    });

    this.axios.get(`/api/settings/state`).then(response => {
      this.states = response.data;
    });

    this.axios.get(`/api/settings/authors`).then(response => {
      this.authors = response.data;
    });
  },

  mounted() {
    this.removeErrors();
  },

  methods: {
    // Validation methods
    validate() {
      if (
        this.project.title.de &&
        this.project.title_short.de &&
        this.project.location.de &&
        this.project.year &&
        this.project.images.length > 0
      ) {
        return true;
      }

      if (!this.project.title.de) {
        this.errors.title.de = true;
        this.tabs.data.error = true;
      }

      if (!this.project.title_short.de) {
        this.errors.title_short.de = true;
        this.tabs.data.error = true;
      }

      if (!this.project.location.de) {
        this.errors.location.de = true;
        this.tabs.data.error = true;
      }

      if (!this.project.year) {
        this.errors.year = true;
        this.tabs.data.error = true;
      }

      if (this.project.images.length == 0) {
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
      let uri = "/api/project/create";
      this.axios.post(uri, this.project).then(response => {
        this.$router.push({ name: "projects" });
        this.$notify({ type: "success", text: "Projekt erfasst!" });
      });
    },

    // Update the project
    update() {
      let uri = `/api/project/update/${this.$route.params.id}`;
      this.axios.post(uri, this.project).then(response => {
        this.$router.push({ name: "projects" });
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
        file_response.coords_w = null;
        file_response.coords_h = null;
        file_response.coords_x = null;
        file_response.coords_y = null;
        file_response.order = -1;
        file_response.publish = 1;
        file_response.is_preview_navigation = 0;
        file_response.is_preview_works = 0;
        this.project.images.push(file_response);
      }
    },

    // Delete image by its name
    deleteImage(file, event) {
      if (confirm("Please confirm!")) {
        let uri = `/api/project/image/destroy/${file}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          const index = this.project.images.findIndex(x => x.name === file);
          this.project.images.splice(index, 1);
          this.progress(el);
        });
      }
    },

    // Toggle image status
    toggleImage(asset, event) {
      if (asset.id === null) {
        const index = this.project.images.findIndex(x => x.name === asset.name);
        this.project.images[index].publish = asset.publish == 1 ? 0 : 1;
      } else {
        let uri = `/api/project/image/status/${asset.id}`;
        let el = this.progress(event.target);
        this.axios.get(uri).then(response => {
          const index = this.project.images.findIndex(x => x.id === asset.id);
          this.project.images[index].publish = response.data;
          this.progress(el);
        });
      }
    },

    // Save asset coords
    saveImageCoords(asset) {
      if (asset.id === null) {
        const index = this.project.images.findIndex(x => x.name === asset.name);
        this.project.images[index].coords = asset.coords;
      } 
      else {
        let uri = `/api/project/image/coords/${asset.id}`;
        this.axios.post(uri, asset).then(response => {
          this.$notify({ type: "success", text: "Änderungen gespeichert!" });
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
        this.project.documents.push(file_response);
      }
    },

    // Delete asset by its name
    deleteFile(file, event) {
      if (confirm("Please confirm!")) {
        let uri = `/api/project/document/destroy/${file}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          const index = this.project.documents.findIndex(x => x.name === file);
          this.project.documents.splice(index, 1);
          this.progress(el);
        });
      }
    },

    // Toggle asset status
    toggleFile(asset, event) {
      if (asset.id === null) {
        const index = this.project.documents.findIndex(x => x.name === asset.name);
        this.project.documents[index].publish = asset.publish == 1 ? 0 : 1;
      } else {
        let uri = `/api/project/document/status/${asset.id}`;
        let el = this.progress(event.target);
        this.axios.get(uri).then(response => {
          const index = this.project.documents.findIndex(x => x.id === asset.id);
          this.project.documents[index].publish = response.data;
          this.progress(el);
        });
      }
    }
  },

  computed: {
    title: function() {
      return this.$props.type == "edit"
        ? "Projekt bearbeiten"
        : "Projekt hinzufügen";
    }
  }
};
</script>