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
                  <input type="text" @focus="removeError('title', 'de')" v-model="profile.title.de">
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row">
                  <label>Text</label>
                  <tinymce-editor
                    :api-key="tinyApiKey"
                    :init="tinyConfig"
                    v-model="profile.description.de"
                  ></tinymce-editor>
                </div>
              </div>
              <div class="column-sidebar">
                <div>
                  <div class="form-row is-sm is-last">
                    <label class="is-sm">Publizieren?</label>
                    <div class="form-radio">
                      <input
                        v-model="profile.publish"
                        type="radio"
                        name="publish_1"
                        id="publish_1"
                        value="1"
                        class="visually-hidden"
                      >
                      <label for="publish_1" class="form-control">Ja</label>
                      <input
                        v-model="profile.publish"
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
                  <input type="text" v-model="profile.title.en">
                </div>
                <div class="form-row">
                  <label>Text *</label>
                  <tinymce-editor
                    :api-key="tinyApiKey"
                    :init="tinyConfig"
                    v-model="profile.description.en"
                  ></tinymce-editor>
                </div>
              </div>
            </div>
          </div>
          <form-footer :route="'profile'"></form-footer>
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

// Editor
import TinymceEditor from "@tinymce/tinymce-vue";
import tinyConfig from "@/config/tinyconfig.js";

// Mixins
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

// Config
import profileModel from "@/components/profile/text/config/model.js";
import profileTabs from "@/components/profile/text/config/tabs.js";
import profileErrors from "@/components/profile/text/config/errors.js";

export default {
  components: {
    FormFooter,
    TinymceEditor,
    Tabs,
  },

  props: {
    type: String
  },

  mixins: [Utils, Progress],

  data() {
    return {

      // profile validation
      errors: profileErrors,

      // profile tabs
      tabs: profileTabs,

      // profile model
      profile: profileModel,

      // tinymce config
      tinyConfig: tinyConfig,
      tinyApiKey: 'vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro',
    };
  },

  created() {
    if (this.$props.type == "edit") {
      let uri = `/api/profile/edit/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.profile = response.data;
        this.tabs.data.active = true;
      });

      // set editor height
      this.tinyConfig.height = '480px';
    }
  },

  mounted() {
    this.removeErrors();
  },

  methods: {
    // Validation methods
    validate() {
      if (this.profile.title.de) {
        return true;
      }

      if (!this.profile.title.de) {
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
      let uri = "/api/profile/create";
      this.axios.post(uri, this.profile).then(response => {
        this.$router.push({ name: "profile" });
        this.$notify({ type: "success", text: "Text erfasst!" });
      });
    },

    // Update the project
    update() {
      let uri = `/api/profile/update/${this.$route.params.id}`;
      this.axios.post(uri, this.profile).then(response => {
        this.$router.push({ name: "profile" });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
      });
    },
  },

  computed: {
    title: function() {
      return this.$props.type == "edit"
        ? "Profil bearbeiten"
        : "Profil hinzufügen";
    }
  }
};
</script>