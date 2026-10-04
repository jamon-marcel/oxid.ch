/**
 * "2026-10-04" (or an ISO timestamp) → "04.10.2026". Empty stays empty;
 * moment used to show "Invalid date" for a missing value.
 */
export function formatDate(value) {
  const match = /^(\d{4})[-.](\d{2})[-.](\d{2})/.exec(value || '');
  return match ? `${match[3]}.${match[2]}.${match[1]}` : null;
}
