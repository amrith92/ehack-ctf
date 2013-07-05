var map, marker = null;
function initialiseMap() {
    var myOptions = {
        center: new google.maps.LatLng(13.0810, 80.2740),
        zoom: 6,
        mapTypeControl: false,
        mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
        navigationControl: false,
        navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    marker = new google.maps.Marker({
        position: map.getCenter(),
        map: map,
        title:"You are here",
        draggable: true
    });
    map.panTo(marker.getPosition());
    geocodePosition(marker.getPosition());
    google.maps.event.addListener(marker, 'dragend', function(evt) {
        $("#ctf_userbundle_usertype_location").val(evt.latLng.lat() + "," + evt.latLng.lng());
        geocodePosition(marker.getPosition());
    });
}

function initialize() {
    var mapOptions = {
        center: new google.maps.LatLng(-33.8688, 151.2195),
    };
    var input = document.getElementById('ctf_userbundle_usertype_org');
    var options = {
        types: ['establishment']
    };
    var autocomplete = new google.maps.places.Autocomplete(input, options);
}

function onLocateClick() {
    if(geoPosition.init()) {
        geoPosition.getCurrentPosition(showPosition,function(){ alert('Could not find you! :('); },{enableHighAccuracy:true});
    }
}

function showPosition(p) {
    var latitude = parseFloat( p.coords.latitude );
    var longitude = parseFloat( p.coords.longitude );
    $('#ctf_userbundle_usertype_location').val(latitude + "," + longitude);
    var pos=new google.maps.LatLng( latitude , longitude);
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

$(document).ready(function() {
    $('.date').datepicker({
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true
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
            initialize();
            initialiseMap();
            var mapval = $('#ctf_userbundle_usertype_location').val();
            if (null !== mapval) {
                var latlng = mapval.split(',');
                showPosition({ coords: { latitude: latlng[0], longitude: latlng[1] } });
            }
            $('#regTabs a[href="#address"]').tab('show');
        }, "html");
    });
    
    initialize();
    initialiseMap();
    
    var mapval = $('#ctf_userbundle_usertype_location').val();
    if (null != mapval && 'NaN,NaN' != mapval) {
        var latlng = mapval.split(',');
        showPosition({ coords: { latitude: latlng[0], longitude: latlng[1] } });
        geocodePosition(marker.getPosition());
    }
});
