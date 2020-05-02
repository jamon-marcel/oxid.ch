<template>
  <div>
    <page-header/>
    <notifications classes="notification"/>
    <div class="container">
      <main class="content" role="main">
        <div>
          <h1>Kontakt</h1>
          <div class="list-items" v-if="contact">
            <div
              :class="[c.publish == 0 ? 'is-disabled' : '', 'list-item']"
              v-for="c in contact"
              :key="c.id"
              data-icons="1"
            >
              <div class="list-item-body">
                <div v-html="c.address.de">{{ c.address.de }}</div>
              </div>
              <div class="list-item-action" data-icons="1">
                <router-link
                  :to="{name: 'contact-edit', params: { id: c.id }}"
                  class="icon-edit icon-mini"
                ></router-link>
              </div>
            </div>
          </div>
          <div v-else>
            <p>Es sind noch keine Inhalte vorhanden...</p>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
<script>
import PageHeader from "@/layout/PageHeader.vue";
import Progress from "@/mixins/progress";

export default {
  components: {
    PageHeader: PageHeader,
  },

  mixins: [Progress],

  data() {
    return {
      contact: [],
    };
  },

  created() {
    this.fetch();
  },

  methods: {
    fetch() {
      this.axios.get(`/api/contact/get`).then(response => {
        this.contact = response.data;
      });
    },
  },
};
</script>