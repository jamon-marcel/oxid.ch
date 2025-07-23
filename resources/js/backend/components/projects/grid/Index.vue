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
                <template v-if="grid.elements">
                  <div class="grid-layout-row__images">
                    <div v-for="element in grid.elements" :key="element.id">
                      <img 
                        :src="getSource(element.image.name, 'thumbnail')" 
                        height="300" 
                        width="300"
                        style="height: 50px; width: auto; display: block; margin: 0 4px"
                        v-if="element.image">       
                    </div>
                  </div>
                </template>
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
import Utils from "@/mixins/utils";

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

  mixins: [Utils],

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

