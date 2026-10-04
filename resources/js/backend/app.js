import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import Notifications from '@kyvg/vue3-notification';
import axios from './bootstrap';
import store from './store';
import routes from './routes';
import App from '@/components/App.vue';

// vue-advanced-cropper 2 no longer injects its core styles
import 'vue-advanced-cropper/dist/style.css';

const router = createRouter({ history: createWebHistory(), routes });

// Protected routes need a live session
router.beforeEach(async (to) => {
  const requiresAuth = to.matched.some(route => route.meta.requiresAuth);

  if (!requiresAuth && to.name !== 'login') {
    return true;
  }

  try {
    await axios.post('/api/auth/me');
    store.isLoggedIn = true;
    return to.name === 'login' ? { name: 'dashboard' } : true;
  }
  catch (error) {
    store.isLoggedIn = false;
    return requiresAuth ? { name: 'login' } : true;
  }
});

// Session gone (expired or logged out elsewhere): 401, or 419 because the
// CSRF token expired with it. Auth calls handle their own errors.
axios.interceptors.response.use(
  response => response,
  error => {
    const status = error.response && error.response.status;
    const url = (error.config && error.config.url) || '';

    if ((status === 401 || status === 419) && !url.includes('/api/auth/')) {
      store.isLoggedIn = false;
      if (router.currentRoute.value.name !== 'login') {
        router.push({ name: 'login' });
      }
    }
    return Promise.reject(error);
  }
);

const app = createApp(App);

// Components call this.axios, as they did with vue-axios
app.config.globalProperties.axios = axios;

app.use(router).use(Notifications).mount('#app');
