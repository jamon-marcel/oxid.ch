function debounce(a,b,c){var d;return function(){var e=this,f=arguments;clearTimeout(d),d=setTimeout(function(){d=null,c||a.apply(e,f)},b),c&&!d&&a.apply(e,f)}}

var ImageScroll = (function() {

  // selectors
  var selectors = {
    html:       'html',
    body:       'body',
    item:     '.js-scroll-item',
    btnNext:    '.js-btn-scroll-next',
    btnPrev:    '.js-btn-scroll-prev',
  };

  // css classes
  var classes = {
    visible: 'is-visible',
    active: 'is-active',
  };

  // media queries
  var mq = {
    sm: window.matchMedia("(min-width: 960px)"),
  };

  var totalItems = $(selectors.body).find(selectors.item).length;
  var index = 1;
   
  var _initialize = function() {
    _bind();
  };

  var _bind = function() {
    $(selectors.body).on('click', selectors.btnNext, function(){
      _scrollToNext();
    });

    $(selectors.body).on('click', selectors.btnPrev, function(){
      _scrollToPrev();
    });

    if (mq.sm.matches) {
      _scroll();
    }

    $(window).scroll(function(event){
      if (mq.sm.matches) {
        _scroll();
      }
    });
  };

  var _scrollToNext = function(){
    $.scrollTo('.js-scroll-item:eq('+index+')', 400);
  };

  var _scrollToPrev = function(){
    var idx = index-2;
    if (idx > -1) $.scrollTo('.js-scroll-item:eq('+idx+')', 400);
  };
  
  var _scroll = debounce(function(){

    // get scroll position from top
    var posY = _getScrollPosition()[1];

    // get total height
    var documentHeight = $(document).height();

    // get height per grid (= section)
    var itemHeight = documentHeight/totalItems;

    // set offset to 1/2 so the index changes in the middle of two sections
    var offset = itemHeight/2;

    // set current index
    index = Math.floor((posY + offset) / itemHeight) + 1;

    // control btn visibility
    _watchBtns();

  }, 50);

  var _watchBtns = function() {
    if (index == totalItems) {
      $(selectors.btnNext).hide();
    }
    else {
      $(selectors.btnNext).show();
    }

    if (index == 1) {
      $(selectors.btnPrev).hide();
    }
    else {
      $(selectors.btnPrev).show();
    }
  };

  var _getScrollPosition = function() {
    if (window.pageYOffset != undefined) {
      return [pageXOffset, pageYOffset];
    } 
    else {
      var sx, sy, d = document, r = d.documentElement, b = d.body;
      sx = r.scrollLeft || b.scrollLeft || 0;
      sy = r.scrollTop || b.scrollTop || 0;
      return [sx, sy];
    }
  };

  return {
    init:  _initialize,
  };
	
})();

// Initialize
$(function() {
  ImageScroll.init();
});

