var $ = jQuery.noConflict();
var mapStyles = [{featureType:'water',elementType:'all',stylers:[{hue:'#d7ebef'},{saturation:-5},{lightness:54},{visibility:'on'}]},{featureType:'landscape',elementType:'all',stylers:[{hue:'#eceae6'},{saturation:-49},{lightness:22},{visibility:'on'}]},{featureType:'poi.park',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-81},{lightness:34},{visibility:'on'}]},{featureType:'poi.medical',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-80},{lightness:-2},{visibility:'on'}]},{featureType:'poi.school',elementType:'all',stylers:[{hue:'#c8c6c3'},{saturation:-91},{lightness:-7},{visibility:'on'}]},{featureType:'landscape.natural',elementType:'all',stylers:[{hue:'#c8c6c3'},{saturation:-71},{lightness:-18},{visibility:'on'}]},{featureType:'road.highway',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-92},{lightness:60},{visibility:'on'}]},{featureType:'poi',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-81},{lightness:34},{visibility:'on'}]},{featureType:'road.arterial',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-92},{lightness:37},{visibility:'on'}]},{featureType:'transit',elementType:'geometry',stylers:[{hue:'#c8c6c3'},{saturation:4},{lightness:10},{visibility:'on'}]}];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Set default map parametrs -------------------------------------------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
var json_data 	 = globalGmap.json_data;
var map_nonce 	 = globalGmap.zoner_ajax_maps_nonce;
var location_data= [];
var source_path = globalGmap.source_path;
var detatail_text = globalGmap.detatail_text;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Homepage map - Google
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$(document).ready(function() {

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Set map height to 100% ----------------------------------------------------------------------------------------------
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($('body').hasClass('map-fullscreen') ) {
		if( $(window).width() > 768 ) {
			$('.map-canvas').height( $(window).height() - $('.header').height() );
		} else {
			$('.map-canvas .map-wrapper').height( $(window).height() - $('.header').height() );
		}
	}
	
	// Scrollbar on "Results" section
	if( $('.items-list').length > 0 ){
		$(".items-list").mCustomScrollbar({
			mouseWheel:{ scrollAmount: 200 }
		});
	}
	
	$('.shortcode-map-wrapper').each(function(indx, elem){
		var map_id = $(elem).attr('id');
		
		var start_lat = $(elem).data('start_lat');
		var start_lng = $(elem).data('start_lng');
		var items_number_max = $(elem).data('items_number_max');
		var auto_zoom = $(elem).data('auto_zoom');
		var default_zoom = $(elem).data('default_zoom');
		default_zoom = parseInt(default_zoom);
        var serialize = [];

		if ($(elem).data('tax_city'))
        { 
            serialize = [
                {name: 'sb-city', value: $(elem).data('tax_city')}
            ];
        }
		pushItemsFromForm(map_id, start_lat, start_lng, default_zoom, auto_zoom, items_number_max, serialize);
		
	});
	
});

