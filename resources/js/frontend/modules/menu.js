var Menu = (function() {
	
	// selectors
	var selectors = {
    html:           'html',
    body:           'body',
    menuBtn:        '.js-menu-btn',
    menuBar:        '.js-menu-bar',
    menu:           '.js-menu',
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
    sm: window.matchMedia("(min-width: 600px)"),
    md: window.matchMedia("(min-width: 900px)"),
    lg: window.matchMedia("(min-width: 1200px)")
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
  };

  var _toggle = function(btn) {
    $(selectors.menu).toggleClass(classes.visible);
    $(selectors.menuBar).toggleClass(classes.hidden);
    $(selectors.html).toggleClass(classes.hasMenu);
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

