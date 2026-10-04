import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { notify } from '@/lib/notify';
import http, { validationErrors } from '@/lib/http';

/**
 * Create/edit form for an API resource.
 *
 * type      'create' | 'edit' (route prop)
 * endpoint  resource path below /api, e.g. 'news'
 *           (GET edit/{id}, POST create, POST update/{id})
 * model     () => empty record; in edit mode, null values from the API
 *           fall back to these defaults (e.g. a missing translation object)
 * redirect  route to go to after saving
 * titles    { create, edit }
 * load      extra requests (() => Promise) the form waits for
 * loaded    (record) => void, after the record is loaded (e.g. reformat a date)
 * validate  (record) => { field: true } for checks the API doesn't do
 */
export function useResourceForm({ type, endpoint, model, redirect, titles, load = [], loaded = () => {}, validate = () => ({}) }) {
  const route = useRoute();
  const router = useRouter();

  const isEdit = type === 'edit';

  const record = ref(model());
  const errors = ref({});
  const isLoading = ref(false);
  const isFetched = ref(false);
  const title = computed(() => isEdit ? titles.edit : titles.create);

  async function fetch() {
    const { data } = await http.get(`/api/${endpoint}/edit/${route.params.id}`);
    const defaults = model();
    for (const key in defaults) {
      data[key] ??= defaults[key];
    }
    loaded(data);
    record.value = data;
  }

  async function init() {
    isLoading.value = true;
    try {
      await Promise.all([isEdit ? fetch() : null, ...load.map(request => request())]);
      isFetched.value = true;
    }
    catch {
      // Notified by the http error handler
    }
    finally {
      isLoading.value = false;
    }
  }

  async function submit() {
    const invalid = validate(record.value);
    if (Object.keys(invalid).length) {
      errors.value = invalid;
      notify({ type: 'error', text: 'Bitte markierte Felder prüfen!' });
      window.scrollTo({ top: 0, behavior: 'smooth' });
      return;
    }

    isLoading.value = true;
    try {
      const url = isEdit ? `/api/${endpoint}/update/${route.params.id}` : `/api/${endpoint}/create`;
      await http.post(url, record.value);
      errors.value = {};
      router.push(redirect);
      notify({ type: 'success', text: isEdit ? 'Änderungen gespeichert!' : 'Daten erfasst!' });
    }
    catch (error) {
      errors.value = validationErrors(error) ?? errors.value;
    }
    finally {
      isLoading.value = false;
    }
  }

  // A field's error goes away once it gets focus
  function clearError(field) {
    delete errors.value[field];
  }

  init();

  return { record, errors, isEdit, isLoading, isFetched, title, submit, clearError };
}
