<template>
  <div class="container">
    <LoadingIndicator v-if="isLoading" />
    <main class="content" role="main">
      <div>
        <h1>Home - News</h1>
        <router-link :to="{ name: 'news-create' }" class="btn-add">
          <span>Hinzufügen</span>
        </router-link>
        <div class="list-items" v-if="news.items.length">
          <draggable
            v-model="news.items"
            item-key="id"
            ghost-class="draggable-ghost"
            draggable=".list-item"
            @end="order(news.items)"
          >
            <template #item="{ element: item }">
              <div :class="[item.publish == 0 ? 'is-disabled' : '', 'list-item is-draggable']" data-icons="3">
                <div class="list-item-body">
                  <strong>{{ item.title.de }}</strong>
                  <PhPushPin v-if="item.sticky" :size="16" weight="light" title="Angeheftet" />
                </div>
                <ListActions :record="item" edit-route="news-edit" @toggle="news.toggle" @destroy="news.destroy" />
              </div>
            </template>
          </draggable>
        </div>
        <div v-else-if="news.isFetched">
          <p>Es sind noch keine News vorhanden...</p>
        </div>
      </div>
    </main>
  </div>
</template>
<script setup>
import { ref, reactive } from 'vue';
import draggable from 'vuedraggable';
import { PhPushPin } from '@phosphor-icons/vue';
import LoadingIndicator from '@/components/ui/LoadingIndicator.vue';
import ListActions from '@/components/ui/ListActions.vue';
import { useListing } from '@/composables/useListing';
import { useOrder } from '@/composables/useOrder';

const isLoading = ref(false);
const news = reactive(useListing({ list: '/api/news/get', resource: 'news', isLoading }));
const order = useOrder({ url: '/api/news/order', key: 'news' });
</script>
