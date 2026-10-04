import { ref } from 'vue';
import { notify } from '@kyvg/vue3-notification';
import http from '@/lib/http';
import { confirmDelete, translations } from '@/lib/utils';

/**
 * A standalone image page (home, team, jobs, profile): every change is
 * saved right away.
 *
 * endpoint  e.g. 'team' (GET images/get, POST image/create,
 *           POST image/update/{id}, POST image/coords/{id},
 *           GET image/status/{id}, DELETE image/destroy/{name})
 */
export function useImageLibrary({ endpoint, isLoading = ref(false) }) {
  const images = ref([]);
  const isFetched = ref(false);
  const base = `/api/${endpoint}/image`;

  async function request(call, success) {
    isLoading.value = true;
    try {
      const result = await call();
      if (success) {
        notify({ type: 'success', text: success });
      }
      return result;
    }
    catch {
      // Notified by the http error handler
    }
    finally {
      isLoading.value = false;
    }
  }

  async function fetch() {
    const response = await request(() => http.get(`/api/${endpoint}/images/get`));
    if (response) {
      images.value = response.data.data;
      isFetched.value = true;
    }
  }

  async function store(upload) {
    const image = { name: upload.name, caption: translations(), publish: 0 };
    const response = await request(() => http.post(`${base}/create`, image), 'Bild gespeichert!');
    if (response) {
      images.value.push({ ...image, id: response.data.imageId, orientation: upload.orientation });
    }
  }

  async function destroy(image) {
    if (!confirmDelete()) {
      return;
    }
    if (await request(() => http.delete(`${base}/destroy/${image.name}`))) {
      images.value.splice(images.value.indexOf(image), 1);
    }
  }

  async function toggle(image) {
    const response = await request(() => http.get(`${base}/status/${image.id}`));
    if (response) {
      image.publish = response.data;
    }
  }

  function update(image) {
    return request(() => http.post(`${base}/update/${image.id}`, image), 'Änderungen gespeichert!');
  }

  function saveCoords(image) {
    return request(() => http.post(`${base}/coords/${image.id}`, image), 'Änderungen gespeichert!');
  }

  fetch();

  return { images, isFetched, isLoading, store, destroy, toggle, update, saveCoords };
}
