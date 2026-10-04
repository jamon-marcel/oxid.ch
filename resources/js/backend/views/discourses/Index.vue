<template>
  <div class="container">
    <LoadingIndicator v-if="isLoading" />
    <main class="content" role="main">
      <div>
        <h1>Diskurs</h1>
        <router-link :to="{ name: 'discourse-create' }" class="btn-add">
          <span>Hinzufügen</span>
        </router-link>
        <div class="list-items" v-if="discourses.items.length">
          <draggable
            v-model="discourses.items"
            item-key="id"
            ghost-class="draggable-ghost"
            draggable=".list-item"
            @end="order(discourses.items)"
          >
            <template #item="{ element: discourse }">
              <div :class="[discourse.publish == 0 ? 'is-disabled' : '', 'list-item is-draggable']" data-icons="3">
                <div class="list-item-body">
                  {{ discourse.title.de }} - ({{ discourse.heading.de }}, {{ discourse.date.de }}) - {{ categories[discourse.category] }}
                </div>
                <ListActions :record="discourse" edit-route="discourse-edit" @toggle="discourses.toggle" @destroy="discourses.destroy" />
              </div>
            </template>
          </draggable>
        </div>
        <div v-else-if="discourses.isFetched">
          <p>Es sind noch keine Einträge vorhanden...</p>
        </div>
      </div>
    </main>
  </div>
</template>
<script setup>
import { ref, reactive } from 'vue';
import draggable from 'vuedraggable';
import LoadingIndicator from '@/components/ui/LoadingIndicator.vue';
import ListActions from '@/components/ui/ListActions.vue';
import { useListing } from '@/composables/useListing';
import { useOrder } from '@/composables/useOrder';
import http from '@/lib/http';

const isLoading = ref(false);
const discourses = reactive(useListing({ list: '/api/discourses/get', resource: 'discourse', isLoading }));
const order = useOrder({ url: '/api/discourse/order', key: 'discourses' });

const categories = ref({});
http.get('/api/settings/discourseCategories').then(({ data }) => categories.value = data);
</script>
