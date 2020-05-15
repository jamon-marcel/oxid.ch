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
                    :api-key="tinyApiKey"
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
                    :api-key="tinyApiKey"
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
                    :api-key="tinyApiKey"
                    :init="tinyConfig"
                    v-model="job.description.en"
                  ></tinymce-editor>
                </div>
                <div class="form-row is-last">
                  <label>Info</label>
                  <tinymce-editor
                    :api-key="tinyApiKey"
                    :init="tinyConfig"
                    v-model="job.info.en"
                  ></tinymce-editor>
                </div>
              </div>
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
            <div class="form-row">
              <file-listing 
                :files="job.documents"
              ></file-listing>
            </div>
          </div>
          <form-footer :route="'jobs'"></form-footer>
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
import FileUpload from "@/components/global/files/Upload.vue";
import FileListing from "@/components/global/files/Listing.vue";

// TinyMCE
import tinyConfig from "@/config/tinyconfig.js";
import TinymceEditor from "@tinymce/tinymce-vue";

// Mixins
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

// Config
import jobTabs from "@/components/jobs/config/tabs.js";
import jobErrors from "@/components/jobs/config/errors.js";

export default {
  components: {
    FormFooter,
    TinymceEditor,
    FileUpload,
    FileListing,
    Tabs
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
      job: {
        title: {
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
        documents: [],
        publish: 0,
      },

      // settings
      categories: [],

      // TinyMCE
      tinyConfig: tinyConfig,
      tinyApiKey: 'vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro',
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

    // Store uploaded file
    storeFile(upload) {
      let file = {
        id: null,
        name: upload.name,
        caption: { de: null, en: null },
        publish: 1,
      }
      this.job.documents.push(file);
    },

    // Delete by name
    destroyFile(file, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/job/document/destroy/${file}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          const index = this.job.documents.findIndex(x => x.name === file);
          this.job.documents.splice(index, 1);
          this.progress(el);
        });
      }
    },

    // Toggle status
    toggleFile(file, event) {
      if (file.id === null) {
        const index = this.job.documents.findIndex(x => x.name === file.name);
        this.job.documents[index].publish = file.publish == 1 ? 0 : 1;
      } else {
        let uri = `/api/job/document/status/${file.id}`;
        let el = this.progress(event.target);
        this.axios.get(uri).then(response => {
          const index = this.job.documents.findIndex(x => x.id === file.id);
          this.job.documents[index].publish = response.data;
          this.progress(el);
        });
      }
    },
  },

  computed: {
    title: function() {
      return this.$props.type == "edit" ? "Job bearbeiten" : "Job hinzufügen";
    }
  }
};
</script>