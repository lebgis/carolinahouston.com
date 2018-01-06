var $ = jQuery.noConflict();
var mapStyles = [{featureType:'water',elementType:'all',stylers:[{hue:'#d7ebef'},{saturation:-5},{lightness:54},{visibility:'on'}]},{featureType:'landscape',elementType:'all',stylers:[{hue:'#eceae6'},{saturation:-49},{lightness:22},{visibility:'on'}]},{featureType:'poi.park',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-81},{lightness:34},{visibility:'on'}]},{featureType:'poi.medical',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-80},{lightness:-2},{visibility:'on'}]},{featureType:'poi.school',elementType:'all',stylers:[{hue:'#c8c6c3'},{saturation:-91},{lightness:-7},{visibility:'on'}]},{featureType:'landscape.natural',elementType:'all',stylers:[{hue:'#c8c6c3'},{saturation:-71},{lightness:-18},{visibility:'on'}]},{featureType:'road.highway',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-92},{lightness:60},{visibility:'on'}]},{featureType:'poi',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-81},{lightness:34},{visibility:'on'}]},{featureType:'road.arterial',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-92},{lightness:37},{visibility:'on'}]},{featureType:'transit',elementType:'geometry',stylers:[{hue:'#c8c6c3'},{saturation:4},{lightness:10},{visibility:'on'}]}];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Google Map - Homepage
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function createHomepageGoogleMap(_latitude,_longitude,_locations,source_path){
    setMapHeight();
	var ggMapsTypes = google.maps.MapTypeId.ROADMAP;

	if (map_type == 0) {
		ggMapsTypes = google.maps.MapTypeId.ROADMAP;
	} else if(map_type == 1) {
		ggMapsTypes = google.maps.MapTypeId.ROADMAP;
		mapStyles  = '';
	} else if(map_type == 2) {
		ggMapsTypes = google.maps.MapTypeId.SATELLITE;
		mapStyles  = '';
	} else if(map_type == 3) {
		ggMapsTypes = google.maps.MapTypeId.HYBRID;
		mapStyles  = '';
	} else if(map_type == 4) {
		ggMapsTypes = google.maps.MapTypeId.TERRAIN;
		mapStyles  = '';
	}
	
	if( document.getElementById('map') != null ) {
        if (_locations.length > 0) {
            //check what we parse string or object
            if (typeof _locations == "string")
              var data = jQuery.parseJSON(_locations);
            else
              var data = _locations;
		  
			var centerPos = new google.maps.LatLng(_latitude, _longitude);
			var map 	  = new google.maps.Map(document.getElementById('map'), {
                zoom: maps_zoom,
                scrollwheel: false,
                center: centerPos,
                mapTypeId:  ggMapsTypes,
                styles: mapStyles,
				zoomControlOptions: {	position: google.maps.ControlPosition.RIGHT_CENTER	},
				streetViewControlOptions: { position: google.maps.ControlPosition.RIGHT_CENTER },
                disableDefaultUI: (is_mobile == 1),
                //draggable: (is_mobile == 0),
                zoomControl: true,
                gestureHandling: 'cooperative'
            });
            var i;
            var newMarkers = [];
			
			var iconMarker = source_path + '/img/marker.png';
			if (icon_marker) {
				iconMarker = icon_marker;
			}
			
			for (i = 0; i < data.length; i++) {
			    var pictureLabel = document.createElement("img");
                pictureLabel.src = data[i]['type'];
				pictureLabel.width = '26';
				pictureLabel.height = '26';
				
                var boxText = document.createElement("div");
                infoboxOptions = {
                    content: boxText,
                    disableAutoPan: false,
                    //maxWidth: 150,
                    pixelOffset: new google.maps.Size(-100, 0),
                    zIndex: null,
                    alignBottom: true,
                    boxClass: "infobox-wrapper",
                    enableEventPropagation: true,
                    closeBoxMargin: "0px 0px -8px 0px",
                    closeBoxURL: source_path + "/img/close-btn.png",
                    infoBoxClearance: new google.maps.Size(1, 1)
                };
                var marker = new MarkerWithLabel({
                    position: new google.maps.LatLng(data[i]['lat'], data[i]['lng']),
                    map: map,
                    icon: iconMarker,
                    labelContent: pictureLabel,
                    labelAnchor: new google.maps.Point(50, 0),
                    labelClass: "marker-style"
                });
                newMarkers.push(marker);
                
				var htmlWindow = '';
				
				htmlWindow += '<div class="infobox-inner">';
					htmlWindow += '<a href="' + data[i]['link'] + '">';
						htmlWindow += '<div class="infobox-image" style="position: relative">';
							
							if (data[i]['featured-image'] != '') {
								htmlWindow += '<img src="' + data[i]['featured-image'] + '">';
							} else {
								htmlWindow += '<img data-src="' + data[i]['holder-image'] + '">';
							}	
							
							htmlWindow += '<div><span class="infobox-price">' + data[i]['price'] + '</span></div>';
						htmlWindow += '</div>';
						htmlWindow += '</a>';
						
						htmlWindow += '<div class="infobox-description">';
							htmlWindow += '<div class="infobox-title"><a href="'+ data[i]['link'] +'">' + data[i]['title'] + '</a></div>';
							htmlWindow += '<div class="infobox-location">' + data[i]['address'] + '</div>';
						htmlWindow += '</div>';
				htmlWindow += '</div>';	
				
				boxText.innerHTML = htmlWindow;
                		
                //Define the infobox
                newMarkers[i].infobox = new InfoBox(infoboxOptions);
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        for (h = 0; h < newMarkers.length; h++) {
                            newMarkers[h].infobox.close();
                        }
                        newMarkers[i].infobox.open(map, this);
						setTimeout(function() {
							Holder.run();	
						}, 1);
                    }
                })(marker, i));
            }
            var clusterStyles = [
                {
                    url: source_path + '/img/cluster.png',
                    height: 37,
                    width: 37
                }
            ];
            var markerCluster = new MarkerClusterer(map, newMarkers, {styles: clusterStyles});
            markerCluster.onClick = 
                function(clickedClusterIcon, 
                         sameLatitude, 
                         sameLongitude){ 
                            return multiChoice(sameLatitude, sameLongitude, data);
                        };            
            $('body').addClass('loaded');
            setTimeout(function() {
                $('body').removeClass('has-fullscreen-map');
            }, 1000);
            $('#map').removeClass('fade-map');
        }
        // Enable Geo Location on button click
        $('.geo-location').on("click", function() {
            if (navigator.geolocation) {
                $('#map').addClass('fade-map');
                navigator.geolocation.getCurrentPosition(success, error);
            } else {
                error('Geo Location is not supported');
            }
        });
    }
}

