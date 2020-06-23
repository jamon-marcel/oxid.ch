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
                    :api-key="tinyApiKey"
                    :init="tinyConfig"
                    v-model="project.description.de"
                  ></tinymce-editor>
                </div>
                <div class="form-row">
                  <label>Info</label>
                  <tinymce-editor
                    :api-key="tinyApiKey"
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
                  <label>Auftrag</label>
                  <input type="text" v-model="project.client_works">
                </div>
                <div class="form-row">
                  <label>Bauherrschaft</label>
                  <input type="text" v-model="project.principal_works">
                </div>
                <div class="form-row is-last">
                  <label>Autorenschaft</label>
                  <input type="text" v-model="project.author_works">
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
                    :api-key="tinyApiKey"
                    :init="tinyConfig"
                    v-model="project.description.en"
                  ></tinymce-editor>
                </div>
                <div class="form-row is-last">
                  <label>Info</label>
                  <tinymce-editor
                    :api-key="tinyApiKey"
                    :init="tinyConfig"
                    v-model="project.info.en"
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
            <div class="form-row" v-if="project.images.length">
              <image-listing 
                :images="project.images"
              ></image-listing>
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
            <div class="form-row" v-if="project.documents.length">
              <file-listing 
                :files="project.documents"
              ></file-listing>
            </div>
          </div>
          <form-footer :route="'projects'"></form-footer>
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
import ImageListing from "@/components/projects/images/Listing.vue";
import FileUpload from "@/components/global/files/Upload.vue";
import FileListing from "@/components/global/files/Listing.vue";

// TinyMCE
import tinyConfig from "@/config/tinyconfig.js";
import TinymceEditor from "@tinymce/tinymce-vue";

// Mixins
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

// Config
import projectTabs from "@/components/projects/config/tabs.js";
import projectErrors from "@/components/projects/config/errors.js";

export default {
  components: {
    FormFooter,
    TinymceEditor,
    FileUpload,
    FileListing,
    ImageUpload,
    ImageListing,
    Tabs,
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
      project: {
        title: {
          de: null,
          en: null,
        },
        title_short: {
          de: null,
          en: null,
        },
        location: {
          de: null,
          en: null,
        },
        description: {
          de: null,
          en: null
        },
        info: {
          de: null,
          en: null,
        },

        year: null,
        year_works: null,
        client_works: null,
        principal_works: null,
        author_works: null,
        images: [],
        documents: [],
        is_filter_wood: 0,
        is_filter_reuse: 0,
        is_filter_area: 0,
        is_highlight: 0,
        has_detail: 0,
        publish: 0,
        program: 1,
        state: 1,
        author: 1,
      },

      // settings
      programs: [],
      states: [],
      authors: [],

      // TinyMCE
      tinyConfig: tinyConfig,
      tinyApiKey: 'vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro',
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

    // Store uploaded image
    storeImage(upload) {
      let image = {
        id: null,
        name: upload.name,
        caption: { de: null, en: null },
        is_preview_navigation: 0,
        is_preview_works: 0,
        is_plan: 0,
        coords_w: 0,
        coords_h: 0,
        coords_x: 0,
        coords_y: 0,
        orientation: upload.orientation,
        order: -1,
        publish: 1,
      }
      this.project.images.push(image);
    },

    // Delete by name
    destroyImage(image, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/project/image/destroy/${image}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          const index = this.project.images.findIndex(x => x.name === image);
          this.project.images.splice(index, 1);
          this.progress(el);
        });
      }
    },

    // Toggle image status
    toggleImage(image, event) {
      if (image.id === null) {
        const index = this.project.images.findIndex(x => x.name === image.name);
        this.project.images[index].publish = image.publish == 1 ? 0 : 1;
      } else {
        let uri = `/api/project/image/status/${image.id}`;
        let el = this.progress(event.target);
        this.axios.get(uri).then(response => {
          const index = this.project.images.findIndex(x => x.id === image.id);
          this.project.images[index].publish = response.data;
          this.progress(el);
        });
      }
    },

    // Save image coords
    saveImageCoords(image) {
      if (image.id === null) {
        const index = this.project.images.findIndex(x => x.name === image.name);
        this.project.images[index].coords = image.coords;
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
      } 
      else {
        let uri = `/api/project/image/coords/${image.id}`;
        this.axios.post(uri, image).then(response => {
          this.$notify({ type: "success", text: "Änderungen gespeichert!" });
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
      this.project.documents.push(file);
    },

    // Delete by name
    destroyFile(file, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/project/document/destroy/${file}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          const index = this.project.documents.findIndex(x => x.name === file);
          this.project.documents.splice(index, 1);
          this.progress(el);
        });
      }
    },

    // Toggle file status
    toggleFile(file, event) {
      if (file.id === null) {
        const index = this.project.documents.findIndex(x => x.name === file.name);
        this.project.documents[index].publish = file.publish == 1 ? 0 : 1;
      } else {
        let uri = `/api/project/document/status/${file.id}`;
        let el = this.progress(event.target);
        this.axios.get(uri).then(response => {
          const index = this.project.documents.findIndex(x => x.id === file.id);
          this.project.documents[index].publish = response.data;
          this.progress(el);
        });
      }
    },

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