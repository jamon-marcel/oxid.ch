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
                <div class="form-row" :class="errors.firstname ? 'has-error': ''">
                  <label>Vorname *</label>
                  <input type="text" @focus="removeError('firstname')" v-model="team.firstname">
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row" :class="errors.name ? 'has-error': ''">
                  <label>Name *</label>
                  <input
                    type="text"
                    @focus="removeError('name')"
                    v-model="team.name"
                  >
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row">
                  <label>Funktion</label>
                  <input
                    type="text"
                    v-model="team.role.de"
                  >
                </div>
                <div class="form-row">
                  <label>Position</label>
                  <input
                    type="text"
                    v-model="team.position.de"
                  >
                </div>
                <div class="form-row">
                  <label>E-Mail</label>
                  <input
                    type="text"
                    v-model="team.email"
                  >
                </div>
              </div>
              <div class="column-sidebar">
                <div>
                  <div class="form-row is-sm">
                    <label>Kategorie</label>
                    <div class="select-wrapper is-sidebar">
                      <select v-model="team.category" name="category">
                        <option v-for="(c, index) in categories" :key="index" :value="index">{{ c }}</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-row is-sm is-last">
                    <label class="is-sm">Publizieren?</label>
                    <div class="form-radio">
                      <input
                        v-model="team.publish"
                        type="radio"
                        name="publish_1"
                        id="publish_1"
                        value="1"
                        class="visually-hidden"
                      >
                      <label for="publish_1" class="form-control">Ja</label>
                      <input
                        v-model="team.publish"
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
                  <label>Funktion</label>
                  <input type="text" v-model="team.role.en">
                </div>
                <div class="form-row">
                  <label>Position</label>
                  <input type="text" v-model="team.position.en">
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
                :files="team.documents"
              ></file-listing>
            </div>
          </div>
          <form-footer :route="'team'"></form-footer>
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
import FileListing from "@/components/team/files/Listing.vue";

// Mixins
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

// Config
import teamTabs from "@/components/team/team/config/tabs.js";
import teamErrors from "@/components/team/team/config/errors.js";

export default {
  components: {
    FormFooter,
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

      // team validation
      errors: teamErrors,

      // team tabs
      tabs: teamTabs,

      // team model
      team: {
        firstname: null,
        name: null,
        email: null,
        role: {
          de: null,
          en: null,
        },
        position: {
          de: null,
          en: null,
        },
        documents: [],
        publish: 0,
        category: 1,
      },

      // settings
      categories: [],
    };
  },

  created() {
    if (this.$props.type == "edit") {
      let uri = `/api/team/edit/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.team = response.data;
        this.tabs.data.active = true;
      });
    }

    this.axios.get(`/api/settings/teamCategories`).then(response => {
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
        this.team.firstname &&
        this.team.name
      ) {
        return true;
      }

      if (!this.team.firstname) {
        this.errors.firstname = true;
        this.tabs.data.error = true;
      }

      if (!this.team.name) {
        this.errors.name = true;
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
      let uri = "/api/team/create";
      this.axios.post(uri, this.team).then(response => {
        this.$router.push({ name: "team" });
        this.$notify({ type: "success", text: "Diskurs erfasst!" });
      });
    },

    // Update the project
    update() {
      let uri = `/api/team/update/${this.$route.params.id}`;
      this.axios.post(uri, this.team).then(response => {
        this.$router.push({ name: "team" });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
      });
    },

    // Store uploaded file
    storeFile(upload) {
      let file = {
        id: null,
        name: upload.name,
        caption: { de: null, en: null },
        language: 0,
        publish: 1,
      }
      this.team.documents.push(file);
    },

    // Delete by name
    destroyFile(file, event) {
      if (confirm("Please confirm!")) {
        let uri = `/api/team/document/destroy/${file}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          const index = this.team.documents.findIndex(x => x.name === file);
          this.team.documents.splice(index, 1);
          this.progress(el);
        });
      }
    },

    // Toggle status
    toggleFile(file, event) {
      if (file.id === null) {
        const index = this.team.documents.findIndex(x => x.name === file.name);
        this.team.documents[index].publish = file.publish == 1 ? 0 : 1;
      } else {
        let uri = `/api/team/document/status/${file.id}`;
        let el = this.progress(event.target);
        this.axios.get(uri).then(response => {
          const index = this.team.documents.findIndex(x => x.id === file.id);
          this.team.documents[index].publish = response.data;
          this.progress(el);
        });
      }
    },
  },

  computed: {
    title: function() {
      return this.$props.type == "edit"
        ? "Teammitglied bearbeiten"
        : "Teammitglied hinzufügen";
    }
  }
};
</script>