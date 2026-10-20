import { confirm } from '@/lib/confirm';

/**
 * Asks before deleting (the confirm dialog); resolves true or false.
 */
export function confirmDelete(title = 'Eintrag löschen', options = {}) {
  return confirm({ title, ...options });
}

/**
 * Sets each item's order to its index and returns the list.
 */
export function withOrder(items) {
  items.forEach((item, index) => item.order = index);
  return items;
}

/**
 * "2026-10-04" (or "2026.10.04", or an ISO timestamp) → "04.10.2026".
 * Empty stays empty.
 */
export function formatDate(value) {
  const match = /^(\d{4})[-.](\d{2})[-.](\d{2})/.exec(value || '');
  return match ? `${match[3]}.${match[2]}.${match[1]}` : null;
}

/**
 * Typing aid for dd.mm.yyyy: digits only, dots added. "01062020" → "01.06.2020".
 */
export function dateMask(value) {
  const digits = (value || '').replace(/\D/g, '').slice(0, 8);
  return [digits.slice(0, 2), digits.slice(2, 4), digits.slice(4)].filter(Boolean).join('.');
}

/**
 * { field: true } for each empty field; paths like 'title.de'.
 */
export function requiredErrors(record, paths) {
  const errors = {};
  paths.forEach(path => {
    const value = path.split('.').reduce((object, key) => object?.[key], record);
    if (value === null || value === undefined || value === '' || (Array.isArray(value) && !value.length)) {
      errors[path] = true;
    }
  });
  return errors;
}

/**
 * An upload's name without the uniqid prefix the server adds:
 * "6a97cc421059b_01-situation.png" → "01-situation.png".
 */
export function displayName(name) {
  return (name ?? '').replace(/^[0-9a-f]{13}_/, '');
}

/**
 * { de: null, en: null }
 */
export function translations() {
  return { de: null, en: null };
}

/**
 * Plain text of an HTML string (tags out, entities decoded), cut to length
 * (list cells). DOMParser doesn't run the HTML's scripts or handlers.
 */
export function truncate(html, length = 100) {
  const text = new DOMParser().parseFromString(html ?? '', 'text/html').body.textContent.replace(/\s+/g, ' ').trim();
  return text.length > length ? `${text.slice(0, length)}…` : text;
}

/**
 * { id: label } (the settings endpoints) → [{ value: Number(id), label }]
 * for a select.
 */
export function toOptions(settings) {
  return Object.entries(settings ?? {}).map(([id, label]) => ({ value: Number(id), label }));
}
