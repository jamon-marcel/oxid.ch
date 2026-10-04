import { debounce, desktop, scrollToElement } from '../lib/utils.js';
import { close as closeMenu } from './menu.js';

const PERIOD = '[data-period]';
const ACTIVE = 'is-active';

// Mark the year links (href="#1984" …) for the current period
function markActive(period) {
  document.querySelectorAll('a[href^="#"]').forEach((a) => {
    a.classList.toggle(ACTIVE, a.getAttribute('href') === `#${period}`);
  });
}

function onHashChange() {
  const period = window.location.hash.slice(1);
  closeMenu();
  markActive(period);
  const el = document.querySelector(`[data-period="${CSS.escape(period)}"]`);
  if (el) scrollToElement(el);
}

// Desktop: the period in the upper half of the viewport becomes the hash
const spy = debounce(() => {
  document.querySelectorAll(PERIOD).forEach((el) => {
    const rect = el.getBoundingClientRect();
    if (rect.top <= window.innerHeight / 2 && rect.bottom >= 0) {
      const period = el.dataset.period;
      closeMenu();
      markActive(period);
      history.replaceState(null, '', `${window.location.pathname}#${period}`);
    }
  });
}, 50);

export function init() {
  if (!document.querySelector(PERIOD)) return;

  window.addEventListener('hashchange', onHashChange);

  if (desktop.matches) spy();
  window.addEventListener('scroll', () => {
    if (desktop.matches) spy();
  }, { passive: true });
}