function setShortcodeMapHeight(){
    var shortcode = $('.gmap-shortcode');
    var shortcode_map = $('.gmap-shortcode .map-wrapper ');
    var shortcode_search = $('.gmap-shortcode .search-box.map ');
    var shortcode_list = $('.gmap-shortcode .mCustomScrollBox');
    if ($(window).width() > 768) {
        shortcode.height($(window).height()/2);
        shortcode_map.height($(window).height()/2);
        shortcode_list.height(shortcode_map.height());
        shortcode_search.fadeIn();
    }
    else {
        shortcode_map.height($(window).height()/2);
    }
    
    $(window).on('resize', function(){
        shortcode.height($(window).height()/2);
        shortcode_map.height($(window).height()/2);
        shortcode_list.height(shortcode_map.height());
        if ($(window).width() > 768) {
            shortcode_search.fadeIn();
        }
    });

}
function createShortcodeGoogleMap(_latitude,_longitude, _zoom, auto_zoom, data, map_id){
    setShortcodeMapHeight();
	var mapCenter  = new google.maps.LatLng(_latitude,_longitude);
    
	var mapOptions = {
			zoom: _zoom,
            center: mapCenter,
            disableDefaultUI: false,
            scrollwheel: false,
            styles: mapStyles,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.LEFT_TOP
            },
            panControl: false,
            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.RIGHT_TOP
            }
        };
		var mapElement 		= document.getElementById(map_id);
        var map 			= new google.maps.Map(mapElement, mapOptions);
        var newMarkers 		= [];
        var markerClicked 	= 0;
        var activeMarker 	= false;
        var lastClicked 	= false;
        if (auto_zoom == 1)
            var bounds = new google.maps.LatLngBounds();
        for (var i = 0; i < data.length; i++) {
            // Google map marker content -----------------------------------------------------------------------------------
			
			var infoboxContent = '';	
            var markerContent  = document.createElement('div');	
            var markerHtml 	  =  null;
			
			if( data[i].is_featured == 1 ) {
				markerHtml  = '<div id="'+data[i].id+'" class="map-marker featured">';
			} else {
				markerHtml  = '<div id="'+data[i].id+'" class="map-marker">';
			}
			if (data[i].tax_icon)
    			markerHtml += '<div class="icon"><img src="'+ data[i].tax_icon + '" alt="" /></div></div>';
            else
                markerHtml += '<div class="icon"></div></div>';    
			markerContent.innerHTML = markerHtml;
            			
            // Create marker on the map ------------------------------------------------------------------------------------
            var pictureLabel = document.createElement("img");
                pictureLabel.src = data[i]['type'];
                pictureLabel.width = '26';
                pictureLabel.height = '26';
                
            var marker = new MarkerWithLabel({
                position: new google.maps.LatLng(data[i]['lat'], data[i]['lng']),
                map: map,
                icon: source_path + '/img/marker.png',
                labelContent: pictureLabel,
                labelAnchor: new google.maps.Point(50, 0),
                labelClass: "marker-style"
            });

            newMarkers.push(marker);
			//newMarkers[i].content.className    = 'bounce-animation marker-loaded';
			//newMarkers[i].content.setAttribute('data-uniqid', data[i].uniq_id);
			
            // Create infobox for marker -----------------------------------------------------------------------------------
			var boxText = document.createElement("div");
            var infoboxOptions = {
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
            boxText.innerHTML = drawInfobox(data, i);
			
            // Create new markers ------------------------------------------------------------------------------------------
			
            
			newMarkers[i].infobox = new InfoBox(infoboxOptions);
			
			
			 // Show infobox after click ------------------------------------------------------------------------------------
			
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
			
            if (auto_zoom == 1)
                bounds.extend(newMarkers[i].position);
        }
		// Autocenter map if autozoom----------------------------------------------------------------------------------------------
        if (auto_zoom == 1)
            map.fitBounds(bounds);
		 
         google.maps.event.addListener(map, 'click', function(event) {
            if( activeMarker != false && lastClicked != false ){
                if( markerClicked == 1 ){
                    activeMarker.infobox.open(map);
                    activeMarker.infobox.setOptions({ boxClass:'fade-in-marker'});
                   //activeMarker.content.className = 'marker-active marker-loaded';
                }
                else {
                    markerClicked = 0;
                    activeMarker.infobox.setOptions({ boxClass:'fade-out-marker' });
                    //activeMarker.content.className = 'marker-loaded';
                    setTimeout(function() {
                        activeMarker.infobox.close();
                    }, 350);
                }
                markerClicked = 0;
            }
            if( activeMarker != false ){
                google.maps.event.addListener(activeMarker, 'click', function(event) {
                    markerClicked = 1;
                });
            }
            markerClicked = 0;
        });
    var uniq_ids = [];
    var prev_ids = [];
    google.maps.event.addListener(map, 'idle', function() {
         $.each(data, function(a) {
                if(map.getBounds().contains( new google.maps.LatLng(data[a].lat, data[a].lng))) {
                   uniq_ids.push(data[a].post_id);
                }
            });
            if (!arraysEqual(uniq_ids, prev_ids)) pushItemsToArray(uniq_ids, map_id);
            prev_ids = uniq_ids;
            uniq_ids = [];
      });
        
   
        // Create marker clusterer -----------------------------------------------------------------------------------------

         var clusterStyles = [
                {
                    url: source_path + '/img/cluster.png',
                    height: 37,
                    width: 37
                }
            ];

        var markerCluster  = new MarkerClusterer(map, newMarkers, { styles: clusterStyles});
			markerCluster.onClick = 
				function(clickedClusterIcon, 
						 sameLatitude, 
						 sameLongitude) {
							return multiChoice(sameLatitude, sameLongitude, data);
						};
		google.maps.event.addListener(map, 'idle', function() {
		});
		redrawMap('google', map);
		
		/*Hover effect with items*/
		$('.results .item').live('mouseover', function(){
			var itemID = $(this).data('itemid');
				$.each( newMarkers, function (i) {
					var markerItemID = newMarkers[i].content.getAttribute('data-uniqid');
					//if (itemID == markerItemID)
					//newMarkers[i].content.className = 'marker-active marker-loaded';	
				});
		});
		
		$('.results .item').live('mouseleave', function() {
			var itemID = $(this).data('itemid');
				$.each( newMarkers, function (i) {
					var markerItemID = newMarkers[i].content.getAttribute('data-uniqid');
					//if (itemID == markerItemID)
					//newMarkers[i].content.className = 'marker-loaded';
				});
        });
         // Geolocation of user -----------------------------------------------------------------------------------------

        $('.geolocation').on("click", function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(success);
            } else {
                console.log('Geo Location is not supported');
            }
        });

        function success(position) {
            var locationCenter = new google.maps.LatLng( position.coords.latitude, position.coords.longitude);
            map.setCenter( locationCenter );
            map.setZoom(14);

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                "latLng": locationCenter
            }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var lat = results[0].geometry.location.lat(),
                        lng = results[0].geometry.location.lng(),
                        placeName = results[0].address_components[0].long_name,
                        latlng = new google.maps.LatLng(lat, lng);
                    $(this).parent().find('[name="location"]').val(results[0].formatted_address);
                    $(this).parent().find('[name="lat"]').val(lat);
                    $(this).parent().find('[name="lng"]').val(lng);

                }
            });

        }

        // Autocomplete address ----------------------------------------------------------------------------------------
        var inputs = document.getElementsByName('location');
        var autocomplete = [];
        if ( inputs.length )
		for (i = 0; i < inputs.length; i++) {
    		autocomplete[i] = new google.maps.places.Autocomplete(inputs[i], {
            	types: ["geocode"]
        	});

        	google.maps.event.addListener(autocomplete[i], 'place_changed', function() {
       		this.bindTo('bounds', map);
            var place = this.getPlace();
            if (!place.geometry) {
                return;
            }
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
                map.setZoom(14);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(14);
            }

            //marker.setPosition(place.geometry.location);
            //marker.setVisible(true);

            var address = '';
            if (place.address_components) {
                address = [
                    (place.address_components[0] && place.address_components[0].short_name || ''),
                    (place.address_components[1] && place.address_components[1].short_name || ''),
                    (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }    
	        }); 
        }        
}

