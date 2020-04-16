<template>
  <div>
    <div class="grid-layout-selector">
      <a href class="btn-toggle-layout" @click.prevent="toggleSelect()">Layout wählen</a>
      <ul :class="showSelect ? 'is-visible': ''" ref="dropdown">
        <li v-for="layout in layouts" :key="layout.id">
          <a href @click.prevent="storeGrid(layout.id)">
            <img :src="getAssetUri(layout.key)" height="30" width="100">
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      layouts: [],
      showSelect: false
    };
  },

  created() {
    this.axios.get('/api/project/grid/layouts').then(response => {
      this.layouts = response.data.data;
    });
  },

  methods: {

    getAssetUri(key) {
      return `/assets/backend/img/icons/grid-${key}.svg`;
    },

    toggleSelect() {
      this.showSelect = this.showSelect ? false : true;
    },

    storeGrid(gridId) {
      this.showSelect = false;
      this.$parent.store(gridId);
    }
  }
};
</script>
