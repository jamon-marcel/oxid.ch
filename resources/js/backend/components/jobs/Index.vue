<template>
  <div>
    <page-header/>
    <notifications classes="notification"/>
    <div class="container">
      <main class="content" role="main">
        <div>
          <h1>Diskurs</h1>
          <router-link :to="{ name: 'job-create' }" class="btn-add">
            <span>Hinzufügen</span>
          </router-link>
          <div class="list-items" v-if="jobs.length">
            <div
              :class="[j.publish == 0 ? 'is-disabled' : '', 'list-item']"
              v-for="j in jobs"
              :key="j.id"
              data-icons="3"
            >
              <div class="list-item-body">{{ j.title.de }}</div>
              <div class="list-item-action" data-icons="3">
                <a
                  href="javascript:;"
                  :class="[j.publish == 1 ? 'icon-eye' : 'icon-eye-off', 'icon-mini']"
                  @click.prevent="toggle(j.id,$event)"
                ></a>
                <router-link
                  :to="{name: 'job-edit', params: { id: j.id }}"
                  class="icon-edit icon-mini"
                ></router-link>
                <a
                  href="javascript:;"
                  class="icon-trash icon-mini"
                  @click.prevent="destroy(j.id,$event)"
                ></a>
              </div>
            </div>
          </div>
          <div v-else>
            <p>Es sind noch keine Jobs vorhanden...</p>
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
      jobs: []
    };
  },

  created() {
    this.fetch();
  },

  methods: {
    fetch() {
      this.axios.get(`/api/jobs/get`).then(response => {
        this.jobs = response.data.data;
      });
    },

    destroy(id, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/job/destroy/${id}`;
        let el = this.progress(event.target);
        this.axios.delete(uri).then(response => {
          this.fetch();
          this.$notify({ type: "success", text: "Eintrag gelöscht" });
          this.progress(el);
        });
      }
    },

    toggle(id, event) {
      let el = this.progress(event.target);
      this.axios.get(`/api/job/status/${id}`).then(response => {
        const index = this.jobs.findIndex(x => x.id === id);
        this.jobs[index].publish = response.data;
        this.$notify({ type: "success", text: "Status geändert" });
        this.progress(el);
      });
    }
  }
};
</script>