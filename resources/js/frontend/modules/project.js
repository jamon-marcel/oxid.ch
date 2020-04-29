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
    sm: window.matchMedia("(min-width: 720px)"),
    md: window.matchMedia("(min-width: 1024px)"),
    lg: window.matchMedia("(min-width: 1168px)")
  };

  var footerHeight = 60;
  var gridGap      = 12;
  var index        = 0;
  var max          = $(selectors.grids).find(selectors.grid).length;
  var min          = 1;
   
  var _initialize = function() {
    _bind();
  };

  var _bind = function() {
    $(selectors.body).on('click', selectors.btnScroll, function(){
      _scroll();
    });

    // $(selectors.grid).each(function () {
    //   new Waypoint.Inview({
    //     element: this,
    //     entered: function(direction) {
    //       _updateIndex(direction);
    //     },
    //     offset: 0
    //   });
    // });
  };

  var _scroll = function(){
    var distance = $(window).height() - footerHeight + gridGap;
    $.scrollTo('+=' + distance, 400);
  };

  var _updateIndex = function(direction) {

    if (direction == 'down' && index < max) {
      index++;
    }
    else if (direction == 'up' && index >= min) {
      index--;
    }
    $(selectors.index).html(index);
  };

  return {
    init:  _initialize,
  };
	
})();

// Initialize
$(function() {
  Projects.init();
});