// Function which set marker to the user position
function success(position) {
	createHomepageGoogleMap(position.coords.latitude, position.coords.longitude, ZonerGlobal.locations, ZonerGlobal.source_path);
}

function error(err) {
	$('#map').removeClass('fade-map');
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Google Map - Property Detail
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function initMap(lat,lng,pictureLabelSrc,icon_url) {
  if(ZonerGlobal.gm_or_osm == 0){
        var subtractPosition = 0;
        var mapWrapper = $('#property-detail-map.float');

        if (document.documentElement.clientWidth > 1200) {
            subtractPosition = 0.013;
        }
        if (document.documentElement.clientWidth < 1199) {
            subtractPosition = 0.006;
        }
        if (document.documentElement.clientWidth < 979) {
            subtractPosition = 0.001;
        }
        if (document.documentElement.clientWidth < 767) {
            subtractPosition = 0;
        }

        var mapCenter = new google.maps.LatLng(lat,lng);

        if ( $("#property-detail-map").hasClass("float") ) {
            mapCenter = new google.maps.LatLng(lat,lng - subtractPosition);
            mapWrapper.css('width', mapWrapper.width() + mapWrapper.offset().left )
        }
          var ggMapsTypes = google.maps.MapTypeId.ROADMAP;

          if (map_type == 0) {
              ggMapsTypes = google.maps.MapTypeId.ROADMAP;
          } else if(map_type == 1) {
              ggMapsTypes = google.maps.MapTypeId.ROADMAP;
              mapStyles  = '';
          } else if(map_type == 2) {
              ggMapsTypes = google.maps.MapTypeId.SATELLITE;
              mapStyles  = '';
          } else if(map_type == 3) {
              ggMapsTypes = google.maps.MapTypeId.HYBRID;
              mapStyles  = '';
          } else if(map_type == 4) {
              ggMapsTypes = google.maps.MapTypeId.TERRAIN;
              mapStyles  = '';
          }
        var mapOptions = {
            zoom: 15,
            center: mapCenter,
            disableDefaultUI: false,
            scrollwheel: false,
            mapTypeId:  ggMapsTypes,
            styles: mapStyles,
            gestureHandling: 'cooperative'
        };
		
        var mapElement = document.getElementById('property-detail-map');
        var map = new google.maps.Map(mapElement, mapOptions);

        var pictureLabel = document.createElement("img");
			pictureLabel.src = pictureLabelSrc;
			pictureLabel.width ="26";
			pictureLabel.heigth="26";
			
        var markerPosition = new google.maps.LatLng(lat,lng);
        var marker = new MarkerWithLabel({
            position: markerPosition,
            map: map,
            icon: icon_url,
            labelContent: pictureLabel,
            labelAnchor: new google.maps.Point(50, 0),
            labelClass: "marker-style"
        });
      } else {//else OSM
        setMapHeight();
        var mbUrl 		= 'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFuaW1pbGxzIiwiYSI6ImNpaHZ0dXFwYTAwNXd3MWtwcm5neTRjdDgifQ.Tm_WKZTI9vwh_phQn_LoKA';
        var mbAttr		= '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors';
        var grayscale   = L.tileLayer(mbUrl, {id: 'mapbox.light', 	  minZoom: 0, maxZoom: 20, attribution: mbAttr}),
        streets     = L.tileLayer(mbUrl, {id: 'mapbox.streets',   minZoom: 0, maxZoom: 20, attribution: mbAttr});
        var map = L.map('property-detail-map', {
          center: [lat,lng],
          zoom: maps_zoom,
          scrollWheelZoom:false,
          closeOnClick:true,
          layers: [grayscale]
        });
        var markers = L.markerClusterGroup({
          showCoverageOnHover: false
        });
        var _icon = L.divIcon({
          html: '<img width="26" height="26" src="' + pictureLabelSrc +'">',
          iconSize:     [40, 48],
          iconAnchor:   [20, 48],
          popupAnchor:  [0, -48]
        });
        var marker = L.marker(new L.LatLng(lat,lng), {
          title: 'title',
          icon: _icon
        });
        markers.addLayer(marker);
        map.addLayer(markers);
      }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Google Map - Contact
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function contactUsMap(){
    var mapCenter = new google.maps.LatLng(_latitude,_longitude);
    if (map_type == 0) {
        ggMapsTypes = google.maps.MapTypeId.ROADMAP;
    } else if(map_type == 1) {
        ggMapsTypes = google.maps.MapTypeId.ROADMAP;
        mapStyles  = '';
    } else if(map_type == 2) {
        ggMapsTypes = google.maps.MapTypeId.SATELLITE;
        mapStyles  = '';
    } else if(map_type == 3) {
        ggMapsTypes = google.maps.MapTypeId.HYBRID;
        mapStyles  = '';
    } else if(map_type == 4) {
        ggMapsTypes = google.maps.MapTypeId.TERRAIN;
        mapStyles  = '';
    }
    var mapOptions = {
        zoom: 15,
        center: mapCenter,
        disableDefaultUI: false,
        scrollwheel: false,
        mapTypeId:  ggMapsTypes,
        styles: mapStyles,
        gestureHandling: 'cooperative'
    };
    var mapElement = document.getElementById('contact-map');
    var map = new google.maps.Map(mapElement, mapOptions);

	var iconMarker = source_path + '/img/marker.png';
	if (icon_marker) {
		iconMarker = icon_marker;
	}
			
    var marker = new MarkerWithLabel({
        position: mapCenter,
        map: map,
        icon: iconMarker,
        //labelContent: pictureLabel,
        labelAnchor: new google.maps.Point(50, 0),
        labelClass: "marker-style"
    });
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OpenStreetMap - Homepage
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function createHomepageOSM(_latitude,_longitude,_locations,source_path){
	setMapHeight();
    if( document.getElementById('map') != null ){
        if (_locations.length > 0) {
			var data = jQuery.parseJSON(_locations);
			
			var mbUrl 		= 'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFuaW1pbGxzIiwiYSI6ImNpaHZ0dXFwYTAwNXd3MWtwcm5neTRjdDgifQ.Tm_WKZTI9vwh_phQn_LoKA';
			var mbAttr		= '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors';
			var grayscale   = L.tileLayer(mbUrl, {id: 'mapbox.light', 	  minZoom: 0, maxZoom: 20, attribution: mbAttr}),
				streets     = L.tileLayer(mbUrl, {id: 'mapbox.streets',   minZoom: 0, maxZoom: 20, attribution: mbAttr});
			
			var map = L.map('map', {
				center: [_latitude,_longitude],
                zoom: maps_zoom,
                zoomControl: false,
                scrollWheelZoom:false,
				closeOnClick:true,
				layers: [grayscale],
                tap:false
			});

            if( is_mobile == 1 )
                map.dragging.disable();
			
			map.on('popupopen', function(e) {
				Holder.run({ domain: "galleryFrontEnd.holder", use_canvas: true }); 
			});
			
			var markers = L.markerClusterGroup({
                showCoverageOnHover: false
            });
			
			function locateUser() {
                $('#map').addClass('fade-map');
                map.locate({setView : true})
            }

            function onLocationFound(){
                $('#map').removeClass('fade-map');
            }
            new L.Control.Zoom({ position: 'bottomleft' }).addTo(map);
            for (var i = 0; i < data.length; i++) {
				var _icon = L.divIcon({
					html: '<img width="26" height="26" src="' + data[i]['type'] +'">',
                    iconSize:     [40, 48],
                    iconAnchor:   [20, 48],
                    popupAnchor:  [0, -48]
                });

                var title = data[i]['title'];
				var marker = L.marker(new L.LatLng(data[i]['lat'],data[i]['lng']), {
                    title: title,
                    icon: _icon
                });
				
				var fimg = '<img width="100%" data-src="galleryFrontEnd.holder/555x445?text=' + pl_text_property + '" alt="" />';
				
				if (data[i]['featured-image'] != '') {
					fimg = '<img width="100%" src="' + data[i]['featured-image'] + '">'
				}	
						
				var htmlWindow = '';		
				htmlWindow += '<div class="infobox-inner">';
					htmlWindow += '<a href="' + data[i]['link'] + '">';
						htmlWindow += '<div class="infobox-image" style="position: relative">';
							
							if (data[i]['featured-image'] != '') {
								htmlWindow += '<img src="' + data[i]['featured-image'] + '">';
							} else {
								htmlWindow += '<img data-src="galleryFrontEnd.holder/555x445?auto=yes&text=Property">';
							}	
							
							htmlWindow += '<div><span class="infobox-price">' + data[i]['price'] + '</span></div>';
						htmlWindow += '</div>';
						htmlWindow += '</a>';
						
						htmlWindow += '<div class="infobox-description">';
							htmlWindow += '<div class="infobox-title"><a href="'+ data[i]['link'] +'">' + data[i]['title'] + '</a></div>';
							htmlWindow += '<div class="infobox-location">' + data[i]['address'] + '</div>';
						htmlWindow += '</div>';
				htmlWindow += '</div>';	
				
                marker.bindPopup(htmlWindow);
                markers.addLayer(marker);
            }
			
            map.addLayer(markers);
            map.on('locationfound', onLocationFound);

            $('.geo-location').on("click", function() {
				locateUser();
            });
				
			$('body').addClass('loaded');
            setTimeout(function() {
                $('body').removeClass('has-fullscreen-map');
            }, 1000);
            
			$('#map').removeClass('fade-map');	
		}
    }	
}

function multiChoice(sameLatitude, sameLongitude) {
    
	var isAgencyPage = is_agency_page;
	var isAgentPage  = is_agent_page;
	
	
	$.ajax({
        url: ajaxurl,
        data: { 'action':'zoner_get_multiitems', 'sameLatitude':sameLatitude, 'sameLongitude':sameLongitude, isAgentAgencyPage : (isAgencyPage == '1' || isAgentPage == '1') },
        success:function(html) {
			if (isAgencyPage == '1' || isAgentPage == '1') {
				$('#map.agency-map').append('<div class="modal-window multichoice fade_in"></div>');   
			} else {
				$('body').append('<div class="modal-window multichoice fade_in"></div>');   
			}
			$('.modal-window').html(html);
        }
    });  

    $('.modal-window .modal-background, .modal-close').live('click',  function(e){
        $('.modal-window').addClass('fade_out');
        setTimeout(function() { 
            $('.modal-window').remove();
        }, 300);
    });
}