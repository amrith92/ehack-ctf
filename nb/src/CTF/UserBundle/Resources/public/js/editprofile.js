var map, marker = null;
function initialiseMap() {
    var latlng = new google.maps.LatLng(13.0810, 80.2740);
    var myOptions = {
        center: latlng,
        zoom: 6,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title:"You are here",
        draggable: true
    });
    map.panTo(latlng);
    geocodePosition(latlng);
    google.maps.event.addListener(marker, 'dragend', function(evt) {
        $("#ctf_userbundle_usertype_location").val(evt.latLng.lat() + "," + evt.latLng.lng());
        geocodePosition(marker.getPosition());
    });
    
    var input = document.getElementById('ctf_userbundle_usertype_org');
    var options = {
        types: ['establishment']
    };
    var autocomplete = new google.maps.places.Autocomplete(input, options);
}

function onLocateClick() {
    if(geoPosition.init()) {
        geoPosition.getCurrentPosition(showPosition, function(e) {
            alert(e.message);
        }, {
            enableHighAccuracy: true
        });
    }
}

function showPosition(p) {
    var latitude = parseFloat( p.coords.latitude );
    var longitude = parseFloat( p.coords.longitude );
    $('#ctf_userbundle_usertype_location').val(latitude + "," + longitude);
    var pos = new google.maps.LatLng( latitude , longitude);
    map.setCenter(pos);
    map.setZoom(14);

    if (null === marker) {
        marker = new google.maps.Marker({
            position: pos,
            map: map,
            title:"You are here",
            draggable: true
        });
        geocodePosition(marker.getPosition());
        google.maps.event.addListener(marker, 'dragend', function(evt) {
            $("#ctf_userbundle_usertype_location").val(evt.latLng.lat() + "," + evt.latLng.lng());
            geocodePosition(marker.getPosition());
        });
    } else {
        marker.setPosition(pos);
    }
    map.panTo(pos);
}

function geocodePosition(pos) {
    geocoder = new google.maps.Geocoder();
    geocoder.geocode(
        {
            latLng: pos
        }, 
       function(results, status) {
           if (status == google.maps.GeocoderStatus.OK) {
               $('#formatted-address').text(results[0].formatted_address);
           }
       }
    );
}

function locationize() {
    var location = $('#ctf_userbundle_usertype_location').val();
    if (null != location && location.length > 0) {
        location = location.split(',');
        var latlng = {
            coords: {
                latitude: location[0],
                longitude: location[1]
            }
        };
        
        showPosition(latlng);
    }
}

$('#map_fullscreen').click(function (e) {
    e.preventDefault();
    
    $('#map_canvas').addClass('fullscreen');
    $('#map_canvas').empty();
    initialiseMap();
    locationize();
    $('#map_normalize_container').removeClass('hidden');
});

$('#map_normalize').click(function (e) {
    e.preventDefault();
    
    $('#map_canvas').removeClass('fullscreen');
    $('#map_canvas').empty();
    initialiseMap();
    locationize();
    $('#map_normalize_container').addClass('hidden');
});

$(document).ready(function() {
    $('.date').datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        maxDate: "-17Y",
        yearRange: "-100:-17"
    });
    
    $( "[title]" ).tooltip();
    
    $('#editForm').on('change', '#ctf_userbundle_usertype_country', function() {
        document.getElementById('state-loader').innerHTML = document.getElementById('loader').innerHTML;
        $.post(Routing.generate('ctf_get_edit_form', null), $("#edit-form").serialize(), function(data) {
            $('#edit-form-container').html(data);
            $('.date').datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true
            });
            initialiseMap();
            var mapval = $('#ctf_userbundle_usertype_location').val();
            if (null !== mapval) {
                var latlng = mapval.split(',');
                if (latlng.length > 1) {
                    showPosition({ coords: { latitude: latlng[0], longitude: latlng[1] } });
                    geocodePosition(marker.getPosition());
                }
            }
            $('a[href="#address"]').click();
        }, "html");
    });
    
    initialiseMap();
    
    var mapval = $('#ctf_userbundle_usertype_location').val();
    if (null != mapval && 'NaN,NaN' != mapval) {
        var latlng = mapval.split(',');
        if (latlng.length > 1) {
            showPosition({ coords: { latitude: latlng[0], longitude: latlng[1] } });
            geocodePosition(marker.getPosition());
        }
    }
});
