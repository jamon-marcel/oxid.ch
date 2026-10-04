<template>
  <div class="container">
    <LoadingIndicator v-if="isLoading" />
    <main class="content" role="main">
      <div>
        <h1>Profil</h1>
        <div class="list-items" v-if="profile.items.length">
          <div
            v-for="item in profile.items"
            :key="item.id"
            :class="[item.publish == 0 ? 'is-disabled' : '', 'list-item']"
            data-icons="2"
          >
            <div class="list-item-body">{{ item.title.de }}</div>
            <ListActions :record="item" edit-route="profile-edit" :has-destroy="false" @toggle="profile.toggle" />
          </div>
        </div>
        <div v-else-if="profile.isFetched">
          <p>Es sind noch keine Inhalte vorhanden...</p>
        </div>
      </div>
    </main>
  </div>
</template>
<script setup>
import { ref, reactive } from 'vue';
import LoadingIndicator from '@/components/ui/LoadingIndicator.vue';
import ListActions from '@/components/ui/ListActions.vue';
import { useListing } from '@/composables/useListing';

const isLoading = ref(false);
const profile = reactive(useListing({ list: '/api/profile/get', resource: 'profile', isLoading }));
</script>