function multiChoice(sameLatitude, sameLongitude) {
	$.ajax({
        url: ajaxurl,
        data: { 'action':'_zoner_get_multiitems_', 'sameLatitude':sameLatitude, 'sameLongitude':sameLongitude  },
        success:function(html) {
            if (!$('.modal-window.multichoice').length)
			 $('body').append('<div class="modal-window multichoice fade_in"></div>');	
			$('.multichoice').html(html);
        }
    });  

	$('.modal-window .modal-background, .modal-close').live('click',  function(e){
		$('.modal-window').addClass('fade_out');
		setTimeout(function() { 
			$('.modal-window').remove();
		}, 300);
	});
}

function drawInfobox(locations, i) {
	var htmlWindow = '';
    htmlWindow += '<div class="infobox-inner">';
                    htmlWindow += '<a href="' + locations[i]['link'] + '">';
                        htmlWindow += '<div class="infobox-image" style="position: relative">';
                            
                            if (locations[i]['featured-image'] != '') {
                                htmlWindow += '<img src="' + locations[i]['featured-image'] + '">';
                            } else {
                                htmlWindow += '<img locations-src="' + locations[i]['holder-image'] + '">';
                            }   
                            
                            htmlWindow += '<div><span class="infobox-price">' + locations[i]['price'] + '</span></div>';
                        htmlWindow += '</div>';
                        htmlWindow += '</a>';
                        
                        htmlWindow += '<div class="infobox-description">';
                            htmlWindow += '<div class="infobox-title"><a href="'+ locations[i]['link'] +'">' + locations[i]['title'] + '</a></div>';
                            htmlWindow += '<div class="infobox-location">' + locations[i]['address'] + '</div>';
                        htmlWindow += '</div>';
                htmlWindow += '</div>'; 

   return htmlWindow;		
}
 function pushItemsToArray(uniq_ids, map_id) {
        $('#'+map_id).closest('.gmap-shortcode').find('.property').fadeOut();
        uniq_ids.forEach(function(item, i, arr){
           $('#'+map_id).closest('.gmap-shortcode').find('[data-uniqid='+item+']').removeClass('left-property');
           if (i%2 === 0) $('#'+map_id).closest('.gmap-shortcode').find('[data-uniqid='+item+']').addClass('left-property');
            $('#'+map_id).closest('.gmap-shortcode').find('[data-uniqid='+item+']').fadeIn();
        });
        
    }
