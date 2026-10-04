<template>
  <div class="container">
    <LoadingIndicator v-if="isLoading" />
    <main class="content" role="main">
      <div>
        <h1>Kontakt</h1>
        <div class="list-items" v-if="contact.items.length">
          <div
            v-for="item in contact.items"
            :key="item.id"
            :class="[item.publish == 0 ? 'is-disabled' : '', 'list-item']"
            data-icons="1"
          >
            <div class="list-item-body">
              <div v-html="item.address.de"></div>
            </div>
            <ListActions :record="item" edit-route="contact-edit" :has-toggle="false" :has-destroy="false" />
          </div>
        </div>
        <div v-else-if="contact.isFetched">
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
const contact = reactive(useListing({ list: '/api/contact/get', resource: 'contact', isLoading }));
</script>
