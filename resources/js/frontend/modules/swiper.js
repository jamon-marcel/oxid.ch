import Swiper from 'swiper';
import { desktop } from '../lib/utils.js';

// Buttons that switch between is-dark and is-light with the slide's theme
const THEMED = '[data-swiper="themed"]';

let swiper;

function enable() {
  swiper = new Swiper('.swiper-container', {
    speed: 600,
    autoHeight: false,
    autoplay: false,
    loop: true,
    navigation: {
      nextEl: '.swiper-btn-next',
      prevEl: '.swiper-btn-prev',
    },
    on: {
      transitionEnd() {
        const dark = this.slides[this.activeIndex]?.dataset.theme === '0';
        document.querySelectorAll(THEMED).forEach((el) => {
          el.classList.toggle('is-dark', dark);
          el.classList.toggle('is-light', !dark);
        });
      },
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
  if (!document.querySelector('.swiper-container')) return;
  watch();
  desktop.addEventListener('change', watch);
}
