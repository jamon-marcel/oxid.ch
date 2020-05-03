var Menu = (function() {
	
	// selectors
	var selectors = {
    html:           'html',
    body:           'body',
    menuBtn:        '.js-menu-btn',
    menuBar:        '.js-menu-bar',
    menu:           '.js-menu',
    menuParent:     '.js-menu-parent',
    menuFilterBtn:  '.js-menu-filter-btn',
	};

  // css classes
  var classes = {
    active:  'is-active',
    visible: 'is-visible',
    hidden: 'is-hidden',
    open:    'is-open',
    parent:  'is-parent',
    hasMenu: 'has-menu',
  };

  // media queries
  var mq = {
    xs: window.matchMedia("(max-width: 959px)"),
    sm: window.matchMedia("(min-width: 960px)"),
  };

  // Init
  var _initialize = function() {
    _bind();
  };

  // Bind events
  var _bind = function() {
    $(selectors.body).on('click', selectors.menuBtn, function(){
      _toggle($(this));
    });

    $(selectors.body).on('click', selectors.menuParent, function(e){
      if (mq.xs.matches) {
        e.preventDefault();
        _toggleSub($(this));
      }
    });

  };

  var _toggle = function(btn) {
    $(selectors.menu).toggleClass(classes.visible);
    $(selectors.menuBar).toggleClass(classes.hidden);
    $(selectors.html).toggleClass(classes.hasMenu);
  };

  var _toggleSub = function(btn) {
    btn.next('ul').toggleClass(classes.visible);
  };

  /* --------------------------------------------------------------
    * RETURN PUBLIC METHODS
    * ------------------------------------------------------------ */

  return {
    init:  _initialize,
  };
	
})();

// Initialize
$(function() {
  Menu.init();
});

