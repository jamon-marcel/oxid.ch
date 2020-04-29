var Overlay = (function() {
	
	// selectors
	var selectors = {
    html:    'html',
    body:    'body',
    wrapper: '.js-info',
    btnInfo: '.js-btn-info'
	};

  // css classes
  var classes = {
    active:  'is-active',
    visible: 'is-visible',
    hidden:  'is-hidden',
    open:    'is-open',
    parent:  'is-parent',
    hasMenu: 'has-menu',
  };


  // Init
  var _initialize = function() {
    _bind();
  };

  // Bind events
  var _bind = function() {
    $(selectors.body).on('click', selectors.btnInfo, function(){
      _toggle($(this));
    });
  };

  var _toggle = function(btn) {
    btn.toggleClass(classes.active);
    $(selectors.wrapper).toggleClass(classes.visible);
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
  Overlay.init();
});

