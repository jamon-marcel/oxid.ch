window._ = require('lodash');

window.moment = require('moment');


/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// import, not require: require() resolves to axios's CJS build, a second
// instance without the interceptors app.js registers
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Auth is Sanctum's session cookie. axios sends the X-XSRF-TOKEN header from
// the XSRF-TOKEN cookie on same-origin requests by itself.
