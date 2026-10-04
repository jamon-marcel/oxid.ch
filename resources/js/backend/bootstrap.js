import axios from 'axios';

// Auth is Sanctum's session cookie. axios sends the X-XSRF-TOKEN header from
// the XSRF-TOKEN cookie on same-origin requests by itself.
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios = axios;

export default axios;
