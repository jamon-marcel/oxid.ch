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
          <draggable
            v-model="jobs.items"
            item-key="id"
            ghost-class="draggable-ghost"
            draggable=".list-item"
            @end="order(jobs.items)"
          >
            <template #item="{ element: job }">
              <div :class="[job.publish == 0 ? 'is-disabled' : '', 'list-item is-draggable']" data-icons="3">
                <div class="list-item-body">{{ job.title.de }}</div>
                <ListActions :record="job" edit-route="job-edit" @toggle="jobs.toggle" @destroy="jobs.destroy" />
              </div>
            </template>
          </draggable>
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
import draggable from 'vuedraggable';
import LoadingIndicator from '@/components/ui/LoadingIndicator.vue';
import ListActions from '@/components/ui/ListActions.vue';
import { useListing } from '@/composables/useListing';
import { useOrder } from '@/composables/useOrder';

const isLoading = ref(false);
const jobs = reactive(useListing({ list: '/api/jobs/get', resource: 'job', isLoading }));
const order = useOrder({ url: '/api/job/order', key: 'jobs' });
</script>
