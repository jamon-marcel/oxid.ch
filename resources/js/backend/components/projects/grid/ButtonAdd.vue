<template>
  <div>
    <a
      href="javascript:;"
      class="btn-add-media"
      @click.prevent="getImages(gridId,gridPosition);"
    >Bild auswählen</a>
  </div>
</template>
<script>
export default {
  
  props: {
    gridId: Number,
    gridPosition: Number,
  },

  methods: {
    getImages(gridId, position) {
      this.$parent.getImages(gridId, position);
    },
  }
};
</script>