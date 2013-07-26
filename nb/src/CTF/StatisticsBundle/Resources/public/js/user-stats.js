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
