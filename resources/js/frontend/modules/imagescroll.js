import { sections } from '../lib/sections.js';

// Image lists on the office pages
export function init() {
  sections({
    item: '[data-imagescroll="item"]',
    prev: '[data-imagescroll="prev"]',
    next: '[data-imagescroll="next"]',
  });
}
