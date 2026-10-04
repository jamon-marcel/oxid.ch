import { debounce, desktop, scrollToElement } from './utils.js';

// Full-height sections stepped through with prev/next buttons. On desktop the
// current section follows the scroll position and the buttons hide at either
// end; below that the Sass hides the buttons anyway.
export function sections({ item, prev, next, onChange = () => {}, onStep = () => {} }) {
  const items = [...document.querySelectorAll(item)];
  const total = items.length;
  if (!total) return;

  // 1-based, like the "1/14" counter on project pages
  let index = 1;

  const step = (i) => {
    if (items[i]) scrollToElement(items[i]);
    onStep();
  };

  const update = debounce(() => {
    const height = document.documentElement.scrollHeight / total;
    // Switch in the middle between two sections
    index = Math.floor((window.scrollY + height / 2) / height) + 1;
    onChange(index);
    document.querySelectorAll(next).forEach((btn) => { btn.hidden = index === total; });
    document.querySelectorAll(prev).forEach((btn) => { btn.hidden = index === 1; });
  }, 50);

  document.addEventListener('click', (e) => {
    if (e.target.closest(next)) step(index);
    if (e.target.closest(prev)) step(index - 2);
  });

  if (desktop.matches) update();
  window.addEventListener('scroll', () => {
    if (desktop.matches) update();
  }, { passive: true });
}
