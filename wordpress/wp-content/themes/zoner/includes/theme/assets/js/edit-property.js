var $ = jQuery.noConflict();
var mapStyles = [{featureType:'water',elementType:'all',stylers:[{hue:'#d7ebef'},{saturation:-5},{lightness:54},{visibility:'on'}]},{featureType:'landscape',elementType:'all',stylers:[{hue:'#eceae6'},{saturation:-49},{lightness:22},{visibility:'on'}]},{featureType:'poi.park',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-81},{lightness:34},{visibility:'on'}]},{featureType:'poi.medical',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-80},{lightness:-2},{visibility:'on'}]},{featureType:'poi.school',elementType:'all',stylers:[{hue:'#c8c6c3'},{saturation:-91},{lightness:-7},{visibility:'on'}]},{featureType:'landscape.natural',elementType:'all',stylers:[{hue:'#c8c6c3'},{saturation:-71},{lightness:-18},{visibility:'on'}]},{featureType:'road.highway',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-92},{lightness:60},{visibility:'on'}]},{featureType:'poi',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-81},{lightness:34},{visibility:'on'}]},{featureType:'road.arterial',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-92},{lightness:37},{visibility:'on'}]},{featureType:'transit',elementType:'geometry',stylers:[{hue:'#c8c6c3'},{saturation:4},{lightness:10},{visibility:'on'}]}];

$(document).ready(function(){
if(ZonerGlobal.gm_or_osm == 0){

    var mapStyles    = [{featureType:'water',elementType:'all',stylers:[{hue:'#d7ebef'},{saturation:-5},{lightness:54},{visibility:'on'}]},{featureType:'landscape',elementType:'all',stylers:[{hue:'#eceae6'},{saturation:-49},{lightness:22},{visibility:'on'}]},{featureType:'poi.park',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-81},{lightness:34},{visibility:'on'}]},{featureType:'poi.medical',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-80},{lightness:-2},{visibility:'on'}]},{featureType:'poi.school',elementType:'all',stylers:[{hue:'#c8c6c3'},{saturation:-91},{lightness:-7},{visibility:'on'}]},{featureType:'landscape.natural',elementType:'all',stylers:[{hue:'#c8c6c3'},{saturation:-71},{lightness:-18},{visibility:'on'}]},{featureType:'road.highway',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-92},{lightness:60},{visibility:'on'}]},{featureType:'poi',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-81},{lightness:34},{visibility:'on'}]},{featureType:'road.arterial',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-92},{lightness:37},{visibility:'on'}]},{featureType:'transit',elementType:'geometry',stylers:[{hue:'#c8c6c3'},{saturation:4},{lightness:10},{visibility:'on'}]}];
	var _latitude    = ZonerEproperty._latitude;
    var _longitude   = ZonerEproperty._longitude;
	var _single_zoom = parseInt(ZonerEproperty._single_zoom);
	var _icon_marker = ZonerEproperty._icon_marker;


    ZonerGlobal.initSubmitMap = function (_latitude,_longitude){
      var mapCenter = new google.maps.LatLng(_latitude,_longitude);

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
        zoom: _single_zoom,
        center: mapCenter,
        disableDefaultUI: false,
        mapTypeId:  ggMapsTypes,
        styles: mapStyles
      };

      var mapElement  = document.getElementById('submit-map');
      var searchInput = document.getElementById('search-location');

      $(searchInput).keypress(function(e) {
        if(e.keyCode == 13) {
          e.preventDefault();
          return false;
        }
      });

      $('#submit-map').removeClass('fade-map');


      //console.log(ggMapsTypes);
      var map = new google.maps.Map(mapElement, mapOptions);
      var marker = new MarkerWithLabel({
        position: mapCenter,
        map: map,
        icon: ZonerEproperty._icon_marker,
        labelAnchor: new google.maps.Point(50, 0),
        draggable: true
      });

      var autocomplete = new google.maps.places.Autocomplete(searchInput);
      autocomplete.bindTo('bounds', map);

      google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
          return;
        }
        if (place.geometry.viewport) {
          map.fitBounds(place.geometry.viewport);
        } else {
          map.setCenter(place.geometry.location);
          map.setZoom(15);
        }
        marker.setPosition(place.geometry.location);

        $('#latitude').val(marker.getPosition().lat());
        $('#longitude').val(marker.getPosition().lng());
      });

      google.maps.event.addListener(marker, "mouseup", function (event) {
        var latitude  = this.position.lat();
        var longitude = this.position.lng();
        $('#latitude').val( this.position.lat() );
        $('#longitude').val( this.position.lng() );
      });
    }
    google.maps.event.addDomListener(window, 'load', ZonerGlobal.initSubmitMap(_latitude,_longitude));
} else{ //else OpenStreet
  $('.link-arrow.geo-location').remove();
  setMapHeight();
  if( document.getElementById('submit-map') != null ){
    var mbUrl 		= 'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFuaW1pbGxzIiwiYSI6ImNpaHZ0dXFwYTAwNXd3MWtwcm5neTRjdDgifQ.Tm_WKZTI9vwh_phQn_LoKA';
    var mbAttr		= '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors';
    var grayscale   = L.tileLayer(mbUrl, {id: 'mapbox.light', 	  minZoom: 0, maxZoom: 20, attribution: mbAttr}),
    streets     = L.tileLayer(mbUrl, {id: 'mapbox.streets',   minZoom: 0, maxZoom: 20, attribution: mbAttr});

    var map = L.map('submit-map', {
      center: [40.7056308,-73.9780035],
      zoom: maps_zoom,
      scrollWheelZoom:false,
      closeOnClick:true,
      layers: [grayscale]
    });
    var map;
    var feature;
    var searchBox = document.getElementById('search-location');
    var searchBoxParent = searchBox.closest('.input-group');
    $('<div id="results"/>').insertAfter(searchBoxParent);
    var results = document.getElementById('results');

    ZonerGlobal.osmChooseAddr = function (lat1, lng1, lat2, lng2, osm_type, title) {
      var loc1 = new L.LatLng(lat1, lng1);
      var loc2 = new L.LatLng(lat2, lng2);
      var bounds = new L.LatLngBounds(loc1, loc2);
      if (osm_type == "node") {
      feature = L.circle( loc1, 25, {color: 'green', fill: false}).addTo(map);
        map.fitBounds(bounds);
        map.setZoom(18);
      } else {
        var loc3 = new L.LatLng(lat1, lng2);
        var loc4 = new L.LatLng(lat2, lng1);

        map.fitBounds(bounds);
      }
      var markers = L.markerClusterGroup({
        showCoverageOnHover: false
      });
      var _icon = L.divIcon({
        html: '<img width="26" height="26" src="' + ZonerGlobal.source_path + '/img/empty.png">',
        iconSize:     [40, 48],
        iconAnchor:   [20, 48],
        popupAnchor:  [0, -48]
      });
      markerLat = lat1+(lat2-lat1)/2;
      markerLng = lng1+(lng2-lng1)/2;
      var marker = L.marker(new L.LatLng(markerLat,markerLng), {
        title: title,
        icon: _icon
      });
      markers.addLayer(marker);
      map.addLayer(markers);
      document.getElementById('latitude').value = markerLat;
      document.getElementById('longitude').value = markerLng;
      $(results).hide();
    }

    function addr_search() {
      $.getJSON('http://nominatim.openstreetmap.org/search?format=json&limit=5&q=' + $(searchBox).val(), function(data) {
      var items = [];

      $.each(data, function(key, val) {
        bb = val.boundingbox;
        items.push("<li><a href='#' onclick='ZonerGlobal.osmChooseAddr(" + bb[0] + ", " + bb[2] + ", " + bb[1] + ", " + bb[3]  + ", \"" + val.osm_type + "\"" + ", \"" + val.display_name + "\");return false;'>" + val.display_name + '</a></li>');
      });

        $(results).empty();
        if (items.length != 0) {
          $('<ul/>', {
          'class': 'search-results',
          html: items.join('')
          }).appendTo('#results');
        } else {
          $('<p>', { html: "No results found" }).appendTo('#results');
        }
        $(results).show();
      });
    }
    searchBox.addEventListener("keyup", function(){
      addr_search();
    });
  }
}
});

