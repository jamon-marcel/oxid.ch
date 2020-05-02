var MapsUi = (function() {
	
	var selectors = {
		body:	   'body',
		container: 'js-maps', // id!
	};
	
	var coords = {
		lat: 47.371496,
		lng: 8.544790
	};

	var zoom = 15;

	var _initialize = function() {
	    
    if ($(selectors.body).find('#' + selectors.container).length < 1) {
      return;
    }
    
    var myLatlng = new google.maps.LatLng(coords.lat,coords.lng);
    var mapOptions = {
      center: myLatlng,
      zoom: zoom,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var styles = [
      {
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#f5f5f5"
        }
      ]
    },
    {
      "elementType": "labels.icon",
      "stylers": [
        {
          "visibility": "off"
        }
      ]
    },
    {
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#616161"
        }
      ]
    },
    {
      "elementType": "labels.text.stroke",
      "stylers": [
        {
          "color": "#f5f5f5"
        }
      ]
    },
    {
      "featureType": "administrative.land_parcel",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#bdbdbd"
        }
      ]
    },
    {
      "featureType": "poi",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#eeeeee"
        }
      ]
    },
    {
      "featureType": "poi",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#757575"
        }
      ]
    },
    {
      "featureType": "poi.park",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#e5e5e5"
        }
      ]
    },
    {
      "featureType": "poi.park",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#9e9e9e"
        }
      ]
    },
    {
      "featureType": "road",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#ffffff"
        }
      ]
    },
    {
      "featureType": "road.arterial",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#757575"
        }
      ]
    },
    {
      "featureType": "road.highway",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#dadada"
        }
      ]
    },
    {
      "featureType": "road.highway",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#616161"
        }
      ]
    },
    {
      "featureType": "road.local",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#9e9e9e"
        }
      ]
    },
    {
      "featureType": "transit.line",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#e5e5e5"
        }
      ]
    },
    {
      "featureType": "transit.station",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#eeeeee"
        }
      ]
    },
    {
      "featureType": "water",
      "elementType": "geometry",
      "stylers": [
        {
          "color": "#c9c9c9"
        }
      ]
    },
    {
      "featureType": "water",
      "elementType": "labels.text.fill",
      "stylers": [
        {
          "color": "#9e9e9e"
        }
      ]
    }
  ];

    var map = new google.maps.Map(document.getElementById(selectors.container),mapOptions);
      map.setOptions({styles: styles});

    var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      icon: {
        anchor: new google.maps.Point(0,0),
        url: 'data:image/svg+xml;utf-8, \
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 43.3 63.6"><defs><style>.cls-1{fill-rule:evenodd}</style></defs><g><path class="cls-1" d="M21.7 63.6s21.6-28.6 21.6-41S36.2 0 21.7 0 0 10.1 0 22.6s21.7 41 21.7 41zm0-33.3a11.6 11.6 0 0 0 0-23.1 11.6 11.6 0 1 0 0 23.1z"/></g></svg>',
        scaledSize: new google.maps.Size(40, 60)
      }
    }); 
	};
	
	return {
		init: _initialize,
	}
})();

// Initialize
MapsUi.init();