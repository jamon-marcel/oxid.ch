<template>
  <div>
    <label>
      {{ label }} 
      <em v-if="required">*</em>
    </label>
    <input
      type="text"
      :value="modelValue"
      @input="update($event.target.value)"
      @focus="focus()"
    >
    <div class="is-required" v-if="required">
      Pflichtfeld
    </div>
  </div>
</template>
<script>
export default {
  props: {
    label: {
      type: String,
    },
    name: {
      type: String,
    },
    language: {
      type: String,
      default: null
    },
    modelValue: {
      type: String
    },
    required: {
      type: Boolean,
      default: false
    }
  },

  emits: ['update:modelValue'],

  methods: {
    
    update(value) {
      this.$emit('update:modelValue', value)
    },

    focus() {
      if (this.$props.required) {
        this.$parent.removeError(this.$props.name, this.$props.language)
      }
    }
  }
}
</script>