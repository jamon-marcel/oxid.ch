<template>
  <div class="box-wrapper">
    <figure :style="{ backgroundImage: `url(/img/crop/${element.image}/2400/1500/${element.coords})` }">
      <a href="javascript:;" class="btn-trash is-mini" @click.prevent="emit('destroy', element.id)">Löschen</a>
      <figcaption v-if="element.caption && element.caption != 'null' && element.caption != 'undefined'">
        <strong>{{ element.caption }}</strong>
      </figcaption>
    </figure>
  </div>
</template>
<script setup>
// The /img/crop/… URL redirects to the signed rendition (05-image-pipeline.md)
defineProps({
  element: { type: Object, required: true },
});

const emit = defineEmits(['destroy']);
</script>
