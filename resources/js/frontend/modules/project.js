function debounce(a,b,c){var d;return function(){var e=this,f=arguments;clearTimeout(d),d=setTimeout(function(){d=null,c||a.apply(e,f)},b),c&&!d&&a.apply(e,f)}}

var Projects = (function() {

  // selectors
  var selectors = {
    html:       'html',
    body:       'body',
    grids:      '.js-project-grids',
    grid:       '.js-project-grid',
    btnDown:    '.js-btn-scroll-down',
    btnUp:      '.js-btn-scroll-up',
    index:      '.js-project-idx',
    menuItem:   '[data-project-id]',
    menu:    '.js-menu',
    menuBar: '.js-menu-bar',
  };

  // css classes
  var classes = {
    visible: 'is-visible',
    active: 'is-active',
    hidden: 'is-hidden',
    hasMenu: 'has-menu',
    selected: 'is-selected',
    open: 'is-open',
  };

  // media queries
  var mq = {
    sm: window.matchMedia("(min-width: 960px)"),
  };

  var totalGrids   = $(selectors.grids).find(selectors.grid).length;
  var index = 1;
   
  var _initialize = function() {
    _bind();
  };

  var _bind = function() {
    $(selectors.body).on('click', selectors.btnDown, function(){
      _scrollToNext();
      _closeMenu();
    });

    $(selectors.body).on('click', selectors.btnUp, function(){
      _scrollToPrev();
      _closeMenu();
    });

    if (mq.sm.matches) {
      _scroll();
    }

    $(window).scroll(function(event){
      if (mq.sm.matches) {
        _scroll();
      }
    });

    $(selectors.body).on('mouseover', selectors.menuItem, function(){
      if (mq.sm.matches) {
        var id = $(this).data('projectId');
        if (!$(this).hasClass(classes.active) && $(selectors.body).find('[data-project-teaser="'+ id +'"]').length) {
          $('[data-project-teaser="'+ id +'"]').addClass(classes.visible);
        }
      }
    });

    $(selectors.body).on('mouseleave', selectors.menuItem, function(){
      if (mq.sm.matches) {
        var id = $(this).data('projectId');
        $('[data-project-teaser="'+ id +'"]').removeClass(classes.visible);
      }
    });
  };

  var _closeMenu = function() {
    if ($(selectors.menu).hasClass(classes.visible)) {
      $(selectors.menu).removeClass(classes.visible);
      $(selectors.menuBar).toggleClass(classes.hidden);
    }
  };

  var _scrollToNext = function(){
    $.scrollTo('.js-project-grid:eq('+index+')', 400);
  };

  var _scrollToPrev = function(){
    var idx = index-2;
    if (idx > -1) $.scrollTo('.js-project-grid:eq('+idx+')', 400);
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
    index = Math.floor((posY + offset) / gridHeight) + 1;
    $(selectors.index).html(index);

    // control btn visibility
    _watchBtns();

  }, 50);

  var _watchBtns = function() {
    if (index == totalGrids) {
      $(selectors.btnDown).hide();
    }
    else {
      $(selectors.btnDown).show();
    }

    if (index == 1) {
      $(selectors.btnUp).hide();
    }
    else {
      $(selectors.btnUp).show();
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
  Projects.init();
});

