<template>
  <div class="contactiner">
    <notifications classes="notification"/>
    <main class="content" role="main">
      <div>
        <h1>{{title}}</h1>
        <tabs :tabs="tabs" :errors="errors"></tabs>
        <form @submit.prevent="submit">
          <div v-show="tabs.data.active">
            <div class="grid-main-sidebar">
              <div class="column-main">
                <div class="form-row">
                  <label>Adresse</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="contact.address.de"
                  ></tinymce-editor>
                </div>
                <div class="form-row">
                  <label>Info</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="contact.info.de"
                  ></tinymce-editor>
                </div>
                <div class="form-row">
                  <label>Impressum</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="contact.imprint.de"
                  ></tinymce-editor>
                </div>
              </div>
            </div>
          </div>
          <div v-show="tabs.translation.active">
            <div class="grid-main-sidebar">
              <div class="column-main">
                <div class="form-row">
                  <label>Adresse</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="contact.address.en"
                  ></tinymce-editor>
                </div>
                <div class="form-row">
                  <label>Info</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="contact.info.en"
                  ></tinymce-editor>
                </div>
                <div class="form-row">
                  <label>Impressum</label>
                  <tinymce-editor
                    api-key="vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro"
                    :init="tinyConfig"
                    v-model="contact.imprint.en"
                  ></tinymce-editor>
                </div>
              </div>
            </div>
          </div>
          <form-buttons :route="'contact'"></form-buttons>
        </form>
      </div>
    </main>
  </div>
</template>
<script>
import PageHeader from "@/layout/PageHeader.vue";
import FormButtons from "@/components/global/buttons/FormButtons.vue";
import Tabs from "@/components/global/tabs/Tabs.vue";

import Editor from "@tinymce/tinymce-vue";
import tinyConfig from "@/config/tinyconfig.js";

import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

import contactModel from "@/components/contact/config/model.js";
import contactTabs from "@/components/contact/config/tabs.js";
import contactErrors from "@/components/contact/config/errors.js";

export default {
  components: {
    FormButtons: FormButtons,
    tinymceEditor: Editor,
    Tabs: Tabs,
  },

  props: {
    type: String
  },

  mixins: [Utils, Progress],

  data() {
    return {

      // contact validation
      errors: contactErrors,

      // contact tabs
      tabs: contactTabs,

      // contact model
      contact: contactModel,

      // tinymce config
      tinyConfig: tinyConfig
    };
  },

  created() {
    if (this.$props.type == "edit") {
      let uri = `/api/contact/edit/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.contact = response.data;
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

    // Submit form
    submit() {

      if (this.$props.type == "edit") {
        this.update();
      }

      if (this.$props.type == "create") {
        this.store();
      }
    },

    // Store the project
    store() {
      let uri = "/api/contact/create";
      this.axios.post(uri, this.contact).then(response => {
        this.$router.push({ name: "contact" });
        this.$notify({ type: "success", text: "Text erfasst!" });
      });
    },

    // Update the project
    update() {
      let uri = `/api/contact/update/${this.$route.params.id}`;
      this.axios.post(uri, this.contact).then(response => {
        this.$router.push({ name: "contact" });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
      });
    },
  },

  computed: {
    title: function() {
      return this.$props.type == "edit"
        ? "Kontakt bearbeiten"
        : "Kontakt hinzufügen";
    }
  }
};
</script>