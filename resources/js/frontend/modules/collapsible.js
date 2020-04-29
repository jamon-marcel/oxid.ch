var Collapsible = (function() {

  // this 
  var _self;
  
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
    el.parents(selectors.wrapper).toggleClass(classes.expanded);
    el.parents(selectors.wrapper).find(selectors.content).toggle();
  };

  var _hide = function(el) {
    el.parents(selectors.wrapper).removeClass(classes.expanded);
    el.parents(selectors.wrapper).find(selectors.content).hide();
  };

  var _expandAll = function() {
    $(selectors.wrapper).each(function(){
      $(this).addClass(classes.expanded);
      $(this).find(selectors.content).show();
    });
  };

  var _collapseAll = function() {

  };

  /* --------------------------------------------------------------
   * RETURN PUBLIC METHODS
   * ------------------------------------------------------------ */
       
return {
  init: _initialize,
  hide: _hide,
  expandAll: _expandAll,
  collapseAll: _collapseAll,
};

})();

// Initialize
Collapsible.init();

export default Collapsible;

