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
      @click.prevent="showEdit(image)"
    ></a>
    <a 
      :href="getSource(image.name, 'large')" 
      target="_blank" 
      class="icon-external-link icon-mini">
    </a>
    <a
      href="javascript:;"
      :class="[grid == 1 ? 'icon-disabled' : '', 'icon-trash icon-mini']"
      @click.prevent="destroy(image,$event)">
    </a>
    <a
      href="javascript:;"
      class="icon-crop icon-mini"
      @click.prevent="showCropper(image)"
    ></a>
  </div>
</template>

<script>
import Utils from "@/mixins/utils";
export default {

  props: {
    image: Object,
    publish: Number,
    grid: {
      type: Number,
      default: 0
    }
  },

  mixins: [Utils],

  methods: {
    
    toggle(image, $event) {
      this.$parent.toggle(image,$event);
    },

    destroy(image, $event) {
      this.$parent.destroy(image.name,$event);
    },

    showEdit(image) {
      this.$parent.showEdit(image);
    },

    showCropper(image) {
      this.$parent.showCropper(image);
    },
  }
}
</script>
