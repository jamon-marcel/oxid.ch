<template>
  <div>
    <page-header/>
    <notifications classes="notification"/>
    <div class="container">
      <main class="content" role="main">
        <div>
          <h1>Team</h1>
          <router-link :to="{ name: 'team-create' }" class="btn-add">
            <span>Hinzufügen</span>
          </router-link>
          <div class="list-items is-grouped" v-if="teams">
            <div v-for="(team, index) in teams" :key="index">
              <h3 class="list-item-header">{{categories[index]}}</h3>
              <draggable 
                :disabled="false"
                v-model="teams[index]" 
                @end="order(index)"
                ghost-class="draggable-ghost"
                draggable=".list-item">
                <div
                  :class="[t.publish == 0 ? 'is-disabled' : '', 'list-item is-draggable']"
                  v-for="t in team"
                  :key="t.id"
                  data-icons="3"
                >
                  <div class="list-item-body">
                    {{ t.firstname }} {{ t.name }}
                  </div>
                  <div class="list-item-action" data-icons="3">
                    <a
                      href="javascript:;"
                      :class="[t.publish == 1 ? 'icon-eye' : 'icon-eye-off', 'icon-mini']"
                      @click.prevent="toggle(t.id,$event)"
                    ></a>
                    <router-link
                      :to="{name: 'team-edit', params: { id: t.id }}"
                      class="icon-edit icon-mini"
                    ></router-link>
                    <a
                      href="javascript:;"
                      class="icon-trash icon-mini"
                      @click.prevent="destroy(t.id,$event)"
                    ></a>
                  </div>
                </div>
              </draggable>
            </div>
          </div>
          <div v-else>
            <p>Es sind noch keine Teammitglieder vorhanden...</p>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
<script>
import PageHeader from "@/layout/PageHeader.vue";
import draggable from 'vuedraggable';
import Progress from "@/mixins/progress";

export default {
  components: {
    PageHeader: PageHeader,
    draggable,
  },

  mixins: [Progress],

  data() {
    return {
      teams: [],
      categories: [],
    };
  },

  created() {
    this.fetch();
  },

  methods: {
    fetch() {
      this.axios.get(`/api/teams/get`).then(response => {
        this.teams = response.data;
      });
      this.axios.get(`/api/settings/teamCategories`).then(response => {
        this.categories = response.data;
      });
    },

    destroy(id,event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let el = this.progress(event.target);
        this.axios.delete(`/api/team/destroy/${id}`).then(response => {
          this.fetch();
          this.$notify({ type: "success", text: "Eintrag gelöscht" });
          this.progress(el);
        });
      }
    },

    toggle(id,event) {
      let el = this.progress(event.target), teams = this.teams;
      this.axios.get(`/api/team/status/${id}`).then(response => {
        Object.keys(teams).forEach(function(key) {
          const index = teams[key].findIndex(x => x.id === id);
          if (index > -1) {
            teams[key][index].publish = response.data;
          }
        });
        this.teams = teams;
        this.$notify({ type: "success", text: "Status geändert" });
        this.progress(el);
      });
    },

    order(index) {
      let teams = this.teams[index].map(function(team, idx) {
        team.order = idx;
        return team;
      });

      if (this.debounce) return;

      this.debounce = setTimeout(function() {
        this.debounce = false 
        this.axios.post(`/api/team/order`, {teams: teams}).then((response) => {
          this.$notify({type: 'success', text: 'Reihenfolge angepasst'});
        });
      }.bind(this, teams), 500);
    },
  },

  computed: {
  }
};
</script>