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
                  <input type="text" @focus="removeError('title', 'de')" v-model="job.title.de">
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
                    v-model="job.description.de"
                  ></tinymce-editor>
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div
                  class="form-row is-last"
                  :class="errors.info.de ? 'has-error': ''"
                  @mouseenter="removeError('info', 'de')"
                >
                  <label>Info *</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="job.info.de"
                  ></tinymce-editor>
                  <div class="is-required">Pflichtfeld</div>
                </div>
              </div>
              <div class="column-sidebar">
                <div>
                  <div class="form-row is-sm is-last">
                    <label class="is-sm">Publizieren?</label>
                    <div class="form-radio">
                      <input
                        v-model="job.publish"
                        type="radio"
                        name="publish_1"
                        id="publish_1"
                        value="1"
                        class="visually-hidden"
                      >
                      <label for="publish_1" class="form-control">Ja</label>
                      <input
                        v-model="job.publish"
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
                  <input type="text" v-model="job.title.en">
                </div>
                <div class="form-row">
                  <label>Beschreibung</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="job.description.en"
                  ></tinymce-editor>
                </div>
                <div class="form-row is-last">
                  <label>Info</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="job.info.en"
                  ></tinymce-editor>
                </div>
              </div>
            </div>
          </div>
          <div v-show="tabs.files.active">
            <file-upload
              :labelNew="'Upload Dokumente'"
              :labelExisting="'Existierende Dokumente'"
              :labelRestrictions="'pdf | max. 8 MB'"
              :maxFiles="99"
              :maxFilesize="8"
              :assets="job.documents"
              :assetType="'file'"
              :acceptedFiles="'.pdf'"
              :uploadUrl="'/api/media/upload'"
            ></file-upload>
          </div>
          <form-buttons :route="'jobs'"></form-buttons>
        </form>
      </div>
    </main>
  </div>
</template>
<script>
import PageHeader from "@/layout/PageHeader.vue";
import FormButtons from "@/components/global/buttons/FormButtons.vue";
import Tabs from "@/components/global/tabs/Tabs.vue";

import FileUpload from "@/components/jobs/upload/FileUpload.vue";
import tinyConfig from "@/config/tinyconfig.js";
import Editor from "@tinymce/tinymce-vue";
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

import jobModel from "@/components/jobs/config/model.js";
import jobTabs from "@/components/jobs/config/tabs.js";
import jobErrors from "@/components/jobs/config/errors.js";

export default {
  components: {
    FormButtons: FormButtons,
    tinymceEditor: Editor,
    FileUpload: FileUpload,
    Tabs: Tabs
  },

  props: {
    type: String
  },

  mixins: [Utils, Progress],

  data() {
    return {
      // job validation
      errors: jobErrors,

      // job tabs
      tabs: jobTabs,

      // job model
      job: jobModel,

      // settings
      categories: [],

      // tinymce config
      tinyConfig: tinyConfig
    };
  },

  created() {
    if (this.$props.type == "edit") {
      let uri = `/api/job/edit/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.job = response.data;
        this.tabs.data.active = true;
      });
    }
  },

  mounted() {
    this.removeErrors();
  },

  methods: {
    // Validation methods
    validate() {
      if (this.job.title.de && this.job.description.de && this.job.info.de) {
        return true;
      }

      if (!this.job.title.de) {
        this.errors.title.de = true;
        this.tabs.data.error = true;
      }

      if (!this.job.description.de) {
        this.errors.description.de = true;
        this.tabs.data.error = true;
      }

      if (!this.job.info.de) {
        this.errors.info.de = true;
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
      let uri = "/api/job/create";
      this.axios.post(uri, this.job).then(response => {
        this.$router.push({ name: "jobs" });
        this.$notify({ type: "success", text: "Diskurs erfasst!" });
      });
    },

    // Update the project
    update() {
      let uri = `/api/job/update/${this.$route.params.id}`;
      this.axios.post(uri, this.job).then(response => {
        this.$router.push({ name: "jobs" });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
      });
    },

    // Upload file callback
    uploadFile(file) {
      if (file.status == "error" && file.accepted == false) {
        this.$notify({ type: "error", text: "Invalid format or file to big!" });
      } else {
        let file_response = JSON.parse(file.xhr.response);
        file_response.id = null;
        file_response.caption = { de: null, en: null };
        file_response.publish = 1;
        this.job.documents.push(file_response);
      }
    },

    // Delete file by its name
    deleteFile(file, event) {
      if (confirm("Please confirm!")) {
        let uri = `/api/job/document/destroy/${file}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          const index = this.job.documents.findIndex(x => x.name === file);
          this.job.documents.splice(index, 1);
          this.progress(el);
        });
      }
    },

    // Toggle file status
    toggleFile(asset, event) {
      if (asset.id === null) {
        const index = this.job.documents.findIndex(x => x.name === asset.name);
        this.job.documents[index].publish = asset.publish == 1 ? 0 : 1;
      } else {
        let uri = `/api/job/document/status/${asset.id}`;
        let el = this.progress(event.target);
        this.axios.get(uri).then(response => {
          const index = this.job.documents.findIndex(x => x.id === asset.id);
          this.job.documents[index].publish = response.data;
          this.progress(el);
        });
      }
    }
  },

  computed: {
    title: function() {
      return this.$props.type == "edit" ? "Job bearbeiten" : "Job hinzufügen";
    }
  }
};
</script>