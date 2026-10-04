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
                  <rich-text v-model="contact.address.de" class="is-tall"></rich-text>
                </div>
                <div class="form-row">
                  <label>Google Maps URL</label>
                  <input type="text" v-model="contact.google_maps_url" class="form-control">
                </div>
                <div class="form-row">
                  <label>Kontakte</label>
                  <rich-text v-model="contact.contacts.de" class="is-tall"></rich-text>
                </div>
                <div class="form-row">
                  <label>Info</label>
                  <rich-text v-model="contact.info.de" class="is-tall"></rich-text>
                </div>
                <div class="form-row">
                  <label>Impressum</label>
                  <rich-text v-model="contact.imprint.de" class="is-tall"></rich-text>
                </div>
              </div>
            </div>
          </div>
          <div v-show="tabs.translation.active">
            <div class="grid-main-sidebar">
              <div class="column-main">
                <div class="form-row">
                  <label>Adresse</label>
                  <rich-text v-model="contact.address.en" class="is-tall"></rich-text>
                </div>
                <div class="form-row">
                  <label>Kontakte</label>
                  <rich-text v-model="contact.contacts.en" class="is-tall"></rich-text>
                </div>
                <div class="form-row">
                  <label>Info</label>
                  <rich-text v-model="contact.info.en" class="is-tall"></rich-text>
                </div>
                <div class="form-row">
                  <label>Impressum</label>
                  <rich-text v-model="contact.imprint.en" class="is-tall"></rich-text>
                </div>
              </div>
            </div>
          </div>
          <form-footer :route="'contact'"></form-footer>
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
import RichText from "@/components/global/editor/Editor.vue";

// Utils
import Utils from "@/mixins/utils";
import Progress from "@/mixins/progress";

// config
import contactTabs from "@/components/contact/config/tabs.js";
import contactErrors from "@/components/contact/config/errors.js";

export default {
  components: {
    FormFooter,
    RichText,
    Tabs,
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
      contact: {
        address: {
          de: null,
          en: null,
        },
        google_maps_url: null,
        contacts: {
          de: null,
          en: null,
        },
        info: {
          de: null,
          en: null,
        },
        imprint: {
          de: null,
          en: null,
        },
      },
    };
  },

  created() {
    if (this.$props.type == "edit") {
      let uri = `/api/contact/edit/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.contact = response.data;
        if (!this.contact.contacts) {
          this.contact.contacts = { de: null, en: null };
        }
        this.tabs.data.active = true;
      });

      // set editor height
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