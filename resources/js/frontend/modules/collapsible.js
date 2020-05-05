var Collapsible = (function() {
 
  // selectors 	
  var selectors = {
    body:  		'body',
    wrapper:	'.js-clpsbl', 
    content:	'.js-clpsbl-body',
    btn:		  '.js-clpsbl-btn',
  };

  // css classes
  var classes = {
    expanded: 'is-expanded',
  };

  var _initialize = function() {
    _bind();
  };

  var _bind = function() {
    $(selectors.body).on('click', selectors.btn, function(e){
      _toggle($(this));
    });
  };

  var _toggle = function(el) {
    var wrapper = el.parents(selectors.wrapper);
    if (!wrapper.hasClass(classes.expanded)) {
      var distance = wrapper.offset().top - 20;
      $.scrollTo(distance, 400);
    }
    wrapper.toggleClass(classes.expanded);
    wrapper.find(selectors.content).toggle();
  };

  var _hide = function(el) {
    var wrapper = el.parents(selectors.wrapper);
    wrapper.removeClass(classes.expanded);
    wrapper.find(selectors.content).hide();
  };

  var _expandAll = function() {
    $(selectors.wrapper).each(function(){
      $(this).addClass(classes.expanded);
      $(this).find(selectors.content).show();
    });
  };
       
  return {
    init: _initialize,
    hide: _hide,
    expandAll: _expandAll,
  };

})();

$(function() {
  Collapsible.init();
});

export default Collapsible;

