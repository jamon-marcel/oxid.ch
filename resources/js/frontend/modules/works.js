import Collapsible from './collapsible';

var Works = (function() {
	
	// selectors
	var selectors = {
    html:      'html',
    body:      'body',
    items:     '.js-filter-items',
    item:      '.js-filter-item',
    btnFilter: '.js-filter-btn'
  };
  
  // css classes
  var classes = {
    active: 'is-active',
  };
    
  var _initialize = function() {
    _bind();
  };

  var _bind = function() {
    $(selectors.body).on('click', selectors.btnFilter, function(){
      _filter($(this));
    })
  };

  var _filter = function(btn) {

    // Set button state
    $(selectors.btnFilter).removeClass(classes.active);
    btn.addClass(classes.active);
    
    // Hide all items
    $(selectors.item).hide();

    // Show matching items
    $('[data-filter-'+ btn.data('filter') +'="'+ 1 +'"]').show();

    // Expand all
    Collapsible.expandAll();

    // Collapse all with no visible items
    $(selectors.items).each(function() {
      var items = $(this).find(selectors.item + ':visible');
      if (items.length == 0) {
        Collapsible.hide($(this));
      }
    });
  };

  return {
    init:  _initialize,
  };
	
})();

// Initialize
$(function() {
  Works.init();
});

