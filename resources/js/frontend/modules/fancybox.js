

var Fancybox = (function() {

  /* --------------------------------------------------------------
   * VARIABLES
   * ------------------------------------------------------------ */

  // selectors
  var selectors = {
    html: 'html',
    body: 'body',
  };

  /* --------------------------------------------------------------
   * METHODS
   * ------------------------------------------------------------ */
  
  // Init
  var _initialize = function() {
    _bind();
  };

  var _bind = function() {
    $('[data-fancybox]').fancybox({
      toolbar: true,
      buttons: ["close"],
      btnTpl: {
        arrowLeft:  '<a href="javascript:;" class="btn-fancybox-nav is-prev" data-fancybox-prev></a>',
        arrowRight: '<a href="javascript:;" class="btn-fancybox-nav is-next" data-fancybox-next></a>',
        close: '<a href="javascript:;" data-fancybox-close class="btn-fancybox-close"></a>',
      },
      fullScreen : {
        autoStart : true
      }           
    });
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
  Fancybox.init();
});

