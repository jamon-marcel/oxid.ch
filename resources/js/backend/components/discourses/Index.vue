<template>
  <div>
    <page-header/>
    <notifications classes="notification"/>
    <div class="container">
      <main class="content" role="main">
        <div>
          <h1>Diskurs</h1>
          <router-link :to="{ name: 'discourse-create' }" class="btn-add">
            <span>Hinzufügen</span>
          </router-link>
          <div class="list-items is-grouped" v-if="discourses">
            <div
              v-for="(discourse, index) in discourses"
              :key="index"
              :class="[hasFilter && filter != index ? 'is-hidden' : '', '']"
            >
              <h3 class="list-item-header">{{categories[index]}}</h3>
              <draggable
                :disabled="false"
                v-model="discourses[index]"
                @end="order(index)"
                ghost-class="draggable-ghost"
                draggable=".list-item"
              >
                <div
                  :class="[d.publish == 0 ? 'is-disabled' : '', 'list-item is-draggable']"
                  v-for="d in discourse"
                  :key="d.id"
                  data-icons="3"
                >
                  <div
                    class="list-item-body"
                  >{{ d.title.de }} - ({{ d.heading.de }}, {{ d.date.de }})</div>
                  <div class="list-item-action" data-icons="3">
                    <a
                      href="javascript:;"
                      :class="[d.publish == 1 ? 'icon-eye' : 'icon-eye-off', 'icon-mini']"
                      @click.prevent="toggle(d.id,$event)"
                    ></a>
                    <router-link
                      :to="{name: 'discourse-edit', params: { id: d.id }}"
                      class="icon-edit icon-mini"
                    ></router-link>
                    <a
                      href="javascript:;"
                      class="icon-trash icon-mini"
                      @click.prevent="destroy(d.id,$event)"
                    ></a>
                  </div>
                </div>
              </draggable>
            </div>
          </div>
          <div v-else>
            <p>Es sind noch keine Diskurs-Einträge vorhanden...</p>
          </div>
          <footer class="site-footer">
            <div>
              <div class="filter">
                <button type="button" class="btn-filter" @click="filterResults(1)">Recherche</button>
                <button type="button" class="btn-filter" @click="filterResults(2)">Veranstaltungen</button>
                <button type="button" class="btn-filter" @click="filterResults(3)">Publikationen</button>
              </div>
            </div>
          </footer>
        </div>
      </main>
    </div>
  </div>
</template>
<script>
import PageHeader from "@/layout/PageHeader.vue";
import draggable from "vuedraggable";
import Progress from "@/mixins/progress";

export default {
  components: {
    draggable,
    PageHeader: PageHeader
  },

  mixins: [Progress],

  data() {
    return {
      discourses: [],
      categories: [],
      filter: null,
      hasFilter: false
    };
  },

  created() {
    this.fetch();
  },

  methods: {
    fetch() {
      this.axios.get(`/api/discourses/get`).then(response => {
        this.discourses = response.data;
      });
      this.axios.get(`/api/settings/discourseCategories`).then(response => {
        this.categories = response.data;
      });
    },

    destroy(id, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/discourse/destroy/${id}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          this.fetch();
          this.$notify({ type: "success", text: "Eintrag gelöscht" });
          this.progress(el);
        });
      }
    },

    toggle(id, event) {
      let el = this.progress(event.target),
        discourses = this.discourses;
      this.axios.get(`/api/discourse/status/${id}`).then(response => {
        Object.keys(discourses).forEach(function(key) {
          const index = discourses[key].findIndex(x => x.id === id);
          if (index > -1) {
            discourses[key][index].publish = response.data;
          }
        });
        this.discourses = discourses;
        this.$notify({ type: "success", text: "Status geändert" });
        this.progress(el);
      });
    },

    order(index) {
      let discourses = this.discourses[index].map(function(discourse, idx) {
        discourse.order = idx;
        return discourse;
      });
      if (this.debounce) return;
      this.debounce = setTimeout(
        function() {
          this.debounce = false;
          this.axios
            .post(`/api/discourse/order`, { discourses: discourses })
            .then(response => {
              this.$notify({ type: "success", text: "Reihenfolge angepasst" });
            });
        }.bind(this, discourses),
        500
      );
    },

    filterResults(category) {
      if (category) {
        this.filter = category;
        this.hasFilter = true;
      }
    }
  }
};
</script>