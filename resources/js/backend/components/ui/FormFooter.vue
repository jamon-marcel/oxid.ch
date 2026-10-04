<template>
  <footer class="site-footer">
    <div>
      <button type="submit" class="btn-secondary">Speichern</button>
      <router-link :to="back">Zurück</router-link>
    </div>
  </footer>
</template>
<script setup>
defineProps({
  back: { type: Object, required: true },
});
</script>
