<template>
  <nav class="tabs">
    <ul>
      <li v-for="tab in tabs" :key="tab.key">
        <a
          href="javascript:;"
          :class="{ 'is-active': active === tab.key, 'has-error': errors.includes(tab.key) }"
          @click="active = tab.key"
        >{{ tab.label }}</a>
      </li>
    </ul>
  </nav>
</template>
<script setup>
defineProps({
  tabs: { type: Array, required: true },
  // keys of the tabs that hold a field with an error
  errors: { type: Array, default: () => [] },
});

const active = defineModel({ type: String, required: true });
</script>
