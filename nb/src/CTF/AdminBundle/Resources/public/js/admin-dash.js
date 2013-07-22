$(document).ready(function() {
    $('#announce-form').submit(function() {
        $.post(Routing.generate('ctf_admin_announce', null), $('#announce-form').serialize(), function(data) {
            $('#announce-box').fadeOut();
            $('#announce-box').fadeIn('fast', function() {
                $('#ctf_adminbundle_announcementtype_announcement').val('');
            });
        });
        return false;
    });
    
    $('#banteam-typeahead').typeahead({
        source: function(query, process) {
            return $.get(Routing.generate('ctf_admin_team_list', { 'query': query }), null, function(data) {
                return process(data.options);
            });
        },
        items: 5
    });
    
    $('#banuser-typeahead').typeahead({
        source: function(query, process) {
            return $.get(Routing.generate('ctf_admin_user_list', { 'query': query }), null, function(data) {
                return process(data.options);
            });
        },
        items: 5
    });
    
    $('#banteam-button').click(function() {
        var name = document.getElementById('banteam-typeahead').value;
        if (name != null && name != '') {
            $.post(Routing.generate('ctf_admin_team_ban', { 'name': name}), null, function(data) {
                var response = JSON.parse(data);
                if ('success' == response.result) {
                    $('#banteam-result').html('<div class="alert alert-success">Successfully banned team "' + name + '"</div>').show().fadeOut(2000);
                    document.getElementById('banteam-typeahead').value = null;
                } else {
                    $('#banteam-result').html('<div class="alert alert-error">Could not ban team "' + name + ' at this time."</div>').show().fadeOut(2000);
                }
            });
        }
    });
    
    $('#banteam-button-unban').click(function() {
        var name = document.getElementById('banteam-typeahead').value;
        if (name != null && name != '') {
            $.post(Routing.generate('ctf_admin_team_unban', { 'name': name}), null, function(data) {
                var response = JSON.parse(data);
                if ('success' == response.result) {
                    $('#banteam-result').html('<div class="alert alert-success">Successfully un-banned team "' + name + '"</div>').show().fadeOut(2000);
                    document.getElementById('banteam-typeahead').value = null;
                } else {
                    $('#banteam-result').html('<div class="alert alert-error">Could not un-ban team "' + name + ' at this time."</div>').show().fadeOut(2000);
                }
            });
        }
    });
    
    $('#banuser-button').click(function() {
        var name = document.getElementById('banuser-typeahead').value;
        if (name != null && name != '') {
            $.post(Routing.generate('ctf_admin_user_ban', { 'name': name}), null, function(data) {
                var response = JSON.parse(data);
                if ('success' == response.result) {
                    $('#banuser-result').html('<div class="alert alert-success">Successfully banned user "' + name + '"</div>').show().fadeOut(2000);
                    document.getElementById('banuser-typeahead').value = null;
                } else {
                    $('#banuser-result').html('<div class="alert alert-error">Could not ban user "' + name + ' at this time."</div>').show().fadeOut(2000);
                }
            });
        }
    });
    
    $('#banuser-button-unban').click(function() {
        var name = document.getElementById('banuser-typeahead').value;
        if (name != null && name != '') {
            $.post(Routing.generate('ctf_admin_user_unban', { 'name': name}), null, function(data) {
                var response = JSON.parse(data);
                if ('success' == response.result) {
                    $('#banuser-result').html('<div class="alert alert-success">Successfully un-banned user "' + name + '"</div>').show().fadeOut(2000);
                    document.getElementById('banuser-typeahead').value = null;
                } else {
                    $('#banuser-result').html('<div class="alert alert-error">Could not un-ban user "' + name + ' at this time."</div>').show().fadeOut(2000);
                }
            });
        }
    });
    
    $.post(Routing.generate('ctf_statistics_team_count', null), null, function(data) {
        $('#teamstats-count').text(data);
    });
    setInterval(function() {
        $.post(Routing.generate('ctf_statistics_team_count', null), null, function(data) {
            $('#teamstats-count').text(data);
        });
    }, 25000);
    
    $.post(Routing.generate('ctf_statistics_players_count', null), null, function(data) {
        $('#player-count').text(data);
    });
    setInterval(function() {
        $.post(Routing.generate('ctf_statistics_players_count', null), null, function(data) {
            $('#player-count').text(data);
        });
    }, 15000);
    
    $.post(Routing.generate('ctf_statistics_genders_count', null), null, function(data) {
        $("#genderChart").empty();
        var chart = new CanvasJS.Chart("genderChart", JSON.parse(data));
        chart.render();
    });
    setInterval(function() {
        $.post(Routing.generate('ctf_statistics_genders_count', null), null, function(data) {
            $("#genderChart").empty();
            var chart = new CanvasJS.Chart("genderChart", JSON.parse(data));
            chart.render();
        });
    }, 60000);
    
    $.post(Routing.generate('ctf_statistics_top_orgs', { 'n': 10 }), null, function(data) {
        $("#topOrgsChart").empty();
        var chart = new CanvasJS.Chart("topOrgsChart", JSON.parse(data));
        chart.render();
    });
    setInterval(function() {
        $.post(Routing.generate('ctf_statistics_top_orgs', { 'n': 10 }), null, function(data) {
            $("#topOrgsChart").empty();
            var chart = new CanvasJS.Chart("topOrgsChart", JSON.parse(data));
            chart.render();
        });
    }, 65000);
});