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
                  <input type="text" @focus="removeError('title', 'de')" v-model="news.title.de">
                  <div class="is-required">Pflichtfeld</div>
                </div>
                <div class="form-row">
                  <label>Subtitel</label>
                  <input type="text" v-model="news.subtitle.de">
                </div>
                <div class="form-row is-last">
                  <label>Text</label>
                  <rich-text v-model="news.text.de"></rich-text>
                </div>
              </div>
              <div class="column-sidebar">
                <div>
                  <div class="form-row is-sm">
                    <label class="is-sm">Publizieren?</label>
                    <div class="form-radio">
                      <input
                        v-model="news.publish"
                        type="radio"
                        name="publish_1"
                        id="publish_1"
                        value="1"
                        class="visually-hidden"
                      >
                      <label for="publish_1" class="form-control">Ja</label>
                      <input
                        v-model="news.publish"
                        type="radio"
                        name="publish_0"
                        id="publish_0"
                        value="0"
                        class="visually-hidden"
                      >
                      <label for="publish_0" class="form-control">Nein</label>
                    </div>
                  </div>
                  <div class="form-row is-sm is-last">
                    <label class="is-sm">Publizieren bis</label>
                    <input
                      v-maska="'##.##.####'"
                      class="is-light"
                      v-model="news.date_end"
                      type="text"
                      placeholder="z.B. 01.06.2020"
                    >
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
                  <input type="text" v-model="news.title.en">
                </div>
                <div class="form-row">
                  <label>Subtitel</label>
                  <input type="text" v-model="news.subtitle.en">
                </div>
                <div class="form-row is-last">
                  <label>Text</label>
                  <rich-text v-model="news.text.en"></rich-text>
                </div>
              </div>
            </div>
          </div>
          <form-footer :route="'news'"></form-footer>
        </form>
      </div>
    </main>
  </div>
</template>
<script>
import PageHeader from "@/layout/PageHeader.vue";
import FormFooter from "@/components/global/form/Footer.vue";
import Tabs from "@/components/global/tabs/Tabs.vue";

import { vMaska } from "maska/vue";
import RichText from "@/components/global/editor/Editor.vue";
import Utils from "@/mixins/utils";
import { formatDate } from "@/utils/date";
import Progress from "@/mixins/progress";

import newsModel from "@/components/home/news/config/model.js";
import newsTabs from "@/components/home/news/config/tabs.js";
import newsErrors from "@/components/home/news/config/errors.js";

export default {
  components: {
    FormFooter,
    Tabs: Tabs,
    RichText,
  },

  directives: {
    maska: vMaska
  },

  props: {
    type: String
  },

  mixins: [Utils, Progress],

  data() {
    return {

      // validation
      errors: newsErrors,

      // tabs
      tabs: newsTabs,

      // model
      news: newsModel,
    };
  },

  created() {
    if (this.$props.type == "edit") {
      let uri = `/api/news/edit/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.news = response.data;
        this.news.date_end = formatDate(this.news.date_end);
      });
    }
  },

  methods: {
    // Validation methods
    validate() {
      
      if (this.news.title.de) {
        return true;
      }

      if (!this.news.title.de) {
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

    // Store the news
    store() {
      let uri = "/api/news/create";
      this.axios.post(uri, this.news).then(response => {
        this.$router.push({ name: "news" });
        this.$notify({ type: "success", text: "News erfasst!" });
      });
    },

    // Update the news
    update() {
      let uri = `/api/news/update/${this.$route.params.id}`;
      this.axios.post(uri, this.news).then(response => {
        this.$router.push({ name: "news" });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
      });
    }
  },

  computed: {
    title: function() {
      return this.$props.type == "edit" 
      ? "News bearbeiten" 
      : "News hinzufügen";
    }
  }
};
</script>