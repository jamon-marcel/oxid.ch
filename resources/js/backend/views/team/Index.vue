<template>
  <div class="container">
    <LoadingIndicator v-if="isLoading" />
    <main class="content" role="main">
      <div>
        <h1>Team</h1>
        <router-link :to="{ name: 'team-create' }" class="btn-add">
          <span>Hinzufügen</span>
        </router-link>
        <div class="list-items is-grouped" v-if="team.items.length">
          <template v-for="categoryId in displayOrder" :key="categoryId">
            <div v-if="groups[categoryId]">
              <h3 class="list-item-header">{{ categories[categoryId] }}</h3>
              <SortableList v-model="groups[categoryId]" @end="order">
                <div v-for="member in groups[categoryId]" :key="member.id" :class="[member.publish == 0 ? 'is-disabled' : '', 'list-item is-draggable']" data-icons="3">
                  <div class="list-item-body">{{ member.firstname }} {{ member.name }}</div>
                  <ListActions :record="member" edit-route="team-edit" @toggle="team.toggle" @destroy="team.destroy" />
                </div>
              </SortableList>
            </div>
          </template>
        </div>
        <div v-else-if="team.isFetched">
          <p>Es sind noch keine Teammitglieder vorhanden...</p>
        </div>
      </div>
    </main>
  </div>
</template>
<script setup>
import { ref, reactive } from 'vue';
import LoadingIndicator from '@/components/ui/LoadingIndicator.vue';
import ListActions from '@/components/ui/ListActions.vue';
import SortableList from '@/components/ui/SortableList.vue';
import { useListing } from '@/composables/useListing';
import { useOrder } from '@/composables/useOrder';
import { groupBy } from '@/lib/utils';
import http from '@/lib/http';

// Categories in this order, not by id
const displayOrder = [1, 4, 5, 2, 3];

const isLoading = ref(false);

// Members are ordered within their category; the API returns them grouped
const groups = ref({});
const team = reactive(useListing({
  list: '/api/teams/get',
  resource: 'team',
  isLoading,
  transform: data => Object.values(data).flat(),
  loaded: items => groups.value = groupBy(items, 'category'),
}));
const order = useOrder({ url: '/api/team/order', key: 'teams' });

const categories = ref({});
http.get('/api/settings/teamCategories').then(({ data }) => categories.value = data);
</script>
