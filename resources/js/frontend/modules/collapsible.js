import { scrollToY } from '../lib/utils.js';

const SEL = {
  root: '[data-collapsible="root"]',
  body: '[data-collapsible="body"]',
  btn: '[data-collapsible="btn"]',
};

const EXPANDED = 'is-expanded';

function setExpanded(root, expanded) {
  root.classList.toggle(EXPANDED, expanded);
  root.querySelectorAll(SEL.body).forEach((body) => { body.hidden = !expanded; });
  root.querySelectorAll(SEL.btn).forEach((btn) => btn.setAttribute('aria-expanded', String(expanded)));
}

function toggle(btn) {
  const root = btn.closest(SEL.root);
  if (!root) return;

  const expand = !root.classList.contains(EXPANDED);
  if (expand) {
    scrollToY(root.getBoundingClientRect().top + window.scrollY - 20);
  }
  setExpanded(root, expand);
}

// Collapse the collapsible that contains `el`
export function hide(el) {
  const root = el.closest(SEL.root);
  if (root) setExpanded(root, false);
}

export function expandAll() {
  document.querySelectorAll(SEL.root).forEach((root) => setExpanded(root, true));
}

export function init() {
  document.querySelectorAll(SEL.root).forEach((root) => {
    const expanded = root.classList.contains(EXPANDED);
    root.querySelectorAll(SEL.btn).forEach((btn) => btn.setAttribute('aria-expanded', String(expanded)));
  });

  document.addEventListener('click', (e) => {
    const btn = e.target.closest(SEL.btn);
    if (btn) toggle(btn);
  });
}
