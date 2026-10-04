import { reactive } from 'vue';

/**
 * Toast messages, in place of @kyvg/vue3-notification.
 * notify({ type: 'success' | 'error' | 'warn', text }); shown for 3 s.
 */
export const notifications = reactive([]);

let id = 0;

export function notify({ type = 'success', text, duration = 3000 }) {
  const notification = { id: ++id, type, text };
  notifications.push(notification);
  setTimeout(() => dismiss(notification), duration);
}

export function dismiss(notification) {
  const index = notifications.indexOf(notification);
  if (index !== -1) {
    notifications.splice(index, 1);
  }
}
