export function confirmDelete() {
  return window.confirm('Bitte löschen bestätigen!');
}

/**
 * Items grouped by the value of key, keeping the item objects (so changes
 * to an item show in every list that holds it).
 */
export function groupBy(items, key) {
  return items.reduce((groups, item) => {
    (groups[item[key]] ??= []).push(item);
    return groups;
  }, {});
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
