<template>
  <div>
    <page-header/>
    <notifications classes="notification"/>
    <div class="container">
      <main class="content" role="main">
        <div>
          <h1>Projekte</h1>
          <router-link :to="{ name: 'project-create' }" class="btn-add">
            <span>Hinzufügen</span>
          </router-link>
          <div class="list-items" v-if="projects.length">
            <div
              :class="[p.publish == 0 ? 'is-disabled' : '', 'list-item']"
              v-for="p in projects"
              :key="p.id"
              data-icons="4"
            >
              <div class="list-item-body">
                <strong>{{ p.title_short.de }}</strong>, {{ p.location.de }}
              </div>
              <div class="list-item-action" data-icons="4">
                <router-link
                  :to="{name: 'project-grids', params: { id: p.id }}"
                  :class="[p.images.length > 0 ? '' : 'is-disabled', 'icon-grid icon-mini']"
                  title="Layout"
                ></router-link>
                <a
                  href="javascript:;"
                  :class="[p.publish == 1 ? 'icon-eye' : 'icon-eye-off', 'icon-mini']"
                  @click.prevent="toggle(p.id,$event)"
                ></a>
                <router-link
                  :to="{name: 'project-edit', params: { id: p.id }}"
                  class="icon-edit icon-mini"
                ></router-link>
                <a
                  href="javascript:;"
                  class="icon-trash icon-mini"
                  @click.prevent="destroy(p.id,$event)"
                ></a>
              </div>
            </div>
          </div>
          <div v-else>
            <p>Es sind noch keine Projekte vorhanden...</p>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
<script>
import PageHeader from "@/layout/PageHeader.vue";
import Progress from "@/mixins/progress";

export default {
  components: {
    PageHeader: PageHeader
  },

  mixins: [Progress],

  data() {
    return {
      projects: [],
    };
  },

  created() {
    this.fetch();
  },

  methods: {
    fetch() {
      let uri = "/api/projects/get";
      this.axios.get(uri).then(response => {
        this.projects = response.data.data;
      });
    },

    destroy(id,event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/project/destroy/${id}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          this.fetch();
          this.$notify({ type: "success", text: "Eintrag gelöscht" });
          this.progress(el);
        });
      }
    },

    toggle(id,event) {
      let uri = `/api/project/status/${id}`;
      let el = this.progress(event.target);
      this.axios.get(uri).then(response => {
        const index = this.projects.findIndex(x => x.id === id);
        this.projects[index].publish = response.data;
        this.$notify({ type: "success", text: "Status geändert" });
        this.progress(el);
      });
    },
  },
};
</script>