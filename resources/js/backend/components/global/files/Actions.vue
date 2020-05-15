<template>
  <div>
    <a
      href="javascript:;"
      :class="[publish == 1 ? 'icon-eye' : 'icon-eye-off', 'icon-mini']"
      @click.prevent="toggle(file,$event)"
    ></a>
    <a
      href="javascript:;"
      class="icon-edit icon-mini"
      @click.prevent="showEdit(file,$event)"
    ></a>
    <a 
      :href="`/storage/uploads/${file.name}`" 
      target="_blank" 
      class="icon-external-link icon-mini">
    </a>
    <a
      href="javascript:;"
      class="icon-trash icon-mini"
      @click.prevent="destroy(file,$event)">
    </a>
  </div>
</template>

<script>
import Utils from "@/mixins/utils";
export default {

  props: {
    file: Object,
    publish: Number,
  },

  mixins: [Utils],

  methods: {
    
    toggle(file, $event) {
      this.$parent.toggle(file,$event);
    },

    destroy(file, $event) {
      this.$parent.destroy(file.name,$event);
    },

    showEdit(file) {
      this.$parent.showEdit(file);
    },
  }
}
</script>
