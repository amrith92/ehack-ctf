var WorldUsers = {
    map: null,
    loadUsers: function() {
        $.get(Routing.generate('ctf_users_world', null), null, function(data) {
            var response = JSON.parse(data);

            if ('success' == response.result) {
                var markers = new L.MarkerClusterGroup({
                    spiderfyOnMaxZoom: true
                });

                for (var i = 0, term = response.users.length; i < term; ++i) {
                    var marker = new L.Marker(new L.LatLng(
                        response.users[i].location.lat,
                        response.users[i].location.lng
                    ));
                    marker.bindPopup('<div class="row-fluid"><div class="span12"><h4 style="color:#000;"><a href="' + Routing.generate('ctf_public_profile', { id: response.users[i].id }) + '" target="_blank">@' + response.users[i].username + '</a></h4></div></div><div class="row-fluid"><div class="span5"><img src="' + response.users[i].dp + '" title="' + response.users[i].username + '\'s Profile Picture" class="img-polaroid" width="50"></div><div class="span7"><div class="label label-inverse">Organization</div><p>' + response.users[i].organization + '</p></div></div>');
                    markers.addLayer(marker);
                }

                WorldUsers.map.addLayer(markers);
            }
        });
    }
};

window.onload = function() {
    WorldUsers.map = L.map('users-map').setView([13.0810, 80.2740], 3);
    
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png').addTo(WorldUsers.map);
    
    WorldUsers.loadUsers();
    
    $.post(Routing.generate('ctf_statistics_genders_count', null), null, function(data) {
        $("#genderChart").empty();
        var chart = new CanvasJS.Chart("genderChart", JSON.parse(data));
        chart.render();
    });
    
    $.post(Routing.generate('ctf_statistics_team_count', null), null, function(data) {
        $('#teamstats-count').text(data);
    });
    
    $.post(Routing.generate('ctf_statistics_players_count', null), null, function(data) {
        $('#player-count').text(data);
    });
    
    $.post(Routing.generate('ctf_statistics_top_orgs', { 'n': 3 }), null, function(data) {
        $("#topOrgsChart").empty();
        var chart = new CanvasJS.Chart("topOrgsChart", JSON.parse(data));
        chart.render();
    });
    
    $.post(Routing.generate('ctf_statistics_bottom_orgs', { 'n': 3 }), null, function(data) {
        $("#bottomOrgsChart").empty();
        var chart = new CanvasJS.Chart("bottomOrgsChart", JSON.parse(data));
        chart.render();
    });
};
