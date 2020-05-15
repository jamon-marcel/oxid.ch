<template>
  <div>
    <page-header/>
    <notifications classes="notification"/>
    <div class="container">
      <main class="content" role="main">
        <div>
          <h1>Layout für Projekt «{{pageTitle}}»</h1>
          <grid-selector></grid-selector>
          <a href="javascript:;"
            class="icon-layout"
            @click.prevent="toggleView()">
            <span v-if="layout == 'grid'">Grid</span>
            <span v-if="layout == 'list'">Liste</span>
          </a>          
          <div v-if="layout == 'grid'">
            <div class="grid-layout-row " v-for="grid in grids" :key="grid.id">
              <a
                href="javascript:;"
                class="btn-trash"
                @click.prevent="destroy(grid.id,$event)"
              >Zeile löschen</a>
              <grid-row :layout="grid.layout.key" :gridId="grid.id" :projectId="projectId"></grid-row>
            </div>
          </div>
          <div v-if="layout == 'list'">
            <draggable 
              :disabled="false"
              v-model="grids" 
              @end="updateOrder"
              ghost-class="draggable-ghost"
              draggable=".grid-layout-row">
              <div class="grid-layout-row  is-list is-draggable" v-for="grid in grids" :key="grid.id">
                <span class="icon-grid-list">
                  <img :src="'/assets/backend/img/icons/grid-' + grid.layout.key + '.svg'" height="172" width="126">
                </span>
              </div>
            </draggable>
          </div>
          <footer class="site-footer">
            <div>
              <a
                :href="'/projekt/' + projectId"
                class="btn-preview"
                target="_blank"
              >Vorschau</a>
              <router-link :to="{name: 'projects'}">Zurück</router-link>
            </div>
          </footer>
        </div>
      </main>
    </div>
  </div>
</template>
<script>
import PageHeader from "@/layout/PageHeader.vue";
import draggable from 'vuedraggable';
import GridRow from "@/components/projects/grid/Row.vue";
import GridSelector from "@/components/projects/grid/Selector.vue";

export default {
  components: {
    draggable,
    PageHeader: PageHeader,
    GridRow: GridRow,
    GridSelector: GridSelector
  },

  data() {
    return {
      grids: [],
      pageTitle: null,
      projectId: null,
      debounce: false,
      hasOverlayEdit: false,
      layout: 'grid',
    };
  },

  created() {
    this.projectId = parseInt(this.$route.params.id);
    this.fetch();
  },

  mounted() {
    let uri = `/api/project/get/${parseInt(this.$route.params.id)}`;
    this.axios.get(uri).then(response => {
      let p = response.data;
      this.pageTitle = `${p.title.de}`;
    });
  },

  methods: {

    fetch() {
      let uri = `/api/project/grids/${this.projectId}`;
      this.axios.get(uri).then(response => {
        this.grids = response.data.data;
      });
    },

    store(gridId) {
      let uri = `/api/project/grid/store/${this.projectId}/${gridId}`;
      this.axios.get(uri).then(response => {
        this.$notify({type: 'success', text: 'Zeile hinzugefügt'});
        this.fetch();
      });
    },

    updateOrder() {
      let grids = this.grids.map(function(grid, index) {
          grid.order = index;
          return grid;
      });
      if (this.debounce) return;
      this.debounce = setTimeout(function(books) {
        this.debounce = false 
        let uri = `/api/project/grids/order`;
        this.axios.post(uri, {grids: grids}).then((response) => {
          this.$notify({type: 'success', text: 'Reihenfolge angepasst'});
        });
      }.bind(this, grids), 1000);
    },

    destroy(gridId, event) {
      let uri = `/api/project/grid/delete/${gridId}`;
      let btn = event.target;
      btn.classList.add('is-loading');
      this.axios.delete(uri).then(response => {
        let row = event.target.parentNode, self = this;
        btn.classList.remove('is-loading');
        row.classList.add('fade-out');
        setTimeout(function(){
          const index = self.grids.findIndex(x => x.id === gridId);
          self.grids.splice(index, 1);
          self.$notify({type: 'success', text: 'Zeile gelöscht'});
        }, 200);
      });
    },

    toggleView() {
      this.layout = this.layout == 'grid' ? 'list' : 'grid';
    }
  },
};
</script>

