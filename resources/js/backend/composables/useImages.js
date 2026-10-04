import { notify } from '@kyvg/vue3-notification';
import http from '@/lib/http';
import { confirmDelete, translations } from '@/lib/utils';

/**
 * Image actions of a form whose record has an images array (project,
 * discourse). New images are kept on the record and saved with it; delete,
 * publish and crop of saved images go to the API right away.
 *
 * endpoint  resource path below /api, e.g. 'project'
 *           (DELETE image/destroy/{name}, GET image/status/{id},
 *            POST image/coords/{id})
 * fields    () => extra fields of a new image (preview flags, …)
 */
export function useImages({ record, isLoading, endpoint, fields = () => ({}) }) {
  const images = () => record.value.images;
  const base = `/api/${endpoint}/image`;

  function store(upload) {
    images().push({
      id: null,
      name: upload.name,
      caption: translations(),
      coords_w: 0,
      coords_h: 0,
      coords_x: 0,
      coords_y: 0,
      orientation: upload.orientation,
      order: -1,
      publish: 1,
      ...fields(),
    });
  }

  async function destroy(image) {
    if (!confirmDelete()) {
      return;
    }
    isLoading.value = true;
    try {
      await http.delete(`${base}/destroy/${image.name}`);
      images().splice(images().indexOf(image), 1);
    }
    catch {
      // Notified by the http error handler
    }
    finally {
      isLoading.value = false;
    }
  }

  async function toggle(image) {
    if (image.id === null) {
      image.publish = image.publish == 1 ? 0 : 1;
      return;
    }
    isLoading.value = true;
    try {
      const { data } = await http.get(`${base}/status/${image.id}`);
      image.publish = data;
    }
    catch {
      // Notified by the http error handler
    }
    finally {
      isLoading.value = false;
    }
  }

  // The coords are already set on the image; unsaved images keep them
  // until the record is saved
  async function saveCoords(image) {
    if (image.id === null) {
      return;
    }
    isLoading.value = true;
    try {
      await http.post(`${base}/coords/${image.id}`, image);
      notify({ type: 'success', text: 'Änderungen gespeichert!' });
    }
    catch {
      // Notified by the http error handler
    }
    finally {
      isLoading.value = false;
    }
  }

  return { store, destroy, toggle, saveCoords };
}

/**
 * Uploader props for images.
 */
export const imageUpload = {
  restrictions: 'jpg, png | max. 8 MB',
  acceptedFiles: '.png,.jpg',
  maxFiles: 99,
  maxFilesize: 8,
};
