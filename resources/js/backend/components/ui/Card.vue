<template>
  <figure
    :class="['card', { 'is-disabled': disabled, 'is-file': file, 'is-selectable': selectable }]"
    :tabindex="selectable ? 0 : null"
    :role="selectable ? 'button' : null"
    :title="selectable ? label : null"
    @click="selectable && emit('select')"
    @keydown.enter.prevent="selectable && emit('select')"
    @keydown.space.prevent="selectable && emit('select')"
  >
    <component :is="href ? 'a' : 'div'" class="card__media" :href="href" :target="href ? '_blank' : null">
      <img :src="src" alt="" loading="lazy">
    </component>
    <figcaption class="card__footer" v-if="label || $slots.default">
      <span class="card__label" v-if="label" :title="label">{{ label }}</span>
      <div class="card__actions" v-if="$slots.default">
        <slot />
      </div>
    </figcaption>
  </figure>
</template>
<script setup>
// The one card for images and files: square media on top, a footer with a
// label and/or actions. Selectable cards are buttons (the grid image picker).
defineProps({
  src: { type: String, required: true },
  // opens the media in a new tab
  href: { type: String, default: null },
  label: { type: String, default: null },
  // unpublished: dimmed
  disabled: { type: Boolean, default: false },
  // a file icon instead of an image
  file: { type: Boolean, default: false },
  selectable: { type: Boolean, default: false },
});

const emit = defineEmits(['select']);
</script>
