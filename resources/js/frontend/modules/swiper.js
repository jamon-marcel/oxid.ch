import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import { desktop } from '../lib/utils.js';

// Buttons that switch between is-dark and is-light with the slide's theme
const THEMED = '[data-swiper="themed"]';

let swiper;

function applyTheme(s) {
  const dark = s.slides[s.activeIndex]?.dataset.theme === '0';
  document.querySelectorAll(THEMED).forEach((el) => {
    el.classList.toggle('is-dark', dark);
    el.classList.toggle('is-light', !dark);
  });
}

function enable() {
  swiper = new Swiper('.swiper', {
    modules: [Navigation],
    speed: 600,
    loop: true,
    navigation: {
      nextEl: '.swiper-btn-next',
      prevEl: '.swiper-btn-prev',
    },
    on: {
      init: applyTheme,
      transitionEnd: applyTheme,
    },
  });
}

// Slider on desktop, a plain image list below
function watch() {
  if (desktop.matches) {
    enable();
  } else if (swiper) {
    swiper.destroy(true, true);
    swiper = undefined;
  }
}

export function init() {
  if (!document.querySelector('.swiper')) return;
  watch();
  desktop.addEventListener('change', watch);
}
