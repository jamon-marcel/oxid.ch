import Collapsible from './collapsible';

var Filter = (function() {
	
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
    });
  };

  var _filter = function(btn) {

    var type = btn.data('filter');

    // Set button state
    $(selectors.btnFilter).removeClass(classes.active);
    btn.addClass(classes.active);

    if (type == 'all') {
      $(selectors.item).css('display', 'inline-block');
      return;
    }

    // Hide all items
    $(selectors.item).css('display', 'none');

    // Show matching items
    $('[data-filter-'+ type +'="'+ 1 +'"]').css('display', 'inline-block');

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
  Filter.init();
});

