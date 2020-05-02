<template>
  <div>
    <page-header/>
    <notifications classes="notification"/>
    <div class="container">
      <main class="content" role="main">
        <div>
          <h1>Profil</h1>
          <div class="list-items" v-if="profile">
            <div
              :class="[p.publish == 0 ? 'is-disabled' : '', 'list-item']"
              v-for="p in profile"
              :key="p.id"
              data-icons="2"
            >
              <div class="list-item-body">
                {{ p.title.de }}
              </div>
              <div class="list-item-action" data-icons="2">
                <a
                  href="javascript:;"
                  :class="[p.publish == 1 ? 'icon-eye' : 'icon-eye-off', 'icon-mini']"
                  @click.prevent="toggle(p.id,$event)"
                ></a>
                <router-link
                  :to="{name: 'profile-edit', params: { id: p.id }}"
                  class="icon-edit icon-mini"
                ></router-link>
              </div>
            </div>
          </div>
          <div v-else>
            <p>Es sind noch keine Inhalte vorhanden...</p>
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
    PageHeader: PageHeader,
  },

  mixins: [Progress],

  data() {
    return {
      profile: [],
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.axios.get(`/api/profile/get`).then(response => {
        this.profile = response.data;
      });
    },

    destroy(id,event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let el = this.progress(evenp.target);
        this.axios.delete(`/api/profile/destroy/${id}`).then(response => {
          this.fetch();
          this.$notify({ type: "success", text: "Eintrag gelöscht" });
          this.progress(el);
        });
      }
    },

    toggle(id, event) {
      let el = this.progress(event.target);
      this.axios.get(`/api/profile/status/${id}`).then(response => {
        const index = this.profile.findIndex(x => x.id === id);
        this.profile[index].publish = response.data;
        this.$notify({ type: "success", text: "Status geändert" });
        this.progress(el);
      });
    }

  },

  computed: {
  }
};
</script>