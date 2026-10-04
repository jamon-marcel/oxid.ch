import { desktop } from '../lib/utils.js';
import { sections } from '../lib/sections.js';
import { close as closeMenu } from './menu.js';

const SEL = {
  grid: '[data-project="grid"]',
  prev: '[data-project="prev"]',
  next: '[data-project="next"]',
  index: '[data-project="index"]',
  menuItem: '[data-project-id]',
};

const ACTIVE = 'is-active';
const VISIBLE = 'is-visible';

const teaser = (item) => document.querySelector(`[data-project-teaser="${item.dataset.projectId}"]`);

export function init() {
  sections({
    item: SEL.grid,
    prev: SEL.prev,
    next: SEL.next,
    onChange: (index) => {
      document.querySelectorAll(SEL.index).forEach((el) => { el.textContent = index; });
    },
    onStep: closeMenu,
  });

  // Desktop: hovering a project in the menu shows its teaser image
  document.querySelectorAll(SEL.menuItem).forEach((item) => {
    item.addEventListener('mouseenter', () => {
      if (desktop.matches && !item.classList.contains(ACTIVE)) teaser(item)?.classList.add(VISIBLE);
    });
    item.addEventListener('mouseleave', () => {
      if (desktop.matches) teaser(item)?.classList.remove(VISIBLE);
    });
  });
}
