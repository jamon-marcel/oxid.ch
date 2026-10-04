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
          <SortableList v-model="news.items" @end="order">
            <div v-for="item in news.items" :key="item.id" :class="[item.publish == 0 ? 'is-disabled' : '', 'list-item is-draggable']" data-icons="3">
              <div class="list-item-body">
                <strong>{{ item.title.de }}</strong>
                <PhPushPin v-if="item.sticky" :size="16" weight="light" title="Angeheftet" />
              </div>
              <ListActions :record="item" edit-route="news-edit" @toggle="news.toggle" @destroy="news.destroy" />
            </div>
          </SortableList>
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
import { PhPushPin } from '@phosphor-icons/vue';
import LoadingIndicator from '@/components/ui/LoadingIndicator.vue';
import ListActions from '@/components/ui/ListActions.vue';
import SortableList from '@/components/ui/SortableList.vue';
import { useListing } from '@/composables/useListing';
import { useOrder } from '@/composables/useOrder';

const isLoading = ref(false);
const news = reactive(useListing({ list: '/api/news/get', resource: 'news', isLoading }));
const order = useOrder({ url: '/api/news/order', key: 'news' });
</script>
