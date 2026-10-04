<template>
  <div class="container">
    <LoadingIndicator v-if="isLoading" />
    <main class="content" role="main">
      <div>
        <h1>Layout für Projekt «{{ projectTitle }}»</h1>
        <GridSelector @select="store" />
        <a href="javascript:;" class="icon-layout" @click.prevent="view = view === 'grid' ? 'list' : 'grid'">
          <span v-if="view === 'grid'">Grid</span>
          <span v-else>Liste</span>
        </a>
        <div v-if="view === 'grid'">
          <div class="grid-layout-row" v-for="grid in grids" :key="grid.id">
            <a href="javascript:;" class="btn-trash" @click.prevent="destroy(grid)">Zeile löschen</a>
            <GridRow :layout="grid.layout.key" :grid-id="grid.id" :project-id="projectId" />
          </div>
        </div>
        <div v-else>
          <SortableList v-model="grids" @end="order">
            <div v-for="grid in grids" :key="grid.id" class="grid-layout-row is-list is-draggable">
              <span class="icon-grid-list">
                <img :src="`/assets/backend/img/icons/grid-${grid.layout.key}.svg`" height="172" width="126">
              </span>
              <div class="grid-layout-row__images" v-if="grid.elements">
                <div v-for="element in grid.elements" :key="element.id">
                  <img
                    v-if="element.image"
                    :src="imageUrl(element.image, 'thumbnail')"
                    height="300"
                    width="300"
                    style="height: 50px; width: auto; display: block; margin: 0 4px"
                  >
                </div>
              </div>
            </div>
          </SortableList>
        </div>
        <footer class="site-footer">
          <div>
            <a :href="`/projekt/${projectId}`" class="btn-preview" target="_blank">Vorschau</a>
            <router-link :to="{ name: 'projects' }">Zurück</router-link>
          </div>
        </footer>
      </div>
    </main>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import { notify } from '@/lib/notify';
import LoadingIndicator from '@/components/ui/LoadingIndicator.vue';
import SortableList from '@/components/ui/SortableList.vue';
import GridRow from '@/components/grid/Row.vue';
import GridSelector from '@/components/grid/Selector.vue';
import { useOrder } from '@/composables/useOrder';
import { imageUrl } from '@/lib/images';
import http from '@/lib/http';

// The page builder: rows (grids) with a layout, each box holding a project image
const route = useRoute();
const projectId = Number(route.params.id);

const grids = ref([]);
const view = ref('grid');
const isLoading = ref(false);
const projectTitle = ref(null);

async function request(call) {
  isLoading.value = true;
  try {
    return await call();
  }
  catch {
    // Notified by the http error handler
  }
  finally {
    isLoading.value = false;
  }
}

async function fetch() {
  const response = await request(() => http.get(`/api/project/grids/${projectId}`));
  if (response) {
    grids.value = response.data.data;
  }
}

async function store(layoutId) {
  if (await request(() => http.get(`/api/project/grid/store/${projectId}/${layoutId}`))) {
    notify({ type: 'success', text: 'Zeile hinzugefügt' });
    fetch();
  }
}

async function destroy(grid) {
  if (await request(() => http.delete(`/api/project/grid/delete/${grid.id}`))) {
    grids.value.splice(grids.value.indexOf(grid), 1);
    notify({ type: 'success', text: 'Zeile gelöscht' });
  }
}

const order = useOrder({ url: '/api/project/grids/order', key: 'grids', delay: 1000 });

http.get(`/api/project/get/${projectId}`).then(({ data }) => projectTitle.value = data.title.de);
fetch();
</script>
