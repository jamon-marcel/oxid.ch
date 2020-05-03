/**
 * Dependencies
 */
import Swiper from '../vendor/swiper.js';

var SwiperUi = (function() {

  var mySwiper;
     
  // media queries
  var bp = window.matchMedia( '(min-width:960px)' );

  var _initialize = function() {
    _bind();
  };

  var _bind = function() {
    _watch();
    bp.addListener(_watch);
  };

  var _watch = function() {
    if (bp.matches === true ) {
      return _enable();
    } 
    else if (bp.matches === false) {
      if ( mySwiper !== undefined ) mySwiper.destroy( true, true );
      return;
    }
 };

  var _enable = function() {
    mySwiper = new Swiper('.swiper-container', {
      speed: 600,
      autoHeight: false,
      autoplay: false,
      loop: true,
      navigation: {
        nextEl: '.swiper-btn-next',
        prevEl: '.swiper-btn-prev'
      }
    });  
  };
  
  return {
    init:  _initialize,
  };
	
})();

// Initialize
$(function() {
  if ($('body').find('.swiper-container').length) {
    console.log('d');
    SwiperUi.init();
  }
});