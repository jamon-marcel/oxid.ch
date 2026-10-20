import { reactive } from 'vue';

/**
 * The confirm dialog, in place of window.confirm. Shown by ConfirmDialog
 * (in App.vue); resolves true when confirmed, false when cancelled.
 *
 *   if (await confirm({ title: 'Zeile löschen', text: '…' })) { … }
 */
export const dialog = reactive({
  open: false,
  title: '',
  text: '',
  action: '',
});

let settle = null;

export function confirm({ title, text = 'Wirklich löschen?', action = 'Löschen' }) {
  // A second call answers the first with "no"
  settle?.(false);
  Object.assign(dialog, { open: true, title, text, action });

  return new Promise((resolve) => settle = resolve);
}

export function answer(confirmed) {
  dialog.open = false;
  settle?.(confirmed);
  settle = null;
}
