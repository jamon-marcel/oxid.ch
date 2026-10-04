const SEL = {
  root: '[data-dropdown="root"]',
  btn: '[data-dropdown="btn"]',
};

const ACTIVE = 'is-active';
const OPEN = 'is-open';

export function init() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest(SEL.btn);
    if (!btn) return;
    btn.classList.toggle(ACTIVE);
    document.querySelectorAll(SEL.root).forEach((el) => el.classList.toggle(OPEN));
  });
}
