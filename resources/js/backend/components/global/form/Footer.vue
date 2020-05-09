<template>
  <footer class="site-footer">
    <div>
      <button type="submit" class="btn-secondary">Speichern</button>
      <router-link :to="{name: route, params: { id: param }}">Zurück</router-link>
    </div>
  </footer>
</template>
<script>
export default {
  props: {
    route: String,
    param: Number
  }
}
</script>