function pushItemsFromForm(map_id, start_lat, start_lng, default_zoom, auto_zoom, items_number_max, serialize_form) {
    $.ajax({
		url: ajaxurl,
		data: { 'action':'_zoner_get_items_', 'serialize_form':serialize_form, 'items_number_max':items_number_max},
		method: "POST",
		beforeSend: function(){
		  //  $('.ajax-loading-end').hide();
			$('#'+map_id).parents('.gmap-shortcode').find('.ajax-loading-start').show();
			$('#'+map_id).closest('.gmap-shortcode').find('.property').fadeOut().removeClass('left-property');
		},
		complete: function(){
		   $('.ajax-loading-start').hide();
		  // $('.ajax-loading-end').show();
		  // setTimeout(function(){$('.ajax-loading-end').fadeOut();}, 2000);
		},
		success:function(locations) {
            console.log(locations);
			$('.loading-items-status').fadeOut();
			if (locations != -1){  
				createShortcodeGoogleMap(start_lat, start_lng, default_zoom, auto_zoom, $.parseJSON(locations), map_id);
				var results_list = $('#'+map_id).parents('.gmap-shortcode').find('.items-list .results');
				if (results_list.length){
					results_list.html(''); 
					locations = jQuery.parseJSON(locations);
					  for (i = 0; i < locations.length; i++) {
                          var result_='';
                          result_+='<div class="property" style="display: none;" data-uniqid="'+locations[i]['post_id']+'">';
						  result_+= locations[i]['status'];
						  result_+= locations[i]['condition'];
						  result_+= '<div class="property-image">';
							result_+='<a href="'+locations[i]['link']+'" rel="nofollow">';
							result_+=(locations[i]['featured-image'].length)? "<img class='img-responsive' src='"+locations[i]['featured-image']+"'>":"<img class='img-responsive placeholder' data-src='holder.js/440x330?auto=yes&text=Property'>";
                          result_+='</a>';
                          result_+='</div>'
                          result_+= '<div class="overlay">'
                            result_+='<div class="info">'
                             result_+='<span class="tag price">'+locations[i]['price']+'</span>';
                              result_+='<a href="'+locations[i]['link']+'" rel="nofollow">';
                              result_+= '<h3>'+ locations[i]['title'] +'</h3></a>';
                              result_+= '<figure>'+ locations[i]['address'] +'</figure></div>';
                              result_+= '<ul class="additional-info">';
                              result_+= locations[i]['area'];
                              result_+= locations[i]['beds'];
                              result_+=	locations[i]['baths'];
                              result_+=	locations[i]['garages'];
                          results_list.append(result_);
					}
                    Holder.run({images:".placeholder.img-responsive"});
				}
			}
			else{
				$('.ajax-loading-start').hide();
				$('.nothing-found').show();
				setTimeout(function(){$('.nothing-found').fadeOut();}, 3000);
			}
		   
		}
	});  
} 
// Redraw map after item list is closed --------------------------------------------------------------------------------

