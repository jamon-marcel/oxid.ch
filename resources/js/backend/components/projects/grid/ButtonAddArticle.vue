<template>
  <div>
    <a
      href="javascript:;"
      class="btn-add-article"
      @click.prevent="getArticles(gridId,gridPosition);"
    >Artikel auswählen</a>
  </div>
</template>
<script>
export default {
  props: {
    gridId: Number,
    gridPosition: Number,
  },
  methods: {
    getArticles(gridId,position) {
      this.$parent.getArticles(gridId, position);
    },
  }
};
</script>