<template>
  <div class="container">
    <LoadingIndicator v-if="isLoading" />
    <main class="content" role="main">
      <div>
        <h1>Jobs</h1>
        <router-link :to="{ name: 'job-create' }" class="btn-add">
          <span>Hinzufügen</span>
        </router-link>
        <div class="list-items" v-if="jobs.items.length">
          <SortableList v-model="jobs.items" @end="order">
            <div v-for="job in jobs.items" :key="job.id" :class="[job.publish == 0 ? 'is-disabled' : '', 'list-item is-draggable']" data-icons="3">
              <div class="list-item-body">{{ job.title.de }}</div>
              <ListActions :record="job" edit-route="job-edit" @toggle="jobs.toggle" @destroy="jobs.destroy" />
            </div>
          </SortableList>
        </div>
        <div v-else-if="jobs.isFetched">
          <p>Es sind noch keine Jobs vorhanden...</p>
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

const isLoading = ref(false);
const jobs = reactive(useListing({ list: '/api/jobs/get', resource: 'job', isLoading }));
const order = useOrder({ url: '/api/job/order', key: 'jobs' });
</script>
