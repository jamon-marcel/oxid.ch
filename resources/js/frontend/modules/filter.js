import * as collapsible from './collapsible.js';

const SEL = {
  btn: '[data-filter="btn"]',
  item: '[data-filter="item"]',
  group: '[data-filter="group"]',
};

const ACTIVE = 'is-active';

function filter(btn) {
  const type = btn.dataset.filterValue;

  document.querySelectorAll(SEL.btn).forEach((el) => el.classList.remove(ACTIVE));
  btn.classList.add(ACTIVE);

  const items = document.querySelectorAll(SEL.item);
  if (type === 'all') {
    items.forEach((item) => { item.hidden = false; });
    return;
  }

  // Items carry data-filter-wood="1" etc.
  items.forEach((item) => { item.hidden = item.getAttribute(`data-filter-${type}`) !== '1'; });

  // Works list: open every group, then close the ones left empty
  collapsible.expandAll();
  document.querySelectorAll(SEL.group).forEach((group) => {
    const visible = [...group.querySelectorAll(SEL.item)].some((item) => !item.hidden);
    if (!visible) collapsible.hide(group);
  });
}

export function init() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest(SEL.btn);
    if (btn) filter(btn);
  });
}
