const SEL = {
  root: '[data-menu="root"]',
  btn: '[data-menu="btn"]',
  bar: '[data-menu="bar"]',
  parent: '[data-menu="parent"]',
};

const VISIBLE = 'is-visible';
const HIDDEN = 'is-hidden';
const HAS_MENU = 'has-menu';

const mobile = window.matchMedia('(max-width: 959px)');

function toggle() {
  document.querySelectorAll(SEL.root).forEach((el) => el.classList.toggle(VISIBLE));
  document.querySelectorAll(SEL.bar).forEach((el) => el.classList.toggle(HIDDEN));
  document.documentElement.classList.toggle(HAS_MENU);
}

// Close the menu if the user opened it
export function close() {
  if (document.documentElement.classList.contains(HAS_MENU)) toggle();
}

export function init() {
  document.addEventListener('click', (e) => {
    if (e.target.closest(SEL.btn)) {
      toggle();
      return;
    }

    // On mobile a top-level entry opens its sub menu instead of navigating
    const parent = e.target.closest(SEL.parent);
    if (parent && mobile.matches) {
      e.preventDefault();
      const sub = parent.nextElementSibling;
      if (sub?.matches('ul')) sub.classList.toggle(VISIBLE);
    }
  });
}
