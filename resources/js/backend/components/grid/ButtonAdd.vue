<template>
  <div>
    <a href="javascript:;" class="btn-add-media" @click.prevent="emit('pick')">Bild auswählen</a>
  </div>
</template>
<script setup>
const emit = defineEmits(['pick']);
</script>
