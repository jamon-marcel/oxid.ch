function debounce(a,b,c){var d;return function(){var e=this,f=arguments;clearTimeout(d),d=setTimeout(function(){d=null,c||a.apply(e,f)},b),c&&!d&&a.apply(e,f)}}

var Projects = (function() {

  // selectors
  var selectors = {
    html:      'html',
    body:      'body',
    grids:     '.js-project-grids',
    grid:      '.js-project-grid',
    btnScroll: '.js-btn-scroll',
    index:     '.js-project-idx'
  };

  // media queries
  var mq = {
    sm: window.matchMedia("(min-width: 960px)"),
  };

  var totalGrids   = $(selectors.grids).find(selectors.grid).length;
  var currentIndex = 1;
   
  var _initialize = function() {
    _bind();
  };

  var _bind = function() {
    $(selectors.body).on('click', selectors.btnScroll, function(){
      _scrollTo();
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

  var _scrollTo = function(){
    $.scrollTo('.js-project-grid:eq('+currentIndex+')', 400);
  };

  var _scroll = debounce(function(){

    // get scroll position from top
    var posY = _getScrollPosition()[1];

    // get total height
    var documentHeight = $(document).height();

    // get height per grid (= section)
    var gridHeight = documentHeight/totalGrids;

    // set offset to 1/2 so the index changes in the middle of two sections
    var offset = gridHeight/2;

    // set current index
    currentIndex = Math.floor((posY + offset) / gridHeight) + 1;
    $(selectors.index).html(currentIndex);

  }, 10);

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
  Projects.init();
});

