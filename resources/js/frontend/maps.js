var Maps = (function() {

  // Selectors
  var selectors = {
    body:	     'body',
    container: 'js-maps',
  };

  // Default zoom level
  var zoom = 16;

  // Init FN
  var _initialize = function(opts) {

    if ($(selectors.body).find('#' + selectors.container).length < 1) {
      return;
    }

    // Set map options
    var mapOptions = {
      zoom: zoom,
      streetViewControl: false,
      mapTypeControl: false,
      mapTypeId: 'roadmap'
    };

    var position = {
      lat: 47.361777,
      lng: 8.512298
    };

    // Create Map
    var map = new google.maps.Map(document.getElementById(selectors.container),mapOptions);

    // Center around a single marker
    var center = new google.maps.LatLng(47.361777, 8.512298);
    map.setOptions({center: center});
    var marker = new google.maps.Marker({position: position, map: map});

	};
	
	return {
		init: _initialize,
  };
  
})();

// Initialize
$(function() {
  Maps.init();
});