function redrawMap(mapProvider, map){
    $('.map .toggle-navigation').click(function() {
        $('.map-canvas').toggleClass('results-collapsed');
        $('.map-canvas .map').one("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){
            if( mapProvider == 'osm' ){
                map.invalidateSize();
            }
            else if( mapProvider == 'google' ){
                google.maps.event.trigger(map, 'resize');
            }
        });
    });
}

//Shortcode form event
var time_updater;
$('.zoner-dinamic-search').delegate(':input, [type="checkbox"],  select, .ui-slider','change',function(){
	update_form_delay($(this)); 
});
$('.zoner-dinamic-search').delegate('.icheckbox input','ifChanged ',function(){
	update_form_delay($(this)); 
});
$('.zoner-dinamic-search').delegate('form','submit ',function(e){
	e.preventDefault();
	update_form_delay($(this).find('input')); 
});
var $priceSlider = $(".zoner-dinamic-search .price-input");
if( $priceSlider.length > 0 ) {
	$priceSlider.each(function() {
		$(this).slider({
			from: min_price,
			to:   max_price,
			smooth: true,
			round: 0,
			format: { format: "###,###", locale: 'en' },
			dimension : "&nbsp;" + default_currency,
			callback:function(){update_form_delay($(".zoner-dinamic-search .price-input"))}
		});
	});
}
	
function update_form_delay(form_element){
    var map_element = form_element.parents('.gmap-shortcode').find('.shortcode-map-wrapper');
	var serialize = form_element.closest('form').serializeArray();
    if (map_element.data('tax_city'))
    {
        $.each(serialize, function(index, field){
            if (field.name=='sb-city' && !field.value){
                serialize[index].value = map_element.data('tax_city');
            } 
        });
    }
  
	
	var map_id = map_element.attr('id');
	
	var start_lat = map_element.data('start_lat');
	var start_lng = map_element.data('start_lng');
	var items_number_max = map_element.data('items_number_max');
	var auto_zoom = map_element.data('auto_zoom');
	var default_zoom = map_element.data('default_zoom');
	default_zoom = parseInt(default_zoom);
	
	clearTimeout(time_updater);
	//it's need for f***ing multiclickers 
	time_updater = setTimeout(function() {
        auto_zoom = 1; //after search it must be dynamic!
		pushItemsFromForm(map_id, start_lat, start_lng, default_zoom, auto_zoom, items_number_max, serialize);
	//resetMapMarkers(3000);
	}, 1000);
}
	
$(window).load(function(){
	$('.gmap-shortcode .results').mCustomScrollbar({theme:"light-3"});
});

function arraysEqual(a, b) {
	if (a === b) return true;
	if (a == null || b == null) return false;
	if (a.length != b.length) return false;

	// If you don't care about the order of the elements inside
	// the array, you should sort both arrays here.

	for (var i = 0; i < a.length; ++i) {
		if (a[i] !== b[i]) return false;
	}
	return true;
}