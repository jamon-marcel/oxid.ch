<template>
  <div class="box-wrapper">
    <figure :style="{ backgroundImage: 'url(/img/crop/'+img.name+'/2400/1500/'+img.coords+')' }">
      <a
        href="javascript:;"
        class="btn-trash is-mini"
        @click.prevent="deleteImage(element.id, $event)">
        Löschen
      </a>
      <figcaption v-if="element.caption != 'null' && element.caption != 'undefined'">
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
        name: null,
        coords: null,
      },
    };
  },

  created() {
    this.img.name = this.$props.element.image;
    this.img.coords = this.$props.element.coords;
  },

  methods: {
    deleteImage(id, event) {
      let btn = event.target;
      this.$parent.deleteImage(id, btn);
    },
  },
};
</script>
