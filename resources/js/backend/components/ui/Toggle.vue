<template>
  <div class="toggle">
    <label v-if="label" class="is-sm" :for="id">{{ label }}</label>
    <label class="toggle__control">
      <input
        :id="id"
        type="checkbox"
        class="toggle__input"
        :checked="value === 1"
        @change="value = $event.target.checked ? 1 : 0"
      >
      <span class="toggle__switch" aria-hidden="true"></span>
      <span class="toggle__state">{{ value === 1 ? labelTrue : labelFalse }}</span>
    </label>
  </div>
</template>
<script setup>
// A yes/no switch for 0/1 fields (was two Ja/Nein buttons)
const props = defineProps({
  label: { type: String, default: '' },
  name: { type: String, default: '' },
  labelTrue: { type: String, default: 'Ja' },
  labelFalse: { type: String, default: 'Nein' },
});

// Unique per instance: the same field appears in several forms and overlays
const id = `toggle-${props.name}-${Math.random().toString(36).slice(2, 8)}`;

// The API returns 0/1, sometimes as strings; booleans from older records too
const value = defineModel({
  get: value => value === null || value === undefined ? 0 : Number(value),
});
</script>
