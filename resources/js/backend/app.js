/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// Import Vue
import Vue from 'vue';
window.Vue = Vue;

// VueAxios
import VueAxios from 'vue-axios';
import axios from 'axios';
Vue.use(VueAxios, axios);

// Store
import store from './store';

// Routes
import routes from './routes';

// VueRouter
import VueRouter from 'vue-router';
Vue.use(VueRouter);

// Import notifications
import Notifications from 'vue-notification';
Vue.use(Notifications);

// Import and configure Vue Moment
import moment from 'moment';
import VueMoment from 'vue-moment';
Vue.use(VueMoment, { moment });

// Filters
require('./filters');

// Set up VueRouter
const router = new VueRouter({ mode: 'history', routes: routes});

// Set up router guards: protected routes need a live session
router.beforeEach(async (to, from, next) => {
  const requiresAuth = to.matched.some(route => route.meta.requiresAuth);

  if (!requiresAuth && to.name !== 'login') {
    next();
    return;
  }

  try {
    await axios.post('/api/auth/me');
    store.commit('loginUser');
    next(to.name === 'login' ? { name: 'dashboard' } : undefined);
  } catch (error) {
    store.commit('logoutUser');
    next(requiresAuth ? { name: 'login' } : undefined);
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
      store.commit('logoutUser');
      if (router.currentRoute.name !== 'login') {
        router.push({ name: 'login' });
      }
    }
    return Promise.reject(error);
  }
);

// Mount App
import AppComponent from '@/components/App.vue';

// Create the Vue instance
new Vue({
  el: '#app',
  components: { AppComponent },
  router,
  store,
  render: h => h(AppComponent)
});