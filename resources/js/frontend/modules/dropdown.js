var Dropdown = (function() {
	
	var selectors = {
    html:     'html',
    body:     'body',
    wrapper:  '.js-dropdown',
    btn:      '.js-btn-dropdown',
  };
  
  // css classes
  var classes = {
    active:  'is-active',
    open:    'is-open',
  };

  var _initialize = function() {
    _bind();
  };

  var _bind = function() {
    $(selectors.body).on('click', selectors.btn, function(){
      $(this).toggleClass(classes.active);
      $(selectors.wrapper).toggleClass(classes.open);
    });
  };

  return {
    init:  _initialize,
  };
	
})();

// Initialize
$(function() {
  Dropdown.init();
});
