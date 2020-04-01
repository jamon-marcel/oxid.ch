<template>
  <div class="container">
    <notifications classes="notification"/>
    <main class="content" role="main">
      <div>
        <h1>{{title}}</h1>
        <nav class="tabs">
          <ul>
            <li>
              <a
                href="javascript:;"
                @click="changeTab('data')"
                :class="[tabs.data.active ? 'is-active' : '', tabs.data.error ? 'has-error' : '']"
              >Daten</a>
            </li>
            <li>
              <a
                href="javascript:;"
                @click="changeTab('translation')"
                :class="[tabs.translation.active ? 'is-active' : '', tabs.translation.error ? 'has-error' : '']"
              >Übersetzung</a>
            </li>
            <li>
              <a
                href="javascript:;"
                @click="changeTab('images')"
                :class="[tabs.images.active ? 'is-active' : '', tabs.images.error ? 'has-error' : '']"
              >Bilder</a>
            </li>
            <li>
              <a
                href="javascript:;"
                @click="changeTab('files')"
                :class="[tabs.files.active ? 'is-active' : '', tabs.files.error ? 'has-error' : '']"
              >Dokumente</a>
            </li>
          </ul>
        </nav>
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
                  <input type="text" @focus="removeError('title_short', 'de')" v-model="project.title_short.de">
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row" :class="errors.location.de ? 'has-error': ''">
                  <label>Ort *</label>
                  <input type="text" @focus="removeError('location', 'de')" v-model="project.location.de">
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row" :class="errors.year.de ? 'has-error': ''">
                  <label>Jahr *</label>
                  <input type="text" @focus="removeError('year', 'de')" v-model="project.year.de">
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
                <div class="form-row is-last">
                  <label>Info</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="project.info.de"
                  ></tinymce-editor>
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
                  <label>Ort *</label>
                  <input type="text" v-model="project.location.en">
                </div>
                <div class="form-row">
                  <label>Jahr *</label>
                  <input type="text" v-model="project.year.en">
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
              <project-image-upload
                :labelNew="'Upload images'"
                :labelExisting="'Existing images'"
                :labelRestrictions="'jpg, png | max. 8 MB'"
                :maxFiles="99"
                :maxFilesize="8"
                :assets="project.images"
                :assetType="'image'"
                :acceptedFiles="'.png,.jpg'"
                :uploadUrl="'/api/media/upload'"
              ></project-image-upload>
          </div>
          <div v-show="tabs.files.active">

          </div>
          <form-buttons :route="'news'"></form-buttons>
        </form>
      </div>
    </main>
  </div>
</template>
<script>
import PageHeader from "@/layout/PageHeader.vue";
import FormButtons from "@/components/global/buttons/FormButtons.vue";
import ProjectImageUpload from "@/components/global/upload/ProjectImageUpload.vue";
import tinyConfig from "@/config/tinyconfig.js";
import Editor from "@tinymce/tinymce-vue";
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

export default {
  components: {
    FormButtons: FormButtons,
    tinymceEditor: Editor,
    ProjectImageUpload: ProjectImageUpload,
  },

  props: {
    type: String
  },

  mixins: [Utils, Progress],

  data() {
    return {

      // validation
      errors: {
        title: {
          de: false
        },
        title_short: {
          de: false,
        },
        location: {
          de: false,
        },
        year: {
          de: false,
        },
      },

      // tabs
      tabs: {
        data: {
          active: true,
          error: false
        },
        translation: {
          active: false,
          error: false
        },
        images: {
          active: false,
          error: false,
        },
        files: {
          active: false,
          error: false
        }
      },

      // model
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
        year: {
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

        images: [],

        is_filter_wood: 0,
        is_filter_reuse: 0,
        is_highlight: 0,
        has_detail: 0,
        publish: 0,
        program: 1,
        state: 1,
        author: 1,
      },

      // setttings
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

  methods: {
    // Validation methods
    validate() {
      
      if (this.project.title.de) {
        return true;
      }

      if (!this.project.title.de) {
        this.errors.title.de = true;
        this.tabs.data.error = true;
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
      this.axios.post(uri, this.news).then(response => {
        this.$router.push({ name: "projects" });
        this.$notify({ type: "success", text: "Projekt erfasst!" });
      });
    },

    // Update the project
    update() {
      let uri = `/api/project/update/${this.$route.params.id}`;
      this.axios.post(uri, this.project).then(response => {
        this.$router.push({ name: "project" });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
      });
    },

    // Upload & asset methods
    uploadComplete(file) {
      if (file.status == "error" && file.accepted == false) {
        this.$notify({ type: "error", text: "Invalid format or file to big!" });
      } 
      else {
        let file_response = JSON.parse(file.xhr.response);
        file_response.id = null;
        file_response.caption = {de: null, en: null};
        file_response.order = -1;
        file_response.publish = 1;
        this.project.images.push(file_response);
      }
    },

    // Delete a single file by its name
    deleteUpload(file, event) {
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

    toggleAsset(asset, event) {
      if (asset.id === null) {
        const index = this.project.images.findIndex(x => x.name === asset.name);
        this.project.images[index].publish = asset.publish == 1 ? 0 : 1;
      } 
      else {
        let uri = `/api/project/image/status/${asset.id}`;
        let el = this.progress(event.target);
        this.axios.get(uri).then(response => {
          const index = this.project.images.findIndex(x => x.id === asset.id);
          this.project.images[index].publish = response.data;
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