function debounce(a,b,c){var d;return function(){var e=this,f=arguments;clearTimeout(d),d=setTimeout(function(){d=null,c||a.apply(e,f)},b),c&&!d&&a.apply(e,f)}}

var ImageScroll = (function() {

  // selectors
  var selectors = {
    html:    'html',
    body:    'body',
    item:    '.js-btn-history',
    menu:    '.js-menu',
    menuBar: '.js-menu-bar',
  };

  // media queries
  var mq = {
    sm: window.matchMedia("(min-width: 960px)"),
  };

  var classes = {
    visible: 'is-visible',
    hidden: 'is-hidden',
    hasMenu: 'has-menu',
    selected: 'is-selected',
    open: 'is-open',
    active: 'is-active',
  };

  var _initialize = function() {
    _bind();
  };

  var _bind = function() {

    $(window).on('hashchange', function(e) {
      _hashChange();
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

  var _hashChange = function() {
    var hash = window.location.hash.substr(1);
    _toggleMenu();
    _toggleMenuItems(hash);
    _scrollTo(hash);
  };

  var _toggleMenu = function() {
    if ($(selectors.html).hasClass(classes.hasMenu)) {
      $(selectors.menu).removeClass(classes.visible);
      $(selectors.menuBar).toggleClass(classes.hidden);
      $(selectors.html).removeClass(classes.hasMenu);
    }
  };

  var _toggleMenuItems = function(hash) {
    $('a[href!="#"]').removeClass(classes.active);
    $('a[href="#'+hash+'"]').addClass(classes.active);
  };

  var _scrollTo = function(target){
    $.scrollTo('[data-period="'+target+'"]', 800);
  };
  
  var _scroll = debounce(function(){
    var matches = document.querySelectorAll("[data-period]");
    [...matches].forEach((match) => {
        var inViewport = _inViewport(match);
        if (inViewport) {
          var hash = match.dataset.period;
          _toggleMenu();
          _toggleMenuItems(hash);
          history.replaceState(null, null, document.location.pathname + '#' + hash);

        }
    });
  }, 50);


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

  var _inViewport = function(el) {
    const rect = el.getBoundingClientRect();
    const windowHeight = (window.innerHeight || document.documentElement.clientHeight);
    const windowWidth = (window.innerWidth || document.documentElement.clientWidth);
    const vertInView = (rect.top <= windowHeight/2) && ((rect.top + rect.height) >= 0);
    //const horInView = (rect.left <= windowWidth) && ((rect.left + rect.width) >= 0);
    return (vertInView);
  };

  return {
    init:  _initialize,
  };
	
})();

// Initialize
$(function() {
  ImageScroll.init();
});

