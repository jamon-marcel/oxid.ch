<template>
  <FormGroup>
    <FormCheckbox v-if="type === 'checkbox'" :id="id" v-model="model">{{ label }}</FormCheckbox>
    <template v-else>
      <FormLabel :for="id" :error="error ? `${label}: ${message}` : null">{{ label }}{{ required ? ' *' : '' }}</FormLabel>
      <Editor v-if="type === 'editor'" v-model="model" :has-error="!!error" :class="{ 'editor-tall': tall }" @focus="clear" />
      <FormSelect v-else-if="type === 'select'" :id="id" v-model="model" :options="options" :placeholder="placeholder" :has-error="!!error" @focusin="clear" />
      <FormInput v-else :id="id" v-model="model" :type="type" :placeholder="placeholder" :autocomplete="autocomplete" :has-error="!!error" @focus="clear" />
    </template>
    <span v-if="hint" class="mt-6 block text-xs text-gray-500 dark:text-warm-400">{{ hint }}</span>
  </FormGroup>
</template>
<script setup>
import { computed } from 'vue';
import FormGroup from '@/components/ui/form/FormGroup.vue';
import FormLabel from '@/components/ui/form/FormLabel.vue';
import FormInput from '@/components/ui/form/FormInput.vue';
import FormSelect from '@/components/ui/form/FormSelect.vue';
import FormCheckbox from '@/components/ui/form/FormCheckbox.vue';
import Editor from '@/components/ui/editor/Editor.vue';

/**
 * Label + control + validation error for one form field.
 * type: text | email | password | select | editor | checkbox
 * name: the field's key in errors (e.g. 'title.de'); focus clears its error
 */
const model = defineModel();

const props = defineProps({
  name: { type: String, required: true },
  label: { type: String, required: true },
  type: { type: String, default: 'text' },
  errors: { type: Object, default: () => ({}) },
  options: { type: Array, default: () => [] },
  placeholder: { type: String, default: null },
  hint: { type: String, default: null },
  // e.g. 'new-password', so the browser doesn't fill in a saved login
  autocomplete: { type: String, default: null },
  required: { type: Boolean, default: false },
  // a taller editor (long texts)
  tall: { type: Boolean, default: false },
});

const id = computed(() => props.name.replace(/\./g, '_'));
const error = computed(() => props.errors[props.name]);

// The API's first message, or Pflichtfeld for an empty required field
const message = computed(() => {
  if (Array.isArray(error.value)) {
    return error.value[0];
  }
  return typeof error.value === 'string' ? error.value : 'Pflichtfeld';
});
const clear = () => delete props.errors[props.name];
</script>
