<template>
  <div class="container">
    <LoadingIndicator v-if="isLoading" />
    <main class="content" role="main">
      <div>
        <h1>Projekte</h1>
        <router-link :to="{ name: 'project-create' }" class="btn-add">
          <span>Hinzufügen</span>
        </router-link>
        <div class="list-items" v-if="projects.items.length">
          <SortableList v-model="projects.items" @end="order">
            <div v-for="project in projects.items" :key="project.id" :class="[project.publish == 0 ? 'is-disabled' : '', 'list-item is-draggable']" data-icons="4">
              <div class="list-item-body">
                <strong>{{ project.title_short.de }}</strong>, {{ project.location.de }}
                <PhStar v-if="project.is_highlight" :size="16" weight="light" class="is-highlight" title="Highlight" />
              </div>
              <div class="list-item-actions">
                <router-link
                  :to="{ name: 'project-grids', params: { id: project.id } }"
                  :class="[project.images.length ? '' : 'is-disabled', 'feather-icon']"
                  title="Layout"
                >
                  <PhSquaresFour :size="18" weight="light" />
                </router-link>
                <ListActions :record="project" edit-route="project-edit" @toggle="projects.toggle" @destroy="projects.destroy" />
              </div>
            </div>
          </SortableList>
        </div>
        <div v-else-if="projects.isFetched">
          <p>Es sind noch keine Projekte vorhanden...</p>
        </div>
      </div>
    </main>
  </div>
</template>
<script setup>
import { ref, reactive } from 'vue';
import { PhStar, PhSquaresFour } from '@phosphor-icons/vue';
import LoadingIndicator from '@/components/ui/LoadingIndicator.vue';
import ListActions from '@/components/ui/ListActions.vue';
import SortableList from '@/components/ui/SortableList.vue';
import { useListing } from '@/composables/useListing';
import { useOrder } from '@/composables/useOrder';

const isLoading = ref(false);
const projects = reactive(useListing({ list: '/api/projects/get', resource: 'project', isLoading }));
const order = useOrder({ url: '/api/project/order', key: 'projects' });
</script>
