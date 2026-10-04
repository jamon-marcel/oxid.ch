import jQuery from 'jquery';
import axios from 'axios';

// The modules use the jQuery global
window.$ = window.jQuery = jQuery;

window.axios = axios;
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}
