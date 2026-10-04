// Same breakpoint as bp-sm() in the Sass
export const desktop = window.matchMedia('(min-width: 960px)');

export function debounce(fn, wait) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), wait);
  };
}

export function scrollToY(top) {
  window.scrollTo({ top, behavior: 'smooth' });
}

export function scrollToElement(el, offset = 0) {
  scrollToY(el.getBoundingClientRect().top + window.scrollY + offset);
}
