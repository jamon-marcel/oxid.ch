<template>
  <div class="box-wrapper">
    <figure :style="{ backgroundImage: 'url(/image/project/'+img.name+')' }">
      <a
        href="javascript:;"
        class="btn-trash is-mini"
        @click.prevent="deleteImage(element.id, $event)">
        Löschen
      </a>
      <figcaption v-if="element.caption != 'null'">
        <strong>{{element.caption}}</strong>
      </figcaption>
    </figure>
  </div>
</template>
<script>
import Utils from "@/mixins/utils";

export default {
  components: {
  },

  props: {
    element: Object,
  },

  mixins: [Utils],

  data() {
    return {
      img: {
        name: null
      },
    };
  },

  created() {
    this.img.name = this.$props.element.image;
  },

  methods: {
    deleteImage(id, event) {
      let btn = event.target;
      this.$parent.deleteImage(id, btn);
    },
  },
};
</script>
