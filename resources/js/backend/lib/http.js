import axios from 'axios';
import { notify } from '@/lib/notify';

// Same-origin SPA on Sanctum's session cookie: axios sends X-XSRF-TOKEN from
// the XSRF-TOKEN cookie by itself.
const http = axios.create({
  headers: { 'X-Requested-With': 'XMLHttpRequest' },
});

/**
 * App-wide handling of failed requests. Callers still get the rejection
 * (forms read 422 errors via validationErrors) but need not notify.
 * Auth calls handle their own errors.
 */
export function handleErrors(router) {
  http.interceptors.response.use(response => response, error => {
    const response = error.response;
    // Auth calls handle their own errors; so does a caller that passes
    // { handleErrors: false } (the uploader shows the API's message)
    if ((error.config?.url ?? '').includes('/api/auth/') || error.config?.handleErrors === false) {
      return Promise.reject(error);
    }

    switch (response?.status) {
      // Session gone (expired, or logged out elsewhere); 419 when the CSRF
      // token expired with it
      case 401:
      case 419:
        if (router.currentRoute.value.name !== 'login') {
          router.push({ name: 'login' });
        }
        break;
      case 404:
        notify({ type: 'error', text: '404 Not Found' });
        break;
      case 422:
        notify({ type: 'error', text: 'Bitte markierte Felder prüfen!' });
        window.scrollTo({ top: 0, behavior: 'smooth' });
        break;
      case 500:
        notify({ type: 'error', text: `500 Internal Server Error ${response.data?.message ?? ''}` });
        break;
    }

    return Promise.reject(error);
  });
}

/**
 * Fields that failed validation, as { 'title.de': true }. The API returns
 * Laravel's default errors: { 'title.de': ['Title is required!'] }.
 */
export function validationErrors(error) {
  if (error.response?.status !== 422) {
    return null;
  }
  const fields = {};
  Object.keys(error.response.data.errors ?? {}).forEach(field => fields[field] = true);
  return fields;
}

export default http;
