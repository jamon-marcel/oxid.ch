import { notify } from '@kyvg/vue3-notification';
import http from '@/lib/http';
import { confirmDelete, translations } from '@/lib/utils';

/**
 * Document actions of a form whose record has a documents array. New files
 * are kept on the record and saved with it; delete and publish of saved
 * files go to the API right away.
 *
 * endpoint  resource path below /api, e.g. 'job'
 *           (DELETE document/destroy/{name}, GET document/status/{id})
 * fields    () => extra fields of a new file (e.g. language)
 */
export function useFiles({ record, isLoading, endpoint, fields = () => ({}) }) {
  const files = () => record.value.documents;
  const base = `/api/${endpoint}/document`;

  function store(upload) {
    files().push({ id: null, name: upload.name, caption: translations(), publish: 1, ...fields() });
  }

  async function destroy(file) {
    if (!confirmDelete()) {
      return;
    }
    isLoading.value = true;
    try {
      await http.delete(`${base}/destroy/${file.name}`);
      files().splice(files().indexOf(file), 1);
      notify({ type: 'success', text: 'Datei gelöscht' });
    }
    catch {
      // Notified by the http error handler
    }
    finally {
      isLoading.value = false;
    }
  }

  async function toggle(file) {
    if (file.id === null) {
      file.publish = file.publish == 1 ? 0 : 1;
      return;
    }
    isLoading.value = true;
    try {
      const { data } = await http.get(`${base}/status/${file.id}`);
      file.publish = data;
    }
    catch {
      // Notified by the http error handler
    }
    finally {
      isLoading.value = false;
    }
  }

  return { store, destroy, toggle };
}

/**
 * Uploader props for documents.
 */
export const fileUpload = {
  restrictions: 'pdf | max. 8 MB',
  acceptedFiles: '.pdf',
  maxFiles: 99,
  maxFilesize: 8,
};
