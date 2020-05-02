var Contact = (function() {
	
	var selectors = {
    html:       'html',
    body:       'body',
    btnImprint: '.js-btn-imprint',
  };
  
  var _initialize = function() {
    _bind();
  };

  var _bind = function() {
    $(selectors.body).on('click', selectors.btnImprint, function(){
      $(this).next('div').toggle();
    });
  };

  return {
    init:  _initialize,
  };
	
})();

// Initialize
$(function() {
  Contact.init();
});