function success(position) {
  ZonerGlobal.initSubmitMap(position.coords.latitude, position.coords.longitude);
}

if ($('.geo-location').length > 0) {
  $('.geo-location').on("click", function() {
    if (navigator.geolocation) {
      $('#submit-map').addClass('fade-map');
      navigator.geolocation.getCurrentPosition(success);
    } else {
      error(ZonerEproperty._error_text);
    }
  });
}


$(document).ready(function() {

/*Validate form add & edit property*/
  if ($('#form-submit').length > 0) {
    $('#form-submit').validate({
      submitHandler: function(form) {
        form.submit();
        return false;
      }
    });
  }


  if ($( "#sortable-image-gallery" ).length > 0) {
    $( "#sortable-image-gallery" ).disableSelection();
    $( "#sortable-image-gallery" ).sortable({placeholder:'ui-SortPlaceHolder'});
  }

  if ($( "#sortable-image-plans" ).length > 0) {
    $( "#sortable-image-plans" ).disableSelection();
    $( "#sortable-image-plans" ).sortable({placeholder:'ui-SortPlaceHolder'});
  }

  $( ".sortable-gallery span.remove-prop").on('click', function() {
    $(this).closest('.sortable').fadeOut('slow', function() {
      $(this).remove();
    });
    return false;
  });


  if ($('.add-video').length > 0) {
    $('.add-video').on('click', function() {
      var data = { action: 'get_input_video'};

      $.post(ZonerGlobal.ajaxurl, data,  function(response) {
        $('#property-video-presentation .row.field-container').append(response);
      });

      return false;
    });
  }


  $('.remove-video').live('click', function() {
    $(this).closest('.inner-fields').fadeOut('slow', function () {
      $(this).remove();
    });
    return false;
  });


  // if disabled in options getting the country from hidden field
  if ($('#submit-country[type="hidden"]').length > 0 ) {
      zoner_get_states_by_country( $('#submit-country[type="hidden"]').val() );
  }

  $('#submit-country').on('change', function(event) {
    var vCurrCountry = $(this).val();
    zoner_get_states_by_country(vCurrCountry);
  });

  function zoner_get_states_by_country(country) {
    var vCurrCountry = country;
    var data = {action : 'get_states_by_country', country : vCurrCountry  };
    $.post(ZonerGlobal.ajaxurl, data,  function(options) {
      var $selectStateWrapper = $('#select-state'),
          $submitState = $('#submit-state[type!="hidden"]');
      if (!options) {
        $selectStateWrapper.hide();
        $submitState.selectpicker('hide');
      } else {
        $selectStateWrapper.show();
        var selectedVal = $submitState.find('option:selected').val();
        $submitState.find('option').remove().end().append(options);
        if (selectedVal && selectedVal.length) {
          $submitState.find('option[value=' + selectedVal + ']').attr('selected', 'selected');
        }
        $submitState.selectpicker('show');
        $submitState.selectpicker('refresh');
      }
    });
  }

  if ($('#select-state option:selected').length == 0) {
    $('#submit-country').change();
  }

  if ($('#submit-state').find('option').length == 0) {
    $('#select-state').hide();
    $('#submit-state').selectpicker('hide');
  }
});