<template>
  <div>
    <div class="grid-layout-selector">
      <a href class="btn-toggle-layout" @click.prevent="isOpen = !isOpen">Layout wählen</a>
      <ul :class="isOpen ? 'is-visible' : ''">
        <li v-for="layout in layouts" :key="layout.id">
          <a href @click.prevent="select(layout.id)">
            <img :src="`/assets/backend/img/icons/grid-${layout.key}.svg`" height="30" width="100">
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import http from '@/lib/http';

const emit = defineEmits(['select']);

const layouts = ref([]);
const isOpen = ref(false);

http.get('/api/project/grid/layouts').then(({ data }) => layouts.value = data.data);

function select(layoutId) {
  isOpen.value = false;
  emit('select', layoutId);
}
</script>
