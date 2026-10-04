import { createApp } from 'vue';
import Notifications from '@kyvg/vue3-notification';
import router from '@/router';
import http, { handleErrors } from '@/lib/http';
import App from '@/App.vue';

// vue-advanced-cropper 2 no longer injects its core styles
import 'vue-advanced-cropper/dist/style.css';

handleErrors(router);

const app = createApp(App);

// Screens not yet on <script setup> call this.axios
app.config.globalProperties.axios = http;

app.use(router).use(Notifications).mount('#app');
