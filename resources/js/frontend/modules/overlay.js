import { desktop } from '../lib/utils.js';

const SEL = {
  root: '[data-overlay="root"]',
  btn: '[data-overlay="btn"]',
};

const ACTIVE = 'is-active';
const VISIBLE = 'is-visible';

function toggle(btn) {
  btn.classList.toggle(ACTIVE);
  document.querySelectorAll(SEL.root).forEach((el) => el.classList.toggle(VISIBLE));
}

function hide() {
  document.querySelectorAll(SEL.btn).forEach((el) => el.classList.remove(ACTIVE));
  document.querySelectorAll(SEL.root).forEach((el) => el.classList.remove(VISIBLE));
}

export function init() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest(SEL.btn);
    if (btn) toggle(btn);
  });

  // The office pages open with the info shown, on desktop only
  const root = document.querySelector(SEL.root);
  if (root && desktop.matches && root.dataset.visibleOnload === '1') {
    root.classList.add(VISIBLE);
  }

  document.addEventListener('keyup', (e) => {
    if (e.key === 'Escape') hide();
  });
}
