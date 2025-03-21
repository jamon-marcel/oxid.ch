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

// Flag to prevent multiple refresh attempts
let isRefreshing = false;
// Store for callbacks to be executed after token refresh
let refreshSubscribers = [];

// Helper to push callbacks to the subscriber array
const subscribeTokenRefresh = (callback) => {
  refreshSubscribers.push(callback);
};

// Helper to notify all subscribers about the new token
const onRefreshed = (token) => {
  refreshSubscribers.forEach(callback => callback(token));
  refreshSubscribers = [];
};

// Set up router guards
router.beforeEach(async (to, from, next) => {
  // Check if the route requires authentication
  if (to.matched.some(route => route.meta.requiresAuth)) {
    // Check if user is logged in
    if (!localStorage.getItem('token')) {
      // Not logged in, redirect to login
      next({ name: 'login' });
      return;
    }
    
    // Ensure token is valid before proceeding to protected route
    try {
      // Make sure the Authorization header is set correctly
      const token = localStorage.getItem('token');
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      
      // Validate token
      await axios.post('/api/auth/me', {}, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      
      // Token is valid, proceed
      next();
    } catch (error) {
      // If token validation fails with 401, let the interceptor handle it
      if (error.response && error.response.status === 401) {
        // If we're already on a route that requires auth, try to refresh the token
        try {
          // Try to refresh the token
          const response = await axios.post('/api/auth/refresh', {}, {
            headers: {
              'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
          });
          
          const newToken = response.data.access_token;
          
          // Update token in localStorage
          localStorage.setItem('token', newToken);
          
          // Update axios default headers with the new token
          axios.defaults.headers.common['Authorization'] = 'Bearer ' + newToken;
          
          // Token refreshed, proceed to route
          next();
        } catch (refreshError) {
          // Refresh failed, redirect to login
          localStorage.removeItem('token');
          store.commit('logoutUser');
          next({ name: 'login' });
        }
      } else {
        // Other error, redirect to login
        localStorage.removeItem('token');
        store.commit('logoutUser');
        next({ name: 'login' });
      }
    }
  } else {
    // If logged in redirect to dashboard when trying to access login
    if (to.path === '/admin/login' && localStorage.getItem('token')) {
      next({ name: 'dashboard' });
      return;
    }
    next();
  }
});

// Intercept Axios Response (401, Unauthorized)
axios.interceptors.response.use(
  response => response,
  async error => {
    if (error.response && error.response.status === 401) {
      const originalRequest = error.config;
      
      // Check if the error is due to a blacklisted token
      const isBlacklisted = error.response.data && 
                           (error.response.data.message === "The token has been blacklisted" ||
                            (error.response.data.exception && 
                             error.response.data.exception.includes('TokenBlacklistedException')));
      
      // If token is blacklisted, clear everything and redirect to login
      if (isBlacklisted) {
        localStorage.removeItem('token');
        store.commit('logoutUser');
        router.push({ name: 'login' });
        return Promise.reject(error);
      }
      
      // Don't retry if this is already a refresh token request or login request
      if (!originalRequest._retry && 
          !originalRequest.url.includes('/api/auth/refresh') && 
          !originalRequest.url.includes('/api/auth/login')) {
        
        // If we're already refreshing, queue this request
        if (isRefreshing) {
          return new Promise(resolve => {
            subscribeTokenRefresh(token => {
              // Replace the expired token and retry
              originalRequest.headers['Authorization'] = 'Bearer ' + token;
              resolve(axios(originalRequest));
            });
          });
        }
        
        originalRequest._retry = true;
        isRefreshing = true;
        
        try {
          // Try to refresh the token
          const response = await axios.post('/api/auth/refresh', {}, {
            headers: {
              'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
          });
          
          const newToken = response.data.access_token;
          
          // Update token in localStorage
          localStorage.setItem('token', newToken);
          
          // Update axios default headers with the new token
          axios.defaults.headers.common['Authorization'] = 'Bearer ' + newToken;
          
          // Notify all subscribers about the new token
          onRefreshed(newToken);
          isRefreshing = false;
          
          // Update the authorization header for the original request
          originalRequest.headers['Authorization'] = 'Bearer ' + newToken;
          
          // Retry the original request with the new token
          return axios(originalRequest);
        } catch (refreshError) {
          // Clear token and redirect to login
          localStorage.removeItem('token');
          store.commit('logoutUser');
          isRefreshing = false;
          
          // Reject all queued requests
          refreshSubscribers = [];
          
          router.push({ name: 'login' });
          return Promise.reject(refreshError);
        }
      } else {
        // If this is already a retry or a refresh/login request that failed
        localStorage.removeItem('token');
        store.commit('logoutUser');
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