<template>
  <div>
    <a
      href="javascript:;"
      :class="[publish == 1 ? 'icon-eye' : 'icon-eye-off', 'icon-mini']"
      @click.prevent="toggle(image,$event)"
    ></a>
    <a
      href="javascript:;"
      class="icon-edit icon-mini"
      @click.prevent="showOverlay(image,$event)"
    ></a>
    <a 
      :href="getSource(image.name, 'large')" 
      target="_blank" 
      class="icon-external-link icon-mini">
    </a>
    <a
      href="javascript:;"
      class="icon-trash icon-mini"
      @click.prevent="destroy(image,$event)">
    </a>
  </div>
</template>

<script>
import Utils from "@/mixins/utils";
export default {

  props: {
    image: Object,
    publish: Number,
  },

  mixins: [Utils],

  methods: {
    
    toggle(image, $event) {
      this.$parent.toggle(image,$event);
    },

    destroy(image, $event) {
      this.$parent.destroy(image.name,$event);
    },

    showOverlay(image, $event) {
      this.$parent.showOverlay(image, $event);
    },
  }
}
</script>